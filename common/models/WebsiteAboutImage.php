<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "website_about_image".
 *
 * @property int $id
 * @property int $type
 * @property string $image
 * @property int $sort
 */
class WebsiteAboutImage extends \common\models\ZModel
{
    const TYPE_MAIN = 1;
    const TYPE_ADDITIONAL = 2;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'website_about_image';
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => \common\components\ChangeLogBehavior::className(),
                'labelObject' => 'Сайт - изображение о нас',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'sort'], 'integer'],
            [['image'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'type' => Yii::t('app', 'Type'),
            'image' => Yii::t('app', 'Image'),
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
