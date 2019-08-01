<?php
// start widget builder

/* Params widget สามารถใช้งาน ตัวแปรต่อไปนี้ได้
'options' => $options,
'widget_config' => $widget_config,
'model' => $model, 
'modelOrigin'=>$modelOrigin,
'menu' => $menu,
'module' => $module,
'addon' => $addon,
'filter' => $filter,
'reloadDiv' => $reloadDiv,
'dataFilter' => $dataFilter,
'modelFilter' => $modelFilter,
'target' => $target,
    */
  
?>
<?php if (\Yii::$app->user->can('administrator')): ?>
<div class="col-md-4" style="padding-left: 0px">
    <!--<button class="btn btn-success" id="btnAddUser"><i class="fa fa-plus"></i> <?= Yii::t('chanpan', 'Add user')?></button>-->
    <!--<button class="btn btn-success" id="importNCRC"><i class="fa fa-plus"></i> <?= Yii::t('chanpan', 'Invite user from nCRC');?></button>-->
    <?php 
        $invite_main = isset(\Yii::$app->params['invite_main']) ? \Yii::$app->params['invite_main'] : 'nCRC';
        echo yii\bootstrap\ButtonDropdown::widget([
            'label' => '<i class="fa fa-plus"></i> '.Yii::t('chanpan', 'Invite Members'),
            'dropdown' => [
                'items' => [
                    ['label' => Yii::t('chanpan', 'Invite Members from Thai Care Cloud'), 'url'=>'#', 'options'=>['id'=>'importTCC']],
                    ['label' => Yii::t('chanpan', 'Invite Members from ').$invite_main, 'url'=>'#','options'=>['id'=>'importNCRC']],
                    ['label' => Yii::t('chanpan', ' Invite by email'), 'url'=>'#','options'=>['id'=>'invitationButton']],
                ],
            ],
            'encodeLabel'=>false,
            'options'=>['class'=>'btn btn-success', 'id'=>'btnImportUser'],
        ]);
        if(Yii::$app->user->can('administrator')){
//            echo ' ';
//            if(\Yii::$app->user->can('administrator')){
//                 echo \yii\helpers\Html::a(Yii::t('user', 'Monitor Users')." <i class='fa fa-share'></i>", ['/manage_user/user/monitor-users'], ['class'=>'btn btn-primary btn-monitor-user']);
//            }
           
        }
 
       // echo "<span id='invite1234'>Invite</span>";
    ?>
</div>


<?php endif; ?>
<div class="clearfix" style="margin-bottom:20px;"></div>
<div class="table-responsive">
    <div id="view-user"></div>
</div>
<?php 
    echo \appxq\sdii\widgets\ModalForm::widget([
        'id' => 'modal-user',
        'size' => 'modal-lg',
        'tabindexEnable' => FALSE,
    ]);
?>

<?php
$this->registerJS("
    $('.btn-monitor-user').on('click', function(){
        let url = $(this).attr(href);
        window.location = url;
        return false;
    });
    InviteUser1234=function(){
        let url = '/manage_user/user/index';
        $.get(url, function(data){
             $('#invite1234').html(data);
        });  
    } 
    //InviteUser1234(); 
    function getSearch(){
        let frm = $('#frmManagerProject').serialize();
        console.log(frm);
        let url = '".yii\helpers\Url::to(['/user/admin/index'])."';
        $.post(url,frm, function(data){
            $('#view-user').html(data);
        });
        return false;
    }
    
    initUser=function(){        
        let url = '".yii\helpers\Url::to(['/user/admin/index'])."';
        $.get(url, function(data){
            $('#view-user').html(data);
        }).fail(function(err) {
             err = JSON.parse(JSON.stringify(err))['responseText'];
             $('#view-user').html(`<div class='alert alert-danger'>\${err}</div>`);
        });
    }
    initUser();
    $('#btnAddUser').click(function(){
        let url ='".\yii\helpers\Url::to(['/user/admin/create'])."';
        modalUser(url); 
    });
    $('#importTCC').click(function(){
        let url ='".\yii\helpers\Url::to(['/manage_user/user/create?db_type=tcc'])."';
        modalUser(url); 
    });
    $('#importNCRC').click(function(){
        let url ='".\yii\helpers\Url::to(['/manage_user/user/create?db_type=ncrc'])."';
        modalUser(url); 
    });
    $('#invitationButton').click(function(){
        let url ='".\yii\helpers\Url::to(['/manage_user/user/create-project-invitation-view'])."';
        modalUser(url); 
    });

    
    function modalUser(url) {
        $('#modal-user .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
        $('#modal-user').modal('show')
        .find('.modal-content')
        .load(url);
    }

");
?>


 
