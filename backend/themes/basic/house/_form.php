<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \common\models\House */
/* @var $modelFrom \backend\models\HouseForm */

$sections = $model->isNewRecord ? [] : $model->getSections()->all();
$floors = $model->isNewRecord ? [] : $model->getFloors()->all();
$risers = $model->isNewRecord ? [] : $model->getRisers()->all();
$houseUserAdmins = $model->isNewRecord ? [] : $model->getHouseUserAdmins()->all();
?>

<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
<div class="row">
    <div class="col-xs-12 col-lg-4">
        <?= $form->field($modelForm, 'name')->textInput() ?>
        <?= $form->field($modelForm, 'address')->textInput() ?>
        <?= $form->field($modelForm, 'image1')->fileInput(['accept' => 'image/*']) ?>
        <?= $form->field($modelForm, 'image2')->fileInput(['accept' => 'image/*']) ?>
        <?= $form->field($modelForm, 'image3')->fileInput(['accept' => 'image/*']) ?>
        <?= $form->field($modelForm, 'image4')->fileInput(['accept' => 'image/*']) ?>
        <?= $form->field($modelForm, 'image5')->fileInput(['accept' => 'image/*']) ?>
    </div>
    <div class="col-xs-12 col-lg-8">
        <div class="row">
            <div class="col-xs-12 col-md-6">
                <img src="<?= Yii::$app->urlManagerFrontend->createAbsoluteUrl(['/site/glide', 'path' => $modelForm->getImagePath1(), 'w' => 522, 'h' => 350, 'fit' => 'crop']) ?>" class="img-responsive largeImg margin-bottom-30" alt="<?= $modelForm->getAttributeLabel('image1') ?>">
            </div>
            <div class="col-xs-6 col-md-3">
                <img src="<?= Yii::$app->urlManagerFrontend->createAbsoluteUrl(['/site/glide', 'path' => $modelForm->getImagePath2(), 'w' => 248, 'h' => 160, 'fit' => 'crop']) ?>" class="img-responsive smallImg margin-bottom-30" alt="<?= $modelForm->getAttributeLabel('image2') ?>">
            </div>
            <div class="col-xs-6 col-md-3">
                <img src="<?= Yii::$app->urlManagerFrontend->createAbsoluteUrl(['/site/glide', 'path' => $modelForm->getImagePath3(), 'w' => 248, 'h' => 160, 'fit' => 'crop']) ?>" class="img-responsive smallImg margin-bottom-30" alt="<?= $modelForm->getAttributeLabel('image3') ?>">
            </div>
            <div class="col-xs-6 col-md-3">
                <img src="<?= Yii::$app->urlManagerFrontend->createAbsoluteUrl(['/site/glide', 'path' => $modelForm->getImagePath4(), 'w' => 248, 'h' => 160, 'fit' => 'crop']) ?>" class="img-responsive smallImg margin-bottom-30" alt="<?= $modelForm->getAttributeLabel('image4') ?>">
            </div>
            <div class="col-xs-6 col-md-3">
                <img src="<?= Yii::$app->urlManagerFrontend->createAbsoluteUrl(['/site/glide', 'path' => $modelForm->getImagePath5(), 'w' => 248, 'h' => 160, 'fit' => 'crop']) ?>" class="img-responsive smallImg margin-bottom-30" alt="<?= $modelForm->getAttributeLabel('image5') ?>">
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xs-12 col-lg-8">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab-sections" data-toggle="tab" aria-expanded="true">Секции</a></li>
                <li><a href="#tab-floors" data-toggle="tab" aria-expanded="false">Этажи</a></li>
                <?php /* ?>
                <li><a href="#tab-risers" data-toggle="tab" aria-expanded="false">Стояки</a></li>
                <?php */ ?>
                <li><a href="#tab-houseuseradmins" data-toggle="tab" aria-expanded="false">Пользователи</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active clearfix" id="tab-sections">
                    <div id="form-section-rows">
                        <?php foreach ($sections as $k => $section) { ?>
                            <?= $this->render('_form-section', ['model' => $section, 'formId' => $k]) ?>
                        <?php } ?>
                    </div>
                    <button class="btn btn-success pull-right form-row-add-section-btn" type="button">Добавить</button>
                </div>
                <div class="tab-pane clearfix" id="tab-floors">
                    <div id="form-floor-rows">
                        <?php foreach ($floors as $k => $floor) { ?>
                            <?= $this->render('_form-floor', ['model' => $floor, 'formId' => $k]) ?>
                        <?php } ?>
                    </div>
                    <button class="btn btn-success pull-right form-row-add-floor-btn" type="button">Добавить</button>
                </div>
                <?php /* ?>
                <div class="tab-pane clearfix" id="tab-risers">
                    <div id="form-riser-rows">
                        <?php foreach ($risers as $k => $riser) { ?>
                            <?= $this->render('_form-riser', ['model' => $riser, 'formId' => $k]) ?>
                        <?php } ?>
                    </div>
                    <button class="btn btn-success pull-right form-row-add-riser-btn" type="button">Добавить</button>
                </div>
                <?php */ ?>
                <div class="tab-pane clearfix" id="tab-houseuseradmins">
                    <div id="form-houseuseradmin-rows">
                        <?php foreach ($houseUserAdmins as $k => $houseUserAdmin) { ?>
                            <?= $this->render('_form-houseuseradmin', ['model' => $houseUserAdmin, 'formId' => $k]) ?>
                        <?php } ?>
                    </div>
                    <button class="btn btn-success pull-right form-row-add-houseuseradmin-btn" type="button">Добавить</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-12 text-right">
        <div class="form-group">
            <a href="<?= Yii::$app->urlManager->createUrl(['/house/index']) ?>" class="btn btn-default">Отменить</a>
            <button type="submit" class="btn btn-success">Сохранить</button>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>

