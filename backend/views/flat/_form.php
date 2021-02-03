<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\House;
use common\models\User;
use kartik\select2\Select2;

/* @var $this yii\web\View */



$userSelectData = ArrayHelper::map(User::find()->all(), 'id', function ($model) {
    $name = $model->profile ? $model->getFullname() : '';
    if (!$name) {
        $name = $model->username;
    } elseif ($name != $model->username) {
        $name .= ' ('.$model->username.')';
    }
    return $name;
});

?>

<div class="flat-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'flat')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'house_id')->dropDownList(
        ArrayHelper::map(House::find()->all(), 'id', 'name'),
        [
            'prompt' => 'Выберите...',
            'onchange'=>'
                $.get("' . urldecode(Yii::$app->urlManager->createUrl(['/flat/get-lists-by-house', 'id' => ''])) . '"+$(this).val(), function( data ) {
                    $("select#flat-section_id").html(data.sections);
                    $("select#flat-riser_id").html(data.risers);
                    $("select#flat-floor_id").html(data.floors);
                });
            ',
        ]
    ) ?>

    <?= $form->field($model, 'section_id')->dropDownList(
        $model->house ? ArrayHelper::map($model->house->sections, 'id', 'name') : [],
        ['prompt' => 'Выберите...']
    ) ?>

    <?= $form->field($model, 'riser_id')->dropDownList(
        $model->house ? ArrayHelper::map($model->house->risers, 'id', 'name') : [],
        ['prompt' => 'Выберите...']
    ) ?>

    <?= $form->field($model, 'floor_id')->dropDownList(
        $model->house ? ArrayHelper::map($model->house->floors, 'id', 'name') : [],
        ['prompt' => 'Выберите...']
    ) ?>

    <? /*= $form->field($model, 'user_id')->dropDownList(
        ArrayHelper::map(User::find()->all(), 'id', function ($model) { return $model->fullname . ' - id:' . $model->id . ' - тел:' . $model->username; }),
        ['prompt' => 'Выберите...']
    )*/ ?>

    <?= $form->field($model, 'user_id')->widget(Select2::classname(), [
        'data' => $userSelectData,
        'language' => 'ru',
        'theme' => Select2::THEME_DEFAULT,
        'options' => ['placeholder' => 'Выберите...', 'class' => 'form-control'],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Сохранить'), ['class' => 'btn btn-success']) ?>
        <?php if ($model->id) { ?>
            <a class="btn btn-primary" href="<?= Yii::$app->urlManager->createUrl(['/invoice/index', 'InvoiceSearch[flat_id]' => $model->id]) ?>">Посмотреть квитанции</a>
        <?php } ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
