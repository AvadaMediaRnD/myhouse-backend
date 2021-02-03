<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\UserAdmin */
/* @var $modelForm backend\models\UserAdminForm */

$this->title = Yii::t('app', 'Новый пользователь');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Пользователи'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="box">
    <div class="box-body">
        <?= $this->render('_form', [
            'modelForm' => $modelForm,
            'model' => $model,
        ]) ?>
    </div>
</div>
