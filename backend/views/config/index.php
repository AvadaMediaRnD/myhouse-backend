<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ConfigSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Настройки');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="config-index">

<!--    <p>-->
<!--        --><?//= Html::a(Yii::t('app', 'Добавить'), ['create'], ['class' => 'btn btn-success']) ?>
<!--    </p>-->

    <p>Описание настроек:</p>
    <ul>
        <li>test - тестовый параметр</li>
        <li>pay_card - номер карты для получения платежей</li>
        <li>... - тут будут перечислены все настройки которые может изменять владелец системы</li>
    </ul>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'key',
            'value:ntext',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update}',
            ],
        ],
    ]); ?>
</div>
