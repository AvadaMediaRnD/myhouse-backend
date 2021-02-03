<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Invoice */

$this->title = Yii::t('app', 'Новая квитанция');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Квитанции'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="invoice-create">

    <?= $this->render('_form', [
        'model' => $model,
        'invoiceMonthYear' => $invoiceMonthYear,
    ]) ?>

</div>
