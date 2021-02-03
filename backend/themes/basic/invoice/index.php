<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\date\DatePicker;
use common\helpers\PriceHelper;
use common\models\PayCompany;
use yii\helpers\ArrayHelper;
use common\models\User;
use kartik\daterange\DateRangePicker;
use common\models\Account;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\InvoiceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Квитанции на оплату');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <!--<div class="col-xs-12 col-sm-6">-->
    <!--<h2 class="page-header">Квитанции</h2>-->
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
        </div>
    </div>
    <div class="col-xs-12 col-lg-3">
        <div class="btn-group pull-right margin-bottom">
            <!--
            <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Выберите действие <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                <li><a href="<?php //echo Yii::$app->urlManager->createUrl(['/invoice/create']) ?>">Создать общую квитанцию</a></li>
            -->
<!--                <li><a href="#!">Создать квитанцию на оплату за ЭЭ</a></li>
                <li><a href="#!">Создать квитанцию на оплату за ГАЗ</a></li>-->
                <?php /* <li><a href="#!" class="copy-one">Копировать</a></li> */ ?>
                <!--<li><a href="#!" class="delete-many">Удалить</a></li>
            </ul>
            -->
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title"></h3>
                <div class="box-tools">
                    <a href="<?= Yii::$app->urlManager->createUrl(['/invoice/index']) ?>" class="btn btn-default btn-sm">
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
                        'data-href' => Yii::$app->urlManager->createUrl(['/invoice/view', 'id' => $model['id']]),
                    ];
                },
                'columns' => [
                    [
                        'class' => 'yii\grid\CheckboxColumn',
                        'headerOptions' => ['style' => 'width: 40px; min-width: 40px'],
                    ],

                    [
                        'attribute' => 'uid',
                        // 'filter' => false,
                        'enableSorting' => false,
                        'headerOptions' => ['style' => 'width: 125px; min-width: 125px'],
                    ],
                    [
                        'attribute' => 'status',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return $model->getStatusLabelHtml();
                        },
                        'filter' => \kartik\select2\Select2::widget([
                            'data' => $searchModel::getStatusOptions(),
                            'model' => $searchModel,
                            'attribute' => 'status',
                            'theme' => \kartik\select2\Select2::THEME_DEFAULT,
                            'options' => [
                                'placeholder' => '',
                            ],
                            'pluginOptions' => ['minimumResultsForSearch' => -1, 'dropdownAutoWidth' => true],
                        ]),
                        'headerOptions' => ['style' => 'width: 100px; min-width: 100px'],
                    ],
//                    [
//                        'attribute' => 'period_start',
//                        'filter' => DatePicker::widget([
//                            'model' => $searchModel,
//                            'attribute' => 'period_start',
//                            'type' => DatePicker::TYPE_INPUT,
//                            'pluginOptions' => [
//                                'autoclose' => true,
//                                'format' => 'yyyy-mm-dd'
//                            ]
//                        ]),
//                    ],
//                    [
//                        'attribute' => 'period_end',
//                        'filter' => DatePicker::widget([
//                            'model' => $searchModel,
//                            'attribute' => 'period_end',
//                            'type' => DatePicker::TYPE_INPUT,
//                            'pluginOptions' => [
//                                'autoclose' => true,
//                                'format' => 'yyyy-mm-dd'
//                            ]
//                        ]),
//                    ],
                    [
                        'attribute' => 'searchUidDate',
                        'value' => function ($model) {
                            return date(Yii::$app->params['dateFormat'], strtotime($model->uid_date));
                        },
                        'label' => Yii::t('model', 'Дата'),
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
                        'attribute' => 'period_end',
                        'label' => Yii::t('model', 'Месяц'),
                        'filter' => DatePicker::widget([
                            'model' => $searchModel,
                            'attribute' => 'searchMonthYear',
                            'type' => DatePicker::TYPE_INPUT,
                            'pluginOptions' => [
                                'autoclose' => true,
                                'format' => 'mm.yyyy',
                                'startView' => 'year',
                                'minViewMode' => 'months',
                            ]
                        ]),
                        'headerOptions' => ['style' => 'width: 135px; min-width: 135px'],
                        'value' => function ($model) {
                            return $model->getMonthYearPrint();
                        }
                    ],
                    
                    [
                        'format' => 'html',
                        'attribute' => 'searchFlat',
                        'value' => function ($model) {
                            return ($model->flat)
                                ? ($model->flat->flat . ', ' . $model->flat->house->name)
                                : null;
                        },
                        'label' => Yii::t('model', 'Квартира'),
                    ],
                    [
                        'attribute' => 'searchUserId',
                        'value' => function ($model) {
                            return ($model->flat && $model->flat->user)
                                ? $model->flat->user->getFullname()
                                : null;
                        },
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
                        'label' => Yii::t('model', 'Владелец'),
                    ],
                    /*[
                        'attribute' => 'pay_company_id',
                        'value' => function ($model) {
                            return $model->payCompany->name;
                        },
                        'headerOptions' => ['style' => 'min-width: 200px'],
                        'filter' => \kartik\select2\Select2::widget([
                            'data' => ArrayHelper::map(PayCompany::find()->all(), 'id', 'name'),
                            'model' => $searchModel,
                            'attribute' => 'pay_company_id',
                            'theme' => \kartik\select2\Select2::THEME_DEFAULT,
                            'options' => [
                                'placeholder' => '',
                            ],
                        ]),
                        'enableSorting' => false,
                        'label' => Yii::t('model', 'Получатель'),
                    ],*/
                    [
                        'attribute' => 'is_checked',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return $model->getIsCheckedLabel();
                        },
                        'filter' => \kartik\select2\Select2::widget([
                            'data' => [1 => 'Проведена', 0 => 'Не проведена'],
                            'model' => $searchModel,
                            'attribute' => 'is_checked',
                            'theme' => \kartik\select2\Select2::THEME_DEFAULT,
                            'options' => [
                                'placeholder' => '',
                            ],
                            'pluginOptions' => ['minimumResultsForSearch' => -1, 'dropdownAutoWidth' => true],
                        ]),
                        'headerOptions' => ['style' => 'width: 120px; min-width: 120px'],
                    ],
                    
                    [
                        'attribute' => 'price',
                        'label' => 'Сумма (грн)',
                        'filter' => false,
                        'enableSorting' => false,
                        'value' => function ($model) {
                            return PriceHelper::format($model->getPrice());
                        },
                        'headerOptions' => ['style' => 'width: 100px; min-width: 100px'],
                    ],
