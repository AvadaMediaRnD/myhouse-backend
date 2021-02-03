<?php

namespace backend\models;

use common\models\UserAdminLog;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * UserAdminLogSearch represents the model behind the search form of `common\models\UserAdminLog`.
 */
class UserAdminLogSearch extends UserAdminLog
{
    public $searchDateFrom;
    public $searchDateTo;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'created_at', 'updated_at', 'user_admin_id'], 'integer'],
            [['event', 'object_class', 'object_id', 'old_attributes', 'message'], 'safe'],
            [['searchDateFrom', 'searchDateTo'], 'safe'],
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
        $query = UserAdminLog::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['created_at' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'user_admin_id' => $this->user_admin_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'event' => $this->event,
            'object_class' => $this->object_class,
            'object_id' => $this->object_id,
        ]);

        $query->andFilterWhere(['like', 'old_attributes', $this->old_attributes])
            ->andFilterWhere(['like', 'message', $this->message]);
        
        if ($this->searchDateFrom) {
            $ts = strtotime($this->searchDateFrom);
            $query->andFilterWhere(['>=', 'created_at', $ts]);
        }
        if ($this->searchDateTo) {
            $ts = strtotime($this->searchDateTo);
            $query->andFilterWhere(['<=', 'created_at', $ts]);
        }

        return $dataProvider;
    }
}
