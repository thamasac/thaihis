<?php

use appxq\sdii\utils\SDdate;
use backend\modules\patient\classes\PatientHelper;
use appxq\sdii\helpers\SDNoty;
?>
<div class="row">
  <table class="table" style="margin-bottom: 0px"> 
    <tbody>
      <tr>
        <td class="col-md-3 text-right">HN : </td>
        <td class="col-md-4"><label class="text-info"><?= $dataAdmit['pt_hn'] ?></label></td>

        <td class="col-md-2 text-right">AN : </td>
        <td class="col-md-3"><label class="text-info"><?= $dataAdmit['admit_an'] ?></label></td>
      </tr>  
      <tr>
        <td class="text-right"><?= Yii::t('patient', 'Name') ?> : </td>
        <td><label class="text-info"><?= $dataAdmit['fullname']; ?></label></td>

        <td class="text-right"><?= Yii::t('patient', 'Age') ?> : </td>
        <td><label class="text-info"><?php
                if ($dataAdmit['pt_bdate']) {
                    echo SDdate::getAge(SDdate::dateTh2bod($dataAdmit['pt_bdate'])) . ' ปี';
                }
                ?></label></td>
      </tr>
      <tr>                   
        <td class="text-right"><?= Yii::t('patient', 'Right') ?> : </td>
        <td><label class="text-info"> 
                <?php
                if (isset($dataRight['right_code']) && isset($dataAdmit['admit_id'])) {
                    $dataProfileFieldsPtRight = backend\modules\ezforms2\models\EzformFields::find()->where('ezf_id = :ezf_id AND ezf_field_name = :ezf_field_name', [':ezf_field_name' => 'right_code', ':ezf_id' => $ezfRight_id])->all();

                    echo \backend\modules\patient\classes\PatientFunc::getRefTableName($dataProfileFieldsPtRight, 'right_code', $dataRight['right_code']);
                }
                ?>
          </label></td>

        <td class="text-right"><?= Yii::t('patient', 'Admit Date') ?> : </td>
        <td><label class="text-info"><?php
                if ($dataAdmit['admit_date']) {
                    echo SDdate::mysql2phpThDateTime($dataAdmit['admit_date']);
                }
                ?></label></td>
      </tr>  
      <tr>                   
        <td class="text-right"><?= Yii::t('patient', 'Ward') ?> : </td>
        <td><label class="text-info"> <?= $dataAdmit['sect_name'] ?> </label></td>

        <td class="text-right"><?= Yii::t('patient', 'Bed') ?> : </td>
        <td><label class="text-info"><?= $dataAdmit['bed'] ?></label></td>
      </tr>  
    </tbody>
  </table>
</div>
<?php
if ($dataAdmit['admit_id']) {
    //$btn = PatientHelper::btnEditTxt('', $ezf_id, $dataAdmit['admit_id'], '', $reloadDiv, 'modal-ezform-main', 'btn-sm');
    $url = yii\helpers\Url::to(['/patient/admit/admit-btnadt-submit', 'dataid' => $dataAdmit['admit_id'], 'target' => $dataAdmit['visit_id'], 'reloadDiv' => $reloadDiv, 'action' => 'cancel']);
    $btn = ' ' . \yii\helpers\Html::button('<i class="fa fa-close"></i> ', ['class' => 'btn btn-danger btn-sm btn-cancel-admit', 'data-url' => $url, 'data-action' => 'cancel']);
} else {
    $btn = PatientHelper::btnAddTxt('', $ezf_id, $visit_id, ['admit_status' => '1', 'admit_doctor_user' => $userProfile['user_id'],
                'admit_date' => date('d-m-Y H:i'), 'admit_from_dept' => $userProfile['department']], $reloadDiv, 'modal-ezform-main', 'btn-sm');
}

$this->registerJS("
    $('#btn-right').html('$btn');
        
    $('.btn-cancel-admit').on('click', function(){
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
?>