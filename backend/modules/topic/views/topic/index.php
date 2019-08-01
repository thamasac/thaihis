<?php

use yii\helpers\Html;
use yii\helpers\Url;
use appxq\sdii\widgets\ModalForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
 
$this->title = Yii::t('app', '');
$this->params['breadcrumbs'][] = $this->title;
$data = (new \yii\db\Query())->select('status')->from('topic')
        ->where([
            'create_by'=> \Yii::$app->user->id,
            'widget_id'=>$options['widget_id']
       ])->one();
$status =0;
if(!empty($data)){
    $status = 1;
}
?>
 
    <div class="text-left">
        <div class="">
            <?php if($status != 1):?> 
                  
                 <?php echo Html::button(SDHtml::getBtnAdd(), ['data-url'=>Url::to(['topic/create','options'=>$options]), 'class' => 'btn btn-success btn-sm', 'id'=>'modal-addbtn-topic']);?>
                 
            <?php endif;?>
            
        </div>
    </div>
    <div id="single-main-<?= $options['widget_id']?>"></div>
 
<?=  ModalForm::widget([
    'id' => 'modal-topic',
    'size'=>'modal-lg'
     
]);
?>

<?php  $this->registerJs("
    
function showTopicAll(){
    let url = '".Url::to(['/topic/topic/get-topic-all','options'=>$options])."'
    $.get(url, function(data){
        $('#single-main-".$options['widget_id']."').html(data);
    });
}
showTopicAll();
$('#modal-addbtn-topic').on('click', function() {
    modalTopic($(this).attr('data-url'));
});
 
function modalTopic(url) {
    $('#modal-topic .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    $('#modal-topic').modal('show')
    .find('.modal-content')
    .load(url);
}

");?>

 