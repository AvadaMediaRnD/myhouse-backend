<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ServiceUnit */
/* @var $form yii\widgets\ActiveForm */
/* @var $formId string */

$formId = (int)$formId;
$disabledDelete = $model->isUsedInService();
?>

<div id="form-serviceunit-<?= $formId ?>" class="row form-serviceunit">
    <div class="col-xs-12">
        <?= Html::activeHiddenInput($model, "[$formId]id", [
            'id' => "serviceunit-$formId-id",
        ]) ?>
        <div class="form-group">
            <label for="serviceunit-<?= $formId ?>-name"><?= $model->getAttributeLabel('name') ?></label>
            <div class="input-group">
                <?= Html::activeTextInput($model, "[$formId]name", [
                    'id' => "serviceunit-$formId-name",
                    'class' => 'form-control',
                ]) ?>
                <span class="input-group-btn">
                    <?= Html::button(
                    '<i class="fa fa-trash"></i>',
                    [
                        'class' => 'btn btn-default form-row-remove-btn' . ($disabledDelete ? ' disabled' : ''),
                        'type' => 'button',
                        'no-delete-msg' => 'Эта ед.изм. используется в услуге. Удаление невозможно.',
                    ]) ?>
                </span>
            </div>
        </div>
    </div>
</div>
