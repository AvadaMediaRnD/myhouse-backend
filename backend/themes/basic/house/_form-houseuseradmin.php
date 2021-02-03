<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\UserAdmin;

/* @var $this yii\web\View */
/* @var $model common\models\HouseUserAdmin */
/* @var $form yii\widgets\ActiveForm */
/* @var $formId string */

$formId = (int)$formId;
?>

<div id="form-houseuseradmin-<?= $formId ?>" class="row form-houseuseradmin">
    <div class="col-xs-12 col-sm-7">
        <?= Html::activeHiddenInput($model, "[$formId]id", [
            'id' => "houseuseradmin-$formId-id",
        ]) ?>
        <?= Html::activeHiddenInput($model, "[$formId]house_id", [
            'id' => "houseuseradmin-$formId-house_id",
        ]) ?>
        <div class="form-group">
            <label for="houseuseradmin-<?= $formId ?>-user_admin_id">ФИО</label>
            <?= Html::activeDropDownList($model, "[$formId]user_admin_id", 
                ArrayHelper::map(UserAdmin::find()->all(), 'id', 'fullname'), 
                [
                    'prompt' => 'Выберите...',
                    'id' => "houseuseradmin-$formId-user_admin_id",
                    'class' => 'form-control useradmin-select',
                ]
            ) ?>
        </div>
    </div>
    <div class="col-xs-12 col-sm-5">
        <div class="form-group">
            <label for="houseuseradmin-<?= $formId ?>-role">Роль</label>
            <div class="input-group">
                <input type="text" class="form-control useradmin-role" id="houseuseradmin-<?= $formId ?>-role" value="<?= $model->userAdmin ? $model->userAdmin->getRoleLabel() : '' ?>" readonly>
                <span class="input-group-btn">
                    <?= Html::button(
                    '<i class="fa fa-trash"></i>',
                    [
                        'class' => 'btn btn-danger form-row-remove-btn',
                        'type' => 'button',
                    ]) ?>
                </span>
            </div>
        </div>
    </div>
</div>
