<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\Invoice;
use common\helpers\PriceHelper;
use common\models\TariffService;

/* @var $this yii\web\View */
/* @var $model common\models\Invoice */

$this->title = Yii::t('app', 'Просмотр квитанции:') . ' #' . $model->uid;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Квитанции'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$badgeClass = '';
if ($model->status == Invoice::STATUS_PAID) {
    $badgeClass = 'bg-green';
} elseif ($model->status == Invoice::STATUS_UNPAID) {
    $badgeClass = 'bg-orange';
}
?>

<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title"></h3>
        <div class="box-tools">
            <a href="<?= Yii::$app->urlManager->createUrl(['/invoice/print', 'id' => $model->id]) ?>" class="btn btn-info btn-sm">
                <i class="fa fa-print" aria-hidden="true"></i> <span class="hidden-xs">Распечатать</span>
            </a>
        </div>
    </div>
    <div class="box-body">
        <div class="table-responsive no-padding margin-top-15">
            <table class="table table-bordered table-hover table-striped">
                <thead>
                    <tr>
                        <th style="width: 40px; min-width: 40px;">#</th>
                        <th>Услуга</th>
                        <th>Количество потребления (расход)</th>
                        <th style="width: 80px; min-width: 80px;">Ед. изм.</th>
                        <th>Цена за ед., грн</th>
                        <th>Стоимость, грн</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <td colspan="5"></td>
                        <td colspan="2"><b>Итого: <?= PriceHelper::format($model->getPrice()) ?></b></td>
                    </tr>
                </tfoot>
                <tbody>
                    <?php foreach ($model->invoiceServices as $k => $invoiceService) { ?>
                        <?php
                        $tariffService = TariffService::find()
                            ->where(['service_id' => $invoiceService->service_id, 'tariff_id' => $model->tariff_id])
                            ->one();
                        ?>
                        <tr>
                            <td><?= $k + 1 ?></td>
                            <td><?= $invoiceService->service->name ?></td>
                            <td><?= number_format($invoiceService->amount, 2) ?></td>
                            <td><?= $invoiceService->service->serviceUnit->name ?></td>
                            <td><?= PriceHelper::format($tariffService->price_unit) ?></td>
                            <td><?= PriceHelper::format($invoiceService->price) ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="box-footer">
        <div class="pull-right">
            <a href="<?= Yii::$app->urlManager->createUrl(['/invoice/download', 'id' => $model->id]) ?>" class="btn btn-sm btn-warning" download><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Скачать в PDF</a>
            <a href="<?= Yii::$app->urlManager->createUrl(['/invoice/pay', 'id' => $model->id]) ?>" class="btn btn-sm btn-success"><i class="fa fa-credit-card" aria-hidden="true"></i> Оплатить</a>
        </div>
    </div>
</div>
