<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\Service;
use common\models\House;
use common\models\Section;
use common\models\Floor;
use common\models\UserAdmin;
use common\models\CounterData;
use common\models\User;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\CounterDataSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $userFlatOptions array */
/* @var $serviceOptions array */

$this->title = Yii::t('app', 'Подробный отчет');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Показания счетчиков'), 'url' => ['/counter-data/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <!--<div class="col-xs-12 col-sm-6">-->
    <!--<h2 class="page-header">Квартиры</h2>-->
    <!--</div>-->
    <div class="col-xs-12">
        <?php $form = ActiveForm::begin(['action' => Yii::$app->urlManager->createUrl(['/counter-data/filter']), 'method' => 'get', 'options' => []]) ?>
            <div class="row">
                <div class="col-md-4 col-xs-12">
                    <?= $form->field($searchModel, 'searchUser', ['template' => '{input}'])->widget(Select2::classname(), [
                        'data' => $userSelectData,
                        'language' => 'ru',
                        'theme' => Select2::THEME_DEFAULT,
                        'options' => ['placeholder' => 'Выберите владелеца...', 'class' => 'form-control', 'onchange' => 'form.submit()', 'style' => 'width: 200px'],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ])->label(false) ?>
                </div>
                <div class="col-md-4 col-xs-12">
                    <?= $form->field($searchModel, 'searchFlat', ['template' => '{input}'])->dropDownList(
                        $userFlatOptions, ['prompt' => 'Выберите квартиру...', 'class' => 'form-control', 'onchange' => 'form.submit()', 'disabled' => $userFlatOptions ? false : true]
                    )->label(false) ?>
                </div>
                <div class="col-md-4 col-xs-12">
                    <?= $form->field($searchModel, 'service_id', ['template' => '{input}'])->dropDownList(
                        $serviceOptions, ['prompt' => 'Выберите услугу...', 'class' => 'form-control', 'onchange' => 'form.submit()']
                    )->label(false) ?>
                </div>
            </div>
        <?php ActiveForm::end() ?>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Показания счетчиков</h3>
                <div class="box-tools">
                    
                </div>
            </div>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                //'filterModel' => $searchModel,
                'tableOptions'=>['class'=>'table table-bordered table-hover table-striped'],
                'layout'=> "<div class=\"box-body table-responsive no-padding\">{items}</div>\n<div class=\"box-footer clearfix\">{pager}</div>",
                'pager' => [
                    'class' => 'yii\widgets\LinkPager',
                    'options' => ['class' => 'pagination pagination-sm no-margin pull-right'],
                ],
                'rowOptions' => function ($model, $index, $widget, $grid) {
                    return [
                        'data-href' => Yii::$app->urlManager->createUrl(['/counter-data/view', 'id' => $model['id']]),
                    ];
                },
                'columns' => [
                    [
                        'attribute' => 'uid',
                        'label' => '#',
                        'filter' => false,
                        'enableSorting' => false,
                        'headerOptions' => ['style' => 'width: 125px; min-width: 125px'],
                    ],
                    [
                        'attribute' => 'searchUidDate',
                        'label' => 'Дата',
                        'value' => function ($model) {
                            return date(Yii::$app->params['dateFormat'], strtotime($model->uid_date));
                        },
                        'filter' => false,
                        'enableSorting' => false,
                        'headerOptions' => ['style' => 'width: 125px; min-width: 125px'],
                    ],
                    [
                        'label' => 'Месяц',
                        'value' => function ($model) {
                            return Yii::$app->formatter->asDate(strtotime($model->uid_date), 'LLLL, yyyy');
                            //return date('F', strtotime($model->uid_date));
                        },
                        'headerOptions' => ['style' => 'width: 90px; min-width: 90px'],
                    ],
                    [
                        'attribute' => 'service_id',
                        'label' => 'Счетчик',
                        'filter' => false,
                        'enableSorting' => false,
                        'value' => function ($model) {
                            return $model->service->name;
                        },
                    ],
                    [
                        'attribute' => 'searchFlat',
                        'label' => 'Квартира',
                        'value' => function ($model) {
                            return $model->flat->flat;
                        },
                        'filter' => false,
                        'headerOptions' => ['style' => 'width: 80px; min-width: 80px'],
                    ],
                    [
                        'attribute' => 'searchHouse',
                        'label' => 'Дом',
                        'value' => function ($model) {
                            return $model->flat->house->name;
                        },
                        'filter' => false,
                    ],
                    [
                        'attribute' => 'searchUser',
                        'label' => 'Владелец',
                        'value' => function ($model) {
                            return $model->flat->user->fullname;
                        },
                        'filter' => false,
                    ],
                    [
                        'attribute' => 'counterDataLast.amount_total',
                        'label' => 'Предыдущие показания',
                        'filter' => false,
                        'enableSorting' => false,
                        'headerOptions' => ['style' => 'width: 150px; min-width: 150px'],
                    ],
                    [
                        'attribute' => 'amount_total',
                        'label' => 'Текущие показания',
                        'filter' => false,
                        'enableSorting' => false,
                        'headerOptions' => ['style' => 'width: 150px; min-width: 150px'],
                    ],
                    [
                        'label' => 'Расход',
                        'value' => function ($model) {
                            return $model->amount_total - $model->counterDataLast->amount_total;
                        },
                        'filter' => false,
                        'enableSorting' => false,
                        'headerOptions' => ['style' => 'width: 80px; min-width: 80px'],
                    ],
                    [
                        'attribute' => 'searchServiceUnit',
                        'label' => 'Ед. изм.',
                        'value' => function ($model) {
                            return $model->service->serviceUnit->name;
                        },
                        'filter' => false,
                        'enableSorting' => false,
                        'headerOptions' => ['style' => 'width: 80px; min-width: 80px'],
                    ],
                            
                    [
                        'attribute' => 'status',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return $model->getStatusLabelHtml();
                        },
                        'filter' => false,
                        'enableSorting' => false,
                    ],
                ],
            ]); ?>
            
        </div>
    </div>
</div>
