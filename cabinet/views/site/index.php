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
/* @var $flat common\models\Flat */
/* @var $showCharts boolean */
/* @var $chartDataPieMonth json */
/* @var $chartDataPieYear json */
/* @var $chartDataArea json */
/* @var $chartLabelsArea json */

$this->title = Yii::t('app', 'Сводка') . ' - ' . $flat->house->name . ', кв.' . $flat->flat;

$balance = $flat->account ? $flat->account->getBalance() : 0;
?>
<div class="row">
    <div class="col-xs-12 col-md-6 col-lg-4">
        <div class="small-box <?= $balance < 0 ? 'bg-red' : 'bg-green' ?>">
            <div class="inner">
                <h3><?= PriceHelper::format($balance, true, true) ?></h3>
                <p>Баланс по квартире</p>
            </div>
            <div class="icon">
                <i class="fa fa-money"></i>
            </div>
            <span class="small-box-footer">
                &nbsp;
            </span>
        </div>
    </div>
    <div class="col-xs-12 col-md-6 col-lg-4">
        <div class="small-box bg-blue">
            <div class="inner">
                <h3><?= $flat->account->uid ?: 'Не создан' ?></h3>
                <p>Лицевой счет</p>
            </div>
            <div class="icon">
                <i class="fa fa-user"></i>
            </div>
            <span class="small-box-footer">
                &nbsp;
            </span>
        </div>
    </div>
    <div class="col-xs-12 col-md-6 col-lg-4">
        <div class="small-box bg-yellow">
            <div class="inner">
                <h3><?= PriceHelper::format($flat->getMonthPriceAverage(), true, true) ?></h3>
                <p>Средний расход за месяц</p>
            </div>
            <div class="icon">
                <i class="fa fa-pie-chart"></i>
            </div>
            <span class="small-box-footer">
                &nbsp;
            </span>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 id="pieChartTitle" class="box-title">Диаграмма расходов</h3>
                <?php /* ?>
                <div class="box-tools pull-right">
                    <div class="form-group form-group-sm">
                        <select name="pie-period" class="form-control">
                            <option value="month" selected>Предыдущий месяц</option>
                            <option value="year">Текущий год</option>
                        </select>
                    </div>
                </div>
                <?php */ ?>
            </div>
            <div class="box-body">
                <div class="row">
                    <?php if ($showCharts) { ?>
                        <div id="pieChartMonth-container" class="col-md-6 col-xs-12">
                            <h4 class="text-center">за предыдущий месяц</h4>
                            <div class="col-xs-12">
                                <div class="chart">
                                    <canvas id="pieChartMonth" style="width: 795px; height: 330px;"></canvas>
                                </div>
                            </div>
                            <div class="col-xs-12">
                                <div id="pieChartMonth-legend" class="text-center"></div>
                            </div>
                        </div>

                        <div id="pieChartYear-container" class="col-md-6 col-xs-12">
                            <h4 class="text-center">за текущий год</h4>
                            <div class="col-xs-12">
                                <div class="chart">
                                    <canvas id="pieChartYear" style="width: 795px; height: 330px;"></canvas>
                                </div>
                            </div>
                            <div class="col-xs-12">
                                <div id="pieChartYear-legend" class="text-center"></div>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="col-xs-12">
                            <p>Недостаточно данных для отображения статистики</p>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Диаграмма расходов по месяцам за год</h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <?php if ($showCharts) { ?>
                        <div class="col-xs-12">
                            <div class="chart">
                                <canvas id="barChart" style="width: 795px; height: 330px;"></canvas>
                                <div id="barChart-legend" class="text-center"></div>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="col-xs-12">
                            <p>Недостаточно данных для отображения статистики</p>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->registerJs(<<<JS
    var mypieChartYear = null;
    $(function () {

        //-------------
        //- PIE CHART -
        //-------------

        // Get context with jQuery - using jQuery's .get() method.
        var pieChartMonthCanvas = $('#pieChartMonth').get(0).getContext('2d');
        var pieChartMonth       = new Chart(pieChartMonthCanvas);
        var PieDataMonth        = {$chartDataPieMonth};
        var pieOptionsMonth     = {
            //Boolean - Whether we should show a stroke on each segment
            segmentShowStroke    : true,
            //String - The colour of each segment stroke
            segmentStrokeColor   : '#fff',
            //Number - The width of each segment stroke
            segmentStrokeWidth   : 2,
            //Number - The percentage of the chart that we cut out of the middle
            percentageInnerCutout: 30, // This is 0 for Pie charts
            //Number - Amount of animation steps
            animationSteps       : 30,
            //String - Animation easing effect
            animationEasing      : 'linear',
            //Boolean - Whether we animate the rotation of the Doughnut
            animateRotate        : true,
            //Boolean - Whether we animate scaling the Doughnut from the centre
            animateScale         : false,
            //Boolean - whether to make the chart responsive to window resizing
            responsive           : true,
            // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
            maintainAspectRatio  : true,
            legend: 'top',
            //String - A legend template
            legendTemplate       : '<ul class="<%=name.toLowerCase()%>-legend"><% for (var i=0; i<segments.length; i++){%><li><span style="background-color:<%=segments[i].fillColor%>"></span><%if(segments[i].label){%><%=segments[i].label%>: <%=segments[i].value%><%}%></li><%}%></ul>'
        };
        //Create pie or douhnut chart
        // You can switch between pie and douhnut using the method below.
        var mypieChartMonth = pieChartMonth.Doughnut(PieDataMonth, pieOptionsMonth);
        document.getElementById('pieChartMonth-legend').innerHTML = mypieChartMonth.generateLegend();

        
        // Get context with jQuery - using jQuery's .get() method.
        var pieChartYearCanvas = $('#pieChartYear').get(0).getContext('2d');
        var pieChartYear       = new Chart(pieChartYearCanvas);
        var PieDataYear        = {$chartDataPieYear};
        var pieOptionsYear     = {
            //Boolean - Whether we should show a stroke on each segment
            segmentShowStroke    : true,
            //String - The colour of each segment stroke
            segmentStrokeColor   : '#fff',
            //Number - The width of each segment stroke
            segmentStrokeWidth   : 2,
            //Number - The percentage of the chart that we cut out of the middle
            percentageInnerCutout: 30, // This is 0 for Pie charts
            //Number - Amount of animation steps
            animationSteps       : 30,
            //String - Animation easing effect
            animationEasing      : 'linear',
            //Boolean - Whether we animate the rotation of the Doughnut
            animateRotate        : true,
            //Boolean - Whether we animate scaling the Doughnut from the centre
            animateScale         : false,
            //Boolean - whether to make the chart responsive to window resizing
            responsive           : true,
            // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
            maintainAspectRatio  : true,
            legend: 'top',
            //String - A legend template
            legendTemplate       : '<ul class="<%=name.toLowerCase()%>-legend"><% for (var i=0; i<segments.length; i++){%><li><span style="background-color:<%=segments[i].fillColor%>"></span><%if(segments[i].label){%><%=segments[i].label%>: <%=segments[i].value%><%}%></li><%}%></ul>'
        };
        //Create pie or douhnut chart
        // You can switch between pie and douhnut using the method below.
        mypieChartYear = pieChartYear.Doughnut(PieDataYear, pieOptionsYear);
        document.getElementById('pieChartYear-legend').innerHTML = mypieChartYear.generateLegend();
        
        // $('#pieChartYear-container').hide();
        
        //-----------------
        //- END PIE CHART -
        //-----------------
        
        //-------------
        //- BAR CHART -
        //-------------

        var areaChartData = {
            labels  : {$chartLabelsArea},
            datasets: [
                {
                    label               : 'Расход за месяц',
//                    fillColor           : 'rgba(243, 156, 17, 1)',
//                    strokeColor         : 'rgba(243, 156, 17, 1)',
                    fillColor           : 'rgba(0, 115, 183, 1)',
                    strokeColor         : 'rgba(0, 115, 183, 1)',
                    //pointColor          : '#3b8bba',
                    //pointStrokeColor    : 'rgba(60,141,188,1)',
                    //pointHighlightFill  : '#fff',
                    //pointHighlightStroke: 'rgba(60,141,188,1)',
                    data                : {$chartDataArea}
                },
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
//        document.getElementById('barChart-legend').innerHTML = myBarChart.generateLegend();

        //-----------------
        //- END BAR CHART -
        //-----------------

    });

JS
);

$this->registerJs(<<<JS
    $('select[name="pie-period"]').on('change', function () {
        var selectValue = $(this).val();
        if (selectValue == 'month') {
            $('#pieChartMonth-container').show();
            $('#pieChartYear-container').hide();
            // $('#pieChartTitle').html('Диаграмма расходов за предыдущий месяц');
        } else if (selectValue == 'year') {
            $('#pieChartMonth-container').hide();
            $('#pieChartYear-container').show();
            // $('#pieChartTitle').html('Диаграмма расходов за текущий год');
        }
    });
JS
);    