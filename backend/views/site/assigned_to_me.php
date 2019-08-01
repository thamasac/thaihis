<?php
    $this->title = Yii::t('chanpan','Assigned to me');
?>
<?= $this->render("_menu");?>
<div id="assigned-to-me"></div>
<?php 
$this->registerJs("
    function initData(){
        $('#assigned-to-me').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
        let url = '".yii\helpers\Url::to(['/manageproject/clone-project/my-assign'])."';
        $.get(url, function(data){
            $('#assigned-to-me').html(data);
        });
        return false;
    }
    initData();

");
?>