<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\MasterRequest */

$this->title = 'Заявка №' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Заявки вызова мастера'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-xs-12 col-md-7 col-lg-6">
        <div class="page-header-spec">
            <div class="form-group">
                <div class="input-group date">
                    <div class="input-group-addon">
                        <i class="glyphicon glyphicon-calendar"></i>
                    </div>
                    <div class="form-control pull-right"><?= $model->date_request ?></div>
                </div>
            </div>
            <span class="label-mid">от</span>
            <div class="form-group">
                <div class="input-group date">
                    <div class="input-group-addon">
                        <i class="glyphicon glyphicon-time"></i>
                    </div>
                    <div class="form-control pull-right"><?= $model->time_request ?></div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title"></h3>
                <div class="box-tools">
                    <a href="<?= Yii::$app->urlManager->createUrl(['/master-request/update', 'id' => $model->id]) ?>" class="btn btn-primary btn-sm">
                        <span class="hidden-xs">Редактировать заявку</span><i class="fa fa-pencil visible-xs" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
            <div class="box-body">
                <table class="table table-bordered table-striped table-view">
                    <tbody>
                        <tr>
                            <td>Статус</td>
                            <td><?= $model->getStatusLabelHtml() ?></td>
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
                            <td>Телефон</td>
                            <td><?= ($model->flat && $model->flat->user && $model->flat->user->profile) ? $model->flat->user->profile->phone : '' ?></td>
                        </tr>
                        <tr>
                            <td>Квартира</td>
                            <td>
                                <?php if ($model->flat) { ?>
                                    <a href="<?= Yii::$app->urlManager->createUrl(['/flat/view', 'id' => $model->flat_id]) ?>">
                                        <?= $model->flat->flat . ', ' . $model->flat->house->name ?>
                                    </a>
                                <?php } else { ?>
                                    не указано
                                <?php } ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Тип мастера</td>
                            <td><?= $model->getTypeLabel() ?></td>
                        </tr>
                        <tr>
                            <td>Мастер</td>
                            <td>
                                <?php if ($model->userAdmin) { ?>
                                    <a href="<?= Yii::$app->urlManager->createUrl(['/user-admin/view', 'id' => $model->user_admin_id]) ?>">
                                        <?= $model->userAdmin->fullname . ' (' . $model->userAdmin->getRoleLabel() . ')' ?>
                                    </a>
                                <?php } else { ?>
                                    не указано
                                <?php } ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Описание</td>
                            <td>
                                <?= $model->description ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Добавлено</td>
                            <td><?= $model->created ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
