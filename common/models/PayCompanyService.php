<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "pay_company_service".
 *
 * @property int $id
 * @property int $service_id
 * @property int $pay_company_id
 *
 * @property PayCompany $payCompany
 * @property Service $service
 */
class PayCompanyService extends \common\models\ZModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pay_company_service';
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => \common\components\ChangeLogBehavior::className(),
                'labelObject' => 'Услуга компании получателя',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['service_id', 'pay_company_id'], 'required'],
            [['service_id', 'pay_company_id'], 'integer'],
            [['pay_company_id'], 'exist', 'skipOnError' => true, 'targetClass' => PayCompany::className(), 'targetAttribute' => ['pay_company_id' => 'id']],
            [['service_id'], 'exist', 'skipOnError' => true, 'targetClass' => Service::className(), 'targetAttribute' => ['service_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'service_id' => Yii::t('app', 'Услуга'),
            'pay_company_id' => Yii::t('app', 'Компания'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPayCompany()
    {
        return $this->hasOne(PayCompany::className(), ['id' => 'pay_company_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getService()
    {
        return $this->hasOne(Service::className(), ['id' => 'service_id']);
    }
}
