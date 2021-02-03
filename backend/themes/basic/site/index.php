<?php

use kartik\daterange\DateRangePicker;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use dosamigos\chartjs\ChartJs;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use common\models\Invoice;
use common\helpers\PriceHelper;

/* @var $this yii\web\View */
/* @var $usersNewCount integer */
/* @var $usersActiveCount integer */
/* @var $accountsCount integer */
/* @var $housesCount integer */
/* @var $flatsCount integer */
/* @var $masterRequestsNewCount integer */
/* @var $masterRequestsActiveCount integer */
/* @var $debtTotal float */
/* @var $debtMonth float */
/* @var $debtQuarter float */
/* @var $accountsBalance float */
/* @var $accountsDebtTotal float */
/* @var $cashboxBalance float */
/* @var $chartLabelsArea json */
/* @var $chartDataBarDebt json */
/* @var $chartDataBarPay json */
/* @var $chartDataBar2In json */
/* @var $chartDataBar2Out json */
/* @var $chartLabelsFrom string */
/* @var $chartLabelsTo string */

$this->title = Yii::t('app', 'Статистика');
?>
<div class="row">
    <div class="col-xs-12 col-sm-6">
        <h2 class="page-header">В вашем обслуживании</h2>
    </div>
</div>
<div class="row">
    <div class="col-lg-4 col-sm-6 col-xs-12">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3><?= $housesCount ?></h3>
                <p>Домов</p>
            </div>
            <div class="icon">
                <i class="fa fa-building"></i>
            </div>
            <a href="<?= Yii::$app->urlManager->createUrl(['/house/index']) ?>" class="small-box-footer">
                Перейти в дома <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-4 col-sm-6 col-xs-12">
        <div class="small-box bg-green">
            <div class="inner">
                <h3><?= $usersActiveCount ?></h3>
                <p>Активных владельцев</p>
            </div>
            <div class="icon">
                <i class="fa fa-users"></i>
            </div>
            <a href="<?= Yii::$app->urlManager->createUrl(['/user/index']) ?>" class="small-box-footer">
                Перейти к владельцам <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-4 col-sm-6 col-xs-12">
        <div class="small-box bg-yellow">
            <div class="inner">
                <h3><?= $masterRequestsActiveCount ?></h3>
                <p>Заявок мастера в работе</p>
            </div>
            <div class="icon">
                <i class="fa fa-wrench"></i>
            </div>
            <a href="<?= Yii::$app->urlManager->createUrl(['/master-request/index']) ?>" class="small-box-footer">
                Перейти в заявки <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-4 col-sm-6 col-xs-12">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3><?= $flatsCount ?></h3>
                <p>Квартир</p>
            </div>
            <div class="icon">
                <i class="fa fa-key"></i>
            </div>
            <a href="<?= Yii::$app->urlManager->createUrl(['/flat/index']) ?>" class="small-box-footer">
                Перейти в квартиры <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-4 col-sm-6 col-xs-12">
        <div class="small-box bg-green">
            <div class="inner">
                <h3><?= $accountsCount ?></h3>
                <p>Лицевых счетов</p>
            </div>
            <div class="icon">
                <i class="fa fa-child"></i>
            </div>
            <a href="<?= Yii::$app->urlManager->createUrl(['/account/index']) ?>" class="small-box-footer">
                Перейти к счетам <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-4 col-sm-6 col-xs-12">
        <div class="small-box bg-yellow">
            <div class="inner">
                <h3><?= $masterRequestsNewCount ?></h3>
                <p>Новых заявок мастера</p>
            </div>
            <div class="icon">
                <i class="fa fa-user-plus"></i>
            </div>
            <a href="<?= Yii::$app->urlManager->createUrl(['/master-request/index']) ?>" class="small-box-footer">
                Перейти в заявки <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-8 col-md-7 col-xs-12">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">График погашения квитанций, грн</h3>
            </div>
            <div class="box-body">
                <div class="chart">
                    <canvas id="barChart" style="height: 201px;"></canvas>
                    <div id="barChart-legend" class="text-center"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-5 col-xs-12">
        <div class="info-box">
            <span class="info-box-icon bg-red"><i class="fa fa-money"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Задолженность по счетам, грн</span>
                <span class="info-box-number"><?= PriceHelper::format($accountsDebtTotal) ?></span>
            </div>
        </div>
        <div class="info-box">
            <span class="info-box-icon bg-blue"><i class="fa fa-money"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Баланс по счетам, грн</span>
                <span class="info-box-number"><?= PriceHelper::format($accountsBalance) ?></span>
            </div>
        </div>
        <div class="info-box">
            <span class="info-box-icon bg-green"><i class="fa fa-money"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Состояние кассы, грн</span>
                <span class="info-box-number"><?= PriceHelper::format($cashboxBalance) ?></span>
            </div>
        </div>
