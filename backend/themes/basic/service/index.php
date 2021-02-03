<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use common\models\Service;
use common\models\ServiceUnit;

/* @var $this yii\web\View */
/* @var $services \common\models\Service[] */
/* @var $serviceUnits \common\models\ServiceUnit[] */

$this->title = Yii::t('app', 'Редактирование услуг');
$activeTab = Yii::$app->request->get('tab');
?>

<div class="box">
    <div class="box-body">
        <?php $form = ActiveForm::begin(); ?>
            <div class="row">
                <div class="col-xs-12 col-lg-7">
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                            <li class="<?= $activeTab != 'tab_serviceunit' ? 'active' : '' ?>"><a href="#tab_service" data-toggle="tab" aria-expanded="true">Услуги</a></li>
                            <li class="<?= $activeTab == 'tab_serviceunit' ? 'active' : '' ?>"><a href="#tab_serviceunit" data-toggle="tab" aria-expanded="false">Единицы измерения</a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane clearfix <?= $activeTab != 'tab_serviceunit' ? 'active' : '' ?>" id="tab_service">
                                <div id="form-service-rows">
                                    <?php foreach ($services as $k => $service) { ?>
                                        <?= $this->render('_form-service', ['model' => $service, 'formId' => $k]) ?>
                                    <?php } ?>
                                </div>
                                <button type="button" class="btn btn-default btn-hover-change pull-left form-row-add-service-btn">Добавить</button>
                            </div>
                            <div class="tab-pane clearfix <?= $activeTab == 'tab_serviceunit' ? 'active' : '' ?>" id="tab_serviceunit">
                                <div id="form-serviceunit-rows">
                                    <?php foreach ($serviceUnits as $k => $serviceUnit) { ?>
                                        <?= $this->render('_form-serviceunit', ['model' => $serviceUnit, 'formId' => $k]) ?>
                                    <?php } ?>
                                </div>
                                <button type="button" class="btn btn-default btn-hover-change pull-left form-row-add-serviceunit-btn">Добавить</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 text-center">
                    <div class="form-group">
                        <a href="<?= Yii::$app->request->url ?>" class="btn btn-default">Отменить</a>
                        <button type="submit" class="btn btn-success">Сохранить</button>
                    </div>
                </div>
            </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>

<?php
$formIdServiceNext = $services ? count($services) : 0;
$formIdServiceUnitNext = $serviceUnits ? count($serviceUnits) : 0;
$urlGetFormService = Yii::$app->urlManager->createUrl(['/service/ajax-get-form-service', 'form_id' => '']);
$urlGetFormServiceUnit = Yii::$app->urlManager->createUrl(['/service/ajax-get-form-service-unit', 'form_id' => '']);
$this->registerJs("
    var formIdServiceNext = {$formIdServiceNext};
    var formIdServiceUnitNext = {$formIdServiceUnitNext};

    $(document).on('click', '.form-row-remove-btn', function(e){
        if ($(this).hasClass('disabled')) {
            var msg = $(this).attr('no-delete-msg');
            alert(msg);
        } else {
            if (confirm('Удалить?')) { 
                $(this).parents('.form-service, .form-serviceunit').remove(); 
            }
        }
    });
    
    $(document).on('click', '.form-row-add-service-btn', function(e){
        $.ajax({
            url: '{$urlGetFormService}'+formIdServiceNext,
            dataType: 'json',
            success: function(json) {
                console.log(json);
                $('#form-service-rows').append(json);
                formIdServiceNext++;
            }
        });
    });

    $(document).on('click', '.form-row-add-serviceunit-btn', function(e){
        $.ajax({
            url: '{$urlGetFormServiceUnit}'+formIdServiceUnitNext,
            dataType: 'json',
            success: function(json) {
                console.log(json);
                $('#form-serviceunit-rows').append(json);
                formIdServiceUnitNext++;
            }
        });
    });
");
