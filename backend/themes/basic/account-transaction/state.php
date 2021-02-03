<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\Account;
use common\models\AccountTransaction;
use common\models\House;
use common\models\Section;
use common\helpers\PriceHelper;
use common\models\TransactionPurpose;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $tableData array */
/* @var $totalOutcome float */
/* @var $totalIncome float */

$this->title = Yii::t('app', 'Состояние счета');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Платежи'), 'url' => ['/account-transaction/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <!--<div class="col-xs-12 col-sm-6">-->
    <!--<h2 class="page-header">Квартиры</h2>-->
    <!--</div>-->
</div>
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Информация о состоянии счета</h3>
            </div>

            <div class="box-body table-responsive no-padding">
                <table class="table table-striped table-hover">
                    <thead>
                        <th>Месяц</th>
                        <th>Услуга</th>
                        <th>Поступило на счет, грн</th>
                        <th>Оплачено со счета, грн</th>
                    </thead>
                    <tbody>
                        <?php if ($tableData) { ?>
                            <?php foreach ($tableData as $month => $data) { ?>
                                <?php 
                                    $idx = 0;
                                ?>
                                <?php foreach ($data['services'] as $k => $service) { ?>

                                    <tr>
                                        <?php if ($idx == 0) { ?>
                                            <td rowspan="<?= count($data['services']) ?>"><?= $month ?></td>
                                        <?php } ?>
                                        <td><?= $service['name'] ?></td>
                                        <td style="width: 180px; max-width: 180px;"><?= PriceHelper::format($service['income']) ?></td>
                                        <td style="width: 180px; max-width: 180px;"><?= PriceHelper::format($service['outcome']) ?></td>
                                    </tr>
                                    <?php
                                        $idx++;
                                    ?>
                                <?php } ?>
                            <?php } ?>
                            <tr class="text-bold">
                                <td colspan="2">Итого:</td>
                                <td><?= PriceHelper::format($totalIncome) ?></td>
                                <td><?= PriceHelper::format($totalOutcome) ?></td>
                            </tr>
                        <?php } else { ?>
                            <tr>
                                <td colspan="4">Нет данных для отображения</td>
                            </tr> 
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            
        </div>
    </div>
</div>
