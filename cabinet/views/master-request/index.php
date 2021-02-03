<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\date\DatePicker;
use common\models\MasterRequest;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\MasterRequestSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Вызов мастера');
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
                <li><a href="<?= Yii::$app->urlManager->createUrl(['/master-request/create']) ?>">Создать заявку</a></li>
            </ul>
        </div>
        */ ?>
        
        <div class="pull-right margin-bottom">
            <a class="btn btn-success" href="<?= Yii::$app->urlManager->createUrl(['/master-request/create']) ?>">Создать заявку</a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => null,
                'tableOptions'=>['class'=>'table table-bordered table-hover table-striped'],
                'layout'=> "<div class=\"box-body table-responsive no-padding\">{items}</div>\n<div class=\"box-footer clearfix\">{pager}</div>",
                'pager' => [
                    'class' => 'yii\widgets\LinkPager',
                    'options' => ['class' => 'pagination pagination-sm no-margin pull-right'],
                ],
                'columns' => [
                    [
                        'attribute' => 'id',
                        'label' => '№ заявки',
                        'enableSorting' => false,
                        'headerOptions' => ['style' => 'width: 80px'],
                    ],
                    [
                        'attribute' => 'type',
                        'enableSorting' => false,
                        'value' => function ($model) {
                            return $model->getTypeLabel();
                        }
                    ],
                    [
                        'attribute' => 'description',
                        'enableSorting' => false,
                        'value' => function ($model) {
                            return $model->getDescriptionShort(100);
                        }
                    ],
                    [
                        'attribute' => 'date_request',
                        'label' => 'Удобное время',
                        'enableSorting' => false,
                        'value' => function ($model) {
                            return $model->getDatetimeRequest();
                        },
                        'headerOptions' => ['style' => 'width: 125px; min-width: 125px;']
                    ],
                    [
                        'attribute' => 'status',
                        'enableSorting' => false,
                        'format' => 'raw',
                        'value' => function ($model) {
                            return $model->getStatusLabelHtml();
                        },
                        'headerOptions' => ['style' => 'width: 60px; min-width: 60px'],
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'headerOptions' => ['style' => 'width: 48px; min-width: 48px'],
                        'template' => '<div class="btn-group pull-right">{delete}</div>',
                        'buttons' => [
                            'delete' => function ($url, $model, $key) {
                                if ($model->status != MasterRequest::STATUS_NEW) {
                                    return '<button class="btn btn-default btn-sm disabled"><i class="fa fa-trash"></i></button>';
                                }
                                return Html::a('<i class="fa fa-trash"></i>',
                                    [
                                        '/master-request/delete',
                                        'id' => $model->id,
                                    ],
                                    ['class' => 'btn btn-default btn-sm', 'data-pjax' => 0, 'data-method' => 'post', 'data-confirm' => 'Вы уверены, что хотите удалить этот элемент?']
                                );
                            },
                        ]
                    ],
                ],
            ]);
            ?>
            
        </div>
    </div>
</div>
