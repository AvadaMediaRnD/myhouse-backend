<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "website_home_slide".
 *
 * @property int $id
 * @property string $image
 * @property int $sort
 */
class WebsiteHomeSlide extends \common\models\ZModel
{
    public $imageFile;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'website_home_slide';
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => \common\components\ChangeLogBehavior::className(),
                'labelObject' => 'Сайт - слайд на главной', 
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
            'image' => Yii::t('app', 'Изображение'),
            'title' => Yii::t('app', 'Заголовок'),
            'sort' => Yii::t('app', 'Порядок'),
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
