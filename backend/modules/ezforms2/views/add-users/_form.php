<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use kartik\widgets\Select2;
use yii\helpers\Url;
    $sql = "SELECT user_id as id, CONCAT(`firstname`,' ',`lastname`) as `name`  FROM `profile` WHERE user_id = :id";
    $dataUser = Yii::$app->db->createCommand($sql, [':id'=>$model['user_id']])->queryOne(); 
    
    $dbType = $db_type;
?>
 
<div class="profile-tcc-form">

    <?php $form = ActiveForm::begin([
	'id'=>'frmAddUser',
    ]); ?>
 

    <div class="modal-body">
        <div class="row">
            <div class="col-md-3">
                <input type="hidden" name="type" id="type" value="<?= $dbType; ?>"/>
                <?php 
                    if($status == '1'){ 
                       echo \cpn\chanpan\classes\CNUser::getUserForm($form, $model, 'user_id', $dbType, $dataUser);
                    } 
                    ?>
            </div>        
            <div class="col-md-3">
               <?php
                    echo \common\modules\user\classes\CNSitecode::getSiteCodeForm($form, $model, 'sitecode');  
                ?>
            </div>
            <div class="col-md-3">
                <?php
                    echo common\modules\user\classes\CNDepartment::getDepartmentForm($form, $model, 'department');  
                ?> 
            </div>
            <div class="col-md-3">
                <br>
                <div style="margin-top:5px;">
                    <?= Html::submitButton(Yii::t('app', "Save"), ['class'=>'btn btn-primary btn-block' , 'id'=>'btnAddUser']) ?>
                </div>
            </div>
        </div>
 
    </div>
 

    <?php ActiveForm::end(); ?>

</div>
<?php  $this->registerJs("
    $('#frmAddUser').on('beforeSubmit', function(e) {
        var \$form = $(this);
        $('#btnAddUser').attr('disabled', true);
        $.post(
            \$form.attr('action'), //serialize Yii2 form
            \$form.serialize()
        ).done(function(result) {
            console.log(result);
            if(result.status == 'success') {
                ". SDNoty::show('result.message', 'result.status') ."
                $('#btnAddUser').attr('disabled', false); 
                 $('#modal-user').modal('toggle');
                 //$('#modal-user2').modal('hide');
                 initUser();
//                $.pjax.reload({container:'#user-grid-pjax'});
//                location.reload();
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
    
    $('#cnprofiletcc-user_id').change(function(){
        let user_id = ''+$(this).val();
        //alert(user_id);
        setSiteCode(user_id);
    });
    $('#cnprofiletcc-sitecode').change(function(){
        let sitecode = $(this).val();
        
    });
    
setSiteCode=function(user_id){
    let url = '/ezforms2/add-users/get-sitecode';
    $.get(url, {user_id:user_id}, function(data){
        data = JSON.parse(data);
//        console.log(data);
        if(data['status'] == 'success'){
            let id = data['results']['id'];
            let name = data['results']['name'];
            let fullName = `\${data['results']['name']}(\${data['results']['id']})`;
             

            $('#select2-cnprofiletcc-sitecode-container').attr('title', `\${fullName}`); 
            $('#select2-cnprofiletcc-sitecode-container').html(`
                <span class='select2-selection__clear'>×</span>\${fullName}</span>
            `);
            $('#cnprofiletcc-sitecode').html(`<option value=\${id}>\${fullName}</option>`);
        }
    });
}

");?>