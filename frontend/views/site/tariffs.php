<?php

use common\models\Website;
use common\models\WebsiteTariff;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $tariffs WebsiteTariff[] */

$this->title = Html::decode(Website::getParamContent(Website::PARAM_TARIFF_META_TITLE));
$this->registerMetaTag(['name' => 'description', 'content' => Html::decode(Website::getParamContent(Website::PARAM_TARIFF_META_DESCRIPTION))]);
$this->registerMetaTag(['name' => 'keywords', 'content' => Html::decode(Website::getParamContent(Website::PARAM_TARIFF_META_KEYWORDS))]);
?>
<div class="breadcrumbs">
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <ol class="breadcrumb">
                    <li><a href="<?= Yii::$app->urlManager->createUrl(['/site/index']) ?>">Главная</a></li>
                    <li class="active">Тарифы</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="about">
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="box clearfix">
                    <div class="page-header">
                        <h1><?= Html::decode(Website::getParamContent(Website::PARAM_TARIFF_TITLE)) ?></h1>
                    </div>
                    <?= Html::decode(Website::getParamContent(Website::PARAM_TARIFF_DESCRIPTION)) ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($tariffs) { ?>
    <div class="gallery g1">
        <div class="container">
            <div class="row row-eq-height">
                <?php foreach ($tariffs as $tariff) { ?>
                    <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3 item" data-src="<?= $tariff->getImagePath() ?>">
                        <a href="<?= $tariff->getImagePath() ?>" class="big">
                            <img src="<?= Yii::$app->urlManager->createUrl(['/site/glide', 'path' => $tariff->getImagePath(), 'w' => 720, 'h' => 720, 'fit' => 'crop']) ?>" alt="<?= Html::encode($tariff->title) ?>" class="img-responsive img-thumbnail">
                        </a>
                        <p><?= Html::encode($tariff->title) ?></p>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
<?php } ?>

<?php $this->registerJs("
    var lightbox = $('.g1 .item a').simpleLightbox({
        captionsData: 'alt',
    });
");
