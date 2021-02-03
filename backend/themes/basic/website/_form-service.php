<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\WebsiteService */
/* @var $form yii\widgets\ActiveForm */
/* @var $formId string */

$formId = (int)$formId;
?>

<div id="form-websiteservice-<?= $formId ?>" class="col-md-4 form-websiteservice">
    <h4>
        Услуга <?= $formId + 1 ?>
        <?php if ($model->id) { ?>
            <a href="<?= Yii::$app->urlManager->createUrl(['/website/delete-service', 'id' => $model->id]) ?>" class="pull-right text-red" data-confirm="Удалить?"><i class="fa fa-trash"></i></a>
        <?php } else { ?>
            <a href="#!" class="pull-right text-red form-row-remove-btn"><i class="fa fa-trash"></i></a>
        <?php } ?>
    </h4>
    <img src="<?= Yii::$app->urlManagerFrontend->createAbsoluteUrl(['/site/glide', 'path' => $model->getImagePath(), 'w' => 650, 'h' => 300, 'fit' => 'crop']) ?>" alt="" class="img-responsive margin-bottom-15">
    <?= Html::activeHiddenInput($model, "[$formId]id", [
        'id' => "websiteservice-$formId-id",
    ]) ?>
    <div class="form-group">
        <label for="websiteservice-<?= $formId ?>-imageFile">Рекомендуемый размер: (650x300)</label>
        <?= Html::activeFileInput($model, "[$formId]imageFile", [
            'id' => "websiteservice-$formId-imagefile",
            'class' => 'form-control',
        ]) ?>
    </div>
    <div class="form-group">
        <label for="websiteservice-<?= $formId ?>-title">Название услуги</label>
        <?= Html::activeTextInput($model, "[$formId]title", [
            'id' => "websiteservice-$formId-title",
            'class' => 'form-control',
        ]) ?>
    </div>
    <div class="form-group margin-bottom-30">
        <label for="websiteservice-<?= $formId ?>-description">Описание услуги</label>
        <?= Html::activeTextarea($model, "[$formId]description", [
            'id' => "websiteservice-$formId-description",
            'class' => 'compose-textarea editor-init form-control',
            'placeholder' => 'Текст описания',
        ]) ?>
    </div>
</div>
