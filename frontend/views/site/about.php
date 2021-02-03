<?php

use common\models\Website;
use common\models\WebsiteAboutImage;
use common\models\WebsiteDocument;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $imagesMain WebsiteAboutImage[] */
/* @var $imagesAdd WebsiteAboutImage[] */
/* @var $documents WebsiteDocument[] */

$this->title = Html::decode(Website::getParamContent(Website::PARAM_ABOUT_META_TITLE));
$this->registerMetaTag(['name' => 'description', 'content' => Html::decode(Website::getParamContent(Website::PARAM_ABOUT_META_DESCRIPTION))]);
$this->registerMetaTag(['name' => 'keywords', 'content' => Html::decode(Website::getParamContent(Website::PARAM_ABOUT_META_KEYWORDS))]);
?>
<div class="breadcrumbs">
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <ol class="breadcrumb">
                    <li><a href="<?= Yii::$app->urlManager->createUrl(['/site/index']) ?>">Главная</a></li>
                    <li class="active">О нас</li>
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
                        <h1><?= Html::decode(Website::getParamContent(Website::PARAM_ABOUT_TITLE)) ?></h1>
                    </div>
                    <img src="<?= Yii::$app->urlManager->createUrl(['/site/glide', 'path' => Html::decode(Website::getParamContent(Website::PARAM_ABOUT_IMAGE)), 'w' => 250]) ?>" class="img-responsive imgFloatRight" alt="">
                    <?= Html::decode(Website::getParamContent(Website::PARAM_ABOUT_DESCRIPTION)) ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($imagesMain) { ?>
    <div class="gallery g1">
        <div class="container">
            <div class="row">
                <?php foreach ($imagesMain as $image) { ?>
                    <div class="col-xs-12 col-sm-6 col-md-3 item" data-src="<?= $image->getImagePath() ?>">
                        <a href="<?= $image->getImagePath() ?>" class="big">
                            <img src="<?= Yii::$app->urlManager->createUrl(['/site/glide', 'path' => $image->getImagePath(), 'w' => 720, 'h' => 250, 'fit' => 'crop']) ?>" alt="" class="img-responsive img-thumbnail">
                        </a>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
<?php } ?>

<div class="about">
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="box clearfix">
                    <div class="page-header">
                        <h3><?= Html::decode(Website::getParamContent(Website::PARAM_ABOUT_TITLE_2)) ?></h3>
                    </div>
                    <?= Html::decode(Website::getParamContent(Website::PARAM_ABOUT_DESCRIPTION_2)) ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($imagesAdd) { ?>
    <div class="gallery g2">
        <div class="container">
            <div class="row">
                <?php foreach ($imagesAdd as $image) { ?>
                    <div class="col-xs-12 col-sm-6 col-md-3 item" data-src="<?= $image->getImagePath() ?>">
                        <a href="<?= $image->getImagePath() ?>" class="big">
                            <img src="<?= Yii::$app->urlManager->createUrl(['/site/glide', 'path' => $image->getImagePath(), 'w' => 720, 'h' => 250, 'fit' => 'crop']) ?>" alt="" class="img-responsive img-thumbnail">
                        </a>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
<?php } ?>

<?php if ($documents) { ?>
    <div class="documents">
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="page-header">
                            <h3>Документы</h3>
                        </div>
                        <?php foreach ($documents as $document) { ?>
                            <div class="media">
                                <div class="media-left">
                                    <div class="media-object">
                                        <?php if (in_array($document->getFileExtension(), ['jpg', 'png'])) { ?>
                                            <img src="<?= Yii::$app->urlManagerFrontend->createAbsoluteUrl(['/site/glide', 'path' => $document->getImagePath(), 'w' => 32, 'h' => 42, 'fit' => 'crop']) ?>" alt="">
                                        <?php } elseif ($document->getFileExtension() == 'pdf') { ?>
                                            <i class="far fa-file-pdf fa-3x"></i>
                                        <?php } else { ?>
                                            <i class="far fa-file fa-3x"></i>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="media-body">
                                    <h4 class="media-heading"><?= $document->title ?: $document->getFileName() ?></h4>
                                    <a href="<?= Yii::$app->urlManager->createUrl($document->file) ?>" download><i class="fa fa-download" aria-hidden="true"></i> Скачать</a>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

<?php $this->registerJs("
    var lightbox = $('.g1 .item a').simpleLightbox({
        captionsData: 'alt',
    });
    
    var lightbox2 = $('.g2 .item a').simpleLightbox({
        captionsData: 'alt',
    });
");
