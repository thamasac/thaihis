<?php
use yii\helpers\Html;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\widgets\ModalForm;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
echo ModalForm::widget([
    'id' => 'modal-export',
    'size' => 'modal-sm',
]);
?>

<div class="" style="text-align: center;">
    <?= Html::label(Yii::t('subject', 'Please wait to be downloading ...', ['class'=>'label_warning_export']), '')?>
</div>
<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);

//$project_id = "1520742111042203500";
?>
<script>
$(function(){
    var label = $('.label_warning_export');
    var url = '<?= yii\helpers\Url::to([
        '/subjects/schedule-config/export-visit-schedule'
    ])?>';
            
    $.get(url,{},function(result){
        var data = JSON.parse(result);
        $('#modal-export .modal-content').html(data.html);
        $('#modal-export').modal('hide');
    });
});

</script>
<?php \richardfan\widget\JSRegister::end(); ?>