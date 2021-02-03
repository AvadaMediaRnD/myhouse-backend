<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use frontend\widgets\Alert;
use common\models\Website;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="favicon.png">

    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>

    <?php $this->head() ?>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
    <?php $this->beginBody() ?>
    
    <nav class="navbar navbar-default navbar-fixed-top">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#menuBtn" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="<?= Yii::$app->urlManager->createUrl(['/site/index']) ?>">
                    <img src="<?= Yii::$app->urlManager->createUrl('/logo.svg') ?>" alt="<?= Yii::$app->name ?>">
                </a>
            </div>

            <div class="collapse navbar-collapse" id="menuBtn">
                <ul class="nav navbar-nav navbar-right">
                    <li class="<?= Yii::$app->controller->action->id == 'index' ? 'active' : '' ?>"><a href="<?= Yii::$app->urlManager->createUrl(['/site/index']) ?>">Главная<span class="sr-only">(current)</span></a></li>
                    <li class="<?= Yii::$app->controller->action->id == 'about' ? 'active' : '' ?>"><a href="<?= Yii::$app->urlManager->createUrl(['/site/about']) ?>">О нас</a></li>
                    <li class="<?= Yii::$app->controller->action->id == 'services' ? 'active' : '' ?>"><a href="<?= Yii::$app->urlManager->createUrl(['/site/services']) ?>">Услуги</a></li>
                    <li class="<?= Yii::$app->controller->action->id == 'tariffs' ? 'active' : '' ?>"><a href="<?= Yii::$app->urlManager->createUrl(['/site/tariffs']) ?>">Тарифы</a></li>
                    <?php if ($urlSite = Html::decode(Website::getParamContent(Website::PARAM_CONTACT_URL_SITE))) { ?>
                        <li><a href="<?= $urlSite ?>">Условия приобретения</a></li>
                    <?php } ?>
                    <li class="<?= Yii::$app->controller->action->id == 'contact' ? 'active' : '' ?>"><a href="<?= Yii::$app->urlManager->createUrl(['/site/contact']) ?>">Контакты</a></li>
                    <li><a href="<?= Yii::$app->urlManagerCabinet->createAbsoluteUrl(['/site/login']) ?>"><i class="fas fa-sign-in-alt"></i> Вход</a></li>
                </ul>
            </div>
        </div>
    </nav>
    
    <?= $content ?>

    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-xs-12 text-center">
                    <p class="text-muted">Разработано <a href="https://avada-media.ua/">AVADA-MEDIA</a>. На базе системы управления <a href="https://avada-media.ua/moydom24/">"МойДом24"</a></p>
                </div>
            </div>
        </div>
    </footer>
    
    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
