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
<div id="print-<?= $modelEzf->ezf_id ?>" style="background-color: #fff; border-radius: 6px;">
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3 class="modal-title" id="itemModalLabel"><?= backend\modules\ezforms2\classes\EzfUiFunc::getEzformIcon($modelEzf)?> 
        <?php
        $co_dev = \appxq\sdii\utils\SDUtility::string2Array($modelEzf['co_dev']);
            if(Yii::$app->user->can('administrator') || $modelEzf['created_by'] == Yii::$app->user->id || in_array(Yii::$app->user->id, $co_dev)){
                echo '<a class="" href="'.Url::to(['/ezbuilder/ezform-builder/update', 'id'=>$modelEzf['ezf_id']]).'" target="_blank"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
            } 
        ?>
        <?= $modelEzf->ezf_name ?> <small><?= $modelEzf->ezf_detail ?></small>
      
            <?php 
            
            
                if($modelEzf->enable_version==1){
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
      
            <button type="button" class="btn btn-info ezform-main-open btn-auth-view" data-modal="<?=$modal?>" data-url="<?= Url::to(['/ezforms2/ezform-data/history', 
            'ezf_id'=>$ezf_id,
            'modal'=>$modal,
            'reloadDiv'=>$reloadDiv,
            'initdata'=>EzfFunc::arrayEncode2String($initdata),
            'target'=>$target,
            'targetField'=>$targetField,
            ])?>"><i class="glyphicon glyphicon-list"></i> <?= Yii::t('ezform', 'Data Table')?></button>
    </h3>    
</div>
<div class="modal-body">
  <ul class="nav nav-tabs" role="tablist" style="margin-bottom: 10px;">
        <li role="presentation" class="active"><a href="#form-<?=$modelEzf->ezf_id?>" aria-controls="form-<?=$modelEzf->ezf_id?>" role="tab" data-toggle="tab"><?=Yii::t('app', 'Form')?></a></li>
        <?php if($modelEzf->consult_tools==2):?>
        <li role="presentation"><a href="#discuss-<?=$modelEzf->ezf_id?>" aria-controls="discuss-<?=$modelEzf->ezf_id?>" role="tab" data-toggle="tab"><?=Yii::t('app', 'Discuss')?></a></li>
        <?php endif;?>
        <?php if(in_array($modelEzf->query_tools, [2,3])):?>
        <li role="presentation"><a href="#querytool-<?=$modelEzf->ezf_id?>" aria-controls="querytool-<?=$modelEzf->ezf_id?>" role="tab" data-toggle="tab"><?=Yii::t('ezform', 'Query Tool')?></a></li>
        <?php endif;?>
    </ul>

  <!-- Tab panes -->
  <div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="form-<?=$modelEzf->ezf_id?>" >
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
      <div role="tabpanel" class="tab-pane" id="discuss-<?=$modelEzf->ezf_id?>">
        <?php
        if($modelEzf->consult_tools==2){
            echo backend\modules\ezforms2\classes\CommunityBuilder::Community()->type('ezform')->object_id($modelEzf->ezf_id)->dataid($dataid)->buildCommunity();
        } else {
            echo '<p class="lead" style="font-size: 16px;margin-bottom: 10px;">'.Yii::t('ezform', 'Close the consultant tool.').'</p>';
        }
        ?>
    </div>
    <div role="tabpanel" class="tab-pane" id="querytool-<?=$modelEzf->ezf_id?>">
        <?php
        if(in_array($modelEzf->query_tools, [2,3])){
            echo backend\modules\ezforms2\classes\CommunityBuilder::Community()->type('query_tool')->object_id($modelEzf->ezf_id)->dataid($dataid)->buildQueryTool();
        } else {
            echo '<p class="lead" style="font-size: 16px;margin-bottom: 10px;">'.Yii::t('ezform', 'Close the query tool.').'</p>';
        }
        ?>
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
                            let win = window.open(result.path+result.data, '_blank');
                            win.focus();
                            //window.location.href = result.path+result.data;
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
    <?= EzfFunc::genBtnEzform($model, $modelEzf, ($model->rstat==1 && empty($sql_error))) ?>
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

if(!empty($reloadPage)){
    $reloadPage = strtr($reloadPage, ['{target}'=>$target, '{dataid}'=>$dataid]);
    
    $jsAddon .= "
        let pageUrl = '$reloadPage';
        window.location.href = pageUrl;
        ";
}

$options = SDUtility::string2Array($modelEzf->ezf_options);
$popup_size = (isset($options['popup_size']) && !empty($options['popup_size']))?$options['popup_size'].'%':'';
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
$popup_css = $popup_size==''?'':"width: $popup_size;";//addClass removeClass
$this->registerCss("
    @media (min-width: 768px){
        #$modal .popup-size {
            $popup_css
        }
    }
");

$this->registerJs("

$('#$modal .modal-dialog').addClass('popup-size');

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
