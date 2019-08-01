<?php

use backend\modules\patient\classes\PatientHelper;
use yii\helpers\Url;
use appxq\sdii\helpers\SDNoty;
use yii\helpers\Html;

if ($IO == 'I') {
    switch ($dataAdmit['admit_status']) {
        case '1':

            break;
        case '2':
            if ($dataAdmit['bed_tran_status'] == '1') {
                echo PatientHelper::btnEditTxt(Yii::t('patient', 'Edit') . ' ' . Yii::t('patient', 'Move Ward,Bed'), $ezfBedTran_id, $dataAdmit['bed_tran_id'], [], $reloadDiv, 'modal-ezform-main', 'btn-warning', 'fa fa-bed');
            } else {
                echo PatientHelper::btnAddTxt(Yii::t('patient', 'Move Ward,Bed'), $ezfBedTran_id, $dataAdmit['admit_id'], ['bed_tran_status' => '1'], $reloadDiv, 'modal-ezform-main', 'btn-warning', 'fa fa-bed');
            }

            $ezfAdmit_id = \backend\modules\patient\Module::$formID['admit'];
    $btnEdit = \backend\modules\ezforms2\classes\EzfHelper::btn($ezfAdmit_id)
            ->label(Yii::t('patient', 'Admit Form'))
            ->options(['class'=>'btn btn-primary'])
            ->buildBtnEdit($dataAdmit['admit_id'] );
            echo ' '.$btnEdit;
            $url = Url::to(['/patient/admit/admit-btnadt-submit', 'dataid' => $dataAdmit['admit_id'], 'target' => $modelVisit['id'], 'reloadDiv' => $reloadDiv, 'action' => 'predis']);
            echo ' ' . Html::button('<i class="fa fa-transfer"></i> ' . Yii::t('patient', 'Pre Discharge'), ['class' => 'btn btn-danger btn-cancel-admit', 'data-url' => $url, 'data-action' => 'predischarge']);

            break;
        case '3':
            echo PatientHelper::btnAddTxt(Yii::t('patient', 'Discharge'), $ezfDisch_id, $visit_id, ['discharge_date' => date('d-m-Y H:i'), 'discharge_from_dept' => Yii::$app->user->identity->profile->attributes['department']], 'print-discharge', 'modal-ward-xxl', '', 'fa fa-share');

            $url = Url::to(['/patient/admit/admit-btnadt-submit', 'dataid' => $dataAdmit['admit_id'], 'target' => $modelVisit['id'], 'reloadDiv' => $reloadDiv, 'action' => 'cancelpredis']);
            echo ' ' . Html::button('<i class="fa fa-close"></i> ' . Yii::t('patient', 'Cancel Discharge'), ['class' => 'btn btn-danger btn-cancel-admit', 'data-url' => $url, 'data-action' => 'predischarge']);
            break;
        case '4':
            echo PatientHelper::btnEditTxt(Yii::t('patient', 'Transfer'), $ezfAdmit_id, $dataAdmit['admit_id'], [], $reloadDiv, 'modal-ezform-main', 'btn-block', 'fa fa-share');
            break;
        default:
            if ($bedtran_id) {
                echo PatientHelper::btnEditTxt(Yii::t('patient', 'Edit') . ' ' . Yii::t('patient', 'Move Ward,Bed'), $ezfBedTran_id, $bedtran_id, [], $reloadDiv, 'modal-' . $ezfBedTran_id, 'btn-warning', 'fa fa-bed');
            } else {
                echo PatientHelper::btnAddTxt(Yii::t('patient', 'Select') . Yii::t('patient', 'Ward'), $ezfBedTran_id, $admit_id, ['bed_tran_status' => '1'], $reloadDiv, 'modal-' . $ezfBedTran_id);
            }
            break;
    }
} else {
    if ($dataAdmit['admit_status'] == '1') {
        $url = Url::to(['/patient/admit/admit-btnadt-submit', 'dataid' => $dataAdmit['admit_id'], 'target' => $visit_id, 'reloadDiv' => $reloadDiv, 'action' => 'cancel']);
        echo Html::button('<i class="fa fa-close"></i> ' . Yii::t('patient', 'Cancel Admit'), ['class' => 'btn btn-danger btn-block btn-cancel-admit', 'data-url' => $url, 'data-action' => 'cancel']);
    } elseif (empty($dataAdmit)) {
        echo PatientHelper::btnAddTxt(Yii::t('patient', 'Admit'), $ezfAdmit_id, $visit_id, ['admit_status' => '1', 'admit_doctor_id' => '', //หาวิธีค้นหาแพทย์ผู้สั่ง Admit ในกรณี Visit หลายเค้าเตอร์
            'admit_date' => date('d-m-Y H:i'), 'admit_from_dept' => Yii::$app->user->identity->profile->attributes['department']]
                , $reloadDiv, 'modal-ezform-main', 'btn-block', 'fa fa-bed');
    }
}

$this->registerJs("
   $('#ezf-modal-box').append('<div id=\"modal-{$ezfBedTran_id}\" class=\"fade modal\" role=\"dialog\"><div class=\"modal-dialog modal-xxl\"><div class=\"modal-content\"></div></div></div>');

   $('#$reloadDiv .btn-cancel-admit').on('click', function(){
            var url = $(this).attr('data-url');
            var action = $(this).attr('data-action');
            var txtConfirm = '';
            if(action === 'cancel'){
                txtConfirm = '" . Yii::t('patient', 'Are you sure you want to cancel admit?') . "';
            }else if(action === 'predischarge'){
                txtConfirm = '" . Yii::t('patient', 'Are you sure you want to pre discharge?') . "';
            }
            yii.confirm(txtConfirm, function(){
                $.get(url).done(function(result){
                        if(result.status == 'success'){
                                " . SDNoty::show('result.message', 'result.status') . "
                                var urlreload =  $('#$reloadDiv').attr('data-url');
                                getUiAjax(urlreload, '$reloadDiv');
                        } else {
                                " . SDNoty::show('result.message', 'result.status') . "
                        }
                }).fail(function(){
                        " . SDNoty::show("'" . "Server Error'", '"error"') . "
                        console.log('server error');
                });
            });
        });
  ");
$url = Url::to(['/patient/admit/print-discharge', 'visit_id' => (isset($visit_id) ? $visit_id : '')]);
?>
<div id="print-discharge" data-url="<?= $url ?>">

</div>