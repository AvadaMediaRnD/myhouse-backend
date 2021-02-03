<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\date\DatePicker;
use common\models\MasterRequest;
use common\models\User;
use common\models\UserAdmin;
use yii\helpers\ArrayHelper;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\MasterRequestSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Заявки вызова мастера');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <!--<div class="col-xs-12 col-sm-6">-->
    <!--<h2 class="page-header">Вызов мастера</h2>-->
    <!--</div>-->
    <div class="col-xs-12">
        <?php /*
        <div class="btn-group pull-right margin-bottom">
            <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Выберите действие <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                <li><a href="<?= Yii::$app->urlManager->createUrl(['/master-request/create']) ?>">Добавить заявку</a></li>
            </ul>
        </div>
        */ ?>
        
        <div class="pull-right margin-bottom">
            <a class="btn btn-success" href="<?= Yii::$app->urlManager->createUrl(['/master-request/create']) ?>">Добавить заявку</a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title"></h3>
                <div class="box-tools">
                    <a href="<?= Yii::$app->urlManager->createUrl(['/master-request/index']) ?>" class="btn btn-default btn-sm">
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
                        'data-href' => Yii::$app->urlManager->createUrl(['/master-request/view', 'id' => $model['id']]),
                    ];
                },
                'columns' => [
                    [
                        'attribute' => 'id',
                        'label' => '№ заявки',
                        'headerOptions' => ['style' => 'width: 100px; min-width: 100px'],
                    ],
                    [
                        'attribute' => 'searchDateRequest',
                        'value' => function ($model) {
                            return $model->getDatetimeRequest();
                        },
                        'label' => Yii::t('model', 'Удобное время'),
                        'filter' => DateRangePicker::widget([
                            'model' => $searchModel,
                            'attribute' => 'searchDateRequestRange',
                            'convertFormat' => true,
                            'pluginOptions' => [
                                'timePicker' => false,
                                'locale' => ['format' => 'd.m.Y']
                            ]  
                        ]),
                        'headerOptions' => ['style' => 'width: 135px; min-width: 135px'],
                    ],
                    [
                        'attribute' => 'type',
                        'value' => function ($model) {
                            return $model->getTypeLabel();
                        },
                        'headerOptions' => ['style' => 'min-width: 125px'],
                        'filter' => \kartik\select2\Select2::widget([
                            'data' => MasterRequest::getTypeOptions(),
                            'model' => $searchModel,
                            'attribute' => 'type',
                            'theme' => \kartik\select2\Select2::THEME_DEFAULT,
                            'options' => [
                                'placeholder' => '',
                            ],
                            'pluginOptions' => ['minimumResultsForSearch' => -1, 'dropdownAutoWidth' => true],
                        ]),
                    ],
                    [
                        'attribute' => 'description',
                        'headerOptions' => ['style' => 'min-width: 350px'],
                        'value' => function ($model) {
                            return $model->getDescriptionShort(48);
                        }
                    ],
                    [
                        'format' => 'html',
                        'attribute' => 'searchFlat',
                        'value' => function ($model) {
                            $flatName = ($model->flat)
                                ? ('кв.' . $model->flat->flat . ', ' . $model->flat->house->name)
                                : null;
                            if (!$flatName) {
                                return $flatName;
                            }
                            return '<a href="'.Yii::$app->urlManager->createUrl(['/flat/view', 'id' => $model->flat_id]).'">' . $flatName . '</a>';
                        },
                        'label' => Yii::t('model', 'Квартира'),
                    ],
                    /*[
                        'attribute' => 'searchFullname',
                        'value' => function ($model) {
                            return ($model->flat && $model->flat->user)
                                ? $model->flat->user->getFullname()
                                : null;
                        },
                        'label' => Yii::t('model', 'Владелец квартир'),
                    ],*/
                    [
                        'format' => 'html',
                        'attribute' => 'searchUserId',
                        'value' => function ($model) {
                            $userName = ($model->flat->user)
                                ? $model->flat->user->getFullname()
                                : null;
                            if (!$userName) {
                                return $userName;
                            }
                            return '<a href="'.Yii::$app->urlManager->createUrl(['/user/view', 'id' => $model->flat->user_id]).'">' . $userName . '</a>';
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
                        'attribute' => 'searchPhone',
                        'value' => function ($model) {
                            return ($model->flat && $model->flat->user && $model->flat->user->profile)
                                ? $model->flat->user->profile->phone
                                : null;
                        },
                        'label' => Yii::t('model', 'Телефон'),
                    ],
                    [
                        'format' => 'html',
                        'attribute' => 'user_admin_id',
                        'value' => function ($model) {
                            if (!$model->user_admin_id) {
                                return null;
                            }
                            return '<a href="'.Yii::$app->urlManager->createUrl(['/user-admin/view', 'id' => $model->user_admin_id]).'">' . $model->userAdmin->fullname . '</a>';
                        },
                        'enableSorting' => false,
                        'headerOptions' => ['style' => 'min-width: 125px'],
                        'filter' => \kartik\select2\Select2::widget([
                            'data' => UserAdmin::getUserMasterOptions(),
                            'model' => $searchModel,
                            'attribute' => 'user_admin_id',
                            'theme' => \kartik\select2\Select2::THEME_DEFAULT,
                            'options' => [
                                'placeholder' => '',
                            ],
                            'pluginOptions' => ['minimumResultsForSearch' => -1, 'dropdownAutoWidth' => true],
                        ]),
                    ],
                    [
                        'attribute' => 'status',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return $model->getStatusLabelHtml();
                        },
                        'headerOptions' => ['style' => 'width: 135px; min-width: 135px'],
                        'filter' => \kartik\select2\Select2::widget([
                            'data' => $searchModel::getStatusOptions(true),
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
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '<div class="btn-group pull-right">{update} {delete}</div>',
                        'headerOptions' => ['style' => 'width: 80px; min-width: 80px'],
                        'buttons' => [
                            'update' => function ($url, $model, $key) {
                                return Html::a('<i class="fa fa-pencil"></i>',
                                    [
                                        '/master-request/update',
                                        'id' => $model->id,
                                    ],
                                    ['class' => 'btn btn-default btn-sm', 'data-toggle' => 'tooltip', 'title' => 'Редактировать']
                                );
                            },
                            'delete' => function ($url, $model, $key) {
                                return Html::a('<i class="fa fa-trash"></i>',
                                    [
                                        '/master-request/delete',
                                        'id' => $model->id,
                                    ],
                                    ['class' => 'btn btn-default btn-sm', 'data-toggle' => 'tooltip', 'title' => 'Удалить', 'data-pjax' => 0, 'data-method' => 'post', 'data-confirm' => 'Вы уверены, что хотите удалить этот элемент?']
                                );
                            },
                        ]
                    ],
                ],
            ]); ?>
            
        </div>
    </div>
</div>
