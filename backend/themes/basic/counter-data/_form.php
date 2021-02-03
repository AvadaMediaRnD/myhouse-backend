<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use yii\helpers\ArrayHelper;
use common\models\House;
use common\models\CounterData;
use common\models\Service;

/* @var $this yii\web\View */
/* @var $model common\models\CounterData */
/* @var $form yii\widgets\ActiveForm */

$houseId = $model->flat ? $model->flat->house_id : null;
$sectionId = $model->flat ? $model->flat->section_id : null;
$sectionOptions = ($houseId && $model->flat->house->sections) 
    ? ArrayHelper::map($model->flat->house->sections, 'id', 'name') 
    : [];
$flatOptions = [];
if ($model->flat && $model->flat->section) {
    $flatOptions = ArrayHelper::map($model->flat->section->flats, 'id', 'flat');
} elseif ($model->flat && $model->flat->house) {
    $flatOptions = ArrayHelper::map($model->flat->house->flats, 'id', 'flat');
}
$serviceOptions = ArrayHelper::map(Service::find()->where(['is_counter' => 1])->all(), 'id', function ($model) {
    return $model->name . ' (' . $model->serviceUnit->name . ')';
});
$counterDataOptions = CounterData::getOptions($model->service, $model->flat, $model->id);
?>

<?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-xs-12 col-md-7 col-lg-6">
            <div class="page-header-spec">
                <?= $form->field($model, 'uid', [
                    'template' => '<div class="input-group">
                            <div class="input-group-addon">
                                №
                            </div>{input}
                        </div>',
                    'enableAjaxValidation' => true,
                    'enableClientValidation' => true,
                ])->textInput() ?>
                <span class="label-mid">от</span>
                <?= $form->field($model, 'uid_date', ['template' => '{input}'])->widget(DatePicker::className(), [
                    'type' => DatePicker::TYPE_COMPONENT_PREPEND,
                    'removeButton' => false,
                    'pluginOptions' => [
                        'autoclose' => true,
                        'format' => 'dd.mm.yyyy'
                    ]
                ]) ?>
            </div>
        </div>
    </div>
    <div class="box">
        <div class="box-body">
            <?= Html::activeHiddenInput($model, 'id') ?>
            
            <div class="row">
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group">
                        <label for="house_id">Дом</label>
                        <?= Html::dropDownList(
                                'house_id', 
                                $houseId, 
                                House::getOptions(),
                                [
                                    'id' => 'house_id',
                                    'prompt' => 'Выберите...',
                                    'class' => 'form-control',
                                    'onchange'=>'
                                        $.get("' . urldecode(Yii::$app->urlManager->createUrl(['/flat/get-lists-by-house', 'house_id' => ''])) . '"+$(this).val(), function( data ) {
                                            $("select#section_id").html(data.sections);
                                            $("select#counterdata-flat_id").html(data.flats);
                                            console.log(data);
                                        });
                                    ',
                                ]) ?>
                    </div>
                    <div class="form-group">
                        <label for="section_id">Секция</label>
                        <?= Html::dropDownList(
                                'section_id', 
                                $sectionId, 
                                $sectionOptions,
                                [
                                    'id' => 'section_id',
                                    'prompt' => 'Выберите...',
                                    'class' => 'form-control',
                                    'onchange' => '
                                        $.get("' . urldecode(Yii::$app->urlManager->createUrl(['/flat/get-lists-by-house', 'section_id' => ''])) . '"+$(this).val(), function( data ) {
                                            $("select#counterdata-flat_id").html(data.flats);
                                            console.log(data);
                                        });
                                    ',
                                ]) ?>
                    </div>
                    <?= $form->field($model, 'flat_id')->dropDownList($flatOptions, [
                        'class' => 'form-control flat-select',
                        'prompt' => 'Выберите...',
                    ]) ?>
                    <?= $form->field($model, 'service_id')->dropDownList($serviceOptions, [
                        'class' => 'form-control service-select',
                        'prompt' => 'Выберите...',
                    ]) ?>
                    
                </div>
                <div class="col-xs-12 col-sm-6">
                    <?= $form->field($model, 'status')->dropDownList(CounterData::getStatusOptions(), [
                        'prompt' => 'Выберите...',
                    ]) ?>
                    
                    <?php /*echo $form->field($model, 'counter_data_last_id')->dropDownList($counterDataOptions, [
                        'class' => 'form-control data-counter_data_last_id',
                        'prompt' => 'Выберите...',
                    ])*/ ?>
                    <?php /*echo $form->field($model, 'isAutoSetLast')->checkbox([
                        'id' => 'isAutoSetLast',
                        'onchange' => '$("select.data-counter_data_last_id").attr("disabled", this.checked)',
                        'onload' => '$("select.data-counter_data_last_id").attr("disabled", this.checked)'
                    ])*/ ?>
                    
                    <?= Html::activeHiddenInput($model, 'isAutoSetLast') ?>
                    
                    <?= $form->field($model, 'amount_total')->textInput(['type' => 'number', 'step' => '0.1']) ?>
                    <?php // echo $form->field($model, 'amount')->textInput() ?>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 text-right">
                    <div class="form-group">
                        <a href="<?= Yii::$app->urlManager->createUrl(['/counter-data/index']) ?>" class="btn btn-default margin-bottom-15">Отменить</a>
                        <?= Html::input('submit', 'action_save', Yii::t('app', 'Сохранить'), ['class' => 'btn btn-success margin-bottom-15']) ?>
                        <?= Html::input('submit', 'action_save_add', Yii::t('app', 'Сохранить и добавить новые показания'), ['class' => 'btn btn-success margin-bottom-15 bg-green-active']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php ActiveForm::end(); ?>
<?php
$urlGetAmount = Yii::$app->urlManager->createUrl(['/counter-data/ajax-get-amount', 'amount_total' => '']);
$urlGetCounterDataOptions = Yii::$app->urlManager->createUrl(['/invoice/ajax-get-counter-data-options', 'service_id' => '']);
$this->registerJs("
//    $(document).on('change', '#counterdata-amount_total', function(e){
//        var uidDate = $('#counterdata-uid_date').val();
//        var serviceId = $('#counterdata-service_id').val();
//        var flatId = $('#counterdata-flat_id').val();
//        $.get('{$urlGetAmount}'+$(this).val()+'&uid_date='+uidDate+'&service_id='+serviceId+'&flat_id='+flatId, function( data ) {
//            $('#counterdata-amount').val(data.amount);
//        });
//    });

    $(document).on('change', '.service-select, .flat-select', function(e) {
        var flatId = $('#counterdata-flat_id').val();
        var serviceId = $('#counterdata-service_id').val();
        $.get('{$urlGetCounterDataOptions}'+serviceId+'&flat_id='+flatId+'&current_id={$model->id}', function( data ) {
            console.log(data);
            $('select.data-counter_data_last_id').html(data.counterData);
        });
    });
    
    $('#isAutoSetLast').trigger('change');
");