<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use backend\modules\ezforms2\classes\EzActiveForm;
use backend\modules\ezforms2\classes\EzfFunc;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;

//appxq\sdii\utils\VarDumper::dump($reloadDiv);
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$ezf_id = $model['ezf_id'];
if ($ezf_id == '') {
    $ezf_id = '0';
}

//$options = isset($model->options) ? \appxq\sdii\utils\SDUtility::string2Array($model->options) : [];
$itemsEzform = \backend\modules\ezforms2\classes\EzfQuery::getEzformAll('0');
$itemProcedure = \backend\modules\subjects\classes\SubjectManagementQuery::getVisitProcedureByWidget($schedule_id);

$data_id = isset($data_id) ? $data_id : '';
if ($data_id == '11111' || $data_id == '22222') {
    $dataArray = appxq\sdii\utils\SDUtility::string2Array($model['options']);
    $data = $dataArray[$data_id];
    //appxq\sdii\utils\VarDumper::dump($model);
    if ($data_id == '11111') {
        $val_ezf_id = $data['main_ezf_id'];
        $val_visit_name = $data['form_name'];
        $val_actual_date = $data['main_actual_date'];
        $val_plan_date = $data['main_plan_distance'];
        $val_earliest_date = $data['main_earliest_distance'];
        $val_latest_date = $data['main_latest_distance'];
    } else {
        $val_ezf_id = $data['random_ezf_id'];
        $val_visit_name = $data['form_name'];
        $val_actual_date = $data['random_actual_date'];
        $val_plan_date = $data['random_plan_distance'];
        $val_earliest_date = $data['random_earliest_distance'];
        $val_latest_date = $data['random_latest_distance'];
    }
} else {
    $parent_id = "";
    if ($require == "New_task") {
        $parent_id = $model['id'];
        $model = null;
    } else {
        $parent_id = $model['visit_parent'];
    }
}


$form = EzActiveForm::begin([
            'id' => 'form-submit',
            'action' => ['/gantt/gantt/save-gantt2',
                'ezf_id' => isset($ezf_id) ? $ezf_id : '',
                'widget' => isset($widget_id) ? $widget_id : '',
                'dataid' => isset($dataid) ? $dataid : '',
            ],
            'options' => [
                'enctype' => 'multipart/form-data',
                'ezf_id' => isset($ezf_id) ? $ezf_id : '',
                'widget' => isset($widget_id) ? $widget_id : '',
                'dataid' => isset($dataid) ? $dataid : '',
            ]
        ]);
?>
<?= Html::hiddenInput('widget_id', $widget_id) ?>
<?= Html::hiddenInput('schedule_id', $schedule_id) ?>
<?= Html::hiddenInput('data_id', isset($model['id'])?$model['id']:$data_id) ?>
<div class="modal-header" style="margin-bottom: 15px;">
    <h4 class="modal-title" ><?= Yii::t('ezmodule', 'Visit Config') ?></h4>
