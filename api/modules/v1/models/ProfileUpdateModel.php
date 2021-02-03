<?php
namespace api\modules\v1\models;

use common\models\Profile;
use api\modules\v1\models\ZModel as Model;
use Yii;

/**
 * Profile update form
 */
class ProfileUpdateModel extends Model
{
    public $firstname;
    public $lastname;
    public $middlename;
    public $birthdate;
    public $email;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['firstname', 'lastname', 'middlename', 'birthdate', 'email'], 'string', 'max' => 255],
        ];
    }

    /**
     * Update user profile.
     *
     * @return Profile|null the saved model or null if saving fails
     */
    public function process()
    {
        $user = $this->getUser();
        if ($user && $this->validate()) {
            $profile = $user->profile;
            $profile->firstname = $this->firstname ? $this->firstname : $profile->firstname;
            $profile->lastname = $this->lastname ? $this->lastname : $profile->lastname;
            $profile->middlename = $this->middlename ? $this->middlename : $profile->middlename;
            if ($this->email && $this->email != $profile->email) {
                $profile->setEmailUnconfirmed($this->email);
            }
            $profile->birthdate = $this->birthdate ? (date('Y-m-d', strtotime($this->birthdate))) : $profile->birthdate;
            if ($profile->save()) {
                if ($profile->email_confirm_status == 0) {
                    $profile->sendEmailConfirmation();
                }

                return $profile;
            }
        }

        return null;
    }

}
