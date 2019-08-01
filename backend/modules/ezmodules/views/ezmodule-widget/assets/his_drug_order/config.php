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

$conditions = isset($options['conditions']) ? $options['conditions'] : [];

?>

<div class="modal-header" style="margin-bottom: 15px;">
  <h4 class="modal-title" ><?= Yii::t('ezmodule', 'Widget Config') ?></h4>
</div>

<div class="form-group row">
  <div class="col-md-4">
      <?= Html::label(Yii::t('ezform', 'Title'), 'options[title]', ['class' => 'control-label']) ?>
      <?= Html::textInput('options[title]', (isset($options['title']) ? $options['title'] : Yii::t('ezform', 'Title')), ['class' => 'form-control']) ?>
  </div>
  <div class="col-md-4 sdbox-col">
      <?php
      $attrname_visit_ezf_id = 'options[visit_ezf_id]';
      $value_visit_ezf_id = isset($options['visit_ezf_id']) ? $options['visit_ezf_id'] : 0;
      ?>
      <?= Html::label(Yii::t('thaihis', 'Form Visit'), $attrname_visit_ezf_id, ['class' => 'control-label']) ?>
      <?php
      echo kartik\select2\Select2::widget([
          'name' => $attrname_visit_ezf_id,
          'value' => $value_visit_ezf_id,
          'options' => ['placeholder' => Yii::t('ezform', 'Form'), 'id' => 'config_visit_ezf_id'],
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
      $value_ezf_id = isset($options['ezf_id']) ? $options['ezf_id'] : 0;
      ?>
      <?= Html::label(Yii::t('thaihis', 'Form pis order'), $attrname_ezf_id, ['class' => 'control-label']) ?>
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

  <div class="col-md-12">
      <?php
      $attrname_ref = 'options[refform]';
      $value_ref = isset($options['refform']) && is_array($options['refform']) ? \appxq\sdii\utils\SDUtility::array2String($options['refform']) : '{}';
      
      ?>
      <?= Html::label(Yii::t('ezform', 'Reference Form'), $attrname_ref, ['class' => 'control-label']) ?>
    <div id="ref_form_box">

    </div>
  </div>
</div>
<div class="form-group row">

    <div class="col-md-12">
        <?php
        $attrname_left_ref = 'options[left_refform]';
        $value_left_ref = isset($options['left_refform']) && is_array($options['left_refform']) ? \appxq\sdii\utils\SDUtility::array2String($options['left_refform']) : '{}';
        ?>
        <?= Html::label(Yii::t('ezform', 'Reference Form (Left join only)'), '', ['class' => 'control-label']) ?>
        <div id="left_ref_form_box"  >

        </div>
    </div>
</div>
<div class="form-group row">
  <div class="col-md-12">
      <?php
      $attrname_fields = 'options[fields]';
      $value_fields = isset($options['fields']) && is_array($options['fields']) ? \appxq\sdii\utils\SDUtility::array2String($options['fields']) : '{}';
      ?>
      <?= Html::label(Yii::t('ezform', 'Fields'), $attrname_fields, ['class' => 'control-label']) ?>
    <div id="ref_field_box">

    </div>
  </div>
</div>

<div class="form-group row">

  <div class="col-md-6 " >
      <?= Html::label(Yii::t('ezform', 'Action'), 'options[action]', ['class' => 'control-label']) ?>
      <?=
      kartik\select2\Select2::widget([
          'id' => 'config_action',
          'name' => 'options[action]',
          'value' => isset($options['action']) ? $options['action'] : ['create', 'update', 'delete', 'view', 'search'],
          'data' => backend\modules\ezmodules\classes\ModuleFunc::itemAlias('action'),
//          'maintainOrder' => true,
          'options' => ['placeholder' => Yii::t('ezform', 'Select action ...'), 'multiple' => true],
          'pluginOptions' => [
              'allowClear' => true,
              'tags' => true,
              'tokenSeparators' => [',', ' '],
          ]
      ]);
      ?>
  </div>
</div>

<div class="modal-header" style="background-color:#CBCAC6;margin-bottom: 2%"><h4>Drug list for search</h4></div>
<div class="col-md-4 sdbox-col">
    <?php
    $attrname_item_ezf_id = 'options[item_ezf_id]';
    $value_item_ezf_id = isset($options['item_ezf_id']) ? $options['item_ezf_id'] : 0;
    ?>
    <?= Html::label(Yii::t('thaihis', 'Form pis item'), $attrname_item_ezf_id, ['class' => 'control-label']) ?>
    <?php
    echo kartik\select2\Select2::widget([
        'name' => $attrname_item_ezf_id,
        'value' => $value_item_ezf_id,
        'options' => ['placeholder' => Yii::t('ezform', 'Item Form'), 'id' => 'config_item_ezf_id'],
        'data' => ArrayHelper::map($itemsEzform, 'ezf_id', 'ezf_name'),
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]);
    ?>
</div> 
<div class="form-group row">

  <div class="col-md-12">
      <?php
      $attrname_ref2 = 'options[refform2]';
      $value_ref2 = isset($options['refform2']) && is_array($options['refform2']) ? \appxq\sdii\utils\SDUtility::array2String($options['refform2']) : '{}';
      ?>
      <?= Html::label(Yii::t('ezform', 'Reference Form'), $attrname_ref2, ['class' => 'control-label']) ?>
    <div id="ref_form2_box">

    </div>
  </div>
</div>
<div class="form-group row">

    <div class="col-md-12">
        <?php
        $attrname_left_ref2 = 'options[left_refform2]';
        $value_left_ref2 = isset($options['left_refform2']) && is_array($options['left_refform2']) ? \appxq\sdii\utils\SDUtility::array2String($options['left_refform2']) : '{}';
        ?>
        <?= Html::label(Yii::t('ezform', 'Reference Form (Left join only)'), '', ['class' => 'control-label']) ?>
        <div id="left_ref_form_box2"  >

        </div>
    </div>
</div>
<div class="form-group row">
  <div class="col-md-12">
      <?php
      $attrname_fields2 = 'options[fields2]';
      $value_fields2 = isset($options['fields2']) && is_array($options['fields2']) ? \appxq\sdii\utils\SDUtility::array2String($options['fields2']) : '{}';
      ?>
      <?= Html::label(Yii::t('ezform', 'Fields'), $attrname_fields2, ['class' => 'control-label']) ?>
    <div id="ref_field2_boxs">

    </div>
  </div>
</div>
<div class="form-group row">
  <div class="col-md-12">
      <?php
      echo Html::label(Yii::t('thaihis', 'Condition custom'));
      echo Html::button("<i class='fa fa-plus'></i>", ['class' => 'btn btn-success', 'id' => 'btn-add-condition']) ?>
  </div>

  <div class="col-md-12" id="display-condition">

  </div>
</div>

<div class="form-group row">
  <div class="col-md-6">
      <?php
      $attrname_fields_search = 'options[fields_search]';
      $value_fields_search = isset($options['fields_search']) && is_array($options['fields_search']) ? \appxq\sdii\utils\SDUtility::array2String($options['fields_search']) : '{}';
      ?>
      <?= Html::label(Yii::t('ezform', 'Fields Search'), $attrname_fields_search, ['class' => 'control-label']) ?>
    <div id="ref_field_search_box">

    </div>
  </div>
  <div class="col-md-6 sdbox-col">
      <?php
      $attrname_image_field = 'options[image_field]';
      $value_image_field = isset($options['image_field']) ? $options['image_field'] : '';
      ?>
      <?= Html::label(Yii::t('ezform', 'Image Field'), $attrname_image_field, ['class' => 'control-label']) ?>
    <div id="pic_field_box">

    </div>
  </div>   
</div>
<div class="form-group row">
  <div class="col-md-12">
    <?= Html::radioList('options[action_view]', (isset($options['action_view']) ? $options['action_view'] : '2'), ['1' => 'Pis', '2' => 'User'])?>
  </div>
</div>
<div class="form-group">
  <?= Html::label(Yii::t('ezform', 'Template Content'), 'options[template_content]', ['class' => 'control-label']) ?>
  <?= Html::textarea('options[template_content]', isset($options['template_content']) ? $options['template_content'] : '', ['class' => 'form-control', 'row' => 3]) ?>
</div>
<?php
$this->registerJS("

    
    form_ref($('#config_ezf_id').val(),$('#config_ref_form').val(),$('#config_left_ref_form').val());
    form_left_ref($('#config_ezf_id').val(),$('#config_ref_form').val(),$('#config_left_ref_form').val());
    fields($('#config_ref_form').val(),$('#config_left_ref_form').val(),$('#config_ezf_id').val());
    form_ref2($('#config_item_ezf_id').val(),$('#config_ref_form2').val(),$('#config_left_ref_form2').val());
    form_left_ref2($('#config_item_ezf_id').val(),$('#config_ref_form2').val(),$('#config_left_ref_form2').val());
    fields2($('#config_ref_form2').val(),$('#config_left_ref_form2').val(),$('#config_item_ezf_id').val());
    fields_search($('#config_ref_form2').val(),$('#config_item_ezf_id').val());
    pic_fields($('#config_ref_form2').val(),$('#config_item_ezf_id').val());
    var conditions = " . json_encode($conditions) . ";
    
    if(conditions && Object.keys(conditions).length > 0){
            $.each(conditions,function(i,e){
                onLoadCondition(i,'onLoad');
            }); 
        }
    
    $('#config_ezf_id').on('change',function(){
      form_ref($(this).val(),$('#config_ref_form').val(),$('#config_left_ref_form').val());
      form_left_ref($(this).val(),$('#config_ref_form').val(),$('#config_left_ref_form').val());
    });
    
    $('#config_item_ezf_id').on('change',function(){
      form_ref2($(this).val(),$('#config_ref_form2').val(),$('#config_left_ref_form2').val());
      form_left_ref2($(this).val(),$('#config_ref_form2').val(),$('#config_left_ref_form2').val());
    });
    
    $('#ref_form_box').on('change','#config_ref_form',function(){
      fields($(this).val(),$('#config_left_ref_form').val(),$('#config_ezf_id').val());
      form_left_ref($('#config_ezf_id').val(),$(this).val(),$('#config_left_ref_form').val());
    });
    
    $('#left_ref_form_box').on('change','#config_left_ref_form',function(){
      fields($('#config_ref_form').val(),$(this).val(),$('#config_item_ezf_id').val());
    });
    
    $('#ref_form2_box').on('change','#config_ref_form2',function(){
      fields2($(this).val(),$('#config_left_ref_form2').val(),$('#config_item_ezf_id').val());
      fields_search($(this).val(),$('#config_item_ezf_id').val());
      form_left_ref2($('#config_item_ezf_id').val(),$(this).val(),$('#config_left_ref_form2').val());
      pic_fields($(this).val(),$('#config_item_ezf_id').val());
    });
    
    $('#left_ref_form_box2').on('change','#config_left_ref_form2',function(){
      fields2($('#config_ref_form2').val(),$(this).val(),$('#config_item_ezf_id').val());
      fields_search($(this).val(),$('#config_item_ezf_id').val());
      pic_fields($(this).val(),$('#config_item_ezf_id').val());
    });
    
    function fields(ezf_id,ezf_id2,main_ezf_id){
        var value = ".$value_fields.";
        var name = '$attrname_fields';
        var value_ref = ".$value_ref.";
        var value_ref2 = ".$value_left_ref.";
            
        if (ezf_id) {
            value_ref = ezf_id;
        }
        if (ezf_id2) {
            value_ref2 = ezf_id2;
        }
        var value_merge = value_ref;
        if ($.isArray(value_ref2) && $.isArray(value_ref))
            value_merge = $.merge(value_ref, value_ref2);
            
        $.post('" . Url::to(['/thaihis/patient-visit/get-fields-forms']) . "',{ ezf_id: value_merge, main_ezf_id:main_ezf_id, multiple:1, name: '{$attrname_fields}', value: value ,id:'config_fields'}
          ).done(function(result){
             $('#ref_field_box').html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }

    function form_ref(ezf_id,value_ref,value_ref2){ 
        var value = ". $value_ref.";
        if (value_ref) {
            value = value_ref;
        }

        var value_merge = value;
        if ($.isArray(value_ref2) && $.isArray(value_ref))
            value_merge = $.merge(value_ref, value_ref2);
            
        $.post('" . Url::to(['/thaihis/patient-visit/get-form-ref2']) . "',{ ezf_id: ezf_id, multiple:1, name: '{$attrname_ref}', value_ref: value_merge ,id:'config_ref_form'}
          ).done(function(result){
             $('#ref_form_box').html(result);
             //$('#add-condition').attr('data-ezf_id',$('#config_ezf_id').val());
             //fields($('#ezf_target_id').val());
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }

    function form_left_ref(ezf_id, value_ref, value_ref2) {
        var value = ".$value_left_ref.";
        var name = '".$attrname_left_ref."';
        if (value_ref2) {
            value = value_ref2;
        }
        var value_merge = value;
        if ($.isArray(value_ref2) && $.isArray(value_ref))
            value_merge = $.merge(value_ref, value_ref2);
            
        $.post('".Url::to(['/thaihis/configs/get-form-ref2'])."', {ezf_id: ezf_id, multiple: 1, name: name, value_ref: value, value_merge: value_merge, id: 'config_left_ref_form'}
        ).done(function (result) {
            $('#left_ref_form_box').html(result);
            //$('#add-condition').attr('data-ezf_id',$('#config_ezf_id').val());
            //fields($('#ezf_target_id').val());
        }).fail(function () {
        ". \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') .";
            console.log('server error');
        });
    }
    
    function form_left_ref2(ezf_id, value_ref, value_ref2) {
        var value = ".$value_left_ref2.";
        var name = '".$attrname_left_ref2."';
        if (value_ref2) {
            value = value_ref2;
        }
        var value_merge = value_ref;
        if ($.isArray(value_ref2) && $.isArray(value_ref))
            value_merge = $.merge(value_ref, value_ref2);
            
        $.post('".Url::to(['/thaihis/configs/get-form-ref2'])."', {ezf_id: ezf_id, multiple: 1, name: name, value_ref: value, value_merge: value_merge, id: 'config_left_ref_form2'}
        ).done(function (result) {
            $('#left_ref_form_box2').html(result);
            //$('#add-condition').attr('data-ezf_id',$('#config_ezf_id').val());
            //fields($('#ezf_target_id').val());
        }).fail(function () {
        ". \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') .";
            console.log('server error');
        });
    }
    
    function fields2(ezf_id,ezf_id2,main_ezf_id){
        var value = " . $value_fields2 . ";
        var value_ref2 = " . $value_ref2 . ";
        var value_ref = " . $value_ref . ";
        if (ezf_id) {
            value_ref = ezf_id;
        }
        if (ezf_id2) {
            value_ref2 = ezf_id2;
        }
        var value_merge = value_ref;
        if ($.isArray(value_ref2) && $.isArray(value_ref))
            value_merge = $.merge(value_ref, value_ref2);
        
        $.post('" . Url::to(['/thaihis/patient-visit/get-fields-forms']) . "',{ ezf_id: value_merge, main_ezf_id:main_ezf_id, multiple:1, name: '{$attrname_fields2}', value: value ,id:'config_fields2'}
          ).done(function(result){
             $('#ref_field2_boxs').html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }

    function form_ref2(ezf_id,value_ref,value_ref2){ 
        var value = " . $value_ref2 . ";
        if (value_ref2) {
            value = value_ref2;
        }

        var value_merge = value;
        if ($.isArray(value_ref2) && $.isArray(value_ref))
            value_merge = $.merge(value_ref, value_ref2);
        $.post('" . Url::to(['/thaihis/patient-visit/get-form-ref2']) . "',{ ezf_id: ezf_id, multiple:1, name: '{$attrname_ref2}', value_ref: value_merge ,id:'config_ref_form2'}
          ).done(function(result){
             $('#ref_form2_box').html(result);
             //$('#add-condition').attr('data-ezf_id',$('#config_ezf_id').val());
             //fields($('#ezf_target_id').val());
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    } 
    
    function fields_search(ezf_id,main_ezf_id){
        var value = " . $value_fields_search . ";
        var value_ref = " . $value_ref2 . ";
        if(ezf_id){
            value_ref = ezf_id;
        }
        $.post('" . Url::to(['/thaihis/patient-visit/get-fields-forms2']) . "',{ ezf_id: value_ref, main_ezf_id:main_ezf_id, multiple:1, name: '{$attrname_fields_search}', value: value ,id:'config_fields_search'}
          ).done(function(result){
             $('#ref_field_search_box').html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
    function pic_fields(ezf_id,main_ezf_id){
        var value = '{$value_image_field}';
        var value_ref = " . $value_ref . ";
        if(ezf_id){
            value_ref = ezf_id;
        }
        $.post('" . Url::to(['/thaihis/patient-visit/get-fields-forms']) . "',{ ezf_id: value_ref, main_ezf_id:main_ezf_id, multiple:0, name: '{$attrname_image_field}', value: value ,id:'config_pic_fields'}
          ).done(function(result){
             $('#pic_field_box').html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
$('#btn-add-condition').on('click',function(){
    var key_index = getMilisecTime();
    onLoadCondition(key_index,'addNew');
});

function onLoadCondition(index,act){
    var value_ref = ".$value_ref2.";
    var value_ref2 = ".$value_left_ref2.";
    var value_merge = value_ref;
    if ($.isArray(value_ref2) && $.isArray(value_ref))
        value_merge = $.merge(value_ref, value_ref2);
        
    var ezf_id = $('#config_ref_form2').val();
    var main_ezf_id = $('#config_item_ezf_id').val();
    var div_condition = $('#display-condition');
    if(ezf_id){
        value_ref = ezf_id;
    }
    
    var url = '" . Url::to(['/thaihis/configs/add-newcondition', 'conditions' => $conditions]) . "';
      $.get(url,{ezf_id:value_merge,main_ezf_id:main_ezf_id,act:act,key_index:index},function(result){
          div_condition.append(result);
      });
}
function getMilisecTime(){
    var d = new Date();
    var key_index = d.getFullYear() +''+ d.getMonth() +''+ d.getDate() +''+ d.getHours() +''+ d.getMinutes() +''+ d.getSeconds() +''+ d.getMilliseconds();
    return key_index;
 } 
");
?>
