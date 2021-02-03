<?php

namespace api\modules\v1\controllers;

use api\modules\v1\models\SignupModel;
use Yii;
use api\modules\v1\controllers\ZController as Controller;
use yii\web\NotFoundHttpException;

class AuthController extends Controller {

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => \yii\filters\auth\CompositeAuth::className(),
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

    protected function verbs() {
        return [
            'login' => ['POST'],
        ];
    }

    public function actionIndex() {
        throw new NotFoundHttpException("Unsupported action request", 100);
    }

    /**
     * @api {post} /auth/login Login
     * @apiVersion 1.0.0
     * @apiName Login
     * @apiGroup Auth
     *
     * @apiDescription Логин пользователя, в header необходимо передать через basic авторизацию логин:пароль в base64
     *
     * @apiHeader {string} Authorization Логин и пароль в base64.
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic KzM4MDkzMTIzNDU2NzoxMTExMTE="
     *  }
     *
     * @apiSuccess {string} access_token Токен пользователя.
     *
     * @apiSuccessExample Success-Response:
     *  HTTP/1.1 200 OK
     *  {
     *      "code": 200,
     *      "status": "success",
     *      "message": "",
     *      "data": {
     *          "access_token": "EjniWdbzJob2O5mSn_qfidDGbUEIdjJC"
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
    public function actionLogin() {
        $user = Yii::$app->user->identity;
        $response = [
            'code' => static::CODE_SUCCESS,
            'status' => static::STATUS_SUCCESS,
            'message' => '',
            'data' => [
                'access_token' => $user->getAuthKey(),
            ],
        ];
        return $response;
    }



}
