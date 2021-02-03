<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\Invoice;
use common\helpers\PriceHelper;
use common\models\TariffService;

/* @var $this yii\web\View */
/* @var $model common\models\Invoice */

$this->title = Yii::t('app', 'Квитанция');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Квитанции'), 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Квитанция №' . $model->uid;

?>

<div class="row">
    <div class="col-xs-12 col-md-7 col-lg-6">
        <div class="page-header-spec">
            <div class="form-group">
                <div class="input-group date">
                    <div class="input-group-addon">
                        №
                    </div>
                    <div class="form-control pull-right"><?= $model->uid ?></div>
                </div>
            </div>
            <span class="label-mid">от</span>
            <div class="form-group">
                <div class="input-group date">
                    <div class="input-group-addon">
                        <i class="glyphicon glyphicon-calendar"></i>
                    </div>
                    <div class="form-control pull-right"><?= $model->getUidDate() ?></div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">Просмотр квитанции</h3>
        <div class="box-tools">
            <a href="<?= Yii::$app->urlManager->createUrl(['/invoice/print', 'id' => $model->id]) ?>" class="btn btn-default btn-sm">
                <span class="hidden-xs">Печать</span><i class="fa fa-print visible-xs" aria-hidden="true"></i>
            </a>
            <a href="<?= Yii::$app->urlManager->createUrl(['/invoice/print', 'id' => $model->id]) ?>" class="btn btn-default btn-sm">
                <span class="hidden-xs">Отправить на e-mail</span><i class="fa fa-envelope-o visible-xs" aria-hidden="true"></i>
            </a>
            <!--
            <a href="<?php // echo Yii::$app->urlManager->createUrl(['/invoice/update', 'id' => $model->id]) ?>" class="btn btn-primary btn-sm">
                <span class="hidden-xs">Редактировать квитанцию</span><i class="fa fa-pencil visible-xs" aria-hidden="true"></i>
            </a>
            -->
        </div>
    </div>
    <div class="box-body">
        <table class="table table-bordered table-striped table-view">
            <tbody>
                <tr>
                    <td>Проведена</td>
                    <td>
                        <?= $model->getIsCheckedLabelHtml() ?> 
                    </td>
                </tr>
                <tr>
                    <td>Статус</td>
                    <td>
                        <?= $model->getStatusLabelHtml() ?> 
                    </td>
                </tr>
                <?php /* <tr>
                    <td>Месяц</td>
                    <td>
                        <?php echo $model->getMonthYearPrint() ?>
                    </td>
                </tr> */ ?>
                <tr>
                    <td>Период</td>
                    <td>
                        <?= $model->getPeriodPrint() ?>
                    </td>
                </tr>
                <tr>
                    <td>Владелец</td>
                    <td>
                        <?php if ($model->flat && $model->flat->user) { ?>
                            <a href="<?= Yii::$app->urlManager->createUrl(['/user/view', 'id' => $model->flat->user_id]) ?>">
                                <?= $model->flat->user->fullname ?>
                            </a>
                        <?php } else { ?>
                            не указано
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <td>Лицевой счет</td>
                    <td>
                        <?php if ($model->flat && $model->flat->account) { ?>
                            <a href="<?= Yii::$app->urlManager->createUrl(['/account/view', 'id' => $model->flat->account->id]) ?>">
                                <?= $model->flat->account->uid ?>
                            </a>
                        <?php } else { ?>
                            не указано
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <td>Телефон</td>
                    <td><?= $model->flat->user->profile->phone ?></td>
                </tr>
                <tr>
                    <td>Дом</td>
                    <td>
                        <?php if ($model->flat) { ?>
                            <a href="<?= Yii::$app->urlManager->createUrl(['/house/view', 'id' => $model->flat->house_id]) ?>">
                                <?= $model->flat->house->name ?>
                            </a>
                        <?php } else { ?>
                            не указано
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <td>Квартира</td>
                    <td>
                        <?php if ($model->flat) { ?>
                            <a href="<?= Yii::$app->urlManager->createUrl(['/flat/view', 'id' => $model->flat->id]) ?>">
                                <?= $model->flat->flat ?>
                            </a>
                        <?php } else { ?>
                            не указано
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <td>Секция</td>
                    <td><?= $model->flat->section->name ?></td>
                </tr>
                <tr>
                    <td>Тариф</td>
                    <td>
                        <?php if ($model->flat->tariff) { ?>
                            <a href="<?= Yii::$app->urlManager->createUrl(['/tariff/view', 'id' => $model->flat->tariff->id]) ?>">
                                <?= $model->flat->tariff->name ?>
                            </a>
                        <?php } else { ?>
                            не указано
                        <?php } ?>
                    </td>
                </tr>
                <?php /* ?><tr>
                    <td>Получатель</td>
                    <td><?= $model->payCompany->name ?></td>
                </tr><?php */ ?>
            </tbody>
        </table>
        <div class="table-responsive no-padding margin-top-15">
            <table class="table table-bordered table-striped">
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
                        <tr role="row">
                            <td><?= $k + 1 ?></td>
                            <td><?= $invoiceService->service->name ?></td>
                            <td><?= number_format($invoiceService->amount, 2) ?></td>
                            <td><?= $invoiceService->service->serviceUnit->name ?></td>
                            <td><?= PriceHelper::format($invoiceService->price_unit) ?></td>
                            <td><?= PriceHelper::format($invoiceService->price) ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
