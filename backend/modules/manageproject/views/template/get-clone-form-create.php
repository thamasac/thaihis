<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use backend\modules\manageproject\classes\JImages;

\backend\modules\ezforms2\assets\JLoading::register($this);
$imgPath = Yii::getAlias('@storageUrl');
$imgBackend = Yii::getAlias('@backendUrl');
$imageSec = "/img/health-icon.png";
$image = !empty($data['projecticon']) ? "{$imgPath}/ezform/fileinput/{$data['projecticon']}" : "{$imageSec}";
//appxq\sdii\utils\VarDumper::dump($data);
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <i class="fa fa-clone"></i> Clone <?= $data['projectname'] ?>
</div>
<div class="modal-body">
    <div id="form-create" style="margin-bottom:50px;">
    
        
        <?php
        $urlDefault = $data['projurl'];
        $domainDefault = $data['projdomain'];
        
         
        $idGen = rand(0,10000); //appxq\sdii\utils\SDUtility::getMillisecTime();
        $projectacronym = "{$data['projectacronym']}";
        $projectacronym = substr($projectacronym, 0, 15).$idGen;
        $url = "{$idGen}";
        ?>
        <div class="col-md-12">
            <h4 class="list-group-item-heading">
                <span>
                    <img src="<?= $data['projecticon'] ?>" class="img-rounded" style="width:80px;">
                </span>
                <?= $data['projectname'] ?> 
            </h4>
        </div>
        <div class="col-md-8 col-md-offset-2">
            <dl class="dl-horizontal">
                <dt style="text-align:left">1. Select Clone Type <span style="color: #F44336;font-size: 14pt;">*</span></dt>
                <dd>
                    <?= Html::dropDownList('clone_for', '', [
                        '0'=>'Please Select',
                        //'original'=>'Clone for Original',
                        'testing'=>'Clone for Testing',
                        'training'=>'Clone for Training',
                        'backup'=>'Clone for Backup'
                    ], [
                        'class'=>'form-control',
                        'id'=>'clone_select'
                    ])?>
                    <p class="clone_select-error" style="color: rgb(169, 68, 66);"></p>
                </dd>
                 
                <dt style="text-align:left">2. Edit the Acronym </dt>
                <dd>
                    <div class="form-group">
                        <input type="hidden" id="change_icon" value="<?= $data['projecticon']?>"/> 
                        <?= Html::textInput('acronym', $projectacronym, ['class'=>'form-control', 'id'=>'acronym']);?>
                    </div>
                </dd>
                <dt style="text-align:left">3. Edit the URL </dt>
                <dd> 
                    <div class="form-group" style="display: flex;">  
                        <div style="flex-grow: 1;margin-right: 5px;">
                            <?php 
                                $url=str_replace("_","",$url);
                            ?>
                             <input type="text" value="<?= $data['projurl'] ?>" class="form-control" readonly="">
                        </div>
                        <div style="flex-grow: 2;">
                            <?= Html::textInput('url', $url, ['class'=>'form-control', 'id'=>'url']);?>                      
                            <p class="url-error" style="color: rgb(169, 68, 66);"></p>
                        </div>
                        <div style="flex-grow:1;align-self: center;font-size:11pt;margin-top:-10px;padding-left:10px;">
                            <strong class="label label-info">.<?= $data['projdomain'] ?></strong>
                        </div>
                    </div>
                </dd>
                <dt style="text-align:left">4. Edit the Project Name : </dt>
                <dd>     
                    <div class="form-group">                         
                        <?= Html::input('text', 'projname', $data['projectname'], ['class' => 'form-control', 'id' => 'projname', 'placeholder' => 'Specify Project Name']) ?>
                        <?= Html::hiddenInput('id', $data['id'], ['class' => 'form-control', 'id' => 'projid']) ?>
                        <p class="project-name-error" style="color:#a94442;display: none;">Specify Project Name cannot be blank.</p>
                    </div>
                </dd>

            </dl>
        </div>
        <div class="col-md-6 col-md-offset-4">
                <?php ActiveForm::begin(); ?>
            <div class="form-group">
                
            </div>
            <div class="form-group text-right">
                        <button class="btn btn-success btn-block btn-lg" id="btnCloneCreateProject" style="    box-shadow: 1px 2px 3px #293a2a;"> <b>Submit</b></button>
           </div>
            
<?php ActiveForm::end(); ?>
        </div>
        <div>
            <div class="clearfix"></div>

        </div>
    </div>
</div>
<?php appxq\sdii\widgets\CSSRegister::begin(); ?>
<style>
    dt {
        line-height: 25px;
    }
    @media (min-width: 768px){
        .dl-horizontal dt {
            float: left;
            width: 180px;
            overflow: hidden;
            clear: left;
            text-align: right;
            text-overflow: ellipsis;
            white-space: nowrap;
            text-align: left;
        }
    }

