<?php

namespace api\modules\v1\controllers;

use common\models\Config;
use common\models\User;
use Yii;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\helpers\ArrayHelper;
use yii\rest\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UnauthorizedHttpException;

class ZController extends Controller {

    // http codes
    const RESPONSE_STATUS_SUCCESS = 200;
    const RESPONSE_STATUS_ERROR_DEFAULT = 400;
    const RESPONSE_STATUS_ERROR_UNAUTHORIZED = 401;
    const RESPONSE_STATUS_ERROR_FORBIDDEN = 403;
    const RESPONSE_STATUS_ERROR_NOT_FOUND = 404;
    const RESPONSE_STATUS_ERROR_VALIDATION = 440;

    // custom error codes. Any coincidences are occasional
    const CODE_SUCCESS = 200;
    const CODE_ERROR_DEFAULT = 400;
    const CODE_ERROR_UNAUTHORIZED = 401;
    const CODE_ERROR_FORBIDDEN = 403;
    const CODE_ERROR_VALIDATION = 440;

    // some general error messages
    const ERROR_MESSAGE_INVALID_VALIDATION = 'Validation failed';
    const ERROR_MESSAGE_FORBIDDEN = 'Access not allowed or object does not exist.';

    const STATUS_ERROR = 'error';
    const STATUS_SUCCESS = 'success';

    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {

            Config::initConfig();

            file_put_contents(Yii::getAlias('@app/web/apilog.txt'), "\n----------------\n".date('Y-m-d H:i:s')."\nIP:".Yii::$app->request->getUserIP()."\n", FILE_APPEND);
            file_put_contents(Yii::getAlias('@app/web/apilog.txt'), Yii::$app->request->absoluteUrl."\n", FILE_APPEND);
            file_put_contents(Yii::getAlias('@app/web/apilog.txt'), print_r(Yii::$app->request->get(), true)."\n", FILE_APPEND);
            file_put_contents(Yii::getAlias('@app/web/apilog.txt'), print_r(Yii::$app->request->post(), true)."\n", FILE_APPEND);
            file_put_contents(Yii::getAlias('@app/web/apilog.txt'), print_r(Yii::$app->request->getHeaders()->toArray(), true)."\n", FILE_APPEND);
        }

        return true;
    }

}
