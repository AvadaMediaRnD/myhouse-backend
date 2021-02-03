<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\User;
use kartik\date\DatePicker;
use yii\helpers\ArrayHelper;
use common\models\House;
use common\models\Invoice;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Владельцы квартир');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <!--<div class="col-xs-12 col-sm-6">-->
    <!--<h2 class="page-header">Владельцы квартир</h2>-->
    <!--</div>-->
    <div class="col-xs-12">
        <div class="btn-group pull-right margin-bottom">
            <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Выберите действие <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                <li><a href="<?= Yii::$app->urlManager->createUrl(['/user/create']) ?>">Добавить владельца квартиры</a></li>
                <li><a href="<?= Yii::$app->urlManager->createUrl([
                    '/message/create',
                    'MessageAddress[user_has_debt]' => 1,
                    'MessageAddress[house_id]' => Yii::$app->request->get('UserSearch')['searchHouse'] ?: null,
                ]) ?>">Отправить сообщение должникам</a></li>
                <li><a href="<?= Yii::$app->urlManager->createUrl(['/user/invite']) ?>">Отправить приглашение</a></li>
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
                    <a href="<?= Yii::$app->urlManager->createUrl(['/user/index']) ?>" class="btn btn-default btn-sm">
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
                        // 'id' => $model['id'], 
                        // 'onclick' => 'location.href="' . Yii::$app->urlManager->createUrl(['/user/view', 'id' => $model['id']]) . '"',
                        'data-href' => Yii::$app->urlManager->createUrl(['/user/view', 'id' => $model['id']]),
                    ];
                },
                'columns' => [
                    // ['class' => 'yii\grid\SerialColumn'],
                    [
                        'attribute' => 'uid',
                        // 'filter' => false,
                        'enableSorting' => false,
                        'headerOptions' => ['style' => 'width: 110px; min-width: 110px'],
                    ],
                    [
                        'attribute' => 'searchFullname',
                        'label' => Yii::t('model', 'ФИО'),
                        'value' => function ($model) {
                            return $model->profile->fullname;
                        }
                    ],
                    [
                        'attribute' => 'searchPhone',
                        'label' => Yii::t('model', 'Телефон'),
                        'value' => function ($model) {
                            return $model->profile->phone ? $model->profile->phone : '';
                        },
                        'headerOptions' => ['style' => 'width: 140px; min-width: 140px'],
                    ],
                    [
                        'attribute' => 'email',
                        'enableSorting' => false,
                        'headerOptions' => ['style' => 'width: 140px; min-width: 140px'],
                    ],
                    

                    [
                        'attribute' => 'searchHouse',
                        'label' => Yii::t('model', 'Дом'),
                        'value' => function ($model) {
                            $houses = ArrayHelper::getColumn($model->flats, function ($model) {
                                return '<a href="'.Yii::$app->urlManager->createUrl(['/house/view', 'id' => $model->house_id]).'">' . $model->house->name . '</a>';
                            });
                            return implode(',<br/>', $houses);
                        },
                        'headerOptions' => ['style' => 'min-width: 250px'],
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
                        'format' => 'html',
                    ],
                    [
                        'format' => 'html',
                        'attribute' => 'searchFlat',
                        'headerOptions' => ['style' => 'min-width: 300px'],
                        'value' => function ($model) {
                            $flats = ArrayHelper::getColumn($model->flats, function ($model) {
                                return '<a href="'.Yii::$app->urlManager->createUrl(['/flat/view', 'id' => $model->id]).'">' . '№' . $model->flat . ', ' . $model->house->name . '</a>';
                            });
                            return implode(',<br/>', $flats);
                        },
                        'label' => Yii::t('model', 'Квартира'),
                    ],
                    [
                        'attribute' => 'searchCreatedDate',
                        'label' => Yii::t('model', 'Добавлен'),
                        'headerOptions' => ['style' => 'width: 110px; min-width: 110px'],
                        'value' => function ($model) {
                            return $model->createdDate;
                        },
                        'filter' => DatePicker::widget([
                            'model' => $searchModel,
                            'attribute' => 'searchCreatedDate',
                            'type' => DatePicker::TYPE_INPUT,
                            'pluginOptions' => [
                                'autoclose' => true,
                                'format' => 'dd.mm.yyyy'
                            ]
                        ]),
//                        'filter' => DateRangePicker::widget([
//                            'model' => $searchModel,
//                            'attribute' => 'searchCreatedDateRange',
//                            'convertFormat' => true,
//                            'pluginOptions' => [
//                                'timePicker' => false,
//                                'locale' => ['format' => 'd.m.Y']
//                            ]  
//                        ]),
                    ],
                    [
                        'format' => 'html',
                        'attribute' => 'status',
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
                    ],
                    [
                        'format' => 'html',
                        'attribute' => 'searchHasDebt',
                        'headerOptions' => ['style' => 'width: 80px; min-width: 80px'],
                        'value' => function ($model) {
                            $debt = array_sum(ArrayHelper::getColumn($model->flats, 'debt'));
                            return $debt ? '<span class="">Да</span>' : '';
                        },
                        'label' => Yii::t('model', 'Есть долг'),
                        'filter' => \kartik\select2\Select2::widget([
                            'data' => [1 => 'Да'],
                            'model' => $searchModel,
                            'attribute' => 'searchHasDebt',
                            'theme' => \kartik\select2\Select2::THEME_DEFAULT,
                            'options' => [
                                'placeholder' => '',
                            ],
                            'pluginOptions' => ['minimumResultsForSearch' => -1, 'dropdownAutoWidth' => true],
                        ]),
                    ],
                    // 'status',
                    // 'created_at',
                    // 'updated_at',

                    [
                        'class' => 'yii\grid\ActionColumn',
                        'headerOptions' => ['style' => 'width: 114px; min-width: 114px'],
                        'template' => '<div class="btn-group pull-right">{send_message} {update} {delete}</div>',
                        'buttons' => [
                            'send_message' => function ($url, $model, $key) {
                                return Html::a('<i class="fa fa-envelope"></i>',
                                    [
                                        '/message/create',
                                        'MessageAddress[user_id]' => $model->id,
                                    ],
                                    ['class' => 'btn btn-default btn-sm', 'data-toggle' => 'tooltip', 'title' => 'Отправить сообщение']
                                );
                            },
                            'update' => function ($url, $model, $key) {
                                return Html::a('<i class="fa fa-pencil"></i>',
                                    [
                                        '/user/update',
                                        'id' => $model->id,
                                    ],
                                    ['class' => 'btn btn-default btn-sm', 'data-toggle' => 'tooltip', 'title' => 'Редактировать']
                                );
                            },
                            'delete' => function ($url, $model, $key) {
                                return Html::a('<i class="fa fa-trash"></i>',
                                    [
                                        '/user/delete',
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
                <div>Количество владельцев: <span class="text-bold"><?= User::find()->count() ?></span></div>
            </div>
            
        </div>
    </div>
</div>
