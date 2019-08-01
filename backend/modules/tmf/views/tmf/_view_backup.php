<?php

echo \yii\bootstrap\Tabs::widget([
    'id' => 'eztabs-'.$reloadDiv,
    'items' => $items,
]);




?>
<div class="clearfix"></div>
<br/>

<div class="clearfix"></div>
<div id="divContent-<?=$reloadDiv?>" >
    <div class="sdloader "><i class="sdloader-icon"></i></div>
</div>

<div class="modal modal-content-grid" role="dialog">
    <div class="modal-dialog" style="width:80%;" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Document All Version</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<?php

//\appxq\sdii\utils\VarDumper::dump($field_taget);
richardfan\widget\JSRegister::begin(['position'=> yii\web\View::POS_READY]);
?>
<script>
    
    

    getGrid(0);
    $('.tabHeader').on('click',function(){
         getGrid($(this).attr('data-value'));
    });
    
    function getGrid(id){
        $('#divContent-<?=$reloadDiv?>').html('<div class="sdloader "><i class="sdloader-icon"></i></div>');
        $.ajax({
            url:'/ezforms2/tmf/get-grid',
            method:'get',
            type:'html',
            data:{
                typeId:id,
                type_ezf:'<?=$docTypeId?>',
                name_ezf:'<?=$docNameId?>',
                detail_ezf:'<?=$docDetailId?>',
                column:<?=json_encode($field_column)?>,
                ezf_id:'<?=$docDetailId?>',
                pageSize:'<?=$pageSize?>',
                field_taget:'<?=$field_taget?>',
                reloadDiv:'<?=$reloadDiv?>'
                
            },
            success:function(result){
                $('#divContent-<?=$reloadDiv?>').html(result);
            }
        });
    }
</script>

<?phprichardfan\widget\JSRegister::end();



