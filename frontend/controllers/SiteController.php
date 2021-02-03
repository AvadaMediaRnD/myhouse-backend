<?php
namespace frontend\controllers;

use Yii;
use common\models\WebsiteHomeSlide;
use common\models\WebsiteHomeFeature;
use common\models\WebsiteAboutImage;
use common\models\WebsiteService;
use common\models\WebsiteDocument;
use common\models\WebsiteTariff;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\Pagination;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
            'glide' => 'trntv\glide\actions\GlideAction',
        ];
    }

    public function actionIndex()
    {
        $slides = WebsiteHomeSlide::find()->orderBy(['id' => SORT_ASC])->all();
        $features = WebsiteHomeFeature::find()->orderBy(['id' => SORT_ASC])->all();
        
        return $this->render('index', [
            'slides' => $slides,
            'features' => $features,
        ]);
    }

    public function actionContact()
    {
        return $this->render('contact', [
            
        ]);
    }

    public function actionAbout()
    {
        $imagesMain = WebsiteAboutImage::find()->where(['type' => WebsiteAboutImage::TYPE_MAIN])->orderBy(['id' => SORT_ASC])->all();
        $imagesAdd = WebsiteAboutImage::find()->where(['type' => WebsiteAboutImage::TYPE_ADDITIONAL])->orderBy(['id' => SORT_ASC])->all();
        $documents = WebsiteDocument::find()->all();
        
        return $this->render('about', [
            'imagesMain' => $imagesMain,
            'imagesAdd' => $imagesAdd,
            'documents' => $documents,
        ]);
    }

    public function actionServices()
    {
        $query = WebsiteService::find()->orderBy(['id' => SORT_ASC]);
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => Yii::$app->params['pageSize']]);
        $pages->pageSizeParam = false;
        $services = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();
        
        return $this->render('services', [
            'services' => $services,
            'pages' => $pages,
        ]);
    }
    
    public function actionTariffs()
    {
        $tariffs = WebsiteTariff::find()->orderBy(['id' => SORT_ASC])->all();
        
        return $this->render('tariffs', [
            'tariffs' => $tariffs,
        ]);
    }

}
