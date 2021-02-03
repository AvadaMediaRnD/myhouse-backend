<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\House;
use common\models\User;
use common\models\Tariff;
use common\models\Account;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model \common\models\Flat */
/* @var $modelForm \backend\models\FlatForm */

$userSelectData = ArrayHelper::map(User::find()->all(), 'id', 'fullname');
$accountSelectData = ArrayHelper::map(
    Account::find()->where(['is', 'flat_id', null])->andWhere(['!=', 'status', Account::STATUS_DISABLED])->all(), 
    'uid', 'uid');
?>

<?php $form = ActiveForm::begin(); ?>
<div class="row">
    <div class="col-xs-12 col-sm-6 col-sm-push-6">
        <?php // echo $form->field($modelForm, 'account_uid')->textInput(['maxlength' => true]) ?>
        <div class="form-group field-flatform-account_uid <?= $modelForm->hasErrors('account_uid') ? 'has-error' : '' ?>">
            <label class="control-label" for="flatform-account_uid"><?= $modelForm->getAttributeLabel('account_uid') ?></label>
            <?= Html::activeTextInput($modelForm, 'account_uid', ['class' => 'form-control', 'id' => 'flatform-account_uid']) ?>
            <?= Select2::widget([
                'id' => 'account_uid',
                'name' => 'account_uid',
                'data' => $accountSelectData,
                'language' => 'ru',
                'theme' => Select2::THEME_DEFAULT,
                'options' => [
                    'placeholder' => 'или выберите из списка...', 
                    'class' => 'form-control',
                    'onchange' => 'if ($(this).val()) { $("#flatform-account_uid").val($(this).val()); $("#account_uid").val("").trigger("change"); }',
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]) ?>
            <div class="help-block"><?= $modelForm->hasErrors('account_uid') ? $modelForm->getErrors('account_uid')[0] : '' ?></div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-sm-pull-6">
        <?= Html::activeHiddenInput($modelForm, 'id') ?>
        <?= $form->field($modelForm, 'flat')->textInput(['maxlength' => true]) ?>
        <?= $form->field($modelForm, 'square')->textInput(['maxlength' => true]) ?>
        <?= $form->field($modelForm, 'house_id')->dropDownList(
            House::getOptions(),
            [
                'prompt' => 'Выберите...',
                'onchange'=>'
                    $.get("' . urldecode(Yii::$app->urlManager->createUrl(['/flat/get-lists-by-house', 'house_id' => ''])) . '"+$(this).val(), function(data) {
                        $("select#flatform-section_id").html(data.sections);
                        $("select#flatform-riser_id").html(data.risers);
                        $("select#flatform-floor_id").html(data.floors);
                    });
                ',
            ]
        ) ?>
        <?= $form->field($modelForm, 'section_id')->dropDownList(
            $model->house ? ArrayHelper::map($model->house->sections, 'id', 'name') : [],
            ['prompt' => 'Выберите...']
        ) ?>

        <?= $form->field($modelForm, 'floor_id')->dropDownList(
            $model->house ? ArrayHelper::map($model->house->floors, 'id', 'name') : [],
            ['prompt' => 'Выберите...']
        ) ?>

        <?php /* echo $form->field($modelForm, 'riser_id')->dropDownList(
            $model->house ? ArrayHelper::map($model->house->risers, 'id', 'name') : [],
            ['prompt' => 'Выберите...']
        )*/ ?>
        <?php /* echo $form->field($modelForm, 'user_id')->dropDownList(
            ArrayHelper::map(User::find()->all(), 'id', function ($model) { return $model->fullname . ' - id:' . $model->id . ' - тел:' . $model->username; }),
            ['prompt' => 'Выберите...']
        )*/ ?>
        <?= $form->field($modelForm, 'user_id')->widget(Select2::classname(), [
            'data' => $userSelectData,
            'language' => 'ru',
            'theme' => Select2::THEME_DEFAULT,
            'options' => ['placeholder' => 'Выберите...', 'class' => 'form-control'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]) ?>
        
        <?= $form->field($modelForm, 'tariff_id')->dropDownList(
            ArrayHelper::map(Tariff::find()->all(), 'id', 'name'),
            ['prompt' => 'Выберите...']
        ) ?>
    </div>
</div>
<div class="row">
    <div class="col-xs-12 text-right">
        <div class="form-group">
            <a href="<?= Yii::$app->urlManager->createUrl(['/flat/index']) ?>" class="btn btn-default margin-bottom-15">Отменить</a>
            <?= Html::input('submit', 'action_save', Yii::t('app', 'Сохранить'), ['class' => 'btn btn-success margin-bottom-15']) ?>
            <?= Html::input('submit', 'action_save_add', Yii::t('app', 'Сохранить и добавить новую'), ['class' => 'btn btn-success margin-bottom-15 bg-green-active']) ?>
            <?php /* if ($model->id) { ?>
                <a class="btn btn-primary margin-bottom-15" href="<?= Yii::$app->urlManager->createUrl(['/invoice/index', 'InvoiceSearch[flat_id]' => $model->id]) ?>">Посмотреть квитанции</a>
            <?php } */ ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
