<?php

namespace cabinet\controllers;

use common\models\Flat;
use Yii;
use cabinet\controllers\ZController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * TariffController implements the CRUD actions for User model.
 */
class TariffController extends ZController
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
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex($flat_id = null)
    {
        $user = Yii::$app->user->identity;
        if (!$flat_id) {
            if ($user->getFlats()->exists()) { 
                $flat_id = Yii::$app->user->identity->flats[0]->id;
                return $this->redirect(['index', 'flat_id' => $flat_id]);
            } else {
                return $this->redirect(['/user/view']);
            }
        }
        
        $flat = Flat::findOne($flat_id);
        if ($flat->user_id != $user->id) {
            return $this->redirect(['/site/error']);
        }
        
        $model = $flat->tariff;
        
        return $this->render('index', [
            'model' => $model,
            'flat' => $flat,
        ]);
    }
}
