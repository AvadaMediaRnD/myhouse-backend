<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "flat".
 *
 * @property int $id
 * @property string $flat
 * @property float $square
 * @property int $created_at
 * @property int $updated_at
 * @property int $house_id
 * @property int $user_id
 * @property int $section_id
 * @property int $riser_id
 * @property int $floor_id
 * @property int $tariff_id
 */
class Flat extends \common\models\ZModel {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'flat';
    }

    /**
     * @inheritdoc
     */
    public function fields() {
        $fields = [
            'id', 'flat', 'square', 'created_at', 'updated_at', 'house_id', 'user_id', 'section_id', 'riser_id', 'floor_id', 'tariff_id',
            'debt',
        ];
        return $fields;
    }

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            \yii\behaviors\TimestampBehavior::className(),
            [
                'class' => \common\components\ChangeLogBehavior::className(),
                'labelObject' => 'Квартира',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['created_at', 'updated_at', 'house_id', 'user_id', 'section_id', 'riser_id', 'floor_id'], 'integer'],
            ['square', 'number'],
            [['house_id'], 'required'],
            [['flat'], 'integer', 'max' => 2147483647],
            [['floor_id'], 'exist', 'skipOnError' => true, 'targetClass' => Floor::className(), 'targetAttribute' => ['floor_id' => 'id']],
            [['house_id'], 'exist', 'skipOnError' => true, 'targetClass' => House::className(), 'targetAttribute' => ['house_id' => 'id']],
            [['riser_id'], 'exist', 'skipOnError' => true, 'targetClass' => Riser::className(), 'targetAttribute' => ['riser_id' => 'id']],
            [['section_id'], 'exist', 'skipOnError' => true, 'targetClass' => Section::className(), 'targetAttribute' => ['section_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['tariff_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tariff::className(), 'targetAttribute' => ['tariff_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('model', 'ID'),
            'flat' => Yii::t('model', '№ квартиры'),
            'square' => Yii::t('model', 'Площадь (кв.м.)'),
            'created_at' => Yii::t('model', 'Добавлен'),
            'updated_at' => Yii::t('model', 'Изменен'),
            'house_id' => Yii::t('model', 'Дом'),
            'user_id' => Yii::t('model', 'Владелец'),
            'section_id' => Yii::t('model', 'Секция'),
            'riser_id' => Yii::t('model', 'Стояк'),
            'floor_id' => Yii::t('model', 'Этаж'),
            'tariff_id' => Yii::t('model', 'Тариф'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFloor() {
        return $this->hasOne(Floor::className(), ['id' => 'floor_id']);
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
    public function getRiser() {
        return $this->hasOne(Riser::className(), ['id' => 'riser_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSection() {
        return $this->hasOne(Section::className(), ['id' => 'section_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser() {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTariff() {
        return $this->hasOne(Tariff::className(), ['id' => 'tariff_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoices() {
        return $this->hasMany(Invoice::className(), ['flat_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMasterRequests() {
        return $this->hasMany(MasterRequest::className(), ['flat_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMessageAddresses() {
        return $this->hasMany(MessageAddress::className(), ['flat_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccount() {
        return $this->hasOne(Account::className(), ['flat_id' => 'id']);
    }

    public function getDebt() {
        $invoices = $this->getInvoices()
                ->andWhere(['invoice.status' => Invoice::STATUS_UNPAID])
                ->all();
        return array_sum(ArrayHelper::getColumn($invoices, 'price'));

//        $balance = $this->account->getBalance();
//        return $balance < 0 ? abs($balance) : 0;
    }

    public function getMonthPriceAverage() {
        $invoicesQuery = $this->getInvoices()
                ->andWhere(['!=', 'invoice.status', Invoice::STATUS_DISABLED]);
        $count = $invoicesQuery->count();
        $sum = array_sum(ArrayHelper::getColumn($invoicesQuery->all(), 'price'));
        return $count ? round($sum / floatval($count), 2) : 0;
    }

    public function getMonthPriceEstimate() {
        $invoices = $this->getInvoices()
                ->andWhere(['!=', 'invoice.status', Invoice::STATUS_DISABLED])
                ->orderBy(['invoice.uid_date' => SORT_DESC])
                ->limit(2)
                ->all();
        if (!$invoices) {
            return 0;
        }
        if (count($invoices) == 1) {
            return $invoices[0]->getPrice();
        }
        return round(($invoices[0]->getPrice() + $invoices[1]->getPrice()) / 2, 2);
    }

}