<!--        <div class="info-box">
            <span class="info-box-icon bg-green"><i class="fa fa-cog"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Задолженность за месяц, грн</span>
                <span class="info-box-number"><?= PriceHelper::format($debtMonth) ?></span>
            </div>
        </div>
        <div class="info-box">
            <span class="info-box-icon bg-aqua"><i class="fa fa-cog"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Задолженность за квартал, грн</span>
                <span class="info-box-number"><?= PriceHelper::format($debtQuarter) ?></span>
            </div>
        </div>-->
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">График приходов и расходов по кассе, грн</h3>
            </div>
            <div class="box-body">
                <div class="chart">
                    <canvas id="barChart2" style="height: 230px;"></canvas>
                    <div id="barChart2-legend" class="text-center"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->registerJs(<<<JS
    $(function () {

        'use strict';

        //-------------
        //- BAR CHART -
        //-------------

        var areaChartData = {
            labels  : {$chartLabelsArea},
            datasets: [
                {
                    label               : 'Задолженность',
                    fillColor           : 'rgba(221, 75, 57, 1)',
                    strokeColor         : 'rgba(221, 75, 57, 1)',
                    //pointColor          : 'rgba(210, 214, 222, 1)',
                    //pointStrokeColor    : '#c1c7d1',
                    //pointHighlightFill  : '#fff',
                    //pointHighlightStroke: 'rgba(220,220,220,1)',
                    data                : {$chartDataBarDebt}
                },
                {
                    label               : 'Погашение задолженности',
                    fillColor           : 'rgba(0, 166, 90, 1)',
                    strokeColor         : 'rgba(0, 166, 90, 1)',
                    //pointColor          : '#3b8bba',
                    //pointStrokeColor    : 'rgba(60,141,188,1)',
                    //pointHighlightFill  : '#fff',
                    //pointHighlightStroke: 'rgba(60,141,188,1)',
                    data                : {$chartDataBarPay}
                }
            ]
        };

        var barChartCanvas                   = $('#barChart').get(0).getContext('2d');
        var barChart                         = new Chart(barChartCanvas);
        var barChartData                     = areaChartData;
//        barChartData.datasets[1].fillColor   = '#00a65a';
//        barChartData.datasets[1].strokeColor = '#00a65a';
//        barChartData.datasets[1].pointColor  = '#00a65a';
        var barChartOptions                  = {
            //Boolean - Whether the scale should start at zero, or an order of magnitude down from the lowest value
            scaleBeginAtZero        : true,
            //Boolean - Whether grid lines are shown across the chart
            scaleShowGridLines      : true,
            //String - Colour of the grid lines
            scaleGridLineColor      : 'rgba(0,0,0,.05)',
            //Number - Width of the grid lines
            scaleGridLineWidth      : 1,
            //Boolean - Whether to show horizontal lines (except X axis)
            scaleShowHorizontalLines: true,
            //Boolean - Whether to show vertical lines (except Y axis)
            scaleShowVerticalLines  : true,
            //Boolean - If there is a stroke on each bar
            barShowStroke           : true,
            //Number - Pixel width of the bar stroke
            barStrokeWidth          : 2,
            //Number - Spacing between each of the X value sets
            barValueSpacing         : 5,
            //Number - Spacing between data sets within X values
            barDatasetSpacing       : 1,
            //String - A legend template
            legendTemplate          : '<ul class="<%=name.toLowerCase()%>-legend"><% for (var i=0; i<datasets.length; i++){%><li><span style="background-color:<%=datasets[i].fillColor%>"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>',
            //Boolean - whether to make the chart responsive
            responsive              : true,
            maintainAspectRatio     : true
        };

        barChartOptions.datasetFill = false;
        var myBarChart = barChart.Bar(barChartData, barChartOptions);
        document.getElementById('barChart-legend').innerHTML = myBarChart.generateLegend();

        //-----------------
        //- END BAR CHART -
        //-----------------
                    
        //-------------
        //- BAR CHART 2 -
        //-------------

        var areaChartData = {
            labels  : {$chartLabelsArea},
            datasets: [
                {
                    label               : 'Приход',
                    fillColor           : 'rgba(0, 166, 90, 1)',
                    strokeColor         : 'rgba(0, 166, 90, 1)',
                    //pointColor          : 'rgba(210, 214, 222, 1)',
                    //pointStrokeColor    : '#c1c7d1',
                    //pointHighlightFill  : '#fff',
                    //pointHighlightStroke: 'rgba(220,220,220,1)',
                    data                : {$chartDataBar2In}
                },
                {
                    label               : 'Расход',
                    fillColor           : 'rgba(221, 75, 57, 1)',
                    strokeColor         : 'rgba(221, 75, 57, 1)',
                    //pointColor          : '#3b8bba',
                    //pointStrokeColor    : 'rgba(60,141,188,1)',
                    //pointHighlightFill  : '#fff',
                    //pointHighlightStroke: 'rgba(60,141,188,1)',
                    data                : {$chartDataBar2Out}
                }
            ]
        };

        var barChart2Canvas                   = $('#barChart2').get(0).getContext('2d');
        var barChart2                         = new Chart(barChart2Canvas);
        var barChart2Data                     = areaChartData;
//        barChart2Data.datasets[1].fillColor   = '#00a65a';
//        barChart2Data.datasets[1].strokeColor = '#00a65a';
//        barChart2Data.datasets[1].pointColor  = '#00a65a';
        var barChart2Options                  = {
            //Boolean - Whether the scale should start at zero, or an order of magnitude down from the lowest value
            scaleBeginAtZero        : true,
            //Boolean - Whether grid lines are shown across the chart
            scaleShowGridLines      : true,
            //String - Colour of the grid lines
            scaleGridLineColor      : 'rgba(0,0,0,.05)',
            //Number - Width of the grid lines
            scaleGridLineWidth      : 1,
            //Boolean - Whether to show horizontal lines (except X axis)
            scaleShowHorizontalLines: true,
            //Boolean - Whether to show vertical lines (except Y axis)
            scaleShowVerticalLines  : true,
            //Boolean - If there is a stroke on each bar
            barShowStroke           : true,
            //Number - Pixel width of the bar stroke
            barStrokeWidth          : 2,
            //Number - Spacing between each of the X value sets
            barValueSpacing         : 5,
            //Number - Spacing between data sets within X values
            barDatasetSpacing       : 1,
            //String - A legend template
            legendTemplate          : '<ul class="<%=name.toLowerCase()%>-legend"><% for (var i=0; i<datasets.length; i++){%><li><span style="background-color:<%=datasets[i].fillColor%>"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>',
            //Boolean - whether to make the chart responsive
            responsive              : true,
            maintainAspectRatio     : true
        };

        barChart2Options.datasetFill = false;
        var myBarChart2 = barChart2.Bar(barChart2Data, barChart2Options);
        document.getElementById('barChart2-legend').innerHTML = myBarChart2.generateLegend();

        //-----------------
        //- END BAR CHART 2 -
        //-----------------
    });

JS
);
