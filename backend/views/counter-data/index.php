<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\CounterDataSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Counter Datas');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="counter-data-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Counter Data'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'uid',
            'uid_date',
            'amount',
            'created_at',
            //'updated_at',
            //'status',
            //'flat_id',
            //'user_admin_id',
            //'service_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
