<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\date\DatePicker;
use common\models\Invoice;
use common\helpers\PriceHelper;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\InvoiceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Квитанции на оплату');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title"></h3>
                <div class="box-tools">
                    <a href="<?= Yii::$app->urlManager->createUrl(['/invoice/index', 'InvoiceSearch[flat_id]' => Yii::$app->request->get('InvoiceSearch') ? Yii::$app->request->get('InvoiceSearch')['flat_id'] : null]) ?>" class="btn btn-default btn-sm">
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
                        'attribute' => 'uid',
                        'label' => '№',
                        'filter' => false,
                        'enableSorting' => false,
                    ],
                    [
                        'attribute' => 'searchUidDate',
                        'value' => function ($model) {
                            return date(Yii::$app->params['dateFormat'], strtotime($model->uid_date));
                        },
                        'label' => Yii::t('model', 'Дата'),
                        'filter' => DatePicker::widget([
                            'model' => $searchModel,
                            'attribute' => 'searchUidDate',
                            'type' => DatePicker::TYPE_INPUT,
                            'pluginOptions' => [
                                'autoclose' => true,
                                'format' => 'dd.mm.yyyy'
                            ]
                        ]),
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
                    ],
                    [
                        'label' => 'Сумма',
                        'value' => function ($model) {
                            return PriceHelper::format($model->getPrice());
                        },
                        'filter' => false,
                        'enableSorting' => false,
                    ],
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
