<?php
    use yii\helpers\Url;
    cpn\chanpan\assets\CNLoadingAssets::register($this);
    $this->title = Yii::t('chanpan','Project Settings');
?>
 
<?php 
    use appxq\sdii\helpers\SDNoty;
    use backend\modules\ezforms2\classes\EzfAuthFunc;
    $reloadDiv1 = "step1-grid"; 
    $modal = "modal-ezform-main";
    $domain = \cpn\chanpan\classes\CNServerConfig::getDomainName();
    $dataDynamic = backend\modules\manageproject\classes\CNCloneDb::checkDynamicDb($domain);
    
  
    $dataId=$dataDynamic['data_id'];
    $ezfId='1523071255006806900';
    $url =  \yii\helpers\Url::to(['/ezforms2/ezform-data/ezform?ezf_id='.$ezfId.'&dataid='.$dataId.'&modal=modal-ezform-main&reloadDiv=step1-grid&db2=0']);
?>

<div id="showCreateProject"></div>
 
<?php 
    echo \appxq\sdii\widgets\ModalForm::widget([
        'id'=>$modal,
        'size' => 'modal-lg',
        'tabindexEnable' => FALSE,
    ]);
?>
<?php $this->registerJsFile(\yii\helpers\Url::to('@web/js/jquery.min.js'))?>
<?php $this->registerJsFile(\yii\helpers\Url::to('@web/wizard/js/chanpanJs.js'))?>

<?php richardfan\widget\JSRegister::begin(['position' => static::POS_END])?>
<script>
    
    var showCreateProject = 'showCreateProject';
    var frm_str = 'ezform-1523071255006806900';
    function loadCreateProjectWithAjax(){
        $('#'+showCreateProject).html('<div class="sdloader "><i class="sdloader-icon"></i></div>'); 
        let url = '<?= $url;?>';
        $.get(url, function(data){
            $('#'+showCreateProject).html(data);       
            removeHeaderForm();
            //readOnlyInput();
            updateStatus();
            renameBtnSubmit();
            btnOnClick();
            return false;
        });
        
    }
    function btnOnClick(){
        setTimeout(function(){
           $('#'+frm_str).on('submit', function(e){
            e.preventDefault();
            let url = '<?= Url::to(['/manageproject/update-project/update-data-form'])?>';
            $.ajax({
               url:url,
               type: "POST",             
               data: new FormData(this), 
               contentType: false,       
               cache: false,             
               processData:false,       
               success: function(data){
                  setTimeout(function(){
                      $('#ezform-1523071255006806900').waitMe("hide");
                      updateBackend();
                  },500);
               }
            });
         }); 
        }, 1500);
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
    
    //init
    loadCreateProjectWithAjax();
</script>
<?php richardfan\widget\JSRegister::end();?>
