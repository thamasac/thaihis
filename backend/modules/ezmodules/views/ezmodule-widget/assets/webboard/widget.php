<?php
$this->title = Yii::t('chanpan', 'Webboard');

//$this->params['breadcrumbs'][] = ['label' => Yii::t('ezform', 'Ezforms'), 'url' => ['/ezforms2/ezform/index']];
//$this->params['breadcrumbs'][] = $this->title;

?>
 
<div id="show-webboard"></div>

<?php 
    $this->registerJs("
        showWebboard=function(){
            let url = '".yii\helpers\Url::to(['/webboard/default/get-webboard'])."';
            getWebboard(url);
        }
        getWebboard=function(url){            
            $('#show-webboard').html('<div class=\"sdloader\"><i class=\"sdloader-icon\"></i></div>');    
            $.get(url, function(data){
                $('#show-webboard').html(data);
            });
        }
        showWebboard();
    ");
?>