<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\MasterRequest;
use common\models\UserAdmin;

/**
 * MasterRequestSearch represents the model behind the search form of `common\models\MasterRequest`.
 */
class MasterRequestSearch extends MasterRequest
{
    public $searchCreated;
    public $searchDateRequest;
    public $searchFullname;
    public $searchPhone;
    public $searchFlat;
    public $searchUserId;
    public $searchDateRequestRange;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status', 'created_at', 'updated_at', 'flat_id', 'user_admin_id'], 'integer'],
            [['description', 'type'], 'safe'],
            [['searchCreated', 'searchFullname', 'searchPhone', 'searchFlat', 'searchUserId', 'searchDateRequest', 'searchDateRequestRange'], 'safe'],
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
        $query = MasterRequest::find();
        
        $houseIds = Yii::$app->user->identity->getHouseIds();
        $query->joinWith('flat')
            ->andWhere(['in', 'flat.house_id', $houseIds]);
        
        $role = Yii::$app->user->identity->role;
        if ($role == UserAdmin::ROLE_PLUMBER) {
            $query->andWhere(['in', 'type', [MasterRequest::TYPE_PLUMBER, MasterRequest::TYPE_DEFAULT]]);
        } elseif ($role == UserAdmin::ROLE_ELECTRICIAN) {
            $query->andWhere(['in', 'type', [MasterRequest::TYPE_ELECTRIC, MasterRequest::TYPE_DEFAULT]]);
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

        unset($dataProvider->sort->attributes['status']);
        unset($dataProvider->sort->attributes['flat_id']);
        unset($dataProvider->sort->attributes['description']);

        $dataProvider->setSort([
            'attributes' => array_merge($dataProvider->sort->attributes, [
                'searchCreated' => [
                    'asc' => ['master_request.created_at' => SORT_ASC],
                    'desc' => ['master_request.created_at' => SORT_DESC],
                    'label' => Yii::t('model', 'Добавлен'),
                    'default' => SORT_ASC
                ],
                'searchDateRequest' => [
                    'asc' => ['master_request.date_request' => SORT_ASC, 'master_request.time_request' => SORT_ASC],
                    'desc' => ['master_request.date_request' => SORT_DESC, 'master_request.time_request' => SORT_ASC],
                    'label' => Yii::t('model', 'Удобное время'),
                    'default' => SORT_ASC
                ],
            ]),
            'defaultOrder' => ['id' => SORT_DESC]
        ]);

        // grid filtering conditions
        $query->andFilterWhere([
            'master_request.id' => $this->id,
            'type' => $this->type,
            'master_request.status' => $this->status,
            'master_request.created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'flat_id' => $this->flat_id,
            'user_admin_id' => $this->user_admin_id,
        ]);

        $query->andFilterWhere(['like', 'description', $this->description]);

        if ($this->searchCreated) {
            $tsFrom = strtotime($this->searchCreated);
            $tsTo = $tsFrom + (24 * 60 * 60);
            $query->andFilterWhere(['>=', 'master_request.created_at', $tsFrom]);
            $query->andFilterWhere(['<', 'master_request.created_at', $tsTo]);
        }
        if ($this->searchFullname) {
            $query->joinWith(['flat.user.profile']);
            $query->andFilterWhere(['or',
                ['like', 'profile.firstname', $this->searchFullname],
                ['like', 'profile.lastname', $this->searchFullname],
                ['like', 'profile.middlename', $this->searchFullname],
            ]);
        }
        if ($this->searchUserId) {
            $query->joinWith(['flat']);
             $query->andFilterWhere(['flat.user_id' => $this->searchUserId]);
        }
        if ($this->searchPhone) {
            $query->joinWith(['flat.user.profile']);
            $query->andFilterWhere(['like', 'profile.phone', $this->searchPhone]);
        }
        if ($this->searchFlat) {
            $query->joinWith(['flat']);
            $query->andFilterWhere(['flat.flat' => $this->searchFlat]);
        }
        if ($this->searchDateRequest) {
            
        }
        if ($this->searchDateRequestRange) {
            $dates = explode(' - ', $this->searchDateRequestRange);
            $tsFrom = strtotime($dates[0]);
            $tsTo = strtotime($dates[1]);
            $query->andFilterWhere(['>=', 'master_request.date_request', date('Y-m-d', $tsFrom)]);
            $query->andFilterWhere(['<=', 'master_request.date_request', date('Y-m-d', $tsTo)]);
        }
        
        $dataProvider->pagination->pageSize = 50;

        return $dataProvider;
    }
}
