<?php
$this->title = Yii::t('chanpan','EzProjects');
use kartik\tabs\TabsX;
use yii\helpers\Url;

$status_update = isset(Yii::$app->user->identity->profile->status_update) ? Yii::$app->user->identity->profile->status_update : '1';

$user_id = \cpn\chanpan\classes\CNUser::getUserId();
$checkSocial = false;//common\modules\user\classes\CNSocialFunc::isAuth($user_id);
 
if($checkSocial == true){
    $checkSocial =1;
}else{
    $checkSocial=0;
} 

echo $this->render("_menu");
?>

<?php 
    $modal_change_password = 'modal-change-password';
     
    echo yii\bootstrap\Modal::widget([
        'id'=>$modal_change_password,
        'size'=>'modal-md',
        'clientOptions' => ['backdrop' => 'static', 'keyboard' => false], 
        'options'=>['tabindex' => false]
    ]);
?>

<div id="all-project"></div>
<?php
$sitecode = common\modules\user\classes\CNSitecode::getSiteCodeCurrent();

$site = substr_replace(base64_encode($sitecode),  rand(0,9), 2, 0);
$site = str_replace("=", "*",$site);
 
$this->registerJs("
    localStorage.setItem('site', '".$site."');
    function initAllProject(){
        let url = '".yii\helpers\Url::to(['/manageproject/clone-project/all-project'])."';
        $('#all-project').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');    
        $.get(url, function(data){
            $('#all-project').html(data);
        });
        return false;
    }
    initAllProject();
    
");
?>

<?php \richardfan\widget\JSRegister::begin();?>
<?php backend\modules\ezforms2\assets\JLoading::register($this);?>
<script>
    function changePassword(){
        if(<?= $status_update?> == '1'){ //1 chanage password 
             if(<?= $checkSocial; ?> == '1'){ //login facebook
                $('#<?= $modal_change_password?>').modal('show');
                $('#<?= $modal_change_password?> .modal-content').html('<div class="sdloader "><i class="sdloader-icon"></i></div>');
                let url = '<?= Url::to(['/site/change-password'])?>';
                $.get(url, function(data){
                    $('#<?= $modal_change_password?> .modal-content').html(data);
                });
             }   
        }
    }
    changePassword();
    
    function onLoadings(ele){
        $(ele).waitMe({
            effect : 'facebook',
            text : 'Please wait...',
            bg : 'rgba(255,255,255,0.7)',
            color : '#000',
            maxSize : '',
            waitTime : -1,
            textPos : 'vertical',
            fontSize : '',
            source : '',
            onClose : function() {}
        });
    }
    function hideLoadings(ele){
         $(ele).waitMe("hide");
    } 
    
</script>
<?php \richardfan\widget\JSRegister::end();?>