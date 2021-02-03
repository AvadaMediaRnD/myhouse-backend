<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\Flat;
use common\models\House;
use common\models\MasterRequest;

/* @var $this yii\web\View */
/* @var $model common\models\MasterRequest */
/* @var $form yii\widgets\ActiveForm */

$flatModel = new Flat();
if (!$model->isNewRecord && $model->flat) {
    $flatModel->house_id = $model->flat->house_id;
}
?>

<div class="master-request-form">

    <?php if (!$model->isNewRecord) { ?>
        <?php if ($model->flat->user) { ?>
        <p>Владелец:
            <a href="<?= Yii::$app->urlManager->createUrl(['/user/update', 'id' => $model->flat->user_id]) ?>" target="_blank">
                <?= $model->flat->user->getFullname() ?>
            </a>
        </p>
        <p>Телефон: <span><?= $model->flat->user->username ?></span></p>
        <?php } else { ?>
            <p class="text-danger">Квартира не привязана к пользователю</p>
        <?php } ?>
        <hr/>
    <?php } ?>


    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->dropDownList(
        MasterRequest::getStatusOptions(),
        ['prompt' => '']
    ) ?>

    <?= $form->field($flatModel, 'house_id')->dropDownList(
        ArrayHelper::map(House::find()->all(), 'id', 'name'),
        [
            'prompt' => 'Выберите...',
            'onchange'=>'
                $.get("' . urldecode(Yii::$app->urlManager->createUrl(['/flat/get-lists-by-house', 'id' => ''])) . '"+$(this).val(), function( data ) {
                    $("select#masterrequest-flat_id").html(data.flats);
                });
            ',
        ]
    ) ?>

    <?= $form->field($model, 'flat_id')->dropDownList(
        ArrayHelper::map(Flat::find()->orderBy(['house_id' => SORT_ASC])->all(), 'id', function($model) {
            return ($model->house ? $model->house->name : '') . ', №' . $model->flat;
        })
    ) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Сохранить'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
