<?php 
    use backend\modules\ezforms2\classes\BtnBuilder;
    use backend\modules\ezforms2\classes\EzfHelper;
    use backend\modules\ezforms2\classes\EzfAuthFuncManage;
    use yii\helpers\Html;
    $this->title= Yii::t('chanpan','Permission');
    $reloadDiv = "permission"; 
    $modal = "modal-ezform-main";
    $module_ids = $module_id;
    $modal_permission='modal-permission';
    $ezfId='1528936267089555700';
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title"><i class="fa fa-unlock-alt"></i> <?= Html::encode($this->title)?></h4>
</div>
<div class="modal-body">
    <div id="show-permission"></div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
</div>
<?php \richardfan\widget\JSRegister::begin();?>
<script>
    function getModules(url){ 
        $('#show-permission').html('<div class="sdloader "><i class="sdloader-icon"></i></div>');
        $.get(url, function(data){
           $('#show-permission').html(data);
        });
    }
    getModules('/manage_modules/default/get-module-all');
    function onLoadings(){
        $('#show-permission').waitMe({
            effect : 'facebook',
            text : 'Please wait...',
            bg : 'rgba(255,255,255,0.7)',
            color : '#000',
            maxSize : '',
            waitTime : -1,
            textPos : 'vertical',
            fontSize : '',
            source : '',
            onClose : function() {}
        });
    }
    function hideLoadings(){
         $('#show-permission').waitMe("hide");
    }  
</script>
<?php richardfan\widget\JSRegister::end()?>