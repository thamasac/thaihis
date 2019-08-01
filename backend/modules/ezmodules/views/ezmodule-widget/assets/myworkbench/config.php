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
    <div class="col-md-6 ">
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
    <div class="col-md-6 sdbox-col">
        <?php
        $attrname_field_display = 'options[field_display]';
        $field_display = isset($options['field_display']) ? $options['field_display'] : '';
        ?>
        <?= Html::label(Yii::t('ezform', 'Field Display'), $attrname_field_label, ['class' => 'control-label']) ?>
        <div id="field_display_box">

        </div>
    </div>
    <div class="clearfix"></div>

</div>
<div class="form-group row">
    <div class="col-md-6">
        <?php
        $attrname_field_download = 'options[field_download]';
        $field_download = isset($options['field_download']) ? $options['field_download'] : '';
        ?>
        <?= Html::label(Yii::t('ezform', 'Field Download'), $attrname_field_download, ['class' => 'control-label']) ?>
        <div id="field_download_box">

        </div>
    </div>
    <div class="col-md-6 sdbox-col">
        <?php
        $attrname_field_status = 'options[field_status]';
        $field_status = isset($options['field_status']) ? $options['field_status'] : '';
        ?>
        <?= Html::label(Yii::t('ezform', 'Field Status'), $attrname_field_status, ['class' => 'control-label']) ?>
        <div id="field_status_box">

        </div>
    </div>
    <div class="clearfix"></div>
</div>
<hr/>
<div class="form-group row">
    <div class="col-md-6 ">
        <?php
        $attrname_ezf_name_id = 'options[ezf_name_id]';
        $value_ezf_name_id = isset($options['ezf_name_id']) ? $options['ezf_name_id'] : '';
        ?>
        <?= Html::label(Yii::t('ezmodule', 'Forms'), $attrname_ezf_match_id, ['class' => 'control-label']) ?>
        <?php
        echo kartik\select2\Select2::widget([
            'name' => $attrname_ezf_name_id,
            'value' => $value_ezf_name_id,
            'options' => ['placeholder' => Yii::t('ezmodule', 'Forms'), 'id' => 'config_ezf_name_id'],
            'data' => ArrayHelper::map($itemsEzform, 'ezf_id', 'ezf_name'),
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
    <div class="col-md-6 ">
        <?php
        $attrname_ezf_match_id = 'options[ezf_match_id]';
        $value_ezf_match_id = isset($options['ezf_match_id']) ? $options['ezf_match_id'] : '';
        ?>
        <?= Html::label(Yii::t('ezmodule', 'Forms'), $attrname_ezf_match_id, ['class' => 'control-label']) ?>
        <?php
        echo kartik\select2\Select2::widget([
            'name' => $attrname_ezf_match_id,
            'value' => $value_ezf_match_id,
            'options' => ['placeholder' => Yii::t('ezmodule', 'Forms'), 'id' => 'config_ezf_match_id'],
            'data' => ArrayHelper::map($itemsEzform, 'ezf_id', 'ezf_name'),
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
</div>

<?php
$this->registerJS("
    fieldDisplay($('#config_ezf_id').val());
    fieldDownload($('#config_ezf_id').val());
    fieldStatus($('#config_ezf_id').val());

    $('#config_ezf_id').on('change',function(){
      var ezf_id = $(this).val();
      fieldDisplay(ezf_id);
      fieldDownload(ezf_id);
      fieldStatus(ezf_id);
    });
    
    function fieldDisplay(ezf_id){
        var value = " . json_encode($field_display) . ";
        $.post('" . Url::to(['/ezforms2/target/get-fields']) . "',{ ezf_id: ezf_id, multiple:1, name: '{$attrname_field_display}', value: value ,id:'config_field_display'}
          ).done(function(result){
             $('#field_display_box').html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
    function fieldDownload(ezf_id){
        var value = " . json_encode($field_download) . ";
        $.post('" . Url::to(['/ezforms2/target/get-fields']) . "',{ ezf_id: ezf_id, multiple:1, name: '{$attrname_field_download}', value: value ,id:'config_field_download'}
          ).done(function(result){
             $('#field_download_box').html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
function fieldStatus(ezf_id){
        var value = " . json_encode($field_status) . ";
        $.post('" . Url::to(['/ezforms2/target/get-fields']) . "',{ ezf_id: ezf_id, multiple:1, name: '{$attrname_field_status}', value: value ,id:'config_field_status'}
          ).done(function(result){
             $('#field_status_box').html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
    
");
?>