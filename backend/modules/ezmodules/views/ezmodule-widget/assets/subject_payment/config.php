<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
if ($ezf_id == '')
    $ezf_id = '0';
$options = isset($model->options) ? \appxq\sdii\utils\SDUtility::string2Array($model->options) : [];
$target = backend\modules\ezforms2\classes\EzfQuery::getTargetOne($ezf_id);
$user_id = \Yii::$app->user->id;

if ($target) {
    $itemsEzform = \backend\modules\ezforms2\classes\EzfQuery::getEzformRef($ezf_id, $target['parent_ezf_id']);
} else {
    $itemsEzform = \backend\modules\ezforms2\classes\EzfQuery::getEzformAll($ezf_id);
}
$itemsWidget = backend\modules\subjects\classes\SubjectManagementQuery::getWidgetByUserId($user_id);
?>

<div class="modal-header" style="margin-bottom: 15px;">
    <h4 class="modal-title" ><?= Yii::t('ezmodule', 'Widget Config')?></h4>
</div>

<!--config start-->

<div class="form-group row">

    <div class="col-md-4 ">
        <?php
        $attrname_widget_id = 'options[schedule_widget_id]';
        $value_widget_id = isset($options['schedule_widget_id']) ? $options['schedule_widget_id'] : '';
        ?>
        <?= Html::label(Yii::t('ezmodule', 'Visit Schedule Widget'), $attrname_widget_id, ['class' => 'control-label']) ?>
        <?php
        echo kartik\select2\Select2::widget([
            'name' => $attrname_widget_id,
            'value' => $value_widget_id,
            'options' => ['placeholder' => Yii::t('ezmodule', 'Widget'), 'id' => 'schedule_widget_id'],
            'data' => ArrayHelper::map($itemsWidget, 'widget_id', 'widget_name'),
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
    
    <div class="col-md-4 sdbox-col">
        <?php
        $attrname_procedure_widget_id = 'options[procedure_widget_id]';
        $procedure_widget_id = isset($options['procedure_widget_id']) ? $options['procedure_widget_id'] : '';
        ?>
        <?= Html::label(Yii::t('ezmodule', 'Visit Procedure Widget'), $attrname_procedure_widget_id, ['class' => 'control-label']) ?>
        <?php
        echo kartik\select2\Select2::widget([
            'name' => $attrname_procedure_widget_id,
            'value' => $procedure_widget_id,
            'options' => ['placeholder' => Yii::t('ezmodule', 'Widget'), 'id' => 'procedure_widget_id'],
            'data' => ArrayHelper::map($itemsWidget, 'widget_id', 'widget_name'),
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
    <div class="col-md-4 sdbox-col">
        <?php
        $attrname_financial_widget_id = 'options[financial_widget_id]';
        $financial_widget_id = isset($options['financial_widget_id']) ? $options['financial_widget_id'] : '';
        ?>
        <?= Html::label(Yii::t('ezmodule', 'Financial Widget'), $attrname_financial_widget_id, ['class' => 'control-label']) ?>
        <?php
        echo kartik\select2\Select2::widget([
            'name' => $attrname_financial_widget_id,
            'value' => $financial_widget_id,
            'options' => ['placeholder' => Yii::t('ezmodule', 'Widget'), 'id' => 'financial_widget_id'],
            'data' => ArrayHelper::map($itemsWidget, 'widget_id', 'widget_name'),
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
    <div class="clearfix"></div>

</div>

<div class="form-group row">

    <div class="col-md-6 ">
        <?php
        $attrname_budget_ezf_id = 'options[budget_ezf_id]';
        $budget_ezf_id = isset($options['budget_ezf_id']) ? $options['budget_ezf_id'] : '';
        ?>
        <?= Html::label(Yii::t('ezmodule', 'Budget Form'), $attrname_budget_ezf_id, ['class' => 'control-label']) ?>
        <?php
        echo kartik\select2\Select2::widget([
            'name' => $attrname_budget_ezf_id,
            'value' => $budget_ezf_id,
            'options' => ['placeholder' => Yii::t('ezmodule', 'Form'), 'id' => 'config_budget_ezf_id'],
            'data' => ArrayHelper::map($itemsEzform, 'ezf_id', 'ezf_name'),
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
    <div class="col-md-6 sdbox-col">
        <?php
        $attrname_budget_fields = 'options[budget_fields]';
        $budget_fields = isset($options['budget_fields']) ? $options['budget_fields'] : '';
        ?>
        <?= Html::label(Yii::t('ezform', 'Budget fields display'), $attrname_budget_fields, ['class' => 'control-label']) ?>
        <div id="budget_fields_box">

        </div>
    </div>
    <div class="clearfix"></div>

</div>
<div class="form-group row">

    <div class="col-md-6 ">
        <?php
        $attrname_section_ezf_id = 'options[section_ezf_id]';
        $section_ezf_id = isset($options['section_ezf_id']) ? $options['section_ezf_id'] : '';
        ?>
        <?= Html::label(Yii::t('ezmodule', 'Section Form'), $attrname_section_ezf_id, ['class' => 'control-label']) ?>
        <?php
        echo kartik\select2\Select2::widget([
            'name' => $attrname_section_ezf_id,
            'value' => $section_ezf_id,
            'options' => ['placeholder' => Yii::t('ezmodule', 'Form'), 'id' => 'config_section_ezf_id'],
            'data' => ArrayHelper::map($itemsEzform, 'ezf_id', 'ezf_name'),
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
    <div class="col-md-6 sdbox-col">
        <?php
        $attrname_budget_fields = 'options[budget_fields]';
        $budget_fields = isset($options['budget_fields']) ? $options['budget_fields'] : '';
        ?>
        <?= Html::label(Yii::t('ezform', 'Budget fields display'), $attrname_budget_fields, ['class' => 'control-label']) ?>
        <div id="budget_fields_box">

        </div>
    </div>
    <div class="clearfix"></div>

</div>
<!--config end-->

<?php
$this->registerJS("
    fieldsBudgetDisplay($('#config_budget_ezf_id').val());
    $('#config_budget_ezf_id').on('change',function(){
      var ezf_id = $(this).val();
      fieldsBudgetDisplay(ezf_id);
    });
    
    function fieldsBudgetDisplay(ezf_id){
        var value = " . json_encode($budget_fields) . ";
        $.post('" . Url::to(['/ezforms2/target/get-fields']) . "',{ ezf_id: ezf_id, multiple:1, name: '{$attrname_budget_fields}', value: value ,id:'config_budget_fields'}
          ).done(function(result){
             $('#budget_fields_box').html(result);
          }).fail(function(){
              " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
");
?>