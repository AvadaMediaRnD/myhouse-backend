<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\House */
/* @var $form yii\widgets\ActiveForm */

$sections = $model->isNewRecord ? [] : $model->getSections()->all();
$floors = $model->isNewRecord ? [] : $model->getFloors()->all();
$risers = $model->isNewRecord ? [] : $model->getRisers()->all();
?>

<div class="house-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <label>Изображение</label>
    <div class="form-group">
        <a href="<?= $model->getImageUrl() ?>" target="_blank">
            <img src="<?= $model->getImageUrl(160, 100) ?>" class="">
        </a>
    </div>
    <button class="btn btn-primary" onclick="$('#house-imagefile').click()" type="button">Загрузить</button>

    <?= $form->field($model, 'imageFile')
        ->fileInput(['accept' => 'image/*', 'class' => 'hide'])
        ->label(false)
        ->hint(Yii::t('app', 'Рекомендуется загружать изображения не меньше 1200x800 пикселей.')) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>

    <ul class="nav nav-tabs">
        <li class="active"><a data-toggle="tab" href="#tab-sections">Секции</a></li>
        <li><a data-toggle="tab" href="#tab-floors">Этажи</a></li>
        <li><a data-toggle="tab" href="#tab-risers">Стояки</a></li>
    </ul>

    <div class="tab-content">
        <div id="tab-sections" class="tab-pane fade in active">
            <h3>Секции</h3>
            <div id="form-section-rows">
                <?php foreach ($sections as $k => $section) { ?>
                    <?= $this->render('_form-section', ['model' => $section, 'formId' => $k]) ?>
                <?php } ?>
            </div>
            <div class="form-group">
                <button class="btn btn-primary form-row-add-section-btn" type="button"><?= Yii::t('app', 'Добавить') ?></button>
            </div>
        </div>
        <div id="tab-floors" class="tab-pane fade">
            <h3>Этажи</h3>
            <div id="form-floor-rows">
                <?php foreach ($floors as $k => $floor) { ?>
                    <?= $this->render('_form-floor', ['model' => $floor, 'formId' => $k]) ?>
                <?php } ?>
            </div>
            <div class="form-group">
                <button class="btn btn-primary form-row-add-floor-btn" type="button"><?= Yii::t('app', 'Добавить') ?></button>
            </div>
        </div>
        <div id="tab-risers" class="tab-pane fade">
            <h3>Стояки</h3>
            <div id="form-riser-rows">
                <?php foreach ($risers as $k => $riser) { ?>
                    <?= $this->render('_form-riser', ['model' => $riser, 'formId' => $k]) ?>
                <?php } ?>
            </div>
            <div class="form-group">
                <button class="btn btn-primary form-row-add-riser-btn" type="button"><?= Yii::t('app', 'Добавить') ?></button>
            </div>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Сохранить'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$formIdSectionNext = count($sections);
$formIdFloorNext = count($floors);
$formIdRiserNext = count($risers);
$urlGetFormSection = Yii::$app->urlManager->createUrl(['/house/ajax-get-form-section', 'house_id' => $model->id, 'form_id' => '']);
$urlGetFormFloor = Yii::$app->urlManager->createUrl(['/house/ajax-get-form-floor', 'house_id' => $model->id, 'form_id' => '']);
$urlGetFormRiser = Yii::$app->urlManager->createUrl(['/house/ajax-get-form-riser', 'house_id' => $model->id, 'form_id' => '']);
$this->registerJs("
    var formIdSectionNext = {$formIdSectionNext};
    var formIdFloorNext = {$formIdFloorNext};
    var formIdRiserNext = {$formIdRiserNext};

    $(document).on('click', '.form-row-remove-btn', function(e){
        if (confirm('Удалить?')) { 
            $(this).parents('.form-section, .form-floor, .form-riser').remove(); 
        }
    });
    
    $(document).on('click', '.form-row-add-section-btn', function(e){
        $.ajax({
            url: '{$urlGetFormSection}'+formIdSectionNext,
            dataType: 'json',
            success: function(json) {
                console.log(json);
                $('#form-section-rows').append(json);
                formIdSectionNext++;
            }
        });
    });

    $(document).on('click', '.form-row-add-floor-btn', function(e){
        $.ajax({
            url: '{$urlGetFormFloor}'+formIdFloorNext,
            dataType: 'json',
            success: function(json) {
                console.log(json);
                $('#form-floor-rows').append(json);
                formIdFloorNext++;
            }
        });
    });
    
    $(document).on('click', '.form-row-add-riser-btn', function(e){
        $.ajax({
            url: '{$urlGetFormRiser}'+formIdRiserNext,
            dataType: 'json',
            success: function(json) {
                console.log(json);
                $('#form-riser-rows').append(json);
                formIdRiserNext++;
            }
        });
    });
");
