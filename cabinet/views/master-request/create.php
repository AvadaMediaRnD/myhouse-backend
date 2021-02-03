<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\MasterRequest */
/* @var $modelForm cabinet\models\MasterRequestForm */

$this->title = Yii::t('app', 'Новая заявка');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Вызов мастера'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="box">
    <div class="box-body">
        <?= $this->render('_form', [
            'model' => $model,
            'modelForm' => $modelForm,
        ]) ?>
    </div>
</div>
