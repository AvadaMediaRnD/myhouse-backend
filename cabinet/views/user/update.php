<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $modelForm common\models\UserForm */

$this->title = Yii::t('app', 'Профиль');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Профиль'), 'url' => ['/user/view']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Редактирование');
?>

<div class="box">
    <div class="box-body">
        <?= $this->render('_form', [
            'model' => $model,
            'modelForm' => $modelForm,
        ]) ?>
    </div>
</div>
