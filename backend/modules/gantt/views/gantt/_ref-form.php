<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use backend\modules\ezforms2\classes\EzActiveForm;
use backend\modules\ezforms2\classes\EzfFunc;
?>    

<div class="reff-ezform">
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
            $attrname_actual_date = 'options[actual_date]';
            $field_actual_date = isset($options['actual_date']) ? $options['actual_date'] : '';
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
            echo Html::input('number', 'options[plan_date]', '', ['class' => 'form-control']);
            ?>
        </div>
        <div class="col-md-4 sdbox-col">
            <?= Html::label(Yii::t('ezmodule', 'Earliest Date'), 'options[earliest_date]', ['class' => 'control-label']) ?>
            <?php
            echo Html::input('number', 'options[earliest_date]', '', ['class' => 'form-control']);
            ?>
        </div>
        <div class="col-md-4 sdbox-col">
            <?= Html::label(Yii::t('ezmodule', 'Latest Date'), 'options[latest_date]', ['class' => 'control-label']) ?>
            <?php
            echo Html::input('number', 'options[latest_date]', '', ['class' => 'form-control']);
            ?>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="form-group row">

        <div class="col-md-6">
            <?= Html::label(Yii::t('ezform', 'Parent'), 'config_parent', ['class' => 'control-label']) ?>
            <?php
            echo kartik\select2\Select2::widget([
                'name' => 'config_parent',
                'value' => '',
                'options' => ['placeholder' => Yii::t('ezmodule', 'Parent'), 'id' => 'config_refparent'],
                'data' => [],
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

<?php
$this->registerJs("
    fieldDisplay($('#config_ezf_id').val());
    fieldDownload($('#config_ezf_id').val());

    $('#config_ezf_id').on('change',function(){
      var ezf_id = $(this).val();
      fieldDisplay(ezf_id);
      fieldDownload(ezf_id);
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
    ");
?>