<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use common\models\Flat;
use common\models\Tariff;
use common\helpers\PriceHelper;

/* @var $this yii\web\View */
/* @var $flat common\models\Flat */
/* @var $model common\models\Tariff */

$this->title = Yii::t('app', 'Тарифы') . ' - ' . $flat->house->name . ', кв.' . $flat->flat;
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="box">
    <div class="box-body">
        <div class="table-responsive no-padding">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th style="min-width:250px">Услуга</th>
                        <th style="min-width:150px">Ед. изм.</th>
                        <th style="min-width:150px">Цена за ед., грн</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($model) { ?>
                        <?php foreach ($model->tariffServices as $tariffService) { ?>
                            <tr>
                                <td><?= $tariffService->service ? $tariffService->service->name : 'не указано' ?></td>
                                <td><?= $tariffService->service ? $tariffService->service->serviceUnit->name : 'не указано' ?></td>
                                <td><?= PriceHelper::format($tariffService->price_unit) ?></td>
                            </tr>
                        <?php } ?>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
