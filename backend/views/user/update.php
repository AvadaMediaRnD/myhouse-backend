<?php

use yii\helpers\Html;

/* @var $this yii\web\View */



$this->title = Yii::t('app', 'Редактирование: {nameAttribute}', [
    'nameAttribute' => $model->getFullname(),
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Владельцы квартир'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Редактирование');
?>
<div class="user-update">

    <?= $this->render('_form', [
        'model' => $model,
        'modelForm' => $modelForm,
    ]) ?>

</div>
