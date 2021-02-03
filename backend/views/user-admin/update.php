<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\UserAdmin */

$this->title = Yii::t('app', 'Редактирование: {nameAttribute}', [
    'nameAttribute' => $model->getFullname(),
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Администраторы'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->getFullname(), 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Редактирование');
?>
<div class="user-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
