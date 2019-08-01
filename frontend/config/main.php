<?php

$params = array_merge(
        require(__DIR__ . '/../../common/config/params.php'), require(__DIR__ . '/../../common/config/params-local.php'), require(__DIR__ . '/params.php'), require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-frontend',
    'language' => 'en-US',
    'timeZone' => 'Asia/Bangkok',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'frontend\components\AppComponent', 'languagepicker', 'admin'],
    'controllerNamespace' => 'frontend\controllers',
    'modules' => [
        'admin' => [
            'class' => 'mdm\admin\Module',
            //'layout' => 'left-menu', // defaults to null, using the application's layou
            'layout' => '@app/views/layouts/main.php',
            'controllerMap' => [
                'assignment' => [
                    'class' => 'mdm\admin\controllers\AssignmentController',
                //'userClassName' => 'common\modules\user\models\User', // fully qualified class name of your User model
                // Usually you don't need to specify it explicitly, since the module will detect it automatically
                //'idField' => 'user_id',        // id field of your User model that corresponds to Yii::$app->user->id
                //'usernameField' => 'username', // username field of your User model
                //'searchClass' => 'app\models\UserSearch'    // fully qualified class name of your User model for searching
                ]
            ],
        ],
        'user' => [
            'class' => 'dektrium\user\Module',
            'enableUnconfirmedLogin' => true,
            'confirmWithin' => 21600,
            'cost' => 12,
            'admins' => ['admin'], //'administrator'
            'adminPermission' => 'administrator',
            'modelMap' => [
                'User' => 'common\modules\user\models\User',
                'Profile' => 'common\modules\user\models\Profile',
                'RegistrationForm' => 'common\modules\user\models\RegistrationForm',
            ],
            'controllerMap' => [
                'admin' => 'common\modules\user\controllers\AdminController',
                'settings' => 'common\modules\user\controllers\SettingsController',
                'registration' => 'common\modules\user\controllers\RegistrationController',
                'security'=>'common\modules\user\controllers\SecurityController',
            ],
        ],
        'api' => [
            'class' => 'frontend\modules\api\v1\Module',
        ],
        'tctr' => [
            'class' => 'frontend\modules\tctr\Module',
        ],
        'report' => [
            'class' => 'app\modules\report\Module',
        ],
        'ezforms2' => [
                'class' => 'backend\modules\ezforms2\Module',
        ],
        'purify' => [
            'class' => 'frontend\modules\purify\Module',
        ],
        'gismap' => [
            'class' => 'frontend\modules\gismap\Module',
        ],
        'gridview' => [
            'class' => '\kartik\grid\Module',
        ],
    ],
    'components' => [
        'urlManagerBackend' => [
                'class' => 'yii\web\urlManager',
                'baseUrl' => '@backendUrl',
                'enablePrettyUrl' => true,
                'showScriptName' => false,
        ],//Yii::$app->urlManagerBackend->createUrl('ezmodules');
        
        'user' => [
            'identityClass' => 'dektrium\user\models\User',
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
        
        'i18n' => [
            'translations' => [
                '*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@frontend/messages', // if advanced application, set @frontend/messages
                    'sourceLanguage' => 'en-US',
                    'fileMap' => [
                        'app' => 'app.php',
                    ],
                ],
            ],
        ],
        'languagepicker' => [
            'class' => 'lajax\languagepicker\Component',
            'languages' => ['en-US', 'th'], // List of available languages (icons only)
            'cookieName' => 'language', // Name of the cookie.
            'expireDays' => 64, // The expiration time of the cookie is 64 days.
            'callback' => function() {
                if (!\Yii::$app->user->isGuest) {
                    //		    $user = \Yii::$app->user->identity;
                    //		    $user->language = \Yii::$app->language;
                    //		    $user->save();
                }
            }
        ],
        'as access' => [
            'class' => 'mdm\admin\components\AccessControl',
            'allowActions' => [
                'site/*',
                'user/*',
                //'admin/*',
            //'some-controller/some-action',
            // The actions listed here will be allowed to everyone including guests.
            // So, 'admin/*' should not appear here in the production, of course.
            // But in the earlier stages of your development, you may probably want to
            // add a lot of actions here until you finally completed setting up rbac,
            // otherwise you may not even take a first step.
            ]
        ],       
    ],
    'params' => $params,
];
