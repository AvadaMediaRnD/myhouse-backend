<?php

use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = Yii::t('app', 'Редактировать');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Сообщения'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Информация'), 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="box">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
