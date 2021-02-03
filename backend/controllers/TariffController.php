<?php
namespace backend\controllers;

use common\models\Tariff;
use common\models\TariffService;
use common\models\UserAdmin;
use backend\models\TariffSearch;
use Yii;
use yii\filters\AccessControl;
//use yii\web\Controller;
use backend\controllers\ZController as Controller;
use yii\filters\VerbFilter;
use yii\helpers\Html;

/**
 * Tariff controller
 */
class TariffController extends Controller
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
        $actions[] = 'ajax-get-form-tariff-service';
        if (!Yii::$app->user->can(UserAdmin::PERMISSION_TARIFF) && !in_array($action->id, $actions)) {
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
        $searchModel = new TariffSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    /**
     * Displays a single Tariff model.
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
     * @param integer $tariff_id
     * @return mixed
     */
    public function actionCreate($tariff_id = null)
    {
        $model = new Tariff();
        if ($tariffId = Yii::$app->request->get('tariff_id')) {
            $modelClone = Tariff::findOne($tariffId);
            if ($modelClone) {
                $model->setAttributes($modelClone->attributes);
            }
            $model->id = null;
            
            $tariffServices = [];
            foreach ($modelClone->tariffServices as $tariffServiceClone) {
                $tariffService = new TariffService();
                $tariffService->setAttributes($tariffServiceClone->attributes);
                $tariffService->id = null;
                $tariffService->tariff_id = null;
                $tariffServices[] = $tariffService;
            }

            if ($tariffServices) {
                $model->populateRelation('tariffServices', $tariffServices);
            }
        }
        $post = Yii::$app->request->post();
        
        if ($model->load($post)) {
            if ($model->save()) {
                $this->saveTariffServices($model);
                return $this->redirect(['index']);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }
    
    /**
     * 
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $post = Yii::$app->request->post();

        if ($model->load($post)) {
            if ($model->save()) {
                $this->saveTariffServices($model);
                return $this->redirect(['index']);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }
    
    /**
     * 
     * @return mixed
     */
    public function actionDelete($id)
    {
        if (Tariff::find()->count() > 1) {
            $model = $this->findModel($id);
            $model->delete();
        } else {
            // Yii::$app->session->addFlash('warning', 'В системе должен быть хотя бы 1 тариф.');
        }
        
        return $this->redirect(['index']);
    }
    
    /**
     * 
     * @return string
     */
    public function actionAjaxGetFormTariffService()
    {
        $formId = (int)Yii::$app->request->get('form_id');
        $tariffId = (int)Yii::$app->request->get('tariff_id');
        $tariff = Tariff::findOne($tariffId);
        $model = new TariffService();
        $model->tariff_id = $tariff->id;
        return $this->renderAjax('_form-tariffservice', [
            'formId' => $formId,
            'model' => $model,
        ]);
    }
    
    /**
     * Finds the Tariff model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Tariff the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Tariff::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    /**
     * @param Tariff $model
     */
    protected function saveTariffServices($model)
    {
        $modelsPostIdx = 'TariffService';
        $ids = [];
        if ($postModels = Yii::$app->request->post()[$modelsPostIdx]) {
            foreach ($postModels as $postModel) {
                $subModel = TariffService::findOne($postModel['id']);
                if (!$subModel) {
                    $subModel = new TariffService();
                }
                $subModel->load([$modelsPostIdx => $postModel]);
                $subModel->price_unit = floatval($subModel->price_unit);
                if (!$subModel->tariff_id) {
                    $subModel->tariff_id = $model->id;
                }
                $subModel->save();

                $ids[] = $subModel->id;
            }
        }
        TariffService::deleteAll(['and', ['tariff_id' => $model->id], ['not in', 'id', $ids]]);
    }
}
