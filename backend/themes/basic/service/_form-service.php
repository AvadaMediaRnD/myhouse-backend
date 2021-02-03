<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\ServiceUnit;
use common\models\InvoiceService;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model common\models\Service */
/* @var $form yii\widgets\ActiveForm */
/* @var $formId string */

$formId = (int)$formId;
$disabledDelete = $model->isUsedInInvoice();
?>

<div id="form-service-<?= $formId ?>" class="row form-service">
    <div class="col-xs-12 col-sm-7">
        <?= Html::activeHiddenInput($model, "[$formId]id", [
            'id' => "service-$formId-id",
        ]) ?>
        <div class="form-group">
            <label for="service-<?= $formId ?>-name"><?= $model->getAttributeLabel('name') ?></label>
            <?= Html::activeTextInput($model, "[$formId]name", [
                'id' => "service-$formId-name",
                'class' => 'form-control',
            ]) ?>
        </div>
    </div>
    <div class="col-xs-12 col-sm-5">
        <div class="form-group">
            <label for="service-<?= $formId ?>-service_unit_id"><?= $model->getAttributeLabel('service_unit_id') ?></label>
            <div class="input-group">
                <?= Html::activeDropDownList(
                    $model, 
                    "[$formId]service_unit_id", 
                    ArrayHelper::map(ServiceUnit::find()->all(), 'id', 'name'), 
                    [
                        'id' => "service-$formId-service_unit_id",
                        'class' => 'form-control',
                        'prompt' => 'Выберите...'
                    ]
                ) ?>
                
                <span class="input-group-btn">
                    <?= Html::button(
                    '<i class="fa fa-trash"></i>',
                    [
                        'class' => 'btn btn-default form-row-remove-btn' . ($disabledDelete ? ' disabled' : ''),
                        'type' => 'button',
                        'no-delete-msg' => 'Эта услуга используется в квитанциях. Удаление невозможно.',
                    ]) ?>
                </span>
            </div>
        </div>
    </div>
    <div class="col-xs-12">
        <?= Html::activeCheckbox($model, "[$formId]is_counter", [
            'id' => "service-$formId-is_counter",
        ]) ?>
        <div style="margin-bottom: 16px;"></div>
    </div>
</div>
