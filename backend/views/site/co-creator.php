<?php
$this->title = Yii::t('chanpan','Co-Creator');
?>
<?= $this->render("_menu");?>
    <div id="co-creator"></div>
<?php
$url = yii\helpers\Url::to(['/manageproject/clone-project/co-creator-project']);
$this->registerJs(<<<JS
    function initData(){
        $('#co-creator').html("<div class='sdloader'><i class='sdloader-icon'></i></div>");
        let url = "$url";
        $.get(url, function(data){
            $('#co-creator').html(data);
        });
        return false;
    }
    initData();
JS
);
?>