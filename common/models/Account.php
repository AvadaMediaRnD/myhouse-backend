<?php

namespace common\models;

use Yii;
use common\helpers\PriceHelper;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "account".
 *
 * @property int $id
 * @property string $uid
 * @property int $status
 * @property int $flat_id
 *
 * @property Flat $flat
 * @property AccountTransaction[] $accountTransactions
 */
class Account extends \common\models\ZModel
{
    const STATUS_ACTIVE = 10;
    const STATUS_DISABLED = 0;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'account';
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => \common\components\ChangeLogBehavior::className(),
                'labelObject' => 'Лицевой счет',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'flat_id'], 'integer'],
            [['uid'], 'string', 'max' => 255],
            [['uid'], 'unique'],
            [['uid'], 'required'],
            [['flat_id'], 'exist', 'skipOnError' => true, 'targetClass' => Flat::className(), 'targetAttribute' => ['flat_id' => 'id']],
            [['flat_id'], 'unique', 'skipOnEmpty' => true, 'message' => 'К этой квартире уже привязан счет.'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'uid' => Yii::t('app', 'Лицевой счет'),
            'status' => Yii::t('app', 'Статус'),
            'flat_id' => Yii::t('app', 'Квартира'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFlat()
    {
        return $this->hasOne(Flat::className(), ['id' => 'flat_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccountTransactions()
    {
        return $this->hasMany(AccountTransaction::className(), ['account_id' => 'id']);
    }
    
    /**
     * @return array
     */
    public static function getStatusOptions()
    {
        return [
            static::STATUS_ACTIVE => Yii::t('model', 'Активен'),
            static::STATUS_DISABLED => Yii::t('model', 'Неактивен'),
        ];
    }

    /**
     * @param null $status
     * @return mixed|null
     */
    public function getStatusLabel($status = null)
    {
        $status = $status == null ? $this->status : $status;
        $options = static::getStatusOptions();
        return isset($options[$status]) ? $options[$status] : null;
    }
    
    /**
     * @param null $status
     * @return mixed|null
     */
    public function getStatusLabelHtml($status = null)
    {
        $status = $status == null ? $this->status : $status;
        $statusLabel = $this->getStatusLabel($status);
        $itemClass = 'label-default';
        if ($status == static::STATUS_ACTIVE) {
            $itemClass = 'label-success';
        } elseif ($status == static::STATUS_DISABLED) {
            $itemClass = 'label-danger';
        }
        return '<small class="label '.$itemClass.'">'.$statusLabel.'</small>';
    }
    
    /**
     * 
     */
    public function generateUid()
    {
        $this->uid = date('dmy') . sprintf('%05d', static::find()->max('id') + 1);
    }
    
    /**
     * 
     * @return float
     */
    public function getBalance()
    {
        $ins = $this->getAccountTransactions()
            ->andWhere(['type' => AccountTransaction::TYPE_IN, 'status' => AccountTransaction::STATUS_COMPLETE])
            ->sum('amount');
        $outs = $this->getAccountTransactions()
            ->andWhere(['type' => AccountTransaction::TYPE_OUT, 'status' => AccountTransaction::STATUS_COMPLETE])
            ->sum('amount');
        $balance = $ins - $outs;
        return $balance;
    }
    
    /**
     * 
     * @return float
     */
    public static function getCashboxIn($periodFrom = null, $periodTo = null)
    {
        $ins = AccountTransaction::find()
            ->andWhere(['type' => AccountTransaction::TYPE_IN, 'status' => AccountTransaction::STATUS_COMPLETE])
            ->andFilterWhere(['>=', 'uid_date', $periodFrom])
            ->andFilterWhere(['<=', 'uid_date', $periodTo])
            ->sum('amount');
        return $ins;
    }
    
    /**
     * 
     * @return float
     */
    public static function getCashboxOut($periodFrom = null, $periodTo = null)
    {
        $outs = AccountTransaction::find()
            ->andWhere(['type' => AccountTransaction::TYPE_OUT, 'status' => AccountTransaction::STATUS_COMPLETE])
            ->andWhere(['is', 'account_id', null])
            ->andFilterWhere(['>=', 'uid_date', $periodFrom])
            ->andFilterWhere(['<=', 'uid_date', $periodTo])
            ->sum('amount');
        return $outs;
    }
    
    /**
     * 
     * @return float
     */
    public static function getCashboxBalance()
    {
        $balance = static::getCashboxIn() - static::getCashboxOut();
        return $balance;
    }
    
    /**
     * 
     * @return float
     */
    public static function getBalanceTotal()
    {
//        $houseIds = Yii::$app->user->identity->getHouseIds();
//        
//        $insQuery = AccountTransaction::find()
//            ->andWhere(['type' => AccountTransaction::TYPE_IN, 'account_transaction.status' => AccountTransaction::STATUS_COMPLETE]);
//        if (Yii::$app->user->identity->role != UserAdmin::ROLE_ADMIN) {
//            $insQuery->joinWith(['account.flat'])
//                ->andWhere(['in', 'flat.house_id', $houseIds]);
//        }
//        $ins = $insQuery->sum('amount');
//        
//        $outsQuery = AccountTransaction::find()
//            ->andWhere(['type' => AccountTransaction::TYPE_OUT, 'account_transaction.status' => AccountTransaction::STATUS_COMPLETE]);
//        if (Yii::$app->user->identity->role != UserAdmin::ROLE_ADMIN) {
//            $insQuery->joinWith(['account.flat'])
//            ->andWhere(['in', 'flat.house_id', $houseIds]);
//        }
//        $outs = $outsQuery->sum('amount');
//        
//        $balance = $ins - $outs;
//        return $balance;
        
        $houseIds = Yii::$app->user->identity->getHouseIds();
        
        $accountsQuery = Account::find();
        if (Yii::$app->user->identity->role != UserAdmin::ROLE_ADMIN) {
            $accountsQuery->joinWith('flat')
            ->andWhere(['in', 'flat.house_id', $houseIds]);
        }
        $total = 0;
        foreach ($accountsQuery->each() as $account) {
            $balance = $account->getBalance();
            if ($balance > 0) {
                $total += $balance;
            }
        }
        
        return $total;
    }
    
    /**
     * 
     * @return float
     */
    public static function getBalanceDebtTotal()
    {
//        $houseIds = Yii::$app->user->identity->getHouseIds();
//        
//        $ins = AccountTransaction::find()
//            ->andWhere(['type' => AccountTransaction::TYPE_IN, 'account_transaction.status' => AccountTransaction::STATUS_WAITING])
//            ->joinWith(['account.flat'])
//            ->andWhere(['in', 'flat.house_id', $houseIds])
//            ->sum('amount');
//        $balanceDebt = $ins;
//        return $balanceDebt;
        
        $houseIds = Yii::$app->user->identity->getHouseIds();
        
        $accountsQuery = Account::find();
        if (Yii::$app->user->identity->role != UserAdmin::ROLE_ADMIN) {
            $accountsQuery->joinWith('flat')
            ->andWhere(['in', 'flat.house_id', $houseIds]);
        }
        $total = 0;
        foreach ($accountsQuery->each() as $account) {
            $balance = $account->getBalance();
            if ($balance < 0) {
                $total += $balance;
            }
        }
        
        return abs($total);
    }
    
    /**
     * @param \backend\models\AccountTransactionSearch $accountTransactionSearchModel
     * @return float
     */
    public static function getInsTotal($accountTransactionSearchModel = null)
    {
        $houseIds = Yii::$app->user->identity->getHouseIds();
        $insQuery = AccountTransaction::find()
            ->andWhere(['type' => AccountTransaction::TYPE_IN, 'account_transaction.status' => AccountTransaction::STATUS_COMPLETE]);
        if (Yii::$app->user->identity->role != UserAdmin::ROLE_ADMIN) {
            $insQuery->joinWith(['account.flat'])
                ->andWhere(['in', 'flat.house_id', $houseIds]);
        }
        if ($accountTransactionSearchModel->searchUidDateRange) {
            $dates = explode(' - ', $accountTransactionSearchModel->searchUidDateRange);
            $tsFrom = strtotime($dates[0]);
            $tsTo = strtotime($dates[1]);
            $insQuery->andFilterWhere(['>=', 'account_transaction.uid_date', date('Y-m-d', $tsFrom)]);
            $insQuery->andFilterWhere(['<=', 'account_transaction.uid_date', date('Y-m-d', $tsTo)]);
        }
        $insQuery->andFilterWhere(['account_transaction.type' => $accountTransactionSearchModel->type]);
        $insQuery->andFilterWhere(['account_transaction.account_id' => $accountTransactionSearchModel->account_id]);
        $insQuery->andFilterWhere(['account_transaction.status' => $accountTransactionSearchModel->status]);
        $insQuery->andFilterWhere(['account_transaction.transaction_purpose_id' => $accountTransactionSearchModel->transaction_purpose_id]);
        $ins = $insQuery->sum('amount');
        
        return $ins;
    }
    
    /**
     * @param \backend\models\AccountTransactionSearch $accountTransactionSearchModel
     * @return float
     */
    public static function getOutsTotal($accountTransactionSearchModel = null)
    {
        $houseIds = Yii::$app->user->identity->getHouseIds();
        $outsQuery = AccountTransaction::find()
            ->andWhere(['type' => AccountTransaction::TYPE_OUT, 'account_transaction.status' => AccountTransaction::STATUS_COMPLETE]);
        if (Yii::$app->user->identity->role != UserAdmin::ROLE_ADMIN) {
            $outsQuery->joinWith(['account.flat'])
            ->andWhere(['in', 'flat.house_id', $houseIds]);
        }
        if ($accountTransactionSearchModel->searchUidDateRange) {
            $dates = explode(' - ', $accountTransactionSearchModel->searchUidDateRange);
            $tsFrom = strtotime($dates[0]);
            $tsTo = strtotime($dates[1]);
            $outsQuery->andFilterWhere(['>=', 'account_transaction.uid_date', date('Y-m-d', $tsFrom)]);
            $outsQuery->andFilterWhere(['<=', 'account_transaction.uid_date', date('Y-m-d', $tsTo)]);
        }
        $outsQuery->andFilterWhere(['account_transaction.type' => $accountTransactionSearchModel->type]);
        $outsQuery->andFilterWhere(['account_transaction.account_id' => $accountTransactionSearchModel->account_id]);
        $outsQuery->andFilterWhere(['account_transaction.status' => $accountTransactionSearchModel->status]);
        $outsQuery->andFilterWhere(['account_transaction.transaction_purpose_id' => $accountTransactionSearchModel->transaction_purpose_id]);
        $outs = $outsQuery->sum('amount');
        return $outs;
    }
}
