<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use common\models\House;
use common\models\Section;
use common\models\Floor;
use common\models\Riser;
use common\models\User;
use common\helpers\PriceHelper;
use common\models\Flat;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\FlatSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Квартиры');
$this->params['breadcrumbs'][] = $this->title;

$userOptions = ArrayHelper::map(User::find()->joinWith('flats')->andWhere(['is not', 'flat.id', null])->andWhere(['in', 'flat.house_id', Yii::$app->user->identity->getHouseIds()])->all(), 'id', 'fullname');
$userOptions[0] = 'Неназначенные';
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
                <li><a href="<?= Yii::$app->urlManager->createUrl(['/flat/create']) ?>">Добавить квартиру</a></li>
            </ul>
        </div> 
        */ ?>
        
        <div class="pull-right margin-bottom">
            <a class="btn btn-success" href="<?= Yii::$app->urlManager->createUrl(['/flat/create']) ?>">Добавить квартиру</a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title"></h3>
                <div class="box-tools">
                    <a href="<?= Yii::$app->urlManager->createUrl(['/flat/index']) ?>" class="btn btn-default btn-sm">
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
                        'data-href' => Yii::$app->urlManager->createUrl(['/flat/view', 'id' => $model['id']]),
                    ];
                },
                'columns' => [
                    /*[
                        'class' => 'yii\grid\SerialColumn',
                        'headerOptions' => ['style' => 'width: 40px; min-width: 40px'],
                    ],*/

                    [
                        'attribute' => 'flat',
                        'headerOptions' => ['style' => 'width: 120px; min-width: 120px'],
                    ],
                    [
                        'attribute' => 'house_id',
                        'value' => function ($model) {
                            return $model->house->name;
                        },
                        'headerOptions' => ['style' => 'min-width: 200px'],
                        'filter' => \kartik\select2\Select2::widget([
                            'data' => House::getOptions(),
                            'model' => $searchModel,
                            'attribute' => 'house_id',
                            'theme' => \kartik\select2\Select2::THEME_DEFAULT,
                            'options' => [
                                'placeholder' => '',
                            ],
                            'pluginOptions' => ['minimumResultsForSearch' => -1, 'dropdownAutoWidth' => true],
                        ]),
                    ],
                    [
                        'attribute' => 'section_id',
                        'value' => function ($model) {
                            return $model->section->name;
                        },
                        'headerOptions' => ['style' => 'width: 160px; min-width: 65px'],
                        // 'filter' => ArrayHelper::map(Section::find()->andWhere(['house_id' => $searchModel->house_id])->all(), 'id', 'name'),
                        'filter' => \kartik\select2\Select2::widget([
                            'data' => ArrayHelper::map(Section::find()->andWhere(['house_id' => $searchModel->house_id])->all(), 'id', 'name'),
                            'model' => $searchModel,
                            'attribute' => 'section_id',
                            'theme' => \kartik\select2\Select2::THEME_DEFAULT,
                            'options' => [
                                'placeholder' => $searchModel->house_id ? '' : Yii::t('app', 'Выберите дом'),
                            ],
                            'pluginOptions' => ['minimumResultsForSearch' => -1, 'dropdownAutoWidth' => true],
                        ]),
                        'filterInputOptions' => ['class' => 'form-control', 'id' => null, 'prompt' => $searchModel->house_id ? '' : Yii::t('app', 'Выберите дом')],
                    ],
                    [
                        'attribute' => 'floor_id',
                        'value' => function ($model) {
                            return $model->floor->name;
                        },
                        'headerOptions' => ['style' => 'width: 160px; min-width: 65px'],
                        // 'filter' => ArrayHelper::map(Floor::find()->andWhere(['house_id' => $searchModel->house_id])->all(), 'id', 'name'),
                        'filter' => \kartik\select2\Select2::widget([
                            'data' => ArrayHelper::map(Floor::find()->andWhere(['house_id' => $searchModel->house_id])->all(), 'id', 'name'),
                            'model' => $searchModel,
                            'attribute' => 'floor_id',
                            'theme' => \kartik\select2\Select2::THEME_DEFAULT,
                            'options' => [
                                'placeholder' => $searchModel->house_id ? '' : Yii::t('app', 'Выберите дом'),
                            ],
                            'pluginOptions' => ['minimumResultsForSearch' => -1, 'dropdownAutoWidth' => true],
                        ]),
                        'filterInputOptions' => ['class' => 'form-control', 'id' => null, 'prompt' => $searchModel->house_id ? '' : Yii::t('app', 'Выберите дом')],
                    ],
                    /*
                    [
                        'attribute' => 'riser_id',
                        'value' => function ($model) {
                            return $model->riser->name;
                        },
                        'headerOptions' => ['style' => 'width: 160px; min-width: 65px'],
                        // 'filter' => ArrayHelper::map(Riser::find()->andWhere(['house_id' => $searchModel->house_id])->all(), 'id', 'name'),
                        'filter' => \kartik\select2\Select2::widget([
                            'data' => ArrayHelper::map(Riser::find()->andWhere(['house_id' => $searchModel->house_id])->all(), 'id', 'name'),
                            'model' => $searchModel,
                            'attribute' => 'riser_id',
                            'theme' => \kartik\select2\Select2::THEME_DEFAULT,
                            'options' => [
                                'placeholder' => $searchModel->house_id ? '' : Yii::t('app', 'Выберите дом'),
                            ],
                            'pluginOptions' => ['minimumResultsForSearch' => -1, 'dropdownAutoWidth' => true],
                        ]),
                        'filterInputOptions' => ['class' => 'form-control', 'id' => null, 'prompt' => $searchModel->house_id ? '' : Yii::t('app', 'Выберите дом')],
                    ],
                    */
                    [
                        'attribute' => 'user_id',
                        'value' => function ($model) {
                            return $model->user->fullname;
                        },
                        'headerOptions' => ['style' => 'min-width: 200px'],
                        'filter' => \kartik\select2\Select2::widget([
                            'data' => $userOptions,
                            'model' => $searchModel,
                            'attribute' => 'user_id',
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
                            if (!$model->account) {
                                return '(нет счета)';
                            }
                            $balance = $model->account->getBalance();
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
                                        '/flat/update',
                                        'id' => $model->id,
                                    ],
                                    ['class' => 'btn btn-default btn-sm', 'data-toggle' => 'tooltip', 'title' => 'Редактировать']
                                );
                            },
                            'delete' => function ($url, $model, $key) {
                                return Html::a('<i class="fa fa-trash"></i>',
                                    [
                                        '/flat/delete',
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
                <div>Количество квартир: <span class="text-bold"><?= Flat::find()->count() ?></span></div>
            </div>
            
        </div>
    </div>
</div>
