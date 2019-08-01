<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use appxq\sdii\helpers\SDNoty;
use yii\web\JsExpression;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$options = isset($model->options) ? \appxq\sdii\utils\SDUtility::string2Array($model->options) : [];

$target = backend\modules\ezforms2\classes\EzfQuery::getTargetOne($ezf_id);
$itemsEzform = backend\modules\thaihis\classes\ThaiHisQuery::getEzformRef($ezf_id);

$tabs = isset($options['tabs']) ? $options['tabs'] : [];
$ezm_id = isset($model['ezm_id']) ? $model['ezm_id'] : $ezm_id;

//appxq\sdii\utils\VarDumper::dump($options,1,0);
?>

<div class="modal-header" style="margin-bottom: 15px;">
    <h4 class="modal-title" ><?= Yii::t('ezmodule', 'Widget Config') ?></h4>
</div>

<div class="clearfix"></div>
<div class="col-md-12 " id="enabled_tab_content" >
    <div class="form-group row">
        <div class="col-md-12">
            <?= Html::button("<i class='fa fa-plus'></i> Add tab", ['class' => 'btn btn-success pull-right', 'id' => 'btn_add_tab']) ?>
        </div>
    </div>

    <div class="clearfix"></div>
    <!--tab start-->
    <div class="form-group row">
        <div class="panel panel-primary">
            <div class="panel-heading" style="padding-bottom:0">
                <div class="container-fluid">
                    <div class="col-md-8">
                        <ul class="nav nav-tabs" id="tablist_config" role="tablist" >
                            <?php
                            $firstKey = null;
                            if (isset($options['tabs']) && is_array($options['tabs'])):

                                foreach ($options['tabs'] as $key => $val):
                                    if (!isset($val['tab_title']))
                                        $val['tab_title'] = 'Unkhow name';
                                    if (!$firstKey) {
                                        $firstKey = $key;
                                        $title = '<li class="nav-item"><a href="#tab' . $key . '" class=" tab-primary tab-active"  id="btn-tab' . $key . '" key_index="' . $key . '" >' . $val['tab_title'] . '</a></li>';
                                    } else {
                                        $title = '<li class="nav-item"><a href="#tab' . $key . '" class="tab-primary" id="btn-tab' . $key . '"  key_index="' . $key . '">' . $val['tab_title'] . '</a></li>';
                                    }
                                    echo $title;
                                    ?><?php
                                endforeach;
                            endif;
                            ?>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <?= Html::button(Yii::t('ezform', 'Remove this tab'), ['class' => 'btn btn-danger pull-right', 'id' => 'btn_remove_tab']) ?>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <div id="manage_tab_widget" >
                    <?php
                    if (isset($tabs) && is_array($tabs)):
                        $firstIndex = null;
                        foreach ($tabs as $key => $val):
                            $key_index = $key;
                            if (!$firstIndex)
                                $firstIndex = $key;

                            echo $this->renderAjax('_tab', [
                                'key_index' => $key_index,
                                'firstIndex' => $firstIndex,
                                'ezm_id' => $ezm_id,
                                'ezf_id' => $ezf_id,
                                'tabs' => $tabs,
                                'act' => 'onLoad'
                            ]);
                        endforeach;
                    else:
                        $key_index = \appxq\sdii\utils\SDUtility::getMillisecTime();
                        echo $this->renderAjax('_tab', [
                            'key_index' => $key_index,
                            'ezm_id' => $ezm_id,
                            'ezf_id' => $ezf_id,
                            'act' => 'addNew'
                        ]);

                    endif;
                    ?>

                </div>
            </div>
        </div>
    </div>
    <!--tab end-->
</div>
<div class="form-group row">
    <div class="col-md-6" >
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

<!--warning_enabled start-->
<div class="form-group row">
    <div class="col-md-6 " >
        <?= yii\bootstrap\Html::hiddenInput('options[warning_enabled]', 0) ?>
        <?= \backend\modules\ezforms2\classes\EzformWidget::checkbox('options[warning_enabled]', (isset($options['warning_enabled']) ? $options['warning_enabled'] : 0), ['label' => 'Warning enabled', 'id' => 'warning_enabled_check']) ?>
    </div>
