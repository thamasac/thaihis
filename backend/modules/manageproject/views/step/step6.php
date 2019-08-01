<?php 
    use yii\helpers\Url;
    $this->title= Yii::t('chanpan','Let\'s begin a project.');
     
    $url = Url::to(['/topic/topic/get-topic-all'])
?>
<?= $this->render('_menu',['defaultActive'=>5])?>
<div id="showTopic"></div>
<?= appxq\sdii\widgets\ModalForm::widget([
    'id' => 'modal-topic',
    'size'=>'modal-lg',
]);
?>
<?php
$this->registerJs("
    
    initTopic=function(){
        let url = '".$url."';
        let params= {
                        'options[icon]': 'fa-table',
                        'options[select_topic]': 1,
                        'options[module_id]': '1521626434026827300',
                        'options[widget_id]': '1522038801015961700',
                        'options[panel]':'1',
                        'options[panel_type]': 'primary'
                    };
        
        $.get(url, params, function(data){
            $('#showTopic').html(data);
            setTimeout(function(){
                $('#btnCallapse').hide();
                $('#collapse').show();
            },500);
        });
    }
    
    initTopic();
");
?>
