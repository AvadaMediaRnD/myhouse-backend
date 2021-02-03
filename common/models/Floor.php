<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "floor".
 *
 * @property int $id
 * @property string $name
 * @property int $sort
 * @property int $house_id
 * @property string $id_1c
 */
class Floor extends \common\models\ZModel {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'floor';
    }

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            [
                'class' => \common\components\ChangeLogBehavior::className(),
                'labelObject' => 'Этаж',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function fields() {
        $fields = [
            'id', 'name', 'house_id', 'sort', 'id_1c',
        ];
        return $fields;
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['house_id'], 'required'],
            [['house_id', 'sort'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['id_1c'], 'string', 'max' => 40],
            [['house_id'], 'exist', 'skipOnError' => true, 'targetClass' => House::className(), 'targetAttribute' => ['house_id' => 'id']],
            [['id_1c'], 'filter', 'filter' => function($value) {
                    return strip_tags(trim($value));
                }],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('model', 'ID'),
            'name' => Yii::t('model', 'Название'),
            'sort' => Yii::t('model', 'Сортировка'),
            'house_id' => Yii::t('model', 'ЖК'),
            'id_1c' => Yii::t('model', 'ID для 1С'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFlats() {
        return $this->hasMany(Flat::className(), ['floor_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHouse() {
        return $this->hasOne(House::className(), ['id' => 'house_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMessageAddresses() {
        return $this->hasMany(MessageAddress::className(), ['floor_id' => 'id']);
    }

}
