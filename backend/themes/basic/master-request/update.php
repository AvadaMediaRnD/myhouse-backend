<?php

use yii\helpers\Html;

/* @var $this yii\web\View */


$this->title = 'Заявка №' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Заявки вызова мастера'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>


        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>

