<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\MessageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Сообщения');
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
    <!--<div class="col-xs-12 col-sm-6">-->
    <!--<h2 class="page-header">Владельцы квартир</h2>-->
    <!--</div>-->
    <div class="col-xs-12">
        <?php /*
        <div class="btn-group pull-right margin-bottom">
            <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Выберите действие <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                <li><a href="<?= Yii::$app->urlManager->createUrl(['/message/create']) ?>">Отправить сообщение</a></li>
            </ul>
        </div>
        */ ?>
        
        <div class="pull-right margin-bottom">
            <a class="btn btn-success" href="<?= Yii::$app->urlManager->createUrl(['/message/create']) ?>">Отправить сообщение</a>
        </div>
    </div>
</div>
<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title"></h3>

        <div class="box-tools pull-right">
            <div class="has-feedback">
                <form action="" method="get">
                    <input type="text" name="search" value="<?= Yii::$app->request->get('search') ?>" class="form-control input-sm" placeholder="Поиск">
                    <span class="glyphicon glyphicon-search form-control-feedback"></span>
                </form>
            </div>
        </div>
    </div>
    <div class="box-body no-padding">
                
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => null,
            'tableOptions' => ['class'=>'table table-hover table-striped linkedRow'],
            // 'showHeader' => false,
            'layout' => "<div class=\"mailbox-controls\"> 
                    <button type=\"button\" class=\"btn btn-default btn-sm checkbox-toggle\"><i class=\"fa fa-square-o\"></i></button>
                    <button type=\"button\" class=\"btn btn-default btn-sm delete-many\"><i class=\"fa fa-trash-o\"></i></button>
                    <div class=\"pull-right\">{pager}</div>
                </div>
                
                <div class=\"table-responsive mailbox-messages\">
                    {items}
                </div>
                    
                <div class=\"mailbox-controls\"> 
                    <button type=\"button\" class=\"btn btn-default btn-sm checkbox-toggle\"><i class=\"fa fa-square-o\"></i></button>
                    <button type=\"button\" class=\"btn btn-default btn-sm delete-many\"><i class=\"fa fa-trash-o\"></i></button>
                    <div class=\"pull-right\">{pager}</div>
                </div>",
            'pager' => [
                'class' => 'yii\widgets\LinkPager',
                'options' => ['class' => 'pagination pagination-sm no-margin pull-right'],
            ],
            'rowOptions' => function ($model, $index, $widget, $grid) {
                return [
                    'data-href' => Yii::$app->urlManager->createUrl(['/message/view', 'id' => $model['id']]),
                ];
            },
            'columns' => [
                [
                    'class' => 'yii\grid\CheckboxColumn',
                    'header' => false,
                ],

    //            'id',
                [
                    'attribute' => 'name',
                    'label' => 'Получатели',
                    'format' => 'raw',
                    'enableSorting' => false,
                    'headerOptions' => ['style' => 'min-width: 200px'],
                    'value' => function ($model) {
                        $to = '';
                        $address = $model->messageAddress;
                        if ($address) {
                            $to = $address->getAddressLabel();
                        }
                        if (!$to) {
                            $to = 'Всем';
                        }
                        $addressUserId = $address->user_id;
            
                        $content = '<a href="'.Yii::$app->urlManager->createUrl(['/message/view', 'id' => $model->id]).'">';
//                        if ($model->getIsUserView($addressUserId)) {
//                            $content .= '<b>';
//                        }
                        $content .= $to;
//                        if ($model->getIsUserView($addressUserId)) {
//                            $content .= '</b>';
//                        }
                        $content .= '</a>';
                        return $content;
                    }
                ],
                [
                    'attribute' => 'description',
                    'format' => 'raw',
                    'enableSorting' => false,
                    'value' => function ($model) {
                        $descriptionStrip = strip_tags($model->description);
                        $descriptionPreview = mb_substr($descriptionStrip, 0, 48);
                        if (strlen($descriptionPreview) < strlen($descriptionStrip)) {
                            $descriptionPreview .= '...';
                        }
                        return '<b>'.$model->name.'</b>' . ' - ' . $descriptionPreview;
                    }
                ],
//                [
//                    'attribute' => 'type',
//                    'value' => function ($model) {
//                        return $model->getTypeLabel();
//                    },
//                    'filter' => $searchModel::getTypeOptions(),
//                ],
                [
                    'attribute' => 'searchCreated',
                    'value' => function ($model) {
                        return $model->created;
                    },
                    'label' => Yii::t('model', 'Дата'),
                    'enableSorting' => false,
                    'headerOptions' => ['style' => 'width: 135px; min-width: 135px'],
                ],
//                [
//                    'attribute' => 'status',
//                    'value' => function ($model) {
//                        return $model->getStatusLabel();
//                    },
//                    'filter' => $searchModel::getStatusOptions(),
//                ],
//                [
//                    'format' => 'html',
//                    'attribute' => 'searchMessageAddress',
//                    'value' => function ($model) {
//                        $addresses = $model->messageAddresses;
//                        $labels = \yii\helpers\ArrayHelper::getColumn($addresses, 'addressLabel');
//                        return implode(',<br/>', $labels);
//                    },
//                    'label' => Yii::t('model', 'Кому'),
//                ],
                // 'created_at',
                // 'updated_at',
            ],
        ]); ?>
     
</div>
    
<?php 
$urlDelete = Yii::$app->urlManager->createUrl(['/message/ajax-delete-many']);
$this->registerJs(<<<JS
    //Enable iCheck plugin for checkboxes
    //iCheck for checkbox and radio inputs
    $('.mailbox-messages input[type="checkbox"]').iCheck({
        checkboxClass: 'icheckbox_flat-blue',
        radioClass: 'iradio_flat-blue'
    });

    //Enable check and uncheck all functionality
    $(".checkbox-toggle").click(function () {
        var clicks = $(this).data('clicks');
        if (clicks) {
            //Uncheck all checkboxes
            $(".mailbox-messages input[type='checkbox']").iCheck("uncheck");
            $(".checkbox-toggle > .fa").removeClass("fa-check-square-o").addClass('fa-square-o');
        } else {
            //Check all checkboxes
            $(".mailbox-messages input[type='checkbox']").iCheck("check");
            $(".checkbox-toggle > .fa").removeClass("fa-square-o").addClass('fa-check-square-o');
        }
        $(this).data("clicks", !clicks);
    });
    
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
JS
); ?>
