<?php

$params = array_merge(
        require(__DIR__ . '/../../common/config/params.php'), require(__DIR__ . '/../../common/config/params-local.php'), require(__DIR__ . '/params.php'), require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'language' => 'en-US',
    'timeZone' => 'Asia/Bangkok',
    'bootstrap' => ['log', 'backend\components\AppComponent', 'languagepicker', 'admin'],
    'modules' => [
        'log' => [
            'class' => 'backend\modules\log\Module',
        ],
        'reports' => [
            'class' => 'backend\modules\reports\Module',
        ],
        'gridview' => [
            'class' => '\kartik\grid\Module',
        ],
        'queue' => [
            'class' => 'backend\modules\queue\Module',
        ],
        'manage_modules' => [
            'class' => 'backend\modules\manage_modules\Module',
        ],
        'update_project' => [
            'class' => 'backend\modules\update_project\Module',
        ],
        'manage_user' => [
            'class' => 'backend\modules\manage_user\Module',
        ],
        'webboard' => [
            'class' => 'backend\modules\webboard\Module',
        ],
        'manageproject' => [
            'class' => 'backend\modules\manageproject\Module',
        ],
        'permission' => [
            'class' => 'backend\modules\permission\Module',
        ],
        'line' => [
            'class' => 'backend\modules\line\Module',
        ],
        'sae' => [
            'class' => 'backend\modules\sae\Module',
        ],
        'topic' => [//chanpan
            'class' => 'backend\modules\topic\Module',
        ],
        'workshop' => [//Kawin Sirikhanarat
            'class' => 'backend\modules\workshop\Module',
        ],
         'random' => [
            'class' => 'backend\modules\random\Module',
        ],
        'ce' => [
            'class' => 'backend\modules\ce\Module',
        ],
        'tmf' => [
            'class' => 'backend\modules\tmf\Module',
        ],
        'notify' => [
            'class' => 'backend\modules\notify\Module',
        ],
        'core' => [
            'class' => 'backend\modules\core\Module',
        ],
        'ezforms2' => [
            'class' => 'backend\modules\ezforms2\Module',
        ],
        'ezbuilder' => [
            'class' => 'backend\modules\ezbuilder\Modules',
        ],
        'ezmodules' => [
            'class' => 'backend\modules\ezmodules\Module',
        ],
        'treemanager' => [
            'class' => '\kartik\tree\Module',
        // other module settings, refer detailed documentation
        ],
        'eztest' => [
            'class' => 'backend\modules\eztest\Module',
        ],
        'patient' => [
            'class' => 'backend\modules\patient\Module',
        ],
        'cpoe' => [
            'class' => 'backend\modules\cpoe\Module',
        ],
        'report' => [
            'class' => 'backend\modules\report\Module',
        ],
        'ezwidget' => [
            'class' => 'backend\modules\ezwidget\Module',
        ],
        'gantt' => [
            'class' => 'backend\modules\gantt\Module',
        ],
        'subjects' => [
            'class' => 'backend\modules\subjects\Module',
        ],
        'study_manage' => [
            'class' => 'backend\modules\study_manage\Module',
        ],
        'myworkbench' => [
            'class' => 'backend\modules\myworkbench\Module',
        ],
        'graphconfig' => [
            'class' => 'backend\modules\graphconfig\Module',
        ],
        'tctr' => [
            'class' => 'backend\modules\tctr\Module',
        ],
        'proposal' => [
            'class' => 'backend\modules\proposal\Module',
        ],
        'thaihis' => [
            'class' => 'backend\modules\thaihis\Module',
        ],
        'pis' => [
            'class' => 'backend\modules\pis\Module',
        ],
        'usfinding' => [
            'class' => 'backend\modules\usfinding\Module',
        ],
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
            'enableConfirmation' => TRUE,
            'enableUnconfirmedLogin' => FALSE,
            'confirmWithin' => 21600,
            'cost' => 12,
            'admins' => ['admin'], //'administrator'
            'adminPermission' => 'administrator',
            'modelMap' => [
                'User' => 'common\modules\user\models\User',
                'Profile' => 'common\modules\user\models\Profile',
                'RegistrationForm' => 'common\modules\user\models\RegistrationForm',
                'RecoveryForm' =>'common\modules\user\models\RecoveryForm'
            ],
            'controllerMap' => [
                'admin' => 'common\modules\user\controllers\AdminController',
                'site-admin' => 'common\modules\user\controllers\SiteAdminController',
                'settings' => 'common\modules\user\controllers\SettingsController',
                'registration' => 'common\modules\user\controllers\RegistrationController',
                'security'=>'common\modules\user\controllers\SecurityController',
                'recovery'=>'common\modules\user\controllers\RecoveryController'
            ],
        ],
        'purify' => [
            'class' => 'backend\modules\purify\Module',
        ],
        'api' => [
            'class' => 'backend\modules\api\v1\Module',
        ],
        'linebot' => [
            'class' => 'backend\modules\linebot\Module',
        ],
        'customer' => [
            'class' => 'backend\modules\customer\Module',
        ],
    ],
    'components' => [
        'urlManagerFrontend' => [
            'class' => 'yii\web\urlManager',
            'baseUrl' => '@frontendUrl',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
        ], //Yii::$app->urlManagerFrontend->createUrl('ezmodules');
        'user' => [
            'identityClass' => 'dektrium\user\models\User',
            'enableAutoLogin' => true,
            //'authTimeout' => 10,
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
                    'basePath' => '@backend/messages', // if advanced application, set @frontend/messages
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
//        'authClientCollection' => [
//            'class' => 'yii\authclient\Collection',
//            'clients' => [
//                'google' => [
//                    'class' => 'dektrium\user\clients\Google',
//                    'clientId' => 'google_client_id',
//                    'clientSecret' => 'google_client_secret',
//                ],
//                'facebook' => [
//                    'class' => 'dektrium\user\clients\Facebook',
//                    'clientId' => 'facebook_client_id',
//                    'clientSecret' => 'facebook_client_secret',
//                ],
//                'thaicarecloud' => [
//                    'class' => 'appxq\sdii\utils\Thaicarecloud',
//                    'clientId' => 'testclient',
//                    'clientSecret' => 'testpass',
//                ],
//                // etc.
//            ],
//        ]
    ],
    'as access' => [
        'class' => 'mdm\admin\components\AccessControl',
        'allowActions' => [
            'site/*',
            'api/*',
            'access-module/access-denied',
            'access-module/test-email',
            //'topic/*',
            //'tctr/*',
            //'random/*',
            //'admin/*',
            //'admin/*',
            //'user/*',
            //'linebot/*',
            'ezforms2/ezform/form-oauthe',
            'ezforms2/ezform-data/index',
            'ezforms2/ezform-data/commit',
            'ezforms2/audio/*',
            'ezforms2/drawing/*',
            'ezforms2/fileinput/*',
            'ezforms2/province/*',
            'ezforms2/select2/hospital',
            'ezforms2/select2/snomed',
            'ezforms2/select2/icd10',
            'ezforms2/select2/icd9',
            'ezforms2/select2/find-component',
            'ezforms2/select2/check-comp',
            'ezforms2/target/*',
            'ezforms2/text-editor/*',
            'ezforms2/select-site-single/get-site',
            'ezforms2/select-site-single/init-site',
            'ezforms2/select-department/get-department',
            'ezforms2/select-department/init-department',
            //'user/registration/*',
            //'tctr/default/index',
            'check-login/index',
//            'manageproject/*',
            'manageproject/step/check-data',
            'manageproject/step/get-status',
            'manageproject/send-mail/index',
            'social-media/*', 
            'purify/*',
            '/manage_user/user/access-invite',
            '/manage_user/user/save-user-project',
            'manage_user/user/*',
//            'notify/*',
//            'debug/*',

        //'some-controller/some-action',
        // The actions listed here will be allowed to everyone including guests.
        // So, 'admin/*' should not appear here in the production, of course.
        // But in the earlier stages of your development, you may probably want to
        // add a lot of actions here until you finally completed setting up rbac,
        // otherwise you may not even take a first step.
        ]
    ],
    'params' => $params,
];
