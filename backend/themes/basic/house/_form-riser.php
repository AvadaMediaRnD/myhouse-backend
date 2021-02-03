<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Riser */
/* @var $form yii\widgets\ActiveForm */
/* @var $formId string */

$formId = (int)$formId;
?>

<div id="form-riser-<?= $formId ?>" class="row form-riser">
    <div class="col-xs-12">
        <?= Html::activeHiddenInput($model, "[$formId]id", [
            'id' => "riser-$formId-id",
        ]) ?>
        <?= Html::activeHiddenInput($model, "[$formId]house_id", [
            'id' => "riser-$formId-house_id",
        ]) ?>
        <div class="form-group">
            <label for="riser-<?= $formId ?>-name"><?= $model->getAttributeLabel('name') ?></label>
            <div class="input-group">
                <?= Html::activeTextInput($model, "[$formId]name", [
                    'id' => "riser-$formId-name",
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
