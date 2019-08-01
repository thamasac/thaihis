<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
 
$title = "";
if($dbType == "tcc"){
    $title =  Yii::t('chanpan', 'Invite Members from Thai Care Cloud');
}else if($dbType == "ncrc"){
    $invite_main = isset(\Yii::$app->params['invite_main']) ? \Yii::$app->params['invite_main'] : 'nCRC';
    $title = Yii::t('chanpan', 'Invite Members from ').$invite_main;
}else{
    $title = Yii::t('chanpan', 'Invite Members from Thai Care Cloud');
} 
 
$this->title = $title;
?>

<div class="profile-tcc-form">

    <?php $form = ActiveForm::begin([
	'id'=>'frmAddUser',
    ]); ?>
 
    <div class="modal-header">
        <b><?= Html::encode($this->title)?></b>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
    </div>
    <div class="modal-body">
        <div class="row">
            
            <div class="col-md-6    ">
                <input type="hidden" name="type" id="type" value="<?= $dbType; ?>"/>
                <?php 
                     
                    echo \cpn\chanpan\classes\CNUser::getUserForm($form, $model, 'user_id', $dbType);
                    
                ?>
            </div>        
            <div class="col-md-6">
               <?php
                    echo \common\modules\user\classes\CNSitecode::getSiteCodeForm($form, $model, 'sitecode', 'Site');  
                ?>
            </div>
            
            <div class="col-md-12">
                <div class="form-group">
                    <?= Html::label(Yii::t('user', 'Message'));?>
                    <?= Html::textarea('message', '', ['class'=>'form-control', 'placeholder'=> Yii::t('user','Enter message to compose')])?>
                </div>
            </div>
            <div class="col-md-4 hidden">
                <?php
                    $model->department = '1524628831068141000';
                    echo common\modules\user\classes\CNDepartment::getDepartmentForm($form, $model, 'department');  
                ?> 
            </div>
             
        </div>
 
    </div>
    <div class="modal-footer">
        <div style="margin-top:5px;">
            <?= Html::submitButton('<i class="fa fa-plus"></i> '.Yii::t('chanpan', "Invite"), ['class'=>'btn btn-success btn-block' , 'id'=>'btnAddUser']) ?>
        </div>
    </div>
 

    <?php ActiveForm::end(); ?>

</div>
<?php richardfan\widget\JSRegister::begin();?>
<script>
    $('#cnuserform-email').on('change', function(){
       let id = $(this).val();
       setName(id, '<?= $dbType?>');
       setSiteCode(id);
       return false;
    });
    setName=function(user_id, type){
        let url = '/manage_user/user/get-name';
        $.get(url, {user_id:user_id, type:type}, function(data){
            data = JSON.parse(data);
            if(data['status'] == 'success'){
                let id = data['results']['id'];
                let name = data['results']['name'];
                let fullName = `${data['results']['name']}`;

                $('#select2-cnuserform-user_id-container').attr('title', `${fullName}`); 
                $('#select2-cnuserform-user_id-container').html(`
                    <span class='select2-selection__clear'>×</span>${fullName}</span>
                `);
                $('#cnuserform-user_id').html(`<option value=${id}>${fullName}</option>`);
            }
        });
    }
</script>
<?php richardfan\widget\JSRegister::end();?>
<?php  $this->registerJs("
    $('#frmAddUser').on('beforeSubmit', function(e) {
        var \$form = $(this);
        $('#btnAddUser').attr('disabled', true);
        $.post(
            \$form.attr('action'), //serialize Yii2 form
            \$form.serialize()
        ).done(function(result) {
//            console.log(result);return false;
            if(result.status == 'success') {
                ". SDNoty::show('result.message', 'result.status') ."
                $('#btnAddUser').attr('disabled', false); 
                 $('#modal-user').modal('toggle');
                 initUser();
            } else {
                ". SDNoty::show('result.message', 'result.status') .";
                $('#btnAddUser').attr('disabled', false);     
            } 
        }).fail(function() {
            ". SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') ."
            console.log('server error');
            $('#btnAddUser').attr('disabled', false); 
        });
        return false;
    });
    
    $('#cnuserform-user_id').change(function(){
        let user_id = ''+$(this).val();        
        setSiteCode(user_id);
    });
    $('#cnprofiletcc-sitecode').change(function(){
        let sitecode = $(this).val();
        
    });
    
setSiteCode=function(user_id){
    let url = '/manage_user/user/get-sitecode';
    $.get(url, {user_id:user_id}, function(data){
        data = JSON.parse(data);
         
        if(data['status'] == 'success'){
            let id = data['results']['id'];
            let name = data['results']['name'];
            let fullName = `\${data['results']['name']}(\${data['results']['id']})`;
             

            $('#select2-cnuserform-sitecode-sitecode-container').attr('title', `\${fullName}`); 
            $('#select2-cnuserform-sitecode-sitecode-container').html(`
                <span class='select2-selection__clear'>×</span>\${fullName}</span>
            `);
            $('#cnuserform-sitecode').html(`<option value=\${id}>\${fullName}</option>`);
        }
    });
}

");?>