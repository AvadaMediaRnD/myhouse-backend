<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\InvoiceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Квитанции');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="invoice-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

<!--    <p>-->
<!--        --><?//= Html::a(Yii::t('app', 'Добавить квитанцию'), ['create'], ['class' => 'btn btn-success']) ?>
<!--    </p>-->

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'period_start',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'period_start',
                    'type' => DatePicker::TYPE_INPUT,
                    'pluginOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd'
                    ]
                ]),
            ],
            [
                'attribute' => 'period_end',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'period_end',
                    'type' => DatePicker::TYPE_INPUT,
                    'pluginOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd'
                    ]
                ]),
            ],
            'price',
            [
                'attribute' => 'status',
                'value' => function ($model) {
                    return $model->getStatusLabel();
                },
                'filter' => $searchModel::getStatusOptions(),
            ],
            [
                'attribute' => 'searchCreated',
                'value' => function ($model) {
                    return $model->created;
                },
                'label' => Yii::t('model', 'Добавлен'),
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'searchCreated',
                    'type' => DatePicker::TYPE_INPUT,
                    'pluginOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd'
                    ]
                ]),
            ],
            // 'updated_at',
            [
                'format' => 'html',
                'attribute' => 'searchFlat',
                'value' => function ($model) {
                    return ($model->flat)
                        ? ('№' . $model->flat->flat . '<br/>' . $model->flat->house->name)
                        : null;
                },
                'label' => Yii::t('model', 'Квартира'),
            ],
            [
                'attribute' => 'searchFullname',
                'value' => function ($model) {
                    return ($model->flat && $model->flat->user)
                        ? $model->flat->user->getFullname()
                        : null;
                },
                'label' => Yii::t('model', 'Владелец квартир'),
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
            ],
        ],
    ]); ?>
</div>
