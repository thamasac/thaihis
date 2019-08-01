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
$key = '11111';
$key2 = '22222';

$target = backend\modules\ezforms2\classes\EzfQuery::getTargetOne($ezf_id);
if ($target) {
    $itemsEzform = \backend\modules\ezforms2\classes\EzfQuery::getEzformRef($ezf_id, $target['parent_ezf_id']);
} else {
    $itemsEzform = \backend\modules\ezforms2\classes\EzfQuery::getEzformAll($ezf_id);
}

$user_id = \Yii::$app->user->id;
$itemsWidget = backend\modules\subjects\classes\SubjectManagementQuery::getWidgetByUserId($user_id);

if (isset($options[$key]['type_system']) && $options[$key]['type_system'] == '')
    $options[$key]['type_system'] = '1';
?>

<div class="modal-header" style="margin-bottom: 15px;">
    <h4 class="modal-title" ><?= Yii::t('ezmodule', 'Widget Config') ?></h4>
</div>

<!--config start-->
<div class="form-group row">
    <div class="col-md-6">
        <?php 
        $type_value = $options['type_system'];
        $type_system_name = 'options[type_system]'; ?>
        
        <?php
        echo Html::radio($type_system_name, $options['type_system'] == '1' ? '1' : '0', ['value' => '1']);
        ?>
        <?= Html::label(Yii::t('ezmodule', 'Observational'), $type_system_name, ['class' => 'control-label']) ?>
        
        
        <?php
        echo Html::radio($type_system_name, $options['type_system'] == '2' ? '1' : '0', ['value' => '2']);
        ?>
        <?= Html::label(Yii::t('ezmodule', 'Randomized controlled Trial(RCT)'), 'options[' . $key . '][type_system]', ['class' => 'control-label']) ?>
    </div>
    <div class="clearfix"></div>
</div>
<div class="form-group row">
    <div class="col-md-6">
        <?php 
        $group_name = isset($options['group_name'])?$options['group_name']:'';
        $attr_group_name= 'options[group_name]'; ?>
        <?= Html::label(Yii::t('subjects', 'Group Name'), $attr_group_name, ['class' => 'control-label']) ?>
        <?php
        echo Html::textInput($attr_group_name,$group_name , ['class' => 'form-control']);
        ?>
       
    </div>
    <div class="clearfix"></div>
