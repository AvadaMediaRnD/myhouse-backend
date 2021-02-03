<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Invoice */

$label = $model->period_end . ' ' . $model->flat->house->name . ' кв.' . $model->flat->flat;
$this->title = Yii::t('app', 'Редактирование: {nameAttribute}', [
    'nameAttribute' => $label,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Квитанции'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $label, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Редактирование');
?>
<div class="invoice-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
