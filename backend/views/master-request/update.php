<?php

use yii\helpers\Html;

/* @var $this yii\web\View */


$this->title = Yii::t('app', 'Редактирование: {nameAttribute}', [
    'nameAttribute' => $model->getDescriptionShort(),
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Заявки вызова мастера'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Редактирование');
?>
<div class="master-request-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
