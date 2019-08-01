<?php

use appxq\sdii\utils\SDdate;
use backend\modules\patient\classes\PatientFunc;
use backend\modules\patient\classes\PatientHelper;

$img = ($dataProfile['pt_pic'] ? Yii::getAlias('@storageUrl/ezform/fileinput') . '/' . $dataProfile['pt_pic'] : Yii::getAlias('@storageUrl/images') . '/nouser.png');
?>
<div class="row">
  <div class="col-md-10">
      <?=
      ($IO == 'O' ? $this->render('_search', [
                  'ezf_id' => $ezf_id,
                  'dataid' => $dataProfile['pt_id'],
                  'fullname' => 'HN : ' . $dataProfile['pt_hn'] . ' ' . Yii::t('patient', 'Name') . ' : ' . $dataProfile['fullname'],
                  'reloadDiv' => $reloadDiv, 'tab' => '4', 'action' => 'reg']) : '');
      ?>
  </div>
  <div class="col-md-2 sdbox-col">
      <?php //echo PatientHelper::btnAppoint('', $dataProfile['pt_id'], 'btn-appoint', $dept) ?>
  </div>
</div>
<div class="row">
  <div class="col-md-3">
      <?= PatientHelper::uiPatientPic($dataProfile['pt_id'], 'pic-patient-emr', ['margin-bottom' => '5px']); ?>
      <?= ($IO == 'O' ? PatientHelper::uiBtnADT('', $target, 'view-btnADT', $IO) : ''); ?> 
  </div>
  <div class="col-md-9 sdbox-col">
    <table class="table" style="margin-bottom: 0px"> 
      <tbody>
        <tr>
          <td class="text-right col-md-3">HN : </td>
          <td class="col-md-3">
            <label class="text-info"><?= $dataProfile['pt_hn'] ?></label>
          </td>
          <td class="text-right col-md-3"><?= Yii::t('patient', 'Right') ?> : </td>
          <td class="col-md-3"><label class="text-info"> 
                  <?php
                  if (isset($dataRight['right_code'])) {
                      $dataProfileFieldsPtRight = backend\modules\ezforms2\models\EzformFields::find()->where('ezf_id = :ezf_id', [':ezf_id' => $ezfPtRight_id])->all();
                  }
                  ?>
              <?= (isset($dataRight['right_code']) ? PatientFunc::getRefTableName($dataProfileFieldsPtRight, 'right_code', $dataRight['right_code']) : '') ?></label>
          </td>
        </tr>                      
        <tr>
          <td class="text-right col-md-3"><?= Yii::t('patient', 'Name') ?> : </td>
          <td colspan="3"><label class="text-info"><?= $dataProfile['fullname']; ?></label></td>
        </tr>
        <tr>
          <td class="text-right col-md-3"><?= Yii::t('patient', 'Birthday') ?> : </td>
          <td class="col-md-3"><label class="text-info"><?php
                  if ($dataProfile['pt_bdate']) {
                      try {
                          echo SDdate::mysql2phpThDateSmall(SDdate::dateTh2bod($dataProfile['pt_bdate']));
                      } catch (Exception $ex) {
                          $bdate = PatientFunc::integeter2date($dataProfile['pt_bdate']);
                          echo SDdate::mysql2phpThDate(SDdate::dateTh2bod($bdate));
                      }
                  }
                  ?></label></td>
          <td class="text-right col-md-3"><?= Yii::t('patient', 'Age') ?> : </td>
          <td class="col-md-3"><label class="text-info"><?php
                  if ($dataProfile['pt_bdate']) {
                      try {
                          echo SDdate::getAge(SDdate::dateTh2bod($dataProfile['pt_bdate'])) . ' ปี';
                      } catch (Exception $ex) {
                          $bdate = PatientFunc::integeter2date($dataProfile['pt_bdate']);
                          echo SDdate::getAge(SDdate::dateTh2bod($bdate)) . ' ปี';
                      }
                  }
                  ?></label></td>
        </tr>
        <tr>
          <td class="text-right col-md-3"><?= Yii::t('patient', 'Admit Date') ?> : </td>
          <td class="col-md-3">
            <label class="text-info"><?php
                if (isset($dataAdmit['admit_date'])) {
                    echo SDdate::mysql2phpThDateSmall($dataAdmit['admit_date']);
                }
                ?></label>
          </td>
          <td class="text-right col-md-3"><?= Yii::t('patient', 'Amount') ?> : </td>
          <td class="col-md-3">
            <label class="text-info"><?php
              if (isset($dataAdmit['admit_amount'])) {
                  echo $dataAdmit['admit_amount'] . ' วัน';
              }
              ?></label>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>