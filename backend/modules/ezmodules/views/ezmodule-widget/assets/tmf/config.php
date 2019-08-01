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
$itemsEzform = \backend\modules\ezforms2\classes\EzfQuery::getEzformCoDevAll();
?>

<div class="modal-header" style="margin-bottom: 15px;">
    <h4 class="modal-title" ><?= Yii::t('ezmodule', 'Widget Config') ?></h4>
</div>

<!--config start-->

<div class="form-group row">
    <div class="col-md-12">
        <?= Html::label(Yii::t('ezform', 'Title'), 'options[title]', ['class' => 'control-label']) ?>
        <?= Html::textInput('options[title]', (isset($options['title']) ? $options['title'] : Yii::t('ezform', 'Title')), ['class' => 'form-control']) ?>
    </div>

</div>

<div class="form-group row">
    <div class="col-md-6 ">
        <?php
        $attrname_ezf_type_id = 'options[ezf_type_id]';
        $value_ezf_type_id = isset($options['ezf_type_id']) ? $options['ezf_type_id'] : '';
        ?>
        <?= Html::label(Yii::t('ezform', 'Form Document Type'), $attrname_ezf_type_id, ['class' => 'control-label']) ?>
        <?php
        echo kartik\select2\Select2::widget([
            'name' => $attrname_ezf_type_id,
            'value' => $value_ezf_type_id,
            'options' => ['placeholder' => Yii::t('ezform', 'Form Document Type'), 'id' => 'config_ezf_type_id'],
            'data' => ArrayHelper::map($itemsEzform, 'ezf_id', 'ezf_name'),
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
    <div class="col-md-3 sdbox-col">
        <?php
        $attrname_type_field_label = 'options[type_field_label]';
        $field_type_label = isset($options['type_field_label'])? $options['type_field_label'] : '';
        ?>
        <?= Html::label(Yii::t('ezform', 'Field Label'), $attrname_type_field_label, ['class' => 'control-label']) ?>
        <div id="type_field_label_box">

        </div>
    </div>
    <div class="col-md-3 sdbox-col">
        <?php
        $attrname_type_field_value = 'options[type_field_value]';
        $field_type_value = isset($options['type_field_value']) ? $options['type_field_value'] : '';
        ?>
        <?= Html::label(Yii::t('ezform', 'Field Value'), $attrname_type_field_value, ['class' => 'control-label']) ?>
        <div id="type_field_value_box">

        </div>
    </div>
</div>

<div class="form-group row">

    <div class="col-md-6">
        <?php
        $attrname_type_fields = 'options[type_fields]';
        $value_type_fields_json = isset($options['type_fields']) ?$options['type_fields'] : '';
        ?>
        <?= Html::label(Yii::t('ezform', 'Fields'), $attrname_type_fields, ['class' => 'control-label']) ?>
        <div id="type_ref_field_box">

        </div>
    </div>

    <div class="col-md-6 sdbox-col">
        <?php
//        $attrname_type_order = 'options[type_order]';
//        $value_type_order = isset($options['type_order'])?$options['type_order'] : '';
//        echo Html::label(Yii::t('ezform', 'Order Fields'), $attrname_order, ['class' => 'control-label']) ?>
<!--        <div id="type_order_field_box">

        </div>-->
    </div>
</div>
<hr/>
<div class="form-group row">
    <div class="col-md-6 ">
        <?php
        $attrname_ezf_name_id = 'options[ezf_name_id]';
        $value_ezf_name_id = isset($options['ezf_name_id']) ? $options['ezf_name_id'] : '';
        ?>
        <?= Html::label(Yii::t('ezform', 'Form Document Name'), $attrname_ezf_name_id, ['class' => 'control-label']) ?>
        <?php
        echo kartik\select2\Select2::widget([
            'name' => $attrname_ezf_name_id,
            'value' => $value_ezf_name_id,
            'options' => ['placeholder' => Yii::t('ezform', 'Form Document Name'), 'id' => 'config_ezf_name_id'],
            'data' => ArrayHelper::map($itemsEzform, 'ezf_id', 'ezf_name'),
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
    
<!--</div>

<div class="form-group row">-->

    <div class="col-md-6">
        <?php
        $attrname_name_fields = 'options[name_fields]';
        $value_name_fields_json = isset($options['name_fields']) && is_array($options['name_fields']) ? \appxq\sdii\utils\SDUtility::array2String($options['name_fields']) : '{}';
//        $value_name_fields_json = isset($options['name_fields'])? $options['name_fields'] : '';
        ?>
        <?= Html::label(Yii::t('ezform', 'Fields'), $attrname_name_fields, ['class' => 'control-label']) ?>
        <div id="name_ref_field_box">

        </div>
    </div>

    <div class="col-md-6 sdbox-col">
        <?php
//        $attrname_name_order = 'options[name_order]';
////        $value_name_order = isset($options['name_order']) && is_array($options['name_order']) ? \appxq\sdii\utils\SDUtility::array2String($options['name_order']) : '{}';
//        $value_name_order = isset($options['name_order'])  ? $options['name_order'] : '';
//        echo Html::label(Yii::t('ezform', 'Order Fields'), $attrname_name_order, ['class' => 'control-label']) ?>
<!--        <div id="name_order_field_box">

        </div>-->
    </div>
</div>

<hr/>

<div class="form-group row">
    <div class="col-md-6 ">
        <?php
        $attrname_ezf_detail_id = 'options[ezf_detail_id]';
        $value_ezf_detail_id = isset($options['ezf_detail_id']) ? $options['ezf_detail_id'] : '';
        ?>
        <?= Html::label(Yii::t('ezform', 'Form Document Detail'), $attrname_ezf_detail_id, ['class' => 'control-label']) ?>
        <?php
        echo kartik\select2\Select2::widget([
            'name' => $attrname_ezf_detail_id,
            'value' => $value_ezf_detail_id,
            'options' => ['placeholder' => Yii::t('ezform', 'Form Document Detail'), 'id' => 'config_ezf_detail_id'],
            'data' => ArrayHelper::map($itemsEzform, 'ezf_id', 'ezf_name'),
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
    
    <div class="col-md-6 sdbox-col" >
        <?php
        $attrname_ref_form_detail = 'options[ref_form_detail]';
        $value_ref_form_detail = isset($options['ref_form_detail']) ? $options['ref_form_detail'] : '';
        ?>
        <?= Html::label(Yii::t('ezform', 'Field Version'), $attrname_ref_form_detail, ['class' => 'control-label']) ?>
        <div id="ref_form_detail_box">

        </div>
    </div>
    
</div>

<div class="form-group row">

    <div class="col-md-6">
        <?php
        $attrname_detail_fields = 'options[detail_fields]';
        $value_detail_fields_json = isset($options['detail_fields']) && is_array($options['detail_fields']) ? \appxq\sdii\utils\SDUtility::array2String($options['detail_fields']) : '{}';
        ?>
        <?= Html::label(Yii::t('ezform', 'Fields'), $attrname_detail_fields, ['class' => 'control-label']) ?>
        <div id="detail_ref_field_box">

        </div>
    </div>

    <div class="col-md-6 sdbox-col" >
        <?php
//        $attrname_detail_order = 'options[detail_order]';
//        $value_detail_order = isset($options['detail_order']) && is_array($options['detail_order']) ? \appxq\sdii\utils\SDUtility::array2String($options['detail_order']) : '{}';
//        echo Html::label(Yii::t('ezform', 'Order Fields'), $attrname_name_order, ['class' => 'control-label']) ?>
<!--        <div id="detail_order_field_box">

        </div>-->
    </div>
</div>

<hr/>

<div class="form-group row">
    <div class="col-md-3 ">
        <?php
        $attrname_pagesize = 'options[pagesize]';
        $value_pagesize = isset($options['pagesize']) ? $options['pagesize'] : 50;
        ?>
        <?= Html::label(Yii::t('ezform', 'Page Size'), $attrname_pagesize, ['class' => 'control-label']) ?>
        <?php
        echo Html::textInput($attrname_pagesize, $value_pagesize, ['class' => 'form-control ', 'type' => 'number']);
        ?>
    </div>
    <div class="col-md-3 sdbox-col">
        <?php
        $attrname_order_by = 'options[order_by]';
        $value_order_by = isset($options['order_by']) ? $options['order_by'] : 4; //4-ASC - 3-DESC
        echo Html::label(Yii::t('ezform', 'Order By'), $attrname_order_by, ['class' => 'control-label']) ;
        echo Html::dropDownList($attrname_order_by, $value_order_by, [4 => 'ASC', 3 => 'DESC'], ['class' => 'form-control ']);
        ?>
    </div>


</div>



<!--<div class="form-group row" >
    <div class="col-md-4">
        <?php // echo Html::hiddenInput('options[default_column]', 0); ?>
        <?php // echo backend\modules\ezforms2\classes\EzformWidget::checkbox('options[default_column]', isset($options['default_column']) ? $options['default_column'] : 1, ['label' => Yii::t('ezmodule', 'Enable Default Column')]); ?>
    </div>
</div>-->

<!--config end-->

<?php
$this->registerJS("
    //doc type table
    fields($('#config_ezf_type_id').val(),'" . $value_type_fields_json . "','" . $attrname_type_fields . "','type_config_field','type_ref_field_box',0);
   fields($('#config_ezf_type_id').val(),'" . $field_type_label . "','" . $attrname_type_field_label . "','type_config_field_lable','type_field_label_box',0);
    fields($('#config_ezf_type_id').val(),'" . $field_type_value . "','" . $attrname_type_field_value . "','type_config_field_value','type_field_value_box',0);
        
    $('#config_ezf_type_id').on('change',function(){
        var ezf_id = $(this).val();
        fields(ezf_id,'" . $value_type_fields_json . "','" . $attrname_type_fields . "','type_config_field','type_ref_field_box',0);
      fields(ezf_id,'" . $field_type_label . "','" . $attrname_type_field_label . "','type_config_field_lable','type_field_label_box',0);
        fields(ezf_id,'" . $field_type_value . "','" . $attrname_type_field_value . "','type_config_field_value','type_field_value_box',0);
    });
    


    //doc name teble
    fields($('#config_ezf_name_id').val()," . $value_name_fields_json . ",'" . $attrname_name_fields . "','name_config_field','name_ref_field_box',1);
  
    $('#config_ezf_name_id').on('change',function(){
        var ezf_id = $(this).val();
        fields(ezf_id," . $value_name_fields_json . ",'" . $attrname_name_fields . "','name_config_field','name_ref_field_box',1);
  });
    
    
    //doc detail teble
    fields($('#config_ezf_detail_id').val()," . $value_detail_fields_json . ",'" . $attrname_detail_fields . "','detail_config_field','detail_ref_field_box',1);
 fields($('#config_ezf_detail_id').val(),'" . $value_ref_form_detail . "','" . $attrname_ref_form_detail . "','ref_form_config','ref_form_detail_box',0);
    
    $('#config_ezf_detail_id').on('change',function(){
        var ezf_id = $(this).val();
        fields(ezf_id," . $value_detail_fields_json . ",'" . $attrname_detail_fields . "','detail_config_field','detail_ref_field_box',1);
      fields(ezf_id,'" . $value_ref_form_detail . "','" . $attrname_ref_form_detail . "','ref_form_config','ref_form_detail_box',0);
    });



    function fields(ezf_id,value,attr_field,id,div,multi){
        $.post('" . Url::to(['/ezforms2/target/get-fields']) . "',{ ezf_id: ezf_id, multiple:multi, name: attr_field, value: value ,id:id}
          ).done(function(result){
             $('#'+div).html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
    function orderfields(ezf_id,value,attr_field,id,div){
        $.post('" . Url::to(['/ezforms2/target/get-fields']) . "',{ ezf_id: ezf_id, multiple:1, name: attr_field, value: value ,id:id}
          ).done(function(result){
             $('#'+div).html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
    
");
?>