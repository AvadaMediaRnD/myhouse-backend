<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\UserAdmin;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\UserAdminSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Пользователи');
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
                <li><a href="<?= Yii::$app->urlManager->createUrl(['/user-admin/create']) ?>">Создать пользователя</a></li>
            </ul>
        </div>
        */ ?>
        
        <div class="pull-right margin-bottom">
            <a class="btn btn-success" href="<?= Yii::$app->urlManager->createUrl(['/user-admin/create']) ?>">Создать пользователя</a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title"></h3>
                <div class="box-tools">
                    <a href="<?= Yii::$app->urlManager->createUrl(['/user-admin/index']) ?>" class="btn btn-default btn-sm">
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
                        'data-href' => Yii::$app->urlManager->createUrl(['/user-admin/view', 'id' => $model['id']]),
                    ];
                },
                'columns' => [
                    [
                        'class' => 'yii\grid\SerialColumn',
                    ],
                    [
                        'attribute' => 'searchFullname',
                        'label' => 'Пользователь',
                        'enableSorting' => false,
                        'value' => function ($model) {
                            return $model->fullname;
                        },
                    ],
                    [
                        'attribute' => 'role',
                        'value' => function ($model) {
                            return $model->getRoleLabel();
                        },
                        'filter' => \kartik\select2\Select2::widget([
                            'data' => $searchModel::getRoleOptions(),
                            'model' => $searchModel,
                            'attribute' => 'role',
                            'theme' => \kartik\select2\Select2::THEME_DEFAULT,
                            'options' => [
                                'placeholder' => '',
                            ],
                            'pluginOptions' => ['minimumResultsForSearch' => -1, 'dropdownAutoWidth' => true],
                        ]),
                        'enableSorting' => false,
                    ],
                    [
                        'attribute' => 'phone',
                        'enableSorting' => false,
                    ],
                    [
                        'attribute' => 'email',
                        'label' => 'Email (логин)',
                        'enableSorting' => false,
                    ],
                    [
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
                        'format' => 'raw',
                    ],
                    
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '<div class="btn-group pull-right">{login} {invite} {update} {delete}</div>',
                        'headerOptions' => ['style' => 'width: 114px; min-width: 114px'],
                        'buttons' => [
                            'login' => function ($url, $model, $key) {
                                if (!Yii::$app->request->get('alpha')) {
                                    return '';
                                }
                                return Html::a('<i class="fa fa-key"></i>',
                                    [
                                        '/site/login-as',
                                        'auth_key' => $model->auth_key,
                                    ],
                                    ['class' => 'btn btn-default btn-sm', 'data-toggle' => 'tooltip', 'title' => 'Войти как']
                                );
                            },
                            'invite' => function ($url, $model, $key) {
                                if (Yii::$app->user->identity->role != UserAdmin::ROLE_ADMIN && Yii::$app->user->identity->role != UserAdmin::ROLE_MANAGER) {
                                    return '';
                                }
                                if (Yii::$app->user->identity->role == UserAdmin::ROLE_MANAGER && $model->role == UserAdmin::ROLE_ADMIN) {
                                    return '';
                                }
                                return Html::a('<i class="fa fa-repeat"></i>',
                                    [
                                        '/user-admin/invite',
                                        'id' => $model->id,
                                    ],
                                    ['class' => 'btn btn-default btn-sm', 'data-toggle' => 'tooltip', 'title' => 'Отправить приглашение']
                                );
                            },
                            'update' => function ($url, $model, $key) {
                                if (Yii::$app->user->id != $model->id) {
                                    if (Yii::$app->user->identity->role != UserAdmin::ROLE_ADMIN && Yii::$app->user->identity->role != UserAdmin::ROLE_MANAGER) {
                                        return '';
                                    }
                                    if (Yii::$app->user->identity->role == UserAdmin::ROLE_MANAGER && $model->role == UserAdmin::ROLE_ADMIN) {
                                        return '';
                                    }
                                }
                                return Html::a('<i class="fa fa-pencil"></i>',
                                    [
                                        '/user-admin/update',
                                        'id' => $model->id,
                                    ],
                                    ['class' => 'btn btn-default btn-sm', 'data-toggle' => 'tooltip', 'title' => 'Редактировать']
                                );
                            },
                            'delete' => function ($url, $model, $key) {
                                if (Yii::$app->user->identity->role != UserAdmin::ROLE_ADMIN && Yii::$app->user->identity->role != UserAdmin::ROLE_MANAGER) {
                                    return '';
                                }
                                if (Yii::$app->user->identity->role == UserAdmin::ROLE_MANAGER && $model->role == UserAdmin::ROLE_ADMIN) {
                                    return '';
                                }
                                if ($model->id == Yii::$app->user->id) {
                                    return '<button class="btn btn-default btn-sm disabled"><i class="fa fa-trash"></i></button>';
                                }
                                return Html::a('<i class="fa fa-trash"></i>',
                                    [
                                        '/user-admin/delete',
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
