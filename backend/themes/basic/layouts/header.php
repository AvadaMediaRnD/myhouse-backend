<?php
use yii\helpers\Html;
use common\models\UserAdmin;

/* @var $this \yii\web\View */
/* @var $content string */

$usersNew = \common\models\Config::getValue('usersNew');
$masterRequestsNew = \common\models\Config::getValue('masterRequestsNew');
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
        <div class="header-title">Панель администратора</div>
        <!-- Navbar Right Menu -->
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <?php if (Yii::$app->user->can(UserAdmin::PERMISSION_USER)) { ?>
                    <!-- Notifications -->
                    <li class="dropdown notifications-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-child fa-lg"></i>
                            <?php if ($usersNew) { ?>
                                <span class="label label-warning"><?= count($usersNew) ?></span>
                            <?php } ?>
                        </a>
                        <ul class="dropdown-menu">

                            <?php if ($usersNew) { ?>
                                <li class="header">Новых пользователей: <?= count($usersNew) ?></li>
                                <li>
                                    <!-- inner menu: contains the actual data -->
                                    <ul class="menu">
                                        <?php foreach ($usersNew as $userNew) { ?>
                                            <li>
                                                <a href="<?= Yii::$app->urlManager->createUrl(['/user/view', 'id' => $userNew->id]) ?>">
                                                    <?= $userNew->getFullname() ?> <!--<i class="fa fa-angle-right pull-right" aria-hidden="true"></i>-->
                                                </a>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </li>
                            <?php } else { ?>
                                <li class="header">Нет новых владельцев квартир</li>
                            <?php } ?>
                            <li class="footer"><a href="<?= Yii::$app->urlManager->createUrl(['/user/index']) ?>">Перейти в раздел пользователей</a></li>

                        </ul>
                    </li>
                <?php } ?>
                <?php if (false && Yii::$app->user->can(UserAdmin::PERMISSION_MASTER_REQUEST)) { ?>
                    <!-- Notifications -->
                    <li class="dropdown notifications-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-wrench fa-lg"></i>
                            <?php if ($masterRequestsNew) { ?>
                                <span class="label label-warning"><?= count($masterRequestsNew) ?></span>
                            <?php } ?>
                        </a>
                        <ul class="dropdown-menu">

                            <?php if ($masterRequestsNew) { ?>
                                <li class="header">Новых заявок мастера: <?= count($masterRequestsNew) ?></li>
                                <li>
                                    <!-- inner menu: contains the actual data -->
                                    <ul class="menu">
                                        <?php foreach ($masterRequestsNew as $masterRequestNew) { ?>
                                            <li>
                                                <a href="<?= Yii::$app->urlManager->createUrl(['/master-request/view', 'id' => $masterRequestNew->id]) ?>">
                                                    <?= mb_substr($masterRequestNew->description, 0, 60) . '...' ?> <!--<i class="fa fa-angle-right pull-right" aria-hidden="true"></i>-->
                                                </a>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </li>
                            <?php } else { ?>
                                <li class="header">Нет новых заявок мастера</li>
                            <?php } ?>
                            <li class="footer"><a href="<?= Yii::$app->urlManager->createUrl(['/master-request/index']) ?>">Перейти в раздел заявок вызова мастера</a></li>

                        </ul>
                    </li>
                <?php } ?>
                <!-- User Account -->
                <?php if (!Yii::$app->user->isGuest) { ?>
                    <li class="dropdown user user-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <!--<img src="<?= Yii::$app->urlManager->createUrl('/dist/img/user2-160x160.jpg') ?>" class="user-image" alt="<?= Yii::$app->user->identity->getFullname() ?>">-->
                            <?php 
                            $user = Yii::$app->user->identity;
                            $role = $user->role;
                            $roleClass = '';
                            ?>
                            <?php if ($role == UserAdmin::ROLE_ADMIN) { 
                                $roleClass = 'text-purple';
                            } elseif ($role == UserAdmin::ROLE_MANAGER) {
                                $roleClass = 'text-red';
                            } elseif ($role == UserAdmin::ROLE_ACCOUNTANT) {
                                $roleClass = 'text-green';
                            } elseif ($role == UserAdmin::ROLE_ELECTRICIAN) {
                                $roleClass = 'text-orange';
                            } elseif ($role == UserAdmin::ROLE_PLUMBER) {
                                $roleClass = 'text-orange';
                            } ?>
                            <i class="fa fa-user-circle <?= $roleClass ?>"></i>
                            <span class="hidden-xs"><?= $user->fullname ?></span>
                        </a>
                        <ul class="dropdown-menu">
                            <!-- User image -->
                            <li class="user-header">
                                <!--<img src="<?= Yii::$app->urlManager->createUrl('/dist/img/user2-160x160.jpg') ?>" class="img-circle" alt="<?= Yii::$app->user->identity->getFullname() ?>">-->
                                <span><i class="fa fa-user-circle <?= $roleClass ?> bg-gray-light img-circle"></i></span>
                                <p><?= $user->fullname ?> (<?= $user->getRoleLabel() ?>)</p>
                            </li>
                            <!-- Menu Footer-->
                            <li class="user-footer">
                                <div class="pull-left">
                                    <?= Html::a(
                                        'Профиль',
                                        ['/user-admin/update-my'],
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
                <?php } ?>
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