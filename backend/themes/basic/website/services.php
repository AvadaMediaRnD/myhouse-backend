<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use common\models\WebsiteService;
use backend\models\WebsiteServiceForm;

/* @var $this yii\web\View */
/* @var $modelForm WebsiteServiceForm */
/* @var $websiteServices WebsiteService[] */

$this->title = Yii::t('app', 'Редактирование страницы');
$this->params['breadcrumbs'][] = 'Редактирование страницы';
?>

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Редактирование страницы "Услуги"</h3>
            </div>
            <div class="box-body">
                <?php $form = ActiveForm::begin([
                    'enableClientValidation' => false,
                    'enableAjaxValidation' => false,
                    'options' => ['enctype' => 'multipart/form-data']
                ]); ?>
                    <div class="row near-img">
                        <div class="col-xs-12">
                            <h3 class="page-header">Услуги</h3>
                        </div>
                        <div id="form-websiteservice-rows">
                            <?php foreach ($websiteServices as $k => $service) { ?>
                                <?= $this->render('_form-service', ['model' => $service, 'formId' => $k]) ?>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h3 class="page-header">Настройки SEO</h3>
                            <?= $form->field($modelForm, 'serviceMetaTitle')->textInput() ?>
                            <?= $form->field($modelForm, 'serviceMetaDescription')->textarea(['rows' => 6]) ?>
                            <?= $form->field($modelForm, 'serviceMetaKeywords')->textarea(['rows' => 6]) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 text-center">
                            <div class="form-group">
                                <a href="<?= Yii::$app->urlManager->createUrl(['/website/services']) ?>" class="btn btn-default">Отменить</a>
                                <button type="button" role="button" class="btn btn-success bg-green-active form-row-add-websiteservice-btn">Добавить услугу</button>
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

$formIdWebsiteServiceNext = count($websiteServices);
$urlGetFormWebsiteService = Yii::$app->urlManager->createUrl(['/website/ajax-get-form-website-service', 'form_id' => '']);

$this->registerJs(<<<JS
    $(function () {
        var formIdWebsiteServiceNext = {$formIdWebsiteServiceNext};
        
        $(document).on('click', '.form-row-remove-btn', function(e){
            if (confirm('Удалить?')) { 
                $(this).parents('.form-websiteservice').remove(); 
            }
        });
        
        $(document).on('click', '.form-row-add-websiteservice-btn', function(e){
            $.ajax({
                url: '{$urlGetFormWebsiteService}'+formIdWebsiteServiceNext,
                dataType: 'json',
                success: function(json) {
                    console.log(json);
                    $('#form-websiteservice-rows').append(json);
                    formIdWebsiteServiceNext++;
                
                    addTextEditor();
                }
            });
        });
    
        //Add text editor
        function addTextEditor() {
            $("textarea.compose-textarea.editor-init").wysihtml5({
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
            }).removeClass('editor-init');
        }
        
        addTextEditor();
    
    });
JS
);
