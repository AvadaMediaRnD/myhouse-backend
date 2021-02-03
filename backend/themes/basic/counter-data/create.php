<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\CounterData */

$this->title = Yii::t('app', 'Новое показание');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Счетчики'), 'url' => ['counters']];
if ($model->flat_id &&  $model->flat) {
    $titleList = Yii::t('app', 'Показания счетчиков');
    $titleList .= ', кв.' . $model->flat->flat;
    $this->params['breadcrumbs'][] = ['label' => $titleList, 'url' => ['counter-list', 'CounterDataSearch[flat_id]' => $model->flat_id, 'CounterDataSearch[service_id]' => $model->service_id]];
}
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('_form', [
    'model' => $model,
]) ?>
