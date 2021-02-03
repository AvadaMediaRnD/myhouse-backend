<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\Tariff;
use common\models\TariffService;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\TariffSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Тарифы');
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
                <li><a href="<?= Yii::$app->urlManager->createUrl(['/tariff/create']) ?>">Добавить тариф</a></li>
            </ul>
        </div>
        */ ?>
        
        <div class="pull-right margin-bottom">
            <a class="btn btn-success" href="<?= Yii::$app->urlManager->createUrl(['/tariff/create']) ?>">Добавить тариф</a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => null,
                'tableOptions'=>['class'=>'table table-bordered table-hover table-striped linkedRow'],
                'layout'=> "<div class=\"box-body table-responsive no-padding\">{items}</div>\n<div class=\"box-footer clearfix\">{pager}</div>",
                'pager' => [
                    'class' => 'yii\widgets\LinkPager',
                    'options' => ['class' => 'pagination pagination-sm no-margin pull-right'],
                ],
                'rowOptions' => function ($model, $index, $widget, $grid) {
                    return [
                        'data-href' => Yii::$app->urlManager->createUrl(['/tariff/view', 'id' => $model['id']]),
                    ];
                },
                'columns' => [
                    [
                        'attribute' => 'name',
                    ],
                    [
                        'attribute' => 'description',
                        'enableSorting' => false,
                    ],
                    [
                        'attribute' => 'updated_at',
                        'value' => function ($model) {
                            return $model->updated;
                        },
                        'enableSorting' => false,
                    ],
                    
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '<div class="btn-group pull-right">{copy} {update} {delete}</div>',
                        'headerOptions' => ['style' => 'width: 114px; min-width: 114px'],
                        'buttons' => [
                            'copy' => function ($url, $model, $key) {
                                return Html::a('<i class="fa fa-clone"></i>',
                                    [
                                        '/tariff/create',
                                        'tariff_id' => $model->id,
                                    ],
                                    ['class' => 'btn btn-default btn-sm', 'data-toggle' => 'tooltip', 'title' => 'Копировать']
                                );
                            },
                            'update' => function ($url, $model, $key) {
                                return Html::a('<i class="fa fa-pencil"></i>',
                                    [
                                        '/tariff/update',
                                        'id' => $model->id,
                                    ],
                                    ['class' => 'btn btn-default btn-sm', 'data-toggle' => 'tooltip', 'title' => 'Редактировать']
                                );
                            },
                            'delete' => function ($url, $model, $key) {
                                return Html::a('<i class="fa fa-trash"></i>',
                                    [
                                        '/tariff/delete',
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
