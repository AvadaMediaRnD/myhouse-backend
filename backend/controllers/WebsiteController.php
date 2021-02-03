<?php
namespace backend\controllers;

use backend\models\WebsiteHomeForm;
use backend\models\WebsiteAboutForm;
use backend\models\WebsiteContactForm;
use backend\models\WebsiteServiceForm;
use backend\models\WebsiteTariffForm;
use common\models\WebsiteHomeSlide;
use common\models\WebsiteHomeFeature;
use common\models\WebsiteAboutImage;
use common\models\WebsiteService;
use common\models\WebsiteDocument;
use common\models\WebsiteTariffImage;
use common\models\WebsiteTariff;
use common\models\UserAdmin;
use common\models\Website;
use Yii;
use yii\filters\AccessControl;
//use yii\web\Controller;
use backend\controllers\ZController as Controller;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use yii\web\UploadedFile;

/**
 * Website controller
 */
class WebsiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function beforeAction($action) {
        $actions = $this->getAllowedActions();
        if (!Yii::$app->user->can(UserAdmin::PERMISSION_WEBSITE) && !in_array($action->id, $actions)) {
            // return $this->redirect(['/site/error']);
            throw new \yii\web\ForbiddenHttpException(Yii::t('app', static::FORBIDDEN_HTTP_MESSAGE));
        }
        return parent::beforeAction($action);
    }
    
    /**
     * 
     * @return mixed
     */
    public function actionHome()
    {
        $modelForm = WebsiteHomeForm::loadFromDb();
        $post = Yii::$app->request->post();
        
        if ($modelForm->load($post)) {
            if ($modelForm->process()) {
                $this->saveSlides();
                $this->saveFeatures();
                return $this->redirect(['home']);
            }
        }
        
        $slides = WebsiteHomeSlide::find()->all();
        $features = WebsiteHomeFeature::find()->all();
        
        return $this->render('home', [
            'modelForm' => $modelForm,
            'slides' => $slides,
            'features' => $features,
        ]);
    }

    /**
     * 
     * @return mixed
     */
    public function actionAbout()
    {
        $modelForm = WebsiteAboutForm::loadFromDb();
        $post = Yii::$app->request->post();
        
        if ($modelForm->load($post)) {
            if ($modelForm->process()) {
                $this->saveDocuments();
                return $this->redirect(['about']);
            }
        }
        
        $imagesMain = WebsiteAboutImage::find()->where(['type' => WebsiteAboutImage::TYPE_MAIN])->orderBy(['id' => SORT_ASC])->all();
        $imagesAdd = WebsiteAboutImage::find()->where(['type' => WebsiteAboutImage::TYPE_ADDITIONAL])->orderBy(['id' => SORT_ASC])->all();
        $websiteDocuments = WebsiteDocument::find()->all();
        
        return $this->render('about', [
            'modelForm' => $modelForm,
            'imagesMain' => $imagesMain,
            'imagesAdd' => $imagesAdd,
            'websiteDocuments' => $websiteDocuments,
        ]);
    }
    
    /**
     * 
     * @return mixed
     */
    public function actionServices()
    {
        $modelForm = WebsiteServiceForm::loadFromDb();
        $websiteServices = WebsiteService::find()->all();
        $post = Yii::$app->request->post();
        
        if ($modelForm->load($post) && $modelForm->process()) {
            $this->saveServices();
            return $this->redirect(['services']);
        }
        
        return $this->render('services', [
            'modelForm' => $modelForm,
            'websiteServices' => $websiteServices,
        ]);
    }
    
    /**
     * 
     * @return mixed
     */
    public function actionTariffs()
    {
        $modelForm = WebsiteTariffForm::loadFromDb();
        $websiteTariffs = WebsiteTariff::find()->all();
        $post = Yii::$app->request->post();
        
        if ($modelForm->load($post) && $modelForm->process()) {
            $this->saveTariffs();
            return $this->redirect(['tariffs']);
        }
        
        return $this->render('tariffs', [
            'modelForm' => $modelForm,
            'websiteTariffs' => $websiteTariffs,
        ]);
    }
    
    /**
     * 
     * @return mixed
     */
    public function actionContact()
    {
        $modelForm = WebsiteContactForm::loadFromDb();
        $post = Yii::$app->request->post();
        
        if ($modelForm->load($post)) {
            if ($modelForm->process()) {
                return $this->redirect(['contact']);
            }
        }
        
        return $this->render('contact', [
            'modelForm' => $modelForm,
        ]);
    }
    
    /**
     * 
     * @return mixed
     */
    public function actionUpdateSeoFiles()
    {
        Website::generateSitemap();
        Website::generateRobots();

        return 'done';
    }
    
    /**
     * 
     * @param integer $id
     * @return mixed
     */
    public function actionDeleteAboutImage($id)
    {
        $model = WebsiteAboutImage::findOne($id);
        if ($model) {
            $model->delete();
        }
        return $this->redirect(['about']);
    }
    
    /**
     * 
     * @param integer $id
     * @return mixed
     */
    public function actionDeleteTariff($id)
    {
        $model = WebsiteTariff::findOne($id);
        if ($model) {
            $model->delete();
        }
        return $this->redirect(['tariffs']);
    }
    
    /**
     * @return string
     */
    public function actionAjaxGetFormWebsiteTariff()
    {
        $formId = (int)Yii::$app->request->get('form_id');
        $model = new WebsiteTariff();
        return $this->renderAjax('_form-tariff', [
            'formId' => $formId,
            'model' => $model,
        ]);
    }
    
    /**
     * 
     * @param integer $id
     * @return mixed
     */
    public function actionDeleteService($id)
    {
        $model = WebsiteService::findOne($id);
        if ($model) {
            $model->delete();
        }
        return $this->redirect(['services']);
    }
    
    /**
     * @return string
     */
    public function actionAjaxGetFormWebsiteService()
    {
        $formId = (int)Yii::$app->request->get('form_id');
        $model = new WebsiteService();
        return $this->renderAjax('_form-service', [
            'formId' => $formId,
            'model' => $model,
        ]);
    }
    
    /**
     * 
     * @param integer $id
     * @return mixed
     */
    public function actionDeleteDocument($id)
    {
        $model = WebsiteDocument::findOne($id);
        if ($model) {
            $model->delete();
        }
        return $this->redirect(['about']);
    }
    
    /**
     * @return string
     */
    public function actionAjaxGetFormWebsiteDocument()
    {
        $formId = (int)Yii::$app->request->get('form_id');
        $model = new WebsiteDocument();
        return $this->renderAjax('_form-document', [
            'formId' => $formId,
            'model' => $model,
        ]);
    }
    
    /**
     * 
     */
    protected function saveSlides()
    {
        $modelsPostIdx = 'WebsiteHomeSlide';
        if ($postModels = Yii::$app->request->post()[$modelsPostIdx]) {
            $ids = [];
            foreach ($postModels as $k => $postModel) {
                $postModel['title'] = Html::encode($postModel['title']);
                
                $subModel = WebsiteHomeSlide::findOne($postModel['id']);
                if (!$subModel) {
                    $subModel = new WebsiteHomeSlide();
                }
                $subModel->load([$modelsPostIdx => $postModel]);
                if ($subModel->save()) {
                    // upload
                    $file = UploadedFile::getInstance($subModel, "[$k]imageFile");
                    if ($file) {
                        $path = '/upload/'.$modelsPostIdx.'/' . $subModel->id . '/image.' . $file->extension; 
                        $pathFull = Yii::getAlias('@frontend/web' . $path);
                        $dir = dirname($pathFull);
                        if (!is_dir($dir)) {
                            mkdir($dir, 0777, true);
                        }
                        if ($file->saveAs($pathFull)) {
                            $subModel->image = $path;
                            $subModel->save(false);
                            Yii::$app->glide->getServer()->deleteCache($path);
                        }
                    }
                }
            }
        }
    }
    
    /**
     * 
     */
    protected function saveFeatures()
    {
        $modelsPostIdx = 'WebsiteHomeFeature';
        if ($postModels = Yii::$app->request->post()[$modelsPostIdx]) {
            $ids = [];
            foreach ($postModels as $k => $postModel) {
                $postModel['title'] = Html::encode($postModel['title']);
                $postModel['description'] = Html::encode($postModel['description']);
                
                $subModel = WebsiteHomeFeature::findOne($postModel['id']);
                if (!$subModel) {
                    $subModel = new WebsiteHomeFeature();
                }
                $subModel->load([$modelsPostIdx => $postModel]);
                if ($subModel->save()) {
                    // upload
                    $file = UploadedFile::getInstance($subModel, "[$k]imageFile");
                    if ($file) {
                        $path = '/upload/'.$modelsPostIdx.'/' . $subModel->id . '/image.' . $file->extension; 
                        $pathFull = Yii::getAlias('@frontend/web' . $path);
                        $dir = dirname($pathFull);
                        if (!is_dir($dir)) {
                            mkdir($dir, 0777, true);
                        }
                        if ($file->saveAs($pathFull)) {
                            $subModel->image = $path;
                            $subModel->save(false);
                            Yii::$app->glide->getServer()->deleteCache($path);
                        }
                    }
                }
            }
        }
    }
    
    /**
     * 
     */
    protected function saveServices()
    {
        $modelsPostIdx = 'WebsiteService';
        if ($postModels = Yii::$app->request->post()[$modelsPostIdx]) {
            foreach ($postModels as $k => $postModel) {
                $subModel = WebsiteService::findOne($postModel['id']);
                if (!$subModel) {
                    $subModel = new WebsiteService();
                }
                $subModel->load([$modelsPostIdx => $postModel]);
                if ($subModel->save()) {
                    // upload
                    $file = UploadedFile::getInstance($subModel, "[$k]imageFile");
                    if ($file) {
                        $path = '/upload/'.$modelsPostIdx.'/' . $subModel->id . '/image.' . $file->extension; 
                        $pathFull = Yii::getAlias('@frontend/web' . $path);
                        $dir = dirname($pathFull);
                        if (!is_dir($dir)) {
                            mkdir($dir, 0777, true);
                        }
                        if ($file->saveAs($pathFull)) {
                            $subModel->image = $path;
                            $subModel->save(false);
                            Yii::$app->glide->getServer()->deleteCache($path);
                        }
                    }
                }
            }
        }
    }
    
    /**
     * 
     */
    protected function saveDocuments()
    {
        $modelsPostIdx = 'WebsiteDocument';
        if ($postModels = Yii::$app->request->post()[$modelsPostIdx]) {
            foreach ($postModels as $k => $postModel) {
                $subModel = WebsiteDocument::findOne($postModel['id']);
                if (!$subModel) {
                    $subModel = new WebsiteDocument();
                }
                $subModel->load([$modelsPostIdx => $postModel]);
                $file = UploadedFile::getInstance($subModel, "[$k]fileFile");
                if (($file || $subModel->title) && $subModel->save()) {
                    // upload
                    if ($file) {
                        $filename = 'file' . $subModel->id . '_' . date('YmdHi');
                        $path = '/upload/'.$modelsPostIdx.'/' . $subModel->id . '/'.$filename.'.' . $file->extension; 
                        $pathFull = Yii::getAlias('@frontend/web' . $path);
                        $dir = dirname($pathFull);
                        if (!is_dir($dir)) {
                            mkdir($dir, 0777, true);
                        }
                        if ($file->saveAs($pathFull)) {
                            $subModel->file = $path;
                            $subModel->save(false);
                            Yii::$app->glide->getServer()->deleteCache($path);
                        }
                    }
                }
            }
        }
    }
    
    /**
     * 
     */
    protected function saveTariffs()
    {
        $modelsPostIdx = 'WebsiteTariff';
        if ($postModels = Yii::$app->request->post()[$modelsPostIdx]) {
            foreach ($postModels as $k => $postModel) {
                $subModel = WebsiteTariff::findOne($postModel['id']);
                if (!$subModel) {
                    $subModel = new WebsiteTariff();
                }
                $subModel->load([$modelsPostIdx => $postModel]);
                if ($subModel->save()) {
                    // upload
                    $file = UploadedFile::getInstance($subModel, "[$k]imageFile");
                    if ($file) {
                        $path = '/upload/'.$modelsPostIdx.'/' . $subModel->id . '/image.' . $file->extension; 
                        $pathFull = Yii::getAlias('@frontend/web' . $path);
                        $dir = dirname($pathFull);
                        if (!is_dir($dir)) {
                            mkdir($dir, 0777, true);
                        }
                        if ($file->saveAs($pathFull)) {
                            $subModel->image = $path;
                            $subModel->save(false);
                            Yii::$app->glide->getServer()->deleteCache($path);
                        }
                    }
                }
            }
        }
    }
}
