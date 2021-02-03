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
<div class="message-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Добавить сообщение'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

//            'id',
            'name',
            'description',
            [
                'attribute' => 'type',
                'value' => function ($model) {
                    return $model->getTypeLabel();
                },
                'filter' => $searchModel::getTypeOptions(),
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
            [
                'attribute' => 'status',
                'value' => function ($model) {
                    return $model->getStatusLabel();
                },
                'filter' => $searchModel::getStatusOptions(),
            ],
            [
                'format' => 'html',
                'attribute' => 'searchMessageAddress',
                'value' => function ($model) {
                    $addresses = $model->messageAddresses;
                    $labels = \yii\helpers\ArrayHelper::getColumn($addresses, 'addressLabel');
                    return implode(',<br/>', $labels);
                },
                'label' => Yii::t('model', 'Кому'),
            ],
            // 'created_at',
            // 'updated_at',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
            ],
        ],
    ]); ?>
</div>
