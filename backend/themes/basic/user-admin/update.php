<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\UserAdmin */
/* @var $modelForm backend\models\UserAdminForm */

$this->title = Yii::t('app', 'Пользователь');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Пользователи'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Редактирование');
?>

<div class="box">
    <div class="box-body">
        <?= $this->render('_form', [
            'modelForm' => $modelForm,
            'model' => $model,
        ]) ?>
    </div>
</div>
