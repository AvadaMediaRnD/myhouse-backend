<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\Invoice;
use common\helpers\PriceHelper;
use common\models\TariffService;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\Invoice */
/* @var $templateModels common\models\InvoiceTemplate[] */

$this->title = Yii::t('app', 'Печатная форма документа');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Квитанции'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Квитанция #' . $model->uid, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">Список шаблонов</h3>
        <div class="box-tools">
            <a href="<?= Yii::$app->urlManager->createUrl(['/invoice/template']) ?>" class="btn btn-default btn-sm">
                <span class="hidden-xs">Настройка шаблонов</span><i class="fa fa-file-text visible-xs" aria-hidden="true"></i>
            </a>
        </div>
    </div>
    <div class="box-body">
        <?php $form = ActiveForm::begin() ?>
            <div class="row">
                <div class="col-xs-12 col-sm-7 col-lg-4">
                    <?php if ($templateModels) { ?>
                        <?php foreach ($templateModels as $template) { ?>
                            <div class="form-group">
                                <div class="radio">
                                    <label for="invoice_template_id-<?= $template->id ?>">
                                        <?= Html::radio('invoice_template_id', $template->is_default, ['value' => $template->id, 'id' => 'invoice_template_id-' . $template->id]) ?>
                                        <?= $template->title ?> <?php if ($template->is_default) { ?>(по-умолчанию)<?php } ?>
                                    </label>
                                </div>
                            </div>
                        <?php } ?>
                    <?php } else { ?>
                        <p>Нет загруженных шаблонов</p>
                    <?php } ?>
                        
                    <?= Html::hiddenInput('invoice_template_type', 'xls') ?>
                </div>
                <?php /* ?>
                <div class="col-xs-12 col-sm-5 col-lg-2">
                    <div class="form-group">
                        <div class="radio">
                            <label for="invoice_template_type-xls">
                                <?= Html::radio('invoice_template_type', true, ['value' => 'xls', 'id' => 'invoice_template_type-xls']) ?>
                                <i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="radio">
                            <label for="invoice_template_type-pdf">
                                <?= Html::radio('invoice_template_type', false, ['value' => 'pdf', 'id' => 'invoice_template_type-pdf']) ?>
                                <i class="fa fa-file-pdf-o" aria-hidden="true"></i> PDF
                            </label>
                        </div>
                    </div>
                </div>
                <?php */ ?>
            </div>
            <div class="row">
                <div class="col-xs-12 text-right">
                    <div class="form-group">
                        <button name="action_download" type="submit" class="btn btn-success">Скачать</button>
                        <button name="action_send_email" type="submit" class="btn btn-default">Отправить на e-mail</button>
                    </div>
                </div>
            </div>
        <?php ActiveForm::end() ?>
    </div>
</div>
