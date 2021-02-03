<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\Flat;
use common\models\House;
use common\models\MasterRequest;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $model common\models\MasterRequest */
/* @var $modelForm cabinet\models\MasterRequestForm */
/* @var $form yii\widgets\ActiveForm */
?>

<?php $form = ActiveForm::begin(); ?>
    <div class="modal-body">
        <?= $form->field($modelForm, 'type')->dropDownList(MasterRequest::getTypeOptions(), ['prompt' => 'Выберите...']) ?>
        <?= $form->field($modelForm, 'flat_id')->dropDownList(
            ArrayHelper::map(Yii::$app->user->identity->flats, 'id', function($model) {
                return ($model->house ? ($model->house->name . ', ') : '') . 'кв.' . $model->flat;
            }), 
            ['prompt' => 'Выберите...']
        ) ?>
        <div class="row">
            <div class="col-sm-6">
                <?= $form->field($modelForm, 'date_request')->widget(DatePicker::className(), [
                    'type' => DatePicker::TYPE_COMPONENT_APPEND,
                    'removeButton' => false,
                    'pluginOptions' => [
                        'autoclose' => true,
                        'format' => 'dd.mm.yyyy'
                    ]
                ]) ?>
            </div>
            <div class="col-sm-6">
                <?= $form->field($modelForm, 'time_request', [
                    'template' => "{label}\n
                        <div class=\"input-group bootstrap-timepicker\">{input}\n
                            <div class=\"input-group-addon\">
                            <i class=\"fa fa-clock-o\"></i>
                        </div>
                        </div>\n{hint}\n{error}"
                ])->textInput() ?>
            </div>
        </div>
        <?= $form->field($modelForm, 'description')->textarea(['rows' => 5, 'placeholder' => 'Опишите проблему']) ?>
    </div>
    <div class="modal-footer">
        <?= Html::submitButton(Yii::t('app', 'Отправить заявку'), ['class' => 'btn btn-success']) ?>
    </div>
<?php ActiveForm::end(); ?>

<?php 
$this->registerCssFile(Yii::$app->urlManager->createUrl('plugins/timepicker/bootstrap-timepicker.min.css'));
$this->registerJsFile(Yii::$app->urlManager->createUrl('plugins/timepicker/bootstrap-timepicker.min.js'), ['depends' => yii\web\YiiAsset::class]);
$this->registerJs(<<<JS
    $('#masterrequestform-time_request').timepicker({
        showInputs: false,
        showMeridian: false
    });
JS
);