</div>
<hr/>
<h4 class="modal-title" ><?= Yii::t('ezmodule', 'Screening Visit') ?></h4>
<br/>
<div class="form-group row">
    <div class="col-md-6">
        <?= Html::label(Yii::t('ezmodule', 'Form Name'), 'options[' . $key . '][form_name]', ['class' => 'control-label']) ?>
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

    <div class="col-md-4 ">
        <?php
        $attrname_ezf_id = 'options[' . $key . '][main_ezf_id]';
        $value_ezf_id = isset($options[$key]['main_ezf_id']) ? $options[$key]['main_ezf_id'] : '';
        ?>
        <?= Html::label(Yii::t('ezmodule', 'Forms'), $attrname_ezf_id, ['class' => 'control-label']) ?>
        <?php
        echo kartik\select2\Select2::widget([
            'name' => $attrname_ezf_id,
            'value' => $value_ezf_id,
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
        $attrname_main_actual_date = 'options[' . $key . '][main_actual_date]';
        $main_actual_date = isset($options[$key]['main_actual_date']) ? $options[$key]['main_actual_date'] : '';
        ?>
        <?= Html::label(Yii::t('ezform', 'Actual Date'), $attrname_main_actual_date, ['class' => 'control-label']) ?>
        <div id="main_actual_date_box">

        </div>
    </div>
    <div class="col-md-4 sdbox-col">
        <?php
        $attrname_main_visit_name = 'options[' . $key . '][main_visit_name]';
        $main_visit_name = isset($options[$key]['main_visit_name']) ? $options[$key]['main_visit_name'] : '';
        ?>
        <?= Html::label(Yii::t('ezform', 'Visit Name'), $attrname_main_visit_name, ['class' => 'control-label']) ?>
        <div id="main_visit_name_box">

        </div>
    </div>
    <div class="clearfix"></div>

</div>
<div class="form-group row">
    <div class="col-md-4">
        <?php
        $attrname_main_field_display = 'options[' . $key . '][main_field_display]';
        $main_field_display = isset($options[$key]['main_field_display']) ? $options[$key]['main_field_display'] : '';
        ?>
        <?= Html::label(Yii::t('ezform', 'Field Display'), $attrname_main_field_display, ['class' => 'control-label']) ?>
        <div id="main_field_display_box">

        </div>
    </div>
    <div class="col-md-4 sdbox-col">
        <?= Html::label(Yii::t('ezmodule', 'Distance Date'), 'options[' . $key . '][main_earliest_distance]', ['class' => 'control-label']) ?>
        <?php
        echo Html::input('number', 'options[' . $key . '][main_earliest_distance]', $options[$key]['main_earliest_distance'], ['class' => 'form-control', 'step' => '1', 'min' => '-100', 'max' => '100']);
        ?>
    </div>
    <div class="col-md-4 sdbox-col">
        <?= Html::label(Yii::t('ezmodule', 'Distance Date'), 'options[' . $key . '][main_latest_distance]', ['class' => 'control-label']) ?>
        <?php
        echo Html::input('number', 'options[' . $key . '][main_latest_distance]', $options[$key]['main_latest_distance'], ['class' => 'form-control', 'step' => '1', 'min' => '-100', 'max' => '100']);
        ?>
    </div>
    <div class="clearfix"></div>
</div>
<hr/>
<div class="schedule-rct-config">

</div>
<hr/>
<h4 class="modal-title" ><?= Yii::t('ezmodule', 'Subject Form') ?></h4>
<br/>
<div class="form-group row">
    <div class="col-md-4 ">
        <?php
        $attrname_subject_ezf_id = 'options[subject_ezf_id]';
        $subject_ezf_id = isset($options['subject_ezf_id']) ? $options['subject_ezf_id'] : '';
        ?>
        <?= Html::label(Yii::t('ezmodule', 'Subject Profile Form'), $attrname_subject_ezf_id, ['class' => 'control-label']) ?>
        <?php
        echo kartik\select2\Select2::widget([
            'name' => $attrname_subject_ezf_id,
            'value' => $subject_ezf_id,
            'options' => ['placeholder' => Yii::t('ezmodule', 'Widget'), 'id' => 'config_subject_ezf_id'],
            'data' => ArrayHelper::map($itemsEzform, 'ezf_id', 'ezf_name'),
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
    <div class="col-md-4 sdbox-col">
        <?php
        $attrname_subject_field = 'options[subject_field]';
        $subject_field = isset($options['subject_field']) ? $options['subject_field'] : '';
        ?>
        <?= Html::label(Yii::t('ezform', 'Subject Number Field'), $attrname_subject_field, ['class' => 'control-label']) ?>
        <div id="subject_field_box">

        </div>
    </div>
    <div class="col-md-4 sdbox-col">
        <?php
        $attrname_subject_field_display = 'options[subject_field_display]';
        $subject_field_display = isset($options['subject_field_display']) ? $options['subject_field_display'] : '';
        ?>
        <?= Html::label(Yii::t('ezform', 'Subject Display Field'), $attrname_subject_field_display, ['class' => 'control-label']) ?>
        <div id="subject_field_display_box">

        </div>
    </div>
    <div class="clearfix"></div>
</div>
<div class="form-group row">
    <div class="col-md-6 ">
        <?php
        $attrname_group_ezf_id = 'options[group_ezf_id]';
        $group_ezf_id = isset($options['group_ezf_id']) ? $options['group_ezf_id'] : '';
        ?>
        <?= Html::label(Yii::t('ezmodule', 'Subject Group Form'), $attrname_group_ezf_id, ['class' => 'control-label']) ?>
        <?php
        echo kartik\select2\Select2::widget([
            'name' => $attrname_group_ezf_id,
            'value' => $group_ezf_id,
            'options' => ['placeholder' => Yii::t('ezmodule', 'Widget'), 'id' => 'config_group_ezf_id'],
            'data' => ArrayHelper::map($itemsEzform, 'ezf_id', 'ezf_name'),
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
    <div class="col-md-6 sdbox-col">
        <?php
        $attrname_group_field = 'options[group_field]';
        $group_field = isset($options['group_field']) ? $options['group_field'] : '';
        ?>
        <?= Html::label(Yii::t('ezform', 'Group Name Field'), $attrname_group_field, ['class' => 'control-label']) ?>
        <div id="group_field_box">

        </div>
    </div>
    <div class="clearfix"></div>
</div>
<h4 class="modal-title" ><?= Yii::t('ezmodule', 'Individual drilldown') ?></h4>
<br/>
<div class="form-group row">
    <div class="col-md-6 ">
        <?php
        $attrname_widget_id = 'options[individual_widget_id]';
        $value_widget_id = isset($options['individual_widget_id']) ? $options['individual_widget_id'] : '';
        ?>
        <?= Html::label(Yii::t('ezmodule', 'Individual Drilldown Widget'), $attrname_widget_id, ['class' => 'control-label']) ?>
        <?php
        echo kartik\select2\Select2::widget([
            'name' => $attrname_widget_id,
            'value' => $value_widget_id,
            'options' => ['placeholder' => Yii::t('ezmodule', 'Widget'), 'id' => 'config_widget_id'],
            'data' => ArrayHelper::map($itemsWidget, 'widget_id', 'widget_name'),
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>

    <div class="clearfix"></div>
</div>
<br/>
<!--config end-->

<?php
$this->registerJS("
    fieldActualDateMain($('#config_main_ezf_id').val());
    fieldVisitNameMain($('#config_main_ezf_id').val());
    fieldDisplayMain($('#config_main_ezf_id').val());
    fieldSubjectForm($('#config_subject_ezf_id').val());
    fieldDisplaySubject($('#config_subject_ezf_id').val());
    fieldGroupForm($('#config_group_ezf_id').val());

    $('#config_main_ezf_id').on('change',function(){
      var ezf_id = $(this).val();
      fieldActualDateMain(ezf_id);
      fieldVisitNameMain(ezf_id);
      fieldDisplayMain(ezf_id);
    });
    
    $('#config_subject_ezf_id').on('change',function(){
      var ezf_id = $(this).val();
      fieldSubjectForm(ezf_id);
      fieldDisplaySubject(ezf_id);
    });
    
    $('#config_group_ezf_id').on('change',function(){
      var ezf_id = $(this).val();
      fieldGroupForm(ezf_id);
    });
    
    $(function(){
        var val_type = '$type_value';
        console.log(val_type);
        var rct_show = $('.schedule-rct-config');
        if(val_type=='2'){
            rct_show.html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
            var url = '/subjects/subject-management/random-form-config';
            var options = ".json_encode($options).";
            data = {key:'$key2',options:JSON.stringify(options)};
            $.get(url,data,function(data){
                rct_show.html(data);
            });
        }
    })
    
    $('input[name=\'" . $type_system_name . "\']').on('change', function(){
        var val_type = $(this).val();
        var rct_show = $('.schedule-rct-config');
        rct_show.html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
        var url = '/subjects/subject-management/random-form-config';
        data = {key:'$key2'};
        if(val_type=='2'){
            $.get(url,data,function(data){
                rct_show.html(data);
            });
        }else{
            rct_show.empty();
        }
    })
    
    function fieldActualDateMain(ezf_id){
        var value = " . json_encode($main_actual_date) . ";
        $.post('" . Url::to(['/ezforms2/target/get-fields']) . "',{ ezf_id: ezf_id, multiple:0, name: '{$attrname_main_actual_date}', value: value ,id:'config_main_actual_date'}
          ).done(function(result){
             $('#main_actual_date_box').html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
function fieldVisitNameMain(ezf_id){
        var value = " . json_encode($main_visit_name) . ";
        $.post('" . Url::to(['/ezforms2/target/get-fields']) . "',{ ezf_id: ezf_id, multiple:0, name: '{$attrname_main_visit_name}', value: value ,id:'config_main_visit_name'}
          ).done(function(result){
             $('#main_visit_name_box').html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    function fieldDisplayMain(ezf_id){
        var value = " . json_encode($main_field_display) . ";
        $.post('" . Url::to(['/ezforms2/target/get-fields']) . "',{ ezf_id: ezf_id, multiple:1, name: '{$attrname_main_field_display}', value: value ,id:'config_main_field_display'}
          ).done(function(result){
             $('#main_field_display_box').html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    function fieldSubjectForm(ezf_id){
        var value = " . json_encode($subject_field) . ";
        $.post('" . Url::to(['/ezforms2/target/get-fields']) . "',{ ezf_id: ezf_id, multiple:0, name: '{$attrname_subject_field}', value: value ,id:'config_subject_field'}
          ).done(function(result){
             $('#subject_field_box').html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
function fieldDisplaySubject(ezf_id){
        var value = " . json_encode($subject_field_display) . ";
        $.post('" . Url::to(['/ezforms2/target/get-fields']) . "',{ ezf_id: ezf_id, multiple:1, name: '{$attrname_subject_field_display}', value: value ,id:'config_subject_field_display'}
          ).done(function(result){
             $('#subject_field_display_box').html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
    function fieldGroupForm(ezf_id){
        var value = " . json_encode($group_field) . ";
        $.post('" . Url::to(['/ezforms2/target/get-fields']) . "',{ ezf_id: ezf_id, multiple:0, name: '{$attrname_group_field}', value: value ,id:'config_group_field'}
          ).done(function(result){
             $('#group_field_box').html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    

");
?>