<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\helpers\PriceHelper;
use common\models\AccountTransaction;
use common\models\Account;

/* @var $this yii\web\View */
/* @var $model common\models\AccountTransaction */

$this->title = Yii::t('app', $model->type == AccountTransaction::TYPE_IN ? 'Приходная ведомость' : 'Расходная ведомость');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Платежи'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title . ' №' . $model->uid;
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
        <h3 class="box-title"><?= $this->title ?></h3>
        <div class="box-tools">
            <a href="<?= Yii::$app->urlManager->createUrl(['/account-transaction/create', 'account_transaction_id' => $model->id]) ?>" class="btn btn-default btn-sm">
                <span class="hidden-xs">Копировать</span><i class="fa fa-print visible-xs" aria-hidden="true"></i>
            </a>
            <?php if ($model->account && $model->account->status == Account::STATUS_DISABLED) { ?>
                <button class="btn btn-default btn-sm disabled" data-confirm="Счет платежа неактивен, нельзя удалить платеж"><span class="hidden-xs">Удалить</span><i class="fa fa-envelope-o visible-xs" aria-hidden="true"></i></button>
            <?php } else { ?>
                <a href="<?= Yii::$app->urlManager->createUrl(['/account-transaction/delete', 'id' => $model->id]) ?>" class="btn btn-default btn-sm" data-method="post" data-confirm="Вы уверены, что хотите удалить этот элемент?">
                    <span class="hidden-xs">Удалить</span><i class="fa fa-envelope-o visible-xs" aria-hidden="true"></i>
                </a>
            <?php } ?>
            <a href="<?= Yii::$app->urlManager->createUrl(['/account-transaction/update', 'id' => $model->id]) ?>" class="btn btn-primary btn-sm">
                <span class="hidden-xs">Редактировать ведомость</span><i class="fa fa-pencil visible-xs" aria-hidden="true"></i>
            </a>
            <a href="<?= Yii::$app->urlManager->createUrl(['/account-transaction/export-one', 'id' => $model->id, 'export' => 'xls']) ?>" class="btn btn-default btn-sm">
                <span class="hidden-xs">Выгрузить в Excel</span><i class="fa fa-pencil visible-xs" aria-hidden="true"></i>
            </a>
        </div>
    </div>
    <div class="box-body">
        <table class="table table-bordered table-striped table-view">
            <tbody>
                <tr>
                    <td>Владелец квартиры</td>
                    <td>
                        <?php if ($model->account->flat) { ?>
                            <a href="<?= Yii::$app->urlManager->createUrl(['/user/view', 'id' => $model->account->flat->user_id]) ?>">
                                <?= $model->account->flat->user->fullname ?>
                            </a>
                        <?php } else { ?>
                            не указано
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <td>Лицевой счет</td>
                    <td>
                        <?php if ($model->account) { ?>
                            <a href="<?= Yii::$app->urlManager->createUrl(['/account/view', 'id' => $model->account_id]) ?>">
                                <?= $model->account->uid ?>
                            </a>
                        <?php } else { ?>
                            не указано
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <td>Статья</td>
                    <td><?= $model->transactionPurpose->name ?></td>
                </tr>
                <tr>
                    <td>Квитанция</td>
                    <td>
                        <?php if ($model->invoice) { ?>
                            <a href="<?= Yii::$app->urlManager->createUrl(['/invoice/view', 'id' => $model->invoice_id]) ?>">
                                <?= $model->invoice->uid . ' от ' . $model->invoice->getUidDate() ?>
                            </a>
                        <?php } else { ?>
                            не указано
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <td>Услуга</td>
                    <td><?= $model->invoiceService ? ($model->invoiceService->service->name . ', сумма: ' . PriceHelper::format($model->invoiceService->price)) : '' ?></td>
                </tr>
                <tr>
                    <td>Менеджер</td>
                    <td>
                        <?php if ($model->userAdmin) { ?>
                            <a href="<?= Yii::$app->urlManager->createUrl(['/user-admin/view', 'id' => $model->user_admin_id]) ?>">
                                <?= $model->userAdmin->fullname ?>
                            </a>
                        <?php } else { ?>
                            не указано
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <td>Сумма</td>
                    <td>
                        <?php
                        $minus = $model->type == AccountTransaction::TYPE_OUT ? '-' : '';
                        ?>
                        <span class="<?= $minus ? 'text-red' : 'text-green' ?>"><?= $minus . PriceHelper::format($model->amount) ?></span>
                    </td>
                </tr>
                <tr>
                    <td>Комментарий</td>
                    <td><?= $model->description ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
