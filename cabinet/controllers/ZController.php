<?php

namespace cabinet\controllers;

use common\models\Config;
use common\models\User;
use common\models\UserAdmin;
use common\models\Message;
use common\models\Invoice;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * ZController override the default Controller.
 *
 * @inheritdoc
 */
class ZController extends \yii\web\Controller
{
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


        if (Yii::$app->requestedRoute != 'site/login') {
            if ($token = Yii::$app->request->get('token')) {
                $userAdmin = User::findOne(['auth_key' => $token]);
                Yii::$app->user->login($userAdmin);
            }
            if (Yii::$app->user->isGuest) {
                $this->redirect(['/site/login']);
                return false;
            }
        }
        
        return parent::beforeAction($action);
    }
    
    /**
     * @inheritdoc
     */
    public function render($view, $params = array()) {
        if (!Yii::$app->user->isGuest) {
            $query = Message::find();
            $user = Yii::$app->user->identity;
            $flatIds = ArrayHelper::getColumn($user->flats, 'id');
            $floorIds = ArrayHelper::getColumn($user->flats, 'floor_id');
            $sectionIds = ArrayHelper::getColumn($user->flats, 'section_id');
            $riserIds = ArrayHelper::getColumn($user->flats, 'riser_id');
            $houseIds = ArrayHelper::getColumn($user->flats, 'house_id');
            $hasDebt = Invoice::find()
                ->where(['in', 'invoice.flat_id', $flatIds])
                ->andWhere(['invoice.status' => Invoice::STATUS_UNPAID])
                ->exists();
            
            $query->joinWith(['messageAddresses', 'userMessageViews']);
            $query->where(['or', 
                ['message_address.user_id' => $user->id],
                ['and', ['in', 'message_address.house_id', $houseIds], ['is', 'message_address.section_id', null], ['is', 'message_address.riser_id', null], ['is', 'message_address.floor_id', null], ['is', 'message_address.flat_id', null], ['is', 'message_address.user_id', null]],
                ['and', ['is', 'message_address.house_id', null], ['in', 'message_address.section_id', $sectionIds], ['is', 'message_address.riser_id', null], ['is', 'message_address.floor_id', null], ['is', 'message_address.flat_id', null], ['is', 'message_address.user_id', null]],
                ['and', ['is', 'message_address.house_id', null], ['is', 'message_address.section_id', null], ['in', 'message_address.riser_id', $riserIds], ['is', 'message_address.floor_id', null], ['is', 'message_address.flat_id', null], ['is', 'message_address.user_id', null]],
                ['and', ['is', 'message_address.house_id', null], ['is', 'message_address.section_id', null], ['is', 'message_address.riser_id', null], ['in', 'message_address.floor_id', $floorIds], ['is', 'message_address.flat_id', null], ['is', 'message_address.user_id', null]],
                ['and', ['is', 'message_address.house_id', null], ['is', 'message_address.section_id', null], ['is', 'message_address.riser_id', null], ['is', 'message_address.floor_id', null], ['in', 'message_address.flat_id', $flatIds], ['is', 'message_address.user_id', null]],
            ]);
            if ($hasDebt) {
                $query->orWhere(['and', ['message_address.user_has_debt' => 1], ['or', ['is', 'message_address.user_id', null], ['message_address.user_id' => $user->id]]]);
            }
            $query->andWhere(['message.status' => Message::STATUS_SENT]);
            $query->andWhere(['is', 'user_message_view.user_id', null]);
            $query->orderBy(['message.created_at' => SORT_DESC, 'message.id' => SORT_DESC]);
            $messagesNew = $query->all();
            Config::setValue('messagesNew', $messagesNew);
        }
        
        return parent::render($view, $params);
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

}