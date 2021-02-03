<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Floor */
/* @var $form yii\widgets\ActiveForm */
/* @var $formId string */

$formId = (int)$formId;
?>

<div id="form-floor-<?= $formId ?>" class="form-floor">

    <div class="row">
        <?= Html::activeHiddenInput($model, "[$formId]id", [
            'id' => "floor-$formId-id",
        ]) ?>
        <?= Html::activeHiddenInput($model, "[$formId]house_id", [
            'id' => "floor-$formId-house_id",
        ]) ?>
        <div class="col-md-7">
            <div class="form-group">
                <label class="control-label" for="floor-<?= $formId ?>-name"><?= $model->getAttributeLabel('name') ?></label>
                <?= Html::activeTextInput($model, "[$formId]name", [
                    'id' => "floor-$formId-name",
                    'class' => 'form-control',
                ]) ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label" for="floor-<?= $formId ?>-sort"><?= $model->getAttributeLabel('sort') ?></label>
                <?= Html::activeTextInput($model, "[$formId]sort", [
                    'id' => "floor-$formId-sort",
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
