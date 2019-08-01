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

<div class="modal-body">
  <div class="form-group row" >
    <div class="col-md-4 sdbox-col">
        <?php
        $attrname_main_ezf_id = 'options[main_ezf_id]';
        $value_main_ezf_id = isset($options['main_ezf_id']) ? $options['main_ezf_id'] : null;
        ?>
        <?= Html::label(Yii::t('ezmodule', 'Forms <code>*</code>'), $attrname_main_ezf_id, ['class' => 'control-label']) ?>
        <?php
        
        echo kartik\select2\Select2::widget([
            'name' => $attrname_main_ezf_id,
            'value' => $value_main_ezf_id,
            'options' => ['placeholder' => Yii::t('ezmodule', 'Forms'), 'id' => 'config_main_ezf_id'],
            'data' => ArrayHelper::map($itemsEzform, 'ezf_id', 'ezf_name'),
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
    <div class="col-md-4 sdbox-col">
        <?php
        $attrname_ezf_id = 'options[ezf_id]';
        $value_ezf_id = isset($options['ezf_id']) ? $options['ezf_id'] : null;
        ?>
        <?= Html::label(Yii::t('ezmodule', 'Visit Forms <code>*</code>'), $attrname_ezf_id, ['class' => 'control-label']) ?>
        <?php
        echo kartik\select2\Select2::widget([
            'name' => $attrname_ezf_id,
            'value' => $value_ezf_id,
            'options' => ['placeholder' => Yii::t('ezmodule', 'Forms'), 'id' => 'config_ezf_id'],
            'data' => ArrayHelper::map($itemsEzform, 'ezf_id', 'ezf_name'),
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>

    <div class="col-md-4 sdbox-col">
        <?php
        $attrname_tran_ezf_id = 'options[tran_ezf_id]';
        $value_tran_ezf_id = isset($options['tran_ezf_id']) ? $options['tran_ezf_id'] : 0;
        ?>
        <?= Html::label(Yii::t('thaihis', 'Form Visit Transection'), $attrname_tran_ezf_id, ['class' => 'control-label']) ?>
        <?php
        echo kartik\select2\Select2::widget([
            'name' => $attrname_tran_ezf_id,
            'value' => $value_tran_ezf_id,
            'options' => ['placeholder' => Yii::t('ezform', 'Form'), 'id' => 'config_tran_ezf_id'],
            'data' => ArrayHelper::map($itemsEzform, 'ezf_id', 'ezf_name'),
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div> 
  </div>
  <div class="form-group row">

    <div class="col-md-12 sdbox-col">
        <?php
        $attrname_ref = 'options[refform]';
        $value_ref = isset($options['refform']) && is_array($options['refform']) ? $options['refform'] : null;
        ?>
        <?= Html::label(Yii::t('ezform', 'Reference Form <code>*</code>'), '', ['class' => 'control-label']) ?>
      <div id="ref_form_box">

      </div>
    </div>
  </div>
  <div class="form-group row">

    <div class="col-md-12 sdbox-col">
        <?php
        $attrname_leftref = 'options[left_refform]';
        $value_leftref = isset($options['left_refform']) && is_array($options['left_refform']) ? $options['left_refform'] : null;
        ?>
        <?= Html::label(Yii::t('ezform', 'Form to left join'), '', ['class' => 'control-label']) ?>
      <div id="left_ref_form_box">

      </div>
    </div>
  </div>
  <div class="form-group row" >
    <div class="col-md-6 sdbox-col">
        <?php
        $attrname_field_bdate = 'options[field_bdate]';
        $main_field_bdate = isset($options['field_bdate']) ? $options['field_bdate'] : null;
        ?>
        <?= Html::label(Yii::t('ezform', 'Field of birthdate'), '', ['class' => 'control-label']) ?>
      <div id="ref_field_bdate">

      </div>
    </div>


    <div class="col-md-6 sdbox-col">
        <?php
        $attrname_field_pic = 'options[field_pic]';
        $main_field_pic = isset($options['field_pic']) ? $options['field_pic'] : null;
        ?>
        <?= Html::label(Yii::t('ezform', 'Image Field'), '', ['class' => 'control-label']) ?>
      <div id="ref_field_pic">

      </div>
    </div>
  </div>
  <div class="form-group row" >
    <div class="col-md-12 sdbox-col">
        <?php
        $attrname_field_display = 'options[field_display]';
        $main_field_display = isset($options['field_display']) && is_array($options['field_display']) ? $options['field_display'] : null;
        ?>
        <?= Html::label(Yii::t('ezform', 'Fields <code>*</code>'), 'options[field_display]', ['class' => 'control-label']) ?>
      <div id="ref_field_box">

      </div>
    </div>
  </div>
</div>

<?php
$this->registerJs("
        
    $(function(){
        var ref_form1 = $('#config_ref_form').val();
        var ref_form2 = $('#config_left_ref_form').val();
        var form_merge = ref_form1;
        if(ref_form2)form_merge = $.merge(ref_form1,ref_form2);
        
        form_ref($('#config_ezf_id').val(),null);
        form_leftref($('#config_ezf_id').val(),null,null);
        
        fields(form_merge,$('#config_ezf_id').val());
        field_pic($('#config_ref_form').val(),$('#config_ezf_id').val());
        field_bdate($('#config_ref_form').val(),$('#config_ezf_id').val());
    });
    
    $('#config_ezf_id').on('change',function(){
      var main_ezf_id = $(this).val();
      var ezf_id = $('#config_ref_form').val();
      
      form_ref(main_ezf_id,ezf_id);
      form_leftref(main_ezf_id,ezf_id);
      fields(ezf_id,main_ezf_id);
      field_pic(ezf_id,main_ezf_id);
      field_bdate(ezf_id,main_ezf_id);
    });
    
    $('#ref_form_box').on('change','#config_ref_form',function(){
        var ezf_id = $(this).val();
        var ezf_id2 = $('#config_left_ref_form').val();
        var ezf_merg = ezf_id;
        if(ezf_id2 && $.isArray(ezf_id))ezf_merg = $.merge(ezf_id,ezf_id2);
        
        var main_ezf_id = $('#config_ezf_id').val();
        form_ref(main_ezf_id,ezf_id);
        form_leftref(main_ezf_id,ezf_id2,ezf_id);
        fields(ezf_merg,main_ezf_id);
        field_pic(ezf_merg,main_ezf_id);
        field_bdate(ezf_merg,main_ezf_id);
    });
    
    $('#left_ref_form_box').on('change','#config_left_ref_form',function(){
        var ezf_id2 = $(this).val();
        var ezf_id = $('#config_ref_form').val();
        var ezf_merg = ezf_id;
        if(ezf_id2 && $.isArray(ezf_id))ezf_merg = $.merge(ezf_id,ezf_id2);
        
        var main_ezf_id = $('#config_ezf_id').val();
        form_leftref(main_ezf_id,ezf_id2,ezf_id);
        fields(ezf_merg,main_ezf_id);
        field_pic(ezf_merg,main_ezf_id);
        field_bdate(ezf_merg,main_ezf_id);
    });
    
    function form_ref(ezf_id,value_ref){ 
        var value = " . json_encode($value_ref) . ";
        var name = " . json_encode($attrname_ref) . ";
        if(value_ref){
            value = value_ref;
        }
        
        $.post('" . Url::to(['/thaihis/configs/get-form-ref2']) . "',{ ezf_id: ezf_id, multiple:1, name: name, value_ref: value,id:'config_ref_form'}
          ).done(function(result){
             $('#ref_form_box').html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    } 
    
    function form_leftref(ezf_id,value_ref2,value_ref){ 
        var value = " . json_encode($value_leftref) . ";
        var name = " . json_encode($attrname_leftref) . ";
        var value_merge = value_ref;
        if($.isArray(value_ref2) && $.isArray(value_ref))value_merge = $.merge(value_ref,value_ref2);

        if(value_ref){
            value = value_ref2;
        }
        
        $.post('" . Url::to(['/thaihis/configs/get-form-ref2']) . "',{ ezf_id: ezf_id, multiple:1, name: name, value_ref: value,value_merge:value_merge ,id:'config_left_ref_form'}
          ).done(function(result){
             $('#left_ref_form_box').html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    } 
    function fields(ezf_id,main_ezf_id){
        var value = " . json_encode($main_field_display) . ";
        var name = " . json_encode($attrname_field_display) . ";
        var value_ref = " . json_encode($value_ref) . ";
        var value_ref2 = " . json_encode($value_leftref) . ";

        var value_merge = value_ref;
        if(value_ref2)value_merge = $.merge(value_ref,value_ref2);
        if(ezf_id){
            value_merge = ezf_id;
        }
        
        $.post('" . Url::to(['/thaihis/configs/get-fields-forms']) . "',{ ezf_id: value_merge,main_ezf_id:main_ezf_id, multiple:1, name: name, value: value ,id:'config_fields'}
          ).done(function(result){
             $('#ref_field_box').html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
    function field_pic(ezf_id,main_ezf_id){
        var value = " . json_encode($main_field_pic) . ";
        var name = " . json_encode($attrname_field_pic) . ";
        var value_ref = " . json_encode($value_ref) . ";
        if(ezf_id){
            value_ref = ezf_id;
        }
        $.post('" . Url::to(['/thaihis/patient-visit/get-fields-forms2']) . "',{ ezf_id:  value_ref,main_ezf_id:main_ezf_id, multiple:0, name: name, value: value,id:'config_field_pic'}
          ).done(function(result){
             $('#ref_field_pic').html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    } 
    
    function field_bdate(ezf_id,main_ezf_id){
        var value = " . json_encode($main_field_bdate) . ";
        var name = " . json_encode($attrname_field_bdate) . ";
        var value_ref = " . json_encode($value_ref) . ";
        if(ezf_id){
            value_ref = ezf_id;
        }
        $.post('" . Url::to(['/thaihis/patient-visit/get-fields-forms2']) . "',{ ezf_id:  value_ref,main_ezf_id:main_ezf_id, multiple:0, name: name, value: value,id:'config_field_bdate'}
          ).done(function(result){
             $('#ref_field_bdate').html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
");
?>