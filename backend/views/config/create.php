<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Config */

$this->title = Yii::t('app', 'Добавить настройку');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Настройки'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="config-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
