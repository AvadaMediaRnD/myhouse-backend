<?php
use yii\helpers\Html;
use common\models\Message;

/* @var $this \yii\web\View */
/* @var $content string */

$messagesNew = \common\models\Config::getValue('messagesNew');
?>

<header class="main-header">

    <!-- Logo -->
    <a href="<?= Yii::$app->homeUrl ?>" class="logo">
        <span class="logo-mini">
            <img src="<?= Yii::$app->urlManagerFrontend->createUrl('/logo-mini.svg') ?>" class="img-responsive" alt="">
        </span>
        <span class="logo-lg">
            <img src="<?= Yii::$app->urlManagerFrontend->createUrl('/logo.svg') ?>" class="img-responsive" alt="">
        </span>
    </a>

    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>
        <!-- Header title -->
        <div class="header-title">Личный кабинет</div>
        <!-- Navbar Right Menu -->
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <!-- Notifications -->
                <li class="dropdown notifications-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-bell"></i>
                        <?php if ($messagesNew) { ?>
                            <span class="label label-warning"><?= count($messagesNew) ?></span>
                        <?php } ?>
                    </a>
                    <ul class="dropdown-menu">
                        
                        <?php if ($messagesNew) { ?>
                            <li class="header">Новых сообщений: <?= count($messagesNew) ?></li>
                            <li>
                                <!-- inner menu: contains the actual data -->
                                <ul class="menu">
                                    <?php foreach ($messagesNew as $messageNew) { ?>
                                        <li>
                                            <a href="<?= Yii::$app->urlManager->createUrl(['/message/view', 'id' => $messageNew->id]) ?>">
                                                <?php
                                                $iconClass = 'fa-warning text-yellow';
                                                if ($messageNew->type == Message::TYPE_HOUSE) {
                                                    $iconClass = 'fa-building';
                                                } elseif ($messageNew->type == Message::TYPE_INVOICE) {
                                                    $iconClass = 'fa-file-text-o';
                                                } elseif ($messageNew->type == Message::TYPE_PAY) {
                                                    $iconClass = 'fa-money text-green';
                                                } elseif ($messageNew->type == Message::TYPE_DEFAULT) {
                                                    // 
                                                }
                                                ?>
                                                <i class="fa <?= $iconClass ?>"></i>
                                                <?= $messageNew->name ?> <!--<i class="fa fa-angle-right pull-right" aria-hidden="true"></i>-->
                                            </a>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </li>
                        <?php } else { ?>
                            <li class="header">Нет новых сообщений</li>
                        <?php } ?>
                        <li class="footer"><a href="<?= Yii::$app->urlManager->createUrl(['/message/index']) ?>">Перейти в раздел сообщений</a></li>
                            
                    </ul>
                </li>
                <!-- User Account -->
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <img src="<?= Yii::$app->urlManagerFrontend->createAbsoluteUrl(['/site/glide', 'path' => Yii::$app->user->identity->profile->getAvatar(), 'w' => 25, 'h' => 25, 'fit' => 'crop']) ?>" class="user-image" alt="<?= Yii::$app->user->identity->getFullname() ?>">
                        <span class="hidden-xs"><?= Yii::$app->user->identity->fullname ?></span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header">
                            <img src="<?= Yii::$app->urlManagerFrontend->createAbsoluteUrl(['/site/glide', 'path' => Yii::$app->user->identity->profile->getAvatar(), 'w' => 160, 'h' => 160, 'fit' => 'crop']) ?>" class="img-circle" alt="<?= Yii::$app->user->identity->getFullname() ?>">

                            <p><?= Yii::$app->user->identity->fullname ?></p>
                        </li>
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-left">
                                <?= Html::a(
                                    'Профиль',
                                    ['/user/view'],
                                    ['class' => 'btn btn-default btn-flat']
                                ) ?>
                            </div>
                            <div class="pull-right">
                                <?= Html::a(
                                    'Выход',
                                    ['/site/logout'],
                                    ['data-method' => 'post', 'class' => 'btn btn-default btn-flat']
                                ) ?>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>

    </nav>
</header>


<?php if (false) { ?>
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
<?php } ?>