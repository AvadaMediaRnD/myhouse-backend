<?php

use kartik\daterange\DateRangePicker;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use dosamigos\chartjs\ChartJs;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use common\models\Invoice;

/* @var $this yii\web\View */

$this->title = Yii::t('app', 'Статистика');
?>
<div class="site-index">

    <h3><?= Yii::t('app', 'На обслуживании') ?></h3>

    <div class="row">
        <div class="col-lg-4 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-aqua">
                <div class="inner">
                    <h3><?= $housesCount ?></h3>

                    <p><?= Yii::t('app', 'Жилых комплексов') ?></p>
                </div>
                <div class="icon">
                    <i class="fa fa-home"></i>
                </div>
                <a href="<?= Yii::$app->urlManager->createUrl(['/house/index']) ?>" class="small-box-footer"><?= Yii::t('app', 'Управление ЖК') ?> <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-4 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-aqua">
                <div class="inner">
                    <h3><?= $flatsCount ?></h3>

                    <p><?= Yii::t('app', 'Квартир') ?></p>
                </div>
                <div class="icon">
                    <i class="fa fa-th-large"></i>
                </div>
                <a href="<?= Yii::$app->urlManager->createUrl(['/flat/index']) ?>" class="small-box-footer"><?= Yii::t('app', 'Управление квартирами') ?> <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-4 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-green">
                <div class="inner">
                    <h3><?= $usersNewCount ?></h3>

                    <p><?= Yii::t('app', 'Новых владельцев квартир') ?></p>
                </div>
                <div class="icon">
                    <i class="fa fa-user-plus"></i>
                </div>
                <a href="<?= Yii::$app->urlManager->createUrl(['/user/index']) ?>" class="small-box-footer"><?= Yii::t('app', 'Управление пользователями') ?> <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-4 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-yellow">
                <div class="inner">
                    <h3><?= $usersActiveCount ?></h3>

                    <p><?= Yii::t('app', 'Активных владельцев квартир') ?></p>
                </div>
                <div class="icon">
                    <i class="fa fa-user"></i>
                </div>
                <a href="<?= Yii::$app->urlManager->createUrl(['/user/index']) ?>" class="small-box-footer"><?= Yii::t('app', 'Управление пользователями') ?> <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-4 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-green">
                <div class="inner">
                    <h3><?= $masterRequestsNewCount ?></h3>

                    <p><?= Yii::t('app', 'Новых заявок мастера') ?></p>
                </div>
                <div class="icon">
                    <i class="fa fa-briefcase"></i>
                </div>
                <a href="<?= Yii::$app->urlManager->createUrl(['/master-request/index']) ?>" class="small-box-footer"><?= Yii::t('app', 'Управление заявками') ?> <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-4 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-yellow">
                <div class="inner">
                    <h3><?= $masterRequestsActiveCount ?></h3>

                    <p><?= Yii::t('app', 'Заявок мастера в работе') ?></p>
                </div>
                <div class="icon">
                    <i class="fa fa-briefcase"></i>
                </div>
                <a href="<?= Yii::$app->urlManager->createUrl(['/master-request/index']) ?>" class="small-box-footer"><?= Yii::t('app', 'Управление заявками') ?> <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
    </div>

    <div class="row">



        <?php /* ?>
        <div class="col-md-6">
            <!-- AREA CHART -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Area Chart</h3>

                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="chart">
                        <canvas id="areaChart" style="height:250px"></canvas>
                    </div>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->

            <!-- DONUT CHART -->
            <div class="box box-danger">
                <div class="box-header with-border">
                    <h3 class="box-title">Donut Chart</h3>

                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <canvas id="pieChart" style="height:250px"></canvas>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->

        </div>
        <!-- /.col (LEFT) -->
        <?php */ ?>

        <div class="col-md-12">

            <div class="row">
                <?php $form = ActiveForm::begin([
                    'method' => 'get',
                    'action' => Url::current(['kvdate2' => null, 'dateFrom' => null, 'dateTo' => null])
                ]); ?>
                <div class="col-xs-3">
                    <div class="form-group">
                        <?php
                        $addon = '<span class="input-group-addon">
                            <i class="glyphicon glyphicon-calendar"></i>
                        </span>';
                        echo '<div class="input-group drp-container">';
                        echo DateRangePicker::widget([
                                'name' => 'kvdate2',
                                'useWithAddon' => true,
                                'convertFormat' => true,
                                'startAttribute' => 'dateFrom',
                                'endAttribute' => 'dateTo',
                                'startInputOptions' => ['value' => $dateFrom],
                                'endInputOptions' => ['value' => $dateTo],
                                'pluginOptions' => [
                                    'locale' => ['format' => 'Y-m-d'],
                                ]
                            ]) . $addon;
                        echo '</div>';
                        ?>
                    </div>
                </div>
                <div class="col-xs-2">
                    <?= Html::submitButton('Показать', ['class' => 'btn btn-primary']) ?>
                    <?= Html::a('Сброс', ['/site/index'], ['class' => 'btn btn-default']) ?>
                </div>
                <?php ActiveForm::end(); ?>
            </div>

            <!-- LINE CHART -->
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        График платежей и задолженностей за период по месяцам (в грн.).
                        <?php if ($house) { ?>
                            <span>ЖК: <?= $house->name ?></span>
                        <?php } else { ?>
                            <span>Все ЖК</span>
                        <?php } ?>
                    </h3>
                </div>
                <div class="box-body">
                    <div class="chart">
