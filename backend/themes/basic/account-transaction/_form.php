<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use yii\helpers\ArrayHelper;
use common\models\Invoice;
use common\models\UserAdmin;
use common\models\User;
use common\models\Account;
use common\models\AccountTransaction;
use common\models\TransactionPurpose;
use common\models\Flat;
use kartik\select2\Select2;
use common\helpers\PriceHelper;

/* @var $this yii\web\View */
/* @var $model common\models\AccountTransaction */
/* @var $form yii\widgets\ActiveForm */

$houseIds = Yii::$app->user->identity->getHouseIds();

$userId = ($model->account && $model->account->flat) ? $model->account->flat->user_id : null;

$flatIds = ($userId && $model->account->flat->user) 
    ? ArrayHelper::getColumn($model->account->flat->user->flats, 'id') 
    : ArrayHelper::getColumn(Flat::find()->where(['in', 'house_id', $houseIds])->all(), 'id'); 
$accountOptions = $flatIds
    ? ArrayHelper::map(Account::find()->where(['in', 'flat_id', $flatIds])->all(), 'id', 'uid') 
    : ArrayHelper::map(Account::find()->joinWith('flat')->andWhere(['in', 'flat.house_id', $houseIds])->all(), 'id', 'uid');

$invoiceOptions = $flatIds
    ? ArrayHelper::map(Invoice::find()->where(['in', 'flat_id', $flatIds])->andWhere(['in', 'status', [Invoice::STATUS_UNPAID, Invoice::STATUS_PLANNED]])->all(), 'id', function ($model) {
        return $model->uid . ' от ' . $model->getUidDate();
    }) 
    : [];
    
$invoiceServiceOptions = $model->invoice ? $model->invoice->getInvoiceServiceOptions() : [];

$userAdminOptions = UserAdmin::getUserTransactionOptions();

$userQuery = User::find();
if (Yii::$app->user->identity->role != UserAdmin::ROLE_ADMIN) {
    $userQuery->joinWith('flats')
        ->andWhere(['in', 'flat.house_id', Yii::$app->user->identity->getHouseIds()]);
}
$userOptions = ArrayHelper::map($userQuery->all(), 'id', 'fullname');
?>

