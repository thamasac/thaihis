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
    <h3 class="modal-title" id="itemModalLabel"><?= backend\modules\ezforms2\classes\EzfUiFunc::getEzformIcon($ezform)?> 
<?php
$co_dev = \appxq\sdii\utils\SDUtility::string2Array($ezform['co_dev']);
            if(Yii::$app->user->can('administrator') || $ezform['created_by'] == Yii::$app->user->id || in_array(Yii::$app->user->id, $co_dev)){
                echo '<a class="" href="'.Url::to(['/ezbuilder/ezform-builder/update', 'id'=>$ezform['ezf_id']]).'" target="_blank"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
            } 
?>
 <?= $ezform->ezf_name ?> <small><?= $ezform->ezf_detail ?></small> 
        
      <?php
      echo Html::a('<span class="glyphicon glyphicon-export"></span> '.Yii::t('ezform', 'Export Data'), Url::to(['/ezforms2/data-lists/export',
                                            'ezf_id' => $ezform->ezf_id,
                                            'fsys'=>1
                                        ]), [
                                    'class' => 'btn btn-warning',
                                    
                                    ])
      ?>
      <button type="button" class="btn btn-<?=$varname==1?'info':'default'?> ezform-main-open" data-modal="<?=$modal?>" data-url="<?= Url::current(['varname'=>$varname==1?0:1])?>"><i class="glyphicon glyphicon-<?=$varname==1?'check':'unchecked'?>"></i> <?= Yii::t('ezform', 'Variable Header')?></button>
      <button type="button" class="btn btn-<?=$rawdata==1?'info':'default'?> ezform-main-open" data-modal="<?=$modal?>" data-url="<?= Url::current(['rawdata'=>$rawdata==1?0:1])?>"><i class="glyphicon glyphicon-<?=$rawdata==1?'check':'unchecked'?>"></i> <?= $rawdata==1?Yii::t('ezform', 'Display descriptions'):Yii::t('ezform', 'Display coded data')?></button>
    </h3>
    
</div>
<div class="modal-body">
    <?php // EzfHelper::uiGrid($ezform->ezf_id, $target, 'modal-divview-'.$ezform->ezf_id, 'modal-'.$ezform->ezf_id, $data_column, $disabled, $targetField) ?>
  <?php
  $uiView = EzfHelper::ui($ezform->ezf_id)
                ->target($target)
                ->reloadDiv('modal-divview-'.$ezform->ezf_id)
                ->modal('modal-'.$ezform->ezf_id)
                ->data_column($data_column)
                ->disabled($disabled)
                ->targetField($targetField)
                ->default_column($default_column)
                ->pageSize($pageSize)
                ->order_column($order_column)
                ->search_column($search_column)
                ->addbtn($addbtn)
                ->db2($db2)
                ->title($title)
                ->orderby($orderby)
                ->header($header)
                ->filter($filter)
                ->actions($actions);
  
  if($varname==1){
      $uiView->varname();
  }
  
  if($rawdata==1){
      $uiView->rawdata();
  }
  
  if($db2==1){
      echo $uiView->buildDb2Grid();
  } else {
      echo $uiView->buildGrid();
  }
  
 ?>
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

$('#$modal .modal-dialog').removeClass('popup-size');

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