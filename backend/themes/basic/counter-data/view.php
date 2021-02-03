<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\CounterData */

$this->title = 'Показание счетчикa';
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Счетчики'), 'url' => ['counters']];
$titleList = Yii::t('app', 'Показания счетчиков');
if ($model->flat_id && $model->flat) {
    $titleList .= ', кв.' . $model->flat->flat;
}
$this->params['breadcrumbs'][] = ['label' => $titleList, 'url' => ['counter-list', 'CounterDataSearch[flat_id]' => $model->flat_id, 'CounterDataSearch[service_id]' => $model->service_id]];
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
        <h3 class="box-title"></h3>
        <div class="box-tools">
            <a href="<?= Yii::$app->urlManager->createUrl(['/counter-data/update', 'id' => $model->id]) ?>" class="btn btn-primary btn-sm">
                <span class="hidden-xs">Редактировать показание</span><i class="fa fa-pencil visible-xs" aria-hidden="true"></i>
            </a>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-xs-12 col-lg-4">
                <table class="table table-bordered table-striped table-view">
                    <tbody>
                        <tr>
                            <td>Счетчик</td>
                            <td><?= $model->service->name ?></td>
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
                            <td><?= $model->flat->section->name ?></td>
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
                            <td>Пользователь</td>
                            <td>
                                <?php if ($model->flat->user) { ?>
                                    <a href="<?= Yii::$app->urlManager->createUrl(['/user/view', 'id' => $model->flat->user_id]) ?>">
                                        <?= $model->flat->user->fullname ?>
                                    </a>
                                <?php } else { ?>
                                    не указано
                                <?php } ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Текущие показания</td>
                            <td><?= $model->amount_total ?></td>
                        </tr>
                        <tr>
                            <td>Ед. изм.</td>
                            <td><?= $model->service->serviceUnit->name ?></td>
                        </tr>
                        <tr>
                            <td>Статус</td>
                            <td><?= $model->getStatusLabelHtml() ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
