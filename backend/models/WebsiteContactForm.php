<?php
namespace backend\models;

use common\models\Website;
use Yii;
use yii\base\Model;
use yii\helpers\Html;

/**
 * Website Contact form
 */
class WebsiteContactForm extends Model
{
    public $contactTitle;
    public $contactDescription;
    public $contactMapEmbedCode;
    public $contactFullname;
    public $contactLocation;
    public $contactAddress;
    public $contactPhone;
    public $contactEmail;
    public $contactUrlSite;
    public $contactMetaTitle;
    public $contactMetaDescription;
    public $contactMetaKeywords;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['contactTitle', 'contactDescription', 'contactMapEmbedCode',
                'contactFullname', 'contactLocation', 'contactAddress',
                'contactPhone', 'contactEmail', 'contactUrlSite',
                'contactMetaTitle', 'contactMetaDescription', 'contactMetaKeywords'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'contactTitle' => Yii::t('model', 'Заголовок'),
            'contactDescription' => Yii::t('model', 'Краткий текст'),
            'contactMapEmbedCode' => Yii::t('model', 'Код карты'),
            'contactFullname' => Yii::t('model', 'ФИО'),
            'contactLocation' => Yii::t('model', 'Локация'),
            'contactAddress' => Yii::t('model', 'Адрес'),
            'contactPhone' => Yii::t('model', 'Телефон'),
            'contactEmail' => Yii::t('model', 'E-mail'),
            'contactUrlSite' => Yii::t('model', 'Ссылка на коммерческий сайт'),
            'contactMetaTitle' => Yii::t('model', 'Title'),
            'contactMetaDescription' => Yii::t('model', 'Description'),
            'contactMetaKeywords' => Yii::t('model', 'Keywords'),
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
            $model = Website::getByParam(Website::PARAM_CONTACT_TITLE);
            $model->content = Html::encode($this->contactTitle);
            $model->save();
            $model = Website::getByParam(Website::PARAM_CONTACT_DESCRIPTION);
            $model->content = Html::encode($this->contactDescription);
            $model->save();
            $model = Website::getByParam(Website::PARAM_CONTACT_MAP_EMBED_CODE);
            $model->content = Html::encode($this->contactMapEmbedCode);
            $model->save();
            $model = Website::getByParam(Website::PARAM_CONTACT_FULLNAME);
            $model->content = Html::encode($this->contactFullname);
            $model->save();
            $model = Website::getByParam(Website::PARAM_CONTACT_LOCATION);
            $model->content = Html::encode($this->contactLocation);
            $model->save();
            $model = Website::getByParam(Website::PARAM_CONTACT_ADDRESS);
            $model->content = Html::encode($this->contactAddress);
            $model->save();
            $model = Website::getByParam(Website::PARAM_CONTACT_PHONE);
            $model->content = Html::encode($this->contactPhone);
            $model->save();
            $model = Website::getByParam(Website::PARAM_CONTACT_EMAIL);
            $model->content = Html::encode($this->contactEmail);
            $model->save();
            $model = Website::getByParam(Website::PARAM_CONTACT_URL_SITE);
            $model->content = Html::encode($this->contactUrlSite);
            $model->save();
            $model = Website::getByParam(Website::PARAM_CONTACT_META_TITLE);
            $model->content = Html::encode($this->contactMetaTitle);
            $model->save();
            $model = Website::getByParam(Website::PARAM_CONTACT_META_DESCRIPTION);
            $model->content = Html::encode($this->contactMetaDescription);
            $model->save();
            $model = Website::getByParam(Website::PARAM_CONTACT_META_KEYWORDS);
            $model->content = Html::encode($this->contactMetaKeywords);
            $model->save();
            
            return true;
        }

        return false;
    }

    /**
     * @return WebsiteContactForm
     */
    public static function loadFromDb() {
        $form = new static();
        
        $form->contactTitle = Html::decode(Website::getParamContent(Website::PARAM_CONTACT_TITLE));
        $form->contactDescription = Html::decode(Website::getParamContent(Website::PARAM_CONTACT_DESCRIPTION));
        $form->contactMapEmbedCode = Html::decode(Website::getParamContent(Website::PARAM_CONTACT_MAP_EMBED_CODE));
        $form->contactFullname = Html::decode(Website::getParamContent(Website::PARAM_CONTACT_FULLNAME));
        $form->contactLocation = Html::decode(Website::getParamContent(Website::PARAM_CONTACT_LOCATION));
        $form->contactAddress = Html::decode(Website::getParamContent(Website::PARAM_CONTACT_ADDRESS));
        $form->contactPhone = Html::decode(Website::getParamContent(Website::PARAM_CONTACT_PHONE));
        $form->contactEmail = Html::decode(Website::getParamContent(Website::PARAM_CONTACT_EMAIL));
        $form->contactUrlSite = Html::decode(Website::getParamContent(Website::PARAM_CONTACT_URL_SITE));
        $form->contactMetaTitle = Html::decode(Website::getParamContent(Website::PARAM_CONTACT_META_TITLE));
        $form->contactMetaDescription = Html::decode(Website::getParamContent(Website::PARAM_CONTACT_META_DESCRIPTION));
        $form->contactMetaKeywords = Html::decode(Website::getParamContent(Website::PARAM_CONTACT_META_KEYWORDS));
        
        return $form;
    }
    
}
