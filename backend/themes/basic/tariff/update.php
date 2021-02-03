<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Account */

$this->title = 'Тариф' . ($model->name ? ': ' . $model->name : '');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Тарифы'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>

<div class="box">
    <div class="box-body">
        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
    </div>
</div>
