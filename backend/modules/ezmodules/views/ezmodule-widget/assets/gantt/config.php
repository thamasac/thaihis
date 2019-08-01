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
$user_id = \Yii::$app->user->id;
$itemWidgets = backend\modules\subjects\classes\SubjectManagementQuery::getWidgetByUserId();

?>

<div class="modal-header" style="margin-bottom: 15px;">
    <h4 class="modal-title" ><?= Yii::t('ezmodule', 'Widget Config') ?></h4>
</div>

<!--config start-->
<div class="form-group row">

    <div class="col-md-6 ">
        <?php
        $check_type = isset($options['check_type']) ? $options['check_type'] : '';

        echo Html::radio('options[check_type]', $check_type == '1' ? '1' : '0', ['value' => '1']);
        echo Html::label(Yii::t('ezform', 'Project'), 'options[check_type]');
        ?>
        <?php
        echo Html::radio('options[check_type]', $check_type == '2' ? '1' : '0', ['value' => '2']);
        echo Html::label(Yii::t('ezform', 'Schedule'), 'options[check_type]');
        ?>

    </div>
    <div class="col-md-6 ">

    </div>
    <div class="clearfix"></div>

</div>
<div id="gantt_pms">
    <div class="form-group row">
        <div class="col-md-6 ">
            <?php
            $attrname_project_ezf_id = 'options[project_ezf_id]';
            $value_project_ezf_id = isset($options['project_ezf_id']) ? $options['project_ezf_id'] : '';
            ?>
            <?= Html::label(Yii::t('ezmodule', 'Main Task Forms'), $attrname_project_ezf_id, ['class' => 'control-label']) ?>
            <?php
            echo kartik\select2\Select2::widget([
                'name' => $attrname_project_ezf_id,
                'value' => $value_project_ezf_id,
                'options' => ['placeholder' => Yii::t('ezmodule', 'Forms'), 'id' => 'config_project_ezf_id'],
                'data' => ArrayHelper::map($itemsEzform, 'ezf_id', 'ezf_name'),
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
        </div>
        <div class="col-md-6 sdbox-col">
            <?php
            $attrname_project_name = 'options[project_name]';
            $project_name = isset($options['project_name']) ? $options['project_name'] : '';
            ?>
            <?= Html::label(Yii::t('ezform', 'Main Task Name'), $attrname_project_name, ['class' => 'control-label']) ?>
            <div id="project_name_box">

            </div>
        </div>

        <div class="clearfix"></div>

    </div>
    <div class="form-group row">

        <div class="col-md-6">
            <?php
            $attrname_cate_ezf_id = 'options[cate_ezf_id]';
            $value_cate_ezf_id = isset($options['cate_ezf_id']) ? $options['cate_ezf_id'] : '';
            ?>
            <?= Html::label(Yii::t('ezmodule', 'Sub-Task Forms'), $attrname_cate_ezf_id, ['class' => 'control-label']) ?>
            <?php
            echo kartik\select2\Select2::widget([
                'name' => $attrname_cate_ezf_id,
                'value' => $value_cate_ezf_id,
                'options' => ['placeholder' => Yii::t('ezmodule', 'Forms'), 'id' => 'config_cate_ezf_id'],
                'data' => ArrayHelper::map($itemsEzform, 'ezf_id', 'ezf_name'),
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
        </div>
        <div class="col-md-6 sdbox-col">
            <?php
            $attrname_cate_name = 'options[cate_name]';
            $cate_name = isset($options['cate_name']) ? $options['cate_name'] : '';
            ?>
            <?= Html::label(Yii::t('ezform', 'Sub-Task Name'), $attrname_cate_name, ['class' => 'control-label']) ?>
            <div id="cate_name_box">

            </div>
        </div>
        <div class="clearfix"></div>

    </div>

    <div class="form-group row">
        <div class="col-md-6">
            <?php
            $attrname_ezf_id = 'options[activity_ezf_id]';
            $value_ezf_id = isset($options['activity_ezf_id']) ? $options['activity_ezf_id'] : '';
            ?>
            <?= Html::label(Yii::t('ezmodule', 'Task Item Forms'), $attrname_ezf_id, ['class' => 'control-label']) ?>
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
        <div class="col-md-6 sdbox-col">
            <?php
            $attrname_task_name = 'options[task_name]';
            $task_name = isset($options['task_name']) ? $options['task_name'] : '';
            ?>
            <?= Html::label(Yii::t('ezform', 'Task Name'), $attrname_task_name, ['class' => 'control-label']) ?>
            <div id="task_name_box">

            </div>
        </div>
        <div class="clearfix"></div>

    </div>

    <div class="form-group row">
        <div class="col-md-4 ">
            <?php
            $attrname_start_date = 'options[start_date]';
            $start_date = isset($options['start_date']) ? $options['start_date'] : '';
            ?>
            <?= Html::label(Yii::t('ezform', 'Start Date'), $attrname_start_date, ['class' => 'control-label']) ?>
            <div id="start_date_box">

            </div>
        </div>
        <div class="col-md-4 sdbox-col">
            <?php
            $attrname_finish_date = 'options[finish_date]';
            $finish_date = isset($options['finish_date']) ? $options['finish_date'] : '';
            ?>
            <?= Html::label(Yii::t('ezform', 'Finish Date'), $attrname_start_date, ['class' => 'control-label']) ?>
            <div id="finish_date_box">

            </div>
        </div>

        <div class="clearfix"></div>

    </div>
    
    <div class="form-group row">
        <div class="col-md-12">
            <?php
            $attrname_other_ezforms = 'options[other_ezforms]';
            $value_other_ezforms = isset($options['other_ezforms']) ?$options['other_ezforms'] : [];
            ?>
            <?= Html::label(Yii::t('ezmodule', 'Task Item other form'), $attrname_other_ezforms, ['class' => 'control-label']) ?>
            <?php
            echo kartik\select2\Select2::widget([
                'name' => $attrname_other_ezforms,
                'value' => $value_other_ezforms,
                'options' => ['placeholder' => Yii::t('ezmodule', 'Forms'), 'id' => 'config_other_ezf_id','multiple'=>1],
                'data' => ArrayHelper::map($itemsEzform, 'ezf_id', 'ezf_name'),
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
        </div>
        <div class="clearfix"></div>

    </div>
    <div class="form-group row">
        <div class="col-md-6">
            <?php
            $attrname_response_ezf_id = 'options[response_ezf_id]';
            $value_response_ezf_id = isset($options['response_ezf_id']) ? $options['response_ezf_id'] : '';
            ?>
            <?= Html::label(Yii::t('ezmodule', 'Task Response Forms'), $attrname_response_ezf_id, ['class' => 'control-label']) ?>
            <?php
            echo kartik\select2\Select2::widget([
                'name' => $attrname_response_ezf_id,
                'value' => $value_response_ezf_id,
                'options' => ['placeholder' => Yii::t('ezmodule', 'Forms'), 'id' => 'config_response_ezf_id'],
                'data' => ArrayHelper::map($itemsEzform, 'ezf_id', 'ezf_name'),
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
        </div>
        <div class="clearfix"></div>

    </div>
    <div class="form-group row">
        <div class="col-md-6">
            <?php
            $attrname_calendar_widget_id = 'options[calendar_widget_id]';
            $value_calendar_widget_id = isset($options['calendar_widget_id']) ? $options['calendar_widget_id'] : '';
            ?>
            <?= Html::label(Yii::t('ezmodule', 'PMS Calendar widget'), $attrname_calendar_widget_id, ['class' => 'control-label']) ?>
            <?php
            echo kartik\select2\Select2::widget([
                'name' => $attrname_calendar_widget_id,
                'value' => $value_calendar_widget_id,
                'options' => ['placeholder' => Yii::t('ezmodule', 'Forms'), 'id' => 'config_calendar_widget_id'],
                'data' => ArrayHelper::map($itemWidgets, 'widget_id', 'widget_name'),
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
        </div>
        <div class="col-md-6">
            <?php
            $attrname_schedule_widget_id = 'options[schedule_widget_id]';
            $value_schedule_widget_id = isset($options['schedule_widget_id']) ? $options['schedule_widget_id'] : '';
            ?>
            <?= Html::label(Yii::t('ezmodule', 'Schedule widget'), $attrname_schedule_widget_id, ['class' => 'control-label']) ?>
            <?php
            echo kartik\select2\Select2::widget([
                'name' => $attrname_schedule_widget_id,
                'value' => $value_schedule_widget_id,
                'options' => ['placeholder' => Yii::t('ezmodule', 'Forms'), 'id' => 'config_schedule_widget_id'],
                'data' => ArrayHelper::map($itemWidgets, 'widget_id', 'widget_name'),
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
        </div>
        <div class="clearfix"></div>
    </div>
    
    <div class="form-group row">


        <div class="clearfix"></div>
    </div>

</div>

<div id="gantt_schedule">
    <div class="modal-header" style="margin-bottom: 15px;">
        <h4 class="modal-title" ><?= Yii::t('ezmodule', 'Config for schedule') ?></h4>
    </div>
    <div class="form-group row">
        <div class="col-md-6 ">
            <?php
            $attrname_widget_id = 'options[widget_id]';
            $value_widget_id = isset($options['widget_id']) ? $options['widget_id'] : '';
            ?>
            <?= Html::label(Yii::t('ezmodule', 'Schedule Widget'), $attrname_widget_id, ['class' => 'control-label']) ?>
            <?php
            echo kartik\select2\Select2::widget([
                'name' => $attrname_widget_id,
                'value' => $value_widget_id,
                'options' => ['placeholder' => Yii::t('ezmodule', 'Widget'), 'id' => 'config_widget_id'],
                'data' => ArrayHelper::map($itemWidgets, 'widget_id', 'widget_name'),
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
        </div>
        <div class="clearfix"></div>

    </div>
</div>
<div class="form-group row">
    <div class="col-md-6 ">
        <?php
        $attrname_widget_id = 'options[skin_name]';
        $value_widget_id = isset($options['skin_name']) ? $options['skin_name'] : '';
        ?>
        <?= Html::label(Yii::t('ezmodule', 'Gantt Chart Skin'), $attrname_widget_id, ['class' => 'control-label']) ?>
        <?php
        echo Html::dropDownList($attrname_widget_id, $value_widget_id, [
            "default" => "Default",
            "skyblue" => "Skyblue",
            "broadway" => "Broadway",
            "material" => "Material",
            "contrast" => "Contrast",
            "meadow" => "Meadow",
                ], ['class' => 'form-control'])
        ?>
    </div>
    <div class="clearfix"></div>

</div>
<!--config end-->

<?php
$this->registerJS("
    $(function(){
        var check_type = '$check_type';
        var gantt_pms = $('#gantt_pms');
        var gantt_schedule = $('#gantt_schedule');
        
        if(check_type == '1'){
            gantt_pms.css('display','');
            gantt_schedule.css('display','none');  
        }else{
            gantt_pms.css('display','none');
            gantt_schedule.css('display',''); 
        }
        
        fieldStartDate($('#config_ezf_id').val());
        fieldFinishDate($('#config_ezf_id').val());
        fieldTaskName($('#config_ezf_id').val());

        fieldProjectName($('#config_project_ezf_id').val());
        fieldCateName($('#config_cate_ezf_id').val());
    });

    $('#config_ezf_id').on('change',function(){
      var ezf_id = $(this).val();
      fieldStartDate(ezf_id);
      fieldFinishDate(ezf_id);
      fieldTaskName(ezf_id);
    });
    $('#config_project_ezf_id').on('change',function(){
      var ezf_id = $(this).val();
      fieldProjectName(ezf_id);
    });
    
$('#config_cate_ezf_id').on('change',function(){
      var ezf_id = $(this).val();
      fieldCateName(ezf_id);
    });
    
function fieldTaskName(ezf_id){
        var value = " . json_encode($task_name) . ";
        $.post('" . Url::to(['/ezforms2/target/get-fields']) . "',{ ezf_id: ezf_id, multiple:0, name: '{$attrname_task_name}', value: value ,id:'config_task_name'}
          ).done(function(result){
             $('#task_name_box').html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    function fieldStartDate(ezf_id){
        var value = " . json_encode($start_date) . ";
        $.post('" . Url::to(['/ezforms2/target/get-fields']) . "',{ ezf_id: ezf_id, multiple:0, name: '{$attrname_start_date}', value: value ,id:'config_start_date'}
          ).done(function(result){
             $('#start_date_box').html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
    function fieldFinishDate(ezf_id){
        var value = " . json_encode($finish_date) . ";
        $.post('" . Url::to(['/ezforms2/target/get-fields']) . "',{ ezf_id: ezf_id, multiple:0, name: '{$attrname_finish_date}', value: value ,id:'config_finish_date'}
          ).done(function(result){
             $('#finish_date_box').html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
    
function fieldProjectName(ezf_id){
        var value = " . json_encode($project_name) . ";
        $.post('" . Url::to(['/ezforms2/target/get-fields']) . "',{ ezf_id: ezf_id, multiple:0, name: '{$attrname_project_name}', value: value ,id:'config_project_name_field'}
          ).done(function(result){
             $('#project_name_box').html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
function fieldCateName(ezf_id){
        var value = " . json_encode($cate_name) . ";
        $.post('" . Url::to(['/ezforms2/target/get-fields']) . "',{ ezf_id: ezf_id, multiple:0, name: '{$attrname_cate_name}', value: value ,id:'config_cate_name'}
          ).done(function(result){
             $('#cate_name_box').html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
");
?>