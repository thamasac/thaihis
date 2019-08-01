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
    <h4 class="modal-title" ><?= Yii::t('ezmodule', 'Widget Config') ?></h4>
</div>

<div class="form-group row">
    <div class="col-md-12" style="margin-top: 5px;margin-bottom: 10px;">
        <h4><?= Yii::t('ezmodule', 'Config Tab') ?></h4>
    </div>
    <div class="col-md-4 ">
        <?php
        $attrname_ezf_id = 'options[ezf_id]';
        $value_ezf_id = isset($options['ezf_id']) ? $options['ezf_id'] : '';
        ?>
        <?= Html::label(Yii::t('ezmodule', 'Forms'), $attrname_ezf_id, ['class' => 'control-label']) ?>
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
    <div class="col-md-3 sdbox-col">
        <?php
        $attrname_field_label = 'options[field_label]';
        $field_label = isset($options['field_label']) ? $options['field_label'] : '';
        ?>
        <?= Html::label(Yii::t('ezform', 'Field Label'), $attrname_field_label, ['class' => 'control-label']) ?>
        <div id="field_label_box">

        </div>
    </div>
    <div class="col-md-3 sdbox-col">
        <?php
        $attrname_field_value = 'options[field_value]';
        $field_value = isset($options['field_value']) ? $options['field_value'] : '';
        ?>
        <?= Html::label(Yii::t('ezform', 'Field Value'), $attrname_field_value, ['class' => 'control-label']) ?>
        <div id="field_value_box">

        </div>
    </div>
    <div class="clearfix"></div>
    <hr/>
    <div class="col-md-12" style="margin-top: 5px;margin-bottom: 10px;">
        <h4><?= Yii::t('ezmodule', 'Config Table') ?></h4>
    </div>
    <div class="col-md-5 ">

        <?php
        $attrname_ezf_id_name = 'options[ezf_id_name]';
        $value_ezf_id_name = isset($options['ezf_id_name']) ? $options['ezf_id_name'] : '';
        ?>
        <?= Html::label(Yii::t('ezmodule', 'Grid Document Name'), $attrname_ezf_id_name, ['class' => 'control-label']) ?>
        <?php
        echo kartik\select2\Select2::widget([
            'name' => $attrname_ezf_id_name,
            'value' => $value_ezf_id_name,
            'options' => ['placeholder' => Yii::t('ezmodule', 'Forms'), 'id' => 'config_ezf_id_name'],
            'data' => ArrayHelper::map($itemsEzform, 'ezf_id', 'ezf_name'),
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
    <div class="col-md-5 ">

        <?php
        $attrname_ezf_id_detail = 'options[ezf_id_detail]';
        $value_ezf_id_detail = isset($options['ezf_id_detail']) ? $options['ezf_id_detail'] : '';
        ?>
        <?= Html::label(Yii::t('ezmodule', 'Grid Document Detail'), $attrname_ezf_id_detail, ['class' => 'control-label']) ?>
        <?php
        echo kartik\select2\Select2::widget([
            'name' => $attrname_ezf_id_detail,
            'value' => $value_ezf_id_detail,
            'options' => ['placeholder' => Yii::t('ezmodule', 'Forms'), 'id' => 'config_ezf_id_detail'],
            'data' => ArrayHelper::map($itemsEzform, 'ezf_id', 'ezf_name'),
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
    <div class="col-md-2">
        <?php
        $attrname_page_size = 'options[page_size]';
        $value_page_size = isset($options['page_size']) ? $options['page_size'] : '20';
        echo Html::label(Yii::t('ezmodule', 'Page Size'), $attrname_page_size, ['class' => 'control-label']);
        echo Html::textInput($attrname_page_size, $value_page_size, [
            'id' => 'config_page_size',
            'type' => 'number',
            'class' => 'form-control'
        ]);
        ?>
    </div>

    <div class="col-md-6 ">

        <?php
        $attrname_field_taget = 'options[field_taget]';
        $field_taget = isset($options['field_taget']) ? $options['field_taget'] : [];
        ?>
        <?= Html::label(Yii::t('ezform', 'Ref form'), $attrname_field_taget, ['class' => 'control-label']) ?>
        <div id="field_taget">

        </div>
    </div>

    <div class="col-md-6 ">

        <?php
        $attrname_field_column = 'options[field_column]';
        $field_column = isset($options['field_column']) ? $options['field_column'] : [];
        ?>
        <?= Html::label(Yii::t('ezform', 'Display fields'), $attrname_field_column, ['class' => 'control-label']) ?>
        <div id="field_column">

        </div>
    </div>

</div>



<?php
$this->registerJS("
    fieldLabel($('#config_ezf_id').val());
    fieldValue($('#config_ezf_id').val());    

    $('#config_ezf_id').on('change',function(){
      var ezf_id = $(this).val();
      fieldLabel(ezf_id);
      fieldValue(ezf_id);
    });
    
    function fieldLabel(ezf_id){
        var value = '" . $field_label . "';
        $.post('" . Url::to(['/ezforms2/target/get-fields']) . "',{ ezf_id: ezf_id, multiple:0, name: '{$attrname_field_label}', value: value ,id:'config_field_label'}
          ).done(function(result){
             $('#field_label_box').html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
    function fieldValue(ezf_id){
        var value = '" . $field_value . "';
        $.post('" . Url::to(['/ezforms2/target/get-fields']) . "',{ ezf_id: ezf_id, multiple:0, name: '{$attrname_field_value}', value: value ,id:'config_field_value'}
          ).done(function(result){
             $('#field_value_box').html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    fieldColumn($('#config_ezf_id_detail').val());    

    $('#config_ezf_id_detail').on('change',function(){
      var ezf_id = $(this).val();
      fieldColumn(ezf_id);
    });
    
    function fieldColumn(ezf_id){
        var value = " . json_encode($field_column) . ";
        $.post('" . Url::to(['/ezforms2/target/get-fields']) . "',{ ezf_id: ezf_id, multiple:1, name: '{$attrname_field_column}', value: value ,id:'config_column'}
          ).done(function(result){
             $('#field_column').html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
    fieldTaget($('#config_ezf_id_detail').val());    

    $('#config_ezf_id_detail').on('change',function(){
      var ezf_id = $(this).val();
      fieldTaget(ezf_id);
    });
    
    function fieldTaget(ezf_id){
        var value = " . json_encode($field_taget) . ";
        $.post('" . Url::to(['/ezforms2/target/get-fields']) . "',{ ezf_id: ezf_id, multiple:0, name: '{$attrname_field_taget}', value: value ,id:'config_taget'}
          ).done(function(result){
             $('#field_taget').html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    

    
    
");
?>
