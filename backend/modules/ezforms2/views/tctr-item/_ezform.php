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
            ],
            'options' => [
                'enctype' => 'multipart/form-data',
                'data-ezf_id' => $ezf_id,
                'data-dataid' => $dataid,
                'data-modal' => $modal,
                'data-reloadDiv' => $reloadDiv,
                'data-initdata' => $initdata,
                
            ]
        ]);
?>
<div id="print-<?= $modelEzf->ezf_id ?>" style="background-color: #fff">
<div class="modal-header" style="background-color: #fff">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3 class="modal-title" id="itemModalLabel"><?= $modelEzf->ezf_name ?> <small><?= $modelEzf->ezf_detail ?></small></h3>
    
</div>
<div class="modal-body">
    
    <div id="formPanel-<?= $modelEzf->ezf_id ?>" class="row">
        <?php
        foreach ($modelFields as $field) {
            if ($field['ezf_field_type'] > 0) {

                if (isset($field['ezf_field_ref']) && $field['ezf_field_ref'] > 0) {
                    $cloneRefField = EzfFunc::cloneRefField($field);
                    $field = $cloneRefField['field'];
                    $disabled = $cloneRefField['disabled'];
                    if($disabled){
                        $disable[$field['ezf_field_name']]=$disabled;
                    }
                }


                $dataInput;
                if (isset(Yii::$app->session['ezf_input'])) {
                    $dataInput = EzfFunc::getInputByArray($field['ezf_field_type'], Yii::$app->session['ezf_input']);
                }

                $disabled = isset($disable[$field['ezf_field_name']]) ? 2 : 0;

                echo EzfFunc::generateInput($form, $model, $field, $dataInput, $disabled);
                if ($field['ezf_condition'] == 1) {
                    EzfFunc::generateCondition($model, $field, $modelEzf, $this, $dataInput);
                }

                if (isset($field['ezf_field_cal']) && $field['ezf_field_cal'] != '') {
                    $cut = preg_match_all("%{(.*?)}%is", $field['ezf_field_cal'], $matches);
                    if ($cut) {
                        $varArry = $matches[1];
                        $createEvent = EzfFunc::genJs($varArry, $model, $field);
                        $this->registerJs($createEvent);
                    }
                }
            }
        }
        echo Html::activeHiddenInput($model, 'id');
        ?>
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
    
$('.btn-submit').click(function(){
    $('#submit-form').val($(this).val());
});

$('form#$formName').on('beforeSubmit', function(e) {
    $('#$formName .btn-submit').attr('disabled', true);
    
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