<?php
    use yii\helpers\Url;
    use yii\helpers\Html;
?>
 
 
    <div id="multi-main-<?= $options['widget_id']?>"></div>
<?= appxq\sdii\widgets\ModalForm::widget([
    'id' => 'modal-topic',
    'size'=>'modal-lg',
]);
?>
<?php 
    $this->registerJs("
        function modalTopic(url) {
            $('#modal-topic .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
            $('#modal-topic').modal('show')
            .find('.modal-content')
            .load(url);
        }
       function getData(){
            let url = '".Url::to(['/topic/topic-multi/get-data','options'=>$options])."';
            $.get(url,function(data){
                    $('#multi-main-".$options['widget_id']."').html(data);
            });
       }getData();
    ");
?>