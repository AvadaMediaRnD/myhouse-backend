<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "house_user_admin".
 *
 * @property int $id
 * @property int $house_id
 * @property int $user_admin_id
 *
 * @property House $house
 * @property UserAdmin $userAdmin
 */
class HouseUserAdmin extends \common\models\ZModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'house_user_admin';
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => \common\components\ChangeLogBehavior::className(),
                'labelObject' => 'Назначение пользователя к дому',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['house_id', 'user_admin_id'], 'required'],
            [['house_id', 'user_admin_id'], 'integer'],
            [['house_id'], 'exist', 'skipOnError' => true, 'targetClass' => House::className(), 'targetAttribute' => ['house_id' => 'id']],
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
            'house_id' => Yii::t('app', 'House ID'),
            'user_admin_id' => Yii::t('app', 'User Admin ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHouse()
    {
        return $this->hasOne(House::className(), ['id' => 'house_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserAdmin()
    {
        return $this->hasOne(UserAdmin::className(), ['id' => 'user_admin_id']);
    }
}
