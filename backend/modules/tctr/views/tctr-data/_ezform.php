<?php

use backend\modules\ezforms2\classes\EzActiveForm;
use yii\helpers\Html;
use backend\modules\ezforms2\classes\EzfFunc;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use yii\helpers\Url;
use appxq\sdii\utils\SDUtility;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>

<?php
$formName = 'ezform-' . $ezf_id;
$form = EzActiveForm::begin([
            'id' => $formName,
            'action' => ['/ezforms2/ezform-data/ezform',
                'ezf_id' => $ezf_id,
                'modal' => $modal,
                'dataid' => $dataid,
                'initdata' => $initdata,
                'reloadDiv' => $reloadDiv,
                'v' => $modelVersion['ver_code'],
                'db2' => $db2,
            ],
            'options' => [
                'enctype' => 'multipart/form-data',
                'data-ezf_id' => $ezf_id,
                'data-dataid' => $dataid,
                'data-modal' => $modal,
                'data-reloadDiv' => $reloadDiv,
                'data-initdata' => $initdata,
                'data-v' => $modelVersion['ver_code'],
                'data-db2' => $db2,
            ]
        ]);

?>
<div id="print-<?= $modelEzf->ezf_id ?>" style="background-color: #fff">
<div class="modal-header" style="background-color: #fff">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3 class="modal-title" id="itemModalLabel"><?= $modelEzf->ezf_name ?> <small><?= $modelEzf->ezf_detail ?></small>
        
            <?php if($modelEzf->enable_version==1){
                $model_version = backend\modules\ezforms2\classes\EzfQuery::getEzformVersionApprovList($ezf_id);
                ?>
            <div class="btn-group">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >
                 <?=($modelVersion['ver_active']==1?'<span class="glyphicon glyphicon-star" aria-hidden="true"></span>':'')?> <?=$modelVersion['ver_code']?>  <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" >
                  <?php
                  if(isset($model_version) && !empty($model_version)){
                      foreach ($model_version as $key => $value) {
                          ?>
                        <li class="<?=($value['ver_code']==$modelVersion['ver_code'])?'active':''?>"><a class="ezform-main-open" data-modal="<?=$modal?>" data-url="<?= Url::to(['/ezforms2/ezform-data/ezform', 
                          'ezf_id'=>$ezf_id,
                          'dataid'=>$dataid,  
                          'v'=>$value['ver_code'],  
                          'modal'=>$modal,
                          'reloadDiv'=>$reloadDiv,
                          'initdata'=>EzfFunc::arrayEncode2String($initdata),
                          'target'=>$target,
                          'targetField'=>$targetField,
                          ])?>">
                                       <?=($value['ver_active']==1?'<span class="glyphicon glyphicon-star" aria-hidden="true"></span>':'')?> <?=$value['ver_code']?> 
                          </a></li>
                          <?php
                      }
                  }
                  ?>
                </ul>
              </div>
            <?php 
            }
            ?>
      
            <button type="button" class="btn btn-info ezform-main-open" data-modal="<?=$modal?>" data-url="<?= Url::to(['/ezforms2/ezform-data/history', 
            'ezf_id'=>$ezf_id,
            'modal'=>$modal,
            'reloadDiv'=>$reloadDiv,
            'initdata'=>EzfFunc::arrayEncode2String($initdata),
            'target'=>$target,
            'targetField'=>$targetField,
            ])?>"><i class="glyphicon glyphicon-list"></i> <?= Yii::t('ezform', 'History')?></button>
    </h3>    
</div>
<div class="modal-body">
  <ul class="nav nav-tabs" role="tablist" style="margin-bottom: 10px;">
        <li role="presentation" class="active"><a href="#form" aria-controls="form" role="tab" data-toggle="tab"><?=Yii::t('app', 'Form')?></a></li>
        <li role="presentation"><a href="#discuss" aria-controls="discuss" role="tab" data-toggle="tab"><?=Yii::t('app', 'Discuss')?></a></li>
    </ul>

  <!-- Tab panes -->
  <div class="tab-content">
      <div role="tabpanel" class="tab-pane active" id="form">
        <div id="formPanel-<?= $modelEzf->ezf_id ?>" class="row">
            <?php
            echo \backend\modules\ezforms2\classes\EzfUiFunc::renderEzform($modelFields, Yii::$app->session['ezf_input'], $form, $model, $modelEzf, $this, $disable);
            ?>
        </div>
    </div>
      <div role="tabpanel" class="tab-pane" id="discuss">
        <?= backend\modules\ezforms2\classes\CommunityBuilder::Community()->type('ezform')->object_id($modelEzf->ezf_id)->dataid($dataid)->buildCommunity();?>
    </div>
  </div>
  
    
    
