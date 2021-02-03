<?php

return [
    'name' => 'Demo CRM 24',
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
        ],
        'i18n' => [
            'translations' => [
                '*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    //'basePath' => '@app/messages',
                    //'sourceLanguage' => 'ru-RU',
                    'fileMap' => [
                        'app'       => 'app.php',
                        'error' => 'error.php',
                        'model' => 'model.php',
                    ],
                ],
            ],
        ],
        'glide' => [
            'class' => 'trntv\glide\components\Glide',
            'sourcePath' => '@frontend/web',
            'cachePath' => '@frontend/web/upload/cache',
            'signKey' => false, // 'surprisemotherfucker',
        ],
        'authManager' => [
            'class' => 'yii\rbac\PhpManager',
            'defaultRoles' => ['guest'],
            'itemFile' => '@console/rbac/items.php',
            'assignmentFile' => '@console/rbac/assignments.php',
            'ruleFile' => '@console/rbac/rules.php',
        ],
        'oneC' => [
            'class' => 'common\components\OneCComponent',
            'fileData' => '1c/export-example1.xml',
            // 'fileData' => '1c/export-example2.xml',
        ],
    ],
    'language' => 'ru-RU',
];
