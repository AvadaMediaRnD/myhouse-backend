<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Account */

$this->title = Yii::t('app', 'Новый лицевой счет');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Лицевые счета'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('_form', [
    'model' => $model,
]) ?>
