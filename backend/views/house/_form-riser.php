<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Riser */
/* @var $form yii\widgets\ActiveForm */
/* @var $formId string */

$formId = (int)$formId;
?>

<div id="form-riser-<?= $formId ?>" class="form-riser">

    <div class="row">
        <?= Html::activeHiddenInput($model, "[$formId]id", [
            'id' => "riser-$formId-id",
        ]) ?>
        <?= Html::activeHiddenInput($model, "[$formId]house_id", [
            'id' => "riser-$formId-house_id",
        ]) ?>
        <div class="col-md-7">
            <div class="form-group">
                <label class="control-label" for="riser-<?= $formId ?>-name"><?= $model->getAttributeLabel('name') ?></label>
                <?= Html::activeTextInput($model, "[$formId]name", [
                    'id' => "riser-$formId-name",
                    'class' => 'form-control',
                ]) ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label" for="riser-<?= $formId ?>-sort"><?= $model->getAttributeLabel('sort') ?></label>
                <?= Html::activeTextInput($model, "[$formId]sort", [
                    'id' => "riser-$formId-sort",
                    'class' => 'form-control',
                    'type' => 'number',
                    'min' => 0,
                    'max' => 999999,
                ]) ?>
            </div>
        </div>
        <div class="col-md-1">
            <div class="form-group">
                <?= Html::button(
                    '<i class="glyphicon glyphicon-trash"></i>',
                    [
                        'class' => 'btn btn-danger form-row-remove-btn',
                        'type' => 'button',
                    ]) ?>
            </div>
        </div>
    </div>

</div>
