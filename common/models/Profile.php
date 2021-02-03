<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "profile".
 *
 * @property int $id
 * @property string $firstname
 * @property string $lastname
 * @property string $middlename
 * @property string $birthdate
 * @property string $phone
 * @property string $viber
 * @property string $telegram
 * @property string $image
 * @property int $user_id
 *
 * @property User $user
 */
class Profile extends \common\models\ZModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'profile';
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => \common\components\ChangeLogBehavior::className(),
                'labelObject' => 'Профиль',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function fields()
    {
        $fields = [
            'id', 'firstname', 'lastname', 'middlename', 'birthdate', 'phone', 'viber', 'telegram', 'image', 'user_id',
        ];
        return $fields;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id'], 'integer'],
            [['firstname', 'lastname', 'middlename', 'birthdate', 'phone',
                'viber', 'telegram', 'image'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('model', 'ID'),
            'firstname' => Yii::t('model', 'Имя'),
            'lastname' => Yii::t('model', 'Фамилия'),
            'middlename' => Yii::t('model', 'Отчество'),
            'birthdate' => Yii::t('model', 'Дата рождения'),
            'phone' => Yii::t('model', 'Телефон'),
            'viber' => Yii::t('model', 'Viber'),
            'telegram' => Yii::t('model', 'Telegram'),
            'image' => Yii::t('model', 'Аватар'),
            'user_id' => Yii::t('model', 'Владелец'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * Get data for api
     * @return array
     */
    public function getData()
    {
        return [
            'id' => $this->id,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'middlename' => $this->middlename,
            'birthdate' => $this->birthdate,
            'phone' => $this->phone,
            'viber' => $this->viber,
            'telegram' => $this->telegram,
            'image' => $this->image,
        ];
    }

    /**
     * @param boolean $short
     * @return string
     */
    public function getFullname($short = false)
    {
        $nameParts = [];
        if ($this->lastname) {
            $nameParts[] = $this->lastname;
        }
        if ($this->firstname) {
            if ($short) {
                $nameParts[] = $this->firstname ? mb_substr($this->firstname, 0, 1, 'utf-8').'.' : '';
            } else {
                $nameParts[] = $this->firstname;
            }
        }
        if ($this->middlename) {
            if ($short) {
                $nameParts[] = $this->middlename ? mb_substr($this->middlename, 0, 1, 'utf-8').'.' : '';
            } else {
                $nameParts[] = $this->middlename;
            }
        }
        return $nameParts ? implode(' ', $nameParts) : null;
    }
    
    /**
     * 
     * @return type
     */
    public function getAvatar()
    {
        if ($this->image && file_exists(Yii::getAlias('@frontend/web' . $this->image))) {
            return $this->image;
        }
        return '/upload/placeholder.jpg';
    }
    
    public function getBirthDate()
    {
        if (!$this->birthdate) {
            return $this->birthdate;
        }
        return date(Yii::$app->params['dateFormat'], strtotime($this->birthdate));
    }

    /**
     * check confirm code for email change
     * @param $code
     */
//    public function checkEmailConfirm($code)
//    {
//        if ($this->email_confirm_code == $code) {
//            $this->setEmailConfirmed();
//        }
//    }

    /**
     * update email as confirmed
     */
//    protected function setEmailConfirmed()
//    {
//        $this->email_confirm_code = '';
//        $this->email_confirm_status = 1;
//    }

    /**
     * set new email as unconfirmed, generate confirm key
     * @param $value
     */
//    public function setEmailUnconfirmed($value)
//    {
//        if ($this->email != $value) {
//            $this->email = $value;
//            $this->email_confirm_status = 0;
//            $this->email_confirm_code = rand(1000, 9999) . '';
//        }
//    }

    /**
     * send email to new address
     */
//    public function sendEmailConfirmation()
//    {
//        $header = "From: \"MyHouse24\" <".\Yii::$app->params['supportEmail'].">\n";
//        $header .= "Content-Type: text/html; charset=UTF-8";
//        mail(
//            $this->email,
//            "=?utf-8?B?".base64_encode('Код подтверждения email адреса МойДом24')."?=",
//            'Ваш код подтверждения для смены email адреса: ' . $this->email_confirm_code,
//            $header);
//    }

}
