<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\AccountTransaction;
use common\models\Account;

/* @var $this yii\web\View */
/* @var $model common\models\Flat */

$this->title = 'Квартира' . 
    ($model->flat ? (' №' . $model->flat . ($model->house->name ? (', ' . $model->house->name) : '')) : '');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Квартиры'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">Просмотр квартиры</h3>
        <div class="box-tools">
            <?php if ($model->account->status != Account::STATUS_DISABLED) { ?>
                <a href="<?= Yii::$app->urlManager->createUrl(['/account-transaction/create', 'type' => AccountTransaction::TYPE_IN, 'account_id' => $model->account->id]) ?>" class="btn btn-default btn-sm">
                    <span class="hidden-xs"><i class="fa fa-dollar"></i> Принять платеж</span><i class="fa fa-pencil visible-xs" aria-hidden="true"></i>
                </a>
                <a href="<?= Yii::$app->urlManager->createUrl(['/invoice/create', 'flat_id' => $model->id]) ?>" class="btn btn-default btn-sm">
                    <span class="hidden-xs"><i class="fa fa-files-o"></i> Оформить квитанцию</span><i class="fa fa-pencil visible-xs" aria-hidden="true"></i>
                </a>
            <?php } else { ?>
                <button class="btn btn-default btn-sm disabled" data-confirm="Лицевой счет неактивен"><span class="hidden-xs"><i class="fa fa-dollar"></i> Принять платеж</span><i class="fa fa-pencil visible-xs" aria-hidden="true"></i></button>
                <button class="btn btn-default btn-sm disabled" data-confirm="Лицевой счет неактивен"><span class="hidden-xs"><i class="fa fa-files-o"></i> Оформить квитанцию</span><i class="fa fa-pencil visible-xs" aria-hidden="true"></i></button>
            <?php } ?>
            <a href="<?= Yii::$app->urlManager->createUrl(['/flat/update', 'id' => $model->id]) ?>" class="btn btn-primary btn-sm">
                <span class="hidden-xs">Редактировать квартиру</span><i class="fa fa-pencil visible-xs" aria-hidden="true"></i>
            </a>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-xs-12">
                <table class="table table-bordered table-striped table-view">
                    <tbody>
                        <tr>
                            <td>Лицевой счет</td>
                            <td>
                                <?php if ($model->account) { ?>
                                    <a href="<?= Yii::$app->urlManager->createUrl(['/account/view', 'id' => $model->account->id]) ?>">
                                        <?= $model->account->uid ?>
                                    </a>
                                <?php } else { ?>
                                    не указано
                                <?php } ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Номер квартиры</td>
                            <td><?= $model->flat ?></td>
                        </tr>
                        <tr>
                            <td>Площадь</td>
                            <td>
                                <?php if ($model->square) { ?>
                                    <?= $model->square ?>м<sup>2</sup>
                                <?php } else { ?>
                                    не указано
                                <?php } ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Дом</td>
                            <td>
                                <?php if ($model->house) { ?>
                                    <a href="<?= Yii::$app->urlManager->createUrl(['/house/view', 'id' => $model->house_id]) ?>">
                                        <?= $model->house->name ?>
                                    </a>
                                <?php } else { ?>
                                    не указано
                                <?php } ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Секция</td>
                            <td><?= $model->section->name ?></td>
                        </tr>
                        <tr>
                            <td>Этаж</td>
                            <td><?= $model->floor->name ?></td>
                        </tr>
                        <?php /*
                        <tr>
                            <td>Стояк</td>
                            <td><?= $model->riser->name ?></td>
                        </tr>
                        */ ?>
                        <tr>
                            <td>Владелец</td>
                            <td>
                                <?php if ($model->user) { ?>
                                    <a href="<?= Yii::$app->urlManager->createUrl(['/user/view', 'id' => $model->user_id]) ?>">
                                        <?= $model->user->fullname ?>
                                    </a>
                                <?php } else { ?>
                                    не указано
                                <?php } ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Тариф</td>
                            <td>
                                <?php if ($model->tariff) { ?>
                                    <a href="<?= Yii::$app->urlManager->createUrl(['/tariff/view', 'id' => $model->tariff->id]) ?>">
                                        <?= $model->tariff->name ?>
                                    </a>
                                <?php } else { ?>
                                    не указано
                                <?php } ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div>
                    <p><a href="<?= Yii::$app->urlManager->createUrl(['/counter-data/counter-list', 'CounterDataSearch[flat_id]' => $model->id]) ?>">Посмотреть показания счетчиков</a></p>
                    <p><a href="<?= Yii::$app->urlManager->createUrl(['/account-transaction/index', 'AccountTransactionSearch[account_id]' => (int)$model->account->id, 'AccountTransactionSearch[type]' => AccountTransaction::TYPE_IN]) ?>">Посмотреть приходы</a></p>
                    <p><a href="<?= Yii::$app->urlManager->createUrl(['/invoice/index', 'InvoiceSearch[flat_id]' => $model->id]) ?>">Посмотреть квитанции</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
