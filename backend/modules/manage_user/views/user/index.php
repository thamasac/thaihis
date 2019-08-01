<?php
echo \yii\bootstrap\Html::button("<i class=\"fa fa-envelope\"></i> Invite by email",[
    "style"=>"margin-left:5px",
    "id"=>"invitationButton",
    'class'=>'btn btn-default'
    ])
?>

<?php

echo yii\bootstrap\Modal::widget([
    'id' => 'modal-invite-user',
    'size' => 'modal-lg',
    'options' => ['tabindex' => FALSE],
]);
?>

<?php

$this->registerJS("
      $('#modal-invite-user').on('hidden.bs.modal', function () {
        $('#modal-invite-user').remove();
        InviteUser1234();        
      });
      $('#invitationButton').click(function(){
        let url ='/manage_user/user/create-project-invitation-view';
        $('#modal-invite-user').modal('show');
        $('#modal-invite-user .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
        
        $.get(url, function(data){
              $('#modal-invite-user .modal-content').html(data);
        });
    });
");
?>

