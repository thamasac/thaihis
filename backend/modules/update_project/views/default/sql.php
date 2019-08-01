<?php
    use backend\modules\ezforms2\classes\BtnBuilder;
    use backend\modules\ezforms2\classes\EzfHelper;
    use backend\modules\ezforms2\classes\EzfAuthFuncManage;
    
    $reloadDiv = "update_project"; 
    $modal = "modal-ezform-main";

    $modal_update='modal-ezform-main';
    $ezfId='1527762063047917200';

    echo appxq\sdii\widgets\ModalForm::widget([
        'id'=>$modal_update,
        'size'=>'modal-lg'
    ])
?>
 <?php 
    backend\modules\ezforms2\classes\EzfStarterWidget::begin();
 ?>
  
<div class="modal-body" style="overflow: hidden;overflow-x: auto;    margin-right: 20px;">
    <div class="row">
         
        <div class="col-md-3 pull-right text-right">
            <div class="row">
                <div>
                     
                    <?php
                            echo BtnBuilder::btn()->ezf_id($ezfId)
                                    ->options(['data-id' => $ezfId, 'class' => 'btn btn-success'])
                                    ->modal($modal_update)->reloadDiv($reloadDiv)->label('<i class="fa fa-plus"></i> ' . Yii::t('chanpan', 'Add'))
                                    ->buildBtnAdd();
                        ?>
                </div>
            </div>
        </div>
    </div>
    <br>
    <?php
        $ezfPermission =  EzfHelper::ui($ezfId)
            //->data_column(['permission_type','user_id','role_name','start_date', 'expiry_date'])            
            ->default_column(0)
            ->reloadDiv($reloadDiv)
            ->modal($modal_update);
            //->search_column(['module_id' => $module_id]);     
        
    echo $ezfPermission->buildGrid();
    ?>
</div>
 <?php 
    backend\modules\ezforms2\classes\EzfStarterWidget::end();
 ?>
 
<?php 
    $this->registerJs("
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
        
        $('.btnRunSQL').click(function(){
            let url = $(this).attr('data-url');
            yii.confirm('" . Yii::t('chanpan', 'Are you sure you want to run  Sql Command?') . "', function(){	
                $('.btnRunSQL').attr('disabled', true);
                        $.post(url,{status:1}, function(data){
                            
                            if(data.status == 'success'){
                                ".appxq\sdii\helpers\SDNoty::show('data.message', 'data.status').";
                            }else{
                                ".appxq\sdii\helpers\SDNoty::show('data.message', 'data.status').";
                            }
                            setTimeout(function(){
                                $('.btnRunSQL').attr('disabled', false);
                            },1000);
                        }); 
            });
            return false;
        });
        
    $('.btnHostory').on('click', function(){
        alert('ok');
        return false;
    });

    ");
?>