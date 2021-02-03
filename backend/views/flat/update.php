<?php

use yii\helpers\Html;

/* @var $this yii\web\View */


$this->title = Yii::t('app', 'Редактирование: {nameAttribute}', [
    'nameAttribute' => $model->flat . ($model->house ? (', '.$model->house->name) : ''),
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Квартиры'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Редактирование');
?>
<div class="flat-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
