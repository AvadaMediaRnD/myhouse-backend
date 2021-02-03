<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\Account;
use common\models\AccountTransaction;
use common\models\House;
use common\models\Section;
use common\models\User;
use common\helpers\PriceHelper;
use common\models\TransactionPurpose;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\AccountSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Касса');
$this->params['breadcrumbs'][] = $this->title;

$exportUrlParams = Yii::$app->request->queryParams;
array_unshift($exportUrlParams, '/account-transaction/export');
?>

<div class="row">
    <!--<div class="col-xs-12 col-sm-6">-->
    <!--<h2 class="page-header">Квартиры</h2>-->
    <!--</div>-->
    <div class="col-xs-12 col-lg-9">
        
        <div class="row">
            <div class="col-xs-12 col-md-4">
                <div class="small-box bg-green overflow-hidden">
                    <div class="inner">
                        <h3><?= PriceHelper::format(Account::getCashboxBalance(), true, true) ?></h3>
                        <p>Состояние кассы</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-dollar"></i>
                    </div>
                    <!--<span class="small-box-footer">&nbsp;</span>-->
                </div>
            </div>
            <div class="col-xs-12 col-md-4">
                <div class="small-box bg-blue overflow-hidden">
                    <div class="inner">
                        <h3><?= PriceHelper::format(Account::getBalanceTotal(), true, true) ?></h3>
                        <p>Баланс по счетам</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-dollar"></i>
                    </div>
                    <!--<span class="small-box-footer">&nbsp;</span>-->
                </div>
            </div>
            <div class="col-xs-12 col-md-4">
                <div class="small-box bg-red overflow-hidden">
                    <div class="inner">
                        <h3><?= PriceHelper::format(Account::getBalanceDebtTotal(), true, true) ?></h3>
                        <p>Задолженность по счетам</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-dollar"></i>
                    </div>
                    <!--<span class="small-box-footer">&nbsp;</span>-->
                </div>
            </div>
            <?php /* ?>
            <div class="col-xs-12 col-md-6">
                <div class="small-box bg-green overflow-hidden">
                    <div class="inner">
                        <h3><?= PriceHelper::format(Account::getInsTotal(), true, true) ?></h3>
                        <p>Общий приход</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-dollar"></i>
                    </div>
                    <!--<span class="small-box-footer">&nbsp;</span>-->
                </div>
            </div>
            <div class="col-xs-12 col-md-6">
                <div class="small-box bg-red overflow-hidden">
                    <div class="inner">
                        <h3><?= PriceHelper::format(Account::getOutsTotal(), true, true) ?></h3>
                        <p>Общий расход</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-dollar"></i>
                    </div>
                    <!--<span class="small-box-footer">&nbsp;</span>-->
                </div>
            </div>
            <?php */ ?>
        </div>
    </div>
    <div class="col-xs-12 col-lg-3">
        <div class="btn-group pull-right margin-bottom">
            <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Выберите действие <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                <?php /*
                <li><a href="<?= Yii::$app->urlManager->createUrl(['/account/create']) ?>">Добавить лицевой счет</a></li>
                */ ?>
                <li><a href="<?= Yii::$app->urlManager->createUrl(['/account-transaction/create', 'type' => AccountTransaction::TYPE_IN]) ?>">Создать приход</a></li>
                <li><a href="<?= Yii::$app->urlManager->createUrl(['/account-transaction/create', 'type' => AccountTransaction::TYPE_OUT]) ?>">Создать расход</a></li>
                <?php /*
                <li><a href="<?= Yii::$app->urlManager->createUrl(['/account-transaction/state']) ?>">Посмотреть состояние счета</a></li>
                */ ?>
                <li><a href="<?= Yii::$app->urlManager->createUrl($exportUrlParams) ?>">Выгрузить в Excel</a></li>
            </ul>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title"></h3>
                <div class="box-tools">
                    <a href="<?= Yii::$app->urlManager->createUrl(['/account-transaction/index']) ?>" class="btn btn-default btn-sm">
                        <span class="hidden-xs">Очистить</span><i class="fa fa-eraser visible-xs" aria-hidden="true"></i>
                    </a>
                </div>
            </div>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'tableOptions'=>['class'=>'table table-bordered table-hover table-striped linkedRow'],
                'layout'=> "<div class=\"box-body table-responsive no-padding\">{items}</div>\n<div class=\"box-footer clearfix\">{pager}</div>",
                'pager' => [
                    'class' => 'yii\widgets\LinkPager',
                    'options' => ['class' => 'pagination pagination-sm no-margin pull-right'],
                ],
                'rowOptions' => function ($model, $index, $widget, $grid) {
                    return [
                        'data-href' => Yii::$app->urlManager->createUrl(['/account-transaction/view', 'id' => $model['id']]),
                    ];
                },
                'columns' => [
                    [
                        'attribute' => 'uid',
                        'label' => '№',
                        // 'filter' => false,
                        'enableSorting' => false,
                        'headerOptions' => ['style' => 'width: 125px; min-width: 125px'],
                    ],
                    [
                        'attribute' => 'searchUidDate',
                        'value' => function ($model) {
                            return $model->getUidDate();
                        },
                        'label' => Yii::t('model', 'Дата'),
//                        'filter' => DatePicker::widget([
//                            'model' => $searchModel,
//                            'attribute' => 'searchUidDate',
//                            'type' => DatePicker::TYPE_INPUT,
//                            'pluginOptions' => [
//                                'autoclose' => true,
//                                'format' => 'dd.mm.yyyy'
//                            ]
//                        ]),
                        'filter' => DateRangePicker::widget([
                            'model' => $searchModel,
                            'attribute' => 'searchUidDateRange',
                            'convertFormat' => true,
                            'pluginOptions' => [
                                'timePicker' => false,
                                'locale' => ['format' => 'd.m.Y']
                            ]  
                        ]),
                        'headerOptions' => ['style' => 'width: 125px; min-width: 125px'],
                    ],
                    
                    [
                        'attribute' => 'status',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return $model->getStatusLabel();
                        },
                        'filter' => \kartik\select2\Select2::widget([
                            'data' => AccountTransaction::getStatusOptions(),
                            'model' => $searchModel,
                            'attribute' => 'status',
                            'theme' => \kartik\select2\Select2::THEME_DEFAULT,
                            'options' => [
                                'placeholder' => '',
                            ],
                            'pluginOptions' => ['minimumResultsForSearch' => -1, 'dropdownAutoWidth' => true],
                        ]),
                        'headerOptions' => ['style' => 'width: 120px; min-width: 120px'],
                        'enableSorting' => false,
                    ],
                    
                    [
                        'attribute' => 'transaction_purpose_id',
                        'value' => function ($model) {
                            return $model->transactionPurpose->name;
                        },
                        'filter' => \kartik\select2\Select2::widget([
                            'data' => TransactionPurpose::getOptions(),
                            'model' => $searchModel,
                            'attribute' => 'transaction_purpose_id',
                            'theme' => \kartik\select2\Select2::THEME_DEFAULT,
                            'options' => [
                                'placeholder' => '',
                            ],
                            'pluginOptions' => ['minimumResultsForSearch' => -1, 'dropdownAutoWidth' => true],
                        ]),
                        'enableSorting' => false,
                    ],
