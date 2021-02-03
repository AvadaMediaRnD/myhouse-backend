<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "message_address".
 *
 * @property int $id
 * @property int $message_id
 * @property int $house_id
 * @property int $section_id
 * @property int $riser_id
 * @property int $floor_id
 * @property int $flat_id
 * @property int $user_id
 * @property int $user_has_debt
 */
class MessageAddress extends \common\models\ZModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'message_address';
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['message_id'], 'required'],
            [['message_id', 'house_id', 'section_id', 'riser_id', 'floor_id', 'flat_id', 'user_id', 'user_has_debt'], 'integer'],
            [['flat_id'], 'exist', 'skipOnError' => true, 'targetClass' => Flat::className(), 'targetAttribute' => ['flat_id' => 'id']],
            [['floor_id'], 'exist', 'skipOnError' => true, 'targetClass' => Floor::className(), 'targetAttribute' => ['floor_id' => 'id']],
            [['house_id'], 'exist', 'skipOnError' => true, 'targetClass' => House::className(), 'targetAttribute' => ['house_id' => 'id']],
            [['message_id'], 'exist', 'skipOnError' => true, 'targetClass' => Message::className(), 'targetAttribute' => ['message_id' => 'id']],
            [['riser_id'], 'exist', 'skipOnError' => true, 'targetClass' => Riser::className(), 'targetAttribute' => ['riser_id' => 'id']],
            [['section_id'], 'exist', 'skipOnError' => true, 'targetClass' => Section::className(), 'targetAttribute' => ['section_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('model', 'ID'),
            'message_id' => Yii::t('model', 'Сообщение'),
            'house_id' => Yii::t('model', 'ЖК'),
            'section_id' => Yii::t('model', 'Секция'),
            'riser_id' => Yii::t('model', 'Стояк'),
            'floor_id' => Yii::t('model', 'Этаж'),
            'flat_id' => Yii::t('model', 'Квартира'),
            'user_id' => Yii::t('model', 'Владелец квартир'),
            'user_has_debt' => Yii::t('model', 'Владельцам с задолженностями'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFlat()
    {
        return $this->hasOne(Flat::className(), ['id' => 'flat_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFloor()
    {
        return $this->hasOne(Floor::className(), ['id' => 'floor_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHouse()
    {
        return $this->hasOne(House::className(), ['id' => 'house_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMessage()
    {
        return $this->hasOne(Message::className(), ['id' => 'message_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRiser()
    {
        return $this->hasOne(Riser::className(), ['id' => 'riser_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSection()
    {
        return $this->hasOne(Section::className(), ['id' => 'section_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
    
    /**
     * @return string
     */
    public function getAddressLabel()
    {
        $addressData = [];
        $userData = null;
        if ($this->house_id && $this->house) {
            $addressData[] = $this->house->name;
        }
        if ($this->section_id && $this->section) {
            $addressData[] = $this->section->name;
        }
        if ($this->riser_id && $this->riser) {
            $addressData[] = $this->riser->name;
        }
        if ($this->floor_id && $this->floor) {
            $addressData[] = $this->floor->name;
        }
        if ($this->flat_id && $this->flat) {
            $addressData[] = 'кв.' . $this->flat->flat;
        }
        if ($this->user_id && $this->user) {
            $userData = $this->user->getFullname();
        }

        $addressStrings = [];
        if ($addressData) {
            $addressStrings[] = implode(', ', $addressData);
        }
        if ($userData) {
            $addressStrings[] = $userData;
        }

        return implode('<br/>', $addressStrings);
    }

    /**
     * @return array|User[]
     */
    public function getUserRecipients()
    {
        $userAdminFrom = $this->message->userAdminFrom;
        $houseIds = $userAdminFrom ? $userAdminFrom->getHouseIds() : null;
        
        if ($this->user_id) {
            return $this->user ? [$this->user] : [];
        }

        $usersQuery = User::find();
        $usersQuery->joinWith('flats.invoices');
        
        if ($houseIds !== null) {
            $usersQuery->andWhere(['in', 'flat.house_id', $houseIds]);
        }

        if ($this->flat_id) {
            $usersQuery->andWhere(['flat.id' => $this->flat_id]);
            return $usersQuery->all();
        }

        if ($this->user_has_debt) {
            $usersQuery->andWhere(['invoice.status' => Invoice::STATUS_UNPAID]);


        }

        if ($this->house_id) {
            $usersQuery->andWhere(['flat.house_id' => $this->house_id]);
        }

        if ($this->section_id) {
            $usersQuery->andWhere(['flat.section_id' => $this->section_id]);
        }

        if ($this->riser_id) {
            $usersQuery->andWhere(['flat.riser_id' => $this->riser_id]);
        }

        if ($this->floor_id) {
            $usersQuery->andWhere(['flat.floor_id' => $this->floor_id]);
        }

        return $usersQuery->all();
    }
}
