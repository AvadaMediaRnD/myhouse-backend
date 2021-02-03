<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\UserAdmin */

$this->title = Yii::t('app', 'Добавить администратора');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Администраторы'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
