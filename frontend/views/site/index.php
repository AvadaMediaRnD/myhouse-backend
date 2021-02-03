<?php

use common\models\Website;
use common\models\WebsiteHomeSlide;
use common\models\WebsiteHomeFeature;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $slides WebsiteHomeSlide[] */
/* @var $features WebsiteHomeFeature[] */

$this->title = Html::decode(Website::getParamContent(Website::PARAM_HOME_META_TITLE));
$this->registerMetaTag(['name' => 'description', 'content' => Html::decode(Website::getParamContent(Website::PARAM_HOME_META_DESCRIPTION))]);
$this->registerMetaTag(['name' => 'keywords', 'content' => Html::decode(Website::getParamContent(Website::PARAM_HOME_META_KEYWORDS))]);

$isShowApps = Html::decode(Website::getParamContent(Website::PARAM_HOME_IS_SHOW_APPS));
?>
<?php if ($slides) { ?>
    <div class="slider">
        <?php foreach ($slides as $slide) { ?>
            <div>
                <div class="slideTitle hidden-xs"><?php // echo Html::decode($slide->title) ?><?php echo Html::decode(Website::getParamContent(Website::PARAM_HOME_TITLE)) ?></div>
                <img src="<?= Yii::$app->urlManager->createUrl(['/site/glide', 'path' => $slide->getImagePath(), 'w' => 1920, 'h' => 800, 'fit' => 'crop']) ?>" class="img-responsive" alt="">
            </div>
        <?php } ?>
    </div>
<?php } ?>

<div class="info">
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-sm-7 col-md-8">
                <div class="page-header">
                    <h1><?= Html::decode(Website::getParamContent(Website::PARAM_HOME_TITLE)) ?></h1>
                </div>
                <div class="box">
                    <?= Html::decode(Website::getParamContent(Website::PARAM_HOME_DESCRIPTION)) ?>
                    <div class="row">
                        <div class="col-xs-4">
                            <a href="<?= Yii::$app->urlManager->createUrl(['/site/about']) ?>" class="btn btn-primary">Подробнее</a>
                        </div>
                        <div class="col-xs-8">
                            <?php if ($isShowApps && Yii::$app->params['appUrlIos']) { ?>
                                <a href="<?= Yii::$app->params['appUrlIos'] ?>">
                                    <img src="/img/appstore.png" class="img-responsive imgStore" alt="">
                                </a>
                            <?php } ?>
                            <?php if ($isShowApps && Yii::$app->params['appUrlAndroid']) { ?>
                                <a href="<?= Yii::$app->params['appUrlAndroid'] ?>">
                                    <img src="/img/googleplay.png" class="img-responsive imgStore" alt="">
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-5 col-md-4">
                <div class="page-header">
                    <h3>Контакты</h3>
                </div>
                <div class="box">
                    <?php if ($fullname = Html::decode(Website::getParamContent(Website::PARAM_CONTACT_FULLNAME))) { ?>
                        <p><i class="fas fa-user-circle"></i> <?= $fullname ?></p>
                    <?php } ?>
                    <?php if ($location = Html::decode(Website::getParamContent(Website::PARAM_CONTACT_LOCATION))) { ?>
                        <p><i class="fas fa-compass"></i> <?= $location ?></p>
                    <?php } ?>
                    <?php if ($address = Html::decode(Website::getParamContent(Website::PARAM_CONTACT_ADDRESS))) { ?>
                        <p><i class="fas fa-map-marker"></i> <?= $address ?></p>
                    <?php } ?>
                    <?php if ($phone = Html::decode(Website::getParamContent(Website::PARAM_CONTACT_PHONE))) { ?>
                        <p><i class="fas fa-phone"></i> <a href="tel:<?= str_replace(['(', ')', '-', ' '], '', strip_tags($phone)) ?>"><?= $phone ?></a></p>
                    <?php } ?>
                    <?php if ($email = Html::decode(Website::getParamContent(Website::PARAM_CONTACT_EMAIL))) { ?>
                        <p><i class="fas fa-envelope"></i> <a href="mailto:<?= $email ?>"><?= $email ?></a></p>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php if ($features) { ?>
    <div class="advantages">
        <div class="container">
            <div class="row row-eq-height">
                <div class="col-xs-12">
                    <div class="page-header">
                        <h3>Рядом с нами</h3>
                    </div>
                </div>
                <?php foreach ($features as $feature) { ?>
                    <div class="col-xs-12 col-sm-6 col-md-4 advantage-item">
                        <div class="thumbnail">
                            <img src="<?= Yii::$app->urlManager->createUrl(['/site/glide', 'path' => $feature->getImagePath(), 'w' => 1000, 'h' => 600, 'fit' => 'crop']) ?>" class="img-responsive" alt="">
                            <div class="caption">
                                <h3><?= Html::decode($feature->title) ?></h3>
                                <p><?= Html::decode($feature->description) ?></p>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
<?php } ?>
