<?php
namespace api\modules\v1\models;

use common\models\Profile;
use api\modules\v1\models\ZModel as Model;
use common\models\User;
use Yii;

/**
 * User password update form
 */
class UserPasswordUpdateModel extends Model
{
    public $password_old;
    public $password;
    public $password2;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['password_old', 'required'],
            ['password_old', 'string', 'min' => 6],
            ['password_old','findOldPassword'],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],

            ['password2', 'required'],
            ['password2', 'string', 'min' => 6],
            ['password2', 'compare', 'compareAttribute' => 'password', 'message' => 'Passwords do not match.' ],
        ];
    }

    public function findOldPassword($attribute, $params)
    {
        $user = Yii::$app->user->identity;
        if (!$user->validatePassword($this->password_old)) {
            $this->addError($attribute, 'Old password is incorrect');
        }
    }

    /**
     * Update user profile.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function process()
    {
        $user = $this->getUser();
        if ($user && $this->validate()) {
            $user->setPassword($this->password);
//            $user->generateAuthKey();
            if ($user->save()) {
                return $user;
            }
        }

        return null;
    }

}
