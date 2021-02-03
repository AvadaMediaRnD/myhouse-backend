<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use common\models\House;
use yii\helpers\ArrayHelper;
use common\models\Section;
use common\models\Tariff;
use common\models\Invoice;
use common\models\PayCompany;
use common\models\PayCompanyService;
use common\helpers\PriceHelper;
use common\models\Service;
use backend\models\CounterDataSearch;
use yii\widgets\Pjax;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\Invoice */
/* @var $form yii\widgets\ActiveForm */
/* @var $invoiceMonthYear string */

$houseId = $model->flat ? $model->flat->house_id : null;
$sectionId = $model->flat ? $model->flat->section_id : null;
$sectionOptions = ($houseId && $model->flat->house->sections) 
    ? ArrayHelper::map($model->flat->house->sections, 'id', 'name') 
    : [];
$flatOptions = [];
if ($model->flat && $model->flat->section) {
    $flatOptions = ArrayHelper::map($model->flat->section->flats, 'id', 'flat');
} elseif ($model->flat && $model->flat->house) {
    $flatOptions = ArrayHelper::map($model->flat->house->flats, 'id', 'flat');
}
$accountUid = ($model->flat && $model->flat->account) ? $model->flat->account->uid : null;
$tariffOptions = ArrayHelper::map(Tariff::find()->all(), 'id', 'name');
$invoiceServices = $model->invoiceServices;

// counter data grid
$searchModel = new CounterDataSearch();
$searchModel->flat_id = $model->flat_id;
$counterDataProvider = $searchModel->search(Yii::$app->request->queryParams);
?>

