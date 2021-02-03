<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tariff_service".
 *
 * @property int $id
 * @property string $price_unit
 * @property int $tariff_id
 * @property int $service_id
 *
 * @property Service $service
 * @property Tariff $tariff
 */
class TariffService extends \common\models\ZModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tariff_service';
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => \common\components\ChangeLogBehavior::className(),
                'labelObject' => 'Услуга тарифа',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['price_unit'], 'number'],
            [['tariff_id', 'service_id'], 'required'],
            [['tariff_id', 'service_id'], 'integer'],
            [['service_id'], 'exist', 'skipOnError' => true, 'targetClass' => Service::className(), 'targetAttribute' => ['service_id' => 'id']],
            [['tariff_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tariff::className(), 'targetAttribute' => ['tariff_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'price_unit' => Yii::t('app', 'Price Unit'),
            'tariff_id' => Yii::t('app', 'Tariff ID'),
            'service_id' => Yii::t('app', 'Service ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getService()
    {
        return $this->hasOne(Service::className(), ['id' => 'service_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTariff()
    {
        return $this->hasOne(Tariff::className(), ['id' => 'tariff_id']);
    }
}
