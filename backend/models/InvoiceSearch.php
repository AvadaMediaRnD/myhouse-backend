<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Invoice;

/**
 * InvoiceSearch represents the model behind the search form of `common\models\Invoice`.
 */
class InvoiceSearch extends Invoice
{
    public $searchCreated;
    public $searchFlat;
    public $searchFullname;
    public $searchUserId;
    public $searchUidDate;
    public $searchUidDateRange;
    public $searchMonthYear;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status', 'is_checked', 'created_at', 'updated_at', 'flat_id', 'pay_company_id'], 'integer'],
            [['uid', 'uid_date', 'period_start', 'period_end'], 'safe'],
            [['searchCreated', 'searchFlat', 'searchFullname', 'searchUidDate', 'searchUidDateRange', 'searchUserId', 'searchMonthYear'], 'safe'],
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
        $query = Invoice::find();
        
        $houseIds = Yii::$app->user->identity->getHouseIds();
        $query->joinWith('flat')
            ->andWhere(['or', ['in', 'flat.house_id', $houseIds], ['is', 'invoice.flat_id', null]]);

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

        unset($dataProvider->sort->attributes['status']);
        unset($dataProvider->sort->attributes['is_checked']);

        $dataProvider->setSort([
            'attributes' => array_merge($dataProvider->sort->attributes, [
                'searchCreated' => [
                    'asc' => ['created_at' => SORT_ASC],
                    'desc' => ['created_at' => SORT_DESC],
                    'label' => Yii::t('model', 'Добавлен'),
                    'default' => SORT_ASC
                ],
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
            'period_start' => $this->period_start,
            'period_end' => $this->period_end,
            'invoice.status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'flat_id' => $this->flat_id,
            'pay_company_id' => $this->pay_company_id,
        ]);
        
        $query->andFilterWhere(['like', 'invoice.uid', $this->uid]);

        if ($this->searchCreated) {
            $tsFrom = strtotime($this->searchCreated);
            $tsTo = $tsFrom + (24 * 60 * 60);
            $query->andFilterWhere(['>=', 'created_at', $tsFrom]);
            $query->andFilterWhere(['<', 'created_at', $tsTo]);
        }
        if ($this->searchFlat) {
            $query->joinWith(['flat.house']);
            $query->andFilterWhere(['or', ['flat.flat' => $this->searchFlat], ['like', 'house.name', $this->searchFlat]]);
        }
        if ($this->searchFullname) {
            $query->joinWith(['flat.user.profile']);
            $query->andFilterWhere(['or',
                ['like', 'profile.firstname', $this->searchFullname],
                ['like', 'profile.lastname', $this->searchFullname],
                ['like', 'profile.middlename', $this->searchFullname],
                ['like', 'profile.phone', $this->searchFullname],
                ['like', 'user.email', $this->searchFullname],
            ]);
        }
        if ($this->searchUidDate) {
            $ts = strtotime($this->searchUidDate);
            $query->andFilterWhere(['uid_date' => date('Y-m-d', $ts)]);
        }
        if ($this->searchUidDateRange) {
            $dates = explode(' - ', $this->searchUidDateRange);
            $tsFrom = strtotime($dates[0]);
            $tsTo = strtotime($dates[1]);
            $query->andFilterWhere(['>=', 'invoice.uid_date', date('Y-m-d', $tsFrom)]);
            $query->andFilterWhere(['<=', 'invoice.uid_date', date('Y-m-d', $tsTo)]);
        }
        if ($this->searchUserId) {
            $query->joinWith(['flat.user']);
            $query->andFilterWhere(['user.id' => $this->searchUserId]);
        }
        if (strlen($this->is_checked)) {
            $query->andWhere(['is_checked' => $this->is_checked]);
        }
        if ($this->searchMonthYear) {
            $monthYearTs = strtotime('01.'.$this->searchMonthYear);
            $query->andFilterWhere(['>=', 'invoice.period_end', date('Y-m-01', $monthYearTs)]);
            $query->andFilterWhere(['<=', 'invoice.period_end', date('Y-m-t', $monthYearTs)]);
        }
        
        $dataProvider->pagination->pageSize = 50;

        return $dataProvider;
    }
}
