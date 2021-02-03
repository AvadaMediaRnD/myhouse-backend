<?php

namespace backend\controllers;

use Yii;
use common\models\Account;
use common\models\AccountTransaction;
use common\models\UserAdmin;
use common\models\Currency;
use backend\models\AccountTransactionSearch;
use backend\controllers\ZController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\widgets\ActiveForm;
use yii\filters\AccessControl;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use common\models\InvoiceService;

/**
 * AccountTransactionController implements the CRUD actions for AccountTransaction model.
 */
class AccountTransactionController extends ZController
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
        if (!Yii::$app->user->can(UserAdmin::PERMISSION_ACCOUNT_TRANSACTION) && !in_array($action->id, $actions)) {
            // return $this->redirect(['/site/error']);
            throw new \yii\web\ForbiddenHttpException(Yii::t('app', static::FORBIDDEN_HTTP_MESSAGE));
        }
        return parent::beforeAction($action);
    }

    /**
     * Lists all AccountTransaction models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AccountTransactionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    /**
     * AccountTransaction view account state for each service.
     * @return mixed
     */
    public function actionState()
    {
        $searchModel = new AccountTransactionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        $tableData = [];
        
        $queryIn = AccountTransaction::find()
            ->where(['account_transaction.type' => AccountTransaction::TYPE_IN])
            ->andWhere(['is not', 'account_transaction.invoice_service_id', null])
            ->andWhere(['>', 'uid_date', date('Y-m-d', strtotime(time() . ' -1 year'))])
            ->orderBy(['uid_date' => SORT_DESC]);
        $itemsIn = $queryIn->asArray()->all();
        $queryOut = AccountTransaction::find()
            ->where(['account_transaction.type' => AccountTransaction::TYPE_OUT])
            ->andWhere(['is not', 'account_transaction.invoice_service_id', null])
            ->andWhere(['>', 'uid_date', date('Y-m-d', strtotime(time() . ' -1 year'))])
            ->orderBy(['uid_date' => SORT_DESC]);
        $itemsOut = $queryOut->asArray()->all();
        
        $totalIncome = 0;
        $totalOutcome = 0;
        foreach ($queryIn->each() as $item) {
            $month = Yii::$app->formatter->asDate(strtotime($item['uid_date']), 'LLLL, yyyy');
            if (!isset($tableData[$month])) {
                $tableData[$month] = [
                    'services' => [],
                ];
            }
            
            $service = $item['invoice_service_id'];
            $invoiceService = InvoiceService::findOne($service);
            if (!isset($tableData[$month]['services'][$service])) {
                $tableData[$month]['services'][$service] = [
                    'name' => $invoiceService->service->name,
                    'income' => 0,
                    'outcome' => 0,
                ];
            }
            
            if ($item['type'] == AccountTransaction::TYPE_IN) {
                $tableData[$month]['services'][$service]['income'] += $item['amount'];
                $totalIncome += $item['amount'];
            } elseif ($item['type'] == AccountTransaction::TYPE_OUT) {
                $tableData[$month]['services'][$service]['outcome'] += $item['amount'];
                $totalOutcome += $item['amount'];
            }
        }
        
        foreach ($queryOut->each() as $item) {
            $month = Yii::$app->formatter->asDate(strtotime($item['uid_date']), 'LLLL, yyyy');
            if (!isset($tableData[$month])) {
                $tableData[$month] = [
                    'services' => [],
                ];
            }
            
            $service = $item['invoice_service_id'];
            $invoiceService = InvoiceService::findOne($service);
            if (!isset($tableData[$month]['services'][$service])) {
                $tableData[$month]['services'][$service] = [
                    'name' => $invoiceService->service->name,
                    'income' => 0,
                    'outcome' => 0,
                ];
            }
            
            if ($item['type'] == AccountTransaction::TYPE_IN) {
                $tableData[$month]['services'][$service]['income'] += $item['amount'];
                $totalIncome += $item['amount'];
            } elseif ($item['type'] == AccountTransaction::TYPE_OUT) {
                $tableData[$month]['services'][$service]['outcome'] += $item['amount'];
                $totalOutcome += $item['amount'];
            }
        }

        return $this->render('state', [
            'tableData' => $tableData,
            'totalIncome' => $totalIncome,
            'totalOutcome' => $totalOutcome,
        ]);
    }

    /**
     * Displays a single AccountTransaction model.
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
     * Creates a new AccountTransaction model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param string $type in/out
     * @param integer $account_transaction_id
     * @param integer $account_id
     * @return mixed
     */
    public function actionCreate($type = '', $account_transaction_id = null, $account_id = null)
    {
        $model = new AccountTransaction();
        $post = Yii::$app->request->post();
        if ($accountTransactionId = Yii::$app->request->get('account_transaction_id')) {
            $modelClone = AccountTransaction::findOne($accountTransactionId);
            if ($modelClone) {
                $model->setAttributes($modelClone->attributes);
            }
            $model->id = null;
            $model->uid = null;
        } else {
            $model->currency_id = Currency::find()->orderBy(['is_default' => SORT_DESC])->one()->id;
            if ($type == AccountTransaction::TYPE_IN || $type == AccountTransaction::TYPE_OUT) {
                $model->type = $type;
            }
        }
        
        $model->generateUid();
        $model->uid_date = date(Yii::$app->params['dateFormat'], $model->uid_date ? strtotime($model->uid_date) : time());
        if ($model->status === null) {
            $model->status = AccountTransaction::STATUS_COMPLETE;
        }
        
        $user = Yii::$app->user->identity;
        if ($model->user_admin_id === null && in_array($user->role, UserAdmin::getUserTransactionRoles())) {
            $model->user_admin_id = $user->id;
        }
        
        $account = Account::findOne($account_id);
        if ($account) {
            $model->account_id = $account->id;
        }
        
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            return ActiveForm::validate($model);
        }
        
        if ($model->load($post)) {
            if ($model->validate()) {
                $model->uid_date = $model->uid_date ? date('Y-m-d', strtotime($model->uid_date)) : null;
            }
            if ($model->save()) {
                return $this->redirect(['index']);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing AccountTransaction model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $post = Yii::$app->request->post();

        $model->uid_date = date(Yii::$app->params['dateFormat'], strtotime($model->uid_date));
        if ($model->status === null) {
            $model->status = AccountTransaction::STATUS_COMPLETE;
        }
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            return ActiveForm::validate($model);
        }
        
        if ($model->load($post)) {
            if ($model->validate()) {
                $model->uid_date = $model->uid_date ? date('Y-m-d', strtotime($model->uid_date)) : null;
            }
            if ($model->save()) {
                return $this->redirect(['index']);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }
    
    /**
     * Export to excel existing AccountTransaction models.
     * @return mixed
     */
    public function actionExport()
    {
        $searchModel = new AccountTransactionSearch();
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
     * Export to excel an existing AccountTransaction model.
     * @return mixed
     */
    public function actionExportOne($id)
    {
        $model = $this->findModel($id);
        
        if ($model) {
            $path = $this->exportOne($model);
            return Yii::$app->response->sendFile(Yii::getAlias('@frontend/web').$path);
        }
        
        return $this->redirect(['/site/error']);
    }
    
    /**
     * Deletes an existing AccountTransaction model.
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
     * Finds the AccountTransaction model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AccountTransaction the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AccountTransaction::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
    
    /**
     * Export models
     * @param AccountTransaction[] $models
     */
    protected function export($models)
    {
        // file
        $path = '/upload/AccountTransaction/account_transactions_'.date('Ymd_His').'.xls';
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
        $sheet->setCellValueByColumnAndRow(1, 1, '#');
        $sheet->setCellValueByColumnAndRow(2, 1, 'Дата');
        $sheet->setCellValueByColumnAndRow(3, 1, 'Приход/расход');
        $sheet->setCellValueByColumnAndRow(4, 1, 'Статус');
        $sheet->setCellValueByColumnAndRow(5, 1, 'Статья');
        $sheet->setCellValueByColumnAndRow(6, 1, 'Квитанция');
        $sheet->setCellValueByColumnAndRow(7, 1, 'Услуга');
        $sheet->setCellValueByColumnAndRow(8, 1, 'Сумма');
        $sheet->setCellValueByColumnAndRow(9, 1, 'Валюта');
        $sheet->setCellValueByColumnAndRow(10, 1, 'Владелец квартиры');
        $sheet->setCellValueByColumnAndRow(11, 1, 'Лицевой счет');
        foreach ($models as $k => $model) {
            $minus = $model->type == AccountTransaction::TYPE_OUT ? '-' : '';
            $sheet->setCellValueByColumnAndRow(1, $k+2, $model->uid)->getStyleByColumnAndRow(1, $k+2)->applyFromArray($style);
            $sheet->setCellValueByColumnAndRow(2, $k+2, $model->uid_date ? date(Yii::$app->params['dateFormat'], strtotime($model->uid_date)) : '')->getStyleByColumnAndRow(2, $k+2)->applyFromArray($style);
            $sheet->setCellValueByColumnAndRow(3, $k+2, $model->getTypeLabel())->getStyleByColumnAndRow(3, $k+2)->applyFromArray($style);
            $sheet->setCellValueByColumnAndRow(4, $k+2, $model->getStatusLabel())->getStyleByColumnAndRow(4, $k+2)->applyFromArray($style);
            $sheet->setCellValueByColumnAndRow(5, $k+2, $model->transactionPurpose->name)->getStyleByColumnAndRow(5, $k+2)->applyFromArray($style);
            $sheet->setCellValueByColumnAndRow(6, $k+2, $model->invoice ? ($model->invoice->uid . ' от ' . $model->invoice->getUidDate()) : '')->getStyleByColumnAndRow(6, $k+2)->applyFromArray($style);
            $sheet->setCellValueByColumnAndRow(7, $k+2, $model->invoiceService ? ($model->invoiceService->service->name . ', сумма: ' . $model->invoiceService->price) : '')->getStyleByColumnAndRow(7, $k+2)->applyFromArray($style);
            $sheet->setCellValueByColumnAndRow(8, $k+2, $minus . $model->amount)->getStyleByColumnAndRow(8, $k+2)->applyFromArray($style);
            $sheet->setCellValueByColumnAndRow(9, $k+2, $model->currency->code)->getStyleByColumnAndRow(9, $k+2)->applyFromArray($style);
            $sheet->setCellValueByColumnAndRow(10, $k+2, ($model->account && $model->account->flat && $model->account->flat->user) ? $model->account->flat->user->getFullname() : '')->getStyleByColumnAndRow(10, $k+2)->applyFromArray($style);
            $sheet->setCellValueByColumnAndRow(11, $k+2, $model->account ? $model->account->uid : '')->getStyleByColumnAndRow(11, $k+2)->applyFromArray($style);
        }
        $sheet->getColumnDimensionByColumn(1)->setAutoSize(true);
        $sheet->getColumnDimensionByColumn(2)->setAutoSize(true);
        $sheet->getColumnDimensionByColumn(3)->setAutoSize(true);
        $sheet->getColumnDimensionByColumn(4)->setAutoSize(true);
        $sheet->getColumnDimensionByColumn(5)->setAutoSize(true);
        $sheet->getColumnDimensionByColumn(6)->setAutoSize(true);
        $sheet->getColumnDimensionByColumn(7)->setAutoSize(true);
        $sheet->getColumnDimensionByColumn(8)->setAutoSize(true);
        $sheet->getColumnDimensionByColumn(9)->setAutoSize(true);
        $sheet->getColumnDimensionByColumn(10)->setAutoSize(true);
        $sheet->getColumnDimensionByColumn(11)->setAutoSize(true);
        
        $writer = new Xls($spreadsheet);
        $writer->save(Yii::getAlias('@frontend/web').$path);
        
        chmod(Yii::getAlias('@frontend/web').$path, 0777);
        
        return $path;
    }
    
    /**
     * Export models
     * @param AccountTransaction $model
     */
    protected function exportOne($model)
    {
        // file
        $path = '/upload/AccountTransaction/account_transaction_'.$model->uid.'_'.date('Ymd_His', strtotime($model->uid_date)).'.xls';
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
        $sheet->setCellValueByColumnAndRow(1, 1, 'Платеж');
        $sheet->setCellValueByColumnAndRow(1, 2, 'Дата');
        $sheet->setCellValueByColumnAndRow(1, 3, 'Владелец квартиры');
        $sheet->setCellValueByColumnAndRow(1, 4, 'Лицевой счет');
        $sheet->setCellValueByColumnAndRow(1, 5, 'Приход/расход');
        $sheet->setCellValueByColumnAndRow(1, 6, 'Статус');
        $sheet->setCellValueByColumnAndRow(1, 7, 'Статья');
        $sheet->setCellValueByColumnAndRow(1, 8, 'Квитанция');
        $sheet->setCellValueByColumnAndRow(1, 9, 'Услуга');
        $sheet->setCellValueByColumnAndRow(1, 10, 'Сумма');
        $sheet->setCellValueByColumnAndRow(1, 11, 'Валюта');
        $sheet->setCellValueByColumnAndRow(1, 12, 'Комментарий');
        $sheet->setCellValueByColumnAndRow(1, 13, 'Менеджер');
        
        $minus = $model->type == AccountTransaction::TYPE_OUT ? '-' : '';
        $sheet->setCellValueByColumnAndRow(2, 1, '#' . $model->uid)->getStyleByColumnAndRow(2, 1)->applyFromArray($style);
        $sheet->setCellValueByColumnAndRow(2, 2, $model->uid_date ? date(Yii::$app->params['dateFormat'], strtotime($model->uid_date)) : '')->getStyleByColumnAndRow(2, 2)->applyFromArray($style);
        $sheet->setCellValueByColumnAndRow(2, 3, $model->account->flat->user ? $model->account->flat->user->getFullname() : '')->getStyleByColumnAndRow(2, 3)->applyFromArray($style);
        $sheet->setCellValueByColumnAndRow(2, 4, $model->account->uid)->getStyleByColumnAndRow(2, 4)->applyFromArray($style);
        $sheet->setCellValueByColumnAndRow(2, 5, $model->getTypeLabel())->getStyleByColumnAndRow(2, 5)->applyFromArray($style);
        $sheet->setCellValueByColumnAndRow(2, 6, $model->getStatusLabel())->getStyleByColumnAndRow(2, 6)->applyFromArray($style);
        $sheet->setCellValueByColumnAndRow(2, 7, $model->transactionPurpose->name)->getStyleByColumnAndRow(2, 7)->applyFromArray($style);
        $sheet->setCellValueByColumnAndRow(2, 8, $model->invoice ? ($model->invoice->uid . ' от ' . $model->invoice->getUidDate()) : '')->getStyleByColumnAndRow(2, 8)->applyFromArray($style);
        $sheet->setCellValueByColumnAndRow(2, 9, $model->invoiceService ? ($model->invoiceService->service->name . ', сумма: ' . $model->invoiceService->price) : '')->getStyleByColumnAndRow(2, 9)->applyFromArray($style);
        $sheet->setCellValueByColumnAndRow(2, 10, $minus . $model->amount)->getStyleByColumnAndRow(2, 10)->applyFromArray($style);
        $sheet->setCellValueByColumnAndRow(2, 11, $model->currency->code)->getStyleByColumnAndRow(2, 11)->applyFromArray($style);
        $sheet->setCellValueByColumnAndRow(2, 12, $model->description)->getStyleByColumnAndRow(2, 12)->applyFromArray($style);
        $sheet->setCellValueByColumnAndRow(2, 13, $model->userAdmin ? $model->userAdmin->getFullname() : '')->getStyleByColumnAndRow(2, 13)->applyFromArray($style);
        
        $sheet->getColumnDimensionByColumn(1)->setAutoSize(true);
        $sheet->getColumnDimensionByColumn(2)->setAutoSize(true);
        
        $writer = new Xls($spreadsheet);
        $writer->save(Yii::getAlias('@frontend/web').$path);
        
        chmod(Yii::getAlias('@frontend/web').$path, 0777);
        
        return $path;
    }
}
