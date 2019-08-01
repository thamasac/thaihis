<?php

use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$options = isset($model->options) ? \appxq\sdii\utils\SDUtility::string2Array($model->options) : [];
$target = backend\modules\ezforms2\classes\EzfQuery::getTargetOne($ezf_id);
if ($target) {
    $itemsEzform = \backend\modules\ezforms2\classes\EzfQuery::getEzformRef($ezf_id, $target['parent_ezf_id']);
} else {
    $itemsEzform = \backend\modules\ezforms2\classes\EzfQuery::getEzformAll($ezf_id);
}


?>

    <div class="modal-header" style="margin-bottom: 15px;">
        <h4 class="modal-title"><?= Yii::t('ezmodule', 'Widget Config') ?></h4>
    </div>
<?= Html::label(Yii::t('ezform', 'Visit Form'), null, ['class' => 'control-label']) ?>
<?php

$attrname_ezf_id = 'options[ezf_id]';
$value_ezf_id = isset($options['ezf_id']) ? $options['ezf_id'] : '';
echo kartik\select2\Select2::widget([
    'name' => $attrname_ezf_id,
    'value' => "$value_ezf_id",
    'options' => ['placeholder' => Yii::t('ezform', 'Form'), 'id' => 'config_ezf_id'],
    'data' => ArrayHelper::map($itemsEzform, 'ezf_id', 'ezf_name'),
    'pluginOptions' => [
        'allowClear' => true
    ],
]);
?>
    <br>
<?= Html::label(Yii::t('ezform', 'Enable Visit Type'), null, ['class' => 'control-label']) ?>
    <div id="ref_field_box"></div>
<div style="text-align: right">
    <button type="button" id="add_choice_button" class="btn btn-primary">Add</button>
</div>
<hr>
    <div class="form-group">
        <?= Html::label(Yii::t('ezform', 'Javascript Callback(Result)'), 'options[callback]', ['class' => 'control-label']) ?>
        <?= Html::textarea('options[callback]', isset($options['callback'])?$options['callback']:'', ['class' => 'form-control', 'row'=>3])?>
    </div>
<?php


$url = Url::to(['/thaihis/patient-visit/get-ezform-data']);
$urlAddChoice = Url::to(['/thaihis/patient-visit/create-visit-choice']);
$errorNotify = \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"');
$optionsVisitJson = isset($options['visit_type']) ? json_encode($options['visit_type']) : '[]';
$this->registerJS(<<<JS
$('#add_choice_button').click( ()=>{
   // console.log('add');
   addChoice();
});

    
function initChoice() {
    const optionVisitJson = $optionsVisitJson;
  for(let option in $optionsVisitJson){
      // console.log(optionVisitJson);
      addChoice( optionVisitJson[option]);
  }
}
        
function addChoice(visit_code = 0){
    // var value = 
    $.post('$urlAddChoice',{ visit_code: visit_code}
          ).done(function(result){
        $('#ref_field_box').append(result);
    }).fail(function(){
       $errorNotify
        console.log('server error');
          });
    }
    initChoice();

JS
);
// ezf_id: ezf_id, name: 'visit_type_name', value: value ,id:'config_fields'