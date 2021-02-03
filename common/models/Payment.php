<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "payment".
 *
 * @property int $id
 * @property string $data
 * @property int $created_at
 * @property int $updated_at
 * @property string $price
 * @property int $status
 * @property int $invoice_id
 */
class Payment extends \common\models\ZModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'payment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 'created_at', 'updated_at', 'status', 'invoice_id'], 'integer'],
            [['data'], 'string'],
            [['price'], 'number'],
            [['id'], 'unique'],
            [['invoice_id'], 'exist', 'skipOnError' => true, 'targetClass' => Invoice::className(), 'targetAttribute' => ['invoice_id' => 'id']],
        ];
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
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('model', 'ID'),
            'data' => Yii::t('model', 'Данные'),
            'created_at' => Yii::t('model', 'Добавлен'),
            'updated_at' => Yii::t('model', 'Изменен'),
            'price' => Yii::t('model', 'Сумма'),
            'status' => Yii::t('model', 'Статус'),
            'invoice_id' => Yii::t('model', 'Квитанция'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoice()
    {
        return $this->hasOne(Invoice::className(), ['id' => 'invoice_id']);
    }
}
