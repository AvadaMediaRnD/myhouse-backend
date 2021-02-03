<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $phone string */
/* @var $email string */

$this->title = Yii::t('app', 'Пригласить владельца квартир');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Владельцы квартир'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">Отправить приглашение</h3>
    </div>
    <div class="box-body">
        <?php $form = ActiveForm::begin(); ?>
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <div class="form-group field-phone">
                    <label class="control-label" for="phone">Телефон</label>
                    <?= Html::textInput('phone', $phone, ['class' => 'form-control', 'placeholder' => '+380991234567']) ?>
                </div>

                <div class="form-group field-email">
                    <label class="control-label" for="email">Email</label>
                    <?= Html::textInput('email', $email, ['class' => 'form-control', 'placeholder' => 'info@example.com']) ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 text-center">
                <div class="form-group">
                    <?= Html::submitButton('Отправить приглашение', ['class' => 'btn btn-success']) ?>
                </div>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
