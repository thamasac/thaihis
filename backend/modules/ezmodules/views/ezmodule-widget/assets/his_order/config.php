<?php

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
  <h4 class="modal-title" ><?= Yii::t('ezmodule', 'Widget Config') ?></h4>
</div>

<div class="form-group row">
  <div class="col-md-6">
      <?php
      $attrname_ezf_id = 'options[ezf_id]';
      $value_ezf_id = isset($options['ezf_id']) ? $options['ezf_id'] : 0;
      ?>
      <?= Html::label(Yii::t('thaihis', 'Form Visit'), $attrname_ezf_id, ['class' => 'control-label']) ?>
      <?php
      echo kartik\select2\Select2::widget([
          'name' => $attrname_ezf_id,
          'value' => $value_ezf_id,
          'options' => ['placeholder' => Yii::t('ezform', 'Form'), 'id' => 'config_ezf_id'],
          'data' => ArrayHelper::map($itemsEzform, 'ezf_id', 'ezf_name'),
          'pluginOptions' => [
              'allowClear' => true
          ],
      ]);
      ?>
  </div>  
</div>

<div class="form-group row">
  <div class="col-md-12 ">
      <?php
      $attr_orderlists_ezf_id = 'options[orderlists_ezf_id]';
      $value_orderlists_ezf_id = isset($options['orderlists_ezf_id']) ? $options['orderlists_ezf_id'] : '';
      ?>
      <?= Html::label(Yii::t('ezform Order Lists', 'Form'), $attr_orderlists_ezf_id, ['class' => 'control-label']) ?>
      <?php
      echo kartik\select2\Select2::widget([
          'name' => $attr_orderlists_ezf_id,
          'value' => $value_orderlists_ezf_id,
          'options' => ['placeholder' => Yii::t('ezform', 'Form'), 'id' => 'config_orderlists_ezf_id'],
          'data' => ArrayHelper::map($itemsEzform, 'ezf_id', 'ezf_name'),
          'pluginOptions' => [
              'allowClear' => true
          ],
      ]);
      ?>
  </div>  
</div>

<div class="form-group row">
  <div class="col-md-6">
      <?php
      $attr_orderlists_fields = 'options[orderlists_fields]';
      $value_orderlists_fields = isset($options['orderlists_fields']) && is_array($options['orderlists_fields']) ? \appxq\sdii\utils\SDUtility::array2String($options['orderlists_fields']) : '{}';
      ?>
      <?= Html::label(Yii::t('ezform', 'Fields Show'), $attr_orderlists_fields, ['class' => 'control-label']) ?>
    <div id="box_orderlist_filed">

    </div>
  </div>

  <div class="col-md-6 sdbox-col">
      <?php
      $attr_filter_orderlists_fields = 'options[filter_orderlists_fields]';
      $value_filter_orderlists_fields = isset($options['filter_orderlists_fields']) && is_array($options['filter_orderlists_fields']) ? \appxq\sdii\utils\SDUtility::array2String($options['filter_orderlists_fields']) : '{}';
      ?>
      <?= Html::label(Yii::t('ezform', 'Fields Search'), $attr_filter_orderlists_fields, ['class' => 'control-label']) ?>
    <div id="box_filter_orderlist_filed">

    </div>
  </div>
</div>

