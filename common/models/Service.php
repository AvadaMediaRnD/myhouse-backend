<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "service".
 *
 * @property int $id
 * @property string $name
 * @property int $is_counter
 * @property int $service_unit_id
 * @property string $id_1c

 *
 * @property CounterData[] $counterDatas
 * @property ServiceUnit $serviceUnit
 */
class Service extends \common\models\ZModel {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'service';
    }

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            [
                'class' => \common\components\ChangeLogBehavior::className(),
                'labelObject' => 'Услуга',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['service_unit_id', 'is_counter'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['id_1c'], 'string', 'max' => 40],
            [['service_unit_id'], 'exist', 'skipOnError' => true, 'targetClass' => ServiceUnit::className(), 'targetAttribute' => ['service_unit_id' => 'id']],
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
            'name' => Yii::t('model', 'Услуга'),
            'is_counter' => Yii::t('model', 'Показывать в счетчиках'),
            'service_unit_id' => Yii::t('model', 'Ед. изм.'),
            'id_1c' => Yii::t('model', 'ID для 1С'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCounterDatas() {
        return $this->hasMany(CounterData::className(), ['service_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServiceUnit() {
        return $this->hasOne(ServiceUnit::className(), ['id' => 'service_unit_id']);
    }

    /**
     * @return array
     */
    public static function getOptions($payCompanyModel = null, $counterOnly = false) {
        $companyServiceIds = $payCompanyModel ? ArrayHelper::getColumn($payCompanyModel->payCompanyServices, 'service_id') : [];
        if ($companyServiceIds) {
            $queryCompany = Service::find()->where(['in', 'id', $companyServiceIds]);
            $queryNotCompany = Service::find()->where(['in', 'id', $companyServiceIds]);
            if ($counterOnly) {
                $queryCompany->andWhere(['is_counter' => 1]);
                $queryNotCompany->andWhere(['is_counter' => 1]);
            }
            $serviceOptions = [
                'Услуги получателя' => ArrayHelper::map($queryCompany->all(), 'id', 'name'),
                'Другие услуги' => ArrayHelper::map($queryNotCompany->all(), 'id', 'name')
            ];
        } else {
            $query = Service::find();
            if ($counterOnly) {
                $query->andWhere(['is_counter' => 1]);
            }
            $serviceOptions = ArrayHelper::map($query->all(), 'id', 'name');
        }
        return $serviceOptions;
    }

    /**
     * If it is used in invoices
     * @return boolean
     */
    public function isUsedInInvoice() {
        return (!$this->isNewRecord && InvoiceService::find()->where(['service_id' => $this->id])->exists());
    }

}