//                    [
//                        'attribute' => 'searchInvoice',
//                        'label' => 'Квитанция',
//                        'value' => function ($model) {
//                            if (!$model->invoice) {
//                                return '';
//                            }
//                            return $model->invoice->uid . ' от ' . $model->invoice->getUidDate();
//                        },
//                        'enableSorting' => false,
//                    ],
                    
                    /*[
                        'attribute' => 'searchFullname',
                        'value' => function ($model) {
                            return ($model->account && $model->account->flat && $model->account->flat->user)
                                ? $model->account->flat->user->getFullname()
                                : null;
                        },
                        'label' => Yii::t('model', 'Владелец квартиры'),
                    ],*/
                    [
                        'attribute' => 'searchUserId',
                        'value' => function ($model) {
                            return ($model->account && $model->account->flat && $model->account->flat->user)
                                ? $model->account->flat->user->getFullname()
                                : null;
                        },
                        'label' => Yii::t('model', 'Владелец'),
                        'headerOptions' => ['style' => 'min-width: 200px'],
                        'filter' => \kartik\select2\Select2::widget([
                            'data' => ArrayHelper::map(User::find()->joinWith('flats')->andWhere(['is not', 'flat.id', null])->andWhere(['in', 'flat.house_id', Yii::$app->user->identity->getHouseIds()])->all(), 'id', 'fullname'),
                            'model' => $searchModel,
                            'attribute' => 'searchUserId',
                            'theme' => \kartik\select2\Select2::THEME_DEFAULT,
                            'options' => [
                                'placeholder' => '',
                            ],
                        ]),
                        'filterInputOptions' => ['class' => 'form-control', 'id' => null],
                    ],
                    [
                        'attribute' => 'searchAccountUid',
                        'headerOptions' => ['style' => 'min-width: 160px'],
                        'label' => 'Лицевой счет',
                        'enableSorting' => false,
                        'value' => function ($model) {
                            return $model->account->uid;
                        }
                    ],
                    
                    [
                        'attribute' => 'type',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return $model->getTypeLabelHtml();
                        },
                        'filter' => \kartik\select2\Select2::widget([
                            'data' => AccountTransaction::getTypeOptions(),
                            'model' => $searchModel,
                            'attribute' => 'type',
                            'theme' => \kartik\select2\Select2::THEME_DEFAULT,
                            'options' => [
                                'placeholder' => '',
                            ],
                            'pluginOptions' => ['minimumResultsForSearch' => -1, 'dropdownAutoWidth' => true],
                        ]),
                        'enableSorting' => false,
                    ],
                    [
                        'attribute' => 'amount',
                        'label' => 'Сумма (грн)',
                        'filter' => false,
                        'enableSorting' => false,
                        'format' => 'raw',
                        'value' => function ($model) {
                            $textClass = $model->type == AccountTransaction::TYPE_OUT ? 'text-red' : 'text-green';
                            $minus = $model->type == AccountTransaction::TYPE_OUT ? '-' : '';
                            return '<span class="'.$textClass.'">' . $minus . PriceHelper::format($model->amount) . '</span>';
                        },
                        'headerOptions' => ['style' => 'width: 110px; min-width: 110px'],
                    ],
                    /*
                    [
                        'attribute' => 'currency_id',
                        'filter' => false,
                        'enableSorting' => false,
                        'value' => function ($model) {
                            return $model->currency->code;
                        }
                    ],
                    */
                    
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '<div class="btn-group pull-right">{update} {delete}</div>',
                        'headerOptions' => ['style' => 'width: 80px; min-width: 80px'],
                        'buttons' => [
                            'update' => function ($url, $model, $key) {
                                return Html::a('<i class="fa fa-pencil"></i>',
                                    [
                                        '/account-transaction/update',
                                        'id' => $model->id,
                                    ],
                                    ['class' => 'btn btn-default btn-sm', 'data-toggle' => 'tooltip', 'title' => 'Редактировать']
                                );
                            },
                            'delete' => function ($url, $model, $key) {
                                if ($model->account && $model->account->status == Account::STATUS_DISABLED) {
                                    return '<button class="btn btn-default btn-sm disabled" data-confirm="Счет платежа неактивен, нельзя удалить платеж"><i class="fa fa-trash"></i></button>';
                                }
                                return Html::a('<i class="fa fa-trash"></i>',
                                    [
                                        '/account-transaction/delete',
                                        'id' => $model->id,
                                    ],
                                    ['class' => 'btn btn-default btn-sm', 'data-toggle' => 'tooltip', 'title' => 'Удалить', 'data-pjax' => 0, 'data-method' => 'post', 'data-confirm' => 'Вы уверены, что хотите удалить этот элемент?']
                                );
                            },
                        ]
                    ],
                ],
            ]); ?>
            
            <div class="box-footer">
                <?php if (!$searchModel->type || $searchModel->type == AccountTransaction::TYPE_IN) { ?>
                    <div>Проведен приход: <span class="text-bold"><?= PriceHelper::format(Account::getInsTotal($searchModel), true, true) ?></span></div>
                <?php } ?>
                <?php if (!$searchModel->type || $searchModel->type == AccountTransaction::TYPE_OUT) { ?>
                    <div>Проведен расход: <span class="text-bold"><?= PriceHelper::format(Account::getOutsTotal($searchModel), true, true) ?></span></div>
                <?php } ?>
            </div>
            
        </div>
    </div>
</div>
