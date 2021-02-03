<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Account */

$this->title = Yii::t('app', 'Лицевой счет');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Лицевые счета'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $this->title . ' №' . $model->uid, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>

<?= $this->render('_form', [
    'model' => $model,
]) ?>