<?php
$formIdSectionNext = $sections ? count($sections) : 0;
$formIdFloorNext = $floors ? count($floors) : 0;
$formIdRiserNext = $risers ? count($risers) : 0;
$formIdUserNext = $houseUserAdmins ? count($houseUserAdmins) : 0;
$urlGetFormSection = Yii::$app->urlManager->createUrl(['/house/ajax-get-form-section', 'house_id' => $model->id, 'form_id' => '']);
$urlGetFormFloor = Yii::$app->urlManager->createUrl(['/house/ajax-get-form-floor', 'house_id' => $model->id, 'form_id' => '']);
$urlGetFormRiser = Yii::$app->urlManager->createUrl(['/house/ajax-get-form-riser', 'house_id' => $model->id, 'form_id' => '']);
$urlGetFormHouseUserAdmin = Yii::$app->urlManager->createUrl(['/house/ajax-get-form-house-user-admin', 'house_id' => $model->id, 'form_id' => '']);
$urlGetUserAdminRoleLabel = Yii::$app->urlManager->createUrl(['/house/ajax-get-user-admin-role-label', 'id' => '']);
$this->registerJs("
    var formIdSectionNext = {$formIdSectionNext};
    var formIdFloorNext = {$formIdFloorNext};
    var formIdRiserNext = {$formIdRiserNext};
    var formIdUserNext = {$formIdUserNext};

    $(document).on('click', '.form-row-remove-btn', function(e){
        if (confirm('Удалить?')) { 
            $(this).parents('.form-section, .form-floor, .form-riser, .form-houseuseradmin').remove(); 
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
    
    $(document).on('click', '.form-row-add-houseuseradmin-btn', function(e){
        $.ajax({
            url: '{$urlGetFormHouseUserAdmin}'+formIdUserNext,
            dataType: 'json',
            success: function(json) {
                console.log(json);
                $('#form-houseuseradmin-rows').append(json);
                formIdUserNext++;
            }
        });
    });
    
    // user role
    $(document).on('change', '.useradmin-select', function(e){
        var selector = $(this);
        $.ajax({
            url: '{$urlGetUserAdminRoleLabel}'+selector.val(),
            dataType: 'json',
            success: function(json) {
                console.log(json);
                selector.closest('.form-houseuseradmin').find('.useradmin-role').val(json);
            }
        });
    });
");
