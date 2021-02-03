<?php

namespace backend\controllers;

use Yii;
use common\models\Invoice;
use backend\models\InvoiceSearch;
use common\models\InvoiceService;
use common\models\Tariff;
use common\models\TariffService;
use common\models\ServiceUnit;
use common\models\Service;
use common\models\CounterData;
use common\models\Flat;
use common\models\PayCompany;
use common\models\UserAdmin;
use backend\controllers\ZController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\widgets\ActiveForm;
use yii\filters\AccessControl;
use backend\models\InvoiceTemplateForm;
use common\models\InvoiceTemplate;

/**
 * InvoiceController implements the CRUD actions for Invoice model.
 */
class InvoiceController extends ZController
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
        $actions[] = 'ajax-get-form-invoice-service';
        $actions[] = 'ajax-get-forms-by-tariff';
        $actions[] = 'ajax-get-forms-by-counters';
        $actions[] = 'ajax-get-service';
        $actions[] = 'ajax-get-service-options';
        $actions[] = 'ajax-get-counter-data-options';
        $actions[] = 'ajax-get-invoice-service-options';
        $actions[] = 'ajax-get-invoice-price';
        $actions[] = 'ajax-get-invoice-service-price';
        if (!Yii::$app->user->can(UserAdmin::PERMISSION_INVOICE) && !in_array($action->id, $actions)) {
            // return $this->redirect(['/site/error']);
            throw new \yii\web\ForbiddenHttpException(Yii::t('app', static::FORBIDDEN_HTTP_MESSAGE));
        }
        return parent::beforeAction($action);
    }

    /**
     * Lists all Invoice models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new InvoiceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Invoice model.
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
     * Creates a new Invoice model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param integer $invoice_id
     * @param integer $flat_id
     * @return mixed
     */
    public function actionCreate($invoice_id = null, $flat_id = null)
    {
        $model = new Invoice();
        
        if ($invoice_id) {
            $modelClone = Invoice::findOne($invoice_id);
            if ($modelClone) {
                $model->setAttributes($modelClone->attributes);
                $model->id = null;
                $model->uid = null;
                
                $invoiceServices = [];
                foreach ($modelClone->invoiceServices as $invoiceServiceClone) {
                    $invoiceService = new InvoiceService();
                    $invoiceService->setAttributes($invoiceServiceClone->attributes);
                    $invoiceService->id = null;
                    $invoiceService->invoice_id = null;
                    $invoiceServices[] = $invoiceService;
                }
                
                if ($invoiceServices) {
                    $model->populateRelation('invoiceServices', $invoiceServices);
                }
            }
        }
        
        $model->is_checked = 1;
        $model->status = Invoice::STATUS_UNPAID;
        $model->uid = date('dmy') . sprintf('%05d', Invoice::find()->max('id') + 1);
        $model->uid_date = date(Yii::$app->params['dateFormat'], $model->uid_date ? strtotime($model->uid_date) : time());
        $model->period_start = date(Yii::$app->params['dateFormat'], $model->period_start ? strtotime($model->period_start) : time());
        $model->period_end = date(Yii::$app->params['dateFormat'], $model->period_end ? strtotime($model->period_end) : time());
        $flat = Flat::findOne($flat_id);
        if ($flat) {
            $model->flat_id = $flat->id;
        }
        if ($flat && $model->flat_id) {
            $model->tariff_id = $flat->tariff_id;
        }
        
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            return ActiveForm::validate($model);
        }
        
        $invoiceMonthYear = date('m.Y', $model->period_end ? strtotime($model->period_end) : time());

        if ($model->load(Yii::$app->request->post())) {
            $invoiceMonthYear = Yii::$app->request->post('invoiceMonthYear');

            if ($model->validate()) {
                $model->uid_date = $model->uid_date ? date('Y-m-d', strtotime($model->uid_date)) : null;
                $model->period_start = $model->period_start ? date('Y-m-d', strtotime($model->period_start)) : null;
                $model->period_end = $model->period_end ? date('Y-m-d', strtotime($model->period_end)) : null;
//                $model->period_start = $invoiceMonthYear ? date('Y-m-01', strtotime('01.'.$invoiceMonthYear)) : null;
//                $model->period_end = $invoiceMonthYear ? date('Y-m-t', strtotime('01.'.$invoiceMonthYear)) : null;
            }
            if ($model->save()) {
                $this->saveInvoiceServices($model);
                return $this->redirect(['index']);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'invoiceMonthYear' => $invoiceMonthYear,
        ]);
    }

    /**
     * Updates an existing Invoice model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        
        $model->uid_date = date(Yii::$app->params['dateFormat'], strtotime($model->uid_date));
        $model->period_start = date(Yii::$app->params['dateFormat'], strtotime($model->period_start));
        $model->period_end = date(Yii::$app->params['dateFormat'], strtotime($model->period_end));

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            return ActiveForm::validate($model);
        }
        
        $invoiceMonthYear = date('m.Y', strtotime($model->period_end));
        
        if ($model->load(Yii::$app->request->post())) {
            $invoiceMonthYear = Yii::$app->request->post('invoiceMonthYear');
            
            if ($model->validate()) {
                $model->uid_date = $model->uid_date ? date('Y-m-d', strtotime($model->uid_date)) : null;
                $model->period_start = $model->period_start ? date('Y-m-d', strtotime($model->period_start)) : null;
                $model->period_end = $model->period_end ? date('Y-m-d', strtotime($model->period_end)) : null;
//                $model->period_start = $invoiceMonthYear ? date('Y-m-01', strtotime('01.'.$invoiceMonthYear)) : null;
//                $model->period_end = $invoiceMonthYear ? date('Y-m-t', strtotime('01.'.$invoiceMonthYear)) : null;
            }
            if ($model->save()) {
                $this->saveInvoiceServices($model);
                return $this->redirect(['index']);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'invoiceMonthYear' => $invoiceMonthYear,
        ]);
    }

    /**
     * Deletes an existing Invoice model.
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
     * Print a single Invoice model to file and download.
     * @param integer $id
     * @return mixed
     */
    public function actionPrint($id)
    {
        $model = $this->findModel($id);
        $post = Yii::$app->request->post();
        
        if ($post) {
            $templateId = $post['invoice_template_id'];
            $templateType = $post['invoice_template_type'];
            $template = InvoiceTemplate::findOne($templateId);
            if ($template) {
                $invoiceFilePath = $model->getTemplateFile($template, $templateType);
                
                if (isset($post['action_download'])) {
                    return Yii::$app->response->sendFile(Yii::getAlias('@frontend/web').$invoiceFilePath);
                } elseif (isset($post['action_send_email'])) {
                    $model->sendTemplateFileToEmail($invoiceFilePath);
                }
            }
            return $this->redirect(['print', 'id' => $model->id]);
        }
        
        return $this->render('print', [
            'model' => $model,
            'templateModels' => InvoiceTemplate::find()->orderBy(['id' => SORT_DESC])->all(),
        ]);
    }
    
    /**
     * Change invoice templates.
     * @param integer $default_id
     * @param integer $delete_id
     * @return mixed
     */
    public function actionTemplate($default_id = null, $delete_id = null)
    {
        if ($default_id) {
            $model = InvoiceTemplate::findOne($default_id);
            $model->is_default = 1;
            $model->save();
            return $this->redirect(['template']);
        }
        if ($delete_id) {
            InvoiceTemplate::findOne($delete_id)->delete();
            return $this->redirect(['template']);
        }
        
        $model = new InvoiceTemplate();
        $post = Yii::$app->request->post();

        $modelForm = new InvoiceTemplateForm();
        if ($modelForm->load($post)) {
            if ($model = $modelForm->process()) {
                return $this->redirect(['template']);
            }
        }

        return $this->render('template', [
            'modelForm' => $modelForm,
            'models' => InvoiceTemplate::find()->orderBy(['id' => SORT_DESC])->all(),
        ]);
    }
    
    /**
     * Ajax delete many models by ids
     * 
     * @return mixed
     */
    public function actionAjaxDeleteMany()
    {
        $ids = Yii::$app->request->post('ids');
        
        Invoice::deleteAll(['in', 'id', $ids]);
        
        // Yii::$app->session->addFlash('success', 'Данные удалены');
        
        return [
            'ids' => $ids,
            'status' => 'success',
            'message' => 'Данные удалены',
        ];
    }
    
    /**
     * @return string
     */
    public function actionAjaxGetFormInvoiceService()
    {
        $formId = (int)Yii::$app->request->get('form_id');
        $invoiceId = (int)Yii::$app->request->get('invoice_id');
        $tariffId = (int)Yii::$app->request->get('tariff_id');
        $payCompanyId = (int)Yii::$app->request->get('pay_company_id');
        $model = new InvoiceService();
        $model->invoice_id = (int)$invoiceId;
        $tariffModel = Tariff::findOne($tariffId);
        $payCompanyModel = PayCompany::findOne($payCompanyId);
        return $this->renderAjax('_form-invoiceservice', [
            'formId' => $formId,
            'model' => $model,
            'tariffModel' => $tariffModel,
            'payCompanyModel' => $payCompanyModel,
        ]);
    }
    
    /**
     * @return string
     */
    public function actionAjaxGetFormsByTariff()
    {
        $formId = (int)Yii::$app->request->get('form_id');
        $invoiceId = (int)Yii::$app->request->get('invoice_id');
        $tariffId = (int)Yii::$app->request->get('tariff_id');
        $payCompanyId = (int)Yii::$app->request->get('pay_company_id');
        $tariffModel = Tariff::findOne($tariffId);
        $payCompanyModel = PayCompany::findOne($payCompanyId);
        $forms = '';
        $updateData = [];
        if ($tariffModel) {
            foreach ($tariffModel->tariffServices as $tariffService) {
                $model = new InvoiceService();
                $model->invoice_id = (int)$invoiceId;
                $model->service_id = $tariffService->service_id;
                $model->price_unit = $tariffService->price_unit;
                
                $forms .= $this->renderAjax('_form-invoiceservice', [
                    'formId' => $formId,
                    'model' => $model,
                    'tariffModel' => $tariffModel,
                    'payCompanyModel' => $payCompanyModel,
                ]);
                $updateData[] = [
                    'service_id' => $model->service_id,
                    'price_unit' => $model->price_unit,
                ];
                
                $formId++;
            }
        }
        return [
            'html' => $forms,
            'data' => $updateData,
        ];
    }
    
    /**
     * @return string
     */
    public function actionAjaxGetFormsByCounters()
    {
        $formId = (int)Yii::$app->request->get('form_id');
        $invoiceId = (int)Yii::$app->request->get('invoice_id');
        $tariffId = (int)Yii::$app->request->get('tariff_id');
        $flatId = (int)Yii::$app->request->get('flat_id');
        $payCompanyId = (int)Yii::$app->request->get('pay_company_id');
        $dateMY = Yii::$app->request->get('date_my');
        $tariffModel = Tariff::findOne($tariffId);
        $flatModel = Flat::findOne($flatId);
        $payCompanyModel = PayCompany::findOne($payCompanyId);
        $forms = '';
        $updateData = [];
        if ($tariffModel && $flatModel) {
            $ts = $dateMY ? strtotime('01.'.$dateMY) : time();
            $counterDatasQuery = CounterData::find()
                ->where(['and', ['flat_id' => $flatModel->id], ['in', 'status', [CounterData::STATUS_NEW]]])
                ->andWhere(['and', ['>=', 'uid_date', date('Y-m-01', $ts)], ['<', 'uid_date', date('Y-m-01', strtotime(' +1 month', $ts))]]);

            $counterDatas = $counterDatasQuery->all();
            
            foreach ($counterDatas as $counterData) {
                $tariffService = $tariffModel->getTariffServices()
                    ->andWhere(['service_id' => $counterData->service_id])
                    ->one();
                
                $model = new InvoiceService();
                $model->invoice_id = (int)$invoiceId;
                $model->service_id = $counterData->service_id;
                $model->amount = $counterData->amount;
                $model->price_unit = $tariffService ? $tariffService->price_unit : null;
                $model->price = $model->price_unit ? ($model->price_unit * $model->amount) : null; 
                $model->counter_data_id = $counterData->id;
                
                $forms .= $this->renderAjax('_form-invoiceservice', [
                    'formId' => $formId,
                    'model' => $model,
                    'tariffModel' => $tariffModel,
                    'payCompanyModel' => $payCompanyModel,
                    'flatModel' => $flatModel,
                ]);
                $updateData[] = [
                    'service_id' => $model->service_id,
                    'amount' => $model->amount,
                    'counter_data_id' => $model->counter_data_id,
                ];
                
                $formId++;
            }
        }
        return [
            'html' => $forms,
            'data' => $updateData,
        ];
    }
    
    public function actionAjaxGetService($service_id = null, $tariff_id = null, $flat_id = null)
    {
        $service = null;
        $serviceUnit = null;
        $serviceUnitsData = '<option value="">Выберите...</option>'."\r\n";
        $tariff = null;
        $tariffService = null;
        $flat = null;
        $amount = 0;
        
        if ($tariff_id) {
            $tariff = Tariff::findOne($tariff_id);
        }
        
        if ($service_id) {
            $service = Service::findOne($service_id);
            $serviceUnit = $service->serviceUnit;
            $tariffService = TariffService::find()->where(['service_id' => $service->id, 'tariff_id' => $tariff->id])->one();
        }
        
        $serviceUnits = ServiceUnit::find()->all();
        foreach ($serviceUnits as $serviceUnitItem) {
            if ($serviceUnit && $serviceUnitItem->id == $serviceUnit->id) {
                $serviceUnitsData .= '<option value="' . $serviceUnitItem->id . '" selected="selected">' . $serviceUnitItem->name . '</option>'."\r\n";
            } else {
                $serviceUnitsData .= '<option value="' . $serviceUnitItem->id . '">' . $serviceUnitItem->name . '</option>'."\r\n";
            }
        }
        
        if ($flat_id) {
            $flat = Flat::findOne($flat_id);
        }
        
        if ($flat && $serviceUnit) {
            if ($serviceUnit->id == 4 || in_array($serviceUnit->name, ['кв.м.', 'кв.м', 'м.кв.', 'м.кв.', 'м2'])) {
                $amount = $flat->square;
            }
        }
        
        return [
            'service' => $service,
            'serviceUnit' => $serviceUnit,
            'serviceUnits' => $serviceUnitsData,
            'tariffService' => $tariffService,
            'amount' => $amount
        ];
    }
    
    /**
     * 
     * @param int $service_id
     * @param int $pay_company_id
     * @return string
     */
    public function actionAjaxGetServiceOptions($service_id = null, $pay_company_id = null)
    {
        $payCompany = PayCompany::findOne($pay_company_id);
        $options = Service::getOptions($payCompany);
        $servicesData = '<option value="">Выберите...</option>'."\r\n";
        if (is_array(array_shift(array_slice($options, 0, 1)))) {
            foreach ($options as $label => $options2) {
                $servicesData .= '<optgroup label="'.$label.'">';
                foreach ($options2 as $id => $option) {
                    if ($id == $service_id) {
                        $servicesData .= '<option value="' . $id . '" selected="selected">' . $option . '</option>'."\r\n";
                    } else {
                        $servicesData .= '<option value="' . $id . '">' . $option . '</option>'."\r\n";
                    }
                }
                $servicesData .= '</optgroup>';
            }
        } else {
            foreach ($options as $id => $option) {
                if ($id == $service_id) {
                    $servicesData .= '<option value="' . $id . '" selected="selected">' . $option . '</option>'."\r\n";
                } else {
                    $servicesData .= '<option value="' . $id . '">' . $option . '</option>'."\r\n";
                }
            }
        }
        
        return ['servicesData' => $servicesData];
    }
    
    /**
     * @param int $invoice_id
     * @return string
     */
    public function actionAjaxGetInvoicePrice($invoice_id = null)
    {
        $invoice = Invoice::findOne($invoice_id);
        return ['price' => $invoice ? $invoice->getPrice() : ''];
    }
    
    /**
     * 
     * @param int $service_id
     * @param int $flat_id
     * @param int $current_id
     * @return string
     */
    public function actionAjaxGetCounterDataOptions($service_id = null, $flat_id = null, $current_id = null)
    {
        $service = Service::findOne($service_id);
        $flat = Flat::findOne($flat_id);
        $counterData = '<option value="">Выберите...</option>'."\r\n";
        
        $options = CounterData::getOptions($service, $flat, $current_id);
        foreach ($options as $id => $option) {
            $counterData .= '<option value="' . $id . '">' . $option . '</option>'."\r\n";
        }

        return ['counterData' => $counterData];
    }
    
    /**
     * 
     * @param int $invoice_id
     * @return string
     */
    public function actionAjaxGetInvoiceServiceOptions($invoice_id = null)
    {
        $invoice = Invoice::findOne($invoice_id);
        $options = $invoice->getInvoiceServiceOptions();
        $invoiceServicesData = '<option value="">Выберите...</option>'."\r\n";
        foreach ($options as $id => $option) {
            $invoiceServicesData .= '<option value="' . $id . '">' . $option . '</option>'."\r\n";
        }
        
        return ['invoiceServicesData' => $invoiceServicesData];
    }
    
    /**
     * @param int $invoice_service_id
     * @return string
     */
    public function actionAjaxGetInvoiceServicePrice($invoice_service_id = null)
    {
        $invoiceService = InvoiceService::findOne($invoice_service_id);
        return ['price' => $invoiceService ? $invoiceService->price : ''];
    }
    
    /**
     * @param Invoice $model
     */
    protected function saveInvoiceServices($model)
    {
        $modelsPostIdx = 'InvoiceService';
        $ids = [];
        if ($postModels = Yii::$app->request->post()[$modelsPostIdx]) {
            foreach ($postModels as $postModel) {
                $subModel = InvoiceService::findOne($postModel['id']);
                if (!$subModel) {
                    $subModel = new InvoiceService();
                }
                $subModel->load([$modelsPostIdx => $postModel]);
                $subModel->amount = floatval($subModel->amount);
                $subModel->price = floatval($subModel->price);
                if (!$subModel->invoice_id) {
                    $subModel->invoice_id = $model->id;
                }
                $subModel->save();
                
                // update counter data status
                if ($subModel->counterData) {
                    $subModel->counterData->changeStatus($model->status == Invoice::STATUS_PAID ? CounterData::STATUS_PAY_DONE : CounterData::STATUS_ACTIVE);
                }

                $ids[] = $subModel->id;
            }
        }
        InvoiceService::deleteAll(['and', ['invoice_id' => $model->id], ['not in', 'id', $ids]]);
        
        // save transaction
        $model->makeTransactionForInvoice();
    }
    
    /**
     * Finds the Invoice model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Invoice the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Invoice::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
