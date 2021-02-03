<?php
namespace backend\controllers;

use common\models\Service;
use common\models\ServiceUnit;
use common\models\InvoiceService;
use common\models\UserAdmin;
use Yii;
use yii\filters\AccessControl;
//use yii\web\Controller;
use backend\controllers\ZController as Controller;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

/**
 * Service controller
 */
class ServiceController extends Controller
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
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function beforeAction($action) {
        $actions = $this->getAllowedActions();
        $actions[] = 'ajax-get-form-service';
        $actions[] = 'ajax-get-form-service-unit';
        $actions[] = 'ajax-get-service-units';
        if (!Yii::$app->user->can(UserAdmin::PERMISSION_SERVICE) && !in_array($action->id, $actions)) {
            // return $this->redirect(['/site/error']);
            throw new \yii\web\ForbiddenHttpException(Yii::t('app', static::FORBIDDEN_HTTP_MESSAGE));
        }
        return parent::beforeAction($action);
    }
    
    /**
     * 
     * @return mixed
     */
    public function actionIndex()
    {
        if (Yii::$app->request->isPost) {
            $this->saveServices();
            $this->saveServiceUnits();
            return $this->redirect(['index']);
        }
        
        $services = Service::find()->all();
        $serviceUnits = ServiceUnit::find()->all();
        
        return $this->render('index', [
            'services' => $services,
            'serviceUnits' => $serviceUnits,
        ]);
    }
    
    /**
     * 
     * @return mixed
     */
    public function actionDeleteService($id)
    {
        return $this->redirect(['index']);
    }
    
    /**
     * 
     * @return mixed
     */
    public function actionDeleteServiceUnit($id)
    {
        return $this->redirect(['index']);
    }
    
    /**
     * 
     * @return string
     */
    public function actionAjaxGetFormService()
    {
        $formId = (int)Yii::$app->request->get('form_id');
        $model = new Service();
        return $this->renderAjax('_form-service', [
            'formId' => $formId,
            'model' => $model,
        ]);
    }
    
    /**
     * 
     * @return string
     */
    public function actionAjaxGetFormServiceUnit()
    {
        $formId = (int)Yii::$app->request->get('form_id');
        $model = new ServiceUnit();
        return $this->renderAjax('_form-serviceunit', [
            'formId' => $formId,
            'model' => $model,
        ]);
    }
    
    /**
     * @param $service_id
     * @return array
     */
    public function actionAjaxGetServiceUnits($service_id = null)
    {
        $serviceModel = Service::findOne($service_id);
        $serviceUnits = ServiceUnit::find()->all();

        $serviceUnitData = '<option value="">Выберите...</option>'."\r\n";
        $serviceUnitValue = '';
        if ($serviceUnits) {
            foreach ($serviceUnits as $serviceUnit) {
                if ($serviceModel && $serviceUnit->id == $serviceModel->service_unit_id) {
                    $serviceUnitData .= '<option value="' . $serviceUnit->id . '" selected="selected">' . $serviceUnit->name . '</option>'."\r\n";
                    $serviceUnitValue = $serviceUnit->id;
                } else {
                    $serviceUnitData .= '<option value="' . $serviceUnit->id . '">' . $serviceUnit->name . '</option>'."\r\n";
                }
            }
        }
        
        return [
            'serviceUnitData' => $serviceUnitData,
            'serviceUnitValue' => $serviceUnitValue,
        ];
    }
    
    /**
     * 
     */
    protected function saveServices()
    {
        $modelsPostIdx = 'Service';
        if ($postModels = Yii::$app->request->post()[$modelsPostIdx]) {
            // we dont delete services used in invoices
            $ids = ArrayHelper::getColumn(InvoiceService::find()->all(), 'service_id');
            foreach ($postModels as $postModel) {
                $model = Service::findOne($postModel['id']);
                if (!$model) {
                    $model = new Service();
                }
                $model->load([$modelsPostIdx => $postModel]);
                $model->save();

                $ids[] = $model->id;
            }
            Service::deleteAll(['not in', 'id', $ids]);
        }
    }
    
    /**
     * 
     */
    protected function saveServiceUnits()
    {
        $modelsPostIdx = 'ServiceUnit';
        if ($postModels = Yii::$app->request->post()[$modelsPostIdx]) {
            $ids = [];
            foreach ($postModels as $postModel) {
                $model = ServiceUnit::findOne($postModel['id']);
                if (!$model) {
                    $model = new ServiceUnit();
                }
                $model->load([$modelsPostIdx => $postModel]);
                $model->save();

                $ids[] = $model->id;
            }
            ServiceUnit::deleteAll(['not in', 'id', $ids]);
        }
    }

}
