<?php
    
    $moduleID = '';
    $controllerID = '';
    $actionID = '';

    if (isset(Yii::$app->controller->module->id)) {
        $moduleID = Yii::$app->controller->module->id;
    }
    if (isset(Yii::$app->controller->id)) {
        $controllerID = Yii::$app->controller->id;
    }
    if (isset(Yii::$app->controller->action->id)) {
        $actionID = Yii::$app->controller->action->id;
    }
    
    echo yii\bootstrap\Nav::widget([ 
        'items' => [
            
            [
                'label' => Yii::t('chanpan', 'Verify email template'),
                'url' => ['/manage_user/setting/verify-email'],
                'active'=>($moduleID=='manage_user' && $controllerID=='setting' && $actionID=='verify-email') ? TRUE : FALSE
            ],            
            [
                'label' => Yii::t('chanpan', 'Recover password template'),
                'url' => ['/manage_user/setting/recover-password'], 
                'active'=>($moduleID=='manage_user' && $controllerID=='setting' && $actionID=='recover-password') ? TRUE : FALSE
            ],

        ],
        'options' => ['class' =>'nav nav-tabs'], // set this to nav-tab to get tab-styled navigation
    ]);
?>