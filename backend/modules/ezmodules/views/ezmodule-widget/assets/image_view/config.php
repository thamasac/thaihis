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
$attrname_fields = 'options[fields]';
$value_fields = isset($options['fields']) ? $options['fields']:'{}';

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
<?= Html::label(Yii::t('ezform', 'Field'), null, ['class' => 'control-label']) ?>
    <div id="ref_field_box">
    </div>
<?php
$url = Url::to(['/thaihis/patient-visit/get-ezform-data']);
$urlGetField = Url::to(['/ezforms2/target/get-fields']);
$errorNotify = \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"');
$op = json_encode($options);
$this->registerJS(<<<JS
   fields($('#config_ezf_id').val());
    $('#config_ezf_id').on('change',function(){
      let ezf_id = $(this).val();
      fields(ezf_id);
    });
    
    function fields(ezf_id){
        console.log('ezf_id',ezf_id);
        let value = "$value_fields";
        $.post('$urlGetField',{ ezf_id: ezf_id, multiple:0, name: '{$attrname_fields}', value: value ,id:'config_fields'}
          ).done(function(result){
             $('#ref_field_box').html(result);
          }).fail(function(){
              $errorNotify
              console.log('server error');
          });
    }
JS
);

