<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Message;
use common\models\MessageAddress;
use common\models\User;
use common\models\House;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;

/* @var $this yii\web\View */



$address = null;
if ($model->messageAddress) {
    $address = $model->messageAddress;
} else {
    $messageAddress = new MessageAddress();
    $address = $messageAddress;

    $messageAddressParams = Yii::$app->request->get('MessageAddress');
    if ($messageAddressParams) {
        if ($userHasDebt = (int)$messageAddressParams['user_has_debt']) {
            $address->user_has_debt = $userHasDebt;
        }
        if ($houseId = (int)$messageAddressParams['house_id']) {
            $address->house_id = $houseId;
        }
        if ($userId = (int)$messageAddressParams['user_id']) {
            $address->user_id = $userId;
        }
    }
}

$messageParams = Yii::$app->request->get('Message');
if ($messageParams) {
    if ($type = $messageParams['type']) {
        $model->type = $type;
    }
}

if ($model->isNewRecord) {
    $model->status = Message::STATUS_WAITING;
}
if (!$model->type) {
    $model->type = Message::TYPE_DEFAULT;
}

$userSelectData = ArrayHelper::map(User::find()->all(), 'id', 'fullname');

?>

<div class="message-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['maxlength' => true, 'rows' => 6]) ?>

    <?= $form->field($model, 'type')->dropDownList(
        Message::getTypeOptions(),
        ['prompt' => '']
    ) ?>

    <?= $form->field($model, 'status')->dropDownList(
        Message::getStatusOptions(),
        ['prompt' => '']
    ) ?>

<!--    --><?//= $form->field($model, 'status')->textInput() ?>

    <h3>Кому отправить</h3>

    <?php /*= $form->field($address, 'user_id')->dropDownList(
        ArrayHelper::map(User::find()->all(), 'id', function ($model) { return $model->fullname . ' - id:' . $model->id . ' - тел:' . $model->username; }),
        ['prompt' => 'Выберите...']
    )*/ ?>

    <?= $form->field($address, 'user_has_debt')->checkbox(['class' => ''])
        ->hint('Если указан Владелец квартир, то этот параметр игнорируется')
    ?>

    <?= $form->field($address, 'user_id')->widget(Select2::classname(), [
        'data' => $userSelectData,
        'language' => 'ru',
        'theme' => Select2::THEME_DEFAULT,
        'options' => ['placeholder' => 'Выберите...', 'class' => 'form-control'],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]) ?>

    <?= $form->field($address, 'house_id')->dropDownList(
        ArrayHelper::map(House::find()->all(), 'id', 'name'),
        [
            'prompt' => 'Выберите...',
            'onchange'=>'
                $.get("' . urldecode(Yii::$app->urlManager->createUrl(['/flat/get-lists-by-house', 'id' => ''])) . '"+$(this).val(), function( data ) {
                    $("select#messageaddress-section_id").html(data.sections);
                    $("select#messageaddress-riser_id").html(data.risers);
                    $("select#messageaddress-floor_id").html(data.floors);
                });
            ',
        ]
    ) ?>

    <?= $form->field($address, 'section_id')->dropDownList(
        $address->house ? ArrayHelper::map($address->house->sections, 'id', 'name') : [],
        ['prompt' => 'Выберите...']
    ) ?>

    <?= $form->field($address, 'riser_id')->dropDownList(
        $address->house ? ArrayHelper::map($address->house->risers, 'id', 'name') : [],
        ['prompt' => 'Выберите...']
    ) ?>

    <?= $form->field($address, 'floor_id')->dropDownList(
        $address->house ? ArrayHelper::map($address->house->floors, 'id', 'name') : [],
        ['prompt' => 'Выберите...']
    ) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Сохранить'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
