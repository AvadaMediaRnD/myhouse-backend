<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Invoice */

$label = 'Квитанция №' . $model->uid;
$this->title = Yii::t('app', 'Квитанция');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Квитанции'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $label, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Редактирование');
?>

<?= $this->render('_form', [
    'model' => $model,
    'invoiceMonthYear' => $invoiceMonthYear,
]) ?>
