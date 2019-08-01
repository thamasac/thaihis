<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;

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


$ezm_id = $model['ezm_id'];
?>

<div class="modal-header" style="margin-bottom: 15px;">
    <h4 class="modal-title" ><?= Yii::t('ezmodule', 'Widget Config') ?></h4>
</div>

<div class="form-group row">
    <div class="col-md-6">
        <?= Html::label(Yii::t('ezform', 'Title'), 'options[title]', ['class' => 'control-label']) ?>
        <?= Html::textInput('options[title]', (isset($options['title']) ? $options['title'] : Yii::t('ezform', 'Title')), ['class' => 'form-control']) ?>
    </div>
    <div class="col-md-6 sdbox-col" >
        <?= Html::label(Yii::t('ezform', 'Column'), 'options[column]', ['class' => 'control-label']) ?>
        <?= Html::textInput('options[column]', (isset($options['column']) ? $options['column'] : '2'), ['class' => 'form-control', 'type' => 'number']) ?>
    </div>
</div>

<div class="form-group row">
    <div class="col-md-6 ">
        <?php
        $attrname_ezf_id = 'options[ezf_id]';
        $value_ezf_id = isset($options['ezf_id']) ? $options['ezf_id'] : '';
        ?>
        <?= Html::label(Yii::t('ezform', 'Form'), $attrname_ezf_id, ['class' => 'control-label']) ?>
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
    <div class="col-md-6 sdbox-col">
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
    <div class="col-md-6">
        <?php
        $attrname_image_field = 'options[image_field]';
        $value_image_field = isset($options['image_field']) ? $options['image_field'] : '';
        ?>
        <?= Html::label(Yii::t('ezform', 'Image Field'), $attrname_image_field, ['class' => 'control-label']) ?>
        <div id="pic_field_box">

        </div>
    </div>
    <div class="col-md-6 sdbox-col" >
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
<!--<div class="form-group row">
  <div class="col-md-6 " >
<?= Html::radioList('options[enabled_tab]', isset($options['enabled_tab']) ? $options['enabled_tab'] : '0', ['0' => 'Disabled Tab', '1' => 'Enabled Tab'], ['id' => 'enabled_tab']) ?>
  </div>
</div>-->
<div class="clearfix"></div>
<div class="col-md-12 " id="enabled_tab_content" >
    <div class="form-group row">
        <div class="col-md-12">
            <?= Html::button("<i class='fa fa-plus'></i> Add tab", ['class' => 'btn btn-success pull-right', 'id' => 'btn_add_tab']) ?>
        </div>
    </div>

    <div class="clearfix"></div>
    <div class="form-group row">
        <div class="panel panel-primary">
            <div class="panel-heading" style="padding-bottom:0">
                <div class="container-fluid">
                    <div class="col-md-8">
                        <ul class="nav nav-tabs" id="tablist_config" role="tablist" >
                            <?php
                            if (isset($options['tabs']) && is_array($options['tabs'])):
                                foreach ($options['tabs'] as $key => $val):
                                    if ($key == '0')
                                        $title = '<li class="nav-item"><a href="#tab' . $key . '" class=" tab-primary tab-active"  id="btn-tab' . $key . '" key_index="' . $key . '" >' . $val['tab_title'] . '</a></li>';
                                    else
                                        $title = '<li class="nav-item"><a href="#tab' . $key . '" class="tab-primary" id="btn-tab' . $key . '"  key_index="' . $key . '">' . $val['tab_title'] . '</a></li>';

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
                <div class="container-fluid" id="manage_tab_widget" >

                </div>
            </div>
        </div>
    </div>
