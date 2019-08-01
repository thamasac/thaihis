<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use backend\modules\ezforms2\classes\EzActiveForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use backend\modules\subjects\classes\SubjectManagementQuery;
use backend\modules\ezforms2\classes\EzfQuery;
use yii\web\JsExpression;
use kartik\widgets\DepDrop;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//$ezf_id = isset($model['ezf_id'])?$model['ezf_id']:'';
$ezf_id = '0';

$itemsEzform = SubjectManagementQuery::getEzformAll($ezf_id);
$visitSchedule = SubjectManagementQuery::getVisitScheduleByWidget($widget_id, $group_id);

$ezform_group = EzfQuery::getEzformOne($options['group_ezf_id']);
$groupList = SubjectManagementQuery::getGroupScheduleByWidget($widget_id, $ezform_group, $options['group_field']);
$groupItems[] = ['id'=>'0','group_name'=>'All Group'];

foreach ($groupList as $key => $val) {
    if($val['id'] <> '1')
        $groupItems[] = ['id'=>$val['id'],'group_name'=>$val['group_name']];
}

$roleList = SubjectManagementQuery::getAllRoles();
$userList = EzfQuery::getIntUserAll();

$data_id = isset($data_id) ? $data_id : '';
$val_ezf_id = '';
$val_visit_name = '';
$val_visit_cal = '';
$val_field_cal = '';
$val_actual_date = '';
$val_visit_mapping = '';
$val_plan_date = '';
$val_earliest_date = '';
$val_latest_date = '';
$val_visit_ref = '';
$field_visit_ref = '';
$val_form_list = '';
$warning_users = '';
$warning_roles = '';
$visit_cal_dis = false;
$dataArray = [];
if ($data_id == '11111' || $data_id == '22222') {
    $dataArray = appxq\sdii\utils\SDUtility::string2Array($model['options']);
    $data = $dataArray[$data_id];

    if ($data_id == '11111') {
        $val_ezf_id = $data['main_ezf_id'];
        $val_visit_name = $data['form_name'];
        $val_visit_cal = '';
        $val_field_cal = '';
        $val_actual_date = $data['main_actual_date'];
        $val_visit_mapping = $data['main_visit_name'];
        $val_earliest_date = $data['main_earliest_distance'];
        $val_latest_date = $data['main_latest_distance'];
        $val_form_list = isset($data['form_list']) ? $data['form_list'] : null;
        $warning_users = isset($data['warning_users']) ? $data['warning_users'] : null;
        $warning_roles = isset($data['warning_roles']) ? $data['warning_roles'] : null;
        $visit_cal_dis = true;
    } else {
        $val_ezf_id = $data['random_ezf_id'];
        $val_visit_name = $data['form_name'];
        $val_visit_cal = '11111';
        $val_field_cal = '1';
        $val_actual_date = $data['random_actual_date'];
        $val_visit_mapping = $data['random_visit_name'];
        $val_plan_date = $data['random_plan_distance'];
        $val_earliest_date = $data['random_earliest_distance'];
        $val_latest_date = $data['random_latest_distance'];
        $val_form_list = isset($data['form_list']) ? $data['form_list'] : null;
        $warning_users = isset($data['warning_users']) ? $data['warning_users'] : null;
        $warning_roles = isset($data['warning_roles']) ? $data['warning_roles'] : null;
        $visit_cal_dis = true;
    }
}

$key = $key_index;
if ($key == '')
    $key = \appxq\sdii\utils\SDUtility::getMillisecTime();
$form = EzActiveForm::begin([
            'id' => 'form-submit',
            'action' => ['/subjects/subject-management/save-schedule',
                'ezf_id' => isset($ezf_id) ? $ezf_id : '',
                'widget' => isset($widget_id) ? $widget_id : '',
                'dataid' => isset($data_id) ? $data_id : '',
                'module_id' => isset($module_id)?$module_id:'',
            ],
            'options' => [
                'enctype' => 'multipart/form-data',
                'ezf_id' => isset($ezf_id) ? $ezf_id : '',
                'module_id' => isset($module_id)?$module_id:'',
                'widget' => isset($widget_id) ? $widget_id : '',
                'dataid' => isset($data_id) ? $data_id : '',
            ]
        ]);
