<?php

namespace backend\controllers;

use Yii;
use common\models\CounterData;
use common\models\Flat;
use common\models\UserAdmin;
use backend\models\CounterDataSearch;
use backend\controllers\ZController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\widgets\ActiveForm;
use yii\filters\AccessControl;
use common\models\User;
use yii\helpers\ArrayHelper;
use common\models\Service;

/**
 * CounterDataController implements the CRUD actions for CounterData model.
 */
class CounterDataController extends ZController
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
        $actions[] = 'ajax-get-amount';
        if (!Yii::$app->user->can(UserAdmin::PERMISSION_COUNTER_DATA) && !in_array($action->id, $actions)) {
            // return $this->redirect(['/site/error']);
            throw new \yii\web\ForbiddenHttpException(Yii::t('app', static::FORBIDDEN_HTTP_MESSAGE));
        }
        return parent::beforeAction($action);
    }

    /**
     * Lists all CounterData models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CounterDataSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    /**
     * Lists all Counters.
     * @return mixed
     */
    public function actionCounters()
    {
        $searchModel = new CounterDataSearch();
        $dataProvider = $searchModel->searchCounters(Yii::$app->request->queryParams);

        return $this->render('counters', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    /**
     * Lists all CounterData models for counter.
     * @return mixed
     */
    public function actionCounterList()
    {
        $searchModel = new CounterDataSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('counter-list', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    /**
     * Lists all CounterData models for selected user or flat.
     * @return mixed
     */
    public function actionFilter()
    {
        $userSelectData = ArrayHelper::map(User::find()->all(), 'id', 'fullname');
        $serviceOptions = Service::getOptions();
        $userFlatOptions = [];
        $params = Yii::$app->request->queryParams;
        $searchUser = $params['CounterDataSearch']['searchUser'];
        if ($searchUser) {
            $user = User::findOne($searchUser);
            if ($user) {
                $userFlatOptions = ArrayHelper::map($user->flats, 'flat', function ($model) {
                    return $model->flat . ', ' . $model->house->name;
                });
            }
        }
        if (!isset($userFlatOptions[$params['CounterDataSearch']['searchFlat']])) {
            unset($params['CounterDataSearch']['searchFlat']);
        }
        
        $searchModel = new CounterDataSearch();
        $dataProvider = $searchModel->search($params);
        
        return $this->render('filter', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'userSelectData' => $userSelectData,
            'userFlatOptions' => $userFlatOptions,
            'serviceOptions' => $serviceOptions,
        ]);
    }

    /**
     * Displays a single CounterData model.
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
     * Creates a new CounterData model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param int $counter_data_id
     * @param int $flat_id
     * @param int $service_id
     * @return mixed
     */
    public function actionCreate($flat_id = null, $service_id = null)
    {
        $model = new CounterData();
        $post = Yii::$app->request->post();
        if ($counterDataId = Yii::$app->request->get('counter_data_id')) {
            $modelClone = CounterData::findOne($counterDataId);
            if ($modelClone) {
                $model->setAttributes($modelClone->attributes);
            }
            $model->id = null;
            $model->uid = null;
            $model->status = CounterData::STATUS_NEW;
            $model->flat_id = null;
            $model->counter_data_last_id = null;
            $flatNext = Flat::find()
                ->where(['flat' => $modelClone->flat->flat + 1])
                ->andWhere(['house_id' => $modelClone->flat->house_id, 'section_id' => $modelClone->flat->section_id])
                ->orderBy(['flat' => SORT_ASC])
                ->one();
            if ($flatNext) {
                $model->flat_id = $flatNext->id;
            }
        }
        
        if ($flat_id) {
            if (Flat::find()->where(['id' => $flat_id])->exists()) {
                $model->flat_id = $flat_id;
            }
        }
        if ($service_id) {
            if (Service::find()->where(['id' => $service_id])->exists()) {
                $model->service_id = $service_id;
            }
        }

        $model->uid = date('dmy') . sprintf('%05d', CounterData::find()->max('id') + 1);
        $model->uid_date = date(Yii::$app->params['dateFormat'], $model->uid_date ? strtotime($model->uid_date) : time());
        $model->status = CounterData::STATUS_NEW;
        $model->user_admin_id = Yii::$app->user->id;
        if (!$model->counter_data_last_id) {
            $model->isAutoSetLast = true;
        }
        
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            return ActiveForm::validate($model);
        }
        
        if ($model->load($post)) {
            if ($model->validate()) {
                $model->uid_date = $model->uid_date ? date('Y-m-d', strtotime($model->uid_date)) : null;
            }
            if ($model->save()) {
                if ($post['action_save_add']) {
                    return $this->redirect(['create', 'counter_data_id' => $model->id]);
                }
//                return $this->redirect(['index']);
                return $this->redirect(['counter-list', 'CounterDataSearch[flat_id]' => $model->flat_id, 'CounterDataSearch[service_id]' => $model->service_id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing CounterData model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $post = Yii::$app->request->post();

        $model->uid_date = date(Yii::$app->params['dateFormat'], $model->uid_date ? strtotime($model->uid_date) : time());
        if (!$model->counter_data_last_id) {
            $model->isAutoSetLast = true;
        }
            
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            return ActiveForm::validate($model);
        }
        
        if ($model->load($post)) {
            if ($model->validate()) {
                $model->uid_date = $model->uid_date ? date('Y-m-d', strtotime($model->uid_date)) : null;
            }
            if ($model->save()) {
                if ($post['action_save_add']) {
                    return $this->redirect(['create', 'counter_data_id' => $model->id]);
                }
//                return $this->redirect(['index']);
                return $this->redirect(['counter-list', 'CounterDataSearch[flat_id]' => $model->flat_id, 'CounterDataSearch[service_id]' => $model->service_id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing CounterData model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect([
            'counter-list', 
            'CounterDataSearch[flat_id]' => Yii::$app->request->get('CounterDataSearch')['flat_id'],
            'CounterDataSearch[service_id]' => Yii::$app->request->get('CounterDataSearch')['service_id'],
        ]);
    }
    
    /**
     * @param int $counter_data_id
     * @param float $amount_total
     * @param string $uid_date
     * @param int $service_id
     * @param int $flat_id
     * @return mixed
     */
    public function actionAjaxGetAmount($counter_data_id = null, $amount_total = 0, $uid_date = null, $service_id = null, $flat_id = null)
    {
        if ($counter_data_id && (!$amount_total || !$uid_date || !$service_id || !$flat_id)) {
            $counterData = CounterData::findOne($counter_data_id);
            $amount_total = $counterData->amount_total;
            $uid_date = $counterData->uid_date;
            $service_id = $counterData->service_id;
            $flat_id = $counterData->flat_id;
        }
        $amount = CounterData::getAmountByData($amount_total, $uid_date, $service_id, $flat_id);
        return [
            'amount' => $amount,
        ];
    }

    /**
     * Finds the CounterData model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CounterData the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CounterData::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
