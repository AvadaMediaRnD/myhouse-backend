<?php

namespace backend\controllers;

use common\models\House;
use common\models\Section;
use common\models\Floor;
use common\models\Riser;
use common\models\Account;
use common\models\Tariff;
use common\models\UserAdmin;
use Yii;
use common\models\Flat;
use backend\models\FlatSearch;
use backend\models\FlatForm;
use backend\controllers\ZController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * FlatController implements the CRUD actions for Flat model.
 */
class FlatController extends ZController
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
        $actions[] = 'get-lists-by-house';
        $actions[] = 'get-flat';
        if (!Yii::$app->user->can(UserAdmin::PERMISSION_FLAT) && !in_array($action->id, $actions)) {
            // return $this->redirect(['/site/error']);
            throw new \yii\web\ForbiddenHttpException(Yii::t('app', static::FORBIDDEN_HTTP_MESSAGE));
        }
        return parent::beforeAction($action);
    }

    /**
     * Lists all Flat models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new FlatSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Flat model.
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
     * Creates a new Flat model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Flat();
        $post = Yii::$app->request->post();
        if ($flatId = Yii::$app->request->get('flat_id')) {
            $modelClone = Flat::findOne($flatId);
            if ($modelClone) {
                $model->setAttributes($modelClone->attributes);
            }
            $model->id = null;
            $model->user_id = null;
            $model->flat = Flat::find(['house_id' => $modelClone->house_id])->max('flat') + 1;
        }

        $modelForm = FlatForm::loadFromModel($model);
        if ($modelForm->load($post)) {
            if ($model = $modelForm->process()) {
                if ($post['action_save_add']) {
                    return $this->redirect(['create', 'flat_id' => $model->id]);
                }
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'modelForm' => $modelForm,
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Flat model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $post = Yii::$app->request->post();
        
        $modelForm = FlatForm::loadFromModel($model);
        if ($modelForm->load($post)) {
            if ($model = $modelForm->process()) {
                if ($post['action_save_add']) {
                    return $this->redirect(['create', 'flat_id' => $model->id]);
                }
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'modelForm' => $modelForm,
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Flat model.
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
     * @param $house_id
     * @param $section_id
     * @param $floor_id
     * @return array
     */
    public function actionGetListsByHouse($house_id = null, $section_id = null, $floor_id = null, $riser_id = null)
    {
        $houseModel = House::findOne($house_id);
        $sectionModel = Section::findOne($section_id);
        $floorModel = Floor::findOne($floor_id);
        $riserModel = Riser::findOne($riser_id);

        $sectionsData = '<option value="">Выберите...</option>'."\r\n";
        $risersData = '<option value="">Выберите...</option>'."\r\n";
        $floorsData = '<option value="">Выберите...</option>'."\r\n";
        $flatsData = '<option value="">Выберите...</option>'."\r\n";
        if ($houseModel) {
            $sections = $houseModel->getSections()->all();
            foreach ($sections as $section) {
                $sectionsData .= '<option value="' . $section->id . '">' . $section->name . '</option>'."\r\n";
            }

            $risers = $houseModel->getRisers()->all();
            foreach ($risers as $riser) {
                $risersData .= '<option value="' . $riser->id . '">' . $riser->name . '</option>'."\r\n";
            }

            $floors = $houseModel->getFloors()->all();
            foreach ($floors as $floor) {
                $floorsData .= '<option value="' . $floor->id . '">' . $floor->name . '</option>'."\r\n";
            }
        }
        
        if ($houseModel || $sectionModel || $floorModel || $riserModel) {
            $flatsQuery = Flat::find();
            
            if ($houseModel) {
//                $flatsQuery = $houseModel->getFlats();
                $flatsQuery->andWhere(['house_id' => $houseModel->id]);
            }
            if ($sectionModel) {
//                $flatsQuery = $sectionModel->getFlats();
                $flatsQuery->andWhere(['section_id' => $sectionModel->id]);
            }
            if ($floorModel) {
//                $flatsQuery = $floorModel->getFlats();
                $flatsQuery->andWhere(['floor_id' => $floorModel->id]);
            }
            if ($riserModel) {
//                $flatsQuery = $riserModel->getFlats();
                $flatsQuery->andWhere(['riser_id' => $riserModel->id]);
            }
            if ($flatsQuery) {
                foreach ($flatsQuery->all() as $flat) {
                    $flatsData .= '<option value="' . $flat->id . '">' . $flat->flat . '</option>'."\r\n";
                }
            }
        }

        return [
            'sections' => $sectionsData,
            'risers' => $risersData,
            'floors' => $floorsData,
            'flats' => $flatsData,
        ];
    }
    
    public function actionGetFlat($flat_id = null, $account_uid = null)
    {
        $flat = null;
        $account = null;
        $user = null;
        $tariff = null;
        $house = null;
        $section = null;
        $floor = null;
        $flatsData = '<option value="">Выберите...</option>'."\r\n";
        $tariffsData = '<option value="">Выберите...</option>'."\r\n";
        $housesData = '<option value="">Выберите...</option>'."\r\n";
        $sectionsData = '<option value="">Выберите...</option>'."\r\n";
        $floorsData = '<option value="">Выберите...</option>'."\r\n";
        
        if ($flat_id) {
            $flat = Flat::findOne($flat_id);
            $account = $flat->account;
        }
        if ($account_uid) {
            $account = Account::find()->where(['uid' => $account_uid])->one();
            $flat = $account->flat;
        }
        
        if ($flat) {
            $user = [];
            if ($flat->user) {
                $user = $flat->user->toArray();
                $user['fullname'] = $flat->user->fullname;
                $user['phone'] = $flat->user->profile->phone;
                $user['fullnameHtml'] = '<a href="' . ($flat->user ? Yii::$app->urlManager->createUrl(['/user/view', 'id' => $flat->user_id]) : '#!') . '">' . $flat->user->fullname . '</a>';
                $user['phoneHtml'] = '<a href="tel:' . str_replace(['(', ')', ' ', '-'], '', $flat->user->profile->phone) . '">' . $flat->user->profile->phone . '</a>';
            }
            $tariff = $flat->tariff;
            $house = $flat->house;
            $section = $flat->section;
            $floor = $flat->floor;
        }
        
        // tariffs
        $tariffs = Tariff::find()->all();
        foreach ($tariffs as $tariffItem) {
            if ($tariff && $tariffItem->id == $tariff->id) {
                $tariffsData .= '<option value="' . $tariffItem->id . '" selected="selected">' . $tariffItem->name . '</option>'."\r\n";
            } else {
                $tariffsData .= '<option value="' . $tariffItem->id . '">' . $tariffItem->name . '</option>'."\r\n";
            }
        }
        
        // houses
        $housesQuery = House::find();
        $houses = $housesQuery->all();
        foreach ($houses as $houseItem) {
            if ($house && $houseItem->id == $house->id) {
                $housesData .= '<option value="' . $houseItem->id . '" selected="selected">' . $houseItem->name . '</option>'."\r\n";
            } else {
                $housesData .= '<option value="' . $houseItem->id . '">' . $houseItem->name . '</option>'."\r\n";
            }
        }
        
        // sections
        if ($house) {
            $sectionsQuery = Section::find();
            if ($house) {
                $sectionsQuery = $house->getSections();
            }
            $sections = $sectionsQuery->all();
            foreach ($sections as $sectionItem) {
                if ($section && $sectionItem->id == $section->id) {
                    $sectionsData .= '<option value="' . $sectionItem->id . '" selected="selected">' . $sectionItem->name . '</option>'."\r\n";
                } else {
                    $sectionsData .= '<option value="' . $sectionItem->id . '">' . $sectionItem->name . '</option>'."\r\n";
                }
            }
        }
        
        // floors
        if ($house) {
            $floorsQuery = Floor::find();
            if ($house) {
                $floorsQuery = $house->getFloors();
            }
            $floors = $floorsQuery->all();
            foreach ($floors as $floorItem) {
                if ($floor && $floorItem->id == $floor->id) {
                    $floorsData .= '<option value="' . $floorItem->id . '" selected="selected">' . $floorItem->name . '</option>'."\r\n";
                } else {
                    $floorsData .= '<option value="' . $floorItem->id . '">' . $floorItem->name . '</option>'."\r\n";
                }
            }
        }
        
        // flats
        if ($house || $section || $floor) {
            $flatsQuery = Flat::find();
            if ($house) {
                $flatsQuery = $house->getFlats();
            }
            if ($section) {
                $flatsQuery->andWhere(['section_id' => $section->id]);
            }
            if ($floor) {
                $flatsQuery->andWhere(['floor_id' => $floor->id]);
            }
            $flats = $flatsQuery->all();
            foreach ($flats as $flatItem) {
                if ($flat && $flatItem->id == $flat->id) {
                    $flatsData .= '<option value="' . $flatItem->id . '" selected="selected">' . $flatItem->flat . '</option>'."\r\n";
                } else {
                    $flatsData .= '<option value="' . $flatItem->id . '">' . $flatItem->flat . '</option>'."\r\n";
                }
            }
        } elseif ($flat) {
            $flatsData .= '<option value="' . $flat->id . '" selected="selected">' . $flat->flat . '</option>'."\r\n";
        }
        
        return [
            'flat' => $flat,
            'account' => $account,
            'user' => $user,
            'tariff' => $tariff,
            'flats' => $flatsData,
            'tariffs' => $tariffsData,
            'houses' => $housesData,
            'sections' => $sectionsData,
            'floors' => $floorsData,
        ];
    }

    /**
     * Finds the Flat model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Flat the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Flat::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
