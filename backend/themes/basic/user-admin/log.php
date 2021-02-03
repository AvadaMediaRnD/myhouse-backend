<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use common\models\UserAdminLog;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\UserAdminLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $timeLineData array */
/* @var $pages yii\data\Pagination */

$this->title = Yii::t('app', 'Лог изменений');
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">Лог изменений</h3>
    </div>
    
    <div class="box-body">
        
        <ul class="timeline">
            
            <?php foreach ($timeLineData as $k => $data) { ?>
            
                <li class="time-label">
                    <span class="bg-yellow">
                        <?= $k ?>
                    </span>
                </li>
                
                <?php foreach ($data as $item) { ?> 
                
                    <li>
                        <i class="<?= $item['icon'] ?> <?= $item['bg'] ?>"></i>

                        <div class="timeline-item">
                            <span class="time"><i class="fa fa-clock-o"></i> <?= $item['time'] ?></span>

                            <h3 class="timeline-header"><?= $item['title'] ?> пользователем <a href="<?= $item['userId'] ? Yii::$app->urlManager->createUrl(['/user-admin/view', 'id' => $item['userId']]) : '#!' ?>"><?= $item['user'] ?></a></h3>

                            <div class="timeline-body">
                                <?= $item['text'] ?>
                            </div>
                        </div>
                    </li>
                
                <?php } ?>
            
            <?php } ?>
            
        </ul>
        
        <?= yii\widgets\LinkPager::widget([
            'pagination' => $pages,
        ]) ?>
        
    </div>
    
</div>