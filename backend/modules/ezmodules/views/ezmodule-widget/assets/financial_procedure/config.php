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
    <h4 class="modal-title" ><?= Yii::t('ezmodule', 'Widget Config') ?></h4>
</div>

<!--config start-->
<div class="form-group row">

    <div class="col-md-6 ">
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
    <div class="col-md-6 ">
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
    <div class="col-md-6 ">
        <?php
        $attrname_financial_type_ezf = 'options[financial_type_ezf]';
        $financial_type_ezf = isset($options['financial_type_ezf']) ? $options['financial_type_ezf'] : '';
        ?>
        <?= Html::label(Yii::t('ezmodule', 'Financial Type Form'), $attrname_financial_type_ezf, ['class' => 'control-label']) ?>
        <?php
        echo kartik\select2\Select2::widget([
            'name' => $attrname_financial_type_ezf,
            'value' => $financial_type_ezf,
            'options' => ['placeholder' => Yii::t('ezmodule', 'Form'), 'id' => 'config_financial_ezf_id'],
            'data' => ArrayHelper::map($itemsEzform, 'ezf_id', 'ezf_name'),
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
        <?= Html::label(Yii::t('ezform', 'Budget Fields Display'), $attrname_budget_fields, ['class' => 'control-label']) ?>
        <div id="budget_fields_box">

        </div>
    </div>
</div>
<div class="form-group row">
    <div class="col-md-6 ">
        <?php
        $attrname_study_ezf_id = 'options[study_ezf_id]';
        $study_ezf_id = isset($options['study_ezf_id']) ? $options['study_ezf_id'] : '';
        ?>
        <?= Html::label(Yii::t('ezmodule', 'Study Payment Form'), $attrname_study_ezf_id, ['class' => 'control-label']) ?>
        <?php
        echo kartik\select2\Select2::widget([
            'name' => $attrname_study_ezf_id,
            'value' => $study_ezf_id,
            'options' => ['placeholder' => Yii::t('ezmodule', 'Form'), 'id' => 'config_study_ezf_id'],
            'data' => ArrayHelper::map($itemsEzform, 'ezf_id', 'ezf_name'),
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
    <div class="col-md-6 sdbox-col">
        <?php
        $attrname_study_fields = 'options[study_fields]';
        $study_fields = isset($options['study_fields']) ? $options['study_fields'] : '';
        ?>
        <?= Html::label(Yii::t('ezform', 'Study Fields Display'), $attrname_budget_fields, ['class' => 'control-label']) ?>
        <div id="study_fields_box">

        </div>
    </div>
</div>
<div class="form-group row">
    <div class="col-md-6 ">
        <?php
        $attrname_invoice_ezf_id = 'options[invoice_ezf_id]';
        $invoice_ezf_id = isset($options['invoice_ezf_id']) ? $options['invoice_ezf_id'] : '';
        ?>
        <?= Html::label(Yii::t('ezmodule', 'Invoice Form'), $attrname_study_ezf_id, ['class' => 'control-label']) ?>
        <?php
        echo kartik\select2\Select2::widget([
            'name' => $attrname_invoice_ezf_id,
            'value' => $invoice_ezf_id,
            'options' => ['placeholder' => Yii::t('ezmodule', 'Form'), 'id' => 'config_invoice_ezf_id'],
            'data' => ArrayHelper::map($itemsEzform, 'ezf_id', 'ezf_name'),
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
    <div class="col-md-6 sdbox-col">
        <?php
        $attrname_invoice_fields = 'options[invoice_fields]';
        $invoice_fields = isset($options['invoice_fields']) ? $options['invoice_fields'] : '';
        ?>
        <?= Html::label(Yii::t('ezform', 'Invoice Fields Display'), $attrname_invoice_fields, ['class' => 'control-label']) ?>
        <div id="invoice_fields_box">

        </div>
    </div>
</div>
<hr/>
<h4>Config of Subject Payment</h4>

<div class="form-group row">
<div class="col-md-6 ">
        <?php
        $attrname_subject_payment_widget_id = 'options[subject_payment_widget_id]';
        $value_subject_payment_widget_id= isset($options['subject_payment_widget_id']) ? $options['subject_payment_widget_id'] : '';
        ?>
        <?= Html::label(Yii::t('ezmodule', 'Subject Payment Widget'), $attrname_subject_payment_widget_id, ['class' => 'control-label']) ?>
        <?php
        echo kartik\select2\Select2::widget([
            'name' => $attrname_subject_payment_widget_id,
            'value' => $value_subject_payment_widget_id,
            'options' => ['placeholder' => Yii::t('ezmodule', 'Widget'), 'id' => 'subject_payment_widget_id'],
            'data' => ArrayHelper::map($itemsWidget, 'widget_id', 'widget_name'),
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
</div>
<hr/>
<h4>Config of Clinical trial Agreement</h4>

<div class="form-group row">
    <div class="col-md-6 ">
        <?php
        $attrname_information_ezf_id = 'options[information_ezf_id]';
        $information_ezf_id = isset($options['information_ezf_id']) ? $options['information_ezf_id'] : '';
        ?>
        <?= Html::label(Yii::t('ezmodule', 'Information Of CTA Form'), $attrname_information_ezf_id, ['class' => 'control-label']) ?>
        <?php
        echo kartik\select2\Select2::widget([
            'name' => $attrname_information_ezf_id,
            'value' => $information_ezf_id,
            'options' => ['placeholder' => Yii::t('ezmodule', 'Form'), 'id' => 'config_information_ezf_id'],
            'data' => ArrayHelper::map($itemsEzform, 'ezf_id', 'ezf_name'),
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
    <div class="col-md-6 sdbox-col">
        <?php
        $attrname_information_fields = 'options[information_fields]';
        $information_fields = isset($options['information_fields']) ? $options['information_fields'] : '';
        ?>
        <?= Html::label(Yii::t('ezform', 'Fields Display'), $attrname_information_fields, ['class' => 'control-label']) ?>
        <div id="information_fields_box">

        </div>
    </div>
</div>

<div class="form-group row">
    <div class="col-md-6 ">
        <?php
        $attrname_protocol_ezf_id = 'options[protocol_ezf_id]';
        $protocol_ezf_id = isset($options['protocol_ezf_id']) ? $options['protocol_ezf_id'] : '';
        ?>
        <?= Html::label(Yii::t('ezmodule', 'Protocol Summary'), $attrname_protocol_ezf_id, ['class' => 'control-label']) ?>
        <?php
        echo kartik\select2\Select2::widget([
            'name' => $attrname_protocol_ezf_id,
            'value' => $protocol_ezf_id,
            'options' => ['placeholder' => Yii::t('ezmodule', 'Form'), 'id' => 'config_protocol_ezf_id'],
            'data' => ArrayHelper::map($itemsEzform, 'ezf_id', 'ezf_name'),
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
    <div class="col-md-6 sdbox-col">
        <?php
        $attrname_protocol_fields = 'options[protocol_fields]';
        $protocol_fields = isset($options['protocol_fields']) ? $options['protocol_fields'] : '';
        ?>
        <?= Html::label(Yii::t('ezform', 'Fields Display'), $attrname_protocol_fields, ['class' => 'control-label']) ?>
        <div id="protocol_fields_box">

        </div>
    </div>
</div>

<div class="form-group row">
    <div class="col-md-6 ">
        <?php
        $attrname_budget_payment_ezf_id = 'options[budget_payment_ezf_id]';
        $budget_payment_ezf_id = isset($options['budget_payment_ezf_id']) ? $options['budget_payment_ezf_id'] : '';
        ?>
        <?= Html::label(Yii::t('ezmodule', 'Budget And Payment Schedule'), $attrname_budget_payment_ezf_id, ['class' => 'control-label']) ?>
        <?php
        echo kartik\select2\Select2::widget([
            'name' => $attrname_budget_payment_ezf_id,
            'value' => $budget_payment_ezf_id,
            'options' => ['placeholder' => Yii::t('ezmodule', 'Form'), 'id' => 'config_budget_payment_ezf_id'],
            'data' => ArrayHelper::map($itemsEzform, 'ezf_id', 'ezf_name'),
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
    <div class="col-md-6 sdbox-col">
        <?php
        $attrname_budget_payment_fields = 'options[budget_payment_fields]';
        $budget_payment_fields = isset($options['budget_payment_fields']) ? $options['budget_payment_fields'] : '';
        ?>
        <?= Html::label(Yii::t('ezform', 'Fields Display'), $attrname_budget_payment_fields, ['class' => 'control-label']) ?>
        <div id="budget_payment_fields_box">

        </div>
    </div>
</div>
<!--config end-->

<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
    fieldBudgetFields($('#config_budget_ezf_id').val());
    
    $('#config_budget_ezf_id').on('change',function(){
      var ezf_id = $(this).val();
      fieldBudgetFields(ezf_id);
    });
    
    fieldStudyFields($('#config_study_ezf_id').val());
    
    $('#config_study_ezf_id').on('change',function(){
      var ezf_id = $(this).val();
      fieldStudyFields(ezf_id);
    });
    
    fieldInvoiceFields($('#config_invoice_ezf_id').val());
    
    $('#config_invoice_ezf_id').on('change',function(){
      var ezf_id = $(this).val();
      fieldInvoiceFields(ezf_id);
    });
    
    fieldInformationFields($('#config_information_ezf_id').val());
    
    $('#config_information_ezf_id').on('change',function(){
      var ezf_id = $(this).val();
      fieldInformationFields(ezf_id);
    });
    
    fieldProtocolFields($('#config_protocol_ezf_id').val());
    
    $('#config_protocol_ezf_id').on('change',function(){
      var ezf_id = $(this).val();
      fieldProtocolFields(ezf_id);
    });
    
    fieldBudgetPaymentFields($('#config_budget_payment_ezf_id').val());
    
    $('#config_budget_payment_ezf_id').on('change',function(){
      var ezf_id = $(this).val();
      fieldBudgetPaymentFields(ezf_id);
    });
    
    function fieldBudgetFields(ezf_id){
        var value = <?= json_encode($budget_fields) ?>;
        $.post('<?= Url::to(['/ezforms2/target/get-fields']) ?>',{ ezf_id: ezf_id, multiple:1, name: '<?=$attrname_budget_fields?>', value: value ,id:'config_budget_fields'}
          ).done(function(result){
             $('#budget_fields_box').html(result);
          }).fail(function(){
              <?= \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') ?>;
              console.log('server error');
          });
    }
    
    function fieldStudyFields(ezf_id){
        var value = <?= json_encode($study_fields) ?>;
        $.post('<?= Url::to(['/ezforms2/target/get-fields']) ?>',{ ezf_id: ezf_id, multiple:1, name: '<?=$attrname_study_fields?>', value: value ,id:'config_study_fields'}
          ).done(function(result){
             $('#study_fields_box').html(result);
          }).fail(function(){
              <?= \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') ?>;
              console.log('server error');
          });
    }
    
    function fieldInvoiceFields(ezf_id){
        var value = <?= json_encode($invoice_fields) ?>;
        $.post('<?= Url::to(['/ezforms2/target/get-fields']) ?>',{ ezf_id: ezf_id, multiple:1, name: '<?=$attrname_invoice_fields?>', value: value ,id:'config_invoice_fields'}
          ).done(function(result){
             $('#invoice_fields_box').html(result);
          }).fail(function(){
              <?= \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') ?>;
              console.log('server error');
          });
    }
    
    function fieldInformationFields(ezf_id){
        var value = <?= json_encode($information_fields) ?>;
        $.post('<?= Url::to(['/ezforms2/target/get-fields']) ?>',{ ezf_id: ezf_id, multiple:1, name: '<?=$attrname_information_fields?>', value: value ,id:'config_information_fields'}
          ).done(function(result){
             $('#information_fields_box').html(result);
          }).fail(function(){
              <?= \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') ?>;
              console.log('server error');
          });
    }
    
    function fieldProtocolFields(ezf_id){
        var value = <?= json_encode($protocol_fields) ?>;
        $.post('<?= Url::to(['/ezforms2/target/get-fields']) ?>',{ ezf_id: ezf_id, multiple:1, name: '<?=$attrname_protocol_fields?>', value: value ,id:'config_protocol_fields'}
          ).done(function(result){
             $('#protocol_fields_box').html(result);
          }).fail(function(){
              <?= \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') ?>;
              console.log('server error');
          });
    }
    
    function fieldBudgetPaymentFields(ezf_id){
        var value = <?= json_encode($budget_payment_fields) ?>;
        $.post('<?= Url::to(['/ezforms2/target/get-fields']) ?>',{ ezf_id: ezf_id, multiple:1, name: '<?=$attrname_budget_payment_fields?>', value: value ,id:'config_budget_payment_fields'}
          ).done(function(result){
             $('#budget_payment_fields_box').html(result);
          }).fail(function(){
              <?= \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') ?>;
              console.log('server error');
          });
    }

</script>
<?php \richardfan\widget\JSRegister::end(); ?>