<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\User;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $modelForm backend\models\UserForm */
/* @var $form yii\widgets\ActiveForm */

?>

<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

<input type="hidden" id="is_new_record" value="<?= Yii::$app->request->get('id') ? 0 : 1 ?>">

<div class="row">
    <div class="col-xs-12 col-sm-6">
        <div class="userAvatar">
            <img class="img-circle pull-left img-responsive" src="<?= Yii::$app->urlManagerFrontend->createAbsoluteUrl(['/site/glide', 'path' => $modelForm->getAvatar(), 'w' => 160, 'h' => 160, 'fit' => 'crop']) ?>">
            <?= $form->field($modelForm, 'image')->fileInput(['accept' => 'image/*']) ?>
        </div>
    </div>
    <div class="col-xs-12 col-sm-6">
        <?= $form->field($modelForm, 'status')->dropDownList(User::getStatusOptions()) ?>
        <?= $form->field($modelForm, 'uid')->textInput() ?>
    </div>
</div>
<div class="row">
    <div class="col-xs-12 col-sm-6">
        <?= $form->field($modelForm, 'lastname')->textInput() ?>
        <?= $form->field($modelForm, 'firstname')->textInput() ?>
        <?= $form->field($modelForm, 'middlename')->textInput() ?>
        <?= $form->field($modelForm, 'birthdate')->widget(DatePicker::className(), [
            'type' => DatePicker::TYPE_COMPONENT_APPEND,
            'removeButton' => false,
            'pluginOptions' => [
                'autoclose' => true,
                'format' => 'dd.mm.yyyy'
            ]
        ]) ?>
    </div>
    <div class="col-xs-12 col-sm-6">
        <?= $form->field($modelForm, 'note')->textarea(['rows' => 10, 'style' => 'height: 256px']) ?>
    </div>
</div>
<div class="row">
    <div class="col-xs-12 col-md-6">
        <h2 class="page-header">Контактные данные</h2>
        <?= $form->field($modelForm, 'phone')->textInput() ?>
        <?= $form->field($modelForm, 'viber')->textInput() ?>
        <?= $form->field($modelForm, 'telegram')->textInput() ?>
        <?= $form->field($modelForm, 'email')->textInput() ?>
    </div>
    <div class="col-xs-12 col-md-6">
        <h2 class="page-header">Изменить пароль</h2>
        <?= $form->field($modelForm, 'password', [
            'template' => "{label}\n
                <div class=\"input-group\">{input}\n
                    <span class=\"input-group-btn\">
                        <button class=\"btn btn-default\" type=\"button\" onclick=\"generatePassword('.pass-value')\">
                            Сгенерировать
                        </button>
                        <button type=\"button\" class=\"btn btn-primary\" id=\"showPass\">
                            <i class=\"fa fa-eye\" aria-hidden=\"true\"></i>
                        </button>
                    </span>
                </div>\n{hint}\n{error}"
        ])->passwordInput(['maxlength' => true, 'class' => 'form-control pass-value']) ?>
        <?= $form->field($modelForm, 'password2')->passwordInput(['maxlength' => true, 'class' => 'form-control pass-value']) ?>
    </div>
</div>
<div class="row">
    <div class="col-xs-12 text-right">
        <a href="<?= Yii::$app->urlManager->createUrl(['/user/index']) ?>" class="btn btn-default">Отменить</a>
        <?= Html::submitButton(Yii::t('app', 'Сохранить'), ['class' => 'btn btn-success']) ?>
    </div>
</div>
    
<?php ActiveForm::end(); ?>

<script>
    function generatePassword(targetSelector) {
        var pass = Math.random().toString(36).substring(4);
        $('input'+targetSelector).val(pass);
        $('span'+targetSelector).text(pass);
    }
</script>
