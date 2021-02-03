<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\Invoice;

/* @var $this yii\web\View */
/* @var $model common\models\Invoice */

$this->title = Yii::t('app', 'Просмотр квитанции:') . ' №' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Квитанции'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$badgeClass = '';
if ($model->status == Invoice::STATUS_PAID) {
    $badgeClass = 'bg-green';
} elseif ($model->status == Invoice::STATUS_UNPAID) {
    $badgeClass = 'bg-orange';
}
?>
<div class="invoice-view">

    <p>Квартира: <span><?= '№' . $model->flat->flat . ', ' . $model->flat->house->name . ', адрес: ' . $model->flat->house->address ?></span></p>
    <p>Владелец: <span><?= $model->flat->user ? ($model->flat->user->getFullname() . ', тел.:' . $model->flat->user->username) : 'Квартира не привязана к пользователю' ?></span></p>
    <p>Дата поступления: <span><?= $model->created ?></span></p>
    <p>Статус оплаты: <span class="badge <?= $badgeClass ?>"><?= $model->getStatusLabel() ?></span></p>

    <table id="example2" class="table table-bordered table-hover table-striped dataTable" role="grid">
        <thead>
        <tr role="row">
            <th rowspan="1" colspan="1"></th>
            <th rowspan="1" colspan="1">Услуга</th>
            <th rowspan="1" colspan="1">Расход</th>
            <th rowspan="1" colspan="1">Ед.изм.</th>
            <th rowspan="1" colspan="1">Цена за ед. (грн)</th>
            <th rowspan="1" colspan="1">Стоимость (грн)</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($model->invoiceServices as $k => $service) { ?>
            <tr role="row">
                <td><?= $k + 1 ?></td>
                <td><?= $service->name ?></td>
                <td><?= number_format($service->amount, 2) ?></td>
                <td><?= $service->unit ?></td>
                <td><?= number_format($service->price_unit, 2) ?></td>
                <td><?= number_format($service->price, 2) ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

    <p class="lead text-right"><span>Итого</span>: <span><?= number_format($model->price, 2) ?> грн</span></p>

    <?php foreach ($model->invoiceServices as $service) { ?>

    <?php } ?>

</div>
