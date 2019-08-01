<?php
    use yii\helpers\Html;
    use yii\helpers\Url;
    use appxq\sdii\helpers\SDNoty;
    use backend\modules\ezforms2\classes\EzfAuthFunc;
    backend\modules\ezforms2\assets\JLoading::register($this);
    $this->title = Yii::t('chanapn', 'Update Project');
?>

<?php

$reloadDiv1 = "step1-grid";
$modal = "modal-ezform-main";
$domain = \cpn\chanpan\classes\CNServerConfig::getDomainName();
$dataDynamic = backend\modules\manageproject\classes\CNCloneDb::checkDynamicDb($domain);


$dataId=$id;
$ezfId='1523071255006806900';
$url =  \yii\helpers\Url::to(['/ezforms2/ezform-data/ezform?ezf_id='.$ezfId.'&dataid='.$dataId.'&modal=modal-create-project&reloadDiv=step1-grid&db2=0']);
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <div class="modal-title">        
        <div>
            <?= $button;?>
        </div>        
    </div>
</div>
<div class="modal-body">
    <?php if($rstat != '3'):?>
    <div id="showCreateProject"></div>
    <?php else: ?>
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table teble-bordered table-striped table-bordered table-responsive">
                    <tr>
                        <td width="200" class="text-right">Project Name :</td>
                        <td><?= $data['projectname']?></td>
                    </tr>
                    <tr>
                        <td width="200" class="text-right">Project Acronym :</td>
                        <td><?= $data['projectacronym']?></td>
                    </tr>
                    <tr>
                        <td width="200" class="text-right">Principal Investigator Name :</td>
                        <td><?= $data['pi_name']?></td>
                    </tr>
                    <tr>
                        <td width="200" class="text-right">Specify URL :</td>
                        <td><?= $data['projurl'].''.$data['projdomain']?></td>
                    </tr>
                    <tr>
                        <td width="200" class="text-right">Status :</td>
                        <td><label class="label label-danger">Deleted</label></td>
                    </tr>
                </table>
            </div>
        </div> 
    </div>
    
    <?php endif; ?>
</div>

