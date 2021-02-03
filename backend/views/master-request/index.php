<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\MasterRequestSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Заявки вызова мастера');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="master-request-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Добавить заявку'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'description',
                'value' => function ($model) {
                    return $model->getDescriptionShort();
                }
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
                'attribute' => 'searchUsername',
                'value' => function ($model) {
                    return ($model->flat && $model->flat->user)
                        ? $model->flat->user->username
                        : null;
                },
                'label' => Yii::t('model', 'Телефон'),
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
            ],
        ],
    ]); ?>
</div>
