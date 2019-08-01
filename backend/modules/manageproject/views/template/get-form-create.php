<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use backend\modules\manageproject\classes\CNImages;
\backend\modules\ezforms2\assets\JLoading::register($this);
//\cpn\chanpan\assets\CNCroppieAssets::register($this);
$imgPath = Yii::getAlias('@storageUrl');
$imgBackend = Yii::getAlias('@backendUrl');
$imageSec = "/img/health-icon.png";
$image = !empty($data['projecticon']) ? "{$imgPath}/ezform/fileinput/{$data['projecticon']}" : "{$imageSec}";
    
?>
<div class="row">
    <div class="col-md-12">
        <ul class="breadcrumb" id='btnSelectTemplate'>
            <li><a href="#" id="btn-select-template" style="text-decoration: none;">Select Templates</a></li>
            <li class="active"><?= $data['projectname'] ?></li>
        </ul>
          
    </div> 
</div>
<div id="form-create">     
    <?php 
        $idGen = cpn\chanpan\classes\utils\CNUtils::randomString(4);
        $projectacronym = "{$data['projectacronym']}{$idGen}";
        $url = cpn\chanpan\classes\utils\CNUtils::replaceString(strtolower("{$data['projectacronym']}{$idGen}"));
    ?>     
    <div class="row" style="margin-top:30px;">
            <div class="col-md-2">
            <div class="upload-msg">
                <?= Html::img($data['projecticon'], ['id' => 'preview_icon', 'class' => 'img-rounded']) ?>
            </div>
        </div>
        <div class="col-md-9">
            <dl class="dl-horizontal">
                <dt style="text-align:left">1. Edit the Icon</dt>
                <dd>
                    <div class="form-group">
                        <div class="upload-edit">
                            <div id="upload-edit"></div>
                            <div id="upload-action" class="text-center">
                                <a id="save-upload" class="btn btn-warning"><?= Yii::t('ezform', 'Save Icon') ?></a>
                            </div>
                            <input type="hidden" id="change_icon" value="<?= $data['projecticon']?>"/> 
                            <div id="div-upload-file">
                                 <label for="file-upload" class="custom-file-upload btn btn-success">
                                    <i class="fa fa-plus"></i> 
                                 </label>
                                <?= Html::fileInput('upload_input', null, ['id' => 'upload-input', 'class'=>'']) ?>                      
                            </div>
                        </div>
                    </div>
                </dd>
                <dt style="text-align:left">2. Edit the Acronym</dt>
                <dd>
                    <div class="form-group">
                        <input type="text" name="acronym" id="acronym" value="<?= isset($projectacronym)?$projectacronym:'' ?>" class="form-control"/>
                    </div>
                </dd>
                <dt style="text-align:left">3. Edit the URL</dt>
                <dd>                
                    <div class="form-group input-url">
                        <div class="input-url-div1">
                            <input type="text" name="url" id="url" value="<?= isset($url)?$url:'' ?>" class="form-control"/>
                            <p class="url-error" style="color: rgb(169, 68, 66);"></p>
                        </div>
                        <div class="input-url-div2">
                            <strong style="font-size:14pt;">.<?= isset($data['projdomain'])?$data['projdomain']:''?></strong>
                        </div>
                    </div>
                </dd>
                <?php ActiveForm::begin(); ?>
                <dt style="text-align:left;width: 170px;">4. Edit the Project Name</dt>
                <dd>                
                    <div class="form-group">                        
                        <textarea rows="4" class="form-control" name="projname" id="projname"><?= isset($projectacronym)?$projectacronym:''?></textarea>
                        <?= Html::hiddenInput('id', $data['id'], ['class' => 'form-control', 'id' => 'projid']) ?>
                        <p class="project-name-error" style="color:#a94442;display: none;">Project Name cannot be blank.</p>
                    </div>
                </dd>
                <dt></dt>
                <dd>
                    <div class="form-group text-right">
                        <button class="btn btn-success btn-block btn-lg" id="btnCreateProjectByTemplate" style="    box-shadow: 1px 2px 3px #293a2a;"> <b>Submit</b></button>
                    </div>
                </dd>
            <?php ActiveForm::end(); ?>

            </dl>
        </div>

        
    </div>
</div>

