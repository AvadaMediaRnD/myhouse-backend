<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use yii\helpers\ArrayHelper;
use common\models\House;
use common\models\Account;

/* @var $this yii\web\View */
/* @var $model common\models\Account */
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
        </div>
    </div>
</div>
<div class="box">
    <div class="box-body">
        <div class="row">
            <div class="col-xs-12">
                <?= $form->field($model, 'status')->dropDownList(Account::getStatusOptions()) ?>
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
                                        $("select#account-flat_id").html(data.flats);
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
                                        $("select#account-flat_id").html(data.flats);
                                        console.log(data);
                                    });
                                ',
                            ]) ?>
                </div>
                <?= $form->field($model, 'flat_id')->dropDownList($flatOptions, [
                    'prompt' => 'Выберите...',
                    'onchange' => '
                        $.get("' . urldecode(Yii::$app->urlManager->createUrl(['/flat/get-flat', 'flat_id' => ''])) . '"+$(this).val(), function( data ) {
                            $("#user-fullname").html(data.user ? data.user.fullnameHtml : "");
                            $("#user-phone").html(data.user ? data.user.phoneHtml : "");
                            console.log(data);
                        });
                    ',
                ]) ?>

                <?php if ($model->flat && $model->flat->user) { ?>
                    <p><b>Владелец:</b> <span id="user-fullname"><a href="<?= Yii::$app->urlManager->createUrl(['/user/view', 'id' => $model->flat->user_id]) ?>"><?= $model->flat->user->fullname ?></a></span></p>
                    <p><b>Телефон:</b> <span id="user-phone"><a href="tel:<?= str_replace(['(', ')', ' ', '-'], '', $model->flat->user->profile->phone) ?>"><?= $model->flat->user->profile->phone ?></a></span></p>
                <?php } else { ?>
                    <p><b>Владелец:</b> <span id="user-fullname">не выбран</span></p>
                    <p><b>Телефон:</b> <span id="user-phone">не выбран</span></p>
                <?php } ?>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 text-right">
                <div class="form-group">
                    <a href="<?= Yii::$app->urlManager->createUrl(['/account/index']) ?>" class="btn btn-default">Отменить</a>
                    <button type="submit" class="btn btn-success">Сохранить</button>
                </div>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div

<?php
$urlGetAmount = Yii::$app->urlManager->createUrl(['/counter-data/ajax-get-amount', 'amount_total' => '']);
$this->registerJs("
//    $(document).on('change', '#counterdata-amount_total', function(e){
//        var uidDate = $('#counterdata-uid_date').val();
//        var serviceId = $('#counterdata-service_id').val();
//        var flatId = $('#counterdata-flat_id').val();
//        $.get('{$urlGetAmount}'+$(this).val()+'&uid_date='+uidDate+'&service_id='+serviceId+'&flat_id='+flatId, function( data ) {
//            $('#counterdata-amount').val(data.amount);
//        });
//    });
");