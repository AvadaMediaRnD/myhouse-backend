<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use yii\helpers\ArrayHelper;
use common\models\House;
use common\models\Account;

/* @var $this yii\web\View */
/* @var $model common\models\Tariff */
/* @var $form yii\widgets\ActiveForm */

?>

<?php $form = ActiveForm::begin(); ?>
<div class="row">
    <div class="col-xs-12 col-lg-7">
        <?= $form->field($model, 'name')->textInput() ?>
        <?= $form->field($model, 'description')->textarea(['rows' => 5]) ?>
        <?php // echo $form->field($model, 'is_default')->checkbox() ?>
    </div>
    <div class="col-xs-12 col-lg-7">
        <div id="form-tariffservice-rows">
            <?php foreach ($model->tariffServices as $k => $tariffService) { ?>
                <?= $this->render('_form-tariffservice', ['model' => $tariffService, 'formId' => $k]) ?>
            <?php } ?>
        </div>
        <button type="button" class="btn btn-default btn-hover-change pull-left margin-bottom-15 form-row-add-tariffservice-btn">Добавить услугу</button>
    </div>
</div>
<div class="row">
    <div class="col-xs-12 col-lg-7 text-right">
        <div class="form-group">
            <a href="<?= Yii::$app->urlManager->createUrl(['/tariff/index']) ?>" class="btn btn-default">Отменить</a>
            <button type="submit" class="btn btn-success">Сохранить</button>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
<?php
$formIdTariffServiceNext = $model->tariffServices ? count($model->tariffServices) : 0;
$urlGetFormTariffService = Yii::$app->urlManager->createUrl(['/tariff/ajax-get-form-tariff-service', 'tariff_id' => $model->id, 'form_id' => '']);
$urlGetServiceUnits = Yii::$app->urlManager->createUrl(['/service/ajax-get-service-units', 'service_id' => '']);
$this->registerJs("
    var formIdTariffServiceNext = {$formIdTariffServiceNext};

    $(document).on('click', '.form-row-remove-btn', function(e){
        if ($(this).hasClass('disabled')) {
            alert('Эта услуга используется в квитанциях. Удаление невозможно.');
        } else {
            if (confirm('Удалить?')) { 
                $(this).parents('.form-tariffservice').remove(); 
            }
        }
    });
    
    $(document).on('click', '.form-row-add-tariffservice-btn', function(e){
        $.ajax({
            url: '{$urlGetFormTariffService}'+formIdTariffServiceNext,
            dataType: 'json',
            success: function(json) {
                console.log(json);
                $('#form-tariffservice-rows').append(json);
                formIdTariffServiceNext++;
            }
        });
    });
    
    // user role
    $(document).on('change', '.service-select', function(e){
        var selector = $(this);
        $.ajax({
            url: '{$urlGetServiceUnits}'+selector.val(),
            dataType: 'json',
            success: function(json) {
                console.log(json);
                selector.closest('.form-tariffservice').find('.serviceunit-name').val(json.serviceUnitValue);
            }
        });
    });
");