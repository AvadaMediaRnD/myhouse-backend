<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Flat */

$this->title = Yii::t('app', 'Добавить квартиру');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Квартиры'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="flat-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
