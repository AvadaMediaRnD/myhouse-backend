<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\House */

$this->title = Yii::t('app', 'Добавить ЖК');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Жилые комплексы'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="house-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
