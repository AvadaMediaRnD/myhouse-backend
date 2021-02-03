<?php
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $content string */

$usersNew = \common\models\Config::getValue('usersNew');
?>

<header class="main-header">

    <?= Html::a('<span class="logo-mini"><img src="'.Yii::$app->homeUrl.'logo.svg"></span><span class=""><img src="'.Yii::$app->homeUrl.'logo.svg"></span>', Yii::$app->homeUrl, ['class' => 'logo']) ?>

    <nav class="navbar navbar-static-top" role="navigation">

        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>

        <div class="navbar-custom-menu">

            <ul class="nav navbar-nav">

                <!-- Messages: style can be found in dropdown.less-->

                <li class="dropdown notifications-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-user-o"></i>
                        <?php if ($usersNew) { ?>
                            <span class="label label-success"><?= count($usersNew) ?></span>
                        <?php } ?>
                    </a>
                    <ul class="dropdown-menu">
                        <?php if ($usersNew) { ?>
                            <li class="header">Новых владельцев квартир: <?= count($usersNew) ?></li>
                            <li>
                                <!-- inner menu: contains the actual data -->
                                <ul class="menu">
                                    <?php foreach ($usersNew as $userNew) { ?>
                                        <li>
                                            <a href="<?= Yii::$app->urlManager->createUrl(['/user/update', 'id' => $userNew->id]) ?>">
                                                <i class="fa fa-user-plus"></i> <?= $userNew->getFullname() ?>
                                            </a>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </li>
                        <?php } else { ?>
                            <li class="header">Нет новых владельцев квартир</li>
                        <?php } ?>
                        <li class="footer"><a href="<?= Yii::$app->urlManager->createUrl(['/user/index']) ?>">Управление владельцами квартир</a></li>
                    </ul>
                </li>

                <li class="dropdown notifications-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <span><?= Yii::$app->user->identity->getFullname() ?></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <!-- inner menu: contains the actual data -->
                            <ul class="menu">
                                <li>
                                    <?= Html::a(
                                        '<i class="fa fa-user"></i> ' . Yii::t('app', 'Профиль'),
                                        ['/user-admin/update-my']
                                    ) ?>
                                </li>
                                <li>
                                    <?= Html::a(
                                        '<i class="fa fa-sign-out"></i> ' . Yii::t('app', 'Выйти'),
                                        ['/site/logout'],
                                        ['data-method' => 'post']
                                    ) ?>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>

            </ul>
        </div>
    </nav>
</header>
