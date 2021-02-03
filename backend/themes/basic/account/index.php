<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\Account;
use common\models\AccountTransaction;
use common\models\House;
use common\models\Section;
use common\models\User;
use common\helpers\PriceHelper;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\AccountSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Лицевые счета');
$this->params['breadcrumbs'][] = $this->title;

$exportUrlParams = Yii::$app->request->queryParams;
array_unshift($exportUrlParams, '/account/export');
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
        </div>
    </div>
    <div class="col-xs-12 col-lg-3">
        <div class="btn-group pull-right margin-bottom">
            <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Выберите действие <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                <li><a href="<?= Yii::$app->urlManager->createUrl(['/account/create']) ?>">Добавить лицевой счет</a></li>
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
                    <a href="<?= Yii::$app->urlManager->createUrl(['/account/index']) ?>" class="btn btn-default btn-sm">
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
                        'data-href' => Yii::$app->urlManager->createUrl(['/account/view', 'id' => $model['id']]),
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
                        'attribute' => 'status',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return $model->getStatusLabelHtml();
                        },
                        'filter' => \kartik\select2\Select2::widget([
                            'data' => Account::getStatusOptions(),
                            'model' => $searchModel,
                            'attribute' => 'status',
                            'theme' => \kartik\select2\Select2::THEME_DEFAULT,
                            'options' => [
                                'placeholder' => '',
                            ],
                            'pluginOptions' => ['minimumResultsForSearch' => -1, 'dropdownAutoWidth' => true],
                        ]),
                        'enableSorting' => false,
                    ],
                    [
                        'attribute' => 'searchFlat',
                        'label' => 'Квартира',
                        'value' => function ($model) {
                            return $model->flat->flat;
                        },
                        'headerOptions' => ['style' => 'width: 100px; min-width: 100px'],
                    ],
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
                        'headerOptions' => ['style' => 'width: 160px; min-width: 65px'],
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
                    /*[
                        'attribute' => 'searchFullname',
                        'value' => function ($model) {
                            return ($model->flat && $model->flat->user)
                                ? $model->flat->user->getFullname()
                                : null;
                        },
                        'label' => Yii::t('model', 'Владелец'),
                    ],*/
                    [
                        'attribute' => 'searchUserId',
                        'value' => function ($model) {
                            return $model->flat->user->fullname;
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
                        'attribute' => 'searchBalance',
                        'filter' => \kartik\select2\Select2::widget([
                            'data' => [1 => 'Есть долг', 0 => 'Нет долга'],
                            'model' => $searchModel,
                            'attribute' => 'searchHasDebt',
                            'theme' => \kartik\select2\Select2::THEME_DEFAULT,
                            'options' => [
                                'placeholder' => '',
                            ],
                            'pluginOptions' => ['minimumResultsForSearch' => -1, 'dropdownAutoWidth' => true],
                        ]),
                        'format' => 'html',
                        'value' => function ($model) {
                            $balance = $model->getBalance();
                            $label = ($balance < 0) ? 'text-red' : ($balance > 0 ? 'text-green' : 'text-default');
                            return '<span class="'.$label.'">' . PriceHelper::format($balance) . '</span>';
                        },
                        'label' => Yii::t('model', 'Остаток (грн)'),
                        'headerOptions' => ['style' => 'width: 120px; min-width: 120px'],
                    ],

                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '<div class="btn-group pull-right">{update} {delete}</div>',
                        'headerOptions' => ['style' => 'width: 80px; min-width: 80px'],
                        'buttons' => [
                            'update' => function ($url, $model, $key) {
                                return Html::a('<i class="fa fa-pencil"></i>',
                                    [
                                        '/account/update',
                                        'id' => $model->id,
                                    ],
                                    ['class' => 'btn btn-default btn-sm', 'data-toggle' => 'tooltip', 'title' => 'Редактировать']
                                );
                            },
                            'delete' => function ($url, $model, $key) {
                                return Html::a('<i class="fa fa-trash"></i>',
                                    [
                                        '/account/delete',
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
                <div>Количество счетов: <span class="text-bold"><?= Account::find()->count() ?></span></div>
            </div>
            
        </div>
    </div>
</div>