</div>
<div class="modal-body">
    <div class="form-group row">
        <div class="col-md-6 ">
            <?= Html::label(Yii::t('ezmodule', 'Visit Name'), 'options[visit_name]', ['class' => 'control-label']) ?>
            <?php
            echo Html::textInput('options[visit_name]', isset($model['visit_name']) ? $model['visit_name'] : $val_visit_name, ['class' => 'form-control']);
            ?>
        </div>

    </div>
    <div class="clearfix"></div>
    <div class="reff-ezform">
        <div class="form-group row">
            <div class="col-md-6 ">
                <?php
                $attrname_ezf_id = 'options[ezf_id]';
                $value_ezf_id = isset($val_ezf_id) ? $val_ezf_id : $model['ezf_id'];
                ?>
                <?= Html::label(Yii::t('ezmodule', 'Forms'), $attrname_ezf_id, ['class' => 'control-label']) ?>
                <?php
                //\appxq\sdii\utils\VarDumper::dump(ArrayHelper::map($itemsEzform, 'ezf_id', 'ezf_name'));
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
            <div class="col-md-6 sdbox-col">
                <?php
                $attrname_actual_date = 'options[actual_date]';
                $field_actual_date = isset($model['actual_date']) ? $model['actual_date'] : $val_actual_date;
                ?>
                <?= Html::label(Yii::t('ezform', 'Field Actual Date'), $attrname_actual_date, ['class' => 'control-label']) ?>
                <div id="field_display_box">

                </div>
            </div>
            <div class="clearfix"></div>

        </div>
        <div class="form-group row">
            <div class="col-md-4 ">
                <?= Html::label(Yii::t('ezmodule', 'Plan Date'), 'options[plan_date]', ['class' => 'control-label']) ?>
                <?php
                echo Html::input('number', 'options[plan_date]', isset($model['plan_date']) ? $model['plan_date'] : $val_plan_date, ['class' => 'form-control']);
                ?>
            </div>
            <div class="col-md-4 sdbox-col">
                <?= Html::label(Yii::t('ezmodule', 'Earliest Date'), 'options[earliest_date]', ['class' => 'control-label']) ?>
                <?php
                echo Html::input('number', 'options[earliest_date]', isset($model['earliest_date']) ? $model['earliest_date'] : $val_earliest_date, ['class' => 'form-control']);
                ?>
            </div>
            <div class="col-md-4 sdbox-col">
                <?= Html::label(Yii::t('ezmodule', 'Latest Date'), 'options[latest_date]', ['class' => 'control-label']) ?>
                <?php
                echo Html::input('number', 'options[latest_date]', isset($model['latest_date']) ? $model['latest_date'] : $val_latest_date, ['class' => 'form-control']);
                ?>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="form-group row">

            <div class="col-md-6">
                <?= Html::label(Yii::t('ezform', 'Parent'), 'options[visit_parent]', ['class' => 'control-label']) ?>
                <?php
                echo kartik\select2\Select2::widget([
                    'name' => 'options[visit_parent]',
                    'value' => $parent_id,
                    'options' => ['placeholder' => Yii::t('ezmodule', 'Parent'), 'id' => 'config_refparent'],
                    'data' => ArrayHelper::map($itemProcedure, 'id', 'visit_name'),
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
                ?>
            </div>
            <div class="col-md-6  sdbox-col">
                <?= Html::label(Yii::t('ezmodule', 'Sort order'), 'options[sortorder]', ['class' => 'control-label']) ?>
                <?php
                echo Html::input('number', 'options[sortorder]', '', ['class' => 'form-control']);
                ?>
            </div>
            <div class="clearfix"></div>
        </div>

    </div>


    <div class="form-group row">
        <div class="col-md-6">
            <?= Html::label(Yii::t('ezform', 'Open Node'), 'options[open_state]', ['class' => 'control-label']) ?>
            <?php
            echo Html::checkbox('options[open_state]', '1', ['value' => '1']);
            ?>
        </div>
        <div class="clearfix"></div>
    </div>
</div>
<div class="modal-footer" style="margin-bottom: 15px;">
    <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-primary']) ?>
    <?= Html::button('<i class="glyphicon glyphicon-remove"></i> ' . Yii::t('app', 'Close'), ['class' => 'btn btn-default', 'data-dismiss' => 'modal']) ?> 
    <?php isset($gantt_id) ? null : Html::button('<i class="glyphicon glyphicon-remove"></i> ' . Yii::t('app', 'Delete'), ['class' => 'btn btn-danger pull right', 'data-dismiss' => 'modal']) ?> 
</div>

<?php EzActiveForm::end(); ?>
<?php
$this->registerJS("
    
//    $(function(){
//        var box_display = $('.config-box-display');
//        box_display.html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
//        $.get('/gantt/gantt/get-refform',function(data){
//            box_display.html(data);
//        })
//    })

    fieldActualDate($('#config_main_ezf_id').val());

    $('#config_main_ezf_id').on('change',function(){
      var ezf_id = $(this).val();
      fieldActualDate(ezf_id);
    });
    
    function fieldActualDate(ezf_id){
        var value = '" . $field_actual_date . "';
        $.post('" . Url::to(['/ezforms2/target/get-fields']) . "',{ ezf_id: ezf_id, multiple:0, name: '{$attrname_actual_date}', value: value ,id:'config_field_display'}
          ).done(function(result){
             $('#field_display_box').html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
    $('.checkbox-refform').click(function(){
        var box_display = $('.config-box-display');
        box_display.html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
        if($('.checkbox-refform').is(':checked')){
            $.get('/gantt/gantt/get-refform',function(data){
                box_display.html(data);
            })
        }else{
            $.get('/gantt/gantt/get-not-refform',function(data){
                box_display.html(data);
            })
        }
    });
    
$('form#form-submit').on('beforeSubmit', function(e) {
    
    var \$form = $(this);
    var formData = new FormData($(this)[0]);

    $.ajax({
          url: \$form.attr('action'),
          type: 'POST',
          data: formData,
	  dataType: 'JSON',
	  enctype: 'multipart/form-data',
	  processData: false,  // tell jQuery not to process the data
	  contentType: false,   // tell jQuery not to set contentType
          success: function (result) {
	    if(result.status == 'success') {
                " . SDNoty::show('result.message', 'result.status') . "
                $(document).find('#modal-ezform-gantt').modal('hide');
                getReloadDiv($('#$reloadDiv').attr('data-url'), '$reloadDiv');
            } else {
                " . SDNoty::show('result.message', 'result.status') . "
                    $('#form-submit .btn-submit').attr('disabled', false);
            } 
          },
          error: function () {
            " . SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') . "
                $('#form-submit .btn-submit').attr('disabled', false);
	    console.log('server error');
          }
      });
      
    return false;
});
    
");
?>