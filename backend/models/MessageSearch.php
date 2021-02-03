<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Message;
use common\models\UserAdmin;
use common\models\Section;
use common\models\Riser;
use common\models\Floor;
use common\models\Flat;
use common\models\User;
use yii\helpers\ArrayHelper;

/**
 * MessageSearch represents the model behind the search form of `common\models\Message`.
 */
class MessageSearch extends Message
{
    public $searchCreated;
    public $searchMessageAddress;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['name', 'description', 'type'], 'safe'],
            [['searchCreated', 'searchMessageAddress'], 'safe'],
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
        $query = Message::find();
        
        if (Yii::$app->user->identity->role != UserAdmin::ROLE_ADMIN) {
            $houseIds = Yii::$app->user->identity->getHouseIds();
            $sectionIds = ArrayHelper::getColumn(Section::find()->where(['in', 'house_id', $houseIds])->all(), 'id');
            $riserIds = ArrayHelper::getColumn(Riser::find()->where(['in', 'house_id', $houseIds])->all(), 'id');
            $floorIds = ArrayHelper::getColumn(Floor::find()->where(['in', 'house_id', $houseIds])->all(), 'id');
            $flatIds = ArrayHelper::getColumn(Flat::find()->where(['in', 'house_id', $houseIds])->all(), 'id');
            $userIds = ArrayHelper::getColumn(User::find()->joinWith('flats')->where(['in', 'flat.house_id', $houseIds])->all(), 'id');
            $query->joinWith('messageAddresses')
                ->andWhere(['or',
                    ['in', 'message_address.house_id', $houseIds],
                    ['in', 'message_address.section_id', $sectionIds],
                    ['in', 'message_address.riser_id', $riserIds],
                    ['in', 'message_address.floor_id', $floorIds],
                    ['in', 'message_address.flat_id', $flatIds],
                    ['in', 'message_address.user_id', $userIds],
                ]);
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
        unset($dataProvider->sort->attributes['type']);

        $dataProvider->setSort([
            'attributes' => array_merge($dataProvider->sort->attributes, [
                'searchCreated' => [
                    'asc' => ['created_at' => SORT_ASC],
                    'desc' => ['created_at' => SORT_DESC],
                    'label' => Yii::t('model', 'Добавлен'),
                    'default' => SORT_ASC
                ],
            ]),
            'defaultOrder' => ['id' => SORT_DESC],
        ]);

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'type', $this->type]);

        if ($this->searchCreated) {
            $tsFrom = strtotime($this->searchCreated);
            $tsTo = $tsFrom + (24 * 60 * 60);
            $query->andFilterWhere(['>=', 'created_at', $tsFrom]);
            $query->andFilterWhere(['<', 'created_at', $tsTo]);
        }
        if ($this->searchMessageAddress) {
            $query->joinWith([
                'messageAddresses.house',
                'messageAddresses.flat',
                'messageAddresses.floor',
                'messageAddresses.riser',
                'messageAddresses.section',
                'messageAddresses.user.profile'
            ]);
            $query->andFilterWhere(['or',
                ['like', 'house.name', $this->searchMessageAddress],
                ['like', 'flat.flat', $this->searchMessageAddress],
                ['like', 'floor.name', $this->searchMessageAddress],
                ['like', 'riser.name', $this->searchMessageAddress],
                ['like', 'section.name', $this->searchMessageAddress],
                ['like', 'profile.firstname', $this->searchMessageAddress],
                ['like', 'profile.lastname', $this->searchMessageAddress],
                ['like', 'profile.middlename', $this->searchMessageAddress],
                ['like', 'user.username', $this->searchMessageAddress],
            ]);
        }
        
        // search field
        $search = $params['search'];
        if ($search) {
            $query->joinWith([
                'messageAddresses.user.profile'
            ]);
            $query->andFilterWhere(['or',
                ['like', 'profile.firstname', $search],
                ['like', 'profile.lastname', $search],
                ['like', 'profile.middlename', $search],
                ['like', 'user.email', $search],
                ['like', 'message.name', $search],
            ]);
        }
        
        $dataProvider->pagination->pageSize = 50;

        return $dataProvider;
    }
}
