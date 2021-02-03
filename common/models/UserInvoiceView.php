<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "user_invoice_view".
 *
 * @property int $id
 * @property int $user_id
 * @property int $invoice_id
 */
class UserInvoiceView extends \common\models\ZModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_invoice_view';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'invoice_id'], 'required'],
            [['user_id', 'invoice_id'], 'integer'],
            [['invoice_id'], 'exist', 'skipOnError' => true, 'targetClass' => Invoice::className(), 'targetAttribute' => ['invoice_id' => 'id']],
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
            'user_id' => Yii::t('model', 'Пользователь'),
            'invoice_id' => Yii::t('model', 'Квитанция'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoice()
    {
        return $this->hasOne(Invoice::className(), ['id' => 'invoice_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
