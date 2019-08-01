<?= $this->render('_menu',['defaultActive'=>3])?>
<div style='color: #eb622c;'><b>Optional, required if RCT.</b></div>
<?php
$this->title= Yii::t('chanpan','Schedule Config');
//use appxq\sdii\helpers\SDNoty;
//use backend\modules\ezforms2\classes\EzfAuthFunc;
//
//
//echo "<div><h3>Subject Management System</h3></div>";
//$options = [
//    ['icon' => '', 'title' => Yii::t('chanpan', 'Schedule of Visits'), 'url' => yii\helpers\Url::to(['/ezmodules/ezmodule/view?id=1521807350087906600&tab=1521807381035975500&addon=0']), 'active' => true],
//    ['icon' => '', 'title' => Yii::t('chanpan', 'Visit Procedures'), 'url' => yii\helpers\Url::to(['/ezmodules/ezmodule/view?id=1521807350087906600&tab=1521807355083649100&addon=0'])],
//];
//
//
//echo cpn\chanpan\widgets\CNTabWidget::widget([
//    'options'=>$options,
//    'id'=>'subject_management_system',
//    'script'=>"
//        $('#ezmodule-main-app .modal-header').hide();
//        $('#ezmodule-tab-items #ezmodule_tab_menu').hide();
//    "
//]);
?>

<?php 
    $reloadDiv1 = "step1-grid"; 
    $modal = "modal-ezform-main";    
  ?>

<div id="schedule"></div>
<?php
$this->registerJS("
    //;
    initUser=function(){
        let url = '".yii\helpers\Url::to(['/ezmodules/ezmodule/view?id=1527671551055452800&addon=0&tab=1527684541063328600'])."';
        $.get(url, function(data){
            $('#schedule').html(data);
            $('#ezmodule-main-app .modal-header a , #ezmodule-main-app .modal-header .modal-title img').remove();
        });
    }
    initUser(); 
");
?>