<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use common\models\PayCompany;
use common\models\PayCompanyService;
use common\models\Service;
use common\models\ServiceUnit;

/* @var $this yii\web\View */
/* @var $payCompanies \common\models\PayCompany[] */

$this->title = Yii::t('app', 'Платежные реквизиты');
?>

<div class="box">
    <div class="box-body">
        <?php $form = ActiveForm::begin(); ?>
            <div class="row">
                <div class="col-xs-12 col-lg-7">
                    <div class="nav-tabs-custom">
                        <div class="tab-content">
                            <div class="tab-pane clearfix active">
                                <div id="form-paycompany-rows">
                                    <?php foreach ($payCompanies as $k => $payCompany) { ?>
                                        <?= $this->render('_form-paycompany', ['model' => $payCompany, 'formId' => $k]) ?>
                                    <?php } ?>
                                </div>
                                <?php /* ?>
                                <button type="button" class="btn btn-success pull-right form-row-add-paycompany-btn">Добавить</button>
                                <?php }*/ ?>
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
$formIdPayCompanyNext = $payCompanies ? count($payCompanies) : 0;
$urlGetFormPayCompany = Yii::$app->urlManager->createUrl(['/pay-company/ajax-get-form-pay-company', 'form_id' => '']);
$this->registerJs("
    var formIdPayCompanyNext = {$formIdPayCompanyNext};

    $(document).on('click', '.form-row-remove-btn', function(e){
        if (confirm('Удалить?')) { 
            $(this).parents('.form-paycompany').remove(); 
        }
    });
    
    $(document).on('click', '.form-row-add-paycompany-btn', function(e){
        $.ajax({
            url: '{$urlGetFormPayCompany}'+formIdPayCompanyNext,
            dataType: 'json',
            success: function(json) {
                console.log(json);
                $('#form-paycompany-rows').append(json);
                formIdPayCompanyNext++;
            }
        });
    });
");
