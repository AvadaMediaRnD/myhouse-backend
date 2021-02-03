<?php

namespace backend\controllers;

use Yii;
use common\models\MasterRequest;
use common\models\UserAdmin;
use backend\models\MasterRequestSearch;
use backend\controllers\ZController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * MasterRequestController implements the CRUD actions for MasterRequest model.
 */
class MasterRequestController extends ZController
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
        if (!Yii::$app->user->can(UserAdmin::PERMISSION_MASTER_REQUEST) && !in_array($action->id, $actions)) {
            // return $this->redirect(['/site/error']);
            throw new \yii\web\ForbiddenHttpException(Yii::t('app', static::FORBIDDEN_HTTP_MESSAGE));
        }
        return parent::beforeAction($action);
    }

    /**
     * Lists all MasterRequest models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MasterRequestSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single MasterRequest model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $model->date_request = date(Yii::$app->params['dateFormat'], $model->date_request ? strtotime($model->date_request) : time());
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new MasterRequest model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new MasterRequest();
        
        $model->date_request = date(Yii::$app->params['dateFormat'], $model->date_request ? strtotime($model->date_request) : time());
        $model->type = MasterRequest::TYPE_DEFAULT;
        $model->status = MasterRequest::STATUS_NEW;
        
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                $model->date_request = $model->date_request ? date('Y-m-d', strtotime($model->date_request)) : null;
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
     * Updates an existing MasterRequest model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        
        $model->date_request = date(Yii::$app->params['dateFormat'], strtotime($model->date_request));
        if (!$model->type) {
            $model->type = MasterRequest::TYPE_DEFAULT;
        }
        
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                $model->date_request = date('Y-m-d', strtotime($model->date_request));
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
     * Deletes an existing MasterRequest model.
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
     * Finds the MasterRequest model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MasterRequest the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MasterRequest::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
