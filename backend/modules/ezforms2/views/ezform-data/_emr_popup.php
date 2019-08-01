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
    <h3 class="modal-title" id="itemModalLabel"><?= backend\modules\ezforms2\classes\EzfUiFunc::getEzformIcon($modelEzf)?> <?= $modelEzf->ezf_name ?> <small><?= $modelEzf->ezf_detail ?></small> 
    </h3>
    
</div>
<div class="modal-body">
  <?php
  $uiView = EzfHelper::ui($modelEzf->ezf_id)
                ->target($target)
                ->reloadDiv('modal-divemr-'.$modelEzf->ezf_id)
                ->modal('modal-'.$modelEzf->ezf_id)
                ->disabled($disabled)
                ->addbtn($addbtn)
                ->db2($db2)
                ->title($title);
  
        echo $uiView->buildEmrGrid();
  ?>
</div>
<div class="modal-footer">
<?= Html::button('<i class="glyphicon glyphicon-remove"></i> '.Yii::t('app', 'Close'), ['class' => 'btn btn-default', 'data-dismiss'=>'modal']) ?>    
</div>

<?php
$sub_modal = '<div id="modal-'.$modelEzf->ezf_id.'" class="fade modal" role="dialog"><div class="modal-dialog modal-xxl"><div class="modal-content"></div></div></div>';
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

$('#$modal .modal-dialog').removeClass('popup-size');

$('#ezf-modal-box').append('$sub_modal');    
        
$('#modal-{$modelEzf->ezf_id}').on('hidden.bs.modal', function(e){
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
        
function modal_{$modelEzf->ezf_id}(url) {
    $('#modal-{$modelEzf->ezf_id} .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    $('#modal-{$modelEzf->ezf_id}').modal('show')
    .find('.modal-content')
    .load(url);
}


");

?>