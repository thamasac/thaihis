<?php

use backend\modules\patient\classes\PatientHelper;

if ($model['id']) {
    echo PatientHelper::btnEditTxt(Yii::t('patient', 'Edit') . ' ' . Yii::t('patient', 'Appointment'), $ezf_id, $model['id'], [], 'btn-appoint-save', 'modal-ezform-main', '', 'glyphicon glyphicon-calendar');

    $initdata = [];
    if ($userProfile['position'] == '2') {
        $initdata['app_doctor'] = $userProfile['user_id'];
    }
    $initdata['app_dept'] = $dept;
    $initdata['app_status'] = '1';
    
    echo ' '.backend\modules\ezforms2\classes\BtnBuilder::btn()
            ->ezf_id($ezf_id)
            ->initdata($initdata)
            ->reloadDiv('btn-appoint-save')
            ->target($target)
            //->modal('modal-' . $ezf_id)
            ->modal('modal-ezform-main')
            ->label('<i class="glyphicon glyphicon-plus"></i>')->options(['class' => 'btn btn-success'])
            ->buildBtnAdd();
} else {
    $initdata = [];
    if ($userProfile['position'] == '2') {
        $initdata['app_doctor'] = $userProfile['user_id'];
    }
    $initdata['app_dept'] = $dept;
    $initdata['app_status'] = '1';
    echo PatientHelper::btnAddTxt(Yii::t('patient', 'Appointment'), $ezf_id, $target, $initdata, 'btn-appoint-save', 'modal-ezform-main', 'btn-block', 'glyphicon glyphicon-calendar');
}
?>
