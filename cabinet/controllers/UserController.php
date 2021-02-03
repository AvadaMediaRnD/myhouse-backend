<?php

namespace cabinet\controllers;

use cabinet\models\UserForm;
use common\models\Profile;
use Yii;
use common\models\User;
use cabinet\models\UserSearch;
use cabinet\controllers\ZController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends ZController
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
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->redirect(['view']);
    }

    /**
     * Displays a single User model.
     * @return mixed
     */
    public function actionView()
    {
        return $this->render('view', [
            'model' => Yii::$app->user->identity,
        ]);
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionUpdate()
    {
        $model = Yii::$app->user->identity;
        $post = Yii::$app->request->post();

        $modelForm = UserForm::loadFromModel($model);
        if ($modelForm->load($post)) {
            if ($model = $modelForm->process()) {
                return $this->redirect(['view']);
            }
        }

        $model = Yii::$app->user->identity;

        return $this->render('update', [
            'modelForm' => $modelForm,
            'model' => $model,
        ]);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
