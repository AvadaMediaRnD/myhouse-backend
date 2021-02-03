<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Account */

$this->title = Yii::t('app', 'Новый тариф');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Тарифы'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="box">
    <div class="box-body">
        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
    </div>
</div>
