<?php
namespace backend\models;

use common\models\Message;
use common\models\MessageAddress;
use Yii;
use yii\base\Model;

/**
 * Message form
 */
class MessageForm extends Model
{
    public $name;
    public $description;
    public $type;
    public $status;
    public $user_id;
    public $house_id;
    public $section_id;
    public $riser_id;
    public $floor_id;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'type'], 'string', 'max' => 255],
            [['description'], 'string', 'max' => 15000],
            ['status', 'default', 'value' => Message::STATUS_WAITING],
            ['status', 'in', 'range' => [Message::STATUS_DISABLED, Message::STATUS_WAITING, Message::STATUS_SENT]],
            [['user_id', 'house_id', 'section_id', 'riser_id', 'floor_id'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('model', 'Заголовок'),
            'description' => Yii::t('model', 'Текст'),
            'type' => Yii::t('model', 'Тип'),
            'status' => Yii::t('model', 'Статус'),
            'user_id' => Yii::t('model', 'Пользователь'),
            'house_id' => Yii::t('model', 'ЖК'),
            'section_id' => Yii::t('model', 'Секция'),
            'riser_id' => Yii::t('model', 'Стояк'),
            'floor_id' => Yii::t('model', 'Этаж'),
        ];
    }

    /**
     * Signs user up.
     *
     * @return Message|null the saved model or null if saving fails
     */
    public function process()
    {
        if ($this->validate()) {
            $user = User::findByUsername($this->username);
            if (!$user) {
                $user = new User();
            }
            $user->username = $this->username;
            if ($this->password) {
                $user->setPassword($this->password);
            }
            if ($user->isNewRecord) {
                $user->generateAuthKey();
            }

            if ($user->save()) {
                $profile = $user->profile ? $user->profile : new Profile();
                if ($profile->isNewRecord) {
                    $profile->user_id = $user->id;
                }
                $profile->firstname = $this->firstname;
                $profile->lastname = $this->lastname;
                $profile->middlename = $this->middlename;
                $profile->save();
                return $user;
            }
        }

        return null;
    }

    /**
     * @param Message $model
     * @return UserForm
     */
    public static function loadFromModel($model) {
        $form = new static();
        if ($model) {
            $form->username = $model->username;
            $form->firstname = $model->profile->firstname;
            $form->lastname = $model->profile->lastname;
            $form->middlename = $model->profile->middlename;
            $form->status = $model->status;
        }
        return $form;
    }
}
