<?php

use backend\modules\patient\classes\PatientHelper;
?>
<div class="card card-cpoe">
  <div class="card-header">
    <div class="row">
      <div class="col-md-6">
          <i class="fa fa-address-card-o"></i> <?= Yii::t('patient', 'SOAP note') ?> 
      </div>
      <div class="col-md-6 text-right">
          <?php
          if (!$btnDisabled) {
              echo (isset($model['id']) ? PatientHelper::btnEditTxt('', $ezf_id, $model['id'], [], $reloadDiv, 'modal-ezform-main', 'btn-sm') : PatientHelper::btnAddTxt('', $ezf_id, $target, [], $reloadDiv, 'modal-ezform-main', 'btn-sm'));
          }
          ?>
      </div>
    </div>

  </div>
  <div class="card-block">
    <div class="row">
      <div class="col-md-6">
        <strong> S : </strong> 
        <?= (isset($model['id']) ? $model['fu_s'] : ''); ?>
      </div>
      <div class="col-md-6">
        <strong> A : </strong>
        <?= (isset($model['id']) ? $model['fu_a'] : ''); ?>
      </div>
    </div>
    <div class="row ">
      <div class="col-md-6">
        <strong> O : </strong>
        <?= (isset($model['id']) ? $model['fu_o'] : ''); ?>
      </div> 
      <div class="col-md-6">
        <strong> P : </strong>
        <?= (isset($model['id']) ? $model['fu_p'] : ''); ?>
      </div>
    </div>       
  </div>
</div>