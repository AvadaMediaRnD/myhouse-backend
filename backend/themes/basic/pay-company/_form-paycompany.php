<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\PayCompanyService;
use common\models\Service;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\PayCompany */
/* @var $form yii\widgets\ActiveForm */
/* @var $formId string */

$formId = (int)$formId;
$payCompanyServices = $model->payCompanyServices;
$services = Service::find()->all();
$modelPayCompanyService = new PayCompanyService();
?>

<div id="form-paycompany-<?= $formId ?>" class="row form-paycompany margin-bottom-15">
    <div class="col-xs-12">
        <?= Html::activeHiddenInput($model, "[$formId]id", [
            'id' => "paycompany-$formId-id",
        ]) ?>
        <div class="form-group">
            <label for="paycompany-<?= $formId ?>-name"><?= $model->getAttributeLabel('name') ?></label>
            <?= Html::activeTextInput($model, "[$formId]name", [
                    'id' => "paycompany-$formId-name",
                    'class' => 'form-control',
                ]) ?>
        </div>
        
        <div class="form-group">
            <label for="paycompany-<?= $formId ?>-description"><?= $model->getAttributeLabel('description') ?></label>
            <?= Html::activeTextarea($model, "[$formId]description", [
                    'id' => "paycompany-$formId-description",
                    'class' => 'form-control',
                    'rows' => 5,
                ]) ?>
        </div>
        
        <?php /*
        <label>Услуги компании</label>
        <?= Html::checkboxList("PayCompany[$formId][service_id]", ArrayHelper::getColumn($payCompanyServices, 'service_id'), ArrayHelper::map($services, 'id', 'name')) ?>
        */ ?>
        
        <?php /* echo Html::button(
            '<i class="fa fa-trash"></i>',
            [
                'class' => 'btn btn-danger form-row-remove-btn',
                'type' => 'button',
            ]) */ ?>
        
    </div>
</div>
