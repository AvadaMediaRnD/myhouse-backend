<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "user_admin_log".
 *
 * @property int $id
 * @property string $event
 * @property string $object_class
 * @property int $object_id
 * @property string $old_attributes
 * @property string $message
 * @property int $created_at
 * @property int $updated_at
 * @property int $user_admin_id
 *
 * @property UserAdmin $userAdmin
 */
class UserAdminLog extends \common\models\ZModel
{
    const LOG_INSERT = 'insert';
    const LOG_UPDATE = 'update';
    const LOG_DELETE = 'delete';
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_admin_log';
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['object_id', 'created_at', 'updated_at', 'user_admin_id'], 'integer'],
            [['old_attributes', 'message'], 'string'],
            [['event', 'object_class'], 'string', 'max' => 255],
            [['user_admin_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserAdmin::className(), 'targetAttribute' => ['user_admin_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'event' => Yii::t('app', 'Событие'),
            'object_class' => Yii::t('app', 'Объект'),
            'object_id' => Yii::t('app', 'ID объекта'),
            'old_attributes' => Yii::t('app', 'Данные до изменений'),
            'message' => Yii::t('app', 'Текст'),
            'created_at' => Yii::t('app', 'Создано'),
            'updated_at' => Yii::t('app', 'Обновлено'),
            'user_admin_id' => Yii::t('app', 'Пользователь'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserAdmin()
    {
        return $this->hasOne(UserAdmin::className(), ['id' => 'user_admin_id']);
    }
}
