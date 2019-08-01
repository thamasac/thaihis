<?php
    $this->title = Yii::t('chanpan','Project Trash');
?>
<?= $this->render("_menu");?>
<div id="project-trash"></div>
<?php 
$this->registerJs("
    function initData(){
        let url = '".yii\helpers\Url::to(['/manageproject/clone-project/get-trash-project'])."';
        $('#project-trash').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');    
        $.get(url, function(data){
            $('#project-trash').html(data);
        });
        return false;
    }
    initData();

");
?>