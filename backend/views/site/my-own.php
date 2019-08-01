<?php
    $this->title = Yii::t('chanpan','My own');
?>
<?= $this->render("_menu");?>
<div id="my-own"></div>
<?php 
$this->registerJs("
    function initData(){
        $('#my-own').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
        let url = '".yii\helpers\Url::to(['/manageproject/clone-project/my-own'])."';
        $.get(url, function(data){
            $('#my-own').html(data);
        });
        return false;
    }
    initData();

");
?>