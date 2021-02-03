<?php
namespace api\modules\v1\models;

use common\models\User;
use common\models\validators\PhoneValidator;
use api\modules\v1\models\ZModel as Model;
use Yii;

/**
 * User update form
 */
class UserUpdateModel extends Model
{
    public $username;
    public $status;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'filter', 'filter' => 'trim'],
            [
                'username',
                'unique',
                'targetClass' => '\common\models\User',
                'message' => 'User with this phone already exists.',
                'when' => function ($model) {
                    return $model->username != $this->getUser()->username;
                }
            ],
            ['username', 'string', 'min' => 2, 'max' => 255],
            ['username', PhoneValidator::className(), 'country' => 'UA'],

            ['status', 'in', 'range' => [User::STATUS_ACTIVE, User::STATUS_NEW, User::STATUS_DELETED]],
        ];
    }

    /**
     * Update user.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function process()
    {
        $user = $this->getUser();
        if ($user && $this->validate()) {
            $user->username = $this->username ? $this->username : $user->username;
            $user->status = $this->status !== null ? $this->status : $user->status;
            if ($user->save()) {
                return $user;
            }
        }

        return null;
    }

}
