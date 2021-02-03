<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "website".
 *
 * @property int $id
 * @property string $param
 * @property string $content
 */
class Website extends \common\models\ZModel
{
    const PARAM_HOME_TITLE = 'home_title';
    const PARAM_HOME_DESCRIPTION = 'home_description';
    const PARAM_HOME_IS_SHOW_APPS = 'home_is_show_apps';
    const PARAM_HOME_URL_APP_IOS = 'home_url_app_ios';
    const PARAM_HOME_URL_APP_ANDROID = 'home_url_app_android';
    const PARAM_HOME_META_TITLE = 'home_meta_title';
    const PARAM_HOME_META_DESCRIPTION = 'home_meta_description';
    const PARAM_HOME_META_KEYWORDS = 'home_meta_keywords';
    const PARAM_CONTACT_TITLE = 'contact_title';
    const PARAM_CONTACT_DESCRIPTION = 'contact_description';
    const PARAM_CONTACT_FULLNAME = 'contact_fullname';
    const PARAM_CONTACT_LOCATION = 'contact_location';
    const PARAM_CONTACT_ADDRESS = 'contact_address';
    const PARAM_CONTACT_PHONE = 'contact_phone';
    const PARAM_CONTACT_EMAIL = 'contact_email';
    const PARAM_CONTACT_MAP_EMBED_CODE = 'contact_map_embed_code';
    const PARAM_CONTACT_URL_SITE = 'contact_url_site';
    const PARAM_CONTACT_META_TITLE = 'contact_meta_title';
    const PARAM_CONTACT_META_DESCRIPTION = 'contact_meta_description';
    const PARAM_CONTACT_META_KEYWORDS = 'contact_meta_keywords';
    const PARAM_ABOUT_TITLE = 'about_title';
    const PARAM_ABOUT_DESCRIPTION = 'about_description';
    const PARAM_ABOUT_IMAGE = 'about_image';
    const PARAM_ABOUT_TITLE_2 = 'about_title_2';
    const PARAM_ABOUT_DESCRIPTION_2 = 'about_description_2';
    const PARAM_ABOUT_META_TITLE = 'about_meta_title';
    const PARAM_ABOUT_META_DESCRIPTION = 'about_meta_description';
    const PARAM_ABOUT_META_KEYWORDS = 'about_meta_keywords';
    const PARAM_SERVICE_META_TITLE = 'service_meta_title';
    const PARAM_SERVICE_META_DESCRIPTION = 'service_meta_description';
    const PARAM_SERVICE_META_KEYWORDS = 'service_meta_keywords';
    const PARAM_TARIFF_TITLE = 'tariff_title';
    const PARAM_TARIFF_DESCRIPTION = 'tariff_description';
    const PARAM_TARIFF_META_TITLE = 'tariff_meta_title';
    const PARAM_TARIFF_META_DESCRIPTION = 'tariff_meta_description';
    const PARAM_TARIFF_META_KEYWORDS = 'tariff_meta_keywords';
    
    private static $paramContents = null;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'website';
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => \common\components\ChangeLogBehavior::className(),
                'labelObject' => 'Сайт',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['content'], 'string'],
            [['param'], 'string', 'max' => 255],
            [['param'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'param' => Yii::t('app', 'Param'),
            'content' => Yii::t('app', 'Content'),
        ];
    }

    /**
     * 
     * @param string $param
     * @return string
     */
    public static function getParamContent($param)
    {
        if (static::$paramContents=== null) {
            static::$paramContents = ArrayHelper::map(
                static::find()->all(),
                'param',
                'content'
            );
        }
        if (isset(static::$paramContents[$param])) {
            return static::$paramContents[$param];
        }
        return null;
    }
    
    /**
     * 
     * @param string $param
     * @return Website
     */
    public static function getByParam($param)
    {
        $model = static::find()->where(['param' => $param])->one();
        if (!$model) {
            $model = new static();
            $model->param = $param;
        }
        return $model;
    }
    
    /**
     * generates sitemap.xml file
     */
    public static function generateSitemap()
    {
        $savePath = Yii::getAlias('@frontend/web/sitemap.xml');
        $template = '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
<url>
<loc>{HOST}/</loc>
<lastmod>{LAST_MOD}</lastmod>
<priority>1.0</priority>
</url>
<url>
<loc>{HOST}/about</loc>
<lastmod>{LAST_MOD}</lastmod>
<priority>0.6</priority>
</url>
<url>
<loc>{HOST}/services</loc>
<lastmod>{LAST_MOD}</lastmod>
<priority>0.8</priority>
</url>
<url>
<loc>{HOST}/contact</loc>
<lastmod>{LAST_MOD}</lastmod>
<priority>0.8</priority>
</url>
</urlset>';
        $host = Yii::$app->request->hostInfo;
        $lastMod = date('Y-m-d\TH:i:sP', time());
        $content = str_replace(['{HOST}', '{LAST_MOD}'], [$host, $lastMod], $template);
        
        file_put_contents($savePath, $content);
        chmod($savePath, 0777);
    }
    
    /**
     * generates robots.txt file
     */
    public static function generateRobots()
    {
        $savePath = Yii::getAlias('@frontend/web/robots.txt');
        $template = 'User-agent: *
Disallow: /admin/

Host: {HOST}/
Sitemap: {HOST}/sitemap.xml';
        $host = Yii::$app->request->hostInfo;
        $content = str_replace('{HOST}', $host, $template);
        
        file_put_contents($savePath, $content);
        chmod($savePath, 0777);
    }
    
}
