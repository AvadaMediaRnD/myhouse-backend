<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\helpers\PriceHelper;
use common\models\AccountTransaction;
use common\models\Account;

/* @var $this yii\web\View */
/* @var $model common\models\Account */

$this->title = 'Лицевой счет';
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Лицевые счета'), 'url' => ['index']];
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
        </div>
    </div>
</div>
<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">Просмотр лицевого счета</h3>
        <div class="box-tools">
            <?php if ($model->status != Account::STATUS_DISABLED && $model->flat != null) { ?>
                <a href="<?= Yii::$app->urlManager->createUrl(['/account-transaction/create', 'type' => AccountTransaction::TYPE_IN, 'account_id' => $model->id]) ?>" class="btn btn-default btn-sm">
                    <span class="hidden-xs"><i class="fa fa-dollar"></i> Принять платеж</span><i class="fa fa-pencil visible-xs" aria-hidden="true"></i>
                </a>
                <a href="<?= Yii::$app->urlManager->createUrl(['/invoice/create', 'flat_id' => $model->flat_id]) ?>" class="btn btn-default btn-sm">
                    <span class="hidden-xs"><i class="fa fa-files-o"></i> Оформить квитанцию</span><i class="fa fa-pencil visible-xs" aria-hidden="true"></i>
                </a>
            <?php } else { ?>
                <button class="btn btn-default btn-sm disabled" data-confirm="Лицевой счет неактивен или не привязан к квартире"><span class="hidden-xs"><i class="fa fa-dollar"></i> Принять платеж</span><i class="fa fa-pencil visible-xs" aria-hidden="true"></i></button>
                <button class="btn btn-default btn-sm disabled" data-confirm="Лицевой счет неактивен или не привязан к квартире"><span class="hidden-xs"><i class="fa fa-files-o"></i> Оформить квитанцию</span><i class="fa fa-pencil visible-xs" aria-hidden="true"></i></button>
            <?php } ?>
            <a href="<?= Yii::$app->urlManager->createUrl(['/account/update', 'id' => $model->id]) ?>" class="btn btn-primary btn-sm">
                <span class="hidden-xs">Редактировать счет</span><i class="fa fa-pencil visible-xs" aria-hidden="true"></i>
            </a>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-xs-12">
                <table class="table table-bordered table-striped table-view">
                    <tbody>
                        <tr>
                            <td>Статус</td>
                            <td><?= $model->getStatusLabelHtml() ?></td>
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
                            <td>Секция</td>
                            <td><?= $model->flat ? $model->flat->section->name : 'не указано' ?></td>
                        </tr>
                        <tr>
                            <td>Квартира</td>
                            <td>
                                <?php if ($model->flat) { ?>
                                    <a href="<?= Yii::$app->urlManager->createUrl(['/flat/view', 'id' => $model->flat_id]) ?>">
                                        <?= $model->flat->flat ?>
                                    </a>
                                <?php } else { ?>
                                    не указано
                                <?php } ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Владелец</td>
                            <td>
                                <?php if ($model->flat) { ?>
                                    <a href="<?= Yii::$app->urlManager->createUrl(['/user/view', 'id' => $model->flat->user_id]) ?>">
                                        <?= $model->flat->user->fullname ?>
                                    </a>
                                <?php } else { ?>
                                    не указано
                                <?php } ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Остаток, грн.</td>
                            <td>
                                <?php
                                $balance = $model->getBalance();
                                $label = ($balance < 0) ? 'text-red' : ($balance > 0 ? 'text-green' : 'text-default');
                                echo '<span class="'.$label.'">' . PriceHelper::format($balance) . '</span>';
                                ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div>
                    <p><a href="<?= Yii::$app->urlManager->createUrl(['/counter-data/counter-list', 'CounterDataSearch[flat_id]' => (int)$model->flat_id]) ?>">Посмотреть показания счетчиков</a></p>
                    <p><a href="<?= Yii::$app->urlManager->createUrl(['/account-transaction/index', 'AccountTransactionSearch[account_id]' => $model->id, 'AccountTransactionSearch[type]' => AccountTransaction::TYPE_IN]) ?>">Посмотреть приходы</a></p>
                    <p><a href="<?= Yii::$app->urlManager->createUrl(['/invoice/index', 'InvoiceSearch[flat_id]' => (int)$model->flat_id]) ?>">Посмотреть квитанции</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
