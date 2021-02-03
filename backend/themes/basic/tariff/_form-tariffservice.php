<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\TariffService;
use yii\helpers\ArrayHelper;
use \common\models\Currency;
use common\models\ServiceUnit;
use common\models\Service;

/* @var $this yii\web\View */
/* @var $model common\models\TariffService */
/* @var $form yii\widgets\ActiveForm */
/* @var $formId string */

$formId = (int)$formId;
$currency = Currency::find()->orderBy(['is_default' => SORT_DESC])->one();
$serviceUnit = $model->service ? $model->service->serviceUnit : null;
$serviceUnitOptions = ArrayHelper::map(ServiceUnit::find()->all(), 'id', 'name');
?>

<div id="form-tariffservice-<?= $formId ?>" class="row form-tariffservice">
    <div class="col-xs-6 col-md-4">
        <?= Html::activeHiddenInput($model, "[$formId]id", [
            'id' => "tariffservice-$formId-id",
        ]) ?>
        <?= Html::activeHiddenInput($model, "[$formId]tariff_id", [
            'id' => "tariffservice-$formId-tariff_id",
        ]) ?>
        <div class="form-group">
            <label for="tariffservice-<?= $formId ?>-service_id">Услуга</label>
            <?= Html::activeDropDownList(
                $model, 
                "[$formId]service_id", 
                ArrayHelper::map(Service::find()->all(), 'id', 'name'), 
                [
                    'id' => "tariffservice-$formId-service_id",
                    'class' => 'form-control service-select',
                    'prompt' => 'Выберите...'
                ]
            ) ?>
        </div>
    </div>
    <div class="col-xs-6 col-md-3">
        <div class="form-group">
            <label for="tariffservice-<?= $formId ?>-price_unit">Цена</label>
            <?= Html::activeTextInput($model, "[$formId]price_unit", [
                'id' => "tariffservice-$formId-price_unit",
                'class' => 'form-control',
            ]) ?>
        </div>
    </div>
    <div class="col-xs-6 col-md-2">
        <div class="form-group">
            <label for="<?= $formId ?>-currency-code">Валюта</label>
            <?= Html::textInput("[$formId]currency_code", 'грн', [
                'id' => "$formId-currency-code",
                'class' => 'form-control',
                'disabled' => true,
            ]) ?>
        </div>
    </div>
    <div class="col-xs-6 col-md-3">
        <div class="form-group">
            <label for="<?= $formId ?>-serviceunit-name">Ед. изм.</label>
            <div class="input-group">
                <?= Html::dropDownList("[$formId]serviceunit_name", $serviceUnit->id, $serviceUnitOptions, [
                    'id' => "$formId-serviceunit-name",
                    'class' => 'form-control serviceunit-name',
                    'prompt' => 'Выберите...',
                    'disabled' => true,
                ]) ?>
                <span class="input-group-btn">
                    <?= Html::button(
                    '<i class="fa fa-trash"></i>',
                    [
                        'class' => 'btn btn-default form-row-remove-btn',
                        'type' => 'button',
                    ]) ?>
                </span>
            </div>
        </div>
    </div>
</div>


<?php /* ?>
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
                        'class' => 'btn btn-danger form-row-remove-btn' . ($disabledDelete ? ' disabled' : ''),
                        'type' => 'button',
                    ]) ?>
                </span>
            </div>
        </div>
    </div>
</div>
<?php */ ?>
