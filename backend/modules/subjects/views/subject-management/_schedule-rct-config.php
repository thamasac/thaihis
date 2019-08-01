<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use backend\modules\ezforms2\classes\EzActiveForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
if (isset($ezf_id)) {
    if ($ezf_id == '')
        $ezf_id = '0';
}else{
    $ezf_id = '0';
}
$target = backend\modules\ezforms2\classes\EzfQuery::getTargetOne($ezf_id);
if ($target) {
    $itemsEzform = \backend\modules\ezforms2\classes\EzfQuery::getEzformRef($ezf_id, $target['parent_ezf_id']);
} else {
    $itemsEzform = \backend\modules\ezforms2\classes\EzfQuery::getEzformAll($ezf_id);
}

$key = $key_index;
if ($key == '')
    $key = \appxq\sdii\utils\SDUtility::getMillisecTime();

?>

<h4 class="modal-title" ><?= Yii::t('ezform', 'Randomization Visit') ?></h4>
<br/>
<!--config start-->
<div class="form-group row">
    <div class="col-md-6">
        <?= Html::label(Yii::t('ezmodule', 'Form Name'), 'options[form_name]', ['class' => 'control-label']) ?>
        <?php
        echo Html::input('text', 'options[' . $key . '][form_name]', $options[$key]['form_name'], ['class' => 'form-control', 'step' => '1', 'min' => '-100', 'max' => '100']);
        ?>
    </div>
    <div class="col-md-6">
        <?php
        echo Html::checkbox('options[' . $key . '][enable_visit]', isset($options[$key]['enable_visit'])?$options[$key]['enable_visit']:'1', [ 'step' => '1', 'min' => '-100', 'max' => '100']);
        ?>
        <?= Html::label(Yii::t('ezmodule', 'Enable Visit'), 'options[' . $key . '][enable_visit]', ['class' => 'control-label']) ?>
    </div>
    <div class="clearfix"></div>
</div>
<div class="form-group row">
    <div class="col-md-6 ">
        <?php
        $attrname_ezf_id = 'options[' . $key . '][random_ezf_id]';
        $value_ezf_id = isset($options[$key]['random_ezf_id']) ? $options[$key]['random_ezf_id'] : '';
        ?>
        <?= Html::label(Yii::t('ezmodule', 'Forms'), $attrname_ezf_id, ['class' => 'control-label']) ?>
        <?php
        echo kartik\select2\Select2::widget([
            'name' => $attrname_ezf_id,
            'value' => $value_ezf_id,
            'options' => ['placeholder' => Yii::t('ezmodule', 'Forms'), 'id' => 'config_random_ezf_id'],
            'data' => ArrayHelper::map($itemsEzform, 'ezf_id', 'ezf_name'),
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
    <div class="col-md-6 sdbox-col">
        <?php
        $attrname_random_actual_date = 'options[' . $key . '][random_actual_date]';
        $random_actual_date = isset($options[$key]['random_actual_date']) ? $options[$key]['random_actual_date'] : '';
        ?>
        <?= Html::label(Yii::t('ezform', 'Actual Date'), $attrname_random_actual_date, ['class' => 'control-label']) ?>
        <div id="main_actual_random_box">

        </div>
    </div>
    <div class="clearfix"></div>

</div>
<div class="form-group row">
    <div class="col-md-3">
        <?php
        $attrname_random_visit_name = 'options[' . $key . '][random_visit_name]';
        $random_visit_name = isset($options[$key]['random_visit_name']) ? $options[$key]['random_visit_name'] : '';
        ?>
        <?= Html::label(Yii::t('ezform', 'Visit Name'), $attrname_random_visit_name, ['class' => 'control-label']) ?>
        <div id="random_visit_name_box">

        </div>
    </div>
    <div class="col-md-3 sdbox-col">
        <?= Html::label(Yii::t('ezmodule', 'Plan Date Distance'), 'options[' . $key . '][random_plan_distance]', ['class' => 'control-label']) ?>
        <?php
        echo Html::input('number', 'options[' . $key . '][random_plan_distance]', isset($options[$key])?$options[$key]['random_plan_distance']:'', ['class' => 'form-control', 'step' => '1', 'min' => '-100', 'max' => '100']);
        ?>
    </div>
    <div class="col-md-3 sdbox-col">
        <?= Html::label(Yii::t('ezmodule', 'Earliest Date Distance'), 'options[' . $key . '][random_earliest_distance]', ['class' => 'control-label']) ?>
        <?php
        echo Html::input('number', 'options[' . $key . '][random_earliest_distance]', isset($options[$key])?$options[$key]['random_earliest_distance']:'', ['class' => 'form-control', 'step' => '1', 'min' => '-100', 'max' => '100']);
        ?>
    </div>
    <div class="col-md-3 sdbox-col">
        <?= Html::label(Yii::t('ezmodule', 'Latest Date Distance'), 'options[' . $key . '][random_latest_distance]', ['class' => 'control-label']) ?>
        <?php
        echo Html::input('number', 'options[' . $key . '][random_latest_distance]', isset($options[$key])?$options[$key]['random_latest_distance']:'', ['class' => 'form-control', 'step' => '1', 'min' => '-100', 'max' => '100']);
        ?>
    </div>
    <div class="clearfix"></div>
</div>

<?php
$this->registerJS("
    
$('form#form-submit').on('beforeSubmit', function(e) {
    
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
                $(document).find('#modal-ezform-config').modal('hide');
                location.reload();
//                var urlreload =  $('#modal-ezform-config').attr('data-url');        
//                getUiAjax(urlreload, 'modal-ezform-config');
                
            } else {
                " . SDNoty::show('result.message', 'result.status') . "
                    $('#form-submit .btn-submit').attr('disabled', false);
            } 
          },
          error: function () {
            " . SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') . "
                $('#form-submit .btn-submit').attr('disabled', false);
	    console.log('server error');
          }
      });
      
    return false;
});
    
");
?>
<?php
$this->registerJS("
    fieldActualDateRandom($('#config_random_ezf_id').val());
fieldVisitName($('#config_random_ezf_id').val());
    $('#config_random_ezf_id').on('change',function(){
      var ezf_id = $(this).val();
      fieldActualDateRandom(ezf_id);
      fieldVisitName(ezf_id);
    });
    
    function fieldActualDateRandom(ezf_id){
        var value = " . json_encode($random_actual_date) . ";
        $.post('" . Url::to(['/ezforms2/target/get-fields']) . "',{ ezf_id: ezf_id, multiple:0, name: '{$attrname_random_actual_date}', value: value ,id:'config_random_actual_date'}
          ).done(function(result){
             $('#main_actual_random_box').html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
   
    function fieldVisitName(ezf_id){
        var value = " . json_encode($random_visit_name) . ";
        $.post('" . Url::to(['/ezforms2/target/get-fields']) . "',{ ezf_id: ezf_id, multiple:0, name: '{$attrname_random_visit_name}', value: value ,id:'config_visit_name_mapping'}
          ).done(function(result){
             $('#random_visit_name_box').html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
");
?>