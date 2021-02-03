<?php

namespace common\models;

use Yii;
use common\helpers\DateHelper;

/**
 * This is the model class for table "counter_data".
 *
 * @property int $id
 * @property string $uid
 * @property string $uid_date
 * @property float $amount
 * @property float $amount_total // value from counter absolute
 * @property int $created_at
 * @property int $updated_at
 * @property int $status
 * @property int $flat_id
 * @property int $user_admin_id
 * @property int $service_id
 * @property int $counter_data_last_id
 *
 * @property Flat $flat
 * @property Service $service
 * @property UserAdmin $userAdmin
 * @property CounterData $counterDataLast
 */
class CounterData extends \common\models\ZModel {

    const STATUS_PAY_DONE = 12;
    const STATUS_ACTIVE = 10;
    const STATUS_NEW = 5;
    const STATUS_DISABLED = 0;

    public $isAutoSetLast = false;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'counter_data';
    }

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            \yii\behaviors\TimestampBehavior::className(),
            [
                'class' => \common\components\ChangeLogBehavior::className(),
                'labelObject' => 'Показание счетчика',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['uid', 'flat_id'], 'required'],
            [['amount', 'amount_total'], 'number'],
            [['created_at', 'updated_at', 'status', 'flat_id', 'user_admin_id', 'service_id'], 'integer'],
            [['uid', 'uid_date'], 'string', 'max' => 255],
            [['uid'], 'unique'],
            [['flat_id'], 'exist', 'skipOnError' => true, 'targetClass' => Flat::className(), 'targetAttribute' => ['flat_id' => 'id']],
            [['service_id'], 'exist', 'skipOnError' => true, 'targetClass' => Service::className(), 'targetAttribute' => ['service_id' => 'id']],
            [['user_admin_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserAdmin::className(), 'targetAttribute' => ['user_admin_id' => 'id']],
            [['counter_data_last_id'], 'exist', 'skipOnError' => true, 'targetClass' => static::className(), 'targetAttribute' => ['counter_data_last_id' => 'id']],
            [['isAutoSetLast'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('model', 'ID'),
            'uid' => Yii::t('model', 'Uid'),
            'uid_date' => Yii::t('model', 'Дата'),
            'amount' => Yii::t('model', 'Показания за период'),
            'amount_total' => Yii::t('model', 'Показания счетчика'),
            'created_at' => Yii::t('model', 'Created At'),
            'updated_at' => Yii::t('model', 'Updated At'),
            'status' => Yii::t('model', 'Статус'),
            'flat_id' => Yii::t('model', 'Квартира'),
            'user_admin_id' => Yii::t('model', 'Пользователь'),
            'service_id' => Yii::t('model', 'Счетчик'),
            'counter_data_last_id' => Yii::t('model', 'Предыдущие показания'),
            'isAutoSetLast' => Yii::t('model', 'Установить автоматически'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFlat() {
        return $this->hasOne(Flat::className(), ['id' => 'flat_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getService() {
        return $this->hasOne(Service::className(), ['id' => 'service_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserAdmin() {
        return $this->hasOne(UserAdmin::className(), ['id' => 'user_admin_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCounterDataLast() {
        return $this->hasOne(static::className(), ['id' => 'counter_data_last_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoiceService() {
        return $this->hasOne(InvoiceService::className(), ['counter_data_id' => 'id']);
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert) {
        if ($this->isAutoSetLast) {
            $this->counter_data_last_id = null;
        }

        if (!$this->counter_data_last_id) {
            $this->counter_data_last_id = static::getLastIdByData($this->amount_total, $this->uid_date, $this->service_id, $this->flat_id, $this->id);
        }

        if ($this->hasAttribute('amount')) {
            $amount = 0;
            $counterDataLast = null;
            if ($this->counter_data_last_id) {
                $counterDataLast = $this->getCounterDataLast()->one();
            }
            if ($counterDataLast) {
                $amount = $this->amount_total - $counterDataLast->amount_total;
            } else {
                $amount = $this->amount_total;
            }
//            if ($amount < 0) {
//                $amount = 0;
//            }
            $this->amount = round($amount, 1);
        }

        return parent::beforeSave($insert);
    }

    public function getUidDate() {
        if (!$this->uid_date) {
            return $this->uid_date;
        }
        return date(Yii::$app->params['dateFormat'], strtotime($this->uid_date));
    }

    /**
     * @param Service $service
     * @param Flat $flat
     */
    public static function getOptions($service = null, $flat = null, $currentId = null, $onlyNew = false) {
        $query = static::find()->orderBy(['uid_date' => SORT_DESC, 'id' => SORT_DESC]);
        if ($service) {
            $query->andWhere(['service_id' => $service->id]);
        }
        if ($flat) {
            $query->andWhere(['flat_id' => $flat->id]);
        }
        if ($currentId) {
            $query->andWhere(['!=', 'id', $currentId]);
        }
        if ($onlyNew) {
            $query->andWhere(['status' => static::STATUS_NEW]);
        }
        return \yii\helpers\ArrayHelper::map($query->all(), 'id', function ($model) {
                    return '#' . $model->uid . ' от ' . $model->getUidDate() . ', показания: ' . $model->amount_total . ', расход: ' . $model->amount;
                });
    }

    /**
     * @return array
     */
    public static function getStatusOptions() {
        return [
            static::STATUS_NEW => Yii::t('model', 'Новое'),
            static::STATUS_ACTIVE => Yii::t('model', 'Учтено'),
            static::STATUS_PAY_DONE => Yii::t('model', 'Учтено и оплачено'),
            static::STATUS_DISABLED => Yii::t('model', 'Нулевое'),
        ];
    }

    /**
     * @param null $status
     * @return mixed|null
     */
    public function getStatusLabel($status = null) {
        $status = $status == null ? $this->status : $status;
        $options = static::getStatusOptions();
        return isset($options[$status]) ? $options[$status] : null;
    }

    /**
     * @param null $status
     * @return mixed|null
     */
    public function getStatusLabelHtml($status = null) {
        $status = $status == null ? $this->status : $status;
        $statusLabel = $this->getStatusLabel($status);
        $itemClass = 'label-default';
        if ($status == static::STATUS_ACTIVE) {
            $itemClass = 'label-success';
        } elseif ($status == static::STATUS_PAY_DONE) {
            $itemClass = 'label-success';
        } elseif ($status == static::STATUS_NEW) {
            $itemClass = 'label-warning';
        } elseif ($status == static::STATUS_DISABLED) {
            $itemClass = 'label-primary';
        }
        return '<small class="label ' . $itemClass . '">' . $statusLabel . '</small>';
    }

    /**
     * Get month + year
     * @param boolean $ua is months in ua/ru
     * @return string
     */
    public function getMonthYearPrint($ua = false) {
        return DateHelper::getMonthYearLabel($this->uid_date, $ua);
    }

    /**
     * Get list of options for month+year column filter
     * @return array
     */
    public function getMonthYearOptions() {
        $query = static::find()
                ->select(new \yii\db\Expression('DATE_FORMAT(`uid_date`, "%Y-%m") AS `ymd`'))
                ->groupBy('ymd')
                ->orderBy(['ymd' => SORT_DESC]);
        $query->andFilterWhere(['flat_id' => Yii::$app->request->get('CounterDataSearch')['flat_id']]);
        $query->andFilterWhere(['service_id' => Yii::$app->request->get('CounterDataSearch')['service_id']]);
        $dates = $query->asArray()->all();
        return \yii\helpers\ArrayHelper::map($dates, 'ymd', function ($item) {
                    return DateHelper::getMonthYearLabel($item['ymd']);
                });
    }

    /**
     * Calc amount for period by last counter data
     * @param float $amountTotal
     * @param string $uidDate
     * @param int $serviceId
     * @param int $flatId
     * @return float
     */
    public static function getAmountByData($amountTotal, $uidDate, $serviceId, $flatId) {
        $amount = 0;
        $counterDataLast = static::getLastByData($amountTotal, $uidDate, $serviceId, $flatId);
        if ($counterDataLast) {
            $amount = $amountTotal - $counterDataLast->amount_total;
        } else {
            $amount = $amountTotal;
        }
//        if ($amount < 0) {
//            $amount = 0;
//        }
        return round($amount, 1);
    }

    /**
     * Find last counter data id
     * @param float $amountTotal
     * @param string $uidDate
     * @param int $serviceId
     * @param int $flatId
     * @return int
     */
    protected static function getLastIdByData($amountTotal, $uidDate, $serviceId, $flatId, $currentId = null) {
        $counterDataLast = static::getLastByData($amountTotal, $uidDate, $serviceId, $flatId, $currentId);
        return $counterDataLast ? $counterDataLast->id : null;
    }

    protected static function getLastByData($amountTotal, $uidDate, $serviceId, $flatId, $currentId = null) {
        $counterDataLastQuery = static::find()
                ->where(['<=', 'uid_date', date('Y-m-d', strtotime($uidDate))])
                ->andWhere(['service_id' => $serviceId, 'flat_id' => $flatId])
                ->andWhere(['in', 'status', [static::STATUS_ACTIVE, static::STATUS_PAY_DONE, static::STATUS_DISABLED]])
                ->orderBy(['uid_date' => SORT_DESC, 'created_at' => SORT_DESC])
                ->limit(1);
        $counterDataLastQuery->andFilterWhere(['!=', 'id', $currentId]);
        return $counterDataLastQuery->one();
    }

}
