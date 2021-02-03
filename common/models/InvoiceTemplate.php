<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "invoice_template".
 *
 * @property int $id
 * @property string $title
 * @property string $file
 * @property int $is_default
 */
class InvoiceTemplate extends \common\models\ZModel
{    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'invoice_template';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['is_default'], 'integer'],
            [['title', 'file'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'title' => Yii::t('app', 'Название'),
            'file' => Yii::t('app', 'Файл'),
            'is_default' => Yii::t('app', 'По-умолчанию'),
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert) {
        // verify only 1 is default
        if (static::find()->count() == 0) {
            $this->is_default = 1;
        }
        return parent::beforeSave($insert);
    }
    
    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes) {
        // verify only 1 is default
        if ($this->is_default) {
            static::updateAll(['is_default' => 0], ['!=', 'id', $this->id]);
        }
        return parent::afterSave($insert, $changedAttributes);
    }
    
    /**
     * {@inheritdoc}
     */
    public function afterDelete() {
        // verify only 1 is default
        if (static::find()->where(['is_default' => 1])->count() == 0) {
            $sql = "UPDATE `".static::tableName()."` SET `is_default` = 1 ORDER BY `id` DESC LIMIT 1";
            Yii::$app->db->createCommand($sql)->execute();
        }
        return parent::afterDelete();
    }
}
