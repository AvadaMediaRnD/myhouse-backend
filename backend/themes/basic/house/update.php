<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \common\models\House */
/* @var $modelForm \backend\models\HouseForm */


$this->title = $model->name ?: ('Дом ' . $model->id);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Дома'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>

<div class="box">
    <div class="box-body">
        <?= $this->render('_form', [
            'model' => $model,
            'modelForm' => $modelForm,
        ]) ?>
    </div>
</div>

