<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\TransactionPurpose */

$this->title = Yii::t('app', 'Новая статья');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Статьи приходов/расходов'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="box">
    <div class="box-body">
        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
    </div>
</div>
