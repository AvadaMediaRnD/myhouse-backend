<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\helpers\ArrayHelper;

/**
 * UserAdmin model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $firstname
 * @property string $lastname
 * @property string $middlename
 * @property string $phone
 * @property string $auth_key
 * @property string $role
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 * 
 * @property HouseUserAdmin[] $houseUserAdmins
 * @property House[] $houses
 * @property array $houseIds
 */
class UserAdmin extends ZModel implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_NEW = 5;
    const STATUS_ACTIVE = 10;
    const ROLE_UNDEF = 'undef';
    const ROLE_ADMIN = 'admin';
    const ROLE_MANAGER = 'manager';
    const ROLE_ACCOUNTANT = 'accountant';
    const ROLE_PLUMBER = 'plumber';
    const ROLE_ELECTRICIAN = 'electrician';
    
    const PERMISSION_SITE = 'site';
    const PERMISSION_SITE_VIEW = 'siteView';
    const PERMISSION_ACCOUNT = 'account';
    const PERMISSION_ACCOUNT_VIEW = 'accountView';
    const PERMISSION_ACCOUNT_UPDATE = 'accountUpdate';
    const PERMISSION_ACCOUNT_DELETE = 'accountDelete';
    const PERMISSION_ACCOUNT_TRANSACTION = 'accountTransaction';
    const PERMISSION_ACCOUNT_TRANSACTION_VIEW = 'accountTransactionView';
    const PERMISSION_ACCOUNT_TRANSACTION_UPDATE = 'accountTransactionUpdate';
    const PERMISSION_ACCOUNT_TRANSACTION_DELETE = 'accountTransactionDelete';
    const PERMISSION_USER = 'user';
    const PERMISSION_USER_VIEW = 'userView';
    const PERMISSION_USER_UPDATE = 'userUpdate';
    const PERMISSION_USER_DELETE = 'userDelete';
    const PERMISSION_HOUSE = 'house';
    const PERMISSION_HOUSE_VIEW = 'houseView';
    const PERMISSION_HOUSE_UPDATE = 'houseUpdate';
    const PERMISSION_HOUSE_DELETE = 'houseDelete';
    const PERMISSION_FLAT = 'flat';
    const PERMISSION_FLAT_VIEW = 'flatView';
    const PERMISSION_FLAT_UPDATE = 'flatUpdate';
    const PERMISSION_FLAT_DELETE = 'flatDelete';
    const PERMISSION_MESSAGE = 'message';
    const PERMISSION_MESSAGE_VIEW = 'messageView';
    const PERMISSION_MESSAGE_UPDATE = 'messageUpdate';
    const PERMISSION_MESSAGE_DELETE = 'messageDelete';
    const PERMISSION_MASTER_REQUEST = 'masterRequest';
    const PERMISSION_MASTER_REQUEST_VIEW = 'masterRequestView';
    const PERMISSION_MASTER_REQUEST_UPDATE = 'masterRequestUpdate';
    const PERMISSION_MASTER_REQUEST_DELETE = 'masterRequestDelete';
    const PERMISSION_INVOICE = 'invoice';
    const PERMISSION_INVOICE_VIEW = 'invoiceView';
    const PERMISSION_INVOICE_UPDATE = 'invoiceUpdate';
    const PERMISSION_INVOICE_DELETE = 'invoiceDelete';
    const PERMISSION_COUNTER_DATA = 'counterData';
    const PERMISSION_COUNTER_DATA_VIEW = 'counterDataView';
    const PERMISSION_COUNTER_DATA_UPDATE = 'counterDataUpdate';
    const PERMISSION_COUNTER_DATA_DELETE = 'counterDataDelete';
    const PERMISSION_WEBSITE = 'website';
    const PERMISSION_WEBSITE_UPDATE = 'websiteUpdate';
    const PERMISSION_SYSTEM = 'system';
    const PERMISSION_SERVICE = 'service';
    const PERMISSION_TARIFF = 'tariff';
    const PERMISSION_ROLE = 'role';
    const PERMISSION_USER_ADMIN = 'userAdmin';
    const PERMISSION_PAY_COMPANY = 'payCompany';
    const PERMISSION_SERVICE_VIEW = 'serviceView';
    const PERMISSION_SERVICE_UPDATE = 'serviceUpdate';
    const PERMISSION_SERVICE_DELETE = 'serviceDelete';
    const PERMISSION_TARIFF_VIEW = 'tariffView';
    const PERMISSION_TARIFF_UPDATE = 'tariffUpdate';
    const PERMISSION_TARIFF_DELETE = 'tariffDelete';
    const PERMISSION_ROLE_UPDATE = 'roleUpdate';
    const PERMISSION_USER_ADMIN_VIEW = 'userAdminView';
    const PERMISSION_USER_ADMIN_UPDATE = 'userAdminUpdate';
    const PERMISSION_USER_ADMIN_DELETE = 'userAdminDelete';
    const PERMISSION_PAY_COMPANY_UPDATE = 'payCompanyUpdate';

    public $password_old;
    public $password1;
    public $password2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_admin}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            \yii\behaviors\TimestampBehavior::className(),
            [
                'class' => \common\components\ChangeLogBehavior::className(),
                'labelObject' => 'Пользователь',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_NEW],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_NEW, self::STATUS_DELETED]],
            [['username', 'firstname', 'lastname', 'middlename', 'email', 'phone'], 'string', 'max' => 255],
            ['username', 'filter', 'filter' => 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => '\common\models\UserAdmin', 'message' => 'Этот логин уже используется.'],
            ['username', 'string', 'min' => 2, 'max' => 255],
            [['password1', 'password2', 'password_old'], 'string', 'max' => 255, 'min' => 6],
            ['password2', 'compare', 'compareAttribute' => 'password1', 'message'=> Yii::t('model', 'Пароли не совпадают.')],
            ['password_old', function ($attribute, $params) {
                if (!$this->validatePassword($this->password_old)) {
                    $this->addError($attribute, Yii::t('model', 'Текущий пароль неверный.'));
                }
            }],
            ['role', 'string', 'max' => 255],
            ['role', 'default', 'value' => static::ROLE_UNDEF],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('model', 'ID'),
            'username' => Yii::t('model', 'Логин'),
            'email' => Yii::t('model', 'Email'),
            'firstname' => Yii::t('model', 'Имя'),
            'lastname' => Yii::t('model', 'Фамилия'),
            'middlename' => Yii::t('model', 'Отчество'),
            'phone' => Yii::t('model', 'Телефон'),
            'password' => Yii::t('model', 'Пароль'),
            'password1' => Yii::t('model', 'Пароль'),
            'password2' => Yii::t('model', 'Повтор пароля'),
            'password_old' => Yii::t('model', 'Пароль текущий'),
            'status' => Yii::t('model', 'Статус'),
            'created_at' => Yii::t('model', 'Добавлен'),
            'updated_at' => Yii::t('model', 'Изменен'),
            'role' => Yii::t('model', 'Роль'),
        ];
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        $this->email = $this->username;
        if ($this->password1) {
            $this->setPassword($this->password1);
            $this->generateAuthKey();
        }
        return parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     */
    public function delete()
    {
        $this->status = static::STATUS_DELETED;
        $this->save();

//        return parent::delete();
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::find()->where([
            'and',
            ['id' => $id],
            ['in', 'status', [self::STATUS_ACTIVE, self::STATUS_NEW]]
        ])->one();
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::find()->where(['auth_key' => $token])->one();
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null|ActiveRecord
     */
    public static function findByUsername($username)
    {
        return static::find()->where([
            'and',
            ['username' => $username],
            ['in', 'status', [self::STATUS_ACTIVE, self::STATUS_NEW]]
        ])->one();
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null|ActiveRecord
     */
    public static function findByPasswordResetToken($token)
    {
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
    public static function isPasswordResetTokenValid($token)
    {
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
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHouseUserAdmins()
    {
        return $this->hasMany(HouseUserAdmin::className(), ['user_admin_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHouses()
    {
        return $this->hasMany(House::className(), ['id' => 'house_id'])
            ->via('houseUserAdmins');
    }
    
    /**
     * Get ids of houses
     * @return array
     */
    public function getHouseIds($byRole = true)
    {
        if ($byRole && $this->role == static::ROLE_ADMIN) {
            return ArrayHelper::getColumn(House::find()->all(), 'id');
        }
        return ArrayHelper::getColumn($this->getHouses()->all(), 'id');
    }

    /**
     * @param boolean $defaultUsername if fullname is empty return username or null
     * @return string
     */
    public function getFullname($defaultUsername = true)
    {
        $nameParts = [];
        if ($this->firstname) {
            $nameParts[] = $this->firstname;
        }
        if ($this->middlename) {
            $nameParts[] = $this->middlename;
        }
        if ($this->lastname) {
            $nameParts[] = $this->lastname;
        }
        return $nameParts ? implode(' ', $nameParts) : ($defaultUsername ? $this->username : null);
    }

    /**
     * @return array
     */
    public static function getStatusOptions()
    {
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
    public function getStatusLabel($status = null)
    {
        $status = $status == null ? $this->status : $status;
        $options = static::getStatusOptions();
        return isset($options[$status]) ? $options[$status] : null;
    }
    
    /**
     * @param null $status
     * @return mixed|null
     */
    public function getStatusLabelHtml($status = null)
    {
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
        return '<small class="label '.$itemClass.'">'.$statusLabel.'</small>';
    }
    
    /**
     * @return array
     */
    public static function getRoleOptions()
    {
        return [
            static::ROLE_ADMIN => Yii::t('model', 'Директор'),
            static::ROLE_MANAGER => Yii::t('model', 'Управляющий'),
            static::ROLE_ACCOUNTANT => Yii::t('model', 'Бухгалтер'),
            static::ROLE_ELECTRICIAN => Yii::t('model', 'Электрик'),
            static::ROLE_PLUMBER => Yii::t('model', 'Сантехник'),
            // static::ROLE_UNDEF => Yii::t('model', 'Неопределен'),
        ];
    }

    /**
     * @param null $role
     * @return mixed|null
     */
    public function getRoleLabel($role = null)
    {
        $role = $role == null ? $this->role : $role;
        $options = static::getRoleOptions();
        return isset($options[$role]) ? $options[$role] : null;
    }
    
    /**
     * get master options for master request
     * @return array
     */
    public static function getUserMasterOptions()
    {
        $userMasterQuery = static::find()
            //->where(['in', 'role', [UserAdmin::ROLE_ELECTRICIAN, UserAdmin::ROLE_PLUMBER]])
            ->orderBy(new \yii\db\Expression('IF(`role`="'.static::ROLE_ELECTRICIAN.'" OR `role`="'.static::ROLE_PLUMBER.'", 1, 0) DESC'));
        $userMasterOptions = ArrayHelper::map($userMasterQuery->all(), 'id', function ($model) {
            return $model->getRoleLabel() . ' - ' . $model->fullname;
        });
        return $userMasterOptions;
    }
    
    /**
     * get manager options for account-transaction
     * @return array
     */
    public static function getUserTransactionOptions()
    {
        return ArrayHelper::map(
            static::find()
                ->where(['in', 'role', static::getUserTransactionRoles()])
                ->orderBy(['role' => SORT_ASC])
                ->all(), 
            'id', 
            function ($model) {
                return $model->getRoleLabel() . ' - ' . $model->fullname;
            }
        );
    }
    
    /**
     * get roles list available for transaction model
     */
    public static function getUserTransactionRoles()
    {
        return [static::ROLE_ADMIN, static::ROLE_ACCOUNTANT, static::ROLE_MANAGER];
    }

    /**
     * Invite user
     * @param string $password
     */
    public function sendInvite($password = '')
    {
        if ($this->email) {
            $urlCabinet = Yii::$app->urlManagerBackend->createAbsoluteUrl(['/']);
            $title = 'Приглашение в ' . Yii::$app->name;
            $message = 'Вас приглашают подключиться к системе ' . Yii::$app->name . ' с уровнем доступа: ' . $this->getRoleLabel() . '.'
. "\r\n \r\n" . 'Ваш логин: ' . $this->username . 
"\r\n"  . ($password ? ('Пароль: ' . $password) : 'Чтобы узнать пароль свяжитесь с администрацией.')
. "\r\n \r\n" . 'Ссылка для входа: <a href="' . $urlCabinet . '">' . $urlCabinet . '</a>';
            
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
