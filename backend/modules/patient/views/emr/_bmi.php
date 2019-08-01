<?php

use backend\modules\patient\classes\PatientHelper;
?>
<div class="card card-cpoe">
  <div class="card-header">
    <div class="row">
      <div class="col-md-6">
        <i class="fa fa-stethoscope"></i> <?= Yii::t('patient', 'Body Mass Index (BMI)') ?>
      </div>
      <div class="col-md-6 text-right">
        <div>
            <?php
            if (!$btnDisabled) {
                echo (isset($model['id']) ? PatientHelper::btnEditTxt('', $ezf_id, $model['id'], [], $reloadDiv, 'modal-ezform-main', 'btn-sm') : '')
                . ' ' . PatientHelper::btnAddTxt('', $ezf_id, $target, [], $reloadDiv, 'modal-ezform-main', 'btn-sm')
                . ' ' . PatientHelper::btnViewTxt('', $ezf_id, $target, ['bmi_bw', 'bmi_ht', 'bmi_bmi', 'bmi_bsa'], 'modal-ezform-main', 'btn-sm');
            }
            ?>
        </div>
      </div>
    </div>

  </div>
  <div class="card-block">
    <div class="row">
      <div class="col-md-4">
        <strong>BW : </strong><span class="text-info"><?= (isset($model['id']) ? $model['bmi_bw'] : ''); ?> </span>
      </div>
      <div class="col-md-4 sdbox-col">
        <strong>HT : </strong><span class="text-info"><?= (isset($model['id']) ? $model['bmi_ht'] : ''); ?></span>
      </div>
      <div class="col-md-4 sdbox-col">
        <strong>รอบเอว : </strong><span class="text-info"><?= (isset($model['id']) ? $model['bmi_waistline'] : ''); ?></span>
      </div>
      <div class="col-md-4">
        <strong>BMI : </strong><span class="text-info"><?= (isset($model['id']) ? number_format($model['bmi_bmi'], 2) : ''); ?></span>
      </div>
      <div class="col-md-4 sdbox-col">
        <strong>BSA : </strong><span class="text-info"><?= (isset($model['id']) ? $model['bmi_bsa'] : ''); ?></span>
      </div>
    </div>
  </div>
</div>



