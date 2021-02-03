<?php
namespace api\modules\v1\models;

use common\models\Profile;
use common\models\User;
use common\models\validators\PhoneValidator;
use api\modules\v1\models\ZModel as Model;
use Yii;

/**
 * Signup form
 */
class SignupModel extends Model
{
    public $username;
    public $password;
    public $password2;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'filter', 'filter' => 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'User with this phone already exists.'],
            ['username', 'string', 'min' => 2, 'max' => 255],
            ['username', PhoneValidator::className(), 'country' => 'UA'],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],

            ['password2', 'required'],
            ['password2', 'string', 'min' => 6],
            ['password2', 'compare', 'compareAttribute' => 'password', 'message' => 'Passwords do not match.' ],
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if ($this->validate()) {
            $user = new User();
            $user->username = $this->username;
            $user->setPassword($this->password);
            $user->generateAuthKey();
            if ($user->save()) {
                $profile = new Profile();
                $profile->user_id = $user->id;
                $profile->firstname = '';
                $profile->middlename = '';
                $profile->lastname = '';
                $profile->save();
                return $user;
            }
        }

        return null;
    }
}
