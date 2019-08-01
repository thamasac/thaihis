<?php
?>
 
<div class="row">
    <div class="col-md-6 col-md-offset-3">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <i class="fa fa-refresh" aria-hidden="true"></i> Email Verification
            </div>
            <div class="panel-body">
                <div class="alert alert-info">
                    A verification email has been sent to <strong><?= $email ?></strong> <br/>
                    Please activate your account with the link in this email. If you cannot fine the email, please also check the Junk/Spam folder!
                </div>

                <div id="show-resend"></div>

            </div>
        </div>
    </div>
</div>




<?php 
$this->registerJs("
    showresend=function(){
        let url = '".yii\helpers\Url::to(['/user/resend'])."';
        $.get(url , function(data){
            $('#show-resend').html(data);
        });    
    }    
    showresend();
");
?>
