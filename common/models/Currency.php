<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "currency".
 *
 * @property int $id
 * @property string $name
 * @property string $code
 * @property int $is_default
 * @property float $course
 *
 * @property AccountTransaction[] $accountTransactions
 */
class Currency extends \common\models\ZModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'currency';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'code'], 'string', 'max' => 255],
            ['course', 'number'],
            ['is_default', 'boolean'],
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
            'code' => Yii::t('app', 'Код'),
            'is_default' => Yii::t('app', 'По-умолчанию'),
            'course' => Yii::t('app', 'Курс'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccountTransactions()
    {
        return $this->hasMany(AccountTransaction::className(), ['currency_id' => 'id']);
    }
    
    /**
     * default model
     * @return Currency
     */
    public function findDefault()
    {
        return static::find()->where(['is_default' => 1])->one();
    }
}
