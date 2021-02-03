<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use backend\models\WebsiteHomeForm;
use common\models\WebsiteHomeSlide;
use common\models\WebsiteHomeFeature;

/* @var $this yii\web\View */
/* @var $modelForm WebsiteHomeForm */
/* @var $slides WebsiteHomeSlide[] */
/* @var $slides WebsiteHomeFeature[] */

$this->title = Yii::t('app', 'Редактирование страницы');
$this->params['breadcrumbs'][] = 'Редактирование страницы';
?>

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Редактирование страницы "Главная"</h3>
                <div class="box-tools">
                    <a href="<?= Yii::$app->urlManager->createUrl(['/website/update-seo-files']) ?>" class="btn btn-default btn-sm updateSeoFiles">
                        <span class="hidden-xs">Обновить robots и sitemap</span><i class="fa fa-refresh visible-xs" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
            <div class="box-body">
                <?php $form = ActiveForm::begin([
                    'enableClientValidation' => false,
                    'enableAjaxValidation' => false,
                    'options' => ['enctype' => 'multipart/form-data']
                ]); ?>
                    <h3 class="page-header">Слайдер</h3>
                    <div class="row site-main-slider">
                        <?php if ($slides) { ?>
                            <?php foreach ($slides as $k => $slide) { ?>
                                <div class="col-md-4">
                                    <h4>Слайд <?= $k + 1 ?></h4>
                                    <img src="<?= Yii::$app->urlManagerFrontend->createAbsoluteUrl(['/site/glide', 'path' => $slide->getImagePath(), 'w' => 522, 'h' => 300, 'fit' => 'crop']) ?>" alt="" class="img-responsive margin-bottom-15">
                                    <?= $form->field($slide, "[$k]id")->hiddenInput()->label(false) ?>
                                    <?= $form->field($slide, "[$k]imageFile", ['options' => ['class' => 'form-group margin-bottom-30']])->fileInput(['accept' => 'image/*'])->label('Рекомендуемый размер: (1920x800)') ?>
                                    <?php // echo $form->field($slide, "[$k]title")->textInput() ?>
                                </div>
                            <?php } ?>
                        <?php } ?>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h3 class="page-header">Краткая информация</h3>
                            <?= $form->field($modelForm, 'homeTitle')->textInput() ?>
                            <?= $form->field($modelForm, 'homeDescription')->textarea(['class' => 'compose-textarea form-control', 'placeholder' => 'Текст сообщения']) ?>
                            <?= $form->field($modelForm, 'homeIsShowApps')->checkbox() ?>
                        </div>
                        <?php /* ?><div class="col-md-4">
                            <h3 class="page-header">Контакты</h3>
                            <?= $form->field($modelForm, 'contactFullname')->textInput() ?>
                            <?= $form->field($modelForm, 'contactLocation')->textInput() ?>
                            <?= $form->field($modelForm, 'contactAddress')->textInput() ?>
                            <?= $form->field($modelForm, 'contactPhone')->textInput() ?>
                            <?= $form->field($modelForm, 'contactEmail')->textInput() ?>
                        </div><?php */ ?>
                    </div>
                    <div class="row near-img">
                        <div class="col-xs-12">
                            <h3 class="page-header">Рядом с нами</h3>
                        </div>
                        <?php if ($features) { ?>
                            <?php foreach ($features as $k => $feature) { ?>
                                <div class="col-md-4">
                                    <h4>Блок <?= $k + 1 ?></h4>
                                    <img src="<?= Yii::$app->urlManagerFrontend->createAbsoluteUrl(['/site/glide', 'path' => $feature->getImagePath(), 'w' => 1000, 'h' => 600, 'fit' => 'crop']) ?>" alt="" class="img-responsive margin-bottom-15">
                                    <?= $form->field($feature, "[$k]id")->hiddenInput()->label(false) ?>
                                    <?= $form->field($feature, "[$k]imageFile")->fileInput(['accept' => 'image/*'])->label('Рекомендуемый размер: (1000x600)') ?>
                                    <?= $form->field($feature, "[$k]title")->textInput() ?>
                                    <?= $form->field($feature, "[$k]description", ['options' => ['class' => 'form-group margin-bottom-30']])->textarea(['class' => 'compose-textarea form-control']) ?>
                                </div>
                            <?php } ?>
                        <?php } ?>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h3 class="page-header">Настройки SEO</h3>
                            <?= $form->field($modelForm, 'homeMetaTitle')->textInput() ?>
                            <?= $form->field($modelForm, 'homeMetaDescription')->textarea(['rows' => 6]) ?>
                            <?= $form->field($modelForm, 'homeMetaKeywords')->textarea(['rows' => 6]) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 text-center">
                            <div class="form-group">
                                <a href="<?= Yii::$app->urlManager->createUrl(['/website/home']) ?>" class="btn btn-default">Отменить</a>
                                <button type="submit" class="btn btn-success">Сохранить</button>
                            </div>
                        </div>
                    </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

<?php $this->registerJs(<<<JS
    $(function () {
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
    
        $('.updateSeoFiles').on('click', function(e) {
            e.preventDefault();
            if (confirm('Обновить robots.txt и sitemap.xml?')) {
                $.get($(this).attr('href'), function(data) {
                    console.log(data);
                    alert('robots.txt и sitemap.xml обновлены.');
                });
            }
        });
    });
JS
);
