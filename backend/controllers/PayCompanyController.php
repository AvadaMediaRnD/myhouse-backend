<?php
namespace backend\controllers;

use common\models\PayCompany;
use common\models\PayCompanyService;
use common\models\UserAdmin;
use Yii;
use yii\filters\AccessControl;
//use yii\web\Controller;
use backend\controllers\ZController as Controller;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use common\models\Service;

/**
 * PayCompany controller
 */
class PayCompanyController extends Controller
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
        $actions[] = 'ajax-get-form-pay-company';
        if (!Yii::$app->user->can(UserAdmin::PERMISSION_PAY_COMPANY) && !in_array($action->id, $actions)) {
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
            $this->savePayCompanies();
            return $this->redirect(['index']);
        }
        
        $payCompanies = PayCompany::find()->all();
        
        return $this->render('index', [
            'payCompanies' => $payCompanies,
        ]);
    }
    
    /**
     * 
     * @return mixed
     */
    public function actionCreate()
    {
        return $this->render('create', [
            
        ]);
    }
    
    /**
     * 
     * @return mixed
     */
    public function actionUpdate($id)
    {
        return $this->render('update', [
            
        ]);
    }
    
    /**
     * 
     * @return mixed
     */
    public function actionDelete($id)
    {
        return $this->redirect(['index']);
    }
    
    /**
     * 
     * @return string
     */
    public function actionAjaxGetFormPayCompany()
    {
        $formId = (int)Yii::$app->request->get('form_id');
        $model = new PayCompany();
        return $this->renderAjax('_form-paycompany', [
            'formId' => $formId,
            'model' => $model,
        ]);
    }
    
    /**
     * Finds the PayCompany model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PayCompany the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PayCompany::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    /**
     * 
     */
    protected function savePayCompanies()
    {
        $allServiceIds = ArrayHelper::getColumn(Service::find()->all(), 'id');
        
        $modelsPostIdx = 'PayCompany';
        if ($postModels = Yii::$app->request->post()[$modelsPostIdx]) {
            $ids = [];
            foreach ($postModels as $k => $postModel) {
                $model = PayCompany::findOne($postModel['id']);
                if (!$model) {
                    $model = new PayCompany();
                }
                $model->load([$modelsPostIdx => $postModel]);
                if ($model->save()) {
                    $serviceIds = [];
//                    if ($postModel['service_id']) {
//                        foreach ($postModel['service_id'] as $serviceId) {
//                            //
//                        }
//                    }
                    if ($allServiceIds) {
                        foreach ($allServiceIds as $serviceId) {
                            $serviceModel = PayCompanyService::find()->where(['pay_company_id' => $model->id, 'service_id' => $serviceId])->one();
                            if (!$serviceModel) {
                                $serviceModel = new PayCompanyService();
                            }
                            $serviceModel->pay_company_id = $model->id;
                            $serviceModel->service_id = $serviceId;
                            $serviceModel->save();
                            $serviceIds[] = $serviceModel->service_id;
                        }
                    }
                    PayCompanyService::deleteAll([
                        'and', 
                        ['pay_company_id' => $model->id], 
                        ['not in', 'service_id', $serviceIds]
                    ]);
                }
                $ids[] = $model->id;
            } 
            PayCompany::deleteAll(['not in', 'id', $ids]);
        }
    }

}
