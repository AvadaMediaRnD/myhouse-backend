<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\Service;
use common\models\ServiceUnit;
use common\models\Tariff;
use common\models\TariffService;
use common\models\PayCompany;
use common\helpers\PriceHelper;
use common\models\CounterData;

/* @var $this yii\web\View */
/* @var $model common\models\InvoiceService */
/* @var $tariffModel common\models\Tariff */
/* @var $payCompanyModel common\models\PayCompany */
/* @var $flatModel common\models\Flat */
/* @var $form yii\widgets\ActiveForm */
/* @var $formId string */

$formId = (int)$formId;

$unit = $model->service->serviceUnit ? $model->service->serviceUnit->id : null;
$tariffService = TariffService::find()->where(['service_id' => $model->service_id, 'tariff_id' => $tariffModel->id])->one();
if ($model->isNewRecord) {
    $model->price_unit = $tariffService ? $tariffService->price_unit : null;
}
if (!$flatModel) {
    $flatModel = $model->invoice->flat;
}

$serviceOptions = Service::getOptions($payCompanyModel);
$counterDataOptions = CounterData::getOptions($model->service, $flatModel, null, true);

$model->amount = $model->amount ? PriceHelper::format($model->amount, true, false, '') : $model->amount;
$model->price_unit = $model->price_unit ? PriceHelper::format($model->price_unit, true, false, '') : $model->price_unit;
$model->price = $model->price ? PriceHelper::format($model->price, true, false, '') : $model->price;
?>

<tr id="form-invoiceservice-<?= $formId ?>" class="form-invoiceservice">
    <td>
        <?= Html::activeHiddenInput($model, "[$formId]id", [
            'id' => "invoiceservice-$formId-id",
        ]) ?>
        <?= Html::activeHiddenInput($model, "[$formId]invoice_id", [
            'id' => "invoiceservice-$formId-invoice_id",
        ]) ?>
        <?= Html::activeHiddenInput($model, "[$formId]counter_data_id", [
            'id' => "invoiceservice-$formId-counter_data_id",
            'class' => 'data-counter_data_id',
        ]) ?>
        
        <?= Html::activeDropDownList($model, "[$formId]service_id", $serviceOptions, [
            'id' => "invoiceservice-$formId-service_id",
            'class' => 'form-control service-select',
            'prompt' => 'Выберите...',
        ]) ?>
    </td>
    <?php /* <td>
        <?= Html::activeDropDownList($model, "[$formId]counter_data_id", $counterDataOptions, [
            'id' => "invoiceservice-$formId-counter_data_id",
            'class' => 'form-control data-counter_data_id',
            'prompt' => 'Выберите...',
        ]) ?>
    </td> */ ?>
    <td>
        <?= Html::activeTextInput($model, "[$formId]amount", [
            'id' => "invoiceservice-$formId-amount",
            'class' => 'form-control data-amount',
        ]) ?>
    </td>
    <td>
        <?= Html::dropDownList("[$formId]unit", $unit, ArrayHelper::map(ServiceUnit::find()->all(), 'id', 'name'), [
            'id' => "$formId-unit",
            'class' => 'form-control data-unit',
            'prompt' => 'Выберите...',
        ]) ?>
    </td>
    <td>
        <?= Html::activeTextInput($model, "[$formId]price_unit", [
            'id' => "invoiceservice-$formId-price_unit",
            'class' => 'form-control data-price_unit',
        ]) ?>
    </td>
    <td>
        <?= Html::activeTextInput($model, "[$formId]price", [
            'id' => "invoiceservice-$formId-price",
            'class' => 'form-control data-price',
        ]) ?>
    </td>
    <td>
        <?= Html::button(
        '<i class="fa fa-trash"></i>',
        [
            'class' => 'btn btn-default btn-sm form-row-remove-btn',
            'title' => 'Удалить услугу',
            'type' => 'button',
        ]) ?>
    </td>
</tr>
