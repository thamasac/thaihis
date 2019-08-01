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
$user_id = \Yii::$app->user->id;
$target = backend\modules\ezforms2\classes\EzfQuery::getTargetOne($ezf_id);
if ($target) {
    $itemsEzform = \backend\modules\ezforms2\classes\EzfQuery::getEzformRef($ezf_id, $target['parent_ezf_id']);
} else {
    $itemsEzform = \backend\modules\ezforms2\classes\EzfQuery::getEzformAll($ezf_id);
}

$itemsWidget = backend\modules\subjects\classes\SubjectManagementQuery::getWidgetByUserId($user_id);
?>

<div class="modal-header" style="margin-bottom: 15px;">
    <h4 class="modal-title" ><?= Yii::t('ezmodule', 'Widget Config') ?></h4>
</div>

<!--config start-->

<div class="form-group row">
    <div class="col-md-6 ">
        <?php
        $attrname_subject_profile_ezf = 'options[subject_profile_ezf]';
        $subject_profile_ezf = isset($options['subject_profile_ezf']) ? $options['subject_profile_ezf'] : '';
        ?>
        <?= Html::label(Yii::t('ezmodule', 'Subject Profile Form'), $attrname_subject_profile_ezf, ['class' => 'control-label']) ?>
        <?php
        echo kartik\select2\Select2::widget([
            'name' => $attrname_subject_profile_ezf,
            'value' => $subject_profile_ezf,
            'options' => ['placeholder' => Yii::t('ezmodule', 'Form'), 'id' => 'config_subject_profile_ezf'],
            'data' => ArrayHelper::map($itemsEzform, 'ezf_id', 'ezf_name'),
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
    <div class="col-md-6 sdbox-col">
        <?php
        $attrname_profile_field_subject = 'options[profile_field_subject]';
        $profile_field_subject = isset($options['profile_field_subject']) ? $options['profile_field_subject'] : '';
        ?>
        <?= Html::label(Yii::t('ezform', 'Field Subject Number'), $attrname_profile_field_subject, ['class' => 'control-label']) ?>
        <div id="profile_field_subject_box">

        </div>
    </div>

    <div class="clearfix"></div>
</div>
<div class="form-group row">
    <div class="col-md-6">
        <?php
        $attrname_profile_field_display = 'options[profile_field_display]';
        $profile_field_display = isset($options['profile_field_display']) ? $options['profile_field_display'] : '';
        ?>
        <?= Html::label(Yii::t('ezform', 'Field Display'), $attrname_profile_field_display, ['class' => 'control-label']) ?>
        <div id="profile_field_display_box">

        </div>
    </div>

    <div class="clearfix"></div>
</div>
<div class="form-group row">
    <div class="col-md-6 ">
        <?php
        $attrname_subject_detail_ezf = 'options[subject_detail_ezf]';
        $subject_detail_ezf = isset($options['subject_detail_ezf']) ? $options['subject_detail_ezf'] : '';
        ?>
        <?= Html::label(Yii::t('ezmodule', 'Subject Detail Form'), $attrname_subject_profile_ezf, ['class' => 'control-label']) ?>
        <?php
        echo kartik\select2\Select2::widget([
            'name' => $attrname_subject_detail_ezf,
            'value' => $subject_detail_ezf,
            'options' => ['placeholder' => Yii::t('ezmodule', 'Form'), 'id' => 'config_subject_detail_ezf'],
            'data' => ArrayHelper::map($itemsEzform, 'ezf_id', 'ezf_name'),
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
    <div class="col-md-6">
        <?php
        $attrname_detail_field_display = 'options[detail_field_display]';
        $detail_field_display = isset($options['detail_field_display']) ? $options['detail_field_display'] : '';
        ?>
        <?= Html::label(Yii::t('ezform', 'Field Display For Subject'), $attrname_detail_field_display, ['class' => 'control-label']) ?>
        <div id="detail_field_display_box">

        </div>
    </div>

    <div class="clearfix"></div>
</div>
<div class="form-group row">
    <div class="col-md-6">
        <?php
        $attrname_detail_field_display2 = 'options[detail_field_display2]';
        $detail_field_display2 = isset($options['detail_field_display2']) ? $options['detail_field_display2'] : '';
        ?>
        <?= Html::label(Yii::t('ezform', 'Field Display '), $attrname_detail_field_display2, ['class' => 'control-label']) ?>
        <div id="detail_field_display2_box">

        </div>
    </div>

    <div class="clearfix"></div>
</div>
<div class="form-group row">
    <div class="col-md-6 ">
        <?php
        $attrname_widget_id = 'options[schedule_widget_id]';
        $value_widget_id = isset($options['schedule_widget_id']) ? $options['schedule_widget_id'] : '';
        ?>
        <?= Html::label(Yii::t('ezmodule', 'Visit Schedule Widget'), $attrname_widget_id, ['class' => 'control-label']) ?>
        <?php
        echo kartik\select2\Select2::widget([
            'name' => $attrname_widget_id,
            'value' => $value_widget_id,
            'options' => ['placeholder' => Yii::t('ezmodule', 'Widget'), 'id' => 'schedule_widget_id'],
            'data' => ArrayHelper::map($itemsWidget, 'widget_id', 'widget_name'),
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
    <div class="clearfix"></div>
</div>

<!--config end-->

<?php
$this->registerJS("
    fieldProfileDisplay($('#config_subject_profile_ezf').val());
    fieldDetailDisplay($('#config_subject_detail_ezf').val());
    fieldDetailDisplay2($('#config_subject_detail_ezf').val());
    fieldProfileSubject($('#config_subject_profile_ezf').val());

    $('#config_subject_profile_ezf').on('change',function(){
      var ezf_id = $(this).val();
      fieldProfileDisplay(ezf_id);
      fieldProfileSubject(ezf_id);
    });
    $('#config_subject_detail_ezf').on('change',function(){
      var ezf_id = $(this).val();
      fieldDetailDisplay(ezf_id);
      fieldDetailDisplay2(ezf_id);
    });
    
    function fieldProfileDisplay(ezf_id){
        var value = " . json_encode($profile_field_display) . ";
        $.post('" . Url::to(['/ezforms2/target/get-fields']) . "',{ ezf_id: ezf_id, multiple:1, name: '{$attrname_profile_field_display}', value: value ,id:'config_profile_field_display'}
          ).done(function(result){
             $('#profile_field_display_box').html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
function fieldProfileSubject(ezf_id){
        var value = " . json_encode($profile_field_subject) . ";
        $.post('" . Url::to(['/ezforms2/target/get-fields']) . "',{ ezf_id: ezf_id, multiple:0, name: '{$attrname_profile_field_subject}', value: value ,id:'config_profile_field_subject'}
          ).done(function(result){
             $('#profile_field_subject_box').html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
    function fieldDetailDisplay(ezf_id){
        var value = " . json_encode($detail_field_display) . ";
        $.post('" . Url::to(['/ezforms2/target/get-fields']) . "',{ ezf_id: ezf_id, multiple:1, name: '{$attrname_detail_field_display}', value: value ,id:'config_detail_field_display'}
          ).done(function(result){
             $('#detail_field_display_box').html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
    function fieldDetailDisplay2(ezf_id){
        var value = " . json_encode($detail_field_display2) . ";
        $.post('" . Url::to(['/ezforms2/target/get-fields']) . "',{ ezf_id: ezf_id, multiple:1, name: '{$attrname_detail_field_display2}', value: value ,id:'config_detail_field_display2'}
          ).done(function(result){
             $('#detail_field_display2_box').html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
");
