<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\WebsiteDocument */
/* @var $form yii\widgets\ActiveForm */
/* @var $formId string */

$formId = (int)$formId;
?>

<div id="form-websitedocument-<?= $formId ?>" class="form-websitedocument">
    <?= Html::activeHiddenInput($model, "[$formId]id", [
        'id' => "websitedocument-$formId-id",
    ]) ?>
    <div class="form-group">
        <?php if ($model->id) { ?>
            <a href="<?= Yii::$app->urlManager->createUrl(['/website/delete-document', 'id' => $model->id]) ?>" class="pull-right text-red" data-confirm="Удалить?"><i class="fa fa-trash"></i></a>
        <?php } else { ?>
            <a href="#!" class="pull-right text-red form-row-remove-btn"><i class="fa fa-trash"></i></a>
        <?php } ?>
        
        <?php if (in_array($model->getFileExtension(), ['jpg', 'png'])) { ?>
            <img class="fa fa-3x pull-left" src="<?= Yii::$app->urlManagerFrontend->createAbsoluteUrl(['/site/glide', 'path' => $model->getImagePath(), 'w' => 32, 'h' => 42, 'fit' => 'crop']) ?>" alt="">
        <?php } elseif ($model->getFileExtension() == 'pdf') { ?>
            <i class="fa fa-file-pdf-o fa-3x pull-left" style="width: 32px;" aria-hidden="true"></i>
        <?php } else { ?>
            <i class="fa fa-file-o fa-3x pull-left" style="width: 32px;" aria-hidden="true"></i>
        <?php } ?>        
        
        <label for="websitedocument-<?= $formId ?>-filefile">PDF, JPG (макс. размер 20 Mb)</label>
        <?= Html::activeFileInput($model, "[$formId]fileFile", [
            'id' => "websitedocument-$formId-filefile",
        ]) ?>
    </div>
    
    <div class="form-group">
        <label for="websiteservice-<?= $formId ?>-title">Название документа</label>
        <?= Html::activeTextInput($model, "[$formId]title", [
            'id' => "websitedocument-$formId-title",
            'class' => 'form-control',
        ]) ?>
    </div>
</div>
