<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "website_tariff".
 *
 * @property int $id
 * @property string $image
 * @property string $title
 * @property int $sort
 */
class WebsiteTariff extends \common\models\ZModel
{
    public $imageFile;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'website_tariff';
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => \common\components\ChangeLogBehavior::className(),
                'labelObject' => 'Сайт - тариф', 
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sort'], 'integer'],
            [['image', 'title'], 'string', 'max' => 255],
            [['imageFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'image' => Yii::t('app', 'Image'),
            'title' => Yii::t('app', 'Title'),
            'sort' => Yii::t('app', 'Sort'),
        ];
    }
    
    /**
     * 
     * @return string
     */
    public function getImagePath()
    {
        if ($this->image && file_exists(Yii::getAlias('@frontend/web' . $this->image))) {
            return $this->image;
        }
        return '/upload/placeholder.jpg';
    }
}
