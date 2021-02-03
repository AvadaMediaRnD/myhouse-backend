<?php

namespace backend\models;

use common\models\UserAdmin;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * UserAdminSearch represents the model behind the search form of `common\models\UserAdmin`.
 */
class UserAdminSearch extends UserAdmin
{
    public $searchFullname;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['username', 'auth_key', 'password_hash', 'password_reset_token',
                'email', 'role', 'firstname', 'lastname', 'middlename', 'phone'], 'safe'],
            [['searchFullname'], 'safe'],
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
        $query = UserAdmin::find();

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

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
            'role' => $this->role,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['or', ['like', 'username', $this->email], ['like', 'email', $this->email]])
            ->andFilterWhere(['like', 'firstname', $this->firstname])
            ->andFilterWhere(['like', 'lastname', $this->lastname])
            ->andFilterWhere(['like', 'middlename', $this->middlename])
            ->andFilterWhere(['like', 'phone', $this->phone]);
        
        if ($this->searchFullname) {
            $query->andFilterWhere(['or',
                ['like', 'firstname', $this->searchFullname],
                ['like', 'lastname', $this->searchFullname],
                ['like', 'middlename', $this->searchFullname],
            ]);
        }
        
        $dataProvider->pagination->pageSize = 50;

        return $dataProvider;
    }
}