<div class="form-group row">
  <div class="col-md-2">
      <?= Html::label(Yii::t('ezform', 'Filter OrderType'), '', ['class' => 'control-label']) ?>
      <?= yii\bootstrap\Html::hiddenInput('options[filterOrderType]', 0) ?>
      <?= \backend\modules\ezforms2\classes\EzformWidget::checkbox('options[filterOrderType]', (isset($options['filterOrderType']) ? $options['filterOrderType'] : 0), ['label' => 'filterOrderType', 'id' => 'inputOrderType']) ?>    
  </div>  
  <div class="col-md-4 sdbox-col" id="divOrderType">
      <?= Html::label('Type', '', ['class' => 'control-label']) ?>
      <?php
      $attr_ordertype_ezf_id = 'options[ordertype_ezf_id]';
      $value_ordertype_ezf_id = isset($options['ordertype_ezf_id']) ? [$options['ordertype_ezf_id']] : '';

      echo kartik\select2\Select2::widget([
          'name' => $attr_ordertype_ezf_id,
          'value' => $value_ordertype_ezf_id,
          'options' => ['placeholder' => Yii::t('ezform', 'Form'), 'id' => 'config_ordertype_ezf_id'],
          'data' => ArrayHelper::map($itemsEzform, 'ezf_id', 'ezf_name'),
          'pluginOptions' => [
              'allowClear' => true
          ],
      ]);
      ?>
  </div>

  <div class="col-md-6 sdbox-col">
      <?= Html::label(Yii::t('ezform', 'Filter OrderType Default'), '', ['class' => 'control-label']) ?>
      <?php
      $attr_filtertype_default = "options[filter_ordertype_default]";
      $value_filtertype_default = isset($options['filter_ordertype_default']) ? $options['filter_ordertype_default'] : '';
      $dataGroup = backend\modules\thaihis\classes\OrderQuery::getOrderType();
      echo kartik\select2\Select2::widget([
          'name' => $attr_filtertype_default,
          'value' => $value_filtertype_default,
          'options' => ['placeholder' => Yii::t('ezform', 'Order Type'), 'id' => 'sadasdasdasd', 'multiple' => true],
          'data' => ArrayHelper::map($dataGroup, 'order_type_code', 'order_type_name'),
          'pluginOptions' => [
              'allowClear' => true
          ],
      ]);
      ?>
  </div>
</div>

<div class="form-group row">
  <div class="col-md-6 ">
      <?php
      $attr_order_ezf_id = 'options[order_ezf_id]';
      $value_order_ezf_id = isset($options['order_ezf_id']) ? $options['order_ezf_id'] : '';
      ?>
      <?= Html::label(Yii::t('ezform', 'Form'), $attr_order_ezf_id, ['class' => 'control-label']) ?>
      <?php
      echo kartik\select2\Select2::widget([
          'name' => $attr_order_ezf_id,
          'value' => $value_order_ezf_id,
          'options' => ['placeholder' => Yii::t('ezform', 'Form'), 'id' => 'config_order_ezf_id'],
          'data' => ArrayHelper::map($itemsEzform, 'ezf_id', 'ezf_name'),
          'pluginOptions' => [
              'allowClear' => true
          ],
      ]);
      ?>
  </div>
  <div class="col-md-6 sdbox-col">
      <?php
      $attr_ordertran_ezf_id = 'options[ordertran_ezf_id]';
      $value_ordertran_ezf_id = isset($options['ordertran_ezf_id']) ? \appxq\sdii\utils\SDUtility::array2String([$options['ordertran_ezf_id']]) : '{}';
      $ordertran_ezf_id = isset($options['ordertran_ezf_id']) ? $options['ordertran_ezf_id'] : '';
      ?>
      <?= Html::label(Yii::t('ezform', 'Reference Form'), $attr_ordertran_ezf_id, ['class' => 'control-label']) ?>
    <div id="box_ordertran_form">

    </div>
  </div>
</div>

<div class="form-group row">
  <div class="col-md-6">
      <?= Html::label(Yii::t('ezform', 'Display'), 'options[display]', ['class' => 'control-label']) ?>
      <?=
      kartik\select2\Select2::widget([
          'id' => 'config_display',
          'name' => 'options[display]',
          'value' => isset($options['display']) ? $options['display'] : 'content_h',
          'data' => backend\modules\ezmodules\classes\ModuleFunc::itemAlias('display'),
          'options' => ['placeholder' => Yii::t('ezform', 'Select Display ...')],
          'pluginOptions' => [
              'allowClear' => true,
          ]
      ]);
      ?>
  </div>
  <div class="col-md-6 sdbox-col" >
      <?= Html::label(Yii::t('ezform', 'Theme'), 'options[theme]', ['class' => 'control-label']) ?>
      <?=
      kartik\select2\Select2::widget([
          'id' => 'config_theme',
          'name' => 'options[theme]',
          'value' => isset($options['theme']) ? $options['theme'] : 'default',
          'data' => backend\modules\ezmodules\classes\ModuleFunc::itemAlias('theme'),
          'options' => ['placeholder' => Yii::t('ezform', 'Select Theme ...')],
          'pluginOptions' => [
              'allowClear' => true,
          ]
      ]);
      ?>
  </div>
