<?php

return [
    'class' => 'yii\web\UrlManager',
    'baseUrl' => '/',
    'enablePrettyUrl' => true,
    'showScriptName' => false,
    'rules' => [
        '/' => 'site/index',
        '/about' => 'site/about',
        '/services' => 'site/services',
        '/contact' => 'site/contact',
        '/tariffs' => 'site/tariffs',
        '<controller:[\w-]+>/<id:[\d\-]+>' => '<controller>/view',
        '<controller:[\w-]+>/<action:[\w-]+>/<id:[\d\-]+>' => '<controller>/<action>',
        '<controller:[\w-]+>/<action:[\w-]+>' => '<controller>/<action>',
    ],
];
