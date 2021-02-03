<?php

use common\models\WebsiteService;
use common\models\Website;
use yii\helpers\Html;
use yii\widgets\LinkPager;

/* @var $this yii\web\View */
/* @var $services WebsiteService[] */

$this->title = Html::decode(Website::getParamContent(Website::PARAM_SERVICE_META_TITLE));
$this->registerMetaTag(['name' => 'description', 'content' => Html::decode(Website::getParamContent(Website::PARAM_SERVICE_META_DESCRIPTION))]);
$this->registerMetaTag(['name' => 'keywords', 'content' => Html::decode(Website::getParamContent(Website::PARAM_SERVICE_META_KEYWORDS))]);

?>
<div class="breadcrumbs">
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <ol class="breadcrumb">
                    <li><a href="<?= Yii::$app->urlManager->createUrl(['/site/index']) ?>">Главная</a></li>
                    <li class="active">Услуги</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="services">
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="page-header">
                    <h1>Наши услуги</h1>
                </div>
            </div>
        </div>
        <?php if ($services) { ?>
            <?php foreach ($services as $service) { ?>
                <div class="row">
                    <div class="col-xs-12 col-md-7">
                        <img src="<?= Yii::$app->urlManager->createUrl(['/site/glide', 'path' => $service->getImagePath(), 'w' => 720, 'h' => 300, 'fit' => 'crop']) ?>" class="img-responsive img-thumbnail" alt="">
                    </div>
                    <div class="col-xs-12 col-md-5">
                        <div class="page-header">
                            <h3><?= Html::decode($service->title) ?></h3>
                        </div>
                        <?= Html::decode($service->description) ?>
                    </div>
                </div>
            <?php } ?>
        <?php } ?>
    </div>
</div>

<div class="paginations">
    <div class="container">
        <div class="row">
            <div class="col-xs-12 text-center">
                <nav aria-label="Page navigation">
                    <?= LinkPager::widget(['pagination' => $pages]) ?>
                </nav>
            </div>
        </div>
    </div>
</div>
