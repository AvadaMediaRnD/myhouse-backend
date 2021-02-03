<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tariff".
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property int $is_default
 * @property int $created_at
 * @property int $updated_at
 * @property string $id_1c
 *
 * @property Invoice[] $invoices
 * @property TariffService[] $tariffServices
 */
class Tariff extends \common\models\ZModel {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'tariff';
    }

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            \yii\behaviors\TimestampBehavior::className(),
            [
                'class' => \common\components\ChangeLogBehavior::className(),
                'labelObject' => 'Тариф',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['description'], 'string'],
            [['is_default', 'created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['id_1c'], 'string', 'max' => 40],
            [['id_1c'], 'filter', 'filter' => function($value) {
                    return strip_tags(trim($value));
                }],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Название тарифа'),
            'description' => Yii::t('app', 'Описание тарифа'),
            'is_default' => Yii::t('app', 'По-умолчанию'),
            'created_at' => Yii::t('app', 'Дата создания'),
            'updated_at' => Yii::t('app', 'Дата редактирования'),
            'id_1c' => Yii::t('model', 'ID для 1С'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoices() {
        return $this->hasMany(Invoice::className(), ['tariff_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTariffServices() {
        return $this->hasMany(TariffService::className(), ['tariff_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert) {
        // verify only 1 is default
        if (static::find()->count() == 0) {
            $this->is_default = 1;
        }
        return parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes) {
        // verify only 1 is default
        if ($this->is_default) {
            static::updateAll(['is_default' => 0], ['!=', 'id', $this->id]);
        }
        return parent::afterSave($insert, $changedAttributes);
    }

    /**
     * If it is used in invoices
     * @return boolean
     */
    public function isUsedInInvoice() {
        return (!$this->isNewRecord && Invoice::find()->where(['tariff_id' => $this->id])->exists());
    }

    /**
     * Get default tariff
     * @return Tariff
     */
    public function findDefaultModel() {
        return static::find()->orderBy(['is_default' => SORT_DESC])->one();
    }

}
