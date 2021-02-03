<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Flat;
use common\models\AccountTransaction;

/**
 * FlatSearch represents the model behind the search form of `common\models\Flat`.
 */
class FlatSearch extends Flat
{
    public $searchBalance;
    public $searchHasDebt;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'created_at', 'updated_at', 'house_id', 'user_id', 'section_id', 'riser_id', 'floor_id'], 'integer'],
            [['flat', 'square'], 'safe'],
            [['searchBalance', 'searchHasDebt'], 'safe'],
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
        $query = Flat::find();
        
        $query->joinWith(['account', 'house', 'section', 'floor', 'riser', 'user.profile']);
        
        $houseIds = Yii::$app->user->identity->getHouseIds();
        $query->andWhere(['in', 'flat.house_id', $houseIds]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        
        $dataProvider->sort->attributes['house_id'] = [
            'asc' => ['house.name' => SORT_ASC],
            'desc' => ['house.name' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['section_id'] = [
            'asc' => ['section.name' => SORT_ASC],
            'desc' => ['section.name' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['riser_id'] = [
            'asc' => ['riser.name' => SORT_ASC],
            'desc' => ['riser.name' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['floor_id'] = [
            'asc' => ['floor.name' => SORT_ASC],
            'desc' => ['floor.name' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['user_id'] = [
            'asc' => ['profile.lastname' => SORT_ASC],
            'desc' => ['profile.lastname' => SORT_DESC],
        ];

//        unset($dataProvider->sort->attributes['house_id']);
//        unset($dataProvider->sort->attributes['section_id']);
//        unset($dataProvider->sort->attributes['riser_id']);
//        unset($dataProvider->sort->attributes['floor_id']);
//        unset($dataProvider->sort->attributes['user_id']);

        // grid filtering conditions
        $query->andFilterWhere([
            'flat.id' => $this->id,
            'flat.created_at' => $this->created_at,
            'flat.updated_at' => $this->updated_at,
            'flat.house_id' => $this->house_id,
            'flat.user_id' => $this->user_id,
            'flat.section_id' => $this->section_id,
            'flat.riser_id' => $this->riser_id,
            'flat.floor_id' => $this->floor_id,
            'flat.square' => $this->square,
            'flat.flat' => $this->flat,
        ]);
        
        if ($this->user_id === 0 || $this->user_id === '0') {
            $query->where(['is', 'flat.user_id', null]);
        }
        
        if (strlen($this->searchHasDebt) > 0) {
            $accountsData = AccountTransaction::find()
                ->select(['account_id', new \yii\db\Expression("SUM(IF(`type` = 'in', `amount`, (`amount`*(-1)))) as `amount_total`")])
                ->groupBy('account_id')
                ->asArray()->all();
            $debtIds = [];
            foreach ($accountsData as $data) {
                if ($data['amount_total'] < 0 && $data['account_id']) {
                    $debtIds[] = $data['account_id'];
                }
            }
            
            if ($this->searchHasDebt == 1) {
                $query->andFilterWhere(['in', 'account.id', $debtIds]);
            } else {
                $query->andFilterWhere(['not in', 'account.id', $debtIds]);
            }
        }
        
        $dataProvider->pagination->pageSize = 50;

        return $dataProvider;
    }
}