//                    [
//                        'attribute' => 'searchCreated',
//                        'value' => function ($model) {
//                            return $model->created;
//                        },
//                        'label' => Yii::t('model', 'Добавлен'),
//                        'filter' => DatePicker::widget([
//                            'model' => $searchModel,
//                            'attribute' => 'searchCreated',
//                            'type' => DatePicker::TYPE_INPUT,
//                            'pluginOptions' => [
//                                'autoclose' => true,
//                                'format' => 'yyyy-mm-dd'
//                            ]
//                        ]),
//                    ],
                    // 'updated_at',
                    
                                /*
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '<div class="btn-group pull-right">{copy} {update} {delete}</div>',
                        'headerOptions' => ['style' => 'width: 114px; min-width: 114px'],
                        'buttons' => [
                            'copy' => function ($url, $model, $key) {
                                return Html::a('<i class="fa fa-clone"></i>',
                                    [
                                        '/invoice/create',
                                        'invoice_id' => $model->id,
                                    ],
                                    ['class' => 'btn btn-default btn-sm', 'data-toggle' => 'tooltip', 'title' => 'Копировать']
                                );
                            },
                            'update' => function ($url, $model, $key) {
                                return Html::a('<i class="fa fa-pencil"></i>',
                                    [
                                        '/invoice/update',
                                        'id' => $model->id,
                                    ],
                                    ['class' => 'btn btn-default btn-sm', 'data-toggle' => 'tooltip', 'title' => 'Редактировать']
                                );
                            },
                            'delete' => function ($url, $model, $key) {
                                return Html::a('<i class="fa fa-trash"></i>',
                                    [
                                        '/invoice/delete',
                                        'id' => $model->id,
                                    ],
                                    ['class' => 'btn btn-default btn-sm', 'data-toggle' => 'tooltip', 'title' => 'Удалить', 'data-pjax' => 0, 'data-method' => 'post', 'data-confirm' => 'Вы уверены, что хотите удалить этот элемент?']
                                );
                            },
                        ]
                    ],
                                    */
                ],
            ]); ?>
            
        </div>
    </div>
</div>

<?php 
$urlDelete = Yii::$app->urlManager->createUrl(['/invoice/ajax-delete-many']);
$urlCopy = Yii::$app->urlManager->createUrl(['/invoice/create', 'invoice_id' => '']);
$this->registerJs(<<<JS
    
    // Delete button trigger
    $('body').on('click', '.delete-many', function() {
        var ids = [];
        $('input[name="selection[]"]:checked').each(function() { 
            var v = $(this).val();
            ids.push(v);
        });
        // var idsString = ids.join(',');
        console.log(ids);
        
        if (ids.length) {
            if (confirm('Данные будут удалены. Продолжить?')) {
                $.ajax({
                    url: '{$urlDelete}',
                    data: {ids: ids},
                    type: 'post',
                    success: function (data) {
                        console.log('SUCCESS');
                        console.log(data);
                        location.reload();
                    },
                    error: function (data) {
                        console.log('ERROR');
                        console.log(data);
                    },
                });
            } else {
                console.log('Canceled');
            }
        }
    });
                    
    // Copy button trigger
    $('body').on('click', '.copy-one', function() {
        var ids = [];
        $('input[name="selection[]"]:checked').each(function() { 
            var v = $(this).val();
            ids.push(v);
        });
        // var idsString = ids.join(',');
        console.log(ids);
        
        if (ids.length) {
            document.location.href = '{$urlCopy}'+ids[0];
        }
    });
JS
); ?>
