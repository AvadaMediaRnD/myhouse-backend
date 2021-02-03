<?php

namespace backend\controllers;

use backend\models\UserForm;
use common\models\Profile;
use Yii;
use common\models\User;
use common\models\UserAdmin;
use backend\models\UserSearch;
use backend\controllers\ZController;
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
        if (!Yii::$app->user->can(UserAdmin::PERMISSION_USER) && !in_array($action->id, $actions)) {
            // return $this->redirect(['/site/error']);
            throw new \yii\web\ForbiddenHttpException(Yii::t('app', static::FORBIDDEN_HTTP_MESSAGE));
        }
        return parent::beforeAction($action);
    }
    
    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
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
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new User();
        $post = Yii::$app->request->post();

        $modelForm = new UserForm();
        $modelForm->status = User::STATUS_NEW;
        if ($modelForm->load($post)) {
            if ($model = $modelForm->process()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'modelForm' => $modelForm,
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $post = Yii::$app->request->post();

        $modelForm = UserForm::loadFromModel($model);
        if ($modelForm->load($post)) {
            if ($model = $modelForm->process()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        $model = $this->findModel($id);

        return $this->render('update', [
            'modelForm' => $modelForm,
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionInvite()
    {
        $linkPlay = Yii::$app->params['appUrlAndroid'];
        $linkStore = Yii::$app->params['appUrlIos'];

        $post = Yii::$app->request->post();

        $phone = '';
        $email = '';

        if ($post) {
            $phone = $post['phone'];
            $email = $post['email'];

            if ($phone) {
                // send sms
                /*** SMS notification ***/

//                $phone = str_replace('+', '', $phone);

                $login = "avadav";
                $password = "smsavada";
                $phones = array($phone);
                //$phones = array("380957101283");
                $sender = "TEST-SMS";
                $message = 'Скачайте приложение ' . Yii::$app->name . ': ' . $linkPlay . ' или ' . $linkStore;

                // turbosms
                $tableName = 'avadav';
                $sign = 'Msg';
                /** @var $dbSms yii\db\Connection */
                $dbSms = Yii::$app->has('dbSms') ? Yii::$app->dbSms : null;

                $res = '';

                foreach($phones as $phonesms) {
//                    $res .= file_get_contents("http://api.smsfeedback.ru/messages/v2/send/?login=".$login."&password=".$password."&phone=".rawurlencode($phonesms)."&text=".rawurlencode($message)."&sender=".rawurlencode($sender));
//                    $res .= ' #'.$phonesms.'|';

//                    $dbSms->createCommand()->insert($tableName, [
//                        'number' => $phonesms,
//                        'sign' => $sign,
//                        'message' => $message,
//                    ])->execute();

                    $res .= " $phonesms | ";
                }

                file_put_contents(Yii::getAlias('@app').'/web/smslog.txt', $res . ' @@ ' . $message . "\n", FILE_APPEND);

                /*** End SMS notification ***/
            }
            if ($email) {
                // send email
                $title = 'Приглашение в ' . Yii::$app->name;
                $message = 'Вас приглашают подключиться к системе ' . Yii::$app->name . '.'
. "\r\n" . 'Скачайте приложение:' .
($linkPlay ? ("\r\n" . 'Play Market: <a href="'.$linkPlay.'">'.$linkPlay.'</a>') : '') .
($linkStore ? ("\r\n" . 'App Store: <a href="'.$linkStore.'">'.$linkStore.'</a>') : '') .
"\r\n \r\n" . 'Для дальнейшей информации свяжитесь с администрацией.';
            
                // send email
                \Yii::$app->mailer->compose()
                    ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name . ' robot'])
                    ->setTo($email)
                    ->setSubject($title)
                    ->setTextBody(strip_tags($message))
                    ->setHtmlBody(nl2br($message))
                    ->send();
            }
            
            return $this->refresh();
        }

        return $this->render('invite', [
            'phone' => $phone,
            'email' => $email,
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
