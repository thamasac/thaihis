<?php

use backend\modules\patient\classes\PatientHelper;

//if ($model['id']) {
//    echo PatientHelper::btnEditTxt(Yii::t('patient', 'Edit') . ' ' . Yii::t('patient', 'Certificate'), $ezf_id, $model['id'], [], 'btn-certificate-save', 'modal-' . $ezf_id, '', 'glyphicon glyphicon-calendar');
//
//    $initdata = [];
//    if ($userProfile['position'] == '2') {
//        $initdata['app_doctor'] = $userProfile['user_id'];
//    }
//    $initdata['app_dept'] = $dept;
//    $initdata['app_status'] = '1';
//
//    echo ' ' . backend\modules\ezforms2\classes\BtnBuilder::btn()
//            ->ezf_id($ezf_id)
//            ->initdata($initdata)
//            ->reloadDiv('btn-certificate-save')
//            ->target($target)
//            ->modal('modal-' . $ezf_id)
//            ->label('<i class="glyphicon glyphicon-plus"></i>')->options(['class' => 'btn btn-warning'])
//            ->buildBtnAdd();
//} else {

$text = isset($options['btn_text']) && $options['btn_text'] != '' ? $options['btn_text'] : Yii::t('patient', 'Certificate');
$color = isset($options['btn_color']) && $options['btn_color'] != '' ? $options['btn_color'] : 'btn-default';
$btn_style = isset($options['btn_style']) && $options['btn_style'] != '' ? $options['btn_style'] : 'btn-block';
$icon = isset($options['btn_icon']) && $options['btn_icon'] != '' ? $options['btn_icon'] : 'fa-file';
$initdata = [];
if ($userProfile == 'doctor') {
    $initdata['cer_doctor'] = $userProfile['user_id'];
}
$initdata['cer_weight'] = $model_bmi['bmi_bw'];
$initdata['cer_height'] = $model_bmi['bmi_ht'];
$initdata['cer_pressure'] = $model_vs['vs_bp_squeeze'] . '/' . $model_vs['vs_bp_loosen'];
$initdata['cer_pulse'] = $model_vs['vs_pulse'];
$initdata['cer_diseases'] = $model_history['pt_disease_status'];
$initdata['cer_accident'] = $model_history['pt_or_status'] == 0 ? 1 : 2;
$initdata['cer_everbeentreated'] = 1;
$initdata['cer_physicalcondition'] = 1;
$initdata['app_dept'] = $dept;
$initdata['app_status'] = '1';
if ($target != '') {
    echo PatientHelper::btnAddTxt($text, $ezf_id, $target, $initdata, $reloadDiv . '-save', 'modal-ezform-main', $btn_style, 'fa '.$icon,$color);
}
?>