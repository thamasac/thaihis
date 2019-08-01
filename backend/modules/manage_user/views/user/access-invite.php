<?php 
use yii\helpers\Html;
use yii\widgets\ActiveForm;
//['token'=>$user['id'], 'site'=>$site_detail, 'email'=>$email, 'project_name'=>$project_name]
    $auty_key = \cpn\chanpan\classes\CNUser::getEmail();
    $auty_key = cpn\chanpan\classes\CNEncript::encrypt_decrypt('encrypt', $data['email']);
    $project_url = cpn\chanpan\classes\utils\CNDomain::getCurrentProjectUrl();
    $project_url = "https://{$project_url}/site/index?auth_key={$auty_key}";
    $projectName = \cpn\chanpan\classes\utils\CNProject::getProjectName();
?>

<div class="row">
    <div class="col-md-6 col-md-offset-3">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?= Html::encode("Invitation Confirmation") ?></h3>
            </div>
            <div class="panel-body">
                <div class="alert alert-info">
                    <p class="text-center">
                        <?=
                        Yii::t('user', 'In order to finish your invitation, Please confirm to accept invite for this information below ') ?>
                        .
                    </p>
                    <br>
                    <div class="text-center">
                        <span class="badge badge-pill badge-primary">Project Title</span>
                        <h3 style="margin-top: 2px;"> <?= $projectName ?> </h3>
                        <hr>
                        <span class="badge badge-pill badge-primary">e-Mail Address</span>
                        <h3 style="margin-top: 2px;"><?= $data['email'] ?></h3>
                    </div>

                </div>
                <?php $form = ActiveForm::begin([
                    'id' => 'connect-account-form',
                ]); ?>
     
                <?= Html::hiddenInput('id', $data['token'], ['class'=>'form-control', 'id'=>'id'])?>

                <?= Html::button(Yii::t('user', 'Yes, Please proceed.'), ['data-action'=>'accept','class' => 'btnAccept btn btn-success btn-block', 'data-id'=>$data['token']]) ?>
                <?= Html::button(Yii::t('user', 'No, I reject this invitation. '), ['data-action'=>'reject','class' => 'btnAccept btn btn-danger btn-block', 'name' => "reject-invite", 'data-id'=>$data['token']]) ?>
                <?php ActiveForm::end(); ?>


            </div>
        </div>
    </div>
</div>

<?php \richardfan\widget\JSRegister::begin();?>
<script>
    $('.btnAccept').on('click', function(){
        let id = $(this).attr('data-id');
        let action = $(this).attr('data-action');
        let value = '';
        if(action === 'accept'){
            value = '1';
        }else if(action === 'reject'){
            value = '2';
        }
        let url = '/manage_user/user/save-user-project';
        $.post(url, {id:id, value:value}, function(data){             
            if(data.status == 'success'){                
                <?= \appxq\sdii\helpers\SDNoty::show('data.message', 'data.status')?>
                setTimeout(function(){
                    if(data['data']['status'] == true){
                        location.href = '<?= $project_url?>';
                    }else{
                        location.href = '/site/index';
                    }                    
                },1000);
            }
            else 
            {
                <?= \appxq\sdii\helpers\SDNoty::show('data.message', 'data.status')?>
                location.href = '/site/index';
            }
        });
        return false;
    })
</script>
<?php \richardfan\widget\JSRegister::end();?>

