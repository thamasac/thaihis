<?php 
    use backend\modules\ezforms2\classes\BtnBuilder;
    use backend\modules\ezforms2\classes\EzfHelper;
    use backend\modules\ezforms2\classes\EzfAuthFuncManage;
    
    $reloadDiv = "permission"; 
    $modal = "modal-ezform-main";
    $module_ids = $module_id;
    $modal_permission='modal-permission';
    $ezfId='1519707087068015000';
?>
<?php

$this->registerJs("
        function setModule(){
            let module_id = '" . $module_ids . "';
            localStorage.setItem('module_id', module_id.toString());
            console.log(module_id.toString());
        }setModule();
        
        $('#ezf-modal-box').append('<div id=\'modal-permission\' class=\'fade modal\' role=\'dialog\'><div class=\'modal-dialog modal-xxl\'><div class=\'modal-content\'></div></div></div>');
       
        $('#modal-permission').on('hidden.bs.modal', function(e){
            var hasmodal = $('body .modal').hasClass('in');
            if(hasmodal){
                $('body').addClass('modal-open');
            } 
        });
        function getUiAjax(url, divid) {
            $.ajax({
                method: 'GET',
                url: url,
                dataType: 'HTML',
                success: function(result, textStatus) {
                    $('#'+divid).html(result);
                }
            }).fail(function(err) {
                err = JSON.parse(JSON.stringify(err))['responseText'];
                $('#'+divid).html(`<div class='alert alert-danger'>`+err+`</div>`);
            });
        }

    ");
?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="itemModalLabel"><i class="fa fa-unlock-alt" aria-hidden="true"></i> <?= Yii::t('ezmodule', 'Permission  ') ?></h4>
</div>
<div class="modal-body" style="overflow: hidden;overflow-x: auto;    margin-right: 20px;">
    <div class="row">
        <div class="col-md-3 pull-right">
            <?php 
                //ซ่อนปุ่ม เพิ่ม ถ้าไม่มีสิทธื manage หรือ read and write
                if(EzfAuthFuncManage::auth()->accessManage($module_id, 1)){
                    echo BtnBuilder::btn()
                        ->ezf_id($ezfId)
                        ->options(['data-id' => $_GET['id'], 'class' => 'btn btn-success btn-block'])
                        ->modal($modal_permission)
                        ->reloadDiv($reloadDiv)
                        ->label('<i class="fa fa-plus"></i> '.Yii::t('chanpan', 'Add module permission'))
                        ->buildBtnAdd();
                }    
            ?>
        </div>
    </div>
    <br>
    <?php
        $ezfPermission =  EzfHelper::ui($ezfId)
            ->data_column(['permission_type','user_id','role_name','start_date', 'expiry_date'])            
            ->default_column(0)
            ->reloadDiv($reloadDiv)
            ->modal($modal_permission)
            ->search_column(['module_id' => $module_id])
            ->addbtn(FALSE);
        //ซ่อนปุ่ม view/edit/delete    
        if(EzfAuthFuncManage::auth()->accessManage($module_id, 2)){ //module_id , 2     
            $ezfPermission->disabled(true);
        }

    echo $ezfPermission->buildGrid();
    ?>
</div>
