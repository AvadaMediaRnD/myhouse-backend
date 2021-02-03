<?php

namespace api\modules\v1\controllers;

use api\modules\v1\models\ProfileUpdateModel;
use api\modules\v1\models\UserPasswordUpdateModel;
use api\modules\v1\models\UserUpdateModel;
use common\models\Flat;
use common\models\Profile;
use common\models\User;
use Yii;
use api\modules\v1\controllers\ZController as Controller;
use yii\web\NotFoundHttpException;

class UserController extends Controller {

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

    /**
     * @api {get} /user/get Get
     * @apiVersion 1.0.0
     * @apiName Get
     * @apiGroup User
     *
     * @apiDescription Получение информации о пользователе, по токену.
     * <br/> Статусы status:
     * <br/> 0 - удален
     * <br/> 5 - новый
     * <br/> 10 - активирован
     *
     * @apiHeader {string} Authorization токен пользователя.
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Bearer EjniWdbzJob2O5mSn_qfidDGbUEIdjJC"
     *  }
     *
     * @apiSuccess {array} user Данные пользователя
     *
     * @apiSuccessExample Success-Response:
     *  HTTP/1.1 200 OK
     *  {
     *      "code": 200,
     *      "status": "success",
     *      "message": "",
     *      "data": {
     *          "user": {
     *              "id": 1,
     *              "uid": "0002",
     *              "email": "user2@user.com",
     *              "status": 10,
     *              "created_at": 1504201149,
     *              "updated_at": 1504201149,
     *              "profile": {
     *                  "id": 1,
     *                  "firstname": "Александр",
     *                  "lastname": "Пушкин",
     *                  "middlename": "Сергеевич",
     *                  "birthdate": "1990-06-23",
     *                  "phone": "+380932222222",
     *                  "viber": "vib123456",
     *                  "telegram": "teleg123456",
     *                  "image": "/upload/User/2/avatar.png",
     *                  "user_id": 1
     *              }
     *          }
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
     * @return array|null
     */
    public function actionGet() {

        $user = Yii::$app->user->identity;
        $response = [
            'code' => static::CODE_SUCCESS,
            'status' => static::STATUS_SUCCESS,
            'message' => '',
            'data' => [
                'user' => $user->toArray(),
            ],
        ];
        $response['data']['user']['profile'] = $user->profile;

        return $response;
    }

    /**
     * @api {get} /user/get-flats Get Flats
     * @apiVersion 1.0.0
     * @apiName Get Flats
     * @apiGroup User
     *
     * @apiDescription Получить список квартир пользователя, по токену.
     *
     * @apiHeader {string} Authorization токен пользователя.
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Bearer kGjl32jEysdZpAtlliGuKH8OF5ESIb32"
     *  }
     *
     * @apiSuccess {array} flats Список квартир.
     *
     * @apiSuccessExample Success-Response:
     *  HTTP/1.1 200 OK
     *  {
     *      "code": 200,
     *      "status": "success",
     *      "message": "",
     *      "data": {
     *          "flats": [
     *              {
     *                  "id": 2,
     *                  "flat": "2",
     *                  "created_at": 1504201149,
     *                  "updated_at": 1504201149,
     *                  "house_id": 1,
     *                  "user_id": 3,
     *                  "section_id": 1,
     *                  "riser_id": 1,
     *                  "floor_id": 1,
     *                  "debt": 3900,
     *                  "balance": 2450,
     *                  "house": {
     *                      "id": 1,
     *                      "name": "Жемчужина 1",
     *                      "address": "ул. Пушкина, 4",
     *                      "image1": "/upload/House/1/image1.jpg",
     *                      "image2": "/upload/House/1/image2.jpg",
     *                      "image3": "/upload/House/1/image3.jpg",
     *                      "image4": "/upload/House/1/image4.jpg",
     *                      "image5": "/upload/House/1/image5.jpg",
     *                      "created_at": 1504201149,
     *                      "updated_at": 1504201149
     *                  },
     *                  "section": {
     *                      "id": 1,
     *                      "name": "Секция 1"
     *                  },
     *                  "riser": {
     *                      "id": 1,
     *                      "name": "Cтояк 1"
     *                  },
     *                  "floor": {
     *                      "id": 1,
     *                      "name": "Этаж 1"
     *                  }
     *              },
     *              {
     *                  "id": 10,
     *                  "flat": "104",
     *                  "created_at": 1504201149,
     *                  "updated_at": 1504201149,
     *                  "balance": 0,
     *                  "debt": 0,
     *                  "house": {
     *                      "id": 2,
     *                      "name": "Жемчужина 2",
     *                      "address": "ул. Зеленая, 12",
     *                      "image1": "/upload/House/2/image1.jpg",
     *                      "image2": "/upload/House/2/image2.jpg",
     *                      "image3": "/upload/House/2/image3.jpg",
     *                      "image4": "/upload/House/2/image4.jpg",
     *                      "image5": "/upload/House/2/image5.jpg",
     *                      "created_at": 1504201149,
     *                      "updated_at": 1504201149
     *                  },
     *                  "section": {
     *                      "id": 4,
     *                      "name": "Секция 1"
     *                  },
     *                  "riser": {
     *                      "id": 4,
     *                      "name": "Cтояк 1"
     *                  },
     *                  "floor": {
     *                      "id": 16,
     *                      "name": "Этаж 6"
     *                  }
     *              }
     *          ]
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
     * @return array|null
     */
    public function actionGetFlats() {
        $response = [
            'code' => static::CODE_SUCCESS,
            'status' => static::STATUS_SUCCESS,
            'message' => '',
            'data' => [],
        ];
        $user = Yii::$app->user->identity;
        $flatsQuery = $user->getFlats();
        $flats = $flatsQuery->all();
        if ($flats) {
            $response['data']['flats'] = [];
            foreach ($flats as $k => $flat) {
                $response['data']['flats'][$k] = $flat->toArray();
                $response['data']['flats'][$k]['balance'] = $flat->account ? $flat->account->getBalance() : 0;
                $response['data']['flats'][$k]['house'] = $flat->house->toArray();
                $response['data']['flats'][$k]['section'] = $flat->section;
                $response['data']['flats'][$k]['riser'] = $flat->riser;
                $response['data']['flats'][$k]['floor'] = $flat->floor;
                $response['data']['flats'][$k]['account'] = $flat->account;
            }
        }

        return $response;
    }

}
