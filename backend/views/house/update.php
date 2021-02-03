<?php

use yii\helpers\Html;

/* @var $this yii\web\View */


$this->title = Yii::t('app', 'Редактирование: {nameAttribute}', [
    'nameAttribute' => $model->name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Жилые комплексы'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Редактирование');
?>
<div class="house-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
