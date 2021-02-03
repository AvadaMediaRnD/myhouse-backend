<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\CounterData;

/**
 * CounterDataSearch represents the model behind the search form of `common\models\CounterData`.
 */
class CounterDataSearch extends CounterData
{
    public $searchHouse;
    public $searchSection;
    public $searchFloor;
    public $searchRiser;
    public $searchFlat;
    public $searchServiceUnit;
    public $searchUidDate;
    public $searchUidDateRange;
    public $searchUser;
    public $searchUidMonthYear;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'created_at', 'updated_at', 'status', 'flat_id', 'user_admin_id', 'service_id', 'counter_data_last_id'], 'integer'],
            [['uid', 'uid_date'], 'safe'],
            [['amount', 'amount_total'], 'number'],
            [['searchHouse', 'searchSection', 'searchFloor', 'searchRiser', 'searchFlat', 'searchServiceUnit', 'searchUidDate', 'searchUidDateRange', 'searchUser', 'searchUidMonthYear'], 'safe'],
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
        $query = CounterData::find();
        
        $houseIds = Yii::$app->user->identity->getHouseIds();
        $query->joinWith('flat')
            ->andWhere(['in', 'flat.house_id', $houseIds]);

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
                    'asc' => ['uid_date' => SORT_ASC, 'id' => SORT_ASC],
                    'desc' => ['uid_date' => SORT_DESC, 'id' => SORT_DESC],
                    'label' => Yii::t('model', 'Дата'),
                    'default' => SORT_DESC
                ],
                'searchUidMonthYear' => [
                    'asc' => ['uid_date' => SORT_ASC, 'id' => SORT_ASC],
                    'desc' => ['uid_date' => SORT_DESC, 'id' => SORT_DESC],
                    'label' => Yii::t('model', 'Дата'),
                    'default' => SORT_DESC
                ],
            ]),
            'defaultOrder' => ['searchUidDate' => SORT_DESC],
        ]);

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'amount' => $this->amount,
            'amount_total' => $this->amount_total,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'status' => $this->status,
            'flat_id' => $this->flat_id,
            'user_admin_id' => $this->user_admin_id,
            'service_id' => $this->service_id,
            'counter_data_last_id' => $this->counter_data_last_id,
        ]);

        $query->andFilterWhere(['like', 'uid', $this->uid])
            ->andFilterWhere(['like', 'uid_date', $this->uid_date]);
        
        if ($this->searchHouse) {
            $query->joinWith('flat');
            $query->andFilterWhere(['flat.house_id' => $this->searchHouse]);
        }
        if ($this->searchSection) {
            $query->joinWith('flat');
            $query->andFilterWhere(['flat.section_id' => $this->searchSection]);
        }
        if ($this->searchFloor) {
            $query->joinWith('flat');
            $query->andFilterWhere(['flat.floor_id' => $this->searchFloor]);
        }
        if ($this->searchRiser) {
            $query->joinWith('flat');
            $query->andFilterWhere(['flat.riser_id' => $this->searchRiser]);
        }
        if ($this->searchFlat) {
            $query->joinWith('flat');
            $query->andFilterWhere(['flat.flat' => $this->searchFlat]);
        }
        if ($this->searchUidDate) {
            $ts = strtotime($this->searchUidDate);
            $query->andFilterWhere(['uid_date' => date('Y-m-d', $ts)]);
        }
        if ($this->searchUidDateRange) {
            $dates = explode(' - ', $this->searchUidDateRange);
            $tsFrom = strtotime($dates[0]);
            $tsTo = strtotime($dates[1]);
            $query->andFilterWhere(['>=', 'uid_date', date('Y-m-d', $tsFrom)]);
            $query->andFilterWhere(['<=', 'uid_date', date('Y-m-d', $tsTo)]);
        }
        if ($this->searchUser) {
            $query->joinWith('flat.user');
            $query->andFilterWhere(['user.id' => $this->searchUser]);
        }
        if ($this->searchUidMonthYear) {
            $parts = explode('.', $this->searchUidMonthYear);
            if (count($parts) > 1) {
                // format with .
                if (mb_strlen($parts[count($parts) - 1]) == 4) {
                    $parts = array_reverse($parts);
                }
                $this->searchUidMonthYear = implode('-', $parts);
            }
            
            $dateFrom = $this->searchUidMonthYear . '-01';
            $dateTo = date('Y-m-d', strtotime($this->searchUidMonthYear . '-01 +1 month'));
            $query->andFilterWhere(['>=', 'uid_date', $dateFrom]);
            $query->andFilterWhere(['<', 'uid_date', $dateTo]);
        }
        
        $dataProvider->pagination->pageSize = 50;

        return $dataProvider;
    }
    
    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchCounters($params)
    {
        $query = CounterData::find()
            ->select(['flat_id', 'service_id', '`flat`.`flat` as `flatS`', 'MAX(`amount_total`) AS `amount_total`']);
        
        $houseIds = Yii::$app->user->identity->getHouseIds();
        $query->joinWith(['flat', 'service'])
            ->andWhere(['in', 'flat.house_id', $houseIds]);

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
                    'asc' => ['uid_date' => SORT_ASC, 'amount_total' => SORT_DESC],
                    'desc' => ['uid_date' => SORT_DESC, 'amount_total' => SORT_DESC],
                    'label' => Yii::t('model', 'Дата'),
                    'default' => SORT_DESC
                ],
                'searchFlat' => [
                    'asc' => ['flat' => SORT_ASC, 'uid_date' => SORT_DESC, 'amount_total' => SORT_DESC],
                    'desc' => ['flat' => SORT_DESC, 'uid_date' => SORT_DESC, 'amount_total' => SORT_DESC],
                    'label' => Yii::t('model', '№ квартиры'),
                    'default' => SORT_DESC
                ],
            ]),
            'defaultOrder' => ['uid_date' => SORT_DESC, 'amount_total' => SORT_DESC],
        ]);
        
        $query->andFilterWhere(['counter_data.service_id' => $this->service_id]);
        
        if ($this->searchHouse) {
            $query->andFilterWhere(['flat.house_id' => $this->searchHouse]);
        }
        if ($this->searchSection) {
            $query->andFilterWhere(['flat.section_id' => $this->searchSection]);
        }
        if ($this->searchFloor) {
            $query->andFilterWhere(['flat.floor_id' => $this->searchFloor]);
        }
        if ($this->searchRiser) {
            $query->andFilterWhere(['flat.riser_id' => $this->searchRiser]);
        }
        if ($this->searchFlat) {
            $query->andFilterWhere(['flat.flat' => $this->searchFlat]);
        }
        
        // $query->orderBy(['uid_date' => SORT_DESC, 'amount_total' => SORT_DESC]);
        $query->groupBy(['flat_id', 'service_id']);
        
        $dataProvider->pagination->pageSize = 50;

        return $dataProvider;
    }
}
