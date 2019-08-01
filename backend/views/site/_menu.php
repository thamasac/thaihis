<?php 
    $urlMain    = \Yii::$app->params['model_dynamic']; //\cpn\chanpan\classes\CNServer::getServerName();
    $main_url   = \Yii::$app->params['main_url'] //\backend\modules\core\classes\CoreFunc::getParams('main_url', 'url');
?>

<?php if(Yii::$app->user->can('administrator') && $urlMain['url'] == $main_url): ?>
<div class="pull-right">
    <?= yii\helpers\Html::a("<i class='glyphicon glyphicon-plus'></i> Project Management",'/manageproject/monitor-project', ['class'=>'btn btn-success']);?>
</div>
<?php endif; ?>
<?php

echo yii\bootstrap\Nav::widget([ 
    'items' => [
        [
            'label' => Yii::t('chanpan', 'All My Projects'),
            'url' => ['site/index'],  
        ],
        [
            'label' => Yii::t('chanpan', 'Created by me'),
            'url' => ['site/my-own'],  
        ],
        [
            'label' => Yii::t('chanpan', 'Assigned to me'),
            'url' => ['site/assign-to-me'],  
        ],
        [
            'label' => Yii::t('chanpan', 'Co-Creator'),
            'url' => ['site/co-creator'],
        ],
        [
            'label' => Yii::t('chanpan', 'Templates'),
            'url' => ['site/project-templates'],  
        ],
        [
            'label' => Yii::t('chanpan', 'Collaborations'),
            'url' => ['site/project-seeking'],  
        ],
        [
            'label' => Yii::t('chanpan', 'Trash'),
            'url' => ['site/project-trash'],  
        ],
       
    ],
    'options' => ['class' =>'nav nav-tabs'], // set this to nav-tab to get tab-styled navigation
]);

?>