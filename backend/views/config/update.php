<?php

use yii\helpers\Html;

/* @var $this yii\web\View */


$this->title = Yii::t('app', 'Редактирование: {nameAttribute}', [
    'nameAttribute' => $model->key,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Настройки'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Редактирование');
?>
<div class="config-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
