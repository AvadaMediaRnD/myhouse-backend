<?php
namespace backend\models;

use common\models\Website;
use Yii;
use yii\base\Model;
use yii\helpers\Html;

/**
 * Website Service form
 */
class WebsiteServiceForm extends Model
{
    public $serviceMetaTitle;
    public $serviceMetaDescription;
    public $serviceMetaKeywords;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['serviceMetaTitle', 'serviceMetaDescription', 'serviceMetaKeywords'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'serviceMetaTitle' => Yii::t('model', 'Title'),
            'serviceMetaDescription' => Yii::t('model', 'Description'),
            'serviceMetaKeywords' => Yii::t('model', 'Keywords'),
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
            $model = Website::getByParam(Website::PARAM_SERVICE_META_TITLE);
            $model->content = Html::encode($this->serviceMetaTitle);
            $model->save();
            $model = Website::getByParam(Website::PARAM_SERVICE_META_DESCRIPTION);
            $model->content = Html::encode($this->serviceMetaDescription);
            $model->save();
            $model = Website::getByParam(Website::PARAM_SERVICE_META_KEYWORDS);
            $model->content = Html::encode($this->serviceMetaKeywords);
            $model->save();
            
            return true;
        }

        return false;
    }

    /**
     * @return WebsiteAboutForm
     */
    public static function loadFromDb() {
        $form = new static();
        
        $form->serviceMetaTitle = Html::decode(Website::getParamContent(Website::PARAM_SERVICE_META_TITLE));
        $form->serviceMetaDescription = Html::decode(Website::getParamContent(Website::PARAM_SERVICE_META_DESCRIPTION));
        $form->serviceMetaKeywords = Html::decode(Website::getParamContent(Website::PARAM_SERVICE_META_KEYWORDS));
        
        return $form;
    }
    
}
