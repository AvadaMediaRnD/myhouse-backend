<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Account;
use common\models\AccountTransaction;

/**
 * AccountSearch represents the model behind the search form of `common\models\Account`.
 */
class AccountSearch extends Account
{
    public $searchFlat;
    public $searchHouse;
    public $searchSection;
    public $searchFullname;
    public $searchBalance;
    public $searchUserId;
    public $searchHasDebt;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status', 'flat_id'], 'integer'],
            [['uid'], 'safe'],
            [['searchFlat', 'searchHouse', 'searchSection', 'searchFullname', 'searchBalance', 'searchUserId', 'searchHasDebt'], 'safe'],
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
        $query = Account::find();
        
        $houseIds = Yii::$app->user->identity->getHouseIds();
        $query->joinWith('flat')
            ->andWhere(['or', ['in', 'flat.house_id', $houseIds], ['is', 'account.flat_id', null]]);

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

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'account.status' => $this->status,
            'account.flat_id' => $this->flat_id,
        ]);

        $query->andFilterWhere(['like', 'account.uid', $this->uid]);
        
        if ($this->searchHouse) {
            $query->joinWith('flat');
            $query->andFilterWhere(['flat.house_id' => $this->searchHouse]);
        }
        if ($this->searchSection) {
            $query->joinWith('flat');
            $query->andFilterWhere(['flat.section_id' => $this->searchSection]);
        }
        if ($this->searchFlat) {
            $query->joinWith('flat');
            $query->andFilterWhere(['flat.flat' => $this->searchFlat]);
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
        if ($this->searchUserId) {
            $query->joinWith(['flat']);
            $query->andFilterWhere(['flat.user_id' => $this->searchUserId]);
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
