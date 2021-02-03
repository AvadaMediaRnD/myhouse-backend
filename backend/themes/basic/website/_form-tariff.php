<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\WebsiteTariff */
/* @var $form yii\widgets\ActiveForm */
/* @var $formId string */

$formId = (int)$formId;
?>

<div id="form-websitetariff-<?= $formId ?>" class="col-md-4 form-websitetariff">
    <h4>
        Тариф <?= $formId + 1 ?>
        <?php if ($model->id) { ?>
            <a href="<?= Yii::$app->urlManager->createUrl(['/website/delete-tariff', 'id' => $model->id]) ?>" class="pull-right text-red" data-confirm="Удалить?"><i class="fa fa-trash"></i></a>
        <?php } else { ?>
            <a href="#!" class="pull-right text-red form-row-remove-btn"><i class="fa fa-trash"></i></a>
        <?php } ?>
    </h4>
    <img src="<?= Yii::$app->urlManagerFrontend->createAbsoluteUrl(['/site/glide', 'path' => $model->getImagePath(), 'w' => 650, 'h' => 300, 'fit' => 'crop']) ?>" alt="" class="img-responsive margin-bottom-15">
    <?= Html::activeHiddenInput($model, "[$formId]id", [
        'id' => "websitetariff-$formId-id",
    ]) ?>
    <div class="form-group">
        <label for="websitetariff-<?= $formId ?>-imageFile">Файл</label>
        <?= Html::activeFileInput($model, "[$formId]imageFile", [
            'id' => "websitetariff-$formId-imagefile",
            'class' => 'form-control',
        ]) ?>
    </div>
    <div class="form-group">
        <label for="websitetariff-<?= $formId ?>-title">Подпись</label>
        <?= Html::activeTextInput($model, "[$formId]title", [
            'id' => "websitetariff-$formId-title",
            'class' => 'form-control',
        ]) ?>
    </div>
</div>
