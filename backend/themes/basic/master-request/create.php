<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\MasterRequest */

$this->title = Yii::t('app', 'Новая заявка');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Вызов мастера'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('_form', [
    'model' => $model,
]) ?>
