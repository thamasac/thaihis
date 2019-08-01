<?php
    use yii\helpers\Html;
    use yii\helpers\Url;
    use appxq\sdii\helpers\SDNoty;
    $this->title = Yii::t('chanpan','manage');
?>

<div class="modal-header">
    <div class="modal-title">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button> 
    </div>   
</div>
<div class="modal-body">
     <?= Html::button('<i class="fa fa-plus"></i>', ['class'=>'btn btn-sm btn-success btnAdd'])?>
    <div id="manage-<?= $options['widget_id']?>" style="margin-top:10px;"></div>
</div>
<?php 
$this->registerJs("
    $('.btnAdd').click(function(){
        $('.btnAdd').prop('disabled', true);
        let url = '".Url::to(['/topic/topic-multi/save-form', 'options'=>$options])."';
        $.get(url,function(result){
            if(result.status == 'success') {
		    ". SDNoty::show('result.message', 'result.status') .";
                    getForm();
                    
		} else {
		    ". SDNoty::show('result.message', 'result.status') ."
		}
        });
        return false;
    });
    function getForm(){
        let url = '".Url::to(['/topic/topic-multi/get-form', 'options'=>$options])."';
        $.get(url,function(data){
            $('#manage-".$options['widget_id']."').html(data);
            $('.btnAdd').prop('disabled', false);    
        });
    }getForm();//โหลด ฟอร์มเพื่อบันทึก    
");
?>