</div>
<div class="form-group row">
    <div class="col-md-4">
        <?= yii\bootstrap\Html::hiddenInput('options[graphdisplay]', 0) ?>
        <?= \backend\modules\ezforms2\classes\EzformWidget::checkbox('options[graphdisplay]', (isset($options['graphdisplay']) ? $options['graphdisplay'] : 0), ['label' => 'Graph Display']) ?>
    </div>
    <div class="col-md-4">
        <?= yii\bootstrap\Html::hiddenInput('options[initdata]', 0) ?>
        <?= \backend\modules\ezforms2\classes\EzformWidget::checkbox('options[initdata]', (isset($options['initdata']) ? $options['initdata'] : 0), ['label' => 'initdata']) ?>
    </div>
    <div class="col-md-4">
        <?= yii\bootstrap\Html::hiddenInput('options[disabled_box]', 0) ?>
        <?= \backend\modules\ezforms2\classes\EzformWidget::checkbox('options[disabled_box]', (isset($options['disabled_box']) ? $options['disabled_box'] : 0), ['label' => 'Disabled Box']) ?>
    </div>
    <div class="col-md-4">
        <?= yii\bootstrap\Html::hiddenInput('options[show_label]', 0) ?>
        <?= \backend\modules\ezforms2\classes\EzformWidget::checkbox('options[show_label]', (isset($options['show_label']) ? $options['show_label'] : 0), ['label' => 'Show Label']) ?>
    </div>
    <div class="col-md-4">
        <?= yii\bootstrap\Html::hiddenInput('options[require_data]', 0) ?>
        <?= \backend\modules\ezforms2\classes\EzformWidget::checkbox('options[require_data]', (isset($options['require_data']) ? $options['require_data'] : 0), ['label' => 'Require data column']) ?>

    </div>
    <div class="col-md-4">
        <?php
        $doctor_val = (isset($options['doctor_can']) ? $options['doctor_can'] : 0);
        ?>
        <?= yii\bootstrap\Html::hiddenInput('options[doctor_can]', 0) ?>
        <?= \backend\modules\ezforms2\classes\EzformWidget::checkbox('options[doctor_can]', $doctor_val, ['label' => 'Can doctor manage only', 'id' => 'check_doctor_can']) ?>      
    </div>
    <div class="col-md-3">
        <?php
        $dept_display = (isset($options['dept_display']) ? $options['dept_display'] : 0);
        $css_display = isset($dept_display) && $dept_display == '1' ? 'display:block;' : 'display:none;';
        ?>
        <?= yii\bootstrap\Html::hiddenInput('options[dept_display]', 0) ?>
        <?= \backend\modules\ezforms2\classes\EzformWidget::checkbox('options[dept_display]', $dept_display, ['label' => 'Department same only ', 'id' => 'dept_same_display']) ?>      
    </div>
    <div class="col-md-6" id="department_selector" style="<?= $css_display ?>">
        <?php
        $dept_list = (isset($options['dept_list']) ? $options['dept_list'] : 0);
        $deptjoin = is_array($dept_list) ? join($dept_list, ',') : '';
        $deptData = backend\modules\thaihis\classes\ThaiHisQuery::getTableData("zdata_working_unit", "id IN($deptjoin)");
        ?>
        <?php