</div>
   </div> 
<div class="modal-footer" >
    <?php
    appxq\sdii\assets\Html2CanvasAsset::register($this);
    echo Html::button('<i class="glyphicon glyphicon-print"></i>', [
        'id'=>'h2c',
        'class'=>'btn btn-default ', 
        'target'=>'_blank', 
    ]);     
    ?>

<?php \richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]); ?>
<script>
    
    $('#h2c').click(function(){
        html2canvas($('#print-<?= $modelEzf->ezf_id ?>'), {
            onrendered: function(canvas) {
                var img = canvas.toDataURL('image/png');
                $.ajax({
                    method: 'POST',
                    url:'<?= Url::to(['/ezforms2/drawing/canvas-image'])?>',
                    data: {type: 'data', image: img, name: 'h2c', '_csrf':'<?=Yii::$app->request->getCsrfToken()?>'},
                    dataType: 'JSON',
                    success: function(result, textStatus) {
                        if(result.status == 'success') {
                            window.location.href = result.path+result.data;
                            
                        } else {
                            <?= SDNoty::show('result.message', 'result.status')?>
                        }
                    }
                });
            }
        });
    });
       
</script>
<?php \richardfan\widget\JSRegister::end(); ?>
    
    <?= Html::hiddenInput('submit', $model->rstat == 0 ? 1 : $model->rstat, ['id' => 'submit-form']) ?>
    <?= EzfFunc::genBtnEzform($model, $modelEzf) ?>
    <?= Html::button('<i class="glyphicon glyphicon-remove"></i> '.Yii::t('app', 'Close'), ['class' => 'btn btn-default', 'data-dismiss' => 'modal']) ?>    
</div>
<?php EzActiveForm::end(); ?>



<?php
$jsAddon = '';
if ($reloadDiv != '') {
    $jsAddon = "
            var urlreload =  $('#$reloadDiv').attr('data-url');
            $('#$reloadDiv').attr('data-dataid','$dataid');
            urlreload = urlreload + '&dataid=' + '$dataid'
            if(urlreload){
                $.ajax({
                    method: 'POST',
                    url: urlreload,
                    dataType: 'HTML',
                    success: function(result, textStatus) {
                        $('#$reloadDiv').html(result);
                    }
                });
            }
            ";
}

$disabledInput = '';

if ($model->rstat == 2) {
    $disabledInput = "$('#formPanel-{$modelEzf->ezf_id} input, #formPanel-{$modelEzf->ezf_id} select, #formPanel-{$modelEzf->ezf_id} textarea').attr('disabled', true); $('#formPanel-{$modelEzf->ezf_id} input[type=\"hidden\"]').attr('disabled', false);";
}

$this->registerJs("

{$modelEzf->ezf_js}
   
$disabledInput
    
$('#$formName .btn-submit').click(function(){
    $('#$formName .btn-submit').attr('disabled', true);
    setTimeout(function(){ $('#$formName .btn-submit').attr('disabled', false); }, 3000);
    $('#$formName #submit-form').val($(this).val());
    $('#$formName .btn-submit').submit();
});

$('form#$formName').on('beforeSubmit', function(e) {
    
    var \$form = $(this);
    var formData = new FormData($(this)[0]);

    $.ajax({
          url: \$form.attr('action'),
          type: 'POST',
          data: formData,
	  dataType: 'JSON',
	  enctype: 'multipart/form-data',
	  processData: false,  // tell jQuery not to process the data
	  contentType: false,   // tell jQuery not to set contentType
          success: function (result) {
	    if(result.status == 'success') {
                " . SDNoty::show('result.message', 'result.status') . "
                $(document).find('#{$modal}').modal('hide');
                $jsAddon
                
            } else {
                " . SDNoty::show('result.message', 'result.status') . "
                    $('#$formName .btn-submit').attr('disabled', false);
            } 
          },
          error: function () {
            " . SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') . "
                $('#$formName .btn-submit').attr('disabled', false);
	    console.log('server error');
          }
      });
      
    return false;
});

");
?>