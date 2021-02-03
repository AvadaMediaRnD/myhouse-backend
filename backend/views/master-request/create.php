<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\MasterRequest */

$this->title = Yii::t('app', 'Добавить заявку');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Заявки вызова мастера'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="master-request-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
