<?php

namespace cabinet\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Message;
use common\models\Invoice;
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
        
        $user = Yii::$app->user->identity;
        $flatIds = ArrayHelper::getColumn($user->flats, 'id');
        $floorIds = ArrayHelper::getColumn($user->flats, 'floor_id');
        $sectionIds = ArrayHelper::getColumn($user->flats, 'section_id');
        $riserIds = ArrayHelper::getColumn($user->flats, 'riser_id');
        $houseIds = ArrayHelper::getColumn($user->flats, 'house_id');
        $hasDebt = Invoice::find()
            ->where(['in', 'invoice.flat_id', $flatIds])
            ->andWhere(['invoice.status' => Invoice::STATUS_UNPAID])
            ->exists();
        
        $query->joinWith('messageAddresses');
        
//        $query->where(['or', 
//            ['message_address.user_id' => $user->id],
//            ['and', ['in', 'message_address.house_id', $houseIds], ['is', 'message_address.section_id', null], ['is', 'message_address.riser_id', null], ['is', 'message_address.floor_id', null], ['is', 'message_address.flat_id', null], ['is', 'message_address.user_id', null]],
//            ['and', ['is', 'message_address.house_id', null], ['in', 'message_address.section_id', $sectionIds], ['is', 'message_address.riser_id', null], ['is', 'message_address.floor_id', null], ['is', 'message_address.flat_id', null], ['is', 'message_address.user_id', null]],
//            ['and', ['is', 'message_address.house_id', null], ['is', 'message_address.section_id', null], ['in', 'message_address.riser_id', $riserIds], ['is', 'message_address.floor_id', null], ['is', 'message_address.flat_id', null], ['is', 'message_address.user_id', null]],
//            ['and', ['is', 'message_address.house_id', null], ['is', 'message_address.section_id', null], ['is', 'message_address.riser_id', null], ['in', 'message_address.floor_id', $floorIds], ['is', 'message_address.flat_id', null], ['is', 'message_address.user_id', null]],
//            ['and', ['is', 'message_address.house_id', null], ['is', 'message_address.section_id', null], ['is', 'message_address.riser_id', null], ['is', 'message_address.floor_id', null], ['in', 'message_address.flat_id', $flatIds], ['is', 'message_address.user_id', null]],
//        ]);
        
//        $query->where(['or', 
//            ['message_address.user_id' => $user->id],
//            [
//                'and', 
//                ['or', ['in', 'message_address.house_id', $houseIds], ['is', 'message_address.house_id', null]], 
//                ['or', ['in', 'message_address.section_id', $sectionIds], ['is', 'message_address.section_id', null]], 
//                ['or', ['in', 'message_address.riser_id', $riserIds], ['is', 'message_address.riser_id', null]], 
//                ['or', ['in', 'message_address.floor_id', $floorIds], ['is', 'message_address.floor_id', null]], 
//                ['or', ['in', 'message_address.flat_id', $flatIds], ['is', 'message_address.flat_id', null]]
//            ],
//        ]);
        
        $query->where(['or', 
            ['message_address.user_id' => $user->id],
            [
                'or', 
                ['in', 'message_address.house_id', $houseIds],
                ['in', 'message_address.section_id', $sectionIds],
                ['in', 'message_address.riser_id', $riserIds],
                ['in', 'message_address.floor_id', $floorIds],
                ['in', 'message_address.flat_id', $flatIds],
            ],
        ]);
        
        if ($hasDebt) {
            $query->orWhere(['and', ['message_address.user_has_debt' => 1], ['or', ['is', 'message_address.user_id', null], ['message_address.user_id' => $user->id]]]);
        }
        $query->andWhere(['message.status' => Message::STATUS_SENT]);

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
                'searchCreated' => [
                    'asc' => ['created_at' => SORT_ASC],
                    'desc' => ['created_at' => SORT_DESC],
                    'label' => Yii::t('model', 'Добавлен'),
                    'default' => SORT_ASC
                ],
            ]),
            'defaultOrder' => ['created_at' => SORT_DESC, 'id' => SORT_DESC],
        ]);
        
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

        return $dataProvider;
    }
}
