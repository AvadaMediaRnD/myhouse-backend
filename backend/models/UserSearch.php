<?php

namespace backend\models;

use common\models\Invoice;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\User;
use common\models\UserAdmin;

/**
 * UserSearch represents the model behind the search form of `common\models\User`.
 */
class UserSearch extends User
{
    public $searchFullname;
    public $searchCreatedDate;
    public $searchCreatedDateRange;
    public $searchHouse;
    public $searchFlat;
    public $searchHasDebt;
    public $searchPhone;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['email', 'uid', 'auth_key', 'password_hash', 'password_reset_token'], 'safe'],
            [['searchFullname', 'searchCreatedDate', 'searchCreatedDateRange', 'searchHouse', 'searchFlat', 'searchHasDebt', 'searchPhone'], 'safe'],
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
        $query = User::find();
        
        $query->joinWith('flats');
        if (Yii::$app->user->identity->role != UserAdmin::ROLE_ADMIN) {
            $query->andWhere(['in', 'flat.house_id', Yii::$app->user->identity->getHouseIds()]);
        }

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
                'searchFullname' => [
                    'asc' => ['CONCAT(profile.firstname,profile.lastname)' => SORT_ASC],
                    'desc' => ['CONCAT(profile.firstname,profile.lastname)' => SORT_DESC],
                    'label' => Yii::t('model', 'ФИО'),
                    'default' => SORT_ASC
                ],
                'searchCreatedDate' => [
                    'asc' => ['user.created_at' => SORT_ASC],
                    'desc' => ['user.created_at' => SORT_DESC],
                    'label' => Yii::t('model', 'Добавлен'),
                    'default' => SORT_ASC
                ],
            ]),
            'defaultOrder' => ['created_at' => SORT_DESC],
        ]);

        $query->joinWith(['profile', 'flats.invoices']);

        // grid filtering conditions
        $query->andFilterWhere([
            'user.id' => $this->id,
            'user.status' => $this->status,
            'user.created_at' => $this->created_at,
            'user.updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'user.uid', $this->uid]);

        if ($this->searchFullname) {
            $query->andFilterWhere(['or',
                ['like', 'profile.firstname', $this->searchFullname],
                ['like', 'profile.lastname', $this->searchFullname],
                ['like', 'profile.middlename', $this->searchFullname]
            ]);
        }
        if ($this->searchCreatedDate) {
            $tsFrom = strtotime($this->searchCreatedDate);
            $tsTo = $tsFrom + (24 * 60 * 60);
            $query->andFilterWhere(['>=', 'user.created_at', $tsFrom]);
            $query->andFilterWhere(['<', 'user.created_at', $tsTo]);
        }
        if ($this->searchCreatedDateRange) {
            $dates = explode(' - ', $this->searchCreatedDateRange);
            $tsFrom = strtotime($dates[0]);
            $tsTo = strtotime($dates[1]) + (24 * 60 * 60);
            $query->andFilterWhere(['>=', 'user.created_at', $tsFrom]);
            $query->andFilterWhere(['<', 'user.created_at', $tsTo]);
        }
        if ($this->searchHouse) {
            $query->andFilterWhere(['flat.house_id' => $this->searchHouse]);
        }
        if ($this->searchFlat) {
            $query->andFilterWhere(['like', 'flat.flat', $this->searchFlat]);
        }
        if ($this->searchHasDebt) {
            $query->andFilterWhere(['invoice.status' => Invoice::STATUS_UNPAID]);
        }
        if ($this->searchPhone) {
            $query->andFilterWhere(['like', 'profile.phone', $this->searchPhone]);
        }
        
        $query->groupBy('user.id');

        unset($dataProvider->sort->attributes['status']);
        
        $dataProvider->pagination->pageSize = 50;

        return $dataProvider;
    }
}
