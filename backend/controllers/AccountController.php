<?php

namespace backend\controllers;

use Yii;
use common\models\Account;
use common\models\AccountTransaction;
use common\models\User;
use common\models\Invoice;
use common\models\UserAdmin;
use backend\models\AccountSearch;
use backend\controllers\ZController;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\widgets\ActiveForm;
use yii\filters\AccessControl;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

/**
 * AccountController implements the CRUD actions for Account model.
 */
class AccountController extends ZController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
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
     * @inheritdoc
     */
    public function beforeAction($action) {
        $actions = $this->getAllowedActions();
        $actions[] = 'get-lists-by-user';
        if (!Yii::$app->user->can(UserAdmin::PERMISSION_ACCOUNT) && !in_array($action->id, $actions)) {
            // return $this->redirect(['/site/error']);
            throw new \yii\web\ForbiddenHttpException(Yii::t('app', static::FORBIDDEN_HTTP_MESSAGE));
        }
        return parent::beforeAction($action);
    }

    /**
     * Lists all Account models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AccountSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Account model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Account model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Account();
        $post = Yii::$app->request->post();
        
        $model->generateUid();

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            return ActiveForm::validate($model);
        }
        
        if ($model->load($post)) {
            if ($model->save()) {
                return $this->redirect(['index']);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Account model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $post = Yii::$app->request->post();

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            return ActiveForm::validate($model);
        }
        
        if ($model->load($post)) {
            if ($model->save()) {
                return $this->redirect(['index']);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }
    
    /**
     * Export to excel an existing Account model.
     * @return mixed
     */
    public function actionExport()
    {
        $searchModel = new AccountSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());
        $dataProvider->pagination->pageSize = 0;
        
        $models = $dataProvider->models;
        
        if ($models) {
            $path = $this->export($models);
            return Yii::$app->response->sendFile(Yii::getAlias('@frontend/web').$path);
        }
        
        return $this->redirect(['/site/error']);
    }

    /**
     * Deletes an existing Account model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }
   
    /**
     * @param $user_id
     * @return array
     */
    public function actionGetListsByUser($user_id = null)
    {
        $userModel = User::findOne($user_id);

        $accountsData = '<option value="">Выберите...</option>'."\r\n";
        $invoicesData = '<option value="">Выберите...</option>'."\r\n";
        $flatsData = '<option value="">Выберите...</option>'."\r\n";
        if ($userModel) {
            $flatIds = ArrayHelper::getColumn($userModel->flats, 'id');
            $accounts = Account::find()->where(['in', 'flat_id', $flatIds])->all();
            foreach ($accounts as $account) {
                $accountsData .= '<option value="' . $account->id . '">' . $account->uid . '</option>'."\r\n";
            }

            $invoices = Invoice::find()->where(['in', 'flat_id', $flatIds])->andWhere(['in', 'status', [Invoice::STATUS_UNPAID, Invoice::STATUS_PLANNED]])->all();
            foreach ($invoices as $invoice) {
                $label = $invoice->uid . ' от ' . $invoice->getUidDate();
                $invoicesData .= '<option value="' . $invoice->id . '" data-price="' . $invoice->getPrice() . '">' . $label . '</option>'."\r\n";
            }
            
            foreach ($userModel->flats as $flat) {
                $label = $flat->flat . ', ' . $flat->house->name;
                $flatsData .= '<option value="' . $flat->id . '">' . $label . '</option>'."\r\n";
            }
        }

        return [
            'accounts' => $accountsData,
            'invoices' => $invoicesData,
            'flats' => $flatsData,
        ];
    }
    
    /**
     * @param $account_id
     * @return array
     */
    public function actionGetListsByAccount($account_id = null)
    {
        $accountModel = Account::findOne($account_id);

        $invoicesData = '<option value="">Выберите...</option>'."\r\n";
        if ($accountModel) {
            $invoices = Invoice::find()->where(['flat_id' => $accountModel->flat_id])->andWhere(['in', 'status', [Invoice::STATUS_UNPAID, Invoice::STATUS_PLANNED]])->all();
            foreach ($invoices as $invoice) {
                $label = $invoice->uid . ' от ' . $invoice->getUidDate();
                $invoicesData .= '<option value="' . $invoice->id . '" data-price="' . $invoice->getPrice() . '">' . $label . '</option>'."\r\n";
            }
        }

        return [
            'invoices' => $invoicesData,
        ];
    }
    
    /**
     * Finds the Account model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Account the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Account::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
    
    /**
     * Export models
     * @param Account[] $models
     */
    protected function export($models)
    {
        // file
        $path = '/upload/Account/accounts_'.date('Ymd_His').'.xls';
        $dir = dirname(Yii::getAlias('@frontend/web').$path);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        if (!is_writable($dir)) {
            chmod($dir, 0777);
        }
        $pathFull = Yii::getAlias('@frontend/web').$path;

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $style = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            ],
        ];
        $sheet->setCellValueByColumnAndRow(1, 1, 'Лицевой счет');
        $sheet->setCellValueByColumnAndRow(2, 1, 'Статус');
        $sheet->setCellValueByColumnAndRow(3, 1, 'Дом');
        $sheet->setCellValueByColumnAndRow(4, 1, 'Секция');
        $sheet->setCellValueByColumnAndRow(5, 1, 'Квартира');
        $sheet->setCellValueByColumnAndRow(6, 1, 'Владелец');
        $sheet->setCellValueByColumnAndRow(7, 1, 'Остаток');
        foreach ($models as $k => $model) {
            $sheet->setCellValueByColumnAndRow(1, $k+2, $model->uid)->getStyleByColumnAndRow(1, $k+2)->applyFromArray($style);
            $sheet->setCellValueByColumnAndRow(2, $k+2, $model->getStatusLabel())->getStyleByColumnAndRow(2, $k+2)->applyFromArray($style);
            $sheet->setCellValueByColumnAndRow(3, $k+2, $model->flat ? $model->flat->house->name : '')->getStyleByColumnAndRow(3, $k+2)->applyFromArray($style);
            $sheet->setCellValueByColumnAndRow(4, $k+2, $model->flat ? $model->flat->section->name : '')->getStyleByColumnAndRow(4, $k+2)->applyFromArray($style);
            $sheet->setCellValueByColumnAndRow(5, $k+2, $model->flat ? $model->flat->flat : '')->getStyleByColumnAndRow(5, $k+2)->applyFromArray($style);
            $sheet->setCellValueByColumnAndRow(6, $k+2, $model->flat ? $model->flat->user->fullname : '')->getStyleByColumnAndRow(6, $k+2)->applyFromArray($style);
            $sheet->setCellValueByColumnAndRow(7, $k+2, $model->getBalance())->getStyleByColumnAndRow(7, $k+2)->applyFromArray($style);
        }
        $sheet->getColumnDimensionByColumn(1)->setAutoSize(true);
        $sheet->getColumnDimensionByColumn(2)->setAutoSize(true);
        $sheet->getColumnDimensionByColumn(3)->setAutoSize(true);
        $sheet->getColumnDimensionByColumn(4)->setAutoSize(true);
        $sheet->getColumnDimensionByColumn(5)->setAutoSize(true);
        $sheet->getColumnDimensionByColumn(6)->setAutoSize(true);
        $sheet->getColumnDimensionByColumn(7)->setAutoSize(true);
        
        $writer = new Xls($spreadsheet);
        $writer->save(Yii::getAlias('@frontend/web').$path);
        
        chmod(Yii::getAlias('@frontend/web').$path, 0777);
        
        return $path;
    }
}
