<?php
use yii\helpers\Html;
use yii\helpers\Url;
use appxq\sdii\helpers\SDNoty;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

?>
<div style="margin: 10px;padding: 10px;border: 1px solid #ccc;border-radius: 6px;">
    <div class="form-group">
        <label class="control-label" ><?=Yii::t('ezform', 'Password')?></label>
        <?= Html::textInput('ezpw', isset(Yii::$app->session["ezpw_{$ezf_id}_{$field}"])?Yii::$app->session["ezpw_{$ezf_id}_{$field}"]:'', ['class'=>'form-control pw', 'type'=>'password', 'autocomplete'=>'new-password'])?>
    </div>
    <div class="form-group ">
        <?= Html::checkbox('ezpw_save', isset(Yii::$app->session["ezpw_{$ezf_id}_{$field}"])?1:0, ['id'=>'ezpw_save','class'=>'pw_save','label'=> Yii::t('ezform', 'Remember me next time')])?>
    </div>
    <div class="form-group ">
        <?= Html::a('<i class="fa fa-unlock"></i>', '#', ['class'=>'btn btn-warning btn-sendpw'])?>
    </div>
</div>  

<?php \richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]); ?>
<script>
    // JS script
    $('.btn-sendpw').click(function(){
        var btn = $(this);
        
        $.ajax({
            method: 'POST',
            url:'<?=Url::to(['/ezforms2/ezform/form-oauthe', 'ezf_id'=>$ezf_id, 'v'=>$v, 'field'=>$field, 'dataid'=> $dataid, 'action'=> \backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String($action), 'reloadDiv'=>$reloadDiv])?>',
            data:{ezpw:btn.parent().parent().parent().find('.pw').val(), ezpw_save:btn.parent().parent().parent().find('.pw_save:checked').val()},
            dataType: 'JSON',
            success: function(result, textStatus) {
                if(result.status == 'success') {
                    btn.parent().parent().parent().parent().html(result.html);
                    <?php
                    if($reloadDiv!=''){
                        echo "
                            if(btn.parent().parent().parent().find('.pw_save:checked').val()){
                                var urlreload =  $('#$reloadDiv').attr('data-url');        
                                getUiAjaxReload(urlreload, '$reloadDiv');
                            }
                            
                        ";
                    }
                    ?>
                } else {
                    <?=SDNoty::show('result.message', 'result.status')?>
                }
            }
        });
        
        return false;
    });
    
    function getUiAjaxReload(url, divid) {
        $.ajax({
            method: 'POST',
            url: url,
            dataType: 'HTML',
            success: function(result, textStatus) {
                $('#'+divid).html(result);
            }
        });
    }
</script>
<?php \richardfan\widget\JSRegister::end(); ?>