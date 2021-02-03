<?php
namespace backend\controllers;

use common\models\Flat;
use common\models\House;
use common\models\Invoice;
use common\models\MasterRequest;
use common\models\User;
use common\models\UserAdmin;
use common\models\AccountTransaction;
use common\models\Account;
use Yii;
use yii\filters\AccessControl;
//use yii\web\Controller;
use backend\controllers\ZController as Controller;
use backend\models\LoginForm;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

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
                        'actions' => ['login', 'error', 'glide', 'test'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index', 'login-as'],
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
    public function beforeAction($action) {
        $actions = $this->getAllowedActions();
        $actions[] = 'logout';
        $actions[] = 'login-as';
        if (!Yii::$app->user->can(UserAdmin::PERMISSION_SITE) && !in_array($action->id, $actions)) {
            // return $this->redirect(['/site/error']);
            return $this->redirect(['/user-admin/update-my'])->send();
            // throw new \yii\web\ForbiddenHttpException(Yii::t('app', static::FORBIDDEN_HTTP_MESSAGE));
        }
        return parent::beforeAction($action);
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

    public function actionIndex($dateFrom = null, $dateTo = null, $houseId = null)
    {
        $houseIds = Yii::$app->user->identity->getHouseIds();
        
        $usersNewCount = User::find()
            ->where(['status' => User::STATUS_NEW])
            ->joinWith('flats.house')
            ->andWhere(['or', ['in', 'house.id', $houseIds], ['is', 'house.id', null]])
            ->distinct()
            ->count();
        $usersActiveCount = User::find()
            ->where(['status' => User::STATUS_ACTIVE])
            ->joinWith('flats.house')
            ->andWhere(['or', ['in', 'house.id', $houseIds], ['is', 'house.id', null]])
            ->distinct()
            ->count();
        $accountsCount = Account::find()
            ->joinWith('flat.house')
            ->andWhere(['or', ['in', 'house.id', $houseIds], ['is', 'house.id', null]])
            ->distinct()
            ->count();
        $housesCount = House::find()->andWhere(['in', 'id', $houseIds])->count();
        $flatsCount = Flat::find()->andWhere(['in', 'house_id', $houseIds])->count();
        $masterRequestsNewCount = MasterRequest::find()
            ->where(['status' => MasterRequest::STATUS_NEW])
            ->joinWith('flat.house')
            ->andWhere(['in', 'house.id', $houseIds])
            ->count();
        $masterRequestsActiveCount = MasterRequest::find()
            ->where(['status' => MasterRequest::STATUS_PROCESSING])
            ->joinWith('flat.house')
            ->andWhere(['in', 'house.id', $houseIds])
            ->count();

        $debtTotal = array_sum(ArrayHelper::getColumn(Invoice::find()
            ->andWhere(['invoice.status' => Invoice::STATUS_UNPAID])
            ->joinWith('flat.house')
            ->andWhere(['in', 'house.id', $houseIds])
            ->all(), 'price'));
        $debtMonth = array_sum(ArrayHelper::getColumn(Invoice::find()
            ->andWhere(['invoice.status' => Invoice::STATUS_UNPAID])
            ->andWhere(['>=', '`invoice`.`uid_date`', date('Y-m-d', strtotime('-1 month', time()))])
            ->joinWith('flat.house')
            ->andWhere(['in', 'house.id', $houseIds])
            ->all(), 'price'));
        $debtQuarter = array_sum(ArrayHelper::getColumn(Invoice::find()
            ->andWhere(['invoice.status' => Invoice::STATUS_UNPAID])
            ->andWhere(['>=', '`invoice`.`uid_date`', date('Y-m-d', strtotime('-3 month', time()))])
            ->joinWith('flat.house')
            ->andWhere(['in', 'house.id', $houseIds])
            ->all(), 'price'));
        
        $accountsDebtTotal = Account::getBalanceDebtTotal();
        
        $accountsBalance = Account::getBalanceTotal();
        $cashboxBalance = Account::getCashboxBalance();
        
        $chartDataBarDebt = []; //[65, 59, 80, 81, 56, 55, 40, 80, 81, 56, 55, 40]
        $chartDataBarPay = []; //[28, 48, 40, 19, 86, 27, 90, 40, 19, 86, 27, 90]
        $chartDataBar2In = []; //[65, 59, 80, 81, 56, 55, 40, 80, 81, 56, 55, 40]
        $chartDataBar2Out = []; //[28, 48, 40, 19, 86, 27, 90, 40, 19, 86, 27, 90]
        
        $chartLabelsArea = [];

        for ($i = 1; $i <= 12; $i++) { // for ($i = 11; $i >= 0; $i--) {
//            $tsFrom = strtotime(date('Y-m-01')." -$i months");
//            $tsTo = strtotime(date('Y-m-01').' -'.($i-1).' months');
            $tsFrom = strtotime(date('Y-'.sprintf('%02d', $i).'-01'));
            $tsTo = ($i == 12 ? strtotime(date('Y-01-01').' +1 year') : strtotime(date('Y-'.sprintf('%02d', ($i+1)).'-01')));
            
            $chartLabelsArea[] = Yii::$app->formatter->asDate($tsFrom, 'LLL, yyyy');
            
            $dateFrom = date('Y-m-d', $tsFrom);
            $dateTo = date('Y-m-d', $tsTo);
            
            $periodInvoiceDebt = array_sum(ArrayHelper::getColumn(Invoice::find()
                ->andWhere(['invoice.status' => Invoice::STATUS_UNPAID])
                ->andWhere(['and', ['>=', 'uid_date', $dateFrom], ['<', 'uid_date', $dateTo]])
                ->joinWith('flat.house')
                ->andWhere(['in', 'house.id', $houseIds])
                ->all(), 'price'));
            $periodInvoicePay = array_sum(ArrayHelper::getColumn(Invoice::find()
                ->andWhere(['invoice.status' => Invoice::STATUS_PAID])
                ->andWhere(['and', ['>=', 'uid_date', $dateFrom], ['<', 'uid_date', $dateTo]])
                ->joinWith('flat.house')
                ->andWhere(['in', 'house.id', $houseIds])
                ->all(), 'price'));
            
            $periodTransactionIn = Account::getCashboxIn($dateFrom, $dateTo);
            $periodTransactionOut = Account::getCashboxOut($dateFrom, $dateTo);
            
            $chartDataBarDebt[] = $periodInvoiceDebt;
            $chartDataBarPay[] = $periodInvoicePay;
            
            $chartDataBar2In[] = $periodTransactionIn;
            $chartDataBar2Out[] = $periodTransactionOut;
        }
            
        return $this->render('index', [
            'usersNewCount' => $usersNewCount,
            'usersActiveCount' => $usersActiveCount,
            'accountsCount' => $accountsCount,
            'housesCount' => $housesCount,
            'flatsCount' => $flatsCount,
            'masterRequestsNewCount' => $masterRequestsNewCount,
            'masterRequestsActiveCount' => $masterRequestsActiveCount,
            'debtTotal' => $debtTotal,
            'debtMonth' => $debtMonth,
            'debtQuarter' => $debtQuarter,
            'accountsBalance' => $accountsBalance,
            'accountsDebtTotal' => $accountsDebtTotal,
            'cashboxBalance' => $cashboxBalance,
            'chartLabelsArea' => json_encode($chartLabelsArea),
            'chartDataBarDebt' => json_encode($chartDataBarDebt),
            'chartDataBarPay' => json_encode($chartDataBarPay),
            'chartDataBar2In' => json_encode($chartDataBar2In),
            'chartDataBar2Out' => json_encode($chartDataBar2Out),
            'chartLabelsFrom' => $chartLabelsArea[0],
            'chartLabelsTo' => $chartLabelsArea[count($chartLabelsArea) - 1],
        ]);
    }

    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect(['/site/index']);
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }
    
    /**
     * 
     * @param string $auth_key
     * @return mixed
     */
    public function actionLoginAs($auth_key = '')
    {
        $user = UserAdmin::findIdentityByAccessToken($auth_key);
        if ($user) {
            Yii::$app->user->logout();
            Yii::$app->user->login($user);
        }
        return $this->redirect(['index']);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
    
    public function actionTest()
    {
        /* @var $oneC \common\components\OneCComponent */
//        $oneC = Yii::$app->oneC;
//        $oneC->updateData();
        die('Test done.');
    }
}
