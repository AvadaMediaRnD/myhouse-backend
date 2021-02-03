<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\Service;
use common\models\House;
use common\models\Section;
use common\models\Floor;
use common\models\UserAdmin;
use common\models\CounterData;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\CounterDataSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Счетчики');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <!--<div class="col-xs-12 col-sm-6">-->
    <!--<h2 class="page-header">Квартиры</h2>-->
    <!--</div>-->
    <div class="col-xs-12">
        <?php /*
        <div class="btn-group pull-right margin-bottom">
            <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Выберите действие <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                <li><a href="<?= Yii::$app->urlManager->createUrl(['/counter-data/create']) ?>">Добавить показание</a></li>
                <li><a href="<?= Yii::$app->urlManager->createUrl(['/counter-data/filter']) ?>">Открыть отчет по владельцам</a></li>
            </ul>
        </div>
        */ ?>
        
        <div class="pull-right margin-bottom">
            <a class="btn btn-success" href="<?= Yii::$app->urlManager->createUrl(['/counter-data/create']) ?>">Добавить показание</a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title"></h3>
                <div class="box-tools">
                    <a href="<?= Yii::$app->urlManager->createUrl(['/counter-data/counters']) ?>" class="btn btn-default btn-sm">
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
                        'data-href' => Yii::$app->urlManager->createUrl(['/counter-data/counter-list', 'CounterDataSearch[flat_id]' => $model->flat_id, 'CounterDataSearch[service_id]' => $model->service_id]),
                    ];
                },
                'columns' => [
//                    [
//                        'attribute' => 'uid',
//                        'label' => '№',
//                        // 'filter' => false,
//                        'enableSorting' => false,
//                        'headerOptions' => ['style' => 'width: 125px; min-width: 125px'],
//                    ],
//                    [
//                        'attribute' => 'status',
//                        'format' => 'raw',
//                        'value' => function ($model) {
//                            return $model->getStatusLabelHtml();
//                        },
//                        'filter' => \kartik\select2\Select2::widget([
//                            'data' => CounterData::getStatusOptions(),
//                            'model' => $searchModel,
//                            'attribute' => 'status',
//                            'theme' => \kartik\select2\Select2::THEME_DEFAULT,
//                            'options' => [
//                                'placeholder' => '',
//                            ],
//                            'pluginOptions' => ['minimumResultsForSearch' => -1, 'dropdownAutoWidth' => true],
//                        ]),
//                        'enableSorting' => false,
//                    ],
//                    [
//                        'attribute' => 'searchUidDate',
//                        'label' => 'Дата',
//                        'value' => function ($model) {
//                            return date(Yii::$app->params['dateFormat'], strtotime($model->uid_date));
//                        },
//                        'filter' => DateRangePicker::widget([
//                            'model' => $searchModel,
//                            'attribute' => 'searchUidDateRange',
//                            'convertFormat' => true,
//                            'pluginOptions' => [
//                                'timePicker' => false,
//                                'locale' => ['format' => 'd.m.Y']
//                            ]  
//                        ]),
//                        'headerOptions' => ['style' => 'width: 125px; min-width: 125px'],
//                    ],
                    [
                        'attribute' => 'searchHouse',
                        'label' => 'Дом',
                        'value' => function ($model) {
                            return $model->flat->house->name;
                        },
                        'headerOptions' => ['style' => 'min-width: 200px'],
                        'filter' => \kartik\select2\Select2::widget([
                            'data' => House::getOptions(),
                            'model' => $searchModel,
                            'attribute' => 'searchHouse',
                            'theme' => \kartik\select2\Select2::THEME_DEFAULT,
                            'options' => [
                                'placeholder' => '',
                            ],
                            'pluginOptions' => ['minimumResultsForSearch' => -1, 'dropdownAutoWidth' => true],
                        ]),
                    ],
                    [
                        'attribute' => 'searchSection',
                        'label' => 'Секция',
                        'value' => function ($model) {
                            return $model->flat->section->name;
                        },
                        'headerOptions' => ['style' => 'min-width: 160px'],
                        'filter' => \kartik\select2\Select2::widget([
                            'data' => ArrayHelper::map(Section::find()->andWhere(['house_id' => $searchModel->searchHouse])->all(), 'id', 'name'),
                            'model' => $searchModel,
                            'attribute' => 'searchSection',
                            'theme' => \kartik\select2\Select2::THEME_DEFAULT,
                            'options' => [
                                'placeholder' => $searchModel->searchHouse ? '' : Yii::t('app', 'Выберите дом'),
                            ],
                            'pluginOptions' => ['minimumResultsForSearch' => -1, 'dropdownAutoWidth' => true],
                        ]),
                        'filterInputOptions' => ['class' => 'form-control', 'id' => null, 'prompt' => $searchModel->searchHouse ? '' : Yii::t('app', 'Выберите дом')],
                    ],
                    [
                        'attribute' => 'searchFlat',
                        'label' => '№ квартиры',
                        'value' => function ($model) {
                            return $model->flat->flat;
                        },
                        'headerOptions' => ['style' => 'width: 110px; min-width: 110px'],
                    ],
        //            [
        //                'attribute' => 'searchFloor',
        //                'label' => 'Этаж',
        //                'value' => function ($model) {
        //                    return $model->flat->floor->name;
        //                },
        //                'headerOptions' => ['style' => 'width: 160px; min-width: 65px'],
        //                'filter' => ArrayHelper::map(Floor::find()->andWhere(['house_id' => $searchModel->searchHouse])->all(), 'id', 'name'),
        //                'filterInputOptions' => ['class' => 'form-control', 'id' => null, 'prompt' => $searchModel->searchHouse ? '' : Yii::t('app', 'Выберите дом')],
        //            ],
//                    [
//                        'attribute' => 'counterDataLast.amount_total',
//                        'label' => 'Предыдущие показания',
//                        'filter' => false,
//                        'enableSorting' => false,
//                        'headerOptions' => ['style' => 'width: 150px; min-width: 150px'],
//                    ],
                    [
                        'attribute' => 'service_id',
                        'label' => 'Счетчик',
                        'filter' => \kartik\select2\Select2::widget([
                            'data' => Service::getOptions(null, true),
                            'model' => $searchModel,
                            'attribute' => 'service_id',
                            'theme' => \kartik\select2\Select2::THEME_DEFAULT,
                            'options' => [
                                'placeholder' => '',
                            ],
                            'pluginOptions' => ['minimumResultsForSearch' => -1, 'dropdownAutoWidth' => true],
                        ]),
                        'enableSorting' => false,
                        'value' => function ($model) {
                            return $model->service->name;
                        },
                    ],
                    [
                        'attribute' => 'amount_total',
                        'label' => 'Текущие показания',
                        'filter' => false,
                        'enableSorting' => false,
                        'headerOptions' => ['style' => 'width: 90px; min-width: 90px'],
                        'contentOptions' => ['style' => 'background-color: #DFD5; font-weight: normal'],
                    ],
//                    [
//                        'label' => 'Расход',
//                        'value' => function ($model) {
//                            return $model->amount_total - $model->counterDataLast->amount_total;
//                        },
//                        'filter' => false,
//                        'enableSorting' => false,
//                        'headerOptions' => ['style' => 'width: 80px; min-width: 80px'],
//                    ],
                    [
                        'attribute' => 'searchServiceUnit',
                        'label' => 'Ед. изм.',
                        'value' => function ($model) {
                            return $model->service->serviceUnit->name;
                        },
                        'filter' => false,
                        'enableSorting' => false,
                        'headerOptions' => ['style' => 'width: 90px; min-width: 90px'],
                        'contentOptions' => ['style' => 'background-color: #DFD5; font-weight: normal'],
                    ],
                    
//                    [
//                        'attribute' => 'user_admin_id',
//                        'label' => 'Пользователь',
//                        'value' => function ($model) {
//                            return $model->userAdmin->fullname;
//                        },
//                        'headerOptions' => ['style' => 'min-width: 200px'],
//                        'filter' => \kartik\select2\Select2::widget([
//                            'data' => ArrayHelper::map(UserAdmin::find()->all(), 'id', 'fullname'),
//                            'model' => $searchModel,
//                            'attribute' => 'user_admin_id',
//                            'theme' => \kartik\select2\Select2::THEME_DEFAULT,
//                            'options' => [
//                                'placeholder' => '',
//                            ],
//                        ]),
//                        'enableSorting' => false,
//                    ],

                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '<div class="btn-group pull-right">{register} {details}</div>',
                        'headerOptions' => ['style' => 'width: 85px; min-width: 85px'],
                        'buttons' => [
                            'register' => function ($url, $model, $key) {
                                return Html::a('<i class="fa fa-dashboard"></i>',
                                    [
                                        '/counter-data/create',
                                        'flat_id' => $model->flat_id,
                                        'service_id' => $model->service_id,
                                    ],
                                    ['class' => 'btn btn-default btn-sm', 'target' => '_blank', 'data-toggle' => 'tooltip', 'title' => 'Снять новое показание счетчика']
                                );
                            },
                            'details' => function ($url, $model, $key) {
                                return Html::a('<i class="fa fa-eye"></i>',
                                    [
                                        '/counter-data/counter-list',
                                        'CounterDataSearch[flat_id]' => $model->flat_id,
                                        'CounterDataSearch[service_id]' => $model->service_id,
                                    ],
                                    ['class' => 'btn btn-default btn-sm', 'data-toggle' => 'tooltip', 'title' => 'Открыть историю показаний для счетчика']
                                );
                            },
                        ]
                    ],
                ],
            ]); ?>
            
        </div>
    </div>
</div>