//            \appxq\sdii\utils\VarDumper::dump($value_form_list);
        echo kartik\select2\Select2::widget([
            'name' => 'options[dept_list]',
            'value' => $dept_list,
            'options' => ['placeholder' => Yii::t('ezmodule', 'Select form (s) a department'), 'id' => 'config_form_list', 'multiple' => '1'],
            'data' => ($deptData)? ArrayHelper::map($deptData, 'id', 'unit_name'):[],
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
</div>
<div class="clearfix"></div>
<div class="form-group row">
    <div class="col-md-4">
        <?php
        $editdat_val = (isset($options['edit_data_own']) ? $options['edit_data_own'] : 0);
        ?>
        <?= \backend\modules\ezforms2\classes\EzformWidget::checkbox('options[edit_data_own]', $editdat_val, ['label' => 'Edit data by own only', 'id' => 'edit_data_own']) ?>    
    </div>
</div>
<div class="form-group">
    <?= Html::label(Yii::t('ezform', 'Template Content'), 'options[template_content]', ['class' => 'control-label']) ?>
    <?= Html::textarea('options[template_content]', isset($options['template_content']) ? $options['template_content'] : '', ['class' => 'form-control', 'row' => 3]) ?>
</div>

<div class="form-group">
    <?= Html::label(Yii::t('ezform', 'Template Box'), 'options[template_box]', ['class' => 'control-label']) ?>
    <?= Html::textarea('options[template_box]', isset($options['template_box']) ? $options['template_box'] : '', ['class' => 'form-control', 'row' => 3]) ?>
</div>

<?php
$this->registerJS("
    $(function(){
        var enabled_tab = null;
        var div = $('#manage_tab_widget');
        $('input[name=\'options[enabled_tab]\'').each(function(i,e){
            if($(e).is(':checked') === true){
                enabled_tab = $(e).val();
            }
        });
        var url = '" . Url::to(['/thaihis/patient-visit/add-newtab', 'ezf_id' => $ezf_id, 'ezm_id' => $ezm_id, 'tabs' => isset($options['tabs']) ? $options['tabs'] : '']) . "';
        
        if(enabled_tab === '1' ){
            $('#enabled_tab_content').show();
            div.html('<div class=\"sdloader\"><i class=\"sdloader-icon\"></i></div>');
            $.get(url,{},function(result){
                div.empty();
                div.html(result);
            });
        }else{
            $('#enabled_tab_content').hide();
        }
    });
    
    fields($('#config_ezf_id').val());
    pic_fields($('#config_ezf_id').val());
    
    $('#config_ezf_id').on('change',function(){
      var ezf_id = $(this).val();
      fields(ezf_id);
      pic_fields(ezf_id);
    });
    
    $('#dept_same_display').on('change',function(){
        if($(this).is(':checked')){ 
            $('#department_selector').css('display','block');
        }else{
            $('#department_selector').css('display','none');
        }
    });

    function fields(ezf_id){
        var value = " . $value_fields . ";
        $.post('" . Url::to(['/ezforms2/target/get-fields']) . "',{ ezf_id: ezf_id, multiple:1, name: '{$attrname_fields}', value: value ,id:'config_fields'}
          ).done(function(result){
             $('#ref_field_box').html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
    function pic_fields(ezf_id){
        var value = '{$value_image_field}';
        $.post('" . Url::to(['/ezforms2/target/get-fields']) . "',{ ezf_id: ezf_id, multiple:0, name: '{$attrname_image_field}', value: value ,id:'config_pic_fields'}
          ).done(function(result){
             $('#pic_field_box').html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
$('input[name=\'options[enabled_tab]\'').change(function(){
        var enabled_tab = $(this).val();
        
        var div = $('#manage_tab_widget');
        var url = '" . Url::to(['/thaihis/patient-visit/add-newtab', 'ezf_id' => $ezf_id, 'ezm_id' => $ezm_id, 'tabs' => isset($options['tabs']) ? $options['tabs'] : '']) . "';
        var ezf_id = '$ezf_id';
        
        if(enabled_tab === '1'){
            $('#enabled_tab_content').show();
        }else{
            $('#enabled_tab_content').hide();
        }
    });
    
    var tab_amt = '" . (isset($options['tabs']) ? count($options['tabs']) - 1 : 0) . "';
    $('#btn_add_tab').click(function(){
        $('#tablist_config').find('.nav-item').each(function(i,e){
            $('#content_config_tab'+i).css('display','none');
            $(e).find('#btn-tab'+i).removeClass('tab-active');
        });
        tab_amt ++;
        $('#tablist_config').append('<li class=\"nav-item\"><a href=\"#tab'+tab_amt+'\" id=\"btn-tab'+tab_amt+'\"  class =\"tab-primary tab-active\" key_index=\"'+tab_amt+'\">New Tab</a></li>');
        var div = $('#manage_tab_widget');
        var url = '" . Url::to(['/thaihis/patient-visit/add-new-boxtab', 'ezf_id' => $ezf_id, 'key_index' => isset($options['tabs']) ? count($options['tabs']) : 0]) . "';
        var ezf_id = '$ezf_id';
            
        $.get(url,{key_index:tab_amt},function(result){
            div.append(result);
        });
        
    });
        
    $(document).on('click','[id^=btn-tab]',function(){
        $(this).parent().parent().find('.nav-item').each(function(i,e){
            $('#content_config_tab'+i).css('display','none');
            $(e).find('#btn-tab'+i).removeClass('tab-active');
        });
        
        $('#content_config_tab'+$(this).attr('key_index')).css('display','block');
        $(this).addClass('tab-active');
    });
    
    $('#btn_remove_tab').click(function(){
        $('#tablist_config').find('.nav-item').each(function(i,e){
            if($(e).find('a').hasClass('tab-active')){
                $(e).remove();
                $('#content_config_tab'+i).remove();
            }
        });
    });
");
?>