<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Message */

$this->title = 'Сообщение';
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Сообщения'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="box">
    <div class="box-body no-padding">
        <div class="mailbox-read-info">
            <h3><?= $model->name ?></h3>
            <h5>От: <?= $model->userAdminFrom ? $model->userAdminFrom->fullname : 'системное' ?>
                <span class="mailbox-read-time pull-right"><?= $model->created ?></span>
            </h5>
        </div>
        <div class="mailbox-read-message">
            <?= Html::decode($model->description) ?>
        </div>
    </div>
    <div class="box-footer">
        <a href="<?= Yii::$app->urlManager->createUrl(['/message/delete', 'id' => $model->id]) ?>" data-confirm="Удалить?" data-method="post">
            <button type="button" class="btn btn-default"><i class="fa fa-trash-o"></i> Удалить</button>
        </a>
    </div>
</div>
