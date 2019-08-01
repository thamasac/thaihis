<?php 
    $reloadDiv1 = "step1-grid"; 
    $modal = "modal-ezform-main";    
  ?>

<div id="schedule"></div>
<?php
$this->registerJS("
    initUser=function(){
        let url = '".yii\helpers\Url::to(['/ezmodules/ezmodule/view?id=1521807350087906600&tab=1521807381035975500&addon=0'])."';
        $.get(url, function(data){
            $('#schedule').html(data);
        });
    }
    initUser();
");
?>