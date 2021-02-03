<?php
namespace backend\models;

use common\models\Website;
use common\models\WebsiteTariffImage;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;
use yii\helpers\Html;

/**
 * Website Tariff form
 */
class WebsiteTariffForm extends Model
{
    public $tariffTitle;
    public $tariffDescription;
    public $tariffMetaTitle;
    public $tariffMetaDescription;
    public $tariffMetaKeywords;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tariffTitle', 'tariffDescription', 'tariffMetaTitle', 'tariffMetaDescription', 'tariffMetaKeywords'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'tariffTitle' => Yii::t('model', 'Заголовок'),
            'tariffDescription' => Yii::t('model', 'Краткий текст'),
            'tariffMetaTitle' => Yii::t('model', 'Title'),
            'tariffMetaDescription' => Yii::t('model', 'Description'),
            'tariffMetaKeywords' => Yii::t('model', 'Keywords'),
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
            $model = Website::getByParam(Website::PARAM_TARIFF_TITLE);
            $model->content = Html::encode($this->tariffTitle);
            $model->save();
            $model = Website::getByParam(Website::PARAM_TARIFF_DESCRIPTION);
            $model->content = Html::encode($this->tariffDescription);
            $model->save();
            $model = Website::getByParam(Website::PARAM_TARIFF_META_TITLE);
            $model->content = Html::encode($this->tariffMetaTitle);
            $model->save();
            $model = Website::getByParam(Website::PARAM_TARIFF_META_DESCRIPTION);
            $model->content = Html::encode($this->tariffMetaDescription);
            $model->save();
            $model = Website::getByParam(Website::PARAM_TARIFF_META_KEYWORDS);
            $model->content = Html::encode($this->tariffMetaKeywords);
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
        
        $form->tariffTitle = Html::decode(Website::getParamContent(Website::PARAM_TARIFF_TITLE));
        $form->tariffDescription = Html::decode(Website::getParamContent(Website::PARAM_TARIFF_DESCRIPTION));
        $form->tariffMetaTitle = Html::decode(Website::getParamContent(Website::PARAM_TARIFF_META_TITLE));
        $form->tariffMetaDescription = Html::decode(Website::getParamContent(Website::PARAM_TARIFF_META_DESCRIPTION));
        $form->tariffMetaKeywords = Html::decode(Website::getParamContent(Website::PARAM_TARIFF_META_KEYWORDS));
        
        return $form;
    }
    
}