</style>
<?php appxq\sdii\widgets\CSSRegister::end(); ?>
<?php \richardfan\widget\JSRegister::begin(); ?>
<script>
    
    $('#url').on('blur', function () {
        let val = $(this).val();
        let id = $('#projid').val();
        let uri = '<?= Url::to(['/manageproject/center-project/check-url'])?>';
        $.get(uri, {action:'create', url:val,id:id}, function(data){
           //console.log(data);
           if(data == '1'){
               $('.url-error').text('Specify URL "'+val+'" has already been taken.');
              
               $('#btnCloneCreateProject').attr('disabled', true);
           }else if(data == '3' || data == '4'){
               let txt = 'Sorry, only English lowercase letters (a-z) and number (0-9) are allowed.';
               $('.url-error').text(txt);
               $('#btnCloneCreateProject').attr('disabled', true);
           }
           else{
               $('.url-error').text('');
               //$('#txt-url-val').text(val);
               //$('#url').addClass('hidden');
               //$("#txt-url").show();
               $('#btnCloneCreateProject').attr('disabled', false);
           }
            <?= \appxq\sdii\helpers\SDNoty::show('data.message', 'data.status') ?>
        });

        return false;
    });
    

    /**/
    var element_loadings = 'body';
    function onLoadings(ele) {
        $(ele).waitMe({
            effect: 'facebook',
            text: 'Please wait...',
            bg: 'rgba(255,255,255,0.7)',
            color: '#000',
            maxSize: '',
            waitTime: -1,
            textPos: 'vertical',
            fontSize: '',
            source: '',
            onClose: function () {}
        });
    }
    function hideLoadings(ele) {
        $(ele).waitMe("hide");
    }
//    onLoadings(element_loadings);
//    setTimeout(function(){
//        hideLoadings(element_loadings);
//    }, 3000);
    $('#btnCloneCreateProject').on('click', function () {
        let clone_select = $('#clone_select').val();
        let urlDefault = '<?= $urlDefault?>';
        let domainDefault = '<?= $domainDefault?>';
        let url = '<?= Url::to(['/manageproject/center-project/create-project']) ?>';
        let id = $('#projid').val();
        let projname = $('#projname').val();
        let change_icon = $('#change_icon').val();
        
        if(clone_select == '0'){
            $('.clone_select-error').text('Please Select Clone Type');
            return false;
        }else{
             $('.clone_select-error').text('');              
        } 
        $('.project-name-error').hide();
        if (projname == '') {
            $('.project-name-error').show();
            return false;
        }
        let acronym = $('#acronym').val();
        let urls = $('#url').val();
        urlDefault = urlDefault.replace(/[^a-zA-Z ]/g, "");
        urls = urlDefault+''+clone_select+''+urls;         
        
        let params = {id: id, projname: projname, acronym: acronym, url: urls,change_icon:change_icon, type:'clone'};    
         
        //{id: "1530522624050892800", projname: "testsfsf", acronym: "Min Set1531409263", url: "Min Set1531409263"}
        onLoadings(element_loadings);         
        $.post(url, params, function (data) {
            hideLoadings(element_loadings);            
            if (data.status == 'success') {
                <?= \appxq\sdii\helpers\SDNoty::show('data.message', 'data.status') ?>
                $('#modal-create-project').modal('hide');                
                    setTimeout(function(){
                        let redirect_url = '<?= Url::to(['/site/index'])?>';
                        location.href = redirect_url;
                    },1000);
//                    bootbox.confirm({
//                        title: 'Confirm',
//                        message: 'Do you want to go to a project: '+data['data']['acronym']+'?',            
//                        callback: function (result) {
//                            if (result) {
//                                location.href = data['data']['url'];
//                            }else{
//                                let url = '/manageproject/setting-project/my-project/';
//                                loadUrl(url);
//                            }
//                        }
//                    });
                
                
            }
           
        }).fail(function () {
            hideLoadings(element_loadings);
        });
        return false;
    });
    function getUpdate(id, url) {
        $.get(url, {id: id}, function (data) {
<?= \appxq\sdii\helpers\SDNoty::show('data.message', 'data.status') ?>
            $('#modal-ezform-main').modal('hide');
            setTimeout(function () {
                hideLoadings(element_loadings);
                showProjectAll();
            }, 1000);
        });
    }

function loadUrl(url){
     $('#my-project').html('<div class="sdloader "><i class="sdloader-icon"></i></div>');
   
//     $('#my-project').html('<div class=\'text-center\'><i class=\"fa fa-spinner fa-pulse fa-3x fa-fw\"></i></div>');
     $.get(url, function(data){
        $('#my-project').html(data);
     });
    }

    $('#btnSelectTemplate').on('click', function () {
        $('#templates').show();
        $('#form-create').remove();
    });
</script>
<?php \richardfan\widget\JSRegister::end(); ?>
