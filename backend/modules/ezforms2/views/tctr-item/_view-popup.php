<?php
use yii\helpers\Html;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use yii\helpers\Url;
use backend\modules\ezforms2\classes\EzfHelper;

Yii::$app->session['reload-div'] = $reloadDiv;
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3 class="modal-title" id="itemModalLabel"><?= $ezform->ezf_name ?> <small><?= $ezform->ezf_detail ?></small> 
        <?php
        if($addbtn){
            echo EzfHelper::btnAdd($ezform->ezf_id, $target, [], 'modal-divview-'.$ezform->ezf_id, 'modal-'.$ezform->ezf_id);
        }
        ?>
    </h3>
</div>
<div class="modal-body">
    <?= EzfHelper::uiGrid($ezform->ezf_id, $target, 'modal-divview-'.$ezform->ezf_id, 'modal-'.$ezform->ezf_id, $data_column, $disabled, $targetField) ?>
</div>
<div class="modal-footer">
<?= Html::button('<i class="glyphicon glyphicon-remove"></i> '.Yii::t('app', 'Close'), ['class' => 'btn btn-default', 'data-dismiss'=>'modal']) ?>    
</div>

<?php
$sub_modal = '<div id="modal-'.$ezform->ezf_id.'" class="fade modal" role="dialog"><div class="modal-dialog modal-xxl"><div class="modal-content"></div></div></div>';
$reloadJs = '';
if(isset(Yii::$app->session['reload-div'])){
    if(isset(Yii::$app->session['main-div'])){
        $mainDiv = Yii::$app->session['main-div'];
        $widgetDiv = Yii::$app->session['reload-div'];
        $reloadJs = "$('#{$mainDiv}').attr('data-reload', '$widgetDiv');";
    }
    unset(Yii::$app->session['reload-div']);
}

$this->registerJs("
$reloadJs
    
$('#ezf-modal-box').append('$sub_modal');    
        
$('#modal-{$ezform->ezf_id}').on('hidden.bs.modal', function(e){
    var hasmodal = $('body .modal').hasClass('in');
    if(hasmodal){
        $('body').addClass('modal-open');
    }
});

function getUiAjax(url, divid) {
    $.ajax({
        method: 'POST',
        url: url,
        dataType: 'HTML',
        success: function(result, textStatus) {
            $('#'+divid).html(result);
        }
    });
}    

function modal_{$ezform->ezf_id}(url) {
    $('#modal-{$ezform->ezf_id} .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    $('#modal-{$ezform->ezf_id}').modal('show')
    .find('.modal-content')
    .load(url);
}


");

?>