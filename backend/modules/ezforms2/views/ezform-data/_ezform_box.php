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
                'dataid' => "$dataid",
                'initdata' => $initdata,
                'reloadDiv' => $reloadDiv,
                'v' => $modelVersion['ver_code'],
                'db2' => $db2,
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
            ]
        ]);

?>

<div class="modal-header" <?=$ezf_box==3 || $ezf_box==4?'style="display: none;"':''?>>
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
<div class="modal-body">
  <?php
    //echo $form->errorSummary($model);

    $html_error = '<div class="error-summary" style="{style_display}"><p>'.Yii::t('yii', 'Please fix the following errors:').'</p><ul>{error_items}</ul></div>';
    $error_items = '';
    $style_display = 'display: none;';
    $sql_error = SDUtility::string2Array($model->error);
    if(isset($sql_error) && !empty($sql_error)){
        foreach ($sql_error as $key_error => $value_error) {
            $error_items .= "<li>{$value_error}</li>";
            $style_display = 'display: block;';
        }
    }

    echo strtr($html_error, ['{error_items}' => $error_items, '{style_display}' => $style_display]);
    ?>

    <div id="formPanel-<?= $modelEzf->ezf_id ?>" class="row">
        <?php
        echo \backend\modules\ezforms2\classes\EzfUiFunc::renderEzform($modelFields, Yii::$app->session['ezf_input'], $form, $model, $modelEzf, $this, $disable);
        ?>
    </div>
  
    
    
</div>
   
<div class="modal-footer" <?=$ezf_box==2 || $ezf_box==4?'style="display: none;"':''?> >
    
    <?= Html::hiddenInput('submit', $model->rstat == 0 ? 1 : $model->rstat, ['id' => 'submit-form']) ?>
    <?= EzfFunc::genBtnEzform($model, $modelEzf, ($model->rstat==1 && empty($sql_error))) ?>
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

if(!empty($reloadPage)){
    $reloadPage = strtr($reloadPage, ['{target}'=>$target, '{dataid}'=>$dataid]);
    
    $jsAddon .= "
        let pageUrl = '$reloadPage';
        window.location.href = pageUrl;
        ";
}

$options = SDUtility::string2Array($modelEzf->ezf_options);
$enable_after_save = isset($options['after_save']['enable'])?$options['after_save']['enable']:0;
if($enable_after_save){
    if(isset($options['after_save']['js']) && $options['after_save']['js']!=''){
        $jsAddon .= $options['after_save']['js'];
    }
}

$disabledInput = '';

if ($model->rstat == 2) {
    $disabledInput = "$('#formPanel-{$modelEzf->ezf_id} input, #formPanel-{$modelEzf->ezf_id} select, #formPanel-{$modelEzf->ezf_id} textarea').attr('disabled', true); $('#formPanel-{$modelEzf->ezf_id} input[type=\"hidden\"]').attr('disabled', false);";
}

//Available events are: ===JS===
//
//beforeValidate.
//afterValidate.
//beforeValidateAttribute.
//afterValidateAttribute.
//beforeSubmit.
//ajaxBeforeSend.
//ajaxComplete.

$this->registerJs("

{$modelEzf->ezf_js}
   
$disabledInput
    
$('#$formName').on('afterValidate', function (e) {
    let scroll = $('#$formName .form-group.has-error').offset();
    if(scroll){
        $('#$modal').animate({ scrollTop: scroll.top }, 300);
    }
});
    
$('#$formName .btn-submit').click(function(){
    $('#$formName .btn-submit').attr('disabled', true);
    setTimeout(function(){ $('#$formName .btn-submit').attr('disabled', false); }, 3000);
    $('#$formName #submit-form').val($(this).val());
    $(this).submit();
});

$('input[type=\"radio\"]:checked').addClass(\"imChecked\");

$('input[type=\"radio\"]').click(function(){
    thisRadio = $(this);
    if (thisRadio.hasClass(\"imChecked\")) {
        thisRadio.removeClass(\"imChecked\");
        thisRadio.prop('checked', false);
    } else { 
        thisRadio.prop('checked', true);
        $('input[name=\"'+thisRadio.attr('name')+'\" ]').removeClass(\"imChecked\");
        thisRadio.addClass(\"imChecked\");
    };
    
    thisRadio.trigger(\"change\");
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
                if(!result.data.error){
                    $(document).find('#{$modal}').modal('hide');
                }
                
                if(result.data.error){
                    $('#{$modal}').modal('show').find('.modal-content').load(\$form.attr('action'));
                }
                
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
