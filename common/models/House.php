<?php

namespace common\models;

use Imagine\Image\ManipulatorInterface;
use Yii;
use yii\imagine\Image;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "house".
 *
 * @property int $id
 * @property string $name
 * @property string $address
 * @property string $image
 * @property int $created_at
 * @property int $updated_at
 * @property string $id_1c
 */
class House extends \common\models\ZModel
{
    public $imageFile;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'house';
    }

    /**
     * @inheritdoc
     */
    public function fields()
    {
        $fields = [
            'id', 'name', 'address', 'image1', 'image2', 'image3', 'image4', 'image5', 'created_at', 'updated_at', 'id_1c',
        ];
        return $fields;
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            \yii\behaviors\TimestampBehavior::className(),
            [
                'class' => \common\components\ChangeLogBehavior::className(),
                'labelObject' => 'Дом',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at'], 'integer'],
            [['name', 'address', 'image1', 'image2', 'image3', 'image4', 'image5'], 'string', 'max' => 255],
            [['id_1c'], 'string', 'max' => 40],
            [['imageFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg'],
            [['id_1c'], 'filter', 'filter' => function($value) { return strip_tags(trim($value)); }]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('model', 'ID'),
            'name' => Yii::t('model', 'Название'),
            'address' => Yii::t('model', 'Адрес'),
            'created_at' => Yii::t('model', 'Добавлен'),
            'updated_at' => Yii::t('model', 'Изменен'),
            'image1' => Yii::t('model', 'Изображение 1'),
            'image2' => Yii::t('model', 'Изображение 2'),
            'image3' => Yii::t('model', 'Изображение 3'),
            'image4' => Yii::t('model', 'Изображение 4'),
            'image5' => Yii::t('model', 'Изображение 5'),
            'imageFile' => Yii::t('model', 'Изображение'),
            'id_1c' => Yii::t('model', 'ID для 1С'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFlats()
    {
        return $this->hasMany(Flat::className(), ['house_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFloors()
    {
        return $this->hasMany(Floor::className(), ['house_id' => 'id'])
            ->orderBy(['sort' => SORT_ASC]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMessageAddresses()
    {
        return $this->hasMany(MessageAddress::className(), ['house_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRisers()
    {
        return $this->hasMany(Riser::className(), ['house_id' => 'id'])
            ->orderBy(['sort' => SORT_ASC]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSections()
    {
        return $this->hasMany(Section::className(), ['house_id' => 'id'])
            ->orderBy(['sort' => SORT_ASC]);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHouseUserAdmins()
    {
        return $this->hasMany(HouseUserAdmin::className(), ['house_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserAdmins()
    {
        return $this->hasMany(UserAdmin::className(), ['id' => 'user_admin_id'])
            ->via('houseUserAdmins');
    }
    
    /**
     * 
     * @return type
     */
    public function getImagePath1()
    {
        if ($this->image1 && file_exists(Yii::getAlias('@frontend/web' . $this->image1))) {
            return $this->image1;
        }
        return '/upload/placeholder.jpg';
    }
    
    /**
     * 
     * @return type
     */
    public function getImagePath2()
    {
        if ($this->image2 && file_exists(Yii::getAlias('@frontend/web' . $this->image2))) {
            return $this->image2;
        }
        return '/upload/placeholder.jpg';
    }
    
    /**
     * 
     * @return type
     */
    public function getImagePath3()
    {
        if ($this->image3 && file_exists(Yii::getAlias('@frontend/web' . $this->image3))) {
            return $this->image3;
        }
        return '/upload/placeholder.jpg';
    }
    
    /**
     * 
     * @return type
     */
    public function getImagePath4()
    {
        if ($this->image4 && file_exists(Yii::getAlias('@frontend/web' . $this->image4))) {
            return $this->image4;
        }
        return '/upload/placeholder.jpg';
    }
    
    /**
     * 
     * @return type
     */
    public function getImagePath5()
    {
        if ($this->image5 && file_exists(Yii::getAlias('@frontend/web' . $this->image5))) {
            return $this->image5;
        }
        return '/upload/placeholder.jpg';
    }
    
    /**
     * 
     * @return array
     */
    public static function getOptions()
    {
        return ArrayHelper::map(static::find()->andWhere(['in', 'id', Yii::$app->user->identity->getHouseIds()])->all(), 'id', 'name');
    }

}
