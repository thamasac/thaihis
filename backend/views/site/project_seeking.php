<?php
$this->title = Yii::t('chanpan','Project Seeking Collaborations');
?>
<?= $this->render("_menu");?>
    <div id="project-seeking"></div>
<?php
$url = yii\helpers\Url::to(['/manageproject/clone-project/collaboration-project']);
$this->registerJs(<<<JS
    function initData(){
        $('#project-seeking').html("<div class='sdloader'><i class='sdloader-icon'></i></div>");
        let url = "$url";
        $.get(url, function(data){
            $('#project-seeking').html(data);
        });
        return false;
    }
    initData();
JS
);
?>