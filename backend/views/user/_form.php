<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\User;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $modelForm backend\models\UserForm */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <input type="hidden" id="is_new_record" value="<?= Yii::$app->request->get('id') ? 0 : 1 ?>">

    <?= $form->field($modelForm, 'username')->textInput(['maxlength' => true]) ?>

    <?= $form->field($modelForm, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($modelForm, 'firstname')->textInput(['maxlength' => true]) ?>

    <?= $form->field($modelForm, 'lastname')->textInput(['maxlength' => true]) ?>

    <?= $form->field($modelForm, 'middlename')->textInput(['maxlength' => true]) ?>

    <?= $form->field($modelForm, 'birthdate')->widget(DatePicker::className(), [
//        'value' => $modelForm->birthdate,
        'type' => DatePicker::TYPE_INPUT,
        'pluginOptions' => [
            'autoclose'=>true,
            'format' => 'yyyy-mm-dd'
        ]
    ]) ?>

    <?= $form->field($modelForm, 'status')->dropDownList(User::getStatusOptions()) ?>

    <?php //= $form->field($modelForm, 'password')->passwordInput(['maxlength' => true]) ?>

    <?= $form->field($modelForm, 'password', [
        'template' => "{label} <span class=\"pass-value\"></span>\n
            <div class=\"input-group\">{input}\n
                <span class=\"input-group-btn\">
                    <button class=\"btn btn-default\" type=\"button\" onclick=\"generatePassword('.pass-value')\">
                        <i class=\"glyphicon glyphicon-refresh\"></i> Сгенерировать
                    </button>
                </span>
            </div>\n{hint}\n{error}"
    ])->passwordInput(['maxlength' => true, 'class' => 'form-control pass-value']) ?>

    <?= $form->field($modelForm, 'password2')->passwordInput(['maxlength' => true, 'class' => 'form-control pass-value']) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Сохранить'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<script>
    function generatePassword(targetSelector) {
        var pass = Math.random().toString(36).substring(6);
        $('input'+targetSelector).val(pass);
        $('span'+targetSelector).text(pass);
    }
</script>
