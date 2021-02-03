<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\AccountTransaction;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\TransactionPurposeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Статьи приходов/расходов');
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
                <li><a href="<?= Yii::$app->urlManager->createUrl(['/transaction-purpose/create']) ?>">Добавить статью</a></li>
            </ul>
        </div>
        */ ?>
        
        <div class="pull-right margin-bottom">
            <a class="btn btn-success" href="<?= Yii::$app->urlManager->createUrl(['/transaction-purpose/create']) ?>">Добавить статью</a>
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
                        'data-href' => Yii::$app->urlManager->createUrl(['/transaction-purpose/update', 'id' => $model['id']]),
                    ];
                },
                'columns' => [
                    [
                        'attribute' => 'name',
                        'enableSorting' => false,
                    ],
                    [
                        'attribute' => 'type',
                        'value' => function ($model) {
                            return $model->getTypeLabelHtml();
                        },
                        'format' => 'raw',
                    ],
                    
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '<div class="btn-group pull-right">{update} {delete}</div>',
                        'headerOptions' => ['style' => 'width: 80px; min-width: 80px'],
                        'buttons' => [
                            'update' => function ($url, $model, $key) {
                                return Html::a('<i class="fa fa-pencil"></i>',
                                    [
                                        '/transaction-purpose/update',
                                        'id' => $model->id,
                                    ],
                                    ['class' => 'btn btn-default btn-sm', 'data-toggle' => 'tooltip', 'title' => 'Редактировать']
                                );
                            },
                            'delete' => function ($url, $model, $key) {
                                if ($model->id == 2) {
                                    return '<button class="btn btn-default btn-sm disabled" data-confirm="Этот элемент используется в системе. Его нельзя удалить."><i class="fa fa-trash"></i></button>';
                                }
                                return Html::a('<i class="fa fa-trash"></i>',
                                    [
                                        '/transaction-purpose/delete',
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
