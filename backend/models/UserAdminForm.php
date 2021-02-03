<?php
namespace backend\models;

use common\models\UserAdmin;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

/**
 * UserAdmin form
 */
class UserAdminForm extends Model
{
    public $id;
    public $firstname;
    public $lastname;
    public $middlename;
    public $username;
    public $phone;
    public $role;
    public $status;
    public $password;
    public $password2;
    
    public $image;

    private $_model = null;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['firstname', 'lastname', 'middlename', 'username', 'phone', 'role'], 'string', 'max' => 255],
            [['password', 'password2'], 'string', 'min' => 6, 'max' => 255],
            ['username', 'email'],
            ['username', 'required'],
            [
                'username',
                'unique',
                'targetClass' => '\common\models\UserAdmin',
                'message' => 'Этот логин уже используется.',
                'when' => function ($model) {
                    $user = UserAdmin::findByUsername($model->username);
                    return $user && $user->id != $this->id;
                }
            ],
            ['password', 'required', 'when' => function ($model) {
                $user = UserAdmin::findByUsername($model->username);
                return !$user;
            }, 'whenClient' => "function (attribute, value) {
                return $('#is_new_record').val() == '1';
            }", 'skipOnEmpty' => true],
            ['password2', 'compare', 'compareAttribute' => 'password', 'message'=> Yii::t('model', 'Пароли не совпадают.')],
            ['status', 'default', 'value' => UserAdmin::STATUS_NEW],
            ['status', 'in', 'range' => [UserAdmin::STATUS_ACTIVE, UserAdmin::STATUS_NEW, UserAdmin::STATUS_DELETED]],
            ['image', 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'username' => Yii::t('model', 'Email (логин)'),
            'firstname' => Yii::t('model', 'Имя'),
            'lastname' => Yii::t('model', 'Фамилия'),
            'middlename' => Yii::t('model', 'Отчество'),
            'phone' => Yii::t('model', 'Телефон'),
            'role' => Yii::t('model', 'Роль'),
            'status' => Yii::t('model', 'Статус'),
            'password' => Yii::t('model', 'Пароль'),
            'password2' => Yii::t('model', 'Повторить пароль'),
            'image' => Yii::t('model', 'Сменить изображение'),
        ];
    }

    /**
     * Save user.
     *
     * @return UserAdmin|null the saved model or null if saving fails
     */
    public function process()
    {
        if ($this->validate()) {
            $user = UserAdmin::find()->where(['username' => $this->_model->username])->one();
            if (!$user) {
                $user = new UserAdmin();
            }
            $user->username = $this->username;
            if ($this->password) {
                $user->setPassword($this->password);

                // send sms
//                $phonesms = $user->username;
//                $message = 'Новый пароль: ' . $this->password;
//                $tableName = 'avadav';
//                $sign = 'Msg';
//                $dbSms = Yii::$app->dbSms;
//                if (!YII_DEBUG) {
//                    $dbSms->createCommand()->insert($tableName, [
//                        'number' => $phonesms,
//                        'sign' => $sign,
//                        'message' => $message,
//                    ])->execute();
//                }
//                $res = " $phonesms ";
//                file_put_contents(Yii::getAlias('@app').'/web/smslog.txt', $res . ' @@ ' . $message . "\n", FILE_APPEND);
            }
            if ($user->isNewRecord || $this->password) {
                $user->generateAuthKey();
            }
            $user->firstname = $this->firstname;
            $user->lastname = $this->lastname;
            $user->middlename = $this->middlename;
            $user->phone = $this->phone;
            $user->status = $this->status;
            $user->role = $this->role;

            if ($user->save()) {
                // image
                /*$file = UploadedFile::getInstance($this, 'image');
                if ($file) {
                    $path = '/upload/UserAdmin/' . $user->id . '/avatar.' . $file->extension; 
                    $pathFull = Yii::getAlias('@frontend/web' . $path);
                    $dir = dirname($pathFull);
                    if (!is_dir($dir)) {
                        mkdir($dir, 0777, true);
                    }
                    if ($file->saveAs($pathFull)) {
                        $user->image = $path;
                        $user->save(false);
                        Yii::$app->glide->getServer()->deleteCache($path);
                    }
                }*/
                
                if ($this->password) {
                    $this->sendNewPassword($user);
                }
                
                $auth = Yii::$app->authManager;
                $auth->revokeAll($user->id);
                if ($user->role == UserAdmin::ROLE_ELECTRICIAN) {
                    $auth->assign($auth->getRole(UserAdmin::ROLE_ELECTRICIAN), $user->id);
                } elseif ($user->role == UserAdmin::ROLE_PLUMBER) {
                    $auth->assign($auth->getRole(UserAdmin::ROLE_PLUMBER), $user->id);
                } elseif ($user->role == UserAdmin::ROLE_ACCOUNTANT) {
                    $auth->assign($auth->getRole(UserAdmin::ROLE_ACCOUNTANT), $user->id);
                } elseif ($user->role == UserAdmin::ROLE_MANAGER) {
                    $auth->assign($auth->getRole(UserAdmin::ROLE_MANAGER), $user->id);
                } elseif ($user->role == UserAdmin::ROLE_ADMIN) {
                    $auth->assign($auth->getRole(UserAdmin::ROLE_ADMIN), $user->id);
                }
                
                $this->_model = $user;
                
                return $user;
            }
        }

        return null;
    }

    /**
     * @param UserAdmin $model
     * @return UserAdminForm
     */
    public static function loadFromModel($model) {
        $form = new static();
        $form->_model = $model;
        if ($model) {
            $form->id = $model->id;
            $form->username = $model->username;
            $form->firstname = $model->firstname;
            $form->lastname = $model->lastname;
            $form->middlename = $model->middlename;
            $form->phone = $model->phone;
            $form->role = $model->role;
            $form->status = $model->status;
        }
        return $form;
    }
    
    /**
     * 
     * @return type
     */
    public function getAvatar()
    {
        if (!$this->_model) {
            return Yii::getAlias('/upload/placeholder.jpg');
        }
        return $this->_model->getAvatar();
    }
    
    /**
     * 
     * @param UserAdmin $user
     */
    private function sendNewPassword($user)
    {
        $urlCabinet = Yii::$app->urlManager->createAbsoluteUrl(['/']);
        $email = $user->email;
        $title = 'Пароль для входа';
        $message = 'Ваш пароль для входа в кабинет "' . Yii::$app->name . '":'
            . "\r\n" . $this->password
            . "\r\n \r\n" . 'Ссылка для входа: <a href="' . $urlCabinet . '">' . $urlCabinet . '</a>';
        
        \Yii::$app->mailer->compose()
            ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name . ' robot'])
            ->setTo($email)
            ->setSubject($title)
            ->setTextBody(strip_tags($message))
            ->setHtmlBody(nl2br($message))
            ->send();
    }
}
