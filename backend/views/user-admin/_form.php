<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\UserAdmin */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'firstname')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'lastname')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => true])
        ->label(Yii::t('model', 'Email (логин)'))?>

    <?php if ($model->id != Yii::$app->user->id) { ?>
        <?= $form->field($model, 'status')->textInput() ?>
    <?php } ?>

    <?= $form->field($model, 'password_old')->passwordInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'password1')->passwordInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'password2')->passwordInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Сохранить'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
