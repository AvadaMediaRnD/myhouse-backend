<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use backend\models\WebsiteTariffForm;
use common\models\WebsiteTariff;

/* @var $this yii\web\View */
/* @var $modelForm WebsiteTariffForm */
/* @var $websiteTariffs WebsiteTariff[] */

$this->title = Yii::t('app', 'Редактирование страницы');
$this->params['breadcrumbs'][] = 'Редактирование страницы';
?>

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Редактирование страницы "Тарифы"</h3>
            </div>
            <div class="box-body">
                <?php $form = ActiveForm::begin([
                    'enableClientValidation' => false,
                    'enableAjaxValidation' => false,
                    'options' => ['enctype' => 'multipart/form-data']
                ]); ?>
                
                    <h3 class="page-header">Информация</h3>
                    <div class="row">
                        <div class="col-md-8">
                            <?= $form->field($modelForm, 'tariffTitle')->textInput() ?>
                            <?= $form->field($modelForm, 'tariffDescription')->textarea(['class' => 'compose-textarea form-control', 'placeholder' => 'Текст']) ?>
                        </div>
                    </div>
                    
                    <div class="row near-img">
                        <div class="col-xs-12">
                            <h3 class="page-header">Изображения</h3>
                        </div>
                        <div id="form-websitetariff-rows">
                            <?php foreach ($websiteTariffs as $k => $tariff) { ?>
                                <?= $this->render('_form-tariff', ['model' => $tariff, 'formId' => $k]) ?>
                            <?php } ?>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <h3 class="page-header">Настройки SEO</h3>
                            <?= $form->field($modelForm, 'tariffMetaTitle')->textInput() ?>
                            <?= $form->field($modelForm, 'tariffMetaDescription')->textarea(['rows' => 6]) ?>
                            <?= $form->field($modelForm, 'tariffMetaKeywords')->textarea(['rows' => 6]) ?>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-xs-12 text-center">
                            <div class="form-group">
                                <a href="<?= Yii::$app->urlManager->createUrl(['/website/tariffs']) ?>" class="btn btn-default">Отменить</a>
                                <button type="button" role="button" class="btn btn-success bg-green-active form-row-add-websitetariff-btn">Добавить тариф</button>
                                <button type="submit" class="btn btn-success">Сохранить</button>
                            </div>
                        </div>
                    </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

<?php 

$formIdWebsiteTariffNext = count($websiteTariffs);
$urlGetFormWebsiteTariff = Yii::$app->urlManager->createUrl(['/website/ajax-get-form-website-tariff', 'form_id' => '']);

$this->registerJs(<<<JS
    $(function () {
        var formIdWebsiteTariffNext = {$formIdWebsiteTariffNext};
        
        $(document).on('click', '.form-row-remove-btn', function(e){
            if (confirm('Удалить?')) { 
                $(this).parents('.form-websitetariff').remove(); 
            }
        });
        
        $(document).on('click', '.form-row-add-websitetariff-btn', function(e){
            $.ajax({
                url: '{$urlGetFormWebsiteTariff}'+formIdWebsiteTariffNext,
                dataType: 'json',
                success: function(json) {
                    console.log(json);
                    $('#form-websitetariff-rows').append(json);
                    formIdWebsiteTariffNext++;
                }
            });
        });
        
        //Add text editor
        $("textarea.compose-textarea").wysihtml5({
            locale: 'ru-RU',
            toolbar: {
                "font-styles": true, //Font styling, e.g. h1, h2, etc. Default true
                "emphasis": true, //Italics, bold, etc. Default true
                "lists": true, //(Un)ordered lists, e.g. Bullets, Numbers. Default true
                "html": false, //Button which allows you to edit the generated HTML. Default false
                "link": false, //Button to insert a link. Default true
                "image": false, //Button to insert an image. Default true,
                "color": false, //Button to change color of font
                "blockquote": false, //Blockquote
                "fa": true,
                "size": 'none' //default: none, other options are xs, sm, lg
            }
        });
    });
JS
);
