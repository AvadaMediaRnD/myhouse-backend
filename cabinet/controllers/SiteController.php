<?php
namespace cabinet\controllers;

use common\models\Flat;
use common\models\House;
use common\models\Invoice;
use common\models\InvoiceService;
use common\models\MasterRequest;
use common\models\User;
use Yii;
use yii\filters\AccessControl;
//use yii\web\Controller;
use cabinet\controllers\ZController as Controller;
use cabinet\models\LoginForm;
use yii\filters\VerbFilter;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use common\helpers\PriceHelper;

/**
 * Site controller
 */
class SiteController extends Controller
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
                        'actions' => ['login', 'error', 'glide'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex($flat_id = null)
    {
        $user = Yii::$app->user->identity;
        if (!$flat_id) {
            if ($user->getFlats()->exists()) { 
                $flat_id = Yii::$app->user->identity->flats[0]->id;
                return $this->redirect(['index', 'flat_id' => $flat_id]);
            } else {
                return $this->redirect(['/user/view']);
            }
        }
        
        $flat = Flat::findOne($flat_id);
        if ($flat->user_id != $user->id) {
            return $this->redirect(['/site/error']);
        }
        
        $chartDataPieMonth = [];
        $chartDataPieYear = [];
        $chartDataArea = []; // [6500, 2559, 7780, 5581, 2256, 6655, 3340, 1280, 3481, 3356, 1255, 8840];
        $chartLabelsArea = [];
        $chartColorsPie = ['#dd4b39', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de'];
        
        for ($i = 1; $i <= 12; $i++) { // for ($i = 11; $i >= 0; $i--) {
//            $tsFrom = strtotime(date('Y-m-01')." -$i months");
//            $tsTo = strtotime(date('Y-m-01').' -'.($i-1).' months');
            $tsFrom = strtotime(date('Y-'.sprintf('%02d', $i).'-01'));
            $tsTo = ($i == 12 ? strtotime(date('Y-01-01').' +1 year') : strtotime(date('Y-'.sprintf('%02d', ($i+1)).'-01')));
            
            $chartLabelsArea[] = Yii::$app->formatter->asDate($tsFrom, 'LLL, yyyy');
            
            $dateFrom = date('Y-m-d', $tsFrom);
            $dateTo = date('Y-m-d', $tsTo);
            
            $chartData = array_sum(ArrayHelper::getColumn(Invoice::find()
                ->andWhere(['!=', 'invoice.status', Invoice::STATUS_DISABLED])
                ->andWhere(['>=', 'invoice.uid_date', $dateFrom])
                ->andWhere(['<=', 'invoice.uid_date', $dateTo])
                ->andWhere(['invoice.flat_id' => $flat->id])
                ->all(), 'price'));
            $chartDataArea[] = $chartData;
        }
        
        $tariffServices = $flat->tariff ? $flat->tariff->tariffServices : [];
        foreach ($tariffServices as $k => $tariffService) {
            $invoiceMonthQuery = InvoiceService::find()
                ->select(['*', new Expression('SUM(`invoice_service`.`price`) as `price_total`')])
                ->joinWith(['invoice', 'service'])
                ->andWhere(['invoice_service.service_id' => $tariffService->id])
                ->andWhere(['!=', 'invoice.status', Invoice::STATUS_DISABLED])
                ->andWhere(['>=', 'invoice.uid_date', date('Y-m-d', strtotime(date('Y-m-01') . ' -1 month'))])
                ->andWhere(['<=', 'invoice.uid_date', date('Y-m-d', strtotime(date('Y-m-01') . ' -1 day'))])
                ->andWhere(['invoice.flat_id' => $flat->id]);
            $invoiceMonth = $invoiceMonthQuery->asArray()->one();
            $invoiceYearQuery = InvoiceService::find()
                ->select(['*', new Expression('SUM(`invoice_service`.`price`) as `price_total`')])
                ->joinWith(['invoice', 'service'])
                ->andWhere(['invoice_service.service_id' => $tariffService->id])
                ->andWhere(['!=', 'invoice.status', Invoice::STATUS_DISABLED])
                ->andWhere(['>=', 'invoice.uid_date', date('Y-01-01')])
                ->andWhere(['<=', 'invoice.uid_date', date('Y-m-d')])
                ->andWhere(['invoice.flat_id' => $flat->id]);
            $invoiceYear = $invoiceYearQuery->asArray()->one();
            
            if ($invoiceMonth['price_total'] !== null) {
                $chartDataMonth = [
                    'value' => $invoiceMonth['price_total'],
                    'color' => $chartColorsPie[$k % count($chartColorsPie)],
                    'highlight' => $chartColorsPie[$k % count($chartColorsPie)],
                    'label' => $invoiceMonth['service']['name'],
                    'valueFormatted' => PriceHelper::format($invoiceMonth['price_total'], true, true),
                ];
                $chartDataPieMonth[] = $chartDataMonth;
            }
            
            if ($invoiceYear['price_total'] !== null) {
                $chartDataYear = [
                    'value' => $invoiceYear['price_total'],
                    'color' => $chartColorsPie[$k % count($chartColorsPie)],
                    'highlight' => $chartColorsPie[$k % count($chartColorsPie)],
                    'label' => $invoiceYear['service']['name'],
                    'valueFormatted' => PriceHelper::format($invoiceYear['price_total'], true, true),
                ];
                $chartDataPieYear[] = $chartDataYear;
            }
        }
        
        $showCharts = false;
        if ($chartDataPieMonth || $chartDataPieYear) {
            $showCharts = true;
        }
        
        return $this->render('index', [
            'flat' => $flat,
            'chartDataPieMonth' => json_encode($chartDataPieMonth),
            'chartDataPieYear' => json_encode($chartDataPieYear),
            'chartDataArea' => json_encode($chartDataArea),
            'chartLabelsArea' => json_encode($chartLabelsArea),
            'showCharts' => $showCharts,
        ]);
    }

    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
