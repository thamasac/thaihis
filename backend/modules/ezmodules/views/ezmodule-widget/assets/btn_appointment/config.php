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
    <h4 class="modal-title"><?= Yii::t('ezmodule', 'Widget Config') ?></h4>
</div>

<div class="form-group row">
    <div class="col-md-3">
        <?= Html::label(Yii::t('ezform', 'Button Icon'), 'options[icon]', ['class' => 'control-label']) ?>
        <?=
        dominus77\iconpicker\IconPicker::widget([
            'name' => 'options[icon]',
            'value' => isset($options['icon']) ? $options['icon'] : '',
            'options' => ['class' => 'dicon-input form-control', 'id' => 'config_icon'],
            'clientOptions' => [
                'hideOnSelect' => true,
            ]
        ])
        ?>

    </div>
    <div class="col-md-3 sdbox-col">
        <?= Html::label(Yii::t('ezform', 'Button Text'), 'options[title]', ['class' => 'control-label']) ?>
        <?= Html::textInput('options[btn_text]', (isset($options['btn_text']) ? $options['btn_text'] : Yii::t('ezform', 'Title')), ['class' => 'form-control']) ?>
    </div>
    <div class="col-md-3 sdbox-col">
        <?= Html::label(Yii::t('ezform', 'Button Color'), 'options[btn_text]', ['class' => 'control-label']) ?>
        <?= Html::dropDownList('options[btn_color]', (isset($options['btn_color']) ? $options['btn_color'] : 'btn-default'), [
            'btn-primary' => 'Primary',
            'btn-info' => 'Info',
            'btn-warning' => 'Warning',
            'btn-danger' => 'Danger',
            'btn-default' => 'Default'
        ], ['class' => 'form-control']) ?>
    </div>
    <div class="col-md-3 sdbox-col">
        <?= Html::label(Yii::t('ezform', 'Button Style'), 'options[btn_style]', ['class' => 'control-label']) ?>
        <?=
        Html::dropDownList('options[btn_style]', (isset($options['btn_style']) ? $options['btn_style'] : 'btn-block'), [
            'btn-block' => 'Block',
            'btn-lg' => 'Large',
            'btn-md' => 'Medium',
            'btn-sm' => 'Small',
            'btn-xs' => 'XSmall',
        ], ['class' => 'form-control'])
        ?>
    </div>
</div>
<div class="form-group row">
    <div class="col-md-4 ">
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
    <div class="col-md-4 sdbox-col">
        <?php
        $attrname_event_name = 'options[event_name]';
        $value_event_name = isset($options['event_name']) ? $options['event_name'] : '';
        ?>
        <?= Html::label(Yii::t('ezform', 'Field event name'), $attrname_event_name, ['class' => 'control-label']) ?>
        <div id="field_event_name_box">

        </div>
    </div>
    <div class="col-md-4 sdbox-col">
        <?php
        $attrname_start_date = 'options[start_date]';
        $value_start_date = isset($options['start_date']) ? $options['start_date'] : '';
        ?>
        <?= Html::label(Yii::t('ezform', 'Field start date'), $attrname_start_date, ['class' => 'control-label']) ?>
        <div id="field_start_date_box">

        </div>
    </div>
</div>

<div class="form-group row">
    <div class="col-md-4">
        <?php
        $attrname_end_date = 'options[end_date]';
        $value_end_date = isset($options['end_date']) ? $options['end_date'] : '';
        ?>
        <?= Html::label(Yii::t('ezform', 'Field end date'), $attrname_end_date, ['class' => 'control-label']) ?>
        <div id="field_end_date_box">

        </div>
    </div>
    <div class="col-md-4 sdbox-col">
        <?php
        $attrname_allDay = 'options[allDay]';
        $value_allDay = isset($options['allDay']) ? $options['allDay'] : '0';
        ?>
        <?= Html::label(Yii::t('ezform', 'All day'), $attrname_allDay, ['class' => 'control-label']) ?>
        <?= Html::dropDownList($attrname_allDay, $value_allDay, ['0' => 'False', '1' => 'True'], ['class' => 'form-control']) ?>
    </div>
</div>

<div class="form-group row">
    <div class="col-md-6">
        <?php
        $query = (new \yii\db\Query())
            ->select('widget_id as id,widget_name as name')
            ->from('ezmodule_widget')
            ->where('widget_attribute != 1');
        if (isset($model['ezm_id']) && $model['ezm_id'] != '') {
            $query->andWhere('ezm_id = :ezm_id', [':ezm_id' => $model['ezm_id']]);
        } else if (isset($ezm_id) && $ezm_id != '') {
            $query->andWhere('ezm_id = :ezm_id', [':ezm_id' => $ezm_id]);
        }
        $queryWidget = $query->all();
        ?>
        <?php
        echo Html::label(Yii::t('ezform', 'Event widget'), 'widget_event', ['class' => 'control-label']);
        echo \kartik\widgets\Select2::widget([
            'id' => 'widget_event',
            'name' => 'options[widget_event]',
            'value' => isset($options['widget_event']) ? $options['widget_event'] : '',
            'data' => ArrayHelper::map($queryWidget, 'id', 'name'),
            'options' => ['placeholder' => 'Select a widget ...'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]); ?>
    </div>
    <div class="col-md-6"></div>

</div>

<?php
$this->registerJS("
    event_name($('#config_ezf_id').val());
    start_date($('#config_ezf_id').val());
    end_date($('#config_ezf_id').val());
    
    $('#config_ezf_id').on('change',function(){
      var ezf_id = $(this).val();
      event_name(ezf_id);
      start_date(ezf_id);
      end_date(ezf_id);
    });
    
    function event_name(ezf_id){
        var value = '" . $value_event_name . "';
        $.post('" . Url::to(['/ezforms2/target/get-fields']) . "',{ ezf_id: ezf_id, multiple:0, name: '{$attrname_event_name}', value: value ,id:'config_fields'}
          ).done(function(result){
             $('#field_event_name_box').html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
    function start_date(ezf_id){
        var value = '" . $value_start_date . "';
        $.post('" . Url::to(['/ezforms2/target/get-fields']) . "',{ ezf_id: ezf_id, multiple:0, name: '{$attrname_start_date}', value: value ,id:'config_fields_start_date'}
          ).done(function(result){
             $('#field_start_date_box').html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
    function end_date(ezf_id){
        var value = '{$value_end_date}';
        $.post('" . Url::to(['/ezforms2/target/get-fields']) . "',{ ezf_id: ezf_id, multiple:0, name: '{$attrname_end_date}', value: value ,id:'config_field_end_date'}
          ).done(function(result){
             $('#field_end_date_box').html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
");
?>
