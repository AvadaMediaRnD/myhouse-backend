<?php

namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use common\models\validators\PhoneValidator;

/**
 * User model
 *
 * @property integer $id
 * @property string $uid
 * @property string $email
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 */
class User extends ZModel implements IdentityInterface {

    const STATUS_DELETED = 0;
    const STATUS_NEW = 5;
    const STATUS_ACTIVE = 10;

    public $password1;
    public $password2;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function fields() {
        $fields = [
            'id', 'uid', 'email', 'status', 'created_at', 'updated_at',
        ];
        return $fields;
    }

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            \yii\behaviors\TimestampBehavior::className(),
            [
                'class' => \common\components\ChangeLogBehavior::className(),
                'labelObject' => 'Владелец квартиры',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            ['uid', 'string', 'max' => 255],
            ['uid', 'unique', 'targetClass' => '\common\models\User', 'message' => 'Этот ID уже используется.'],
            ['status', 'default', 'value' => self::STATUS_NEW],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_NEW, self::STATUS_DELETED]],
            ['uid', 'unique', 'targetClass' => '\common\models\User', 'message' => 'Этот email уже используется.'],
            [['password1', 'password2'], 'string', 'max' => 255, 'min' => 6],
            ['password2', 'compare', 'compareAttribute' => 'password1', 'message' => Yii::t('model', 'Пароли не совпадают.')],
            [['uid'], 'filter', 'filter' => function($value) {
                    return strip_tags(trim($value));
                }],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('model', 'ID'),
            'uid' => Yii::t('model', 'ID 1C'),
            'email' => Yii::t('model', 'Email'),
            'status' => Yii::t('model', 'Статус'),
            'created_at' => Yii::t('model', 'Добавлен'),
            'updated_at' => Yii::t('model', 'Изменен'),
            'password1' => Yii::t('model', 'Пароль'),
            'password2' => Yii::t('model', 'Повтор пароля'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function delete() {
        $this->status = static::STATUS_DELETED;
        $this->save();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFlats() {
        return $this->hasMany(Flat::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMessageAddressesPersonal() {
        return $this->hasMany(MessageAddress::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMessagesPersonal() {
        return $this->hasMany(Message::className(), ['id' => 'message_id'])
                        ->via('messageAddressesPersonal');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMessageAddresses() {

        return $this->hasMany(MessageAddress::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMessages() {

        return $this->hasMany(Message::className(), ['id' => 'message_id'])
                        ->via('messageAddresses');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserMessageViews() {
        return $this->hasMany(UserMessageView::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfile() {
        return $this->hasOne(Profile::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserNote() {
        return $this->hasOne(UserNote::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccountTransaction() {
        return $this->hasMany(AccountTransaction::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserNoteDescription() {
        return $this->userNote ? $this->userNote->description : null;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id) {
        return static::find()->where([
                    'and',
                    ['id' => $id],
                    ['in', 'status', [self::STATUS_ACTIVE, self::STATUS_NEW]]
                ])->one();
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null) {
        return static::findOne(['auth_key' => $token]);
    }

    /**
     * Finds user by email or uid
     *
     * @param string $username
     * @return static|null|ActiveRecord
     */
    public static function findByUsername($username) {
        return static::find()->where([
                    'and',
                    ['or', ['email' => $username], ['uid' => $username]],
                    ['in', 'status', [self::STATUS_ACTIVE, self::STATUS_NEW]]
                ])->one();
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null|ActiveRecord
     */
    public static function findByPasswordResetToken($token) {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::find()->where([
                    'and',
                    ['password_reset_token' => $token],
                    ['in', 'status', [self::STATUS_ACTIVE, self::STATUS_NEW]]
                ])->one();
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token) {
        if (empty($token)) {
            return false;
        }
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        $parts = explode('_', $token);
        $timestamp = (int) end($parts);
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId() {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey() {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey) {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password) {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password) {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey() {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken() {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken() {
        $this->password_reset_token = null;
    }

    /**
     * generate password, only get string
     * @param int $length must divide by 2
     * @return string
     */
    public static function generatePassword($length = 8) {
        if ($length < 1) {
            $length = 1;
        }
        if ($length > 127) {
            $length = 127;
        }
        $alphabet = 'abcdefghijklmnopqrstuvwxyz1234567890';


        $pass = [];
        $alphaLength = strlen($alphabet) - 1;
        for ($i = 0; $i < $length; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass);
    }

    /**
     * @param boolean $defaultEmail if fullname is empty return email or null
     * @return string
     */
    public function getFullname($short = false, $defaultEmail = true) {
        return ($this->profile && $this->profile->getFullname($short)) ? $this->profile->getFullname($short) : ($defaultEmail ? $this->email : null);
    }

    /**
     * @return array
     */
    public static function getStatusOptions() {
        return [
            static::STATUS_ACTIVE => Yii::t('model', 'Активен'),
            static::STATUS_NEW => Yii::t('model', 'Новый'),
            static::STATUS_DELETED => Yii::t('model', 'Отключен'),
        ];
    }

    /**
     * @param null $status
     * @return mixed|null
     */
    public function getStatusLabel($status = null) {
        $status = $status == null ? $this->status : $status;
        $options = static::getStatusOptions();
        return isset($options[$status]) ? $options[$status] : null;
    }

    /**
     * @param null $status
     * @return mixed|null
     */
    public function getStatusLabelHtml($status = null) {
        $status = $status == null ? $this->status : $status;
        $statusLabel = $this->getStatusLabel($status);
        $itemClass = 'label-default';
        if ($status == static::STATUS_ACTIVE) {
            $itemClass = 'label-success';
        } elseif ($status == static::STATUS_NEW) {
            $itemClass = 'label-warning';
        } elseif ($status == static::STATUS_DELETED) {
            $itemClass = 'label-danger';
        }
        return '<small class="label ' . $itemClass . '">' . $statusLabel . '</small>';
    }

    /**
     * Invite user
     * @param string $password
     */
    public function sendInvite($password = '') {
        $linkPlay = Yii::$app->params['appUrlAndroid'];
        $linkStore = Yii::$app->params['appUrlIos'];

        if ($this->email) {
            $title = 'Приглашение в ' . Yii::$app->name;
            $message = 'Вас приглашают подключиться к системе ' . Yii::$app->name . '.'
                    . "\r\n" . 'Скачайте приложение:' .
                    ($linkPlay ? ("\r\n" . 'Play Market: <a href="' . $linkPlay . '">' . $linkPlay . '</a>') : '') .
                    ($linkStore ? ("\r\n" . 'App Store: <a href="' . $linkStore . '">' . $linkStore . '</a>') : '') .
                    "\r\n \r\n" . 'Ваш логин: ' . $this->uid . ' или ваш email адрес' .
                    "\r\n" . ($password ? ('Пароль: ' . $password) : 'Чтобы узнать пароль свяжитесь с администрацией.');

            // send email
            \Yii::$app->mailer->compose()
                    ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name . ' robot'])
                    ->setTo($this->email)
                    ->setSubject($title)
                    ->setTextBody(strip_tags($message))
                    ->setHtmlBody(nl2br($message))
                    ->send();

//            $header = "From: \"".Yii::$app->name."\" <".\Yii::$app->params['supportEmail'].">\n";
//            $header .= "Content-Type: text/html; charset=UTF-8";
//            mail(
//                $email,
//                "=?utf-8?B?".base64_encode($title)."?=",
//                nl2br($message),
//                $header);
        }
    }

}
