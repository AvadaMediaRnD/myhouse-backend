<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\AccountTransaction;

/**
 * AccountTransactionSearch represents the model behind the search form of `common\models\AccountTransaction`.
 */
class AccountTransactionSearch extends AccountTransaction
{
    public $searchFullname;
    public $searchUidDate;
    public $searchAccountUid;
    public $searchInvoice;
    public $searchUserId;
    public $searchUidDateRange;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status', 'account_id', 'invoice_id', 'transaction_purpose_id', 'invoice_service_id'], 'integer'],
            [['uid', 'uid_date', 'amount', 'type'], 'safe'],
            [['searchFullname', 'searchUidDate', 'searchAccountUid', 'searchInvoice', 'searchUserId', 'searchUidDateRange'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = AccountTransaction::find();
        $query->where(['or', 
            ['is', 'account_transaction.account_id', null], 
            ['and', ['is not', 'account_transaction.account_id', null], ['account_transaction.type' => static::TYPE_IN]]
        ]);
        
        $houseIds = Yii::$app->user->identity->getHouseIds();
        $query->joinWith('account.flat')
            ->andWhere(['or', ['in', 'flat.house_id', $houseIds], ['is', 'flat.house_id', null]]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        
        $dataProvider->setSort([
            'attributes' => array_merge($dataProvider->sort->attributes, [
                'searchUidDate' => [
                    'asc' => ['uid_date' => SORT_ASC],
                    'desc' => ['uid_date' => SORT_DESC],
                    'label' => Yii::t('model', 'Дата'),
                    'default' => SORT_ASC
                ],
            ]),
            'defaultOrder' => ['uid_date' => SORT_DESC, 'id' => SORT_DESC],
        ]);

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'account_transaction.status' => $this->status,
            'account_transaction.type' => $this->type,
            'account_id' => $this->account_id,
            'invoice_id' => $this->invoice_id,
            'transaction_purpose_id' => $this->transaction_purpose_id,
            'invoice_service_id' => $this->invoice_service_id,
        ]);

        $query->andFilterWhere(['like', 'account_transaction.uid', $this->uid]);
        
        if ($this->searchAccountUid) {
            $query->joinWith(['account']);
            $query->andFilterWhere(['account.uid' => $this->searchAccountUid]);
        }
        if ($this->searchFullname) {
            $query->joinWith(['account.flat.user.profile']);
            $query->andFilterWhere(['or',
                ['like', 'profile.firstname', $this->searchFullname],
                ['like', 'profile.lastname', $this->searchFullname],
                ['like', 'profile.middlename', $this->searchFullname],
                ['like', 'profile.phone', $this->searchFullname],
                ['like', 'user.email', $this->searchFullname],
            ]);
        }
        if ($this->searchUserId) {
            $query->joinWith(['account.flat']);
            $query->andFilterWhere(['flat.user_id' => $this->searchUserId]);
        }
        if ($this->searchUidDate) {
            $ts = strtotime($this->searchUidDate);
            $query->andFilterWhere(['account_transaction.uid_date' => date('Y-m-d', $ts)]);
        }
        if ($this->searchUidDateRange) {
            $dates = explode(' - ', $this->searchUidDateRange);
            $tsFrom = strtotime($dates[0]);
            $tsTo = strtotime($dates[1]);
            $query->andFilterWhere(['>=', 'account_transaction.uid_date', date('Y-m-d', $tsFrom)]);
            $query->andFilterWhere(['<=', 'account_transaction.uid_date', date('Y-m-d', $tsTo)]);
        }
        if ($this->searchInvoice) {
            $query->joinWith(['invoice']);
            $ts = strtotime($this->searchInvoice);
            $query->andFilterWhere(['or',
                ['like', 'invoice.uid', $this->searchInvoice],
                ['like', 'invoice.uid_date', date('Y-m-d', $ts)]
            ]);
        }
        
        $dataProvider->pagination->pageSize = 50;

        return $dataProvider;
    }
}
