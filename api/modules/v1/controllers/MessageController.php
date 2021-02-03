<?php

namespace api\modules\v1\controllers;

use common\models\House;
use common\models\Invoice;
use common\models\Message;
use common\models\User;
use Yii;
use api\modules\v1\controllers\ZController as Controller;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class MessageController extends Controller {

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => \yii\filters\auth\CompositeAuth::className(),
            'except' => [],
            'authMethods' => [
                [
                    'class' => \yii\filters\auth\HttpBasicAuth::className(),
                    'auth' => function ($username, $password) {
                        $user = \common\models\User::findByUsername($username);
                        if ($user !== null && $user->validatePassword($password)) {
                            return $user;
                        }
                        return null;
                    }
                ],
                \yii\filters\auth\HttpBearerAuth::className(),
            ],
        ];

        return $behaviors;
    }

    public function actionIndex() {
        throw new NotFoundHttpException("Unsuported action request", 100);
    }

    protected function verbs() {
        return [
            'set-user-view' => ['POST'],
        ];
    }

    /**
     * @api {get} /message/get?id=:id Get
     * @apiVersion 1.0.0
     * @apiName Get
     * @apiGroup Message
     *
     * @apiDescription Получить полную информацию по сообщению по id. Статус прочитанного передается в атрибуте "isUserView"
     * <br/> Статусы status:
     * <br/> 0 - неактивно
     * <br/> 5 - ожидает очереди
     * <br/> 10 - отправлено
     * <br/> Типы type:
     * <br/> default - общий
     * <br/> invoice - квитанция
     * <br/> house - дом
     * <br/> pay - оплата
     * <br/> push - пуш-уведомление
     *
     * @apiHeader {string} Authorization токен пользователя.
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Bearer kGjl32jEysdZpAtlliGuKH8OF5ESIb32"
     *  }
     *
     * @apiParam {integer} id Id квартиры.
     *
     * @apiSuccess {array} message Данные сообщения.
     *
     * @apiSuccessExample Success-Response:
     *  HTTP/1.1 200 OK
     *  {
     *      "code": 200,
     *      "status": "success",
     *      "message": "",
     *      "data": {
     *          "message": {
     *              "id": 7,
     *              "name": "Пришла платежка за Август",
     *              "description": "Оплатите квитанцию за Август полный текст",
     *              "type": "pay",
     *              "status": 10,
     *              "created_at": 1504201149,
     *              "updated_at": 1504201149,
     *              "invoice_id": 6,
     *              "isUserView": true
     *          }
     *      }
     *  }
     *
     * @apiErrorExample Error-Response:
     *  HTTP/1.1 403 Forbidden
     *  {
     *      "code": 403,
     *      "status": "error",
     *      "message": "Access not allowed or object does not exist.",
     *      "data": []
     *  }
     *
     * @apiErrorExample Error-Response:
     *  HTTP/1.1 400 Bad Request
     *  {
     *      "error": {
     *          "name": "Bad Request,
     *          "message": "Missing required parameters: id",
     *          "code": 0,
     *          "status": 400,
     *          "type": "yii\\web\\BadRequestHttpException"
     *      }
     *  }
     *
     * @apiErrorExample Error-Response:
     *  HTTP/1.1 400 Bad Request
     *  {
     *      "error": {
     *          "name": "Unauthorized",
     *          "message": "Your request was made with invalid credentials.",
     *          "code": 0,
     *          "status": 401,
     *          "type": "yii\\web\\UnauthorizedHttpException"
     *      }
     *  }
     *
     * @param $id
     * @return array|null
     */
    public function actionGet($id) {

        $message = Message::findOne($id);
        if (!$message || !$message->isRead) {
            Yii::$app->response->setStatusCode(static::RESPONSE_STATUS_ERROR_FORBIDDEN);
            $response = [
                'code' => static::RESPONSE_STATUS_ERROR_FORBIDDEN,
                'status' => static::CODE_ERROR_FORBIDDEN,
                'message' => static::ERROR_MESSAGE_FORBIDDEN,
                'data' => [],
            ];
        } else {
            $response = [
                'code' => static::CODE_SUCCESS,
                'status' => static::STATUS_SUCCESS,
                'message' => '',
                'data' => [],
            ];
            $response['data']['message'] = $message->toArray();
            $response['data']['message']['isUserView'] = $message->getIsUserView();
        }
        return $response;
    }

    /**
     * @api {get} /message/get-list?id=:id&offset=:offset&limit=:limit Get List
     * @apiVersion 1.0.0
     * @apiName Get List
     * @apiGroup Message
     *
     * @apiDescription Получить список сообщений пользователя. Статус прочитанного передается в атрибуте "isUserView". Для выгрузки порциями, использовать параметры offset + limit. 
     *
     * @apiHeader {string} Authorization токен пользователя.
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Bearer kGjl32jEysdZpAtlliGuKH8OF5ESIb32"
     *  }
     *
     * @apiParam {integer} [id] Id сообщения, чтобы вернуть все сообщения с id > указанного для данного запроса.
     * @apiParam {integer} [offset] Сколько сообщений пропустить при выборке. Для выгрузки порциями. По-умолчанию: 0.
     * @apiParam {integer} [limit] Сколько сообщений выгрузить. По-умолчанию: -1. Максимальное значение: 2000.
     *
     * @apiSuccess {array} messages Данные сообщений.
     *
     * @apiSuccessExample Success-Response:
     *  HTTP/1.1 200 OK
     *  {
     *      "code": 200,
     *      "status": "success",
     *      "message": "",
     *      "data": {
     *          "messages": [
     *              {
     *                  "id": 7,
     *                  "name": "Пришла платежка за Август",
     *                  "description": "Оплатите квитанцию за Август полный текст",
     *                  "type": "pay",
     *                  "status": 10,
     *                  "created_at": 1504201149,
     *                  "updated_at": 1504201149,
     *                  "invoice_id": 6,
     *                  "isUserView": true
     *              },
     *              {
     *                  "id": 8,
     *                  "name": "Пришла платежка за Август",
     *                  "description": "Оплатите квитанцию за Август полный текст",
     *                  "type": "pay",
     *                  "status": 10,
     *                  "created_at": 1504201149,
     *                  "updated_at": 1504201149,
     *                  "invoice_id": 8,
     *                  "isUserView": false
     *              }
     *          ]
     *      }
     *  }
     *
     * @apiErrorExample Error-Response:
     *  HTTP/1.1 403 Forbidden
     *  {
     *      "code": 403,
     *      "status": "error",
     *      "message": "Access not allowed or object does not exist.",
     *      "data": []
     *  }
     *
     * @apiErrorExample Error-Response:
     *  HTTP/1.1 400 Bad Request
     *  {
     *      "error": {
     *          "name": "Unauthorized",
     *          "message": "Your request was made with invalid credentials.",
     *          "code": 0,
     *          "status": 401,
     *          "type": "yii\\web\\UnauthorizedHttpException"
     *      }
     *  }
     *
     * @param null $id
     * @param integer $offset
     * @param integer $limit
     * @return array|null
     */
    public function actionGetList($id = null, $offset = 0, $limit = -1) {

        if ($limit > 2000) {
            $limit = 2000;
        }
        
        $user = Yii::$app->user->identity;
        $messagesQuery = Message::find()->offset($offset)->limit($limit)
            ->orderBy(['message.created_at' => SORT_DESC, 'message.id' => SORT_DESC]);
        $messagesQuery->joinWith('messageAddress');

        $flatIds = ArrayHelper::getColumn($user->flats, 'id');
        $houseIds = array_unique(ArrayHelper::getColumn($user->flats, 'house_id'));
        $sectionIds = array_unique(ArrayHelper::getColumn($user->flats, 'section_id'));
        $riserIds = array_unique(ArrayHelper::getColumn($user->flats, 'riser_id'));
        $floorIds = array_unique(ArrayHelper::getColumn($user->flats, 'floor_id'));
        $hasDebt = Invoice::find()
            ->where(['in', 'invoice.flat_id', $flatIds])
            ->andWhere(['invoice.status' => Invoice::STATUS_UNPAID])
            ->exists();
        
        $messagesQuery->where(['or', 
            ['message_address.user_id' => $user->id],
            ['and', ['in', 'message_address.house_id', $houseIds], ['is', 'message_address.section_id', null], ['is', 'message_address.riser_id', null], ['is', 'message_address.floor_id', null], ['is', 'message_address.flat_id', null], ['is', 'message_address.user_id', null]],
            ['and', ['is', 'message_address.house_id', null], ['in', 'message_address.section_id', $sectionIds], ['is', 'message_address.riser_id', null], ['is', 'message_address.floor_id', null], ['is', 'message_address.flat_id', null], ['is', 'message_address.user_id', null]],
            ['and', ['is', 'message_address.house_id', null], ['is', 'message_address.section_id', null], ['in', 'message_address.riser_id', $riserIds], ['is', 'message_address.floor_id', null], ['is', 'message_address.flat_id', null], ['is', 'message_address.user_id', null]],
            ['and', ['is', 'message_address.house_id', null], ['is', 'message_address.section_id', null], ['is', 'message_address.riser_id', null], ['in', 'message_address.floor_id', $floorIds], ['is', 'message_address.flat_id', null], ['is', 'message_address.user_id', null]],
            ['and', ['is', 'message_address.house_id', null], ['is', 'message_address.section_id', null], ['is', 'message_address.riser_id', null], ['is', 'message_address.floor_id', null], ['in', 'message_address.flat_id', $flatIds], ['is', 'message_address.user_id', null]],
        ]);
        if ($hasDebt) {
            $messagesQuery->orWhere(['and', ['message_address.user_has_debt' => 1], ['or', ['is', 'message_address.user_id', null], ['message_address.user_id' => $user->id]]]);
        }
        $messagesQuery->andWhere(['message.status' => Message::STATUS_SENT]);

        if ($id) {
            $messagesQuery->andWhere(['>', 'message.id', (int)$id]);
        }

        $response = [
            'code' => static::CODE_SUCCESS,
            'status' => static::STATUS_SUCCESS,
            'message' => '',
            'data' => [],
        ];

        $messages = $messagesQuery->all();
        if ($messages) {
            $response['data']['messages'] = [];
            foreach ($messages as $k => $message) {
                $response['data']['messages'][$k] = $message->toArray();
                $response['data']['messages'][$k]['isUserView'] = $message->getIsUserView();
            }
        }
        return $response;
    }

    /**
     * @api {post} /message/set-user-view Set Message View for User
     * @apiVersion 1.0.0
     * @apiName Set User View
     * @apiGroup Message
     *
     * @apiDescription Отметить сообщение как прочитанное.
     *
     * @apiHeader {string} Authorization токен пользователя.
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Bearer kGjl32jEysdZpAtlliGuKH8OF5ESIb32"
     *  }
     *
     * @apiParam {integer} id Id сообщения.
     *
     * @apiSuccessExample Success-Response:
     *  HTTP/1.1 200 OK
     *  {
     *      "code": 200,
     *      "status": "success",
     *      "message": "Message read.",
     *      "data": []
     *  }
     *
     * @apiErrorExample Error-Response:
     *  HTTP/1.1 403 Forbidden
     *  {
     *      "code": 403,
     *      "status": "error",
     *      "message": "Access not allowed or object does not exist.",
     *      "data": []
     *  }
     *
     * @return array|null
     * @throws \yii\base\InvalidConfigException
     */
    public function actionSetUserView() {
        $post = Yii::$app->request->post();
        $id = $post['id'];

        $message = Message::findOne($id);
        if (!$message || !$message->isRead) {
            Yii::$app->response->setStatusCode(static::RESPONSE_STATUS_ERROR_FORBIDDEN);
            $response = [
                'code' => static::RESPONSE_STATUS_ERROR_FORBIDDEN,
                'status' => static::CODE_ERROR_FORBIDDEN,
                'message' => static::ERROR_MESSAGE_FORBIDDEN,
                'data' => [],
            ];
        } else {
            $message->setIsUserView();
            $response = [
                'code' => static::CODE_SUCCESS,
                'status' => static::STATUS_SUCCESS,
                'message' => 'Message read.',
                'data' => [],
            ];
        }
        return $response;
    }

}
