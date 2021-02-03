<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $phone string */
/* @var $email string */

$this->title = Yii::t('app', 'Добавить владельца квартир');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Владельцы квартир'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create">

    <?php $form = ActiveForm::begin(); ?>

    <div class="form-group field-phone">
        <label class="control-label" for="phone">Телефон</label>
        <?= Html::textInput('phone', $phone, ['class' => 'form-control', 'placeholder' => '+380991234567']) ?>
    </div>

    <div class="form-group field-email">
        <label class="control-label" for="email">Email</label>
        <?= Html::textInput('email', $email, ['class' => 'form-control', 'placeholder' => 'info@avada-media.com']) ?>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Отправить приглашение', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
