<?php

return [
    'class' => 'yii\web\UrlManager',
    'baseUrl' => '/cabinet',
    'enablePrettyUrl' => true,
    'showScriptName' => false,
    'rules' => [
        '/' => 'site/index',
        '<controller:[\w-]+>/<id:[\d\-]+>' => '<controller>/view',
        '<controller:[\w-]+>/<action:[\w-]+>/<id:[\d\-]+>' => '<controller>/<action>',
        '<controller:[\w-]+>/<action:[\w-]+>' => '<controller>/<action>',
    ],
];
