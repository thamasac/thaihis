<?php

/* 
 * This file is part of the Dektrium project
 * 
 * (c) Dektrium project <http://github.com/dektrium>
 * 
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */
 
use yii\bootstrap\Nav;

$domain = Yii::$app->params['current_url']; //\cpn\chanpan\classes\CNServer::getDemain();
$main_url = Yii::$app->params['main_url']; //\backend\modules\core\classes\CoreFunc::getParams('main_url', 'url');
if($domain == $main_url || $domain == "backend.ncrc.local"){
    $items=[
                [
                    'label'   => Yii::t('user', 'New user'),
                    'url'     => ['/user/admin/create'],
                ],
                [
                    'label'   => Yii::t('rbac-admin', 'New user Thai Care Cloud'),
                    'url'     => ['/ezforms2/add-users','db_type'=>'tcc'],
                ],
                [
                    'label'   => Yii::t('chanpan', 'New user nCRC'),
                    'url'     => ['/ezforms2/add-users','db_type'=>'ncrc'],
                ],
 
            ];
}else{
     $items=[
                [
                    'label'   => Yii::t('rbac-admin', 'New user Thai Care Cloud'),
                    'url'     => ['/ezforms2/add-users','db_type'=>'tcc'],
                ],
                [
                    'label'   => Yii::t('chanpan', 'New user nCRC'),
                    'url'     => ['/ezforms2/add-users','db_type'=>'ncrc'],
                ],
 
            ];
}
?>

<?= Nav::widget([
    'options' => [
        'class' => 'nav-tabs',
        'style' => 'margin-bottom: 15px'
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
            'items' =>$items,
        ]
    ]
]) ?>
