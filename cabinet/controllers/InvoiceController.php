<?php

namespace cabinet\controllers;

use Yii;
use common\models\Invoice;
use common\models\InvoiceTemplate;
use cabinet\models\InvoiceSearch;
use cabinet\controllers\ZController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * InvoiceController implements the CRUD actions for Invoice model.
 */
class InvoiceController extends ZController
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
     * Lists all Invoice models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new InvoiceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Invoice model.
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
     * Creates a new Invoice model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Invoice();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Invoice model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }
    
    /**
     * @param integer $id
     * @return mixed
     */
    public function actionPrint($id)
    {
        $model = $this->findModel($id);

        return $this->renderPartial('print', ['model' => $model]);
    }

    /**
     * Deletes an existing Invoice model.
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
     * Ajax delete many models by ids
     * 
     * @return mixed
     */
    public function actionAjaxDeleteMany()
    {
        $ids = Yii::$app->request->post('ids');
        
        Invoice::deleteAll(['in', 'id', $ids]);
        
        // Yii::$app->session->addFlash('success', 'Данные удалены');
        
        return [
            'ids' => $ids,
            'status' => 'success',
            'message' => 'Данные удалены',
        ];
    }
    
    /**
     * 
     * @return mixed
     */
    public function actionDownload($id)
    {
        $model = $this->findModel($id);
        $template = InvoiceTemplate::find()->orderBy(['is_default' => SORT_DESC])->one();
        if ($template) {
            $invoiceFilePath = $model->getTemplateFile($template, 'pdf');
            return Yii::$app->response->sendFile(Yii::getAlias('@frontend/web').$invoiceFilePath);
        }
    }

    /**
     * Finds the Invoice model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Invoice the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Invoice::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
