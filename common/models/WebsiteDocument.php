<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "website_document".
 *
 * @property int $id
 * @property string $file
 * @property string $title
 * @property int $sort
 */
class WebsiteDocument extends \common\models\ZModel
{
    public $fileFile;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'website_document';
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => \common\components\ChangeLogBehavior::className(),
                'labelObject' => 'Сайт - документ',
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
            [['file', 'title'], 'string', 'max' => 255],
            [['fileFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, pdf'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'file' => Yii::t('app', 'Фото или PDF'),
            'title' => Yii::t('app', 'Название'),
            'sort' => Yii::t('app', 'Sort'),
        ];
    }
    
    /**
     * 
     * @return string
     */
    public function getImagePath()
    {
        if ($this->file && file_exists(Yii::getAlias('@frontend/web' . $this->file))) {
            return $this->file;
        }
        return '/upload/placeholder.jpg';
    }
    
    /**
     * 
     * @return string|boolean
     */
    public function getFileExtension()
    {
        if ($this->file && file_exists(Yii::getAlias('@frontend/web' . $this->file))) {
            $pathParts = pathinfo(Yii::getAlias('@frontend/web' . $this->file));
            return strtolower($pathParts['extension']);
        }
        return false;
    }
    
    /**
     * 
     * @return string|boolean
     */
    public function getFileName()
    {
        if ($this->file && file_exists(Yii::getAlias('@frontend/web' . $this->file))) {
            $pathParts = pathinfo(Yii::getAlias('@frontend/web' . $this->file));
            return strtolower($pathParts['base_name']);
        }
        return false;
    }
}
