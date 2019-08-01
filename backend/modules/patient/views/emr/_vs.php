<?php

use backend\modules\patient\classes\PatientHelper;
?>
<div class="card card-cpoe">
  <div class="card-header">
    <div class="row">
      <div class="col-md-6">
        <i class="fa fa-heartbeat"></i> <?= Yii::t('patient', 'Vital Sign') ?> 
      </div>
      <div class="col-md-6 text-right">
        <div>
            <?php
            if (!$btnDisabled) {
                echo (isset($model['id']) ? PatientHelper::btnEditTxt('', $ezf_id, $model['id'], [], $reloadDiv, 'modal-ezform-main', 'btn-sm') : '')
                . ' ' . PatientHelper::btnAddTxt('', $ezf_id, $target, [], $reloadDiv, 'modal-ezform-main', 'btn-sm')
                . ' ' . PatientHelper::btnViewTxt('', $ezf_id, $target, ['vs_bp_squeeze', 'vs_bp_loosen', 'vs_pulse', 'vs_respiratory', 'vs_temperature'], 'modal-ezform-main', 'btn-sm');
            }
            ?></div>
      </div>
    </div>
  </div>
  <div class="card-block">
    <div class="row">
      <div class="col-md-6">
        <strong>BP : </strong><span class="text-info"><?= (isset($model['id']) ? $model['vs_bp_squeeze'] . ' / ' . $model['vs_bp_loosen'] : ''); ?></span>
      </div>
      <div class="col-md-6 sdbox-col">
        <strong>P : </strong><span class="text-info"><?= (isset($model['id']) ? $model['vs_pulse'] : ''); ?></span>
      </div>
      <div class="col-md-6">
        <strong>R : </strong><span class="text-info"><?= (isset($model['id']) ? $model['vs_respiratory'] : ''); ?></span>
      </div>
      <div class="col-md-6 sdbox-col">
        <strong>T : </strong><span class="text-info"><?= (isset($model['id']) ? $model['vs_temperature'] : ''); ?></span>
      </div>
    </div>
  </div>
</div>



