<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\CounterData */

$this->title = Yii::t('app', 'Показание счетчикa');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Счетчики'), 'url' => ['counters']];
$titleList = Yii::t('app', 'Показания счетчиков');
if ($model->flat_id &&  $model->flat) {
    $titleList .= ', кв.' . $model->flat->flat;
}
$this->params['breadcrumbs'][] = ['label' => $titleList, 'url' => ['counter-list', 'CounterDataSearch[flat_id]' => $model->flat_id, 'CounterDataSearch[service_id]' => $model->service_id]];
$this->params['breadcrumbs'][] = ['label' => $this->title . ' №' . $model->uid, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Редактирование');
?>

<?= $this->render('_form', [
    'model' => $model,
]) ?>
