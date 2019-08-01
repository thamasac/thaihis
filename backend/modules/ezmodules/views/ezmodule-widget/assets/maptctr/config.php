<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$options = isset($model->options)?\appxq\sdii\utils\SDUtility::string2Array($model->options):[];

$target = backend\modules\ezforms2\classes\EzfQuery::getTargetOne($ezf_id);
$itemsEzform = \backend\modules\ezforms2\classes\EzfQuery::getEzformCoDevAll();

?>

<div class="modal-header" style="margin-bottom: 15px;">
    <h4 class="modal-title" ><?= Yii::t('ezmodule', 'Widget Config')?></h4>
</div>

<!--config start-->

<div class="form-group row">
    <div class="col-md-12">
        <?= Html::label(Yii::t('ezform', 'Title'), 'options[title]', ['class' => 'control-label']) ?>
        <?= Html::textInput('options[title]', (isset($options['title'])?$options['title']:Yii::t('ezform', 'Title')), ['class'=>'form-control'])?>
    </div>
  
</div>

<div class="form-group row">
    <div class="col-md-6 ">
      <?php
      $attrname_ezf_id = 'options[ezf_id]';
      $value_ezf_id = isset($options['ezf_id'])?$options['ezf_id']:'';
      ?>
        <?= Html::label(Yii::t('ezform', 'Form'), $attrname_ezf_id, ['class' => 'control-label']) ?>
        <?php 
        echo kartik\select2\Select2::widget([
            'name' => $attrname_ezf_id,
            'value'=> $value_ezf_id,
            'options' => ['placeholder' => Yii::t('ezform', 'Form'), 'id'=>'config_ezf_id'],
            'data' => ArrayHelper::map($itemsEzform,'ezf_id','ezf_name'),
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
    <div class="col-md-6 sdbox-col">
      <?php
      $attrname_fields = 'options[fields]';
      $value_fields_json = isset($options['fields']) && is_array($options['fields'])?\appxq\sdii\utils\SDUtility::array2String($options['fields']):'{}';
      ?>
        <?= Html::label(Yii::t('ezform', 'Fields'), $attrname_fields, ['class' => 'control-label']) ?>
        <div id="ref_field_box">
            
        </div>
    </div>
</div>

<div class="form-group row">
    <div class="col-md-3 ">
      <?php
      $attrname_pagesize = 'options[pagesize]';
      $value_pagesize = isset($options['pagesize'])?$options['pagesize']:50;
      ?>
        <?= Html::label(Yii::t('ezform', 'Page Size'), $attrname_pagesize, ['class' => 'control-label']) ?>
        <?php 
        echo Html::textInput($attrname_pagesize, $value_pagesize, ['class' => 'form-control ', 'type'=>'number']);
        ?>
    </div>
    <div class="col-md-3 sdbox-col">
      <?php
      $attrname_order_by = 'options[order_by]';
      $value_order_by = isset($options['order_by'])?$options['order_by']:4;//4-ASC - 3-DESC
      ?>
        <?= Html::label(Yii::t('ezform', 'Order By'), $attrname_order_by, ['class' => 'control-label']) ?>
        <?php 
        echo Html::dropDownList($attrname_order_by, $value_order_by, [4=>'ASC', 3=>'DESC'], ['class' => 'form-control ']);
        ?>
    </div>
    <div class="col-md-6 sdbox-col">
      <?php
      $attrname_order = 'options[order]';
      $value_order = isset($options['order']) && is_array($options['order'])?\appxq\sdii\utils\SDUtility::array2String($options['order']):'{}';
      ?>
        <?= Html::label(Yii::t('ezform', 'Order Fields'), $attrname_order, ['class' => 'control-label']) ?>
        <div id="order_field_box">
            
        </div>
    </div>
   
</div>
<div class="form-group row">
    <div class="col-md-6">
      <?php
      $attrname_zoom = 'options[zoom]';
      $zoom = isset($options['zoom'])?$options['zoom']:4;
      ?>
        <?= Html::label(Yii::t('ezform', 'Zoom Map'), $attrname_zoom, ['class' => 'control-label']) ?>
        <?php 
        echo Html::textInput($attrname_zoom, $zoom, ['class' => 'form-control ', 'type'=>'number']);
        ?>
    </div>
  <div class="col-md-4">
      <?= Html::hiddenInput('options[default_column]', 0)?>
      <?= backend\modules\ezforms2\classes\EzformWidget::checkbox('options[default_column]', isset($options['default_column'])?$options['default_column']:1, ['label'=> Yii::t('ezmodule', 'Enable Default Column')])?>
  </div>
</div>

<!--config end-->

<?php
$this->registerJS("
    fields($('#config_ezf_id').val());
    orderfields($('#config_ezf_id').val());
    
    $('#config_ezf_id').on('change',function(){
      var ezf_id = $(this).val();
      fields(ezf_id);
      orderfields(ezf_id);
    });
    
    function fields(ezf_id){
        var value = ".$value_fields_json.";
        $.post('".Url::to(['/ezforms2/target/get-fields'])."',{ ezf_id: ezf_id, multiple:1, name: '{$attrname_fields}', value: value ,id:'config_fields'}
          ).done(function(result){
             $('#ref_field_box').html(result);
          }).fail(function(){
              ". \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
    function orderfields(ezf_id){
        var value = ".$value_order.";
        $.post('".Url::to(['/ezforms2/target/get-fields'])."',{ ezf_id: ezf_id, multiple:1, name: '{$attrname_order}', value: value ,id:'config_order_fields'}
          ).done(function(result){
             $('#order_field_box').html(result);
          }).fail(function(){
              ". \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
    
");
?>