<?php

use common\models\UserAdmin;
?>
<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
    <!-- sidebar -->
    <section class="sidebar">

        <?=
        dmstr\widgets\Menu::widget(
                [
                    'options' => ['class' => 'sidebar-menu', 'data-widget' => 'tree'],
                    'items' => [
                        ['label' => Yii::t('app', 'Статистика'), 'icon' => 'line-chart', 'url' => ['/site/index'], 'visible' => Yii::$app->user->can(UserAdmin::PERMISSION_SITE)],
                        ['label' => Yii::t('app', 'Касса'), 'icon' => 'dollar', 'url' => ['/account-transaction/index'], 'active' => Yii::$app->controller->id == 'account-transaction', 'visible' => Yii::$app->user->can(UserAdmin::PERMISSION_ACCOUNT_TRANSACTION)],
                        ['label' => Yii::t('app', 'Квитанции на оплату'), 'icon' => 'files-o', 'url' => ['/invoice/index'], 'active' => Yii::$app->controller->id == 'invoice', 'visible' => Yii::$app->user->can(UserAdmin::PERMISSION_INVOICE)],
                        ['label' => Yii::t('app', 'Лицевые счета'), 'icon' => 'credit-card', 'url' => ['/account/index'], 'active' => Yii::$app->controller->id == 'account', 'visible' => Yii::$app->user->can(UserAdmin::PERMISSION_ACCOUNT)],
                        ['label' => Yii::t('app', 'Квартиры'), 'icon' => 'key', 'url' => ['/flat/index'], 'active' => Yii::$app->controller->id == 'flat', 'visible' => Yii::$app->user->can(UserAdmin::PERMISSION_FLAT)],
                        ['label' => Yii::t('app', 'Владельцы квартир'), 'icon' => 'users', 'url' => ['/user/index'], 'active' => Yii::$app->controller->id == 'user', 'visible' => Yii::$app->user->can(UserAdmin::PERMISSION_USER)],
                        ['label' => Yii::t('app', 'Дома'), 'icon' => 'building', 'url' => ['/house/index'], 'active' => Yii::$app->controller->id == 'house', 'visible' => Yii::$app->user->can(UserAdmin::PERMISSION_HOUSE)],
                        ['label' => Yii::t('app', 'Сообщения'), 'icon' => 'envelope-o', 'url' => ['/message/index'], 'active' => Yii::$app->controller->id == 'message', 'visible' => Yii::$app->user->can(UserAdmin::PERMISSION_MESSAGE)],
                        ['label' => Yii::t('app', 'Заявки вызова мастера'), 'icon' => 'wrench', 'url' => ['/master-request/index'], 'active' => Yii::$app->controller->id == 'master-request', 'visible' => Yii::$app->user->can(UserAdmin::PERMISSION_MASTER_REQUEST)],
                        ['label' => Yii::t('app', 'Показания счетчиков'), 'icon' => 'dashboard', 'url' => ['/counter-data/counters'], 'active' => Yii::$app->controller->id == 'counter-data', 'visible' => Yii::$app->user->can(UserAdmin::PERMISSION_COUNTER_DATA)],
//                    ['label' => Yii::t('app', 'Показания счетчиков'), 'icon' => 'dashboard', 'url' => ['/counter-data/index'], 'active' => Yii::$app->controller->id == 'counter-data', 'visible' => Yii::$app->user->can(UserAdmin::PERMISSION_COUNTER_DATA)],
                        [
                            'label' => Yii::t('app', 'Импорт из 1С'),
                            'icon' => 'files-o',
                            'url' => '#!',
                            'active' => Yii::$app->controller->id == 'import1c',
                            'items' => [
                                ['label' => 'Импорт счетов', 'icon' => 'circle-o', 'url' => ['/import1c/index'], 'active' => (Yii::$app->controller->id == 'import1c' && Yii::$app->controller->action->id == 'index')],
                                ['label' => 'Импорт оплат', 'icon' => 'circle-o', 'url' => ['/import1c/payments'], 'active' => (Yii::$app->controller->id == 'import1c' && Yii::$app->controller->action->id == 'payments')],
                            ],
                            'visible' => Yii::$app->user->can(UserAdmin::PERMISSION_COUNTER_DATA),
                        ],
                        [
                            'label' => Yii::t('app', 'Управление сайтом'),
                            'icon' => 'desktop',
                            'url' => '#!',
                            'active' => Yii::$app->controller->id == 'website',
                            'items' => [
                                ['label' => 'Главная страница', 'icon' => 'circle-o', 'url' => ['/website/home'], 'active' => (Yii::$app->controller->id == 'website' && Yii::$app->controller->action->id == 'home')],
                                ['label' => 'О нас', 'icon' => 'circle-o', 'url' => ['/website/about'], 'active' => (Yii::$app->controller->id == 'website' && Yii::$app->controller->action->id == 'about')],
                                ['label' => 'Услуги', 'icon' => 'circle-o', 'url' => ['/website/services'], 'active' => (Yii::$app->controller->id == 'website' && Yii::$app->controller->action->id == 'services')],
                                ['label' => 'Тарифы', 'icon' => 'circle-o', 'url' => ['/website/tariffs'], 'active' => (Yii::$app->controller->id == 'website' && Yii::$app->controller->action->id == 'tariffs')],
                                ['label' => 'Контакты', 'icon' => 'circle-o', 'url' => ['/website/contact'], 'active' => (Yii::$app->controller->id == 'website' && Yii::$app->controller->action->id == 'contact')],
                            ],
                            'visible' => Yii::$app->user->can(UserAdmin::PERMISSION_WEBSITE),
                        ],
                        [
                            'label' => Yii::t('app', 'Настройки системы'),
                            'icon' => 'cogs',
                            'url' => '#!',
                            'active' => in_array(Yii::$app->controller->id, ['service', 'tariff', 'pay-company', 'transaction-purpose']) || (Yii::$app->controller->id == 'user-admin' && (in_array(Yii::$app->controller->action->id, ['index', 'view', 'create', 'update', 'role']))),
                            'items' => [
                                ['label' => 'Услуги', 'icon' => 'briefcase', 'url' => ['/service/index'], 'active' => (Yii::$app->controller->id == 'service'), 'visible' => Yii::$app->user->can(UserAdmin::PERMISSION_SERVICE)],
                                ['label' => 'Тарифы', 'icon' => 'money', 'url' => ['/tariff/index'], 'active' => (Yii::$app->controller->id == 'tariff'), 'visible' => Yii::$app->user->can(UserAdmin::PERMISSION_TARIFF)],
                                ['label' => 'Роли', 'icon' => 'user', 'url' => ['/user-admin/role'], 'active' => (Yii::$app->controller->id == 'user-admin' && Yii::$app->controller->action->id == 'role'), 'visible' => Yii::$app->user->can(UserAdmin::PERMISSION_ROLE)],
                                ['label' => 'Пользователи', 'icon' => 'user-plus', 'url' => ['/user-admin/index'], 'active' => (Yii::$app->controller->id == 'user-admin' && (in_array(Yii::$app->controller->action->id, ['index', 'view', 'create', 'update']))), 'visible' => Yii::$app->user->can(UserAdmin::PERMISSION_USER_ADMIN)],
                                ['label' => 'Платежные реквизиты', 'icon' => 'credit-card', 'url' => ['/pay-company/index'], 'active' => (Yii::$app->controller->id == 'pay-company' && Yii::$app->controller->action->id == 'index'), 'visible' => Yii::$app->user->can(UserAdmin::PERMISSION_PAY_COMPANY)],
                                ['label' => 'Статьи платежей', 'icon' => 'list-ol', 'url' => ['/transaction-purpose/index'], 'active' => (Yii::$app->controller->id == 'transaction-purpose'), 'visible' => Yii::$app->user->can(UserAdmin::PERMISSION_ACCOUNT_TRANSACTION)],
                            ],
                            'visible' => Yii::$app->user->can(UserAdmin::PERMISSION_SERVICE) || Yii::$app->user->can(UserAdmin::PERMISSION_TARIFF) || Yii::$app->user->can(UserAdmin::PERMISSION_ROLE) || Yii::$app->user->can(UserAdmin::PERMISSION_USER_ADMIN) || Yii::$app->user->can(UserAdmin::PERMISSION_PAY_COMPANY),
                        ],
                    ],
                ]
        )
        ?>

    </section>
    <!-- /.sidebar -->
</aside>

