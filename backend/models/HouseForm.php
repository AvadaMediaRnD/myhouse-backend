<?php
namespace backend\models;

use common\models\House;
use common\models\Section;
use common\models\Floor;
use common\models\Riser;
use common\models\HouseUserAdmin;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

/**
 * House form
 */
class HouseForm extends Model
{
    public $id;
    public $name;
    public $address;
    public $image1;
    public $image2;
    public $image3;
    public $image4;
    public $image5;

    private $_model = null;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['id', 'integer'],
            [['name', 'address'], 'string', 'max' => 255],
            [['image1', 'image2', 'image3', 'image4', 'image5'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg'],
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
            'image1' => Yii::t('model', 'Изображение #1. Размер: (522x350)'),
            'image2' => Yii::t('model', 'Изображение #2. Размер: (248x160)'),
            'image3' => Yii::t('model', 'Изображение #3. Размер: (248x160)'),
            'image4' => Yii::t('model', 'Изображение #4. Размер: (248x160)'),
            'image5' => Yii::t('model', 'Изображение #5. Размер: (248x160)'),
        ];
    }

    /**
     * Save House.
     *
     * @return House|null the saved model or null if saving fails
     */
    public function process()
    {
        if ($this->validate()) {
            $model = House::findOne($this->id);
            if (!$model) {
                $model = new House();
            }
            $model->name = $this->name;
            $model->address = $this->address;
            

            if ($model->save()) {
                // TODO
                // image
                $attributes = ['image1', 'image2', 'image3', 'image4', 'image5'];
                $saveAgain = 0;
                foreach ($attributes as $attribute) {
                    $file = UploadedFile::getInstance($this, $attribute);
                    if ($file) {
                        $path = '/upload/House/' . $model->id . '/'.$attribute.'.' . $file->extension; 
                        $pathFull = Yii::getAlias('@frontend/web' . $path);
                        $dir = dirname($pathFull);
                        if (!is_dir($dir)) {
                            mkdir($dir, 0777, true);
                        }
                        if ($file->saveAs($pathFull)) {
                            $model->$attribute = $path;
                            $saveAgain++;
                            Yii::$app->glide->getServer()->deleteCache($path);
                        }
                    }
                }
                if ($saveAgain) {
                    $model->save(false);
                }
                
                $this->_model = $model;
                
                return $model;
            }
        }

        return null;
    }

    /**
     * @param House $model
     * @return HouseForm
     */
    public static function loadFromModel($model) {
        $form = new static();
        $form->_model = $model;
        if ($model) {
            $form->id = $model->id;
            $form->name = $model->name;
            $form->address = $model->address;
            $form->image1 = $model->image1;
            $form->image2 = $model->image2;
            $form->image3 = $model->image3;
            $form->image4 = $model->image4;
            $form->image5 = $model->image5;
        }
        return $form;
    }
    
    /**
     * 
     * @return type
     */
    public function getImagePath1()
    {
        if (!$this->_model) {
            return Yii::getAlias('/upload/placeholder.jpg');
        }
        return $this->_model->getImagePath1();
    }
    
    /**
     * 
     * @return type
     */
    public function getImagePath2()
    {
        if (!$this->_model) {
            return Yii::getAlias('/upload/placeholder.jpg');
        }
        return $this->_model->getImagePath2();
    }
    
    /**
     * 
     * @return type
     */
    public function getImagePath3()
    {
        if (!$this->_model) {
            return Yii::getAlias('/upload/placeholder.jpg');
        }
        return $this->_model->getImagePath3();
    }
    
    /**
     * 
     * @return type
     */
    public function getImagePath4()
    {
        if (!$this->_model) {
            return Yii::getAlias('/upload/placeholder.jpg');
        }
        return $this->_model->getImagePath4();
    }
    
    /**
     * 
     * @return type
     */
    public function getImagePath5()
    {
        if (!$this->_model) {
            return Yii::getAlias('/upload/placeholder.jpg');
        }
        return $this->_model->getImagePath5();
    }
}
