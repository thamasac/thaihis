<?php

use kartik\widgets\DatePicker;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;


$this->title = "Invitation";
$site = Yii::$app->db->createCommand("SELECT site_name , site_detail FROM zdata_sitecode WHERE rstat <> 3 ORDER BY create_date ASC ", [])->queryOne();
if (!$site)
    throw new \yii\web\HttpException(404, 'This operation require at least 1 site in this project.');
$sitecode = $site["site_name"];
$sitename = $site["site_detail"];
?>


    <div>

        <?php $form = ActiveForm::begin([
            'id' => 'frmInviteEmail',
        ]); ?>

        <div class="modal-header">
            <b><?= Html::encode($this->title) ?></b>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">

            <ul class="nav nav-tabs" id="pills-tab" role="tablist">
                <li class="nav-item active">
                    <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-basic" role="tab"
                       aria-controls="pills-home" aria-selected="true">Basic</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="pills-advance-tab" data-toggle="pill" href="#pills-advance" role="tab"
                       aria-controls="pills-profile" aria-selected="false">Advance</a>
                </li>
            </ul>
            <br>
            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane active in" id="pills-basic" role="tabpanel" aria-labelledby="pills-home-tab">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="exampleInputEmail1">Email address</label>
                                <input type="email" name="email" class="form-control" id="inviteInputEmail"
                                       aria-describedby="emailHelp" placeholder="Enter email" required>
                                <small id="emailHelp" class="form-text text-muted">We'll never share your email with
                                    anyone
                                    else.
                                </small>
                            </div>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="exampleInputEmail1">Message</label>
                                <textarea name="message" class=" form-control" id="inviteInputMessage"
                                          placeholder="Enter message to compose"></textarea>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="pills-advance" role="tabpanel" aria-labelledby="pills-advance-tab">

                    <div class="row">
                        <div class="col-md-4">
                            <?php
                            $model->sitecode = isset(Yii::$app->user->identity->profile->sitecode) ? Yii::$app->user->identity->profile->sitecode : '';
                            echo \common\modules\user\classes\CNSitecode::getSiteCodeForm($form, $model, 'sitecode', 'Select Site for user', ["allowClear" => false]);
                            ?>
                        </div>
                    </div>
                    <div class="row">
                        <h3 style="margin-left: 10px;">Optional information below is not require.</h3>
                        <div class="col-md-6">
                            <?php
                            foreach ($roles as $key => $value) {
                                if ($roles[$key] == null) $roles[$key] = "Undefined";

                            }
                            echo '<label class="control-label">Role</label>';
                            try {
                                echo Select2::widget([
                                    'id' => 'role-selector',
                                    'name' => 'role',
                                    'data' => $roles,
                                    'options' => ['placeholder' => 'Select a role ...', 'id'=>'roles_'.time()],
                                    'pluginOptions' => [
                                        'allowClear' => true
                                    ],
                                ]);

                            } catch (Exception $e) {
                                echo $e->getMessage();
                            }
                            ?>
                        </div>
                    </div>

                    <div class="row" hidden>
                        <br>
                        <div class="col-md-4">
                            <div class="form-check">
                            <input name="enable_expire" class="form-check-input" type="checkbox" value=""
                                   id="expireCheck">
                            <label class="form-check-label" for="expireCheck">
                                Enable expire date.
                            </label>
                        </div>
                        <div class="form-group" id="expire_date_form" hidden>
                            <?php
                            echo 'Expire Date (Invitation valid until end of selected day.)';
                            echo DatePicker::widget([
                                'name' => 'expire_date',
                                'type' => DatePicker::TYPE_INPUT,
                                'options' => ['placeholder' => 'No Expire', 'id' => 'expire_date_picker'],
                                'pluginOptions' => [
                                    'autoclose' => true,
                                    'allowClear' => true,
                                    'format' => 'dd-M-yyyy'
                                ]
                            ]);
                            ?>
                        </div>
                    </div>
                </div>
            </div>


        </div>

    </div>
    <div class="modal-footer">
        <p id="not-valid-text" class="pull-left" style="color: red">* E-Mail not valid.</p>
        <div style="margin-top:5px;">
            <?= Html::submitButton('<i class="fa fa-plus"></i> ' . Yii::t('chanpan', "Invite"), ['class' => 'btn btn-success btn-block', 'id' => 'btnAddUser']) ?>
        </div>
    </div>


<?php ActiveForm::end(); ?>

    </div>
<?php $this->registerJs("

 $('#btnAddUser').attr('disabled',true);
 $('#inviteInputEmail').change(function() {
    function isValidEmailAddress(emailAddress) {
        var pattern = new RegExp(/^((\"[\w-\s]+\")|([\w-]+(?:\.[\w-]+)*)|(\"[\w-\s]+\")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
        return pattern.test(emailAddress);
    };
    var email = $(\"#inviteInputEmail\").val();
    if(isValidEmailAddress(email)) {
        $('#btnAddUser').removeAttr('disabled',false);
        $('#not-valid-text').hide();
    }else{
        $('#btnAddUser').attr('disabled',true);
        $('#not-valid-show').hide();
    }
  });
 


$('#expireCheck').change(function() {
    if($(this).is(':checked')) {
            $('#expire_date_form').show();
    }else{
       $('#expire_date_form').hide();
       }
});

//$('#modal-invite-user').attr('tabindex',-1);
    $( document ).ready(function() {
        let newOption = new Option('$sitename ($sitecode)','$sitecode',true,true);
        $('#cnuserform-sitecode').append(newOption).trigger('change');
        newOption = new Option('Default','1524628831068141000',true,true);
        $('#cnuserform-department').append(newOption).trigger('change');
    });
  
    $('#frmInviteEmail').on('beforeSubmit', function(e) {
        var \$form = $(this);
        $('#btnAddUser').attr('disabled', true);
        $.post(
            \$form.attr('action'), //serialize Yii2 form
            \$form.serialize()
        ).done(function(result) {
            console.log(result);
            if(result.status == 'success') {
                " . SDNoty::show('result.message', 'result.status') . "
                $('#btnAddUser').attr('disabled', false); 
                 $('#modal-invite-user').modal('toggle');
                 initUser();
                 location.reload();
            } else {
                " . SDNoty::show('result.message', 'result.status') . ";
                $('#btnAddUser').attr('disabled', false);     
            } 
        }).fail(function() {
            " . SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') . "
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

"); ?>