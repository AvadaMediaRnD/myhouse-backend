<?php

namespace backend\controllers;

use Yii,
    yii\web\NotFoundHttpException,
    yii\filters\VerbFilter,
    yii\filters\AccessControl,
    yii\web\UploadedFile;
use backend\controllers\ZController,
    backend\models\Import1cForm;
use console\models\XmlParser1C;

/**
 * Import1cController actions for Import1c model.
 */
class Import1cController extends ZController {

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Parser data from 1C
     * @inheritdoc
     */
    public function beforeAction($action) {
        return parent::beforeAction($action);
    }

    /**
     * Import invoices data.
     * @return mixed
     */
    public function actionIndex() {
        $result = false;
        $folder = Yii::getAlias('@1c') . Yii::$app->params['iport_invoice_dir_1c'];

        if (!file_exists($folder)) {
            mkdir($folder, 0766, true);
        }

        // List of files in folder
        $list = scandir($folder);

        $model = new Import1cForm();

        if (Yii::$app->request->post()) {

            $runImport = Yii::$app->request->post('run');

            $model->importFiles = UploadedFile::getInstances($model, 'importFiles');
            if ($model->upload()) {
                return $this->refresh();
            }

            if (isset($runImport) && $runImport == 1) {

                foreach ($list as $item) {
                    $file = $folder . '/' . $item;

                    if ($item !== '.' && $item !== '..' && !is_dir($file)) {
                        $result = $this->parceImportFile($file);

                        if ($result === true) {
                            unlink($file);
                        }
                    }
                }

                if ($result === true) {
                    Yii::$app->session->setFlash('success', Yii::t('app', "Импорт данных счетов из 1C успешно завершён!"));
                } else {
                    Yii::$app->session->setFlash('error', Yii::t('app', "Импорт данных счетов из 1C завершился ошибкой!"));
                }

                return $this->refresh();
            }
        }

        return $this->render('index', ['list' => $list, 'status' => $result, 'model' => $model]);
    }

    /**
     * Import payment data
     * @return mixed
     */
    public function actionPayments() {
        $result = false;
        $folder = Yii::getAlias('@1c') . Yii::$app->params['iport_pay_dir_1c'];

        if (!file_exists($folder)) {
            mkdir($folder, 0766, true);
        }

        // List of files in folder
        $list = scandir($folder);

        $model = new Import1cForm();

        if (Yii::$app->request->post()) {

            $runImport = Yii::$app->request->post('run');

            $model->importFiles = UploadedFile::getInstances($model, 'importFiles');
            if ($model->uploadPayment()) {
                return $this->refresh();
            }

            if (isset($runImport) && $runImport == 1) {

                foreach ($list as $item) {
                    $file = $folder . '/' . $item;

                    if ($item !== '.' && $item !== '..' && !is_dir($file)) {
                        $result = $this->parcePaymentFile($file);

                        if ($result === true) {
                            unlink($file);
                        }
                    }
                }

                // Set invoice status
                XmlParser1C::changeInvoicesStatus();

                if ($result === true) {
                    Yii::$app->session->setFlash('success', Yii::t('app', "Импорт данных оплат из 1C успешно завершён!"));
                } else {
                    Yii::$app->session->setFlash('error', Yii::t('app', "Импорт данных оплат из 1C завершился ошибкой!"));
                }

                return $this->refresh();
            }
        }

        return $this->render('payment', ['list' => $list, 'status' => $result, 'model' => $model]);
    }

    /**
     * Trancate data from 1C
     * @return mixed
     */
    public function actionTrancate() {
        $result = XmlParser1C::trancateData();

        if ($result === true) {
            $this->stdout('Trancate data from 1C success!' . PHP_EOL, Console::FG_GREEN);
        } else {
            $this->stdout('Trancate data from 1C error!' . PHP_EOL, Console::FG_RED);
        }
    }

    /**
     * Parce import invoice file
     * @param string $file
     * @return mixed
     */
    protected function parceImportFile(string $file): bool {
        $result = false;

        // Check mime type of file
        if (mime_content_type($file) === 'text/xml') {
            $result = XmlParser1C::parseData($file);
        }

        return $result;
    }

    /**
     * Parce import payment file
     * @param string $file
     * @return mixed
     */
    protected function parcePaymentFile(string $file): bool {
        $result = false;

        // Check mime type of file
        if (mime_content_type($file) === 'text/xml') {
            $result = XmlParser1C::parsePaymentData($file);
        }

        return $result;
    }

}
