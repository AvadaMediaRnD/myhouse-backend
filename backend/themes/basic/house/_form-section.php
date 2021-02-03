<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Section */
/* @var $form yii\widgets\ActiveForm */
/* @var $formId string */

$formId = (int)$formId;
?>

<div id="form-section-<?= $formId ?>" class="row form-section">
    <div class="col-xs-12">
        <?= Html::activeHiddenInput($model, "[$formId]id", [
            'id' => "section-$formId-id",
        ]) ?>
        <?= Html::activeHiddenInput($model, "[$formId]house_id", [
            'id' => "section-$formId-house_id",
        ]) ?>
        <div class="form-group">
            <label for="section-<?= $formId ?>-name"><?= $model->getAttributeLabel('name') ?></label>
            <div class="input-group">
                <?= Html::activeTextInput($model, "[$formId]name", [
                    'id' => "section-$formId-name",
                    'class' => 'form-control',
                ]) ?>
                <span class="input-group-btn">
                    <?= Html::button(
                    '<i class="fa fa-trash"></i>',
                    [
                        'class' => 'btn btn-danger form-row-remove-btn',
                        'type' => 'button',
                    ]) ?>
                </span>
            </div>
        </div>
    </div>
</div>
