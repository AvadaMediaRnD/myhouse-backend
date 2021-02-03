<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "pay_company".
 *
 * @property int $id
 * @property string $name
 * @property string $description
 *
 * @property Invoice[] $invoices
 * @property PayCompanyService[] $payCompanyServices
 * @property Service[] $services
 */
class PayCompany extends \common\models\ZModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pay_company';
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => \common\components\ChangeLogBehavior::className(),
                'labelObject' => 'Компания получатель',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 255],
            ['description', 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Название компании'),
            'description' => Yii::t('app', 'Информация'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoices()
    {
        return $this->hasMany(Invoice::className(), ['pay_company_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPayCompanyServices()
    {
        return $this->hasMany(PayCompanyService::className(), ['pay_company_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServices()
    {
        return $this->hasMany(Service::className(), ['id' => 'service_id'])
            ->via('payCompanyServices');
    }
}
