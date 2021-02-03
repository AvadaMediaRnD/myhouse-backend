<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\TransactionPurpose;
use common\models\AccountTransaction;

/* @var $this yii\web\View */
/* @var $model common\models\TransactionPurpose */
/* @var $form yii\widgets\ActiveForm */
?>

<?php $form = ActiveForm::begin(); ?>
<div class="row">
    <div class="col-xs-12 col-lg-7">
        <?= $form->field($model, 'name')->textInput() ?>
        <?= $form->field($model, 'type')->dropDownList(AccountTransaction::getTypeOptions()) ?>
    </div>
</div>
<div class="row">
    <div class="col-xs-12 text-right">
        <div class="form-group">
            <a href="<?= Yii::$app->urlManager->createUrl(['/transaction-purpose/index']) ?>" class="btn btn-default">Отменить</a>
            <button type="submit" class="btn btn-success">Сохранить</button>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