</div>

<div class="form-group row">
  <div class="col-md-6 " >
    <?= yii\bootstrap\Html::hiddenInput('options[initdata]', 0) ?>
    <?= \backend\modules\ezforms2\classes\EzformWidget::checkbox('options[initdata]', (isset($options['initdata']) ? $options['initdata'] : 0), ['label' => 'initdata']) ?>
    <?php
//    echo yii\bootstrap\Html::hiddenInput('options[oipd_type]', 0);
    echo Html::radioList('options[oipd_type]', (isset($options['oipd_type']) ? $options['oipd_type'] : 'OPD'), ['OPD' => 'OPD', 'IPD' => 'IPD']);
//    echo \backend\modules\ezforms2\classes\EzformWidget::radioList('options[oipd_type]', (isset($options['oipd_type']) ? $options['oipd_type'] : 'OPD'), ['OPD' => 'OPD', 'IPD' => 'IPD']);
//    echo \backend\modules\ezforms2\classes\EzformWidget::checkbox('options[oipd_type]', (isset($options['disabled_box']) ? $options['disabled_box'] : 0), ['label' => 'Disabled Box']);
    ?>
  </div>
</div>

<?php
$this->registerJS("
    form_ordertran($('#config_order_ezf_id').val());
    fields_orderlist($('#config_orderlists_ezf_id').val());  
    fields_filter_orderlist($('#config_orderlists_ezf_id').val());
//    fields_order($('#config_order_ezf_id').val());
//    fields_ordertran($('#config_ordertran_ezf_id').val());
    
    $('#config_orderlists_ezf_id').on('change',function(){
      var ezf_id = $(this).val();
      fields_orderlist(ezf_id);
      fields_filter_orderlist(ezf_id);
    });
    
    $('#config_order_ezf_id').on('change',function(){
      var ezf_id = $(this).val();
//      fields_order(ezf_id);
      form_ordertran(ezf_id);
    });
    
//    $('#ref_form_box').on('change','#config_ordertran_ezf_id',function(){
//      fields_ordertran($(this).val());
//    });
    
    function fields_orderlist(ezf_id){
        var value = " . $value_orderlists_fields . ";
        $.post('" . Url::to(['/ezforms2/target/get-fields']) . "',{ ezf_id: ezf_id, multiple:1, name: '{$attr_orderlists_fields}', value: value ,id:'config_orderlist_fields'}
          ).done(function(result){
             $('#box_orderlist_filed').html(result);             
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
    function fields_filter_orderlist(ezf_id){
        var value = " . $value_filter_orderlists_fields . ";
        $.post('" . Url::to(['/ezforms2/target/get-fields']) . "',{ ezf_id: ezf_id, multiple:1, name: '{$attr_filter_orderlists_fields}', value: value ,id:'config_filter_orderlist_fields'}
          ).done(function(result){
             $('#box_filter_orderlist_filed').html(result);             
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }         
    
    function form_ordertran(ezf_id){ 
        var value = " . $value_ordertran_ezf_id . ";
        $.post('" . Url::to(['/thaihis/patient-visit/get-form-ref']) . "',{ ezf_id: ezf_id, multiple:0, name: '{$attr_ordertran_ezf_id}', value_ref: value ,id:'config_ordertran_ezf_id'}
          ).done(function(result){
             $('#box_ordertran_form').html(result);    
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }  
    hideOrderType('inputOrderType');
    $('#inputOrderType').on('change',function(){
      hideOrderType('inputOrderType');
    });
    
    function hideOrderType(_attr){
        if($('#'+_attr).prop('checked')) {
            $('#divOrderType').removeClass('hidden');
        } else {
            $('#divOrderType').addClass('hidden');
            $('#ordertype_ezf_id').select2('val','');
        }
    }
    
");
?>