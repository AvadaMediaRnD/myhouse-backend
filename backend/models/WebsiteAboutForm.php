<?php
namespace backend\models;

use common\models\Website;
use common\models\WebsiteAboutImage;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;
use yii\helpers\Html;

/**
 * Website About form
 */
class WebsiteAboutForm extends Model
{
    public $aboutTitle;
    public $aboutDescription;
    public $aboutTitle2;
    public $aboutDescription2;
    public $aboutImage;
    public $imageFile;
    public $aboutImageMainFiles;
    public $aboutImageAddFiles;
    public $aboutMetaTitle;
    public $aboutMetaDescription;
    public $aboutMetaKeywords;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['aboutTitle', 'aboutDescription',
                'aboutTitle2', 'aboutDescription2', 'aboutImage',
                'aboutMetaTitle', 'aboutMetaDescription', 'aboutMetaKeywords'], 'string'],
            [['imageFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg'],
            [['aboutImageMainFiles', 'aboutImageAddFiles'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg', 'maxFiles' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'aboutTitle' => Yii::t('model', 'Заголовок'),
            'aboutDescription' => Yii::t('model', 'Краткий текст'),
            'aboutTitle2' => Yii::t('model', 'Заголовок'),
            'aboutDescription2' => Yii::t('model', 'Краткий текст'),
            'aboutImage' => Yii::t('model', 'Фото'),
            'imageFile' => Yii::t('model', 'Фото'),
            'aboutImageMainFiles' => Yii::t('model', 'Фотогалерея'),
            'aboutImageAddFiles' => Yii::t('model', 'Дополнительная фотогалерея'),
            'aboutMetaTitle' => Yii::t('model', 'Title'),
            'aboutMetaDescription' => Yii::t('model', 'Description'),
            'aboutMetaKeywords' => Yii::t('model', 'Keywords'),
        ];
    }

    /**
     * Save Data.
     *
     * @return bool
     */
    public function process()
    {
        if ($this->validate()) {
            $model = Website::getByParam(Website::PARAM_ABOUT_TITLE);
            $model->content = Html::encode($this->aboutTitle);
            $model->save();
            $model = Website::getByParam(Website::PARAM_ABOUT_DESCRIPTION);
            $model->content = Html::encode($this->aboutDescription);
            $model->save();
            $model = Website::getByParam(Website::PARAM_ABOUT_TITLE_2);
            $model->content = Html::encode($this->aboutTitle2);
            $model->save();
            $model = Website::getByParam(Website::PARAM_ABOUT_DESCRIPTION_2);
            $model->content = Html::encode($this->aboutDescription2);
            $model->save();
            $model = Website::getByParam(Website::PARAM_ABOUT_META_TITLE);
            $model->content = Html::encode($this->aboutMetaTitle);
            $model->save();
            $model = Website::getByParam(Website::PARAM_ABOUT_META_DESCRIPTION);
            $model->content = Html::encode($this->aboutMetaDescription);
            $model->save();
            $model = Website::getByParam(Website::PARAM_ABOUT_META_KEYWORDS);
            $model->content = Html::encode($this->aboutMetaKeywords);
            $model->save();
            
            $model = Website::getByParam(Website::PARAM_ABOUT_IMAGE);
            $file = UploadedFile::getInstance($this, 'imageFile');
            if ($file) {
                $path = '/upload/Website/' . Website::PARAM_ABOUT_IMAGE.'.' . $file->extension; 
                $pathFull = Yii::getAlias('@frontend/web' . $path);
                $dir = dirname($pathFull);
                if (!is_dir($dir)) {
                    mkdir($dir, 0777, true);
                }
                if ($file->saveAs($pathFull)) {
                    $model->content = $path;
                    $model->save();
                    Yii::$app->glide->getServer()->deleteCache($path);
                }
            }
            
            $files = UploadedFile::getInstances($this, 'aboutImageMainFiles');
            if ($files) {
                foreach ($files as $file) {
                    $model = new WebsiteAboutImage();
                    $model->type = WebsiteAboutImage::TYPE_MAIN;
                    $model->save();
                    
                    $path = '/upload/WebsiteAboutImage/' . $model->id . '/image.' . $file->extension; 
                    $pathFull = Yii::getAlias('@frontend/web' . $path);
                    $dir = dirname($pathFull);
                    if (!is_dir($dir)) {
                        mkdir($dir, 0777, true);
                    }
                    if ($file->saveAs($pathFull)) {
                        $model->image = $path;
                        $model->save(false);
                        Yii::$app->glide->getServer()->deleteCache($path);
                    }
                }
            }
            
            $files = UploadedFile::getInstances($this, 'aboutImageAddFiles');
            if ($files) {
                foreach ($files as $file) {
                    $model = new WebsiteAboutImage();
                    $model->type = WebsiteAboutImage::TYPE_ADDITIONAL;
                    $model->save();
                    
                    $path = '/upload/WebsiteAboutImage/' . $model->id . '/image.' . $file->extension; 
                    $pathFull = Yii::getAlias('@frontend/web' . $path);
                    $dir = dirname($pathFull);
                    if (!is_dir($dir)) {
                        mkdir($dir, 0777, true);
                    }
                    if ($file->saveAs($pathFull)) {
                        $model->image = $path;
                        $model->save(false);
                        Yii::$app->glide->getServer()->deleteCache($path);
                    }
                }
            }
            
            return true;
        }

        return false;
    }

    /**
     * @return WebsiteAboutForm
     */
    public static function loadFromDb() {
        $form = new static();
        
        $form->aboutTitle = Html::decode(Website::getParamContent(Website::PARAM_ABOUT_TITLE));
        $form->aboutDescription = Html::decode(Website::getParamContent(Website::PARAM_ABOUT_DESCRIPTION));
        $form->aboutTitle2 = Html::decode(Website::getParamContent(Website::PARAM_ABOUT_TITLE_2));
        $form->aboutDescription2 = Html::decode(Website::getParamContent(Website::PARAM_ABOUT_DESCRIPTION_2));
        $form->aboutImage = Html::decode(Website::getParamContent(Website::PARAM_ABOUT_IMAGE));
        $form->aboutMetaTitle = Html::decode(Website::getParamContent(Website::PARAM_ABOUT_META_TITLE));
        $form->aboutMetaDescription = Html::decode(Website::getParamContent(Website::PARAM_ABOUT_META_DESCRIPTION));
        $form->aboutMetaKeywords = Html::decode(Website::getParamContent(Website::PARAM_ABOUT_META_KEYWORDS));
        
        return $form;
    }
    
    /**
     * 
     * @return type
     */
    public function getImagePath()
    {
        if ($this->aboutImage && file_exists(Yii::getAlias('@frontend/web' . $this->aboutImage))) {
            return $this->aboutImage;
        }
        return '/upload/placeholder.jpg';
    }
    
}
