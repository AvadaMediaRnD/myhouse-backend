<?php

use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = Yii::t('app', 'Профиль владельца');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Владельцы квартир'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Профиль владельца'), 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>

<div class="box">
    <div class="box-body">
        <?= $this->render('_form', [
            'model' => $model,
            'modelForm' => $modelForm,
        ]) ?>
    </div>
</div>

