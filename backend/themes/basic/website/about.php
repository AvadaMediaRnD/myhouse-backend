<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use backend\models\WebsiteAboutForm;
use common\models\WebsiteAboutImage;
use common\models\WebsiteDocument;

/* @var $this yii\web\View */
/* @var $modelForm WebsiteAboutForm */
/* @var $imagesMain WebsiteAboutImage[] */
/* @var $imagesAdd WebsiteAboutImage[] */
/* @var $websiteDocuments WebsiteDocument[] */

$this->title = Yii::t('app', 'Редактирование страницы');
$this->params['breadcrumbs'][] = 'Редактирование страницы';
?>

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Редактирование страницы "О нас"</h3>
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
                            <?= $form->field($modelForm, 'aboutTitle')->textInput() ?>
                            <?= $form->field($modelForm, 'aboutDescription')->textarea(['class' => 'compose-textarea form-control', 'placeholder' => 'Текст']) ?>
                        </div>
                        <div class="col-md-4">
                            <h4>Фото директора</h4>
                            <img src="<?= Yii::$app->urlManagerFrontend->createAbsoluteUrl(['/site/glide', 'path' => $modelForm->getImagePath(), 'w' => 250, 'h' => 300]) ?>" alt="">
                            <?= $form->field($modelForm, 'imageFile')->fileInput(['accept' => 'image/*'])->label('Рекомендуемый размер: (250x310)') ?>
                        </div>
                    </div>
                    <div class="row site-about-gallery">
                        <div class="col-xs-12">
                            <h3 class="page-header">Фотогалерея</h3>
                        </div>
                        <?php if ($imagesMain) { ?>
                            <?php foreach ($imagesMain as $image) { ?>
                                <div class="col-xs-4 col-sm-2 text-center">
                                    <img src="<?= Yii::$app->urlManagerFrontend->createAbsoluteUrl(['/site/glide', 'path' => $image->getImagePath(), 'w' => 250, 'h' => 150, 'fit' => 'crop']) ?>" alt="" class="margin-bottom-15 img-thumbnail">
                                    <div class="form-group margin-bottom-15">
                                        <a href="<?= Yii::$app->urlManager->createUrl(['/website/delete-about-image', 'id' => $image->id]) ?>" data-confirm="Удалить?" title="Удалить"><i class="fa fa-trash text-red" aria-hidden="true"></i></a>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } ?>
                        <div class="col-xs-12">
                            <?= $form->field($modelForm, 'aboutImageMainFiles[]')->fileInput(['accept' => 'image/*', 'multiple' => true])->label('Рекомендуемый размер: (1200x1200)') ?>
                        </div>
                    </div>
                    <h3 class="page-header">Дополнительная информация</h3>
                    <div class="row">
                        <div class="col-xs-12">
                            <?= $form->field($modelForm, 'aboutTitle2')->textInput() ?>
                            <?= $form->field($modelForm, 'aboutDescription2')->textarea(['class' => 'compose-textarea form-control', 'placeholder' => 'Текст сообщения']) ?>
                        </div>
                    </div>
                    <div class="row site-about-gallery">
                        <div class="col-xs-12">
                            <h3 class="page-header">Дополнительная фотогалерея</h3>
                        </div>
                        <?php if ($imagesAdd) { ?>
                            <?php foreach ($imagesAdd as $image) { ?>
                                <div class="col-xs-6 col-sm-4 col-md-2 text-center">
                                    <img src="<?= Yii::$app->urlManagerFrontend->createAbsoluteUrl(['/site/glide', 'path' => $image->getImagePath(), 'w' => 250, 'h' => 150, 'fit' => 'crop']) ?>" alt="" class="margin-bottom-15 img-thumbnail">
                                    <div class="form-group margin-bottom-15">
                                        <a href="<?= Yii::$app->urlManager->createUrl(['/website/delete-about-image', 'id' => $image->id]) ?>" data-confirm="Удалить?" title="Удалить"><i class="fa fa-trash text-red" aria-hidden="true"></i></a>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } ?>
                        <div class="col-xs-12">
                            <?= $form->field($modelForm, 'aboutImageAddFiles[]')->fileInput(['accept' => 'image/*', 'multiple' => true])->label('Рекомендуемый размер: (1200x1200)') ?>
                        </div>
                    </div>
                    
                    <div class="row site-about-documents">
                        <div class="col-xs-12">
                            <h3 class="page-header">Документы</h3>
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <div id="form-websitedocument-rows">
                                <?php foreach ($websiteDocuments as $k => $document) { ?>
                                    <?= $this->render('_form-document', ['model' => $document, 'formId' => $k]) ?>
                                <?php } ?>
                            </div>
                            <div class="btn-group">
                                <button type="button" class="btn btn-success margin-bottom-15 form-row-add-websitedocument-btn">
                                    Добавить документ
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <h3 class="page-header">Настройки SEO</h3>
                            <?= $form->field($modelForm, 'aboutMetaTitle')->textInput() ?>
                            <?= $form->field($modelForm, 'aboutMetaDescription')->textarea(['rows' => 6]) ?>
                            <?= $form->field($modelForm, 'aboutMetaKeywords')->textarea(['rows' => 6]) ?>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-xs-12 text-center">
                            <div class="form-group">
                                <a href="<?= Yii::$app->urlManager->createUrl(['/website/about']) ?>" class="btn btn-default">Отменить</a>
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

$formIdWebsiteDocumentNext = count($websiteDocuments);
$urlGetFormWebsiteDocument = Yii::$app->urlManager->createUrl(['/website/ajax-get-form-website-document', 'form_id' => '']);

$this->registerJs(<<<JS
    $(function () {
        var formIdWebsiteDocumentNext = {$formIdWebsiteDocumentNext};
        
        $(document).on('click', '.form-row-remove-btn', function(e){
            if (confirm('Удалить?')) { 
                $(this).parents('.form-websitedocument').remove(); 
            }
        });
        
        $(document).on('click', '.form-row-add-websitedocument-btn', function(e){
            $.ajax({
                url: '{$urlGetFormWebsiteDocument}'+formIdWebsiteDocumentNext,
                dataType: 'json',
                success: function(json) {
                    console.log(json);
                    $('#form-websitedocument-rows').append(json);
                    formIdWebsiteDocumentNext++;
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
