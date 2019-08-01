

<div id="member"></div>
<?php 
    $this->registerJs("
        initData=function(){
            $('#member').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
            let url = '".\yii\helpers\Url::to(['/ezmodules/ezmodule/view?id=1524804539066947200'])."';
            $.get(url, function(data){
                $('#member').html(data);
                $('#member .modal-header').hide();
            });
        }
        initData();
    ");
?>