</div>
<div id="warning-config-content" style="display: none;">
    <div class="form-group row">
        <div class="col-md-6">
            <?php
            $attrname_warning_ezf_id = 'options[warning_ezf_id]';
            $value_warning_ezf_id = isset($options['warning_ezf_id']) ? $options['warning_ezf_id'] : null;
            ?>
            <?= Html::label(Yii::t('ezmodule', 'Warning Forms <code>*</code>'), $attrname_warning_ezf_id, ['class' => 'control-label']) ?>
            <?php
            echo kartik\select2\Select2::widget([
                'name' => $attrname_warning_ezf_id,
                'value' => $value_warning_ezf_id,
                'options' => ['placeholder' => Yii::t('ezmodule', 'Forms'), 'id' => 'config_warning_ezf_id'],
                'data' => ArrayHelper::map($itemsEzform, 'ezf_id', 'ezf_name'),
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
        </div>
        <div class="col-md-6 sdbox-col">
            <?php
            $attrname_field_warn_level = 'options[field_warn_level]';
            $value_field_warn_level = isset($options['field_warn_level']) ? $options['field_warn_level'] : null;
            ?>
            <?= Html::label(Yii::t('ezform', 'Field warning level'), '', ['class' => 'control-label']) ?>
            <div id="ref_field_warn_level">

            </div>
        </div>
    </div>
    <div class="form-group row">
        <div class="col-md-6 ">
            <?php
            $attrname_field_warn_text = 'options[field_warn_text]';
            $value_field_warn_text = isset($options['field_warn_text']) ? $options['field_warn_text'] : null;
            ?>
            <?= Html::label(Yii::t('ezform', 'Field warning text'), '', ['class' => 'control-label']) ?>
            <div id="ref_field_warn_text">

            </div>
        </div>
        <div class="col-md-6 sdbox-col">
            <?php
            $attrname_field_warn_check = 'options[field_warn_check]';
            $value_field_warn_check = isset($options['field_warn_check']) ? $options['field_warn_check'] : null;
            ?>
            <?= Html::label(Yii::t('ezform', 'Field warning check'), '', ['class' => 'control-label']) ?>
            <div id="ref_field_warn_check">

            </div>
        </div>
    </div>
</div>
<!--warning_enabled end-->

<div class="form-group row">
    <div class="col-md-3">
        <?= yii\bootstrap\Html::hiddenInput('options[initdata]', 0) ?>
        <?= \backend\modules\ezforms2\classes\EzformWidget::checkbox('options[initdata]', (isset($options['initdata']) ? $options['initdata'] : 0), ['label' => 'initdata']) ?>      
    </div>
    <div class="col-md-3">
        <?= yii\bootstrap\Html::hiddenInput('options[action_visit]', 0) ?>
        <?= \backend\modules\ezforms2\classes\EzformWidget::checkbox('options[action_visit]', (isset($options['action_visit']) ? $options['action_visit'] : 0), ['label' => 'Action Visit']) ?>      
    </div>
    <div class="col-md-3">
        <?= yii\bootstrap\Html::hiddenInput('options[graphdisplay]', 0) ?>
        <?= \backend\modules\ezforms2\classes\EzformWidget::checkbox('options[graphdisplay]', (isset($options['graphdisplay']) ? $options['graphdisplay'] : 0), ['label' => 'Graph Display']) ?>
    </div>
    <div class="col-md-3">
        <?= yii\bootstrap\Html::hiddenInput('options[disabled_box]', 0) ?>
        <?= \backend\modules\ezforms2\classes\EzformWidget::checkbox('options[disabled_box]', (isset($options['disabled_box']) ? $options['disabled_box'] : 0), ['label' => 'Disabled BtnAddOrder']) ?>
    </div>
</div>
<div class="form-group row">
    <div class="col-md-3">
        <?php
        $doctor_val = (isset($options['doctor_can']) ? $options['doctor_can'] : 0);
        ?>
        <?= yii\bootstrap\Html::hiddenInput('options[doctor_can]', 0) ?>
        <?= \backend\modules\ezforms2\classes\EzformWidget::checkbox('options[doctor_can]', $doctor_val, ['label' => 'Can doctor manage only', 'id' => 'check_doctor_can']) ?>      
    </div>
    <div class="col-md-3">
        <?= Html::label(Yii::t('thaihis', 'Header Text')) ?>
        <?= Html::textInput('options[header_text]', (isset($options['header_text']) ? $options['header_text'] : ''), ['class' => 'form-control']) ?>
    </div>
    <div class="col-md-6">
        <?= Html::label(Yii::t('thaihis', 'Style in tag a'), 'options[style_content]', ['class' => 'control-label']) ?>
        <?= Html::textInput('options[style_content]', isset($options['style_content']) ? $options['style_content'] : '', ['class' => 'form-control']) ?>
    </div>
</div>
<div class="form-group row">
    <div class="col-md-3">
        <?php
        $condis_val = (isset($options['condition_display']) ? $options['condition_display'] : 0);
        ?>
        <?= yii\bootstrap\Html::hiddenInput('options[condition_display]', 0) ?>
        <?= \backend\modules\ezforms2\classes\EzformWidget::checkbox('options[condition_display]', $condis_val, ['label' => 'Condition to display', 'id' => 'check_condition_display']) ?>      
    </div>
    <div class="col-md-9 sdbox-col " id="condition_display_content" style="display: <?= ($condis_val == '1' ? 'block' : 'none') ?>;">
        <div class="col-md-4 sdbox-col">
            <?= Html::label(Yii::t('thaihis', 'Parameter name')) ?>
            <?= Html::textInput('options[param_name]', (isset($options['param_name']) ? $options['param_name'] : ''), ['class' => 'form-control']) ?>
        </div>
        <div class="col-md-4 sdbox-col">
            <?php
            $items = ['1' => 'เท่ากับ', '2' => 'มากกว่า', '3' => 'น้อยกว่า', '4' => 'ไม่เท่ากับ'];
            ?>
            <?= Html::label(Yii::t('thaihis', 'Condition')) ?>
            <?= Html::dropDownList('options[condition]', (isset($options['condition']) ? $options['condition'] : ''), $items, ['class' => 'form-control']) ?>
        </div>
        <div class="col-md-4 sdbox-col">
            <?= Html::label(Yii::t('thaihis', 'Value')) ?>
            <?= Html::textInput('options[value]', (isset($options['value']) ? $options['value'] : ''), ['class' => 'form-control']) ?>
        </div>
    </div>
</div>
<div class="form-group row">
    <div class="col-md-3">
        <?php
        $dept_show = (isset($options['dept_show']) ? $options['dept_show'] : 0);
        $css_show = isset($dept_show) && $dept_show == '1' ? 'display:block;' : 'display:none;';
        ?>
        <?= yii\bootstrap\Html::hiddenInput('options[dept_show]', 0) ?>
        <?= \backend\modules\ezforms2\classes\EzformWidget::checkbox('options[dept_show]', $dept_show, ['label' => 'Department same only ', 'id' => 'dept_same_show']) ?>      
    </div>
    <div class="col-md-6" id="department_show_selector" style="<?= $css_show ?>">
        <?php
        $dept_list_show = (isset($options['dept_list_show']) ? $options['dept_list_show'] : 0);
        $deptjoin_show = is_array($dept_list_show) ? join($dept_list_show, ',') : '';
        $deptData_show = backend\modules\thaihis\classes\ThaiHisQuery::getTableData("zdata_working_unit", "id IN($deptjoin_show)");
        ?>
        <?php
        echo kartik\select2\Select2::widget([
            'name' => 'options[dept_list_show]',
            'value' => $dept_list_show,
            'options' => ['placeholder' => Yii::t('ezmodule', 'Select form (s) a department'), 'id' => 'config_form_list_show' , 'multiple' => '1'],
            'data' => ($deptData_show) ? ArrayHelper::map($deptData_show, 'id', 'unit_name') : [],
            'pluginOptions' => [
                'allowClear' => true,
                'ajax' => [
                    'url' => '/thaihis/patient-visit/search-dept?sht=',
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

    <!--end tab config--> 
</div>
<?php
$this->registerJS("
    $(function(){
        var div = $('#manage_tab_widget');
        var tabs = " . json_encode($tabs) . ";
        var url = '" . Url::to(['/thaihis/configs/add-newtab', 'ezf_id' => $ezf_id, 'ezm_id' => $ezm_id]) . "';
        //div.html('<div class=\"sdloader\"><i class=\"sdloader-icon\"></i></div>');
        var delay = 100;
        var firstIndex = null;
        
        var content = $('#warning-config-content');
        if($('#warning_enabled_check').is(':checked')){
            content.css('display','block');
        }else{
            content.css('display','none');
        }
        fieldWarnLevel($('#config_warning_ezf_id').val());
        fieldWarnText($('#config_warning_ezf_id').val());
        fieldWarnCheck($('#config_warning_ezf_id').val());
    });
    

    
    $('#check_condition_display').on('change',function(){
        var content = $('#condition_display_content');
        if($(this).is(':checked')){
            content.css('display','block');
        }else{
            content.css('display','none');
        }
    });
    
    $('#warning_enabled_check').on('change',function(){
        var content = $('#warning-config-content');
        if($(this).is(':checked')){
            content.css('display','block');
        }else{
            content.css('display','none');
        }
    });

    $('#config_warning_ezf_id').on('change',function(){
        fieldWarnLevel($(this).val());
        fieldWarnText($(this).val());
        fieldWarnCheck($(this).val());
    });
    
    function fieldWarnLevel(ezf_id){
        var value = '" . $value_field_warn_level . "';
        var name = '" . $attrname_field_warn_level . "';
        
        $.post('" . Url::to(['/ezforms2/target/get-fields']) . "',{ ezf_id: ezf_id, multiple:0, name: name, value: value ,id:'config_field_warn_level'}
          ).done(function(result){
             $('#ref_field_warn_level').html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
    function fieldWarnText(ezf_id){
        var value = '" . $value_field_warn_text . "';
        var name = '" . $attrname_field_warn_text . "';
        
        $.post('" . Url::to(['/ezforms2/target/get-fields']) . "',{ ezf_id: ezf_id, multiple:0, name: name, value: value ,id:'config_field_warn_text'}
          ).done(function(result){
             $('#ref_field_warn_text').html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
    function fieldWarnCheck(ezf_id){
        var value = '" . $value_field_warn_check . "';
        var name = '" . $attrname_field_warn_check . "';
        
        $.post('" . Url::to(['/ezforms2/target/get-fields']) . "',{ ezf_id: ezf_id, multiple:0, name: name, value: value ,id:'config_field_warn_check'}
          ).done(function(result){
             $('#ref_field_warn_check').html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
    $('#btn_add_tab').click(function(){
        var key_index = getMilisecTime();
        var firstIndex = key_index;
        $('#tablist_config').find('.nav-item').each(function(i,e){
            var index = $(e).children().attr('key_index');
            $('#content_config_tab'+index).css('display','none');
            $(e).children().removeClass('tab-active');
        });
        
        $('#tablist_config').append('<li class=\"nav-item\"><a href=\"#tab'+key_index+'\" id=\"btn-tab'+key_index+'\"  class =\"tab-primary tab-active\" key_index=\"'+key_index+'\">New Tab</a></li>');
        var div = $('#manage_tab_widget');
        var url = '" . Url::to(['/thaihis/configs/add-newtab', 'ezf_id' => $ezf_id, 'ezm_id' => $ezm_id, 'act' => 'addNew']) . "';
        var ezf_id = '$ezf_id';
            
        $.post(url,{key_index:key_index,act:'addNew',ezm_id:'$ezm_id',ezf_id:'$ezf_id'},function(result){
            div.append(result);
        });
        
    });
        
    $(document).on('click','[id^=btn-tab]',function(){
        $(this).parent().parent().find('.nav-item').each(function(i,e){
            $('#content_config_tab'+$(e).children().attr('key_index')).css('display','none');
            $(e).children().removeClass('tab-active');
        });
        
        $('#content_config_tab'+$(this).attr('key_index')).css('display','block');
        $(this).addClass('tab-active');
    });
    
    $('#btn_remove_tab').click(function(){
        $('#tablist_config').find('.nav-item').each(function(i,e){
            if($(e).find('a').hasClass('tab-active')){
                var key_index = $(e).children().attr('key_index');
                $(e).remove();
                $('#content_config_tab'+key_index).remove();
            }
        });
    });
    
 function getMilisecTime(){
    var d = new Date();
    var key_index = d.getFullYear() +''+ d.getMonth() +''+ d.getDate() +''+ d.getHours() +''+ d.getMinutes() +''+ d.getSeconds() +''+ d.getMilliseconds();
    return key_index;
 } 
 
 $('#dept_same_show').on('change',function(){
        if($(this).is(':checked')){ 
            $('#department_show_selector').css('display','block');
        }else{
            $('#department_show_selector').css('display','none');
        }
    });
    
");
?>