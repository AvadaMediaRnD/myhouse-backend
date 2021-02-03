<?php

namespace cabinet\models;

use common\models\Invoice;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\User;

/**
 * UserSearch represents the model behind the search form of `common\models\User`.
 */
class UserSearch extends User
{
    public $searchFullname;
    public $searchCreatedDate;
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
            [['searchFullname', 'searchCreatedDate', 'searchHouse', 'searchFlat', 'searchHasDebt', 'searchPhone'], 'safe'],
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

        $query->joinWith(['profile', 'flats.invoices']);

        // grid filtering conditions
        $query->andFilterWhere([
            'user.id' => $this->id,
            'user.uid' => $this->uid,
            'user.status' => $this->status,
            'user.created_at' => $this->created_at,
            'user.updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'auth_key', $this->auth_key])
            ->andFilterWhere(['like', 'password_hash', $this->password_hash])
            ->andFilterWhere(['like', 'password_reset_token', $this->password_reset_token]);

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

        unset($dataProvider->sort->attributes['status']);
        
        $dataProvider->setSort([
            'attributes' => array_merge($dataProvider->sort->attributes, [
                'searchFullname' => [
                    'asc' => ['profile.firstname' => SORT_ASC, 'profile.lastname' => SORT_ASC],
                    'desc' => ['profile.firstname' => SORT_DESC, 'profile.lastname' => SORT_DESC],
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

        return $dataProvider;
    }
}
