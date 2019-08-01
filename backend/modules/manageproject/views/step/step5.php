<?php 
    $reloadDiv1 = "step1-grid"; 
    $modal = "modal-ezform-main";   
    $this->title= Yii::t('chanpan','Financial Allocation');
  ?>
<?= $this->render('_menu',['defaultActive'=>4])?>
<div style='color: #eb622c;'><b>Required both main tabs.</b></div>
<div id="fms"></div>
<?php
$this->registerJS("
    initUser=function(){
        let url = '".yii\helpers\Url::to(['/ezmodules/ezmodule/view?id=1520807564095312900&status=wizard'])."';
        $.get(url, function(data){
            $('#fms').html(data);
        });
    }
    initUser();
");
?>