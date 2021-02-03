<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \cabinet\models\LoginForm */

$this->title = 'Вход';

$fieldOptions1 = [
    'options' => ['class' => 'form-group has-feedback'],
    'template' => '{input}',
    'inputTemplate' => "{input}<span class='fa fa-envelope form-control-feedback'></span>"
];

$fieldOptions2 = [
    'options' => ['class' => 'form-group has-feedback'],
    'template' => '{input}',
    'inputTemplate' => "{input}<span class='fa fa-lock form-control-feedback'></span>"
];
?>



<div class="login-box">
    <div class="login-logo">
        <a href="<?= Yii::$app->urlManagerFrontend->createAbsoluteUrl('/') ?>">
            <img src="<?= Yii::$app->urlManagerFrontend->createAbsoluteUrl('/logo.svg') ?>" alt="logo">
        </a>
    </div>
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#tab_1" data-toggle="tab" aria-expanded="true">Для жильца</a></li>
            <li><a href="<?= Yii::$app->urlManagerBackend->createAbsoluteUrl(['/site/login']) ?>">Для администрации</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="tab_1">
                <div class="login-box-body">
                    
                    <div id="preloader"></div>
                    
                    <p class="login-box-msg">Вход в личный кабинет</p>

                    <?php $form = ActiveForm::begin(['id' => 'login-form', 'enableClientValidation' => false]); ?>
                        
                        <?= $form->field($model, 'username', $fieldOptions1)->label(false)->textInput(['placeholder' => $model->getAttributeLabel('username')]) ?>
                        <?= $form->field($model, 'password', $fieldOptions2)->label(false)->passwordInput(['placeholder' => $model->getAttributeLabel('password')]) ?>
                        
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="checkbox icheck">
                                    <?= Html::activeCheckbox($model, 'rememberMe') ?>
                                </div>
                            </div>
                            <div class="col-xs-12">
                                <?= Html::submitButton(Yii::t('app', 'Вход'), ['class' => 'btn btn-primary btn-block', 'name' => 'login-button']) ?>
                            </div>
                        </div>
                    
                    <?php ActiveForm::end(); ?>
                    
                    <?php /* ?>
                    <a href="#!">Забыли пароль?</a><br>
                    <a href="#!">Регистрация</a>
                    <?php */ ?>
                </div>
            </div>
            
            <?php if (false) { ?>
                <div class="tab-pane" id="tab_2">
                    <div class="login-box-body">
                        <p class="login-box-msg">Вход в панель управления</p>

                        <?php $form = ActiveForm::begin(['id' => 'login-form', 'enableClientValidation' => false]); ?>

                            <?= $form->field($model, 'username', $fieldOptions1)->label(false)->textInput(['placeholder' => $model->getAttributeLabel('username')]) ?>
                            <?= $form->field($model, 'password', $fieldOptions2)->label(false)->passwordInput(['placeholder' => $model->getAttributeLabel('password')]) ?>

                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="checkbox icheck">
                                        <?= Html::activeCheckbox($model, 'rememberMe') ?>
                                    </div>
                                </div>
                                <div class="col-xs-12">
                                    <?= Html::submitButton(Yii::t('app', 'Вход'), ['class' => 'btn btn-primary btn-block', 'name' => 'login-button']) ?>
                                </div>
                            </div>

                        <?php ActiveForm::end(); ?>

                        <?php /* ?>
                        <a href="#!">Забыли пароль?</a><br>
                        <a href="#!">Регистрация</a>
                        <?php */ ?>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<?php $this->registerJs(<<<JS
   $(function ($) {
        //Enable iCheck plugin for checkboxes
        //iCheck for checkbox and radio inputs
        $('.login-box-body input[type="checkbox"]').iCheck({
            checkboxClass: 'icheckbox_flat-blue',
            radioClass: 'icheckbox_flat-blue'
        });
    
        $('#preloader').fadeOut('slow',function(){
            $(this).remove();
        });
    });
JS
); ?>