?>

<div class="modal-header" style="margin-bottom: 15px;">
    <h4 class="modal-title" ><?= Yii::t('ezmodule', 'Add new visit') ?></h4>
</div>
<div class="modal-body" style="margin-bottom: 15px;">
    <!--config start-->
    <div class="form-group row">
        <div class="col-md-6">
            <?= Html::hiddenInput('widget_id', $widget_id, ['id' => 'widget_id']); ?>
            <?= Html::hiddenInput('data_id', $data_id, ['id', 'data_id']); ?>
            <?= Html::hiddenInput('group_name', $group_name, ['id' => 'group_name']); ?>
            <?= Html::label(Yii::t('ezmodule', 'Current Visit Name'), 'options[form_name]', ['class' => 'control-label']) ?>
            <?php
            echo Html::input('text', 'options[form_name]', isset($model['visit_name']) ? $model['visit_name'] : $val_visit_name, ['class' => 'form-control', 'disabled' => $action == 'view' ? true : false]);
            ?>
        </div>
        <div class="col-md-6 sdbox-col">
            <?= Html::hiddenInput('options[group_name]', $group_id) ?>
            <?= Html::label(Yii::t('ezmodule', 'For what study group?'), 'options[group_name]', ['class' => 'control-label']) ?>
            <?php
            $attrname_group_name = 'options[group_name]';
            $value_group_name = isset($model['group_name']) ? $model['group_name']:$group_id;
            echo kartik\select2\Select2::widget([
                'name' => $attrname_group_name,
                'value' => $value_group_name,
                'options' => ['placeholder' => Yii::t('ezmodule', 'Group select...'), 'id' => 'config_group_name'],
                'data' => ArrayHelper::map($groupItems, 'id', 'group_name'),
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="form-group row" style="display:none;">
        <div class="col-md-4 ">
            <?php
            $attrname_ezf_id = 'options[ezf_id]';

            $value_ezf_id = $val_ezf_id != '' ? $val_ezf_id : $model['ezf_id'];
            //\appxq\sdii\utils\VarDumper::dump($value_ezf_id);
            ?>
            <?= Html::label(Yii::t('ezmodule', 'Forms'), $attrname_ezf_id, ['class' => 'control-label']) ?>
            <?php
            echo kartik\select2\Select2::widget([
                'name' => $attrname_ezf_id,
                'value' => $value_ezf_id,
                'options' => ['placeholder' => Yii::t('ezmodule', 'Forms'), 'id' => 'config_main_ezf_id', 'disabled' => $action == 'view' ? true : false],
                'data' => ArrayHelper::map($itemsEzform, 'ezf_id', 'ezf_name'),
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
        </div>
        <div class="col-md-4 sdbox-col">
            <?php
            $attrname_actual_date = 'options[actual_date]';
            $field_actual_date = isset($model['actual_date']) ? $model['actual_date'] : $val_actual_date;
            ?>
            <?= Html::label(Yii::t('ezform', 'Field Actual Date'), $attrname_actual_date, ['class' => 'control-label']) ?>
            <div id="field_display_box">

            </div>
        </div>
        <div class="col-md-4 sdbox-col">
            <?php
            $attrname_visit_name_mapping = 'options[visit_name_mapping]';
            $visit_name_mapping = isset($model['visit_name_mapping']) ? $model['visit_name_mapping'] : $val_visit_mapping;
            ?>
            <?= Html::label(Yii::t('ezform', 'Visit Name'), $attrname_actual_date, ['class' => 'control-label']) ?>
            <div id="visit_name_mapping_box">

            </div>
        </div>
        <div class="clearfix"></div>

    </div>
    <div class="form-group  row " >
        <div class="col-md-4 ">
            <?= Html::label(Yii::t('ezmodule', 'Planed Date of the Current Visit is'), 'options[plan_date]', ['class' => 'control-label']) ?>
            <?php
            echo Html::input('number', 'options[plan_date]', isset($model['plan_date']) ? $model['plan_date'] : $val_plan_date, ['class' => 'form-control', 'step' => '1', 'placeholder' => 'Specify Duration', 'disabled' => $action == 'view' ? true : false]);
            ?>
        </div>
        <div class="col-md-4 sdbox-col">
            <?= Html::label(Yii::t('subjects', 'after'), 'options[field_cal_date]', ['class' => 'control-label']) ?>

            <?=
            Html::dropDownList('options[field_cal_date]', isset($model['field_cal_date']) ? $model['field_cal_date'] : $val_field_cal, [
                'actual_date' => 'Actual Date',
                'plan_date' => 'Plan Date',
                    ], ['class' => 'form-control', 'disabled' => $action == 'view' ? true : $visit_cal_dis])
            ?>
        </div>
        <div class="col-md-4 sdbox-col">
            <?php
            $attrname_visit_ref = 'options[visit_cal_date]';
            $value_visit_ref = isset($model['visit_cal_date']) ? $model['visit_cal_date'] : $val_visit_cal;
            echo Html::hiddenInput('init_index_visit', $value_visit_ref, ['id' => 'init_index_visit']);
            ?>
            <?= Html::label(Yii::t('subjects', 'of the visit'), $attrname_visit_ref, ['class' => 'control-label']) ?>

            <?php
            echo kartik\widgets\DepDrop::widget([
                'type' => DepDrop::TYPE_SELECT2,
                'name' => $attrname_visit_ref,
                'data' => [],
                'options' => ['id' => 'index_visit', 'placeholder' => 'Select a Visit...'],
                'select2Options' => ['pluginOptions' => ['allowClear' => true]],
                'pluginOptions' => [
                    'initialize' => true,
                    'depends' => ['config_group_name'],
                    'url' => Url::to(['/subjects/subject-management/get-visit-group']),
                    'params' => ['widget_id', 'init_index_visit']
                ]
            ]);
            ?>

        </div>

        <div class="clearfix"></div>

    </div>

    <div class="form-group  row">
        <div class="col-md-4 ">
            <?= Html::label(Yii::t('ezmodule', 'Allowable early / late: [Planned date] (-)'), 'options[earliest_date]', ['class' => 'control-label']) ?>
            <?php
            echo Html::input('number', 'options[earliest_date]', isset($model['earliest_date']) ? $model['earliest_date'] : $val_earliest_date, ['class' => 'form-control', 'step' => '1', 'min' => '-100', 'max' => '100', 'disabled' => $action == 'view' ? true : false]);
            ?>
            <code>Please enter a minus (-) in front of the number of earliest day(s) (Example: -7)</code>
        </div>
        <div class="col-md-4 sdbox-col">
            <?= Html::label(Yii::t('ezmodule', '(+)'), 'options[latest_date]', ['class' => 'control-label']) ?>
            <?php
            echo Html::input('number', 'options[latest_date]', isset($model['latest_date']) ? $model['latest_date'] : $val_latest_date, ['class' => 'form-control', 'step' => '1', 'min' => '-100', 'max' => '100', 'disabled' => $action == 'view' ? true : false]);
            ?>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="form-group  row">
        <div class="col-md-6">
            <?= Html::label(Yii::t('ezmodule', 'Warning to users?'), 'options[warning_users]', ['class' => 'control-label']) ?>
            <?php
            $attrname_warning_users = 'options[warning_users]';
            $value_warning_users = isset($model['warning_users']) ?appxq\sdii\utils\SDUtility::string2Array($model['warning_users']): appxq\sdii\utils\SDUtility::string2Array($warning_users) ;
            echo kartik\select2\Select2::widget([
                'name' => $attrname_warning_users,
                'value' => $value_warning_users,
                'options' => ['placeholder' => Yii::t('ezmodule', 'User select...'), 'id' => 'config_warning_users','multiple'=>1],
                'data' => ArrayHelper::map($userList, 'id', 'text'),
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
        </div>
        <div class="col-md-6 sdbox-col">
            <?= Html::label(Yii::t('ezmodule', 'Warning to roles?'), 'options[warning_roles]', ['class' => 'control-label']) ?>
            <?php
            $attrname_warning_roles = 'options[warning_roles]';
            $value_warning_roles = isset($model['warning_roles']) ? appxq\sdii\utils\SDUtility::string2Array($model['warning_roles']):appxq\sdii\utils\SDUtility::string2Array($warning_roles) ;
            echo kartik\select2\Select2::widget([
                'name' => $attrname_warning_roles,
                'value' => $value_warning_roles,
                'options' => ['placeholder' => Yii::t('ezmodule', 'Roles select...'), 'id' => 'config_warning_roles','multiple'=>1],
                'data' => ArrayHelper::map($roleList, 'id', 'role_name'),
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
        </div>
    </div>
    <div class="row" >
        <div class="col-md-12">
            <?php
            $attrname_form_list = 'options[form_list]';
            $value_form_list = isset($model['form_list']) ? $model['form_list'] : $val_form_list;
            ?>
            <?= Html::label(Yii::t('subjects', 'Case record form (CRFs)'), $attrname_form_list, ['class' => 'control-label']) ?>
            <?php
            \backend\modules\ezforms2\classes\EzfStarterWidget::begin();
            echo backend\modules\ezforms2\classes\BtnBuilder::btnCreateEzForm('modal-create-ezform', ['label' => '<i class="fa fa-plus"></i> Create Ezform']);
            \backend\modules\ezforms2\classes\EzfStarterWidget::end();
            ?>
            <br/>
            <?php
//            \appxq\sdii\utils\VarDumper::dump($value_form_list);
            echo kartik\select2\Select2::widget([
                'name' => 'options[form_list]',
                'value' => appxq\sdii\utils\SDUtility::string2Array($value_form_list),
                'options' => ['placeholder' => Yii::t('ezmodule', 'Select form (s) a current visit'), 'id' => 'config_form_list', 'multiple' => '1', 'disabled' => $action == 'view' ? true : false],
                'data' => ArrayHelper::map($itemsEzform, 'ezf_id', 'ezf_name'),
                'pluginOptions' => [
                    'allowClear' => true,
                    'ajax' => [
                        'url' => '/subjects/schedule-config/get-ezforms',
                        'dataType' => 'json',
                        'data' => new JsExpression('function(params) { return {q:params.term}; }')
                    ],
                    'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                    'templateResult' => new JsExpression('function(city) { return city.text; }'),
                    'templateSelection' => new JsExpression('function (city) { return city.text; }'),
                ],
            ]);
            ?>
        </div>
    </div>
    <div class="schedule-form-config">
        <?php //$this->renderAjax("_view-config",['target'=>$target,'options'=>$options]);   ?>
    </div>
</div>
<div class="modal-footer">
    <?= Html::button("Close", ['class' => 'btn btn-defualt pull-right', 'data-dismiss' => 'modal']); ?>
    <?= Html::submitButton("Submit", ['class' => 'btn btn-primary pull-right', 'style' => 'margin-right:10px;']); ?>
</div>


<!--config end-->
<?php EzActiveForm::end(); ?>
<?php
$this->registerJS("

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
                $(document).find('#modal-ezform-config').modal('hide');
                $(document).find('#modal-ezform-gantt').modal('hide');
                var url =$('#$reloadDiv').attr('data-url')+'&group_name=$group_name'+'&group_id=$group_id';
                getReloadDiv(url,'$reloadDiv');
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
<?php
$this->registerJS("
    //fieldActualDate($('#config_main_ezf_id').val());
    //fieldVisitName($('#config_main_ezf_id').val());

    $('#config_main_ezf_id').on('change',function(){
      var ezf_id = $(this).val();
      fieldActualDate(ezf_id);
      fieldVisitName(ezf_id);
    });
    
    function fieldActualDate(ezf_id){
        var value = " . json_encode($field_actual_date) . ";
        $.post('" . Url::to(['/ezforms2/target/get-fields']) . "',{ ezf_id: ezf_id, multiple:0, name: '{$attrname_actual_date}', value: value ,id:'config_field_display'}
          ).done(function(result){
             $('#field_display_box').html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
    function fieldVisitName(ezf_id){
        var value = " . json_encode($visit_name_mapping) . ";
        $.post('" . Url::to(['/ezforms2/target/get-fields']) . "',{ ezf_id: ezf_id, multiple:0, name: '{$attrname_visit_name_mapping}', value: value ,id:'config_visit_name_mapping'}
          ).done(function(result){
             $('#visit_name_mapping_box').html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    

    
");
?>