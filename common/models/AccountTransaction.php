<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "account_transaction".
 *
 * @property int $id
 * @property int $uid
 * @property string $uid_date
 * @property string $type
 * @property int $status
 * @property string $amount
 * @property string $description
 * @property int $currency_id
 * @property int $account_id
 * @property int $invoice_id
 * @property int $user_admin_id
 * @property int $transaction_purpose_id
 * @property int $invoice_service_id
 * @property int $user_id
 *
 * @property Account $account
 * @property Currency $currency
 * @property Invoice $invoice
 * @property UserAdmin $userAdmin
 * @property TransactionPurpose $transactionPurpose
 */
class AccountTransaction extends \common\models\ZModel {

    const PURPOSE_USER_PAY = 1;
    const PURPOSE_INVOICE_PAY = 2;
    const PURPOSE_WITHDRAW = 3;
    const STATUS_COMPLETE = 10;
    const STATUS_WAITING = 5;
    const STATUS_DISABLED = 0;
    const TYPE_IN = 'in';
    const TYPE_OUT = 'out';

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'account_transaction';
    }

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            // \yii\behaviors\TimestampBehavior::className(),
            [
                'class' => \common\components\ChangeLogBehavior::className(),
                'labelObject' => 'Платеж',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['type'], 'string'],
            [['status', 'currency_id', 'account_id', 'user_admin_id', 'invoice_id', 'user_id'], 'integer'],
            [['amount'], 'number'],
            [['uid', 'uid_date', 'amount', 'currency_id', 'status'], 'required'],
            [['uid', 'uid_date'], 'string', 'max' => 255],
            ['uid', 'unique'],
            ['description', 'string'],
            [['account_id'], 'exist', 'skipOnError' => true, 'targetClass' => Account::className(), 'targetAttribute' => ['account_id' => 'id']],
            [['currency_id'], 'exist', 'skipOnError' => true, 'targetClass' => Currency::className(), 'targetAttribute' => ['currency_id' => 'id']],
            [['invoice_id'], 'exist', 'skipOnError' => true, 'targetClass' => Invoice::className(), 'targetAttribute' => ['invoice_id' => 'id']],
            [['user_admin_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserAdmin::className(), 'targetAttribute' => ['user_admin_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['transaction_purpose_id'], 'exist', 'skipOnError' => true, 'targetClass' => TransactionPurpose::className(), 'targetAttribute' => ['transaction_purpose_id' => 'id']],
            [['invoice_service_id'], 'exist', 'skipOnError' => true, 'targetClass' => InvoiceService::className(), 'targetAttribute' => ['invoice_service_id' => 'id']],
            ['account_id', 'validateAccountStatus'],
            ['amount', 'validateOutAmount'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'uid' => Yii::t('app', 'Uid'),
            'uid_date' => Yii::t('app', 'Дата'),
            'type' => Yii::t('app', 'Приход/расход'),
            'status' => Yii::t('app', 'Статус'),
            'amount' => Yii::t('app', 'Сумма'),
            'description' => Yii::t('app', 'Комментарий'),
            'currency_id' => Yii::t('app', 'Валюта'),
            'account_id' => Yii::t('app', 'Лицевой счет'),
            'invoice_id' => Yii::t('app', 'Квитанция'),
            'user_admin_id' => Yii::t('app', 'Менеджер'),
            'user_id' => Yii::t('app', 'Клиент'),
            'transaction_purpose_id' => Yii::t('app', 'Тип платежа'),
            'invoice_service_id' => Yii::t('app', 'Услуга'),
        ];
    }

    /**
     * 
     * @param string $attribute_name
     * @param array $params
     */
    public function validateAccountStatus($attribute_name, $params) {
        if (!empty($this->account_id) && ($this->account->status == Account::STATUS_DISABLED)
        ) {
            $this->addError($attribute_name, Yii::t('model', 'Этот лицевой счет неактивен.'));
            return false;
        }
        return true;
    }

    /**
     * 
     * @param string $attribute_name
     * @param array $params
     */
    public function validateOutAmount($attribute_name, $params) {
        if ($this->type == static::TYPE_OUT && $this->account_id == null && ($this->amount > Account::getCashboxBalance() + $this->getOldAttribute($attribute_name))
        ) {
            $this->addError($attribute_name, Yii::t('model', 'В кассе недостаточно средств.'));
            return false;
        }
        return true;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccount() {
        return $this->hasOne(Account::className(), ['id' => 'account_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCurrency() {
        return $this->hasOne(Currency::className(), ['id' => 'currency_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoice() {
        return $this->hasOne(Invoice::className(), ['id' => 'invoice_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoiceService() {
        return $this->hasOne(InvoiceService::className(), ['id' => 'invoice_service_id']);
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
    public function getUser() {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTransactionPurpose() {
        return $this->hasOne(TransactionPurpose::className(), ['id' => 'transaction_purpose_id']);
    }

    /**
     * @return array
     */
    public static function getStatusOptions() {
        return [
            static::STATUS_COMPLETE => Yii::t('model', 'Проведен'),
            static::STATUS_WAITING => Yii::t('model', 'Не проведен'),
                // static::STATUS_DISABLED => Yii::t('model', 'Заблокирован'),
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
        $itemClass = 'text-default';
        if ($status == static::STATUS_COMPLETE) {
            $itemClass = 'text-green';
        } elseif ($status == static::STATUS_WAITING) {
            $itemClass = 'text-red';
        }
        return '<span class="text ' . $itemClass . '">' . $statusLabel . '</span>';
    }

    /**
     * @return array
     */
    public static function getTypeOptions() {
        return [
            static::TYPE_IN => Yii::t('model', 'Приход'),
            static::TYPE_OUT => Yii::t('model', 'Расход'),
        ];
    }

    /**
     * @param null $type
     * @return mixed|null
     */
    public function getTypeLabel($type = null) {
        $type = $type == null ? $this->type : $type;
        $options = static::getTypeOptions();
        return isset($options[$type]) ? $options[$type] : null;
    }

    /**
     * @param null $type
     * @return mixed|null
     */
    public function getTypeLabelHtml($type = null) {
        $type = $type == null ? $this->type : $type;
        $typeLabel = $this->getTypeLabel($type);
        $itemClass = 'text-default';
        if ($type == static::TYPE_IN) {
            $itemClass = 'text-green';
        } elseif ($type == static::TYPE_OUT) {
            $itemClass = 'text-red';
        }
        return '<span class="text ' . $itemClass . '">' . $typeLabel . '</span>';
    }

    public function getUidDate() {
        if (!$this->uid_date) {
            return $this->uid_date;
        }
        return date(Yii::$app->params['dateFormat'], strtotime($this->uid_date));
    }

    /**
     * 
     */
    public function generateUid() {
        $this->uid = date('dmy') . sprintf('%05d', static::find()->max('id') + 1);
    }
    
}
