<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\CounterData */

$this->title = Yii::t('app', 'Create Counter Data');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Counter Datas'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="counter-data-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
