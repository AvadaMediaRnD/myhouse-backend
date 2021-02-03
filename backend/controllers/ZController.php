<?php

namespace backend\controllers;

use common\models\Config;
use common\models\User;
use common\models\UserAdmin;
use common\models\MasterRequest;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * ZController override the default Controller.
 *
 * @inheritdoc
 */
class ZController extends \yii\web\Controller
{
    const FORBIDDEN_HTTP_MESSAGE = 'Доступ запрещен';
    
    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        Config::initConfig();


        if (Yii::$app->request->isAjax
            || Yii::$app->request->get('ajax')
            || Yii::$app->request->post('ajax')
        ) {
            Yii::$app->response->headers->set('Cache-Control', 'no-cache');
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        }


        if (Yii::$app->user->isGuest && Yii::$app->requestedRoute != 'site/login') {
            if ($token = Yii::$app->request->get('token')) {
                $userAdmin = UserAdmin::findOne(['auth_key' => $token]);
                Yii::$app->user->login($userAdmin);
            }
            if (Yii::$app->user->isGuest) {
                $this->redirect(['/site/login']);
                return false;
            }
        }

        if (!Yii::$app->user->isGuest) {
            $usersNewQuery = User::find()
                ->where(['status' => User::STATUS_NEW])
                ->joinWith('flats')
                ->orderBy(['id' => SORT_DESC]);
//            if (Yii::$app->user->identity->role != UserAdmin::ROLE_ADMIN) {
//                $usersNewQuery->andWhere(['in', 'flat.house_id', Yii::$app->user->identity->getHouseIds()]);
//            }
            $usersNew = $usersNewQuery->all();
            Config::setValue('usersNew', $usersNew);

            $masterRequestsNewQuery = MasterRequest::find()
                ->where(['status' => MasterRequest::STATUS_NEW])
                ->joinWith('flat')
                ->orderBy(['id' => SORT_DESC]);
            if (Yii::$app->user->identity->role != UserAdmin::ROLE_ADMIN) {
                $masterRequestsNewQuery->andWhere(['in', 'flat.house_id', Yii::$app->user->identity->getHouseIds()]);
            }
            $masterRequestsNew = $masterRequestsNewQuery->all();
            Config::setValue('masterRequestsNew', $masterRequestsNew);
        }

        return parent::beforeAction($action);
    }

    /**
     * @inheritdoc
     */
    public function goBack($defaultUrl = null)
    {
        if (!$defaultUrl) {
            $defaultUrl = !empty(Yii::$app->request->referrer) ? Yii::$app->request->referrer : null;
        }
        return parent::goBack($defaultUrl);
    }
    
    /**
     * @return array
     */
    public function getAllowedActions()
    {
        $beh = $this->getBehavior('access');
        if ($beh) {
            $rules = $beh->rules;
            foreach ($rules as $rule) {
                if (!$rule->roles) {
                    return $rule->actions;
                }
            }
        }
        return [];
    }

}