<aside class="main-sidebar">

    <section class="sidebar">

        <?= dmstr\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu'],
                'items' => [
                    ['label' => Yii::t('app', 'Статистика'), 'icon' => 'dashboard', 'url' => ['/site/index']],
                    ['label' => Yii::t('app', 'Владельцы квартир'), 'icon' => 'users', 'url' => ['/user/index'], 'active' => Yii::$app->controller->id == 'user'],
                    ['label' => Yii::t('app', 'Жилые комплексы'), 'icon' => 'home', 'url' => ['/house/index'], 'active' => Yii::$app->controller->id == 'house'],
                    ['label' => Yii::t('app', 'Квартиры'), 'icon' => 'th-large', 'url' => ['/flat/index'], 'active' => Yii::$app->controller->id == 'flat'],
                    ['label' => Yii::t('app', 'Сообщения'), 'icon' => 'envelope', 'url' => ['/message/index'], 'active' => Yii::$app->controller->id == 'message'],
                    ['label' => Yii::t('app', 'Заявки вызова мастера'), 'icon' => 'briefcase', 'url' => ['/master-request/index'], 'active' => Yii::$app->controller->id == 'master-request'],
                    ['label' => Yii::t('app', 'Квитанции'), 'icon' => 'file', 'url' => ['/invoice/index'], 'active' => Yii::$app->controller->id == 'invoice'],
                    ['label' => Yii::t('app', 'Настройки'), 'icon' => 'cogs', 'url' => ['/config/index'], 'active' => Yii::$app->controller->id == 'config'],

//                    ['label' => 'Menu Yii2', 'options' => ['class' => 'header']],
//                    ['label' => 'Gii', 'icon' => 'file-code-o', 'url' => ['/gii']],
//                    ['label' => 'Debug', 'icon' => 'dashboard', 'url' => ['/debug']],
//                    ['label' => 'Login', 'url' => ['site/login'], 'visible' => Yii::$app->user->isGuest],
//                    [
//                        'label' => 'Same tools',
//                        'icon' => 'share',
//                        'url' => '#',
//                        'items' => [
//                            ['label' => 'Gii', 'icon' => 'file-code-o', 'url' => ['/gii'],],
//                            ['label' => 'Debug', 'icon' => 'dashboard', 'url' => ['/debug'],],
//                            [
//                                'label' => 'Level One',
//                                'icon' => 'circle-o',
//                                'url' => '#',
//                                'items' => [
//                                    ['label' => 'Level Two', 'icon' => 'circle-o', 'url' => '#',],
//                                    [
//                                        'label' => 'Level Two',
//                                        'icon' => 'circle-o',
//                                        'url' => '#',
//                                        'items' => [
//                                            ['label' => 'Level Three', 'icon' => 'circle-o', 'url' => '#',],
//                                            ['label' => 'Level Three', 'icon' => 'circle-o', 'url' => '#',],
//                                        ],
//                                    ],
//                                ],
//                            ],
//                        ],
//                    ],
                ],
            ]
        ) ?>

    </section>

</aside>
