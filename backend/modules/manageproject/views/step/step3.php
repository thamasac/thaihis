<?php

$this->title = Yii::t('chanpan','Site and Members');
?>
<?= $this->render('_menu',['defaultActive'=>2])?>
<div style='color: #eb622c;'><b>Optional but perfect if completed.</b></div><br>
 
<?php 
use appxq\sdii\helpers\SDNoty;
use backend\modules\ezforms2\classes\EzfAuthFunc;

$reloadDiv1 = "step1-grid";
$modal = "modal-ezform-main";

$options = [
    ['icon' => '', 'title' => Yii::t('chanpan', 'SiteCode'), 'url' => yii\helpers\Url::to(['/manageproject/step/get-sitecode']), 'active' => true],
    ['icon' => '', 'title' => Yii::t('chanpan', 'Recruit Members'), 'url' => yii\helpers\Url::to(['/manageproject/step/get-recruit-member'])],
    ['icon' => '', 'title' => Yii::t('chanpan', 'Assign Member to Roles'), 'url' => yii\helpers\Url::to(['/manageproject/step/get-matching'])],
    ['icon' => '', 'title' => Yii::t('chanpan', 'Roles'), 'url' => yii\helpers\Url::to(['/manageproject/step/get-role'])],
    
];
echo \backend\modules\random\classes\CNTab::getTab($options);
?>
 

