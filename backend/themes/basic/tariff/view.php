<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\helpers\PriceHelper;

/* @var $this yii\web\View */
/* @var $model common\models\Tariff */

$this->title = 'Тариф' . ($model->name ? ': ' . $model->name : '');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Тарифы'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title"></h3>
        <div class="box-tools">
            <a href="<?= Yii::$app->urlManager->createUrl(['/tariff/update', 'id' => $model->id]) ?>" class="btn btn-primary btn-sm">
                <span class="hidden-xs">Редактировать тариф</span><i class="fa fa-pencil visible-xs" aria-hidden="true"></i>
            </a>
        </div>
    </div>
    <div class="box-body">
        <table class="table table-bordered table-striped table-view">
            <tbody>
                <tr>
                    <td>Название тарифа</td>
                    <td>
                        <?= $model->name ?> 
                    </td>
                </tr>
                <tr>
                    <td>Описание</td>
                    <td><?= $model->description ?></td>
                </tr>
                <tr>
                    <td>Дата редактирования</td>
                    <td><?= $model->updated ?></td>
                </tr>
            </tbody>
        </table>
        <div class="table-responsive no-padding margin-top-15">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Услуга</th>
                        <th>Ед. изм.</th>
                        <th>Цена за ед., грн</th>
                        <th>Валюта</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($model->tariffServices as $k => $tariffService) { ?>
                        <tr role="row">
                            <td><?= $k + 1 ?></td>
                            <td><?= $tariffService->service->name ?></td>
                            <td><?= $tariffService->service->serviceUnit->name ?></td>
                            <td><?= PriceHelper::format($tariffService->price_unit) ?></td>
                            <td>грн</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
