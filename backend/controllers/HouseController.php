<?php

namespace backend\controllers;

use common\models\Floor;
use common\models\Riser;
use common\models\Section;
use common\models\HouseUserAdmin;
use common\models\UserAdmin;
use Yii;
use common\models\House;
use backend\models\HouseSearch;
use backend\controllers\ZController;
use yii\base\Security;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use backend\models\HouseForm;
use yii\filters\AccessControl;

/**
 * HouseController implements the CRUD actions for House model.
 */
class HouseController extends ZController
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
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function beforeAction($action) {
        $actions = $this->getAllowedActions();
        $actions[] = 'ajax-get-form-section';
        $actions[] = 'ajax-get-form-floor';
        $actions[] = 'ajax-get-form-riser';
        $actions[] = 'ajax-get-form-house-user-admin';
        $actions[] = 'ajax-get-user-admin-role-label';
        if (!Yii::$app->user->can(UserAdmin::PERMISSION_HOUSE) && !in_array($action->id, $actions)) {
            // return $this->redirect(['/site/error']);
            throw new \yii\web\ForbiddenHttpException(Yii::t('app', static::FORBIDDEN_HTTP_MESSAGE));
        }
        return parent::beforeAction($action);
    }

    /**
     * Lists all House models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new HouseSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single House model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new House model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new House();
        $post = Yii::$app->request->post();

        $modelForm = HouseForm::loadFromModel($model);
        if ($modelForm->load($post)) {
            if ($model = $modelForm->process()) {
                $this->saveSections($model);
                $this->saveFloors($model);
                // $this->saveRisers($model);
                $this->saveHouseUserAdmins($model);
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'modelForm' => $modelForm,
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing House model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $post = Yii::$app->request->post();

        $modelForm = HouseForm::loadFromModel($model);
        if ($modelForm->load($post)) {
            if ($model = $modelForm->process()) {
                $this->saveSections($model);
                $this->saveFloors($model);
                // $this->saveRisers($model);
                $this->saveHouseUserAdmins($model);
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'modelForm' => $modelForm,
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing House model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @return string
     */
    public function actionAjaxGetFormSection()
    {
        $formId = (int)Yii::$app->request->get('form_id');
        $houseId = (int)Yii::$app->request->get('house_id');
        $model = new Section();
        $model->house_id = (int)$houseId;
        // generate name
        $model->name = 'Секция ' . ($formId + 1);
        return $this->renderAjax('_form-section', [
            'formId' => $formId,
            'model' => $model,
        ]);
    }

    /**
     * @return string
     */
    public function actionAjaxGetFormFloor()
    {
        $formId = (int)Yii::$app->request->get('form_id');
        $houseId = (int)Yii::$app->request->get('house_id');
        $model = new Floor();
        $model->house_id = (int)$houseId;
        // generate name
        $model->name = 'Этаж ' . ($formId + 1);
        return $this->renderAjax('_form-floor', [
            'formId' => $formId,
            'model' => $model,
        ]);
    }

    /**
     * @return string
     */
    public function actionAjaxGetFormRiser()
    {
        $formId = (int)Yii::$app->request->get('form_id');
        $houseId = (int)Yii::$app->request->get('house_id');
        $model = new Riser();
        $model->house_id = (int)$houseId;
        // generate name
        $model->name = 'Стояк ' . ($formId + 1);;
        return $this->renderAjax('_form-riser', [
            'formId' => $formId,
            'model' => $model,
        ]);
    }
    
    /**
     * @return string
     */
    public function actionAjaxGetFormHouseUserAdmin()
    {
        $formId = (int)Yii::$app->request->get('form_id');
        $houseId = (int)Yii::$app->request->get('house_id');
        $model = new HouseUserAdmin();
        $model->house_id = (int)$houseId;
        return $this->renderAjax('_form-houseuseradmin', [
            'formId' => $formId,
            'model' => $model,
        ]);
    }
    
    /**
     * @return string
     */
    public function actionAjaxGetUserAdminRoleLabel()
    {
        $id = (int)Yii::$app->request->get('id');
        $model = UserAdmin::findOne($id);
        if ($model) {
            return $model->getRoleLabel();
        }
        return '';
    }

    /**
     * Finds the House model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return House the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = House::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * @param House $model
     */
    protected function saveSections($model)
    {
        $modelsPostIdx = 'Section';
        if ($postModels = Yii::$app->request->post()[$modelsPostIdx]) {
            $ids = [];
            foreach ($postModels as $postModel) {
                $subModel = Section::findOne($postModel['id']);
                if (!$subModel) {
                    $subModel = new Section();
                }
                $subModel->load([$modelsPostIdx => $postModel]);
                if (!$subModel->house_id) {
                    $subModel->house_id = $model->id;
                }
                $subModel->save();

                $ids[] = $subModel->id;
            }
            Section::deleteAll(['and', ['house_id' => $model->id], ['not in', 'id', $ids]]);
        }
    }

    /**
     * @param House $model
     */
    protected function saveFloors($model)
    {
        $modelsPostIdx = 'Floor';
        if ($postModels = Yii::$app->request->post()[$modelsPostIdx]) {
            $ids = [];
            foreach ($postModels as $postModel) {
                $subModel = Floor::findOne($postModel['id']);
                if (!$subModel) {
                    $subModel = new Floor();
                }
                $subModel->load([$modelsPostIdx => $postModel]);
                if (!$subModel->house_id) {
                    $subModel->house_id = $model->id;
                }
                $subModel->save();

                $ids[] = $subModel->id;
            }
            Floor::deleteAll(['and', ['house_id' => $model->id], ['not in', 'id', $ids]]);
        }
    }

    /**
     * @param House $model
     */
    protected function saveRisers($model)
    {
        $modelsPostIdx = 'Riser';
        if ($postModels = Yii::$app->request->post()[$modelsPostIdx]) {
            $ids = [];
            foreach ($postModels as $postModel) {
                $subModel = Riser::findOne($postModel['id']);
                if (!$subModel) {
                    $subModel = new Riser();
                }
                $subModel->load([$modelsPostIdx => $postModel]);
                if (!$subModel->house_id) {
                    $subModel->house_id = $model->id;
                }
                $subModel->save();

                $ids[] = $subModel->id;
            }
            Riser::deleteAll(['and', ['house_id' => $model->id], ['not in', 'id', $ids]]);
        }
    }
    
    /**
     * @param House $model
     */
    protected function saveHouseUserAdmins($model)
    {
        $modelsPostIdx = 'HouseUserAdmin';
        $ids = [];
        if ($postModels = Yii::$app->request->post()[$modelsPostIdx]) {
            foreach ($postModels as $postModel) {
                $subModel = HouseUserAdmin::findOne($postModel['id']);
                if (!$subModel) {
                    $subModel = new HouseUserAdmin();
                }
                $subModel->load([$modelsPostIdx => $postModel]);
                if (!$subModel->house_id) {
                    $subModel->house_id = $model->id;
                }
                $subModel->save();

                $ids[] = $subModel->id;
            }
        }
        HouseUserAdmin::deleteAll(['and', ['house_id' => $model->id], ['not in', 'id', $ids]]);
    }
}
