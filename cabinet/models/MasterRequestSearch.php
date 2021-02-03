<?php

namespace cabinet\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\MasterRequest;
use yii\helpers\ArrayHelper;

/**
 * MasterRequestSearch represents the model behind the search form of `common\models\MasterRequest`.
 */
class MasterRequestSearch extends MasterRequest
{
    public $searchCreated;
    public $searchFullname;
    public $searchUsername;
    public $searchFlat;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status', 'created_at', 'updated_at', 'flat_id'], 'integer'],
            [['description'], 'safe'],
            [['searchCreated', 'searchFullname', 'searchUsername', 'searchFlat'], 'safe'],
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
        $flatIds = ArrayHelper::getColumn(Yii::$app->user->identity->flats, 'id');
        $query = MasterRequest::find()->where(['in', 'flat_id', $flatIds]);

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
                    'asc' => ['created_at' => SORT_ASC],
                    'desc' => ['created_at' => SORT_DESC],
                    'label' => Yii::t('model', 'Добавлен'),
                    'default' => SORT_ASC
                ],
            ]),
            'defaultOrder' => ['created_at' => SORT_DESC]
        ]);

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'flat_id' => $this->flat_id,
        ]);

        $query->andFilterWhere(['like', 'description', $this->description]);

        if ($this->searchCreated) {
            $tsFrom = strtotime($this->searchCreated);
            $tsTo = $tsFrom + (24 * 60 * 60);
            $query->andFilterWhere(['>=', 'created_at', $tsFrom]);
            $query->andFilterWhere(['<', 'created_at', $tsTo]);
        }
        if ($this->searchFullname) {
            $query->joinWith(['flat.user.profile']);
            $query->andFilterWhere(['or',
                ['like', 'profile.firstname', $this->searchFullname],
                ['like', 'profile.lastname', $this->searchFullname],
                ['like', 'profile.middlename', $this->searchFullname],
            ]);
        }
        if ($this->searchUsername) {
            $query->joinWith(['flat.user']);
            $query->andFilterWhere(['like', 'user.username', $this->searchUsername]);
        }
        if ($this->searchFlat) {
            $query->joinWith(['flat']);
            $query->andFilterWhere(['flat.flat' => $this->searchFlat]);
        }

        return $dataProvider;
    }
}