<?php $this->render('custom_css');?>
<?php $this->registerJs("
        
        
")?>

 
<?php \richardfan\widget\JSRegister::begin(); ?>
<script>
    $('#project-template').hide();
    
    $('.btnChangeData').on('click', function(){
        let action =  $(this).attr('data-action');
        if(action == 'url'){
            //$('#url').removeClass('hidden');
            //$("#txt-url").hide();
            $('#url').focus();
        }else if(action == 'acronym'){
            //$('#acronym').removeClass('hidden');
            //$("#txt-acronym").hide();
            //$('#acronym').focus();
        }
        return false;
    });
    $('#url').on('blur', function(){
        let val = $(this).val();
        let id = $('#projid').val();
        let uri = '<?= Url::to(['/manageproject/center-project/check-url'])?>';
        $.get(uri, {action:'create', url:val,id:id}, function(data){
           //console.log(data);
           if(data == '1'){
               $('.url-error').text('Specify URL "'+val+'" has already been taken.');
               $('#btnCreateProjectByTemplate').attr('disabled', true);
           }else if(data == '3' || data == '4'){
               let txt = 'Sorry, only English lowercase letters (a-z) and number (0-9) are allowed.';
               $('.url-error').text(txt);
               $('#btnCreateProjectByTemplate').attr('disabled', true);
           }
           else{
               $('.url-error').text('');
               //$('#txt-url-val').text(val);
               //$('#url').addClass('hidden');
               //$("#txt-url").show();
               $('#btnCreateProjectByTemplate').attr('disabled', false);
           }
        });
        
        return false;
    });
    $('#acronym').on('blur', function(){
        let val = $(this).val();
        
        //$('#txt-acronym-val').text(val);
        //$(this).addClass('hidden');
        //$("#txt-acronym").show();
        return false;
    });
    
    /**/
    var element_loadings='body';
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
    $('#btnCreateProjectByTemplate').on('click', function () {
        let url = '<?= Url::to(['/manageproject/center-project/create-project']) ?>';
        let id = $('#projid').val();
        let projname = $('#projname').val();
        let change_icon = $('#change_icon').val();
        $('.project-name-error').hide();
        if(projname == ''){
            $('.project-name-error').show();
            return false;
        }
        let acronym = $('#acronym').val();
        let urls = $('#url').val();
        
        
        let params = {id: id, projname: projname, acronym:acronym, url:urls, change_icon:change_icon};
        
         onLoadings(element_loadings);    
        $.post(url, params, function (data) {       
            hideLoadings(element_loadings);  
//            console.log(data); return;
            if(data.status == 'success'){
                <?= \appxq\sdii\helpers\SDNoty::show('data.message', 'data.status') ?>
                $('#modal-ezform-main').modal('hide');
                setTimeout(function(){
                    showProjectAll();
                }, 500);
            }else{
               <?= \appxq\sdii\helpers\SDNoty::show('data.message', 'data.status') ?> 
            }
 
        }).fail(function(){
            hideLoadings(element_loadings);
        });
        return false;
    });
    function getUpdate(id, url) {         
        $.get(url, {id: id}, function (data) {            
            <?= \appxq\sdii\helpers\SDNoty::show('data.message', 'data.status') ?>             
            $('#modal-ezform-main').modal('hide');
            setTimeout(function(){
                hideLoadings(element_loadings);
                showProjectAll(); 
            }, 1000);       
        });
    }
    


    $('#btn-select-template').on('click', function(){
        $('#templates').show();
        $('#form-create').remove();
        $('#project-template').show();
        $('#btnSelectTemplate').hide();
    });
    /* crop images */
        var $uploadCrop;

        function readFile(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $uploadCrop.croppie('bind', {
                            url: e.target.result
                    });
                    $('.upload-edit').addClass('ready');
                }

                reader.readAsDataURL(input.files[0]);
            }
            else {
                swal("Sorry - you're browser doesn't support the FileReader API");
            }
        }

        $uploadCrop = $('#upload-edit').croppie({
            enableExif: true,
            viewport: {
                width: 100,
                height: 100,
                type: 'square' //square, circle
            },
            boundary: {
                width: 150,
                height: 150
            }
        });
        
        $('#upload-input').on('change', function () { 
            $('#btnCreateProjectByTemplate').prop('disabled', true);
            readFile(this); 
        });


        $('#save-upload').on('click', function() {
            $uploadCrop.croppie('result', {
                type: 'canvas',
                size: 'viewport'
            }).then(function (resp) {
                //alert( resp );
                $('#change_icon').val(resp);
                $('#preview_icon').attr('src',resp);
                $('.upload-edit').removeClass('ready');
                $('#change_icon').trigger('change');
                $('#btnCreateProjectByTemplate').prop('disabled', false);
                 
            });
            return false;
        });
   
</script>
<?php \richardfan\widget\JSRegister::end(); ?>
