<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-cabinet',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'cabinet\controllers',
    'bootstrap' => ['log'],
    'modules' => [],
    'components' => [
        'assetManager' => [
            'bundles' => [
                'dmstr\web\AdminLteAsset' => [
                    'skin' => 'skin-black-light',
                ],
            ],
        ],
        'request' => [
            'baseUrl' => '/cabinet',
            'csrfParam' => '_csrf-cabinet',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => require __DIR__ . '/../../cabinet/config/url-manager.php',
        'urlManagerFrontend' => require __DIR__ . '/../../frontend/config/url-manager.php',
        'urlManagerBackend' => require __DIR__ . '/../../backend/config/url-manager.php',
        'urlManagerCabinet' => require __DIR__ . '/../../cabinet/config/url-manager.php',
    ],
    'language' => 'ru-RU',
    'params' => $params,
];
