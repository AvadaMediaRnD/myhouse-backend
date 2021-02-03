<?php

use common\models\Website;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\ContactForm */

$this->title = Html::decode(Website::getParamContent(Website::PARAM_CONTACT_META_TITLE));
$this->registerMetaTag(['name' => 'description', 'content' => Html::decode(Website::getParamContent(Website::PARAM_CONTACT_META_DESCRIPTION))]);
$this->registerMetaTag(['name' => 'keywords', 'content' => Html::decode(Website::getParamContent(Website::PARAM_CONTACT_META_KEYWORDS))]);
?>
<div class="breadcrumbs">
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <ol class="breadcrumb">
                    <li><a href="<?= Yii::$app->urlManager->createUrl(['/site/index']) ?>">Главная</a></li>
                    <li class="active">Контакты</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<?= Html::decode(Website::getParamContent(Website::PARAM_CONTACT_MAP_EMBED_CODE)) ?>

<div class="contacts">
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-sm-7 col-md-8">
                <div class="page-header">
                    <h1><?= Html::decode(Website::getParamContent(Website::PARAM_CONTACT_TITLE)) ?></h1>
                </div>
                <div class="box">
                    <?= Html::decode(Website::getParamContent(Website::PARAM_CONTACT_DESCRIPTION)) ?>
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
