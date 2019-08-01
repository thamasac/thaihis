<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-storage',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'storage\controllers',
    'defaultRoute' => 'glide/index',
    'controllerMap' => [
        'glide' => '\trntv\glide\controllers\GlideController'
    ],
    'components' => [
//        'request' => [
//            'csrfParam' => '_csrf-storage',
//        ],
         'urlManager'=>require(__DIR__.'/_urlManager.php'),
         'glide' => require(__DIR__.'/_glide.php'),
    ],
    'params' => $params,
];