<?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-xs-12 col-md-7 col-lg-6">
            <div class="page-header-spec">
                <?= $form->field($model, 'uid', [
                    'template' => '<div class="input-group">
                            <div class="input-group-addon">
                                №
                            </div>{input}
                        </div>',
                    'enableAjaxValidation' => true,
                    'enableClientValidation' => true,
                ])->textInput() ?>
                <span class="label-mid">от</span>
                <?= $form->field($model, 'uid_date', ['template' => '{input}'])->widget(DatePicker::className(), [
                    'type' => DatePicker::TYPE_COMPONENT_PREPEND,
                    'removeButton' => false,
                    'pluginOptions' => [
                        'autoclose' => true,
                        'format' => 'dd.mm.yyyy'
                    ]
                ]) ?>
            </div>
        </div>
    </div>
    <div class="box">
        <?php if (!$model->isNewRecord) { ?>
            <div class="box-header with-border">
                <h3 class="box-title"></h3>
                <div class="box-tools">
                    <a href="<?= Yii::$app->urlManager->createUrl(['/account-transaction/create', 'account_transaction_id' => $model->id]) ?>" class="btn btn-default btn-sm">
                        <span class="hidden-xs">Копировать</span><i class="fa fa-print visible-xs" aria-hidden="true"></i>
                    </a>
                    <a href="<?= Yii::$app->urlManager->createUrl(['/account-transaction/delete', 'id' => $model->id]) ?>" class="btn btn-default btn-sm" data-method="post" data-confirm="Вы уверены, что хотите удалить этот элемент?">
                        <span class="hidden-xs">Удалить</span><i class="fa fa-envelope-o visible-xs" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        <?php } ?>
        <div class="box-body">
            <?= Html::activeHiddenInput($model, 'id') ?>
            <?= Html::activeHiddenInput($model, 'type') ?>
            <?= Html::activeHiddenInput($model, 'currency_id') ?>
            
            <div class="row">
                <div class="col-xs-12 col-sm-6 col-sm-offset-6">
                    <?= Html::checkbox(
                        'is_complete', 
                        $model->status == AccountTransaction::STATUS_COMPLETE,
                        [
                            'id' => 'is_complete',
                            'onchange' => 'if (this.checked) { 
                                $("#accounttransaction-status").val("'.AccountTransaction::STATUS_COMPLETE.'"); 
                            } else { 
                                $("#accounttransaction-status").val("'.AccountTransaction::STATUS_WAITING.'"); 
                            }'
                        ]
                    ) ?> <label for="is_complete">Проведен</label>
                </div>
            </div>
            
            <div class="row">
                <div class="col-xs-12 col-sm-6">
                    <?php if ($model->type == AccountTransaction::TYPE_IN) { ?>
                        
                        <div class="form-group">
                            <label for="user_id">Владелец квартиры</label>
                            <?php /* echo Html::dropDownList(
                                    'user_id', 
                                    $userId, 
                                    $userOptions,
                                    [
                                        'id' => 'user_id',
                                        'prompt' => 'Выберите...',
                                        'class' => 'form-control',
                                        'onchange'=>'
                                            $.get("' . urldecode(Yii::$app->urlManager->createUrl(['/account/get-lists-by-user', 'user_id' => ''])) . '"+$(this).val(), function( data ) {
                                                $("select#accounttransaction-account_id").html(data.accounts);
                                                $("select#accounttransaction-invoice_id").html(data.invoices);
                                                console.log(data);
                                            });
                                        ',
                                    ]) */ ?>

                            <?php echo Select2::widget([
                                'name' => 'user_id',
                                'value' => $userId,
                                'data' => $userOptions,
                                'language' => 'ru',
                                'theme' => Select2::THEME_DEFAULT,
                                'options' => [
                                    'placeholder' => 'Выберите...', 
                                    'class' => 'form-control',
                                    'onchange'=>'
                                            $.get("' . urldecode(Yii::$app->urlManager->createUrl(['/account/get-lists-by-user', 'user_id' => ''])) . '"+$(this).val(), function( data ) {
                                                $("select#accounttransaction-account_id").html(data.accounts);
                                                $("select#accounttransaction-invoice_id").html(data.invoices);
                                                console.log(data);
                                            });
                                    ',
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true
                                ],
                            ]) ?>
                        </div>
                        <?php /* echo $form->field($model, 'account_id')->dropDownList($accountOptions, [
                            'prompt' => 'Выберите...',
                            'onchange'=>'
                                $.get("' . urldecode(Yii::$app->urlManager->createUrl(['/account/get-lists-by-account', 'account_id' => ''])) . '"+$(this).val(), function( data ) {
                                    $("select#accounttransaction-invoice_id").html(data.invoices);
                                    console.log(data);
                                });
                            ',
                        ]) */ ?>
                        <?php echo $form->field($model, 'account_id')->widget(Select2::className(), [
                            'data' => $accountOptions,
                            'language' => 'ru',
                            'theme' => Select2::THEME_DEFAULT,
                            'options' => [
                                'placeholder' => 'Выберите...', 
                                'class' => 'form-control',
                                'onchange'=>'
                                    $.get("' . urldecode(Yii::$app->urlManager->createUrl(['/account/get-lists-by-account', 'account_id' => ''])) . '"+$(this).val(), function( data ) {
                                        $("select#accounttransaction-invoice_id").html(data.invoices);
                                        console.log(data);
                                    });
                                '
                            ],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ]) ?>
                    
                    <?php } ?>
                    
                    <?= $form->field($model, 'transaction_purpose_id')->dropDownList(TransactionPurpose::getOptions($model->type), [
                        'prompt' => 'Выберите...',
                    ])->label('Статья') ?>
                    <?= $form->field($model, 'amount')->textInput(['style' => 'font-size: 24px;', 'type' => 'number', 'step' => 'any']) ?>
                </div>
                <div class="col-xs-12 col-sm-6">
                    <?= Html::activeHiddenInput($model, 'status') ?>
                    
                    <?= Html::activeHiddenInput($model, 'invoice_id') ?>
                    
                    <?php /* echo $form->field($model, 'invoice_id')->dropDownList($invoiceOptions, [
                        'prompt' => 'Выберите...',
                        'onchange'=>'
                            // $("input#accounttransaction-amount").val($(this).find("option:selected").attr("data-price"));

                            $.get("' . urldecode(Yii::$app->urlManager->createUrl(['/invoice/ajax-get-invoice-price', 'invoice_id' => ''])) . '"+$(this).val(), function( data ) {
                                $("input#accounttransaction-amount").val(data.price);
                                console.log(data);
                            });

                            $.get("' . urldecode(Yii::$app->urlManager->createUrl(['/invoice/ajax-get-invoice-service-options', 'invoice_id' => ''])) . '"+$(this).val(), function( data ) {
                                $("select#accounttransaction-invoice_service_id").html(data.invoiceServicesData);
                                console.log(data);
                            });
                        ',
                    ]) */ ?>
                    <?php /* echo $form->field($model, 'invoice_id')->widget(Select2::className(), [
                        'data' => $invoiceOptions,
                        'language' => 'ru',
                        'theme' => Select2::THEME_DEFAULT,
                        'options' => [
                            'placeholder' => 'Выберите...', 
                            'class' => 'form-control',
                            'onchange'=>'
                                // $("input#accounttransaction-amount").val($(this).find("option:selected").attr("data-price"));

                                $.get("' . urldecode(Yii::$app->urlManager->createUrl(['/invoice/ajax-get-invoice-price', 'invoice_id' => ''])) . '"+$(this).val(), function( data ) {
                                    $("input#accounttransaction-amount").val(data.price);
                                    console.log(data);
                                });

                                $.get("' . urldecode(Yii::$app->urlManager->createUrl(['/invoice/ajax-get-invoice-service-options', 'invoice_id' => ''])) . '"+$(this).val(), function( data ) {
                                    $("select#accounttransaction-invoice_service_id").html(data.invoiceServicesData);
                                    console.log(data);
                                });
                            '
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]) */ ?>
                    
                    <?php /* echo $form->field($model, 'invoice_service_id')->dropDownList($invoiceServiceOptions, [
                        'prompt' => 'Выберите...',
                        'onchange'=>'
                            $.get("' . urldecode(Yii::$app->urlManager->createUrl(['/invoice/ajax-get-invoice-service-price', 'invoice_service_id' => ''])) . '"+$(this).val(), function( data ) {
                                $("input#accounttransaction-amount").val(data.price);
                                console.log(data);
                            });
                        ',
                    ]) */ ?>
                    <?= $form->field($model, 'user_admin_id')->dropDownList($userAdminOptions, [
                        'prompt' => 'Выберите...',
                    ]) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 text-right">
                    <div class="form-group">
                        <a href="<?= Yii::$app->urlManager->createUrl(['/account-transaction/index']) ?>" class="btn btn-default margin-bottom-15">Отменить</a>
                        <?= Html::input('submit', 'action_save', Yii::t('app', 'Сохранить'), ['class' => 'btn btn-success margin-bottom-15']) ?>
                        <?php // echo Html::input('submit', 'action_save_add', Yii::t('app', 'Сохранить и добавить новые показания'), ['class' => 'btn btn-success margin-bottom-15 bg-green-active']) ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <?= $form->field($model, 'description')->textarea(['rows' => 5]) ?>
                </div>
            </div>
        </div>
    </div>
<?php ActiveForm::end(); ?>
