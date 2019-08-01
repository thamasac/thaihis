<?php

use backend\modules\ezforms2\classes\EzActiveForm;
use yii\helpers\Html;
use backend\modules\ezforms2\classes\EzfFunc;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use yii\helpers\Url;
use appxq\sdii\utils\SDUtility;
use backend\modules\ezforms2\classes\EzfStarterWidget;


/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<?php EzfStarterWidget::begin(); ?>
<div id="<?=$reloadDiv?>">

<?php
$formName = 'ezform-' . $ezf_id;
$form = EzActiveForm::begin([
            'id' => $formName,
            'action' => ['/ezforms2/ezform-data/ezform-redirect',
                'ezf_id' => $ezf_id,
                'modal' => $modal,
                'dataid' => "$dataid",
                'initdata' => $initdata,
                'reloadDiv' => $reloadDiv,
                'v' => $modelVersion['ver_code'],
                'db2' => $db2,
                'redirect' => $redirect,
            ],
            'options' => [
                'enctype' => 'multipart/form-data',
                'data-ezf_id' => $ezf_id,
                'data-dataid' => "$dataid",
                'data-modal' => $modal,
                'data-reloadDiv' => $reloadDiv,
                'data-initdata' => $initdata,
                'data-v' => $modelVersion['ver_code'],
                'data-db2' => $db2,
                'autocomplete'=>'off',
                'data-db2' => $db2,
                'data-redirect' => $redirect,
            ]
        ]);

?>
<div id="print-<?= $modelEzf->ezf_id ?>" style="background-color: #fff; border-radius: 6px;">
<div class="modal-header">
    <h3 class="modal-title" id="itemModalLabel"><?= backend\modules\ezforms2\classes\EzfUiFunc::getEzformIcon($modelEzf)?> <?= $modelEzf->ezf_name ?> <small><?= $modelEzf->ezf_detail ?></small>
        
            <?php if($modelEzf->enable_version==1){
                $model_version = backend\modules\ezforms2\classes\EzfQuery::getEzformVersionApprovList($ezf_id);
                ?>
            <div class="btn-group btn-auth-version">
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
      
    </h3>    
</div>
  <div class="row" style="margin-top: 10px;">

  <!-- Tab panes -->
  <?php
    echo \backend\modules\ezforms2\classes\EzfUiFunc::renderEzform($modelFields, Yii::$app->session['ezf_input'], $form, $model, $modelEzf, $this, $disable);
  ?>
    
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
</div>
<?php EzActiveForm::end(); ?>
  
</div>
  
<?php EzfStarterWidget::end(); ?>

<?php
$jsAddon = '';

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

    $('form#$formName').submit();
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
                
            } else {
                
            } 
            " . SDNoty::show('result.message', 'result.status') . "
          },
          error: function () {
                $('#$formName .btn-submit').attr('disabled', false);
	    console.log('server error');
          }
      });
      
    return false;
});
");
?>
