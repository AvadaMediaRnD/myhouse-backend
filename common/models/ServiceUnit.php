<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "service_unit".
 *
 * @property int $id
 * @property string $name
 * @property string $id_1c
 *
 * @property Service[] $services
 */
class ServiceUnit extends \common\models\ZModel {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'service_unit';
    }

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            [
                'class' => \common\components\ChangeLogBehavior::className(),
                'labelObject' => 'Ед. изм.',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['name'], 'string', 'max' => 255],
            [['id_1c'], 'string', 'max' => 40],
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
            'name' => Yii::t('model', 'Ед. изм.'),
            'id_1c' => Yii::t('model', 'ID для 1С'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServices() {
        return $this->hasMany(Service::className(), ['service_unit_id' => 'id']);
    }

    /**
     * If it is used in invoices
     * @return boolean
     */
    public function isUsedInService() {
        return (!$this->isNewRecord && Service::find()->where(['service_unit_id' => $this->id])->exists());
    }

}
