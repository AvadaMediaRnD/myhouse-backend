<?php

namespace backend\controllers;

use common\models\MessageAddress;
use Yii;
use common\models\Message;
use common\models\UserAdmin;
use backend\models\MessageSearch;
use backend\controllers\ZController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\Html;

/**
 * MessageController implements the CRUD actions for Message model.
 */
class MessageController extends ZController
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
        if (!Yii::$app->user->can(UserAdmin::PERMISSION_MESSAGE) && !in_array($action->id, $actions)) {
            // return $this->redirect(['/site/error']);
            throw new \yii\web\ForbiddenHttpException(Yii::t('app', static::FORBIDDEN_HTTP_MESSAGE));
        }
        return parent::beforeAction($action);
    }

    /**
     * Lists all Message models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MessageSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Message model.
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
     * Creates a new Message model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Message();
        $model->user_admin_from_id = Yii::$app->user->id;
        $model->status = Message::STATUS_WAITING;
        $model->type = Message::TYPE_DEFAULT;
        
        $model->description = Html::decode($model->description);

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                $model->description = Html::encode($model->description);
            }
            if ($model->save()) {
                $addressModel = new MessageAddress();
                $addressModel->load(Yii::$app->request->post());
                $addressModel->message_id = $model->id;
                $addressModel->save();
                return $this->redirect(['index']);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Message model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        
        $model->description = Html::decode($model->description);

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                $model->description = Html::encode($model->description);
            }
            if ($model->save()) {
                $addressModel = $model->messageAddress ? $model->messageAddress : new MessageAddress();
                $addressModel->load(Yii::$app->request->post());
                if ($addressModel->isNewRecord) {
                    $addressModel->message_id = $model->id;
                }
                $addressModel->save();
            }
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Message model.
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
     * Ajax delete many models by ids
     * 
     * @return mixed
     */
    public function actionAjaxDeleteMany()
    {
        $ids = Yii::$app->request->post('ids');
        
        Message::deleteAll(['in', 'id', $ids]);
        
        // Yii::$app->session->addFlash('success', 'Данные удалены');
        
        return [
            'ids' => $ids,
            'status' => 'success',
            'message' => 'Данные удалены',
        ];
    }

    /**
     * Finds the Message model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Message the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Message::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