<!--                        <canvas id="lineChart" style="height:250px"></canvas>-->

                        <?= ChartJs::widget([
                            'type' => 'line',
                            'options' => [
                                'height' => 300,
//                                'width' => 400
                            ],
                            'data' => [
                                'labels' => $labels,
                                'datasets' => [
                                    [
                                        'label' => "Платежи",
                                        'backgroundColor' => "rgba(210, 214, 222, 0.2)",
                                        'borderColor' => "rgba(210, 214, 222, 1)",
                                        'pointBackgroundColor' => "rgba(210, 214, 222, 1)",
                                        'pointBorderColor' => "#fff",
                                        'pointHoverBackgroundColor' => "#fff",
                                        'pointHoverBorderColor' => "rgba(210, 214, 222, 1)",
                                        'data' => $valuesPaid
                                    ],
                                    [
                                        'label' => "Задолженности",
                                        'backgroundColor' => "rgba(221, 75, 57, 0.2)",
                                        'borderColor' => "rgba(221, 75, 57, 1)",
                                        'pointBackgroundColor' => "rgba(221, 75, 57, 1)",
                                        'pointBorderColor' => "#fff",
                                        'pointHoverBackgroundColor' => "#fff",
                                        'pointHoverBorderColor' => "rgba(221, 75, 57, 1)",
                                        'data' => $valuesUnpaid
                                    ]
                                ]
                            ]
                        ]);
                        ?>
                    </div>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->

            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">Платежи и задолженности по ЖК за период (в грн.)</h3>
                </div>
                <div class="box-body">
                    <div class="table">
                        <table id="example2" class="table table-bordered table-hover table-striped dataTable" role="grid">
                            <thead>
                            <tr role="row">
                                <th rowspan="1" colspan="1">ЖК</th>
                                <th rowspan="1" colspan="1">Платежи</th>
                                <th rowspan="1" colspan="1">Задолженности</th>
                                <th rowspan="1" colspan="1">Показать на графике</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($houses as $k => $house) { ?>
                                <?php
                                $flatIds = ArrayHelper::getColumn($house->flats, 'id');

                                $invoicesPayQuery = Invoice::find()
                                    ->where(['and', ['>=', 'invoice.created_at', $dateFromTs], ['<', 'invoice.created_at', $dateToTs]])
                                    ->andWhere(['invoice.status' => Invoice::STATUS_PAID])
                                    ->andWhere(['in', 'invoice.flat_id', $flatIds]);
                                $valuePaid = floatval($invoicesPayQuery->sum('invoice.price'));

                                $invoicesUnpayQuery = Invoice::find()
                                    ->where(['and', ['>=', 'invoice.created_at', $dateFromTs], ['<', 'invoice.created_at', $dateToTs]])
                                    ->andWhere(['invoice.status' => Invoice::STATUS_UNPAID])
                                    ->andWhere(['in', 'invoice.flat_id', $flatIds]);
                                $valueUnpaid = floatval($invoicesUnpayQuery->sum('invoice.price'));
                                ?>
                                <tr role="row">
                                    <td><?= $house->name ?></td>
                                    <td><?= number_format($valuePaid, 2) ?></td>
                                    <td><?= number_format($valueUnpaid, 2) ?></td>
                                    <td><a href="<?= Url::current(['houseId' => $house->id]) ?>"><i class="fa fa-line-chart"></i> </a></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->

        </div>
        <!-- /.col (RIGHT) -->
    </div>
    <!-- /.row -->

</div>
