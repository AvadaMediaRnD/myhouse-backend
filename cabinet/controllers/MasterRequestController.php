<?php

namespace cabinet\controllers;

use Yii;
use common\models\MasterRequest;
use cabinet\models\MasterRequestSearch;
use cabinet\controllers\ZController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use cabinet\models\MasterRequestForm;
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
     * Lists all MasterRequest models.
     * @return mixed
     */
    public function actionIndex()
    {
        $user = Yii::$app->user->identity;
        if (!$user->getFlats()->exists()) {
            return $this->redirect(['/user/view']);
        }
        
        $searchModel = new MasterRequestSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
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
        $post = Yii::$app->request->post();

        $modelForm = new MasterRequestForm();
        $modelForm->status = MasterRequest::STATUS_NEW;
        $modelForm->date_request = date(Yii::$app->params['dateFormat'], time());
        $modelForm->time_request = date(Yii::$app->params['timeFormat'], time());
        $modelForm->type = MasterRequest::TYPE_DEFAULT;
        if ($modelForm->load($post)) {
            if ($model = $modelForm->process()) {
                return $this->redirect(['index']);
            }
        }

        return $this->render('create', [
            'modelForm' => $modelForm,
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
