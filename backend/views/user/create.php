<?php

use yii\helpers\Html;


/* @var $this yii\web\View */



$this->title = Yii::t('app', 'Добавить владельца квартир');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Владельцы квартир'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create">

    <?= $this->render('_form', [
        'modelForm' => $modelForm,
    ]) ?>

</div>