<?php richardfan\widget\JSRegister::begin(['position' => static::POS_END])?>
<script>
    $('#modal-create-project').on('hidden.bs.modal', function () {
        $.get('/manageproject/clone-project/get-project-all?status=1', function(data){
          $('#showProjectAll').html(data);
       });
     });

    function initData(){
        
        let url = '<?= yii\helpers\Url::to(['/manageproject/clone-project/get-trash-project'])?>';
        $('#project-trash').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');    
        $.get(url, function(data){
            $('#project-trash').html(data);
        });
        return false;
    }
    $('.btnProject').on('click',function(e){
         
        e.preventDefault();
        let url = $(this).attr('data-url');
        let id = $(this).attr('data-id');
        let action = $(this).attr('data-action');
        if(action == 'delete'){
           yii.confirm('Confirm Delete', function(){
                let ele = 'body';
                $('.btnProject').attr('disabled', true);
                onLoadings(ele);
               $.post(url, {id:id}, function(data){
                   hideLoadings(ele);
                   $('.btnProject').attr('disabled', false);
//                   console.log(data);return false;
                   if(data.status=='success'){
                        <?= appxq\sdii\helpers\SDNoty::show('data.message', 'data.status')?>                    
                    }else{
                        <?= appxq\sdii\helpers\SDNoty::show('data.message', 'data.status')?> 
                    }
                    $('#modal-create-project').modal('hide');
                    setTimeout(function(){
                        showProjectAll();
                    }, 1000);
                    hideLoadings(ele);
                    $('.btnProject').attr('disabled', false);
                    
               }); 
           }); 
           
        }
        else if(action == 'destroy'){
            yii.confirm('<h3>Permanently Delete Warning Message</h3><hr/><p>All files and data of this Project will be deleted permanently. If you wish to restore the Project in the future, please backup the Project then download the nCRC Backup File before this step</p>', function(){
               let ele = 'body';
                $('.btnProject').attr('disabled', true);
                onLoadings(ele);
               $.post(url, {id:id}, function(data){
                   hideLoadings(ele);
                   $('.btnProject').attr('disabled', false);
//                   console.log(data);return false;
                   if(data.status=='success'){
                        <?= appxq\sdii\helpers\SDNoty::show('data.message', 'data.status')?>                         
                        $('.list-items').filter('[data-id="'+id+'"]').remove();        
                    }else{
                        <?= appxq\sdii\helpers\SDNoty::show('data.message', 'data.status')?> 
                    }
                    $('#modal-create-project').modal('hide');  
                    //initData();
                    setTimeout(function(){
                        initData();
                    }, 1000);
                    hideLoadings(ele);
                    $('.btnProject').attr('disabled', false);
                    
               }); 
            });
            return false;
        }
        else if(action == 'restore'){
            yii.confirm('Confirm Restore', function(){
                let ele = 'body';
                $('.btnProject').attr('disabled', true);
                onLoadings(ele);
                $.post(url, {id:id}, function(data){
                   hideLoadings(ele);
                   $('.btnProject').attr('disabled', false);
                  
                   if(data.status=='success'){
                        <?= appxq\sdii\helpers\SDNoty::show('data.message', 'data.status')?>                    
                    }else{
                        <?= appxq\sdii\helpers\SDNoty::show('data.message', 'data.status')?> 
                    }
                    initData();
                    $('#modal-create-project').modal('hide');
                    $('.modal-backdrop').hide();
                    
                    
                }); 
            });
        }
        else if(action == 'update'){
            yii.confirm('Confirm Repair', function(){
                let ele = 'body';
                $('.btnProject').attr('disabled', true);
                onLoadings(ele);
                $.get(url, {id:id}, function(data){
                   console.log(data);
                   <?= SDNoty::show('data.message', 'data.status')?>
                   hideLoadings(ele);
                   $('.btnProject').attr('disabled', false);
                });
            });
        }
        else if(action == 'clone'){
            yii.confirm('Confirm Clone', function(){
                let ele = 'body';
                $('.btnProject').attr('disabled', true);
                onLoadings(ele);
                $.post(url, {id:id}, function(data){
//                   console.log(data);return false;
                   if(data.status == 'success'){
                     let url2 = "/manageproject/setting-project/repair?id="+data['data']['id'];  
                     getUpdate(data['data']['id'], url2); 
                   }
                });
            });
        }else if(action == 'backup'){
           Backup(id, url);
           return false;
       }
        return false; 
    });//btn project
    
    /*backup*/
    function Backup(id, url){
      yii.confirm('Backup file Project', function(){
        onLoadings('body');
        $('.btnProject').attr('disabled', true);
        $.post(url,{id:id}, function(data){
           hideLoadings('body');           
           $('.btnProject').attr('disabled', false);
           if(data.status == 'success'){
               
               <?= \appxq\sdii\helpers\SDNoty::show('data.message', 'data.status')?>
               let param = data['data']['url']+'/'+data['data']['path']+'/'+data['data']['file_name'];
               console.log(param);
               location.href = param;
               let uri = '/manageproject/backup-restore/download';
               $.get(uri,{params:data['data']}, function(data){
                   console.log(data);
               });
                
           } 
        });
      });
      return false;  
    }
    
    function getUpdate(id, url){
        let ele = 'body';
        $.get(url, {id:id}, function(data){
            //console.log(data);
            <?= SDNoty::show('data.message', 'data.status') ?>
            
            $('.btnProject').attr('disabled', false);
            $('#modal-create-project').modal('hide');
            $('.modal-backdrop').hide();
//            let url = '/manageproject/monitor-project/my-project?search='+$('#txtSearch').val();
//            loadUrl(url);
            setTimeout(function(){
                showProjectAll();
                hideLoadings(ele);
                $('.btnProject').attr('disabled', false);
            }, 1000);
        });
    }

    var showCreateProject = 'showCreateProject';
    var frm_str = 'ezform-1523071255006806900';
    function loadCreateProjectWithAjax(){
        $('#'+showCreateProject).html('<div class="sdloader "><i class="sdloader-icon"></i></div>');
        let url = '<?= $url;?>';
        $.get(url, function(data){
            
            $('#'+showCreateProject).html(data);
            removeHeaderForm();
           // readOnlyInput();
            updateStatus();
            renameBtnSubmit();
            btnOnClick();
            return false;
        });

    }
    
    function btnOnClick(){
        setTimeout(function(){
            $('#'+frm_str).on('submit', function(e){
               let id = '<?= $id; ?>';
               let url = '<?= Url::to(['/manageproject/center-project/update'])?>';
               setTimeout(function(){
                   $.post(url, {id:id}, function(data){
                       console.log(data);
                       showProjectAll();
                   });
                   return false;
               }, 2500);
               
            });
        }, 1000);
        return false;
    }
    function updateBackend(){
        let url = '<?= Url::to(['/manageproject/update-project/update-data-form-backend'])?>';
        $.post(url,$('#'+frm_str).serialize(),function(data){
            console.log(data);
        });
    }
    function updateStatus(){
        var status_form = 'ez1523071255006806900-status_form';
        $('#'+status_form).val('update');
        return false;
    }
    function renameBtnSubmit(){
        $('button[type="submit"][value="1"]').html('Update');
        return false;
    }
    function removeHeaderForm(){
        $('#'+showCreateProject+' .nav-tabs').remove();
        $('#'+showCreateProject+' .btn-info').remove();
        $('#'+showCreateProject+' .close').remove();
        $('#'+showCreateProject+' .btn-default').remove();
        $('.field-ez1523071255006806900-projectacronym p span em').remove();
        $('.field-ez1523071255006806900-projurl p span em').remove();
        return false;
    }
    function readOnlyInput(){
        let projdomain = 'ez1523071255006806900-projdomain';
        let projectaconymValue = 'ez1523071255006806900-projectacronym';
        let studydesign_str = 'ez1523071255006806900-studydesign';
        let projUrl = 'ez1523071255006806900-projurl';
        $('#'+projdomain).replaceWith('<div style="padding: 7px; background: #e7e7ec;"><b>'+$('#'+projdomain).val()+'</b></div>');
        $('#'+projectaconymValue).replaceWith('<div style="padding: 7px; background: #e7e7ec;"><b>'+$('#'+projectaconymValue).val()+'</b></div>');
        $('#'+projUrl).replaceWith('<div style="padding: 7px; background: #e7e7ec;"><b>'+$('#'+projUrl).val()+'</b></div>');
        setTimeout(function(){
            $('#'+studydesign_str).attr('disabled', true);
        },1000);
        return false;
    }
    function getUiAjax(url, divid) {
        $.post(url, function(result){
            $('#'+divid).html(result);
        });
        return false;
    }
    
    //loading 
    function onLoadings(ele){
        $(ele).waitMe({
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
    function hideLoadings(ele){
         $(ele).waitMe("hide");
    }

    //init
    loadCreateProjectWithAjax();
</script>
<?php richardfan\widget\JSRegister::end();?>
