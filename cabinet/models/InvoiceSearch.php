<?php

namespace cabinet\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Invoice;
use yii\helpers\ArrayHelper;

/**
 * InvoiceSearch represents the model behind the search form of `common\models\Invoice`.
 */
class InvoiceSearch extends Invoice
{
    public $searchCreated;
    public $searchFlat;
    public $searchFullname;
    public $searchUidDate;
    public $searchPayTo;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status', 'created_at', 'updated_at', 'flat_id'], 'integer'],
            [['uid', 'uid_date', 'period_start', 'period_end'], 'safe'],
            [['searchCreated', 'searchFlat', 'searchFullname', 'searchUidDate', 'searchPayTo'], 'safe'],
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
        
        $user = Yii::$app->user->identity;
        $flatIds = ArrayHelper::getColumn($user->flats, 'id');
        $query->where(['in', 'invoice.flat_id', $flatIds]);

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
                    'label' => Yii::t('model', 'Добавлен'),
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
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'flat_id' => $this->flat_id,
        ]);

        if ($this->searchCreated) {
            $tsFrom = strtotime($this->searchCreated);
            $tsTo = $tsFrom + (24 * 60 * 60);
            $query->andFilterWhere(['>=', 'created_at', $tsFrom]);
            $query->andFilterWhere(['<', 'created_at', $tsTo]);
        }
        if ($this->searchFlat) {
            $query->joinWith(['flat']);
            $query->andFilterWhere(['flat.flat' => $this->searchFlat]);
        }
        if ($this->searchFullname) {
            $query->joinWith(['flat.user.profile']);
            $query->andFilterWhere(['or',
                ['like', 'profile.firstname', $this->searchFullname],
                ['like', 'profile.lastname', $this->searchFullname],
                ['like', 'profile.middlename', $this->searchFullname],
                ['like', 'user.username', $this->searchFullname],
            ]);
        }
        if ($this->searchUidDate) {
            $date = date('Y-m-d', strtotime($this->searchUidDate));
            $query->andFilterWhere(['uid_date' => $date]);
        }
        if ($this->searchPayTo) {
//            $query->joinWith(['flat.user.profile']);
//            $query->andFilterWhere(['or',
//                ['like', 'profile.firstname', $this->searchFullname],
//                ['like', 'profile.lastname', $this->searchFullname],
//                ['like', 'profile.middlename', $this->searchFullname],
//                ['like', 'user.username', $this->searchFullname],
//            ]);
        }

        return $dataProvider;
    }
}
