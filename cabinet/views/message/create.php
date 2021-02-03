<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Message */

$this->title = Yii::t('app', 'Добавить сообщение');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Сообщения'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="message-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
