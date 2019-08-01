<?php

return [
    'timeZone' => 'Asia/Bangkok',
    'language' => 'en-US',
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'aliases' => [
        '@appxq/sdii' => '@common/lib/yii2-sdii',
        '@appxq/admin' => '@common/lib/yii2-admin',
        '@dee/angular' => '@common/lib/yii2-angular',
        '@dms/aomruk' => '@common/lib/yii2-aomruk',
        '@dms/joke' => '@common/lib/yii2-joke',
        '@cpn/chanpan' => '@common/lib/yii2-chanpan',
    ],
    'bootstrap' => [
        'queue', // The component registers own console commands
    ],
    'modules' => [],
    'components' => [
        'queue' => [
            'class' => \yii\queue\db\Queue::class,
            'as log' => \yii\queue\LogBehavior::class,
            'db' => 'db', // DB connection component or its config 
            'tableName' => '{{%queue}}', // Table name
            'channel' => 'default', // Queue channel key
            'mutex' => \yii\mutex\MysqlMutex::class, // Mutex that used to sync queries
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'urlManager' => [
            'class' => 'yii\web\UrlManager',
            'showScriptName' => false, // Disable index.php
            'enablePrettyUrl' => true, // Disable r= routes
            'rules' => [
                '' => 'site/index',
                'access-denied' => 'access-module/access-denied',
                //'site/config'=>'site/index',
                '<controller:\w+>/<id:\d+>' => '<controller>',
                '<controller:\w+>/<action:\w+>/<*:*>' => '<controller>/<action>/<*>',
                '<module:\w+>/<controller:\w+>/<id:\d+>' => '<module>/<controller>',
                '<module:\w+>/<controller:\w+>/<action:\w+>/<*:*>' => '<module>/<controller>/<action>/<*>',
            ],
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            'defaultRoles' => ['guest', 'user'],
            'cache' => 'yii\caching\FileCache'
        ],
        'view' => [
            'theme' => [
                'pathMap' => [
                    '@backend/views' => '@backend/themes/admin/views',
                    '@frontend/views' => '@frontend/themes/standard/views',
                    '@dektrium/user/views' => '@common/modules/user/views',
                ],
            ],
        ],
        'glide' => require(__DIR__ . '/../../storage/config/_glide.php'),
        'fileStorage' => [
            'class' => '\trntv\filekit\Storage',
            'baseUrl' => '@storageUrl/source',
            'filesystem' => [
                'class' => 'common\components\filesystem\LocalFlysystemBuilder',
                'path' => '@storage/web/source'
            ],
            'as log' => [
                'class' => 'backend\modules\core\behaviors\FileStorageLogBehavior',
                'component' => 'fileStorage'
            ]
        ],
        'moduleFileStorage' => [
            'class' => '\trntv\filekit\Storage',
            'baseUrl' => '@storageUrl/module',
            'filesystem' => [
                'class' => 'common\components\filesystem\LocalFlysystemBuilder',
                'path' => '@storage/web/module'
            ],
            'as log' => [
                'class' => 'backend\modules\core\behaviors\FileStorageLogBehavior',
                'component' => 'moduleFileStorage'
            ]
        ],
        'inputFileStorage' => [
            'class' => '\trntv\filekit\Storage',
            'baseUrl' => '@storageUrl/input',
            'filesystem' => [
                'class' => 'common\components\filesystem\LocalFlysystemBuilder',
                'path' => '@storage/web/input'
            ],
            'as log' => [
                'class' => 'backend\modules\core\behaviors\FileStorageLogBehavior',
                'component' => 'inputFileStorage'
            ]
        ],
    ],
];
