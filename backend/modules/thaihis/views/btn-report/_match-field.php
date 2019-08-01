<?php

use \kartik\widgets\Select2;
use yii\helpers\Html;
$id = \appxq\sdii\utils\SDUtility::getMillisecTime();
?>


<div class="form-group row">
    <hr>
    <div class="col-md-6">
        <?= Html::label(Yii::t('ezform', 'Header Report'), 'options[header_report]', ['class' => 'control-label']) ?>
        <?= Html::textInput('options[header_report]', (isset($options['header_report']) ? $options['header_report'] : Yii::t('ezform', 'EMR')), ['class' => 'form-control']) ?>
    </div>
    <div class="clearfix"></div>
    <hr>
</div>

<div class="form-group row">
    <div class="col-md-12">
        <h4 class="modal-title"><?=Yii::t('btn-report', 'Patient Profile')?></h4>
    </div>
    <hr>
    <div class="col-md-4">
        <?php
        echo Html::label(Yii::t('btn-report', 'คำนำหน้า'), 'options[template_content]', ['class' => 'control-label']);
        echo Select2::widget([
                'id' => 'pt-prefix-'.$id,
            'name' => 'options[match_field][pt_prefix]',
            'value' => isset($options['match_field']['pt_prefix']) ? $options['match_field']['pt_prefix'] : '',
            'data' => $dataForm,
            'options' => ['placeholder' => 'Select a field ...'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
    <div class="col-md-4 sdbox-col">
        <?php
        echo Html::label(Yii::t('btn-report', 'ชื่อ'), 'options[template_content]', ['class' => 'control-label']);
        echo Select2::widget([
            'id' => 'pt-firstname-'.$id,
            'name' => 'options[match_field][pt_firstname]',
            'value' => isset($options['match_field']['pt_firstname']) ? $options['match_field']['pt_firstname'] : '',
            'data' => $dataForm,
            'options' => ['placeholder' => 'Select a field ...'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
    <div class="col-md-4 sdbox-col">
        <?php
        echo Html::label(Yii::t('btn-report', 'สกุล'), 'options[template_content]', ['class' => 'control-label']);
        echo Select2::widget([
            'id' => 'pt-lastname-'.$id,
            'name' => 'options[match_field][pt_lastname]',
            'value' => isset($options['match_field']['pt_lastname']) ? $options['match_field']['pt_lastname'] : '',
            'data' => $dataForm,
            'options' => ['placeholder' => 'Select a field ...'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
</div>
<div class="form-group row">
    <div class="col-md-3">
        <?php
        echo Html::label(Yii::t('btn-report', 'อายุ'), 'options[template_content]', ['class' => 'control-label']);
        echo Select2::widget([
            'id' => 'pt-age-'.$id,
            'name' => 'options[match_field][pt_age]',
            'value' => isset($options['match_field']['pt_age']) ? $options['match_field']['pt_age'] : '',
            'data' => $dataForm,
            'options' => ['placeholder' => 'Select a field ...'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
    <div class="col-md-3 sdbox-col">
        <?php
        echo Html::label(Yii::t('btn-report', 'HN'), 'options[template_content]', ['class' => 'control-label']);
        echo Select2::widget([
            'id' => 'pt-hn-'.$id,
            'name' => 'options[match_field][pt_hn]',
            'value' => isset($options['match_field']['pt_hn']) ? $options['match_field']['pt_hn'] : '',
            'data' => $dataForm,
            'options' => ['placeholder' => 'Select a field ...'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
</div>
<div class="clearfix"></div>
<hr>
<div class="form-group row">
    <div class="col-md-12">
        <h4 class="modal-title"><?=Yii::t('btn-report', 'อาการนำผู้ป่วย')?></h4>
    </div>
    <hr>
    <div class="col-md-3">
        <?php
        echo Html::label(Yii::t('btn-report', 'CC'), 'options[template_content]', ['class' => 'control-label']);
        echo Select2::widget([
            'id' => 'tk-cc-'.$id,
            'name' => 'options[match_field][tk_cc]',
            'value' => isset($options['match_field']['tk_cc']) ? $options['match_field']['tk_cc'] : '',
            'data' => $dataForm,
            'options' => ['placeholder' => 'Select a field ...'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
    <div class="col-md-3 sdbox-col">
        <?php
        echo Html::label(Yii::t('btn-report', 'PH'), 'options[template_content]', ['class' => 'control-label']);
        echo Select2::widget([
            'id' => 'tk-ph-'.$id,
            'name' => 'options[match_field][tk_ph]',
            'value' => isset($options['match_field']['tk_ph']) ? $options['match_field']['tk_ph'] : '',
            'data' => $dataForm,
            'options' => ['placeholder' => 'Select a field ...'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
    <div class="col-md-3 sdbox-col">
        <?php
        echo Html::label(Yii::t('btn-report', 'PI'), 'options[template_content]', ['class' => 'control-label']);
        echo Select2::widget([
            'id' => 'tk-pi-'.$id,
            'name' => 'options[match_field][tk_pi]',
            'value' => isset($options['match_field']['tk_pi']) ? $options['match_field']['tk_pi'] : '',
            'data' => $dataForm,
            'options' => ['placeholder' => 'Select a field ...'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
    <div class="col-md-3 sdbox-col">
        <?php
        echo Html::label(Yii::t('btn-report', 'FH'), 'options[template_content]', ['class' => 'control-label']);
        echo Select2::widget([
            'id' => 'tk-fh-'.$id,
            'name' => 'options[match_field][tk_fh]',
            'value' => isset($options['match_field']['tk_fh']) ? $options['match_field']['tk_fh'] : '',
            'data' => $dataForm,
            'options' => ['placeholder' => 'Select a field ...'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
</div>

<div class="form-group row">
    <div class="col-md-3">
        <?php
        echo Html::label(Yii::t('btn-report', 'การตรวจ'), 'options[template_content]', ['class' => 'control-label']);
        echo Select2::widget([
            'id' => 'tk-inspect-'.$id,
            'name' => 'options[match_field][tk_inspect]',
            'value' => isset($options['match_field']['tk_inspect']) ? $options['match_field']['tk_inspect'] : '',
            'data' => $dataForm,
            'options' => ['placeholder' => 'Select a field ...'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
    <div class="col-md-3 sdbox-col">
        <?php
        echo Html::label(Yii::t('btn-report', 'NPO'), 'options[template_content]', ['class' => 'control-label']);
        echo Select2::widget([
            'id' => 'tk-npo-'.$id,
            'name' => 'options[match_field][tk_npo]',
            'value' => isset($options['match_field']['tk_npo']) ? $options['match_field']['tk_npo'] : '',
            'data' => $dataForm,
            'options' => ['placeholder' => 'Select a field ...'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>

</div>
<div class="clearfix"></div>
<hr>
<div class="form-group row">
    <div class="col-md-12">
        <h4 class="modal-title"><?=Yii::t('btn-report', 'การตรวจร่างกาย')?></h4>
    </div>
    <hr>

        <?php

        echo Html::beginTag('div',['class'=>'col-md-4']);
        echo Html::label(Yii::t('btn-report', 'PE'), 'options[template_content]', ['class' => 'control-label']);
        echo Select2::widget([
            'id' => 'pe-n-all-'.$id,
            'name' => 'options[match_field][pe_n_all]',
            'value' => isset($options['match_field']['pe_n_all']) ? $options['match_field']['pe_n_all'] : '',
            'data' => $dataForm,
            'options' => ['placeholder' => 'Select a field ...'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        echo Html::endTag('div');
        echo Html::beginTag('div',['class'=>'col-md-4 sdbox-col']);
        echo Html::label(' . ', 'options[template_content]', ['class' => 'control-label']);
        echo Select2::widget([
            'id' => 'pe-ga-'.$id,
            'name' => 'options[match_field][pe_ga]',
            'value' => isset($options['match_field']['pe_ga']) ? $options['match_field']['pe_ga'] : '',
            'data' => $dataForm,
            'options' => ['placeholder' => 'Select a field ...'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        echo Html::endTag('div');
        ?>

    <div class="clearfix">
    </div>

</div>
<div class="form-group row">
    <div class="col-md-4">
        <?php
        echo Html::label(Yii::t('btn-report', 'Note'), 'options[template_content]', ['class' => 'control-label']);
        echo Select2::widget([
            'id' => 'pe-note-'.$id,
            'name' => 'options[match_field][pe_note]',
            'value' => isset($options['match_field']['pe_note']) ? $options['match_field']['pe_note'] : '',
            'data' => $dataForm,
            'options' => ['placeholder' => 'Select a field ...'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>

</div>

<div class="clearfix"></div>
<hr>
<div class="form-group row">
    <div class="col-md-12">
        <h4 class="modal-title"><?=Yii::t('btn-report', 'Diagnosis')?></h4>
    </div>
    <hr>
    <div class="col-md-6">
        <?php
        echo Html::label(Yii::t('btn-report', 'Principal Dx'), 'options[template_content]', ['class' => 'control-label']);
        echo Select2::widget([
            'id' => 'di_txt-'.$id,
            'name' => 'options[match_field][di_txt]',
            'value' => isset($options['match_field']['di_txt']) ? $options['match_field']['di_txt'] : '',
            'data' => $dataForm,
            'options' => ['placeholder' => 'Select a field ...'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
    <div class="col-md-6 sdbox-col">
        <?php
        echo Html::label(Yii::t('btn-report', 'Principal ICD10'), 'options[template_content]', ['class' => 'control-label']);
        echo Select2::widget([
            'id' => 'di_icd10-'.$id,
            'name' => 'options[match_field][di_icd10]',
            'value' => isset($options['match_field']['di_icd10']) ? $options['match_field']['di_icd10'] : '',
            'data' => $dataForm,
            'options' => ['placeholder' => 'Select a field ...'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>

</div>

<div class="clearfix"></div>
<hr>
<div class="form-group row">
    <div class="col-md-12">
        <h4 class="modal-title"><?=Yii::t('btn-report', 'Treatment')?></h4>
    </div>
    <hr>
    <div class="col-md-4">
        <?php
        echo Html::label(Yii::t('btn-report', 'Consult แผนก'), 'options[template_content]', ['class' => 'control-label']);
        echo Select2::widget([
            'id' => 'treat_consult-'.$id,
            'name' => 'options[match_field][treat_consult]',
            'value' => isset($options['match_field']['treat_consult']) ? $options['match_field']['treat_consult'] : '',
            'data' => $dataForm,
            'options' => ['placeholder' => 'Select a field ...'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
    <div class="col-md-4 sdbox-col">
        <?php
        echo Html::label(Yii::t('btn-report', 'Medication'), 'options[template_content]', ['class' => 'control-label']);
        echo Select2::widget([
            'id' => 'treat_med_check-'.$id,
            'name' => 'options[match_field][treat_med_check]',
            'value' => isset($options['match_field']['treat_med_check']) ? $options['match_field']['treat_med_check'] : '',
            'data' => $dataForm,
            'options' => ['placeholder' => 'Select a field ...'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
    <div class="col-md-4 sdbox-col">
        <?php
        echo Html::label(Yii::t('btn-report', 'แนะนําอาหาร ,การปฏิบัติตัว ,ตรวจรักษาตามอาการที่ รพ. ตามสิทธิ์'), 'options[template_content]', ['class' => 'control-label']);
        echo Select2::widget([
            'id' => 'treat_advice_check-'.$id,
            'name' => 'options[match_field][treat_advice_check]',
            'value' => isset($options['match_field']['treat_advice_check']) ? $options['match_field']['treat_advice_check'] : '',
            'data' => $dataForm,
            'options' => ['placeholder' => 'Select a field ...'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>

</div>

<div class="form-group row">

    <div class="col-md-4">
        <?php
        echo Html::label(Yii::t('btn-report', 'ตอบกลับ รพ.ต้นสังกัด'), 'options[template_content]', ['class' => 'control-label']);
        echo Select2::widget([
            'id' => 'treat_send_check-'.$id,
            'name' => 'options[match_field][treat_send_check]',
            'value' => isset($options['match_field']['treat_send_check']) ? $options['match_field']['treat_send_check'] : '',
            'data' => $dataForm,
            'options' => ['placeholder' => 'Select a field ...'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
    <div class="col-md-4 sdbox-col">
        <?php
        echo Html::label(Yii::t('btn-report', '.'), 'options[template_content]', ['class' => 'control-label']);
        echo Select2::widget([
            'id' => 'treat_send_hosp-'.$id,
            'name' => 'options[match_field][treat_send_hosp]',
            'value' => isset($options['match_field']['treat_send_hosp']) ? $options['match_field']['treat_send_hosp'] : '',
            'data' => $dataForm,
            'options' => ['placeholder' => 'Select a field ...'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
    <div class="clearfix"></div>

</div>

<div class="form-group row">

    <div class="col-md-4">
        <?php
        echo Html::label(Yii::t('btn-report', 'Follow up'), 'options[template_content]', ['class' => 'control-label']);
        echo Select2::widget([
            'id' => 'treat_fu_check-'.$id,
            'name' => 'options[match_field][treat_fu_check]',
            'value' => isset($options['match_field']['treat_fu_check']) ? $options['match_field']['treat_fu_check'] : '',
            'data' => $dataForm,
            'options' => ['placeholder' => 'Select a field ...'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
    <div class="col-md-4 sdbox-col">
        <?php
        echo Html::label(Yii::t('btn-report', '.'), 'options[template_content]', ['class' => 'control-label']);
        echo Select2::widget([
            'id' => 'treat_fu_time-'.$id,
            'name' => 'options[match_field][treat_fu_time]',
            'value' => isset($options['match_field']['treat_fu_time']) ? $options['match_field']['treat_fu_time'] : '',
            'data' => $dataForm,
            'options' => ['placeholder' => 'Select a field ...'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
    <div class="clearfix"></div>

</div>

<div class="form-group row">

    <div class="col-md-4">
        <?php
        echo Html::label(Yii::t('btn-report', 'แนะนำพบแพทย์ เฉพาะทาง'), 'options[template_content]', ['class' => 'control-label']);
        echo Select2::widget([
            'id' => 'treat_advicedoc_check-'.$id,
            'name' => 'options[match_field][treat_advicedoc_check]',
            'value' => isset($options['match_field']['treat_advicedoc_check']) ? $options['match_field']['treat_advicedoc_check'] : '',
            'data' => $dataForm,
            'options' => ['placeholder' => 'Select a field ...'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
    <div class="col-md-4 sdbox-col">
        <?php
        echo Html::label(Yii::t('btn-report', '.'), 'options[template_content]', ['class' => 'control-label']);
        echo Select2::widget([
            'id' => 'treat_advicedoc_txt-'.$id,
            'name' => 'options[match_field][treat_advicedoc_txt]',
            'value' => isset($options['match_field']['treat_advicedoc_txt']) ? $options['match_field']['treat_advicedoc_txt'] : '',
            'data' => $dataForm,
            'options' => ['placeholder' => 'Select a field ...'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
    <div class="clearfix"></div>

</div>

<div class="form-group row">
    <div class="col-md-4">
        <?php
        echo Html::label(Yii::t('btn-report', 'อื่นๆ'), 'options[template_content]', ['class' => 'control-label']);
        echo Select2::widget([
            'id' => 'treat_commant-'.$id,
            'name' => 'options[match_field][treat_commant]',
            'value' => isset($options['match_field']['treat_commant']) ? $options['match_field']['treat_commant'] : '',
            'data' => $dataForm,
            'options' => ['placeholder' => 'Select a field ...'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>

</div>
<div class="clearfix"></div>
<hr>

<div class="form-group row">
    <div class="col-md-12">
        <h4 class="modal-title"><?=Yii::t('btn-report', 'Vital Sugn')?></h4>
    </div>
    <hr>
    <div class="col-md-4">
        <?php
        echo Html::label(Yii::t('btn-report', 'BP'), 'options[template_content]', ['class' => 'control-label']);
        echo Select2::widget([
            'id' => 'vs_bp_squeeze-'.$id,
            'name' => 'options[match_field][vs_bp_squeeze]',
            'value' => isset($options['match_field']['vs_bp_squeeze']) ? $options['match_field']['vs_bp_squeeze'] : '',
            'data' => $dataForm,
            'options' => ['placeholder' => 'Select a field ...'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
    <div class="col-md-4 sdbox-col">
        <?php
        echo Html::label(Yii::t('btn-report', '.'), 'options[template_content]', ['class' => 'control-label']);
        echo Select2::widget([
            'id' => 'vs_bp_loosen-'.$id,
            'name' => 'options[match_field][vs_bp_loosen]',
            'value' => isset($options['match_field']['vs_bp_loosen']) ? $options['match_field']['vs_bp_loosen'] : '',
            'data' => $dataForm,
            'options' => ['placeholder' => 'Select a field ...'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
    <div class="clearfix"></div>

</div>
<div class="form-group row">
    <div class="col-md-4">
        <?php
        echo Html::label(Yii::t('btn-report', 'P'), 'options[template_content]', ['class' => 'control-label']);
        echo Select2::widget([
            'id' => 'vs_pulse-'.$id,
            'name' => 'options[match_field][vs_pulse]',
            'value' => isset($options['match_field']['vs_pulse']) ? $options['match_field']['vs_pulse'] : '',
            'data' => $dataForm,
            'options' => ['placeholder' => 'Select a field ...'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
    <div class="col-md-4 sdbox-col">
        <?php
        echo Html::label(Yii::t('btn-report', 'R'), 'options[template_content]', ['class' => 'control-label']);
        echo Select2::widget([
            'id' => 'vs_respiratory-'.$id,
            'name' => 'options[match_field][vs_respiratory]',
            'value' => isset($options['match_field']['vs_respiratory']) ? $options['match_field']['vs_respiratory'] : '',
            'data' => $dataForm,
            'options' => ['placeholder' => 'Select a field ...'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
    <div class="col-md-4 sdbox-col">
        <?php
        echo Html::label(Yii::t('btn-report', 'T'), 'options[template_content]', ['class' => 'control-label']);
        echo Select2::widget([
            'id' => 'vs_temperature-'.$id,
            'name' => 'options[match_field][vs_temperature]',
            'value' => isset($options['match_field']['vs_temperature']) ? $options['match_field']['vs_temperature'] : '',
            'data' => $dataForm,
            'options' => ['placeholder' => 'Select a field ...'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>

</div>
<div class="clearfix"></div>
<hr>
<div class="form-group row">
    <div class="col-md-12">
        <h4 class="modal-title"><?=Yii::t('btn-report', 'ดัชนีมวลกาย')?></h4>
    </div>
    <hr>
    <div class="col-md-4">
        <?php
        echo Html::label(Yii::t('btn-report', 'BW'), 'options[template_content]', ['class' => 'control-label']);
        echo Select2::widget([
            'id' => 'bmi_bw-'.$id,
            'name' => 'options[match_field][bmi_bw]',
            'value' => isset($options['match_field']['bmi_bw']) ? $options['match_field']['bmi_bw'] : '',
            'data' => $dataForm,
            'options' => ['placeholder' => 'Select a field ...'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
    <div class="col-md-4 sdbox-col">
        <?php
        echo Html::label(Yii::t('btn-report', 'HT'), 'options[template_content]', ['class' => 'control-label']);
        echo Select2::widget([
            'id' => 'bmi_ht-'.$id,
            'name' => 'options[match_field][bmi_ht]',
            'value' => isset($options['match_field']['bmi_ht']) ? $options['match_field']['bmi_ht'] : '',
            'data' => $dataForm,
            'options' => ['placeholder' => 'Select a field ...'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
    <div class="col-md-4 sdbox-col">
        <?php
        echo Html::label(Yii::t('btn-report', 'BMI'), 'options[template_content]', ['class' => 'control-label']);
        echo Select2::widget([
            'id' => 'bmi_bmi-'.$id,
            'name' => 'options[match_field][bmi_bmi]',
            'value' => isset($options['match_field']['bmi_bmi']) ? $options['match_field']['bmi_bmi'] : '',
            'data' => $dataForm,
            'options' => ['placeholder' => 'Select a field ...'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>

</div>
<div class="form-group row">
    <div class="col-md-4">
        <?php
        echo Html::label(Yii::t('btn-report', 'BSA'), 'options[template_content]', ['class' => 'control-label']);
        echo Select2::widget([
            'id' => 'bmi_bsa-'.$id,
            'name' => 'options[match_field][bmi_bsa]',
            'value' => isset($options['match_field']['bmi_bsa']) ? $options['match_field']['bmi_bsa'] : '',
            'data' => $dataForm,
            'options' => ['placeholder' => 'Select a field ...'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
    <div class="col-md-4 sdbox-col">
        <?php
        echo Html::label(Yii::t('btn-report', 'รอบเอว'), 'options[template_content]', ['class' => 'control-label']);
        echo Select2::widget([
            'id' => 'bmi_waistline-'.$id,
            'name' => 'options[match_field][bmi_waistline]',
            'value' => isset($options['match_field']['bmi_waistline']) ? $options['match_field']['bmi_waistline'] : '',
            'data' => $dataForm,
            'options' => ['placeholder' => 'Select a field ...'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
    <div class="clearfix">
    </div>

</div>