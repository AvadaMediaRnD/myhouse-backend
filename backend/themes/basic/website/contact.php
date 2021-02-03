<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use backend\models\WebsiteContactForm;

/* @var $this yii\web\View */
/* @var $modelForm WebsiteContactForm */

$this->title = Yii::t('app', 'Редактирование страницы');
$this->params['breadcrumbs'][] = 'Редактирование страницы';
?>

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Редактирование страницы "Контакты"</h3>
            </div>
            <div class="box-body">
                <?php $form = ActiveForm::begin([
                    'enableClientValidation' => false,
                    'enableAjaxValidation' => false,
                ]); ?>
                    <div class="row">
                        <div class="col-xs-12 col-md-8">
                            <h3 class="page-header">Контактная информация</h3>
                            <?= $form->field($modelForm, 'contactTitle')->textInput() ?>
                            <?= $form->field($modelForm, 'contactDescription')->textarea(['class' => 'compose-textarea form-control', 'placeholder' => 'Текст сообщения']) ?>
                            <?= $form->field($modelForm, 'contactUrlSite')->textInput() ?>
                        </div>
                        <div class="col-xs-12 col-md-4">
                            <h3 class="page-header">Контакты</h3>
                            <?= $form->field($modelForm, 'contactFullname')->textInput() ?>
                            <?= $form->field($modelForm, 'contactLocation')->textInput() ?>
                            <?= $form->field($modelForm, 'contactAddress')->textInput() ?>
                            <?= $form->field($modelForm, 'contactPhone')->textInput() ?>
                            <?= $form->field($modelForm, 'contactEmail')->textInput() ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <h3 class="page-header">Карта</h3>
                            <?= $form->field($modelForm, 'contactMapEmbedCode')->textarea(['rows' => 5, 'placeholder' => 'Вставьте код карты сюда']) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h3 class="page-header">Настройки SEO</h3>
                            <?= $form->field($modelForm, 'contactMetaTitle')->textInput() ?>
                            <?= $form->field($modelForm, 'contactMetaDescription')->textarea(['rows' => 6]) ?>
                            <?= $form->field($modelForm, 'contactMetaKeywords')->textarea(['rows' => 6]) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 text-center">
                            <div class="form-group">
                                <a href="<?= Yii::$app->urlManager->createUrl(['/website/contact']) ?>" class="btn btn-default">Отменить</a>
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
    });
JS
);
