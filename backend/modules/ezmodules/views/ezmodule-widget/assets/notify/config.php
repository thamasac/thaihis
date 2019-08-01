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
    <div class="col-md-2">
        <?= Html::label(Yii::t('ezform', 'Page Size'), 'options[page_size]', ['class' => 'control-label']) ?>
        <?= Html::textInput('options[page_size]', (isset($options['page_size']) ? $options['page_size'] : Yii::t('ezform', 'Page Size')), ['class' => 'form-control', 'type' => 'number']) ?>
    </div>
    <div class="col-md-2" style="margin-top:18px;">
        <?= \backend\modules\ezforms2\classes\EzformWidget::checkbox('options[action]', (isset($options['action']) ? $options['action'] : 0), ['label' => 'Action']) ?>
    </div>
    <div class="col-md-2" style="margin-top:18px;">
        <?= \backend\modules\ezforms2\classes\EzformWidget::checkbox('options[hide_tab]', (isset($options['hide_tab']) ? $options['hide_tab'] : 0), ['label' => 'Hide tab']) ?>
    </div>
    <div class="col-md-6" style="margin-top:18px;">
       
    </div>
    <div class="clearfix"></div>
</div>








<?php
$this->registerJS("
    fields($('#config_ezf_id').val()," . $value_fields . ",'ref_field_box','config_fields','{$attrname_fields}');
    
$('#config_ezf_id').on('change',function(){
        var ezf_id = $(this).val();
        fields(ezf_id," . $value_fields . ",'ref_field_box','config_fields','{$attrname_fields}');
    });
    
    
    
    function fields(ezf_id,value,div,id,name){
        $.post('" . Url::to(['/ezforms2/target/get-fields']) . "',{ ezf_id: ezf_id, multiple:1, name:name, value: value ,id:id}
          ).done(function(result){
             $('#'+div).html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
    
    
");
?>