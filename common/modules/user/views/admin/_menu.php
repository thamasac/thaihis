<?php

use yii\bootstrap\Nav;


$domain = \cpn\chanpan\classes\CNServerConfig::getDomainName();
 

$items=[
               [
                    'label'   => Yii::t('user', 'New user'),
                    'url'     => ['/user/admin/create'],
                ],
                [
                    'label'   => Yii::t('rbac-admin', 'Import user from Thai Care Cloud'),
                    'url'     => ['/ezforms2/add-users','db_type'=>'tcc'],
                ],
                [
                    'label'   => Yii::t('chanpan', 'Import user from nCRC'),
                    'url'     => ['/ezforms2/add-users','db_type'=>'ncrc'],
                ],
 
            ];
?>



<?= Nav::widget([
    'options' => [
        'class' => 'nav-tabs',
        'style' => 'margin-bottom: 15px',
        'id'=>'menu-user'
    ],
    'items' => [
        [
            'label'   => Yii::t('user', 'Users'),
            'url'     => ['/user/admin/index'],
        ],
        [
            'label' => Yii::t('user', 'Roles'),
            'url'   => ['/rbac/role/index'],
            'visible' => isset(Yii::$app->extensions['dektrium/yii2-rbac']),
        ],
        [
            'label' => Yii::t('user', 'Permissions'),
            'url'   => ['/rbac/permission/index'],
            'visible' => isset(Yii::$app->extensions['dektrium/yii2-rbac']),
        ],
        [
            'label' => Yii::t('user', 'Create'),
	    'active' => (in_array($this->context->action->id, ['create'])),
            'items' => $items,
        ]
    ]
]) ?>