<?php $form = ActiveForm::begin(['id' => 'invoice-form']); ?>
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
                
                <?php // echo Html::activeHiddenInput($model, 'period_start') ?>
                <?php // echo Html::activeHiddenInput($model, 'period_end') ?>
            </div>
        </div>
        <div class="col-xs-12 col-md-5 col-lg-6">
            <div class="btn-group pull-right margin-bottom">
                <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Выберите действие <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                    <?php if (!$model->isNewRecord) { ?>
                        <li><a href="<?= Yii::$app->urlManager->createUrl(['/invoice/create', 'invoice_id' => $model->id]) ?>">Копировать</a></li>
                        <li><a href="<?= Yii::$app->urlManager->createUrl(['/invoice/delete', 'id' => $model->id]) ?>" data-method="post" data-confirm="Вы уверены, что хотите удалить этот элемент?">Удалить</a></li>
                    <?php } ?>
                    <li><a href="#!" class="set-tariff-services">Выставить все услуги согласно тарифу</a></li>
                    <li><a href="#!" class="add-counters">Добавить показания счетчиков</a></li>
                    <?php /* ?><li><a href="#!">Добавить показания из прошлых неоплаченных квитанций</a></li><?php */ ?>
                </ul>
            </div>
        </div>
    </div>
    <div class="box">
        <div class="box-body">
                <?= Html::activeHiddenInput($model, 'id') ?>
            
                <div class="row">
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            <label for="house_id">Дом</label>
                            <?= Html::dropDownList(
                                'house_id', 
                                $houseId, 
                                House::getOptions(),
                                [
                                    'id' => 'house_id',
                                    'prompt' => 'Выберите...',
                                    'class' => 'form-control',
                                    'onchange'=>'
                                        $.get("' . urldecode(Yii::$app->urlManager->createUrl(['/flat/get-lists-by-house', 'house_id' => ''])) . '"+$(this).val(), function( data ) {
                                            $("select#section_id").html(data.sections);
                                            $("select#invoice-flat_id").html(data.flats);
                                            console.log(data);
                                        });
                                    ',
                                ]) ?>
                        </div>
                        <div class="form-group">
                            <label for="section_id">Секция</label>
                            <?= Html::dropDownList(
                                'section_id', 
                                $sectionId, 
                                $sectionOptions,
                                [
                                    'id' => 'section_id',
                                    'prompt' => 'Выберите...',
                                    'class' => 'form-control',
                                    'onchange' => '
                                        $.get("' . urldecode(Yii::$app->urlManager->createUrl(['/flat/get-lists-by-house', 'section_id' => ''])) . '"+$(this).val(), function( data ) {
                                            $("select#invoice-flat_id").html(data.flats);
                                            console.log(data);
                                        });
                                    ',
                                ]) ?>
                        </div>
                        <?= $form->field($model, 'flat_id')->dropDownList($flatOptions, [
                            'prompt' => 'Выберите...',
                            'onchange' => '
                                var thisVal = $(this).val();
                                $.get("' . urldecode(Yii::$app->urlManager->createUrl(['/flat/get-flat', 'flat_id' => ''])) . '"+$(this).val(), function( data ) {
                                    $("input#account_uid").val(data.account ? data.account.uid : "");
                                    $("select#invoice-tariff_id").html(data.tariffs);
                                    $("#user-fullname").html(data.user ? data.user.fullnameHtml : "");
                                    $("#user-phone").html(data.user ? data.user.phoneHtml : "");

                                    $("#filterFlatId").val(thisVal).trigger("change");

                                    console.log(data);
                                });
                            ',
                        ]) ?>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <?= $form->field($model, 'is_checked')->checkbox() ?>
                        
                        <?= $form->field($model, 'status')->dropDownList(Invoice::getStatusOptions(), [
                            'prompt' => 'Выберите...',
                        ]) ?>
                        
                        <?php if ($model->isNewRecord) { ?>
                            <?= $form->field($model, 'tariff_id')->dropDownList($tariffOptions, [
                                'prompt' => 'Выберите...',
                                'onchange' => 'tariffId = $(this).val()',
                            ]) ?>
                        <?php } else { ?>
                            <?= Html::activeHiddenInput($model, 'tariff_id') ?>
                            <p><b>Тариф:</b> 
                                <?php if ($model->flat->tariff) { ?>
                                    <a href="<?= Yii::$app->urlManager->createUrl(['/tariff/update', 'id' => $model->flat->tariff_id]) ?>"><?= $model->flat->tariff->name ?></a>
                                <?php } else { ?>
                                    (не указан)
                                <?php } ?>
                            </p>
                        <?php } ?>
                        
                        <?php /*
                        <div class="form-group">
                            <label for="invoiceMonthYear">Месяц</label>
                            <?= DatePicker::widget([
                                'id' => 'invoiceMonthYear',
                                'name' => 'invoiceMonthYear',
                                'value' => $invoiceMonthYear,
                                'type' => DatePicker::TYPE_COMPONENT_PREPEND,
                                'removeButton' => false,
                                'pluginOptions' => [
                                    'autoclose' => true,
                                    'format' => 'mm.yyyy',
                                    'startView' => 'year',
                                    'minViewMode' => 'months',
                                ]
                            ]) ?>
                        </div>
                        */ ?>
                            
                        <div class="row">
                            <div class="col-xs-6">
                                <?= $form->field($model, 'period_start')->widget(DatePicker::className(), [
                                    'type' => DatePicker::TYPE_COMPONENT_PREPEND,
                                    'removeButton' => false,
                                    'pluginOptions' => [
                                        'autoclose' => true,
                                        'format' => 'dd.mm.yyyy'
                                    ]
                                ]) ?>
                            </div>
                            <div class="col-xs-6">
                                <?= $form->field($model, 'period_end')->widget(DatePicker::className(), [
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
                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-6">
                        <div class="form-group">
                            <label for="account_uid">Лицевой счет</label>
                            <?= Html::textInput('account_uid', $accountUid, [
                                'id' => 'account_uid',
                                'class' => 'form-control',
                                'onblur' => '
                                    $.get("' . urldecode(Yii::$app->urlManager->createUrl(['/flat/get-flat', 'account_uid' => ''])) . '"+$(this).val(), function( data ) {
                                        $("select#house_id").html(data.houses);
                                        $("select#section_id").html(data.sections);
                                        $("select#invoice-flat_id").html(data.flats);
                                        $("select#invoice-tariff_id").html(data.tariffs);
                                        $("#user-fullname").html(data.user ? data.user.fullnameHtml : "");
                                        $("#user-phone").html(data.user ? data.user.phoneHtml : "");
                                        console.log(data);
                                    });
                                ',
                            ]) ?>
                        </div>
                           
                        <?php if ($model->flat && $model->flat->user) { ?>
                            <p><b>Владелец:</b> <span id="user-fullname"><a href="<?= Yii::$app->urlManager->createUrl(['/user/view', 'id' => $model->flat->user_id]) ?>"><?= $model->flat->user->fullname ?></a></span></p>
                            <p><b>Телефон:</b> <span id="user-phone"><a href="tel:<?= str_replace(['(', ')', ' ', '-'], '', $model->flat->user->profile->phone) ?>"><?= $model->flat->user->profile->phone ?></a></span></p>
                        <?php } else { ?>
                            <p><b>Владелец:</b> <span id="user-fullname">не выбран</span></p>
                            <p><b>Телефон:</b> <span id="user-phone">не выбран</span></p>
                        <?php } ?>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <?php /* echo $form->field($model, 'status')->dropDownList(Invoice::getStatusOptions(), [
                            'prompt' => 'Выберите...',
                        ]) */ ?>
                    </div>
                    <?php /* ?>
                    <div class="col-xs-12 col-sm-6">
                        <?= $form->field($model, 'pay_company_id')->dropDownList(ArrayHelper::map(PayCompany::find()->all(), 'id', 'name'), [
                            'prompt' => 'Выберите...',
                            'class' => 'form-control pay_company-select',
                        ]) ?>
                    </div>
                    <?php */ ?>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <div class="table-responsive no-padding">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th style="min-width: 200px;">Услуга</th>
                                        <!--<th style="min-width: 180px;">Показания</th>-->
                                        <th style="min-width: 180px;">Расход</th>
                                        <th style="min-width: 120px;">Ед. изм.</th>
                                        <th style="min-width: 180px;">Цена за ед., грн.</th>
                                        <th style="min-width: 180px;">Стоимость, грн.</th>
                                        <th style="width: 40px; min-width: 40px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="form-invoiceservice-rows">

                                    <?php foreach ($invoiceServices as $k => $invoiceService) { ?>
                                        <?= $this->render('_form-invoiceservice', ['model' => $invoiceService, 'formId' => $k, 'tariffModel' => $model->tariff, 'payCompanyModel' => $model->payCompany]) ?>
                                    <?php } ?>

                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4" valing="middle">
                                            <button type="button" class="btn btn-default btn-hover-change form-row-add-invoiceservice-btn">
                                                Добавить услугу
                                            </button>
                                            <button type="button" class="btn btn-default set-tariff-services">
                                                Установить все услуги согласно тарифу
                                            </button>
                                            <button type="button" class="btn btn-default add-counters">
                                                Добавить показания счетчиков
                                            </button>
                                        </td>
                                        <td style="min-width: 180px;">
                                            <div class="h4">
                                                Итого: <b><span id="price-total"><?= PriceHelper::format($model->getPrice()) ?></span></b> грн
                                            </div>
                                        </td>
                                        <td style="width: 40px; min-width: 40px;"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-xs-12 text-right">
                        <div class="form-group">
                            <a href="<?= Yii::$app->urlManager->createUrl(['/invoice/index']) ?>" class="btn btn-default">Отменить</a>
                            <button type="submit" class="btn btn-success">Сохранить</button>
                        </div>
                    </div>
                </div>
            
        </div>
    </div>
<?php ActiveForm::end(); ?>

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Показания счетчиков</h3>
            </div>

            <?php Pjax::begin(['id' => 'counters']) ?>
            <?php echo GridView::widget([
                'dataProvider' => $counterDataProvider,
                'filterModel' => $searchModel,
                'tableOptions'=>['class'=>'table table-bordered table-hover table-striped table-nowrap'],
                'layout'=> "<div class=\"box-body table-responsive no-padding\">{items}</div>\n<div class=\"box-footer clearfix\">{pager}</div>",
                'pager' => [
                    'class' => 'yii\widgets\LinkPager',
                    'options' => ['class' => 'pagination pagination-sm no-margin pull-right'],
                ],
                'filterRowOptions' => ['class' => 'filter-hidden'],
                'columns' => [
                    [
                        'attribute' => 'uid',
                        'label' => '№',
                        'filter' => false,
                        'enableSorting' => false,
                        'headerOptions' => ['style' => 'width: 125px; min-width: 125px'],
                    ],
                    [
                        'attribute' => 'status',
                        'format' => 'raw',
                        'filter' => false,
                        'value' => function ($model) {
                            return $model->getStatusLabelHtml();
                        },
                        'enableSorting' => false,
                    ],
                    [
                        'attribute' => 'searchUidDate',
                        'label' => 'Дата',
                        'filter' => false,
                        'enableSorting' => false,
                        'value' => function ($model) {
                            return date(Yii::$app->params['dateFormat'], strtotime($model->uid_date));
                        },
                        'headerOptions' => ['style' => 'width: 125px; min-width: 125px'],
                    ],
                    [
                        'attribute' => 'searchUidMonthYear',
                        'label' => 'Месяц',
                        'filter' => Html::hiddenInput('CounterDataSearch[searchUidMonthYear]', $searchModel->searchUidMonthYear, ['id' => 'filterMonthYear'])
                            . Html::hiddenInput('CounterDataSearch[flat_id]', $searchModel->flat_id, ['id' => 'filterFlatId']),
                        'enableSorting' => false,
                        'value' => function ($model) {
                            return $model->getMonthYearPrint();
                        },
                        'headerOptions' => ['style' => 'width: 125px; min-width: 125px'],
                    ],
                    [
                        'attribute' => 'searchHouse',
                        'label' => 'Дом',
                        'filter' => false,
                        'value' => function ($model) {
                            return $model->flat->house->name;
                        },
                        'headerOptions' => ['style' => 'min-width: 200px'],
                    ],
                    [
                        'attribute' => 'searchSection',
                        'label' => 'Секция',
                        'filter' => false,
                        'value' => function ($model) {
                            return $model->flat->section->name;
                        },
                        'headerOptions' => ['style' => 'min-width: 160px'],
                    ],
                    [
                        'attribute' => 'searchFlat',
                        'label' => '№ квартиры',
                        'filter' => false,
                        'value' => function ($model) {
                            return $model->flat->flat;
                        },
                        'headerOptions' => ['style' => 'width: 110px; min-width: 110px'],
                    ],
                    [
                        'attribute' => 'service_id',
                        'label' => 'Счетчик',
                        'filter' => false,
                        'enableSorting' => false,
                        'value' => function ($model) {
                            return $model->service->name;
                        },
                    ],
                    [
                        'attribute' => 'amount_total',
                        'label' => 'Показания',
                        'filter' => false,
                        'enableSorting' => false,
                        'headerOptions' => ['style' => 'width: 90px; min-width: 90px'],
                        'contentOptions' => ['style' => 'background-color: #DFD5; font-weight: normal'],
                    ],
                    [
                        'attribute' => 'searchServiceUnit',
                        'label' => 'Ед. изм.',
                        'value' => function ($model) {
                            return $model->service->serviceUnit->name;
                        },
                        'filter' => false,
                        'enableSorting' => false,
                        'headerOptions' => ['style' => 'width: 90px; min-width: 90px'],
                        'contentOptions' => ['style' => 'background-color: #DFD5; font-weight: normal'],
                    ],
                ],
            ]); ?>
            <?php Pjax::end() ?>
        </div>
    </div>
</div>

<?php
$formIdInvoiceServiceNext = $invoiceServices ? count($invoiceServices) : 0;
$urlGetFormInvoiceService = Yii::$app->urlManager->createUrl(['/invoice/ajax-get-form-invoice-service', 'invoice_id' => $model->id, 'form_id' => '']);
$urlGetService = urldecode(Yii::$app->urlManager->createUrl(['/invoice/ajax-get-service', 'service_id' => '']));
$urlGetServiceOptions = Yii::$app->urlManager->createUrl(['/invoice/ajax-get-service-options', 'pay_company_id' => '']);
$urlGetCounterDataOptions = Yii::$app->urlManager->createUrl(['/invoice/ajax-get-counter-data-options', 'service_id' => '']);
$urlGetAmount = Yii::$app->urlManager->createUrl(['/counter-data/ajax-get-amount', 'counter_data_id' => '']);
$urlSetTariffServices = Yii::$app->urlManager->createUrl(['/invoice/ajax-get-forms-by-tariff', 'invoice_id' => $model->id, 'form_id' => '']);
$urlAddCounters = Yii::$app->urlManager->createUrl(['/invoice/ajax-get-forms-by-counters', 'invoice_id' => $model->id, 'form_id' => '']);
$this->registerJs("
    var formIdInvoiceServiceNext = {$formIdInvoiceServiceNext};
        
    $('form#invoice-form').on('keyup keypress', function(e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) { 
            e.preventDefault();
            return false;
        }
    });

    $(document).on('click', '.form-row-remove-btn', function(e){
        if (confirm('Удалить?')) { 
            $(this).parents('.form-invoiceservice').remove(); 
            
            // update total price
            updateTotalPrice();
        }
    });
    
    $(document).on('click', '.form-row-add-invoiceservice-btn', function(e){
        var tariffId = $('#invoice-tariff_id').val();
        var payCompanyId = $('#invoice-pay_company_id').val();
        $.ajax({
            url: '{$urlGetFormInvoiceService}'+formIdInvoiceServiceNext+'&tariff_id='+tariffId+'&pay_company_id='+payCompanyId,
            dataType: 'json',
            success: function(json) {
                console.log(json);
                $('#form-invoiceservice-rows').append(json);
                formIdInvoiceServiceNext++;
            }
        });
    });
    
    $(document).on('change', '.service-select', function(e) {
        var flatId = $('#invoice-flat_id').val();
        var tariffId = $('#invoice-tariff_id').val();
        var parentRow = $(this).parents('.form-invoiceservice');
        $.get('{$urlGetService}'+$(this).val()+'&tariff_id='+tariffId+'&flat_id='+flatId, function( data ) {
            parentRow.find('select.data-unit').html(data.serviceUnits);
            parentRow.find('input.data-price_unit').val(data.tariffService ? data.tariffService.price_unit : '');
            parentRow.find('input.data-price').val('');
            
            if (data.amount > 0) {
                parentRow.find('input.data-amount').val(data.amount);
            } else {
                parentRow.find('input.data-amount').val('');
            }
        });
        
        // 
        var flatId = $('#invoice-flat_id').val();
        var row = $(this).parents('.form-invoiceservice');
        $.get('{$urlGetCounterDataOptions}'+$(this).val()+'&flat_id='+flatId, function( data ) {
            console.log(data);
            row.find('select.data-counter_data_id').html(data.counterData);
        });
    });
    
    $(document).on('blur', '.data-amount, .data-price_unit', function(e) {
        var parentRow = $(this).parents('.form-invoiceservice');
        var priceInput = parentRow.find('input.data-price');
        var priceUnitInput = parentRow.find('input.data-price_unit');
        var amountInput = parentRow.find('input.data-amount');
        
        
        var amount = parseFloat(amountInput.val());
        var priceUnit = parseFloat(priceUnitInput.val());
        var price = (amount * priceUnit).toFixed(2);
        
        console.log(amount + ' * ' + priceUnit + ' = ' + price);
        
        if (isNaN(price)) {
            price = 0;
        }
        
        priceInput.val(price).trigger('change');
    });
    
//    $(document).on('change', '.pay_company-select', function(e) {
//        $.get('{$urlGetServiceOptions}'+$(this).val(), function( data ) {
//            console.log(data);
//            // $('#form-invoiceservice-rows').find('select.service-select').html(data.servicesData);
//            $.each($('#form-invoiceservice-rows').find('select.service-select'), function(i, v) {
//                var valTmp = $(v).val();
//                $(v).html(data.servicesData).val(valTmp);
//            });
//        });
//    });

    $(document).on('change', '.data-counter_data_id', function(e){
        var counterDataId = $(this).val();
        var row = $(this).parents('.form-invoiceservice');
        $.get('{$urlGetAmount}'+$(this).val(), function( data ) {
            row.find('.data-amount').val(data.amount).trigger('blur');
        });
    });
    

    // Set tariff services
    $('body').on('click', '.set-tariff-services', function() {
        var tariffId = $('#invoice-tariff_id').val();
        var payCompanyId = $('#invoice-pay_company_id').val();
        $.ajax({
            url: '{$urlSetTariffServices}'+formIdInvoiceServiceNext+'&tariff_id='+tariffId+'&pay_company_id='+payCompanyId,
            dataType: 'json',
            success: function(json) {
//                $.each(json.data, function(i, v) {
//                    $.each($('select.service-select'), function(ii, vv) {
//                        if ($(vv).val() == v.service_id) {
//                            $(vv).parents('.form-invoiceservice').find('input.data-price_unit').val(v.price_unit).blur();
//                        }
//                    });
//                });

                $('#form-invoiceservice-rows').html(json.html);
                formIdInvoiceServiceNext = $('#form-invoiceservice-rows').children().length;
                
                // update total price
                updateTotalPrice();
            }
        });
    });
    
    // Add counters
    $('body').on('click', '.add-counters', function() {
        var tariffId = $('#invoice-tariff_id').val();
        var flatId = $('#invoice-flat_id').val();
        var payCompanyId = $('#invoice-pay_company_id').val();
        var dateMY = $('#invoice-period_end').val().substr(3);
        $.ajax({
            url: '{$urlAddCounters}'+formIdInvoiceServiceNext+'&tariff_id='+tariffId+'&flat_id='+flatId+'&pay_company_id='+payCompanyId+'&date_my='+dateMY,
            dataType: 'json',
            success: function(json) {
                $.each(json.data, function(i, v) {
                    $.each($('select.service-select'), function(ii, vv) {
                        if ($(vv).val() == v.service_id) {
                            $(vv).parents('.form-invoiceservice').find('.data-counter_data_id').val(v.counter_data_id).blur();
                            $(vv).parents('.form-invoiceservice').find('input.data-amount').val(v.amount).blur();
                        }
                    });
                });
                
//                $('#form-invoiceservice-rows').html(json.html);
                
                formIdInvoiceServiceNext = $('#form-invoiceservice-rows').children().length;
                
                // update total price
                updateTotalPrice();
            }
        });
    });
    
    // filter counter grid for month
    $('#invoiceMonthYear').on('change', function() {
        $('#filterMonthYear').val($(this).val()).trigger('change');
    }).trigger('change');
    
    $('#invoice-period_end').on('change', function() {
        $('#filterMonthYear').val($(this).val().substr(3)).trigger('change');
    });
    
    // Count total price
    $('body').on('change', '.data-price', updateTotalPrice);
    
    // update value of total price
    function updateTotalPrice() {
        var priceTotal = 0;
        $('.data-price').each(function(index) {
            priceTotal += parseFloat($(this).val()) || 0;
            console.log(priceTotal);
        });
        $('#price-total').text(priceTotal.toFixed(2));
    }

");
