<?php 
    $config = [
        [   'step'=>'<b>Step 1</b>', 
            'id'=>'step1',
            'name'=>Yii::t('chanpan','Initial Setting'),
            'url'=> yii\helpers\Url::to(['/manageproject/step/index', 'step'=>1])
        ],
        [   'step'=>'<b>Step 2</b>', 
            'id'=>'step2',
            'name'=>Yii::t('chanpan','Project Information'),
            'url'=> yii\helpers\Url::to(['/manageproject/step/index', 'step'=>2])
        ],
        [   'step'=>'<b>Step 3</b>', 
            'id'=>'step3',
            'name'=>Yii::t('chanpan','Setup Site and Members'),
            'url'=> yii\helpers\Url::to(['/manageproject/step/index', 'step'=>3])
        ],
        [   'step'=>'<b>Step 4</b>', 
            'id'=>'step4',
            'name'=>Yii::t('chanpan','Schedule & Procedures'),
            'url'=> yii\helpers\Url::to(['/manageproject/step/index', 'step'=>4])
        ],
        [   'step'=>'<b>Step 5</b>', 
            'id'=>'step5',
            'name'=>Yii::t('chanpan','Financial Allocation'),
            'url'=> yii\helpers\Url::to(['/manageproject/step/index', 'step'=>5])
        ],
        [   'step'=>'<b>Step 6</b>', 
            'id'=>'step6',
            'name'=>Yii::t('chanpan','Let\'s begin a project.'),
            'url'=> yii\helpers\Url::to(['/manageproject/step/index', 'step'=>6])
        ],
    ];  
    $main_url = \backend\modules\core\classes\CoreFunc::getParams('main_url', 'url');
    $startUrl = \backend\modules\core\classes\CoreFunc::getParams('start_url', 'url');
    if(isset($startUrl)){
        $startUrl = "/ezmodules/ezmodule/view?id=1521647584047559700&addon=0&tab=1528945511006792400";
    }
$this->params['breadcrumbs'][] = ['label' => Yii::t('ezform', 'nCRC Central'), 'url' => 'https://www.ncrc.in.th/'];
    $this->params['breadcrumbs'][] = ['label' => Yii::t('ezform', 'My Projects'), 'url' => "https://{$main_url}"];
    $this->params['breadcrumbs'][] = ['label' => Yii::t('ezform', 'Current Project'), 'url' => \yii\helpers\Url::home()];
    $this->params['breadcrumbs'][] = Yii::t('ezform', 'Initial Setup');
    
    echo \cpn\chanpan\widgets\CNWizards::widget([
         'config'=>$config,
         'defaultStep'=>$defaultActive,
         'urlStart'=>$startUrl
    ]); 
    
?><br>
