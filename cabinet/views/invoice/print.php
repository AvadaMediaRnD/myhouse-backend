<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\Invoice;
use common\helpers\PriceHelper;
use common\models\TariffService;

/* @var $this yii\web\View */
/* @var $model common\models\Invoice */

$this->title = Yii::t('app', 'Печать квитанции:') . ' #' . $model->uid;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Квитанции'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
    html {
        font-family: Arial;
        font-size: 1.3em;
    }
    h4 {
        margin: 0.5em 0;
    }
    table {
        font-size: 1.3em;
    }
    .pagebreak { page-break-before: always; }
    @page { margin: 0; }
    @media print {
        @page { margin: 0; }
        body { margin: 1.6cm; }
    }
</style>

<div class="box pagebreak">
    <h4>Квитанция: #<?= $model->uid ?></h4>
    <h4>Дата: <?= $model->uid_date ?></h4>
    <h4>Квартира: <?= $model->flat->flat ?>, <?= $model->flat->house->address ?></h4>
    <h4>Получатель: <?= $model->flat->user ? $model->flat->user->getFullname() : '' ?></h4>
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

<script>window.print();</script>
