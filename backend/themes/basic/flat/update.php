<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \common\models\Flat */
/* @var $modelForm \backend\models\FlatForm */

$this->title = 'Квартира' . 
    ($model->flat ? (' №' . $model->flat . ($model->house->name ? (', ' . $model->house->name) : '')) : '');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Квартиры'), 'url' => ['index']];
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
