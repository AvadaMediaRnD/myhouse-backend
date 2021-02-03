<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "invoice_service".
 *
 * @property int $id
 * @property int $invoice_id
 * @property int $service_id
 * @property int $counter_data_id
 * @property string $amount
 * @property string $price
 * @property string $price_unit
 * 
 * @property CounterData $counterData
 * @property Invoice $invoice
 * @property Service $service
 */
class InvoiceService extends \common\models\ZModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'invoice_service';
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => \common\components\ChangeLogBehavior::className(),
                'labelObject' => 'Услуга квитанции',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function fields()
    {
        $fields = [
            'id', 'amount', 'price', 'price_unit', 'invoice_id', 'service_id', 'counter_data_id',
        ];
        return $fields;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['invoice_id', 'service_id'], 'required'],
            [['invoice_id', 'service_id'], 'integer'],
            [['amount', 'price', 'price_unit'], 'number'],
            [['invoice_id'], 'exist', 'skipOnError' => true, 'targetClass' => Invoice::className(), 'targetAttribute' => ['invoice_id' => 'id']],
            [['service_id'], 'exist', 'skipOnError' => true, 'targetClass' => Service::className(), 'targetAttribute' => ['service_id' => 'id']],
            [['counter_data_id'], 'exist', 'skipOnError' => true, 'targetClass' => CounterData::className(), 'targetAttribute' => ['counter_data_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('model', 'ID'),
            'invoice_id' => Yii::t('model', 'Квитанция'),
            'service_id' => Yii::t('model', 'Услуга'),
            'amount' => Yii::t('model', 'Расход'),
            'price' => Yii::t('model', 'Сумма'),
            'price_unit' => Yii::t('model', 'Цена за ед.'),
            'counter_data_id' => Yii::t('model', 'Показания счетчика'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoice()
    {
        return $this->hasOne(Invoice::className(), ['id' => 'invoice_id']);
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
    public function getCounterData()
    {
        return $this->hasOne(CounterData::className(), ['id' => 'counter_data_id']);
    }
    
    /**
     * @inheritdoc
     */
    public function beforeSave($insert) 
    {
//        $invoice = $this->invoice;
//        $tariffService = TariffService::find()->where(['service_id' => $this->service_id, 'tariff_id' => $invoice->tariff_id])->one();
//        $this->price = $this->amount * ($tariffService ? $tariffService->price_unit : 0);
        
        if ($this->price === null || ($this->price == 0 && $this->amount != 0 && $this->price_unit != 0)) {
            $this->price = $this->amount * $this->price_unit;
        }
        
        return parent::beforeSave($insert);
    }
}
