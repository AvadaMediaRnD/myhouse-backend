<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\UserAdmin;
use backend\models\UserAdminForm;

/* @var $this yii\web\View */
/* @var $model common\models\UserAdmin */
/* @var $form yii\widgets\ActiveForm */
/* @var $modelForm backend\models\UserAdminForm */
?>

<?php $form = ActiveForm::begin(); ?>

<div class="row">
    <div class="col-xs-12 col-md-6">
        <?= $form->field($modelForm, 'firstname')->textInput(['maxlength' => true]) ?>
        <?= $form->field($modelForm, 'lastname')->textInput(['maxlength' => true]) ?>
        <?= $form->field($modelForm, 'phone')->textInput(['maxlength' => true]) ?>
        <?php if ($model->id != Yii::$app->user->id) { ?>
            <?= $form->field($modelForm, 'role')->dropDownList(UserAdmin::getRoleOptions()) ?>
            <?= $form->field($modelForm, 'status')->dropDownList(UserAdmin::getStatusOptions()) ?>
        <?php } ?>
    </div>

    <div class="col-xs-12 col-md-6">
        <?= $form->field($modelForm, 'username')->textInput(['maxlength' => true])
            ->label(Yii::t('model', 'Email (логин)')) ?>
        
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
        <a href="<?= Yii::$app->urlManager->createUrl(['/user-admin/index']) ?>" class="btn btn-default">Отменить</a>
        <button type="submit" class="btn btn-success">Сохранить</button>
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
