<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "transaction_purpose".
 *
 * @property int $id
 * @property string $name
 * @property string $type
 *
 * @property AccountTransaction[] $accountTransactions
 */
class TransactionPurpose extends \common\models\ZModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'transaction_purpose';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type'], 'string'],
            [['name'], 'string', 'max' => 255],
            ['name', 'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Название'),
            'type' => Yii::t('app', 'Приход/расход'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccountTransactions()
    {
        return $this->hasMany(AccountTransaction::className(), ['transaction_purpose_id' => 'id']);
    }
    
    /**
     * @param null $type
     * @return mixed|null
     */
    public function getTypeLabel($type = null)
    {
        $type = $type == null ? $this->type : $type;
        $options = AccountTransaction::getTypeOptions();
        return isset($options[$type]) ? $options[$type] : null;
    }
    
    /**
     * @param null $type
     * @return mixed|null
     */
    public function getTypeLabelHtml($type = null)
    {
        $type = $type == null ? $this->type : $type;
        $typeLabel = $this->getTypeLabel($type);
        $itemClass = 'text-default';
        if ($type == AccountTransaction::TYPE_IN) {
            $itemClass = 'text-green';
        } elseif ($type == AccountTransaction::TYPE_OUT) {
            $itemClass = 'text-red';
        }
        return '<span class="text '.$itemClass.'">'.$typeLabel.'</span>';
    }
    
    /**
     * @return array
     */
    public static function getOptions($type = null)
    {
        $query = static::find();
        if ($type) {
            $query->where(['type' => $type]);
        }
        $query->andWhere(['!=', 'id', 2]);
        return ArrayHelper::map($query->all(), 'id', 'name');
    }
    
}
