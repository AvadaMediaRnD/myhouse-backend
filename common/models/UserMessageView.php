<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "user_message_view".
 *
 * @property int $id
 * @property int $user_id
 * @property int $message_id
 */
class UserMessageView extends \common\models\ZModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_message_view';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'message_id'], 'required'],
            [['user_id', 'message_id'], 'integer'],
            [['message_id'], 'exist', 'skipOnError' => true, 'targetClass' => Message::className(), 'targetAttribute' => ['message_id' => 'id']],
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
            'user_id' => Yii::t('model', 'Пользователь'),
            'message_id' => Yii::t('model', 'Сообщение'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMessage()
    {
        return $this->hasOne(Message::className(), ['id' => 'message_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
