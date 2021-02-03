<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use common\models\House;
use common\models\Section;
use common\models\Floor;
use common\models\Riser;
use common\models\User;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\FlatSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Квартиры');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="flat-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Добавить квартиру'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'flat',
            [
                'attribute' => 'house_id',
                'value' => function ($model) {
                    return $model->house->name;
                },
                'filter' => ArrayHelper::map(House::find()->all(), 'id', 'name'),
            ],
            [
                'attribute' => 'section_id',
                'value' => function ($model) {
                    return $model->section->name;
                },
                'filter' => ArrayHelper::map(Section::find()->andWhere(['house_id' => $searchModel->house_id])->all(), 'id', 'name'),
                'filterInputOptions' => ['class' => 'form-control', 'id' => null, 'prompt' => Yii::t('app', 'Выберите ЖК')],
            ],
            [
                'attribute' => 'floor_id',
                'value' => function ($model) {
                    return $model->floor->name;
                },
                'filter' => ArrayHelper::map(Floor::find()->andWhere(['house_id' => $searchModel->house_id])->all(), 'id', 'name'),
                'filterInputOptions' => ['class' => 'form-control', 'id' => null, 'prompt' => Yii::t('app', 'Выберите ЖК')],
            ],
            [
                'attribute' => 'riser_id',
                'value' => function ($model) {
                    return $model->riser->name;
                },
                'filter' => ArrayHelper::map(Riser::find()->andWhere(['house_id' => $searchModel->house_id])->all(), 'id', 'name'),
                'filterInputOptions' => ['class' => 'form-control', 'id' => null, 'prompt' => Yii::t('app', 'Выберите ЖК')],
            ],
            [
                'attribute' => 'user_id',
                'value' => function ($model) {
                    return $model->user->profile->fullname;
                },
                'filter' => ArrayHelper::map(User::find()->joinWith('flats')->andWhere(['is not', 'flat.id', null])->all(), 'id', 'profile.fullname'),
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
            ],
        ],
    ]); ?>
</div>
