<?php
namespace backend\models;

use common\models\Website;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;
use yii\helpers\Html;

/**
 * Website Home form
 */
class WebsiteHomeForm extends Model
{
    public $homeTitle;
    public $homeDescription;
    public $homeIsShowApps;
    public $homeUrlAppIos;
    public $homeUrlAppAndroid;
    public $contactFullname;
    public $contactLocation;
    public $contactAddress;
    public $contactPhone;
    public $contactEmail;
    public $homeMetaTitle;
    public $homeMetaDescription;
    public $homeMetaKeywords;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['homeTitle', 'homeDescription',
                'contactFullname', 'contactLocation', 'contactAddress',
                'contactPhone', 'contactEmail', 'homeIsShowApps', 
                'homeUrlAppIos', 'homeUrlAppAndroid',
                'homeMetaTitle', 'homeMetaDescription', 'homeMetaKeywords'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'homeTitle' => Yii::t('model', 'Заголовок'),
            'homeDescription' => Yii::t('model', 'Краткий текст'),
            'contactFullname' => Yii::t('model', 'ФИО'),
            'contactLocation' => Yii::t('model', 'Локация'),
            'contactAddress' => Yii::t('model', 'Адрес'),
            'contactPhone' => Yii::t('model', 'Телефон'),
            'contactEmail' => Yii::t('model', 'E-mail'),
            'homeIsShowApps' => Yii::t('model', 'Показать ссылки на приложения'),
            'homeUrlAppIos' => Yii::t('model', 'Ссылка на IOS приложениe'),
            'homeUrlAppAndroid' => Yii::t('model', 'Ссылка на Android приложение'),
            'homeMetaTitle' => Yii::t('model', 'Title'),
            'homeMetaDescription' => Yii::t('model', 'Description'),
            'homeMetaKeywords' => Yii::t('model', 'Keywords'),
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
            $model = Website::getByParam(Website::PARAM_HOME_TITLE);
            $model->content = Html::encode($this->homeTitle);
            $model->save();
            $model = Website::getByParam(Website::PARAM_HOME_DESCRIPTION);
            $model->content = Html::encode($this->homeDescription);
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
            $model = Website::getByParam(Website::PARAM_HOME_IS_SHOW_APPS);
            $model->content = Html::encode($this->homeIsShowApps);
            $model->save();
//            $model = Website::getByParam(Website::PARAM_HOME_URL_APP_IOS);
//            $model->content = Html::encode($this->homeUrlAppIos);
//            $model->save();
//            $model = Website::getByParam(Website::PARAM_HOME_URL_APP_ANDROID);
//            $model->content = Html::encode($this->homeUrlAppAndroid);
//            $model->save();
            $model = Website::getByParam(Website::PARAM_HOME_META_TITLE);
            $model->content = Html::encode($this->homeMetaTitle);
            $model->save();
            $model = Website::getByParam(Website::PARAM_HOME_META_DESCRIPTION);
            $model->content = Html::encode($this->homeMetaDescription);
            $model->save();
            $model = Website::getByParam(Website::PARAM_HOME_META_KEYWORDS);
            $model->content = Html::encode($this->homeMetaKeywords);
            $model->save();
            
            return true;
        }

        return false;
    }

    /**
     * @return WebsiteHomeForm
     */
    public static function loadFromDb() {
        $form = new static();
        
        $form->homeTitle = Html::decode(Website::getParamContent(Website::PARAM_HOME_TITLE));
        $form->homeDescription = Html::decode(Website::getParamContent(Website::PARAM_HOME_DESCRIPTION));
        $form->contactFullname = Html::decode(Website::getParamContent(Website::PARAM_CONTACT_FULLNAME));
        $form->contactLocation = Html::decode(Website::getParamContent(Website::PARAM_CONTACT_LOCATION));
        $form->contactAddress = Html::decode(Website::getParamContent(Website::PARAM_CONTACT_ADDRESS));
        $form->contactPhone = Html::decode(Website::getParamContent(Website::PARAM_CONTACT_PHONE));
        $form->contactEmail = Html::decode(Website::getParamContent(Website::PARAM_CONTACT_EMAIL));
        $form->homeIsShowApps = Html::decode(Website::getParamContent(Website::PARAM_HOME_IS_SHOW_APPS));
        $form->homeUrlAppIos = Html::decode(Website::getParamContent(Website::PARAM_HOME_URL_APP_IOS));
        $form->homeUrlAppAndroid = Html::decode(Website::getParamContent(Website::PARAM_HOME_URL_APP_ANDROID));
        $form->homeMetaTitle = Html::decode(Website::getParamContent(Website::PARAM_HOME_META_TITLE));
        $form->homeMetaDescription = Html::decode(Website::getParamContent(Website::PARAM_HOME_META_DESCRIPTION));
        $form->homeMetaKeywords = Html::decode(Website::getParamContent(Website::PARAM_HOME_META_KEYWORDS));
        
        return $form;
    }
    
}
