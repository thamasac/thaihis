<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$func_list = [
    'backend\modules\thaihis\classes\BackupFunc::getVisitAll($s, $e)' => 'zdata_visit',
    'backend\modules\thaihis\classes\BackupFunc::getVisitTran($s, $e)' => 'zdata_visit_tran',
    'backend\modules\thaihis\classes\BackupFunc::getOrderTran($s, $e)' => 'zdata_order_tran',
    'backend\modules\thaihis\classes\BackupFunc::getPe($s, $e)' => 'zdata_pe',
    'backend\modules\thaihis\classes\CNBackupFunc::backupZdata_Tk($s, $e)' => 'zdata_tk',
    'backend\modules\thaihis\classes\CNBackupFunc::backupZdata_Vs($s, $e)' => 'zdata_vs',
    'backend\modules\thaihis\classes\CNBackupFunc::backupZdata_Bmi($s, $e)' => 'zdata_bmi',
    'backend\modules\thaihis\classes\BackupFunc::getReportCyto($s, $e)' => 'zdata_reportcyto',
    'backend\modules\thaihis\classes\BackupFunc::getReportEkg($s, $e)' => 'zdata_reportekg',
    'backend\modules\thaihis\classes\BackupFunc::getReportXray($s, $e)' => 'zdata_reportxray',
    'backend\modules\thaihis\classes\BackupFunc::getReportCheckup($s, $e)' => 'zdata_reportcheckup',
    'backend\modules\thaihis\classes\BackupFunc::getPatientRight($s, $e)' => 'zdata_patientright',
    'backend\modules\thaihis\classes\BackupFunc::getAppoint($s, $e)' => 'zdata_appoint',
    'backend\modules\thaihis\classes\BackupFunc::getTreatMent($s, $e)' => 'zdata_treatment',
    'backend\modules\thaihis\classes\BackupFunc::getPatientHistory($s, $e)' => 'zdata_patienthistory',
    'backend\modules\thaihis\classes\BackupFunc::getReceiptMas($s, $e)' => 'zdata_receipt_mas',
    'backend\modules\thaihis\classes\BackupFunc::getReceiptTrn($s, $e)' => 'zdata_receipt_trn',
    'backend\modules\thaihis\classes\JKBackupFunc::getDiagnosis($s, $e)' => 'zdata_dt',
    'backend\modules\thaihis\classes\JKBackupFunc::getDiagnosisComo($s, $e)' => 'zdata_diag_como',
    'backend\modules\thaihis\classes\JKBackupFunc::getDiagnosisComp($s, $e)' => 'zdata_diag_comp',
    'backend\modules\thaihis\classes\JKBackupFunc::getOperation($s, $e)' => 'zdata_operat',
];
?>

<div class="row">
  <div class="row">
  <div class="col-md-6 col-md-offset-2">
    <ul class="nav nav-tabs">
  <li role="presentation" class="active"><a href="<?= yii\helpers\Url::to(['/thaihis/default/configbku']) ?>">Backup</a></li>
  <li role="presentation" ><a href="<?= yii\helpers\Url::to(['/thaihis/default/log-to-db']) ?>">Logs2DB</a></li>
</ul>
    <br>
  </div>
</div>
  
  <div class="col-md-6 col-md-offset-2">
      <?php
      $form = ActiveForm::begin([//post
                  'id' => 'config_bku',
                  'action' => Url::to('/thaihis/default/backup'),
      ]);
      ?>
    <div class="form-group">
      <label>Function (get data)</label>
      <?php
      echo \kartik\select2\Select2::widget([
          'name' => 'func',
          'data' => $func_list,
          'pluginOptions' => [
              'allowClear' => TRUE,
          ],
          'pluginEvents' => [
              //"select2:select" => "function(e) { $('#$inputProvinceID').val(e.params.data.id); $('#$inputAmphurID').val('');$('#$inputTumbonID').val(''); }",
              //"select2:unselect" => "function() { $('#$inputProvinceID').val(''); $('#$inputAmphurID').val('');$('#$inputTumbonID').val(''); }"
              "select2:select" => "function(e) { $('#table_name').val(e.params.data.text); }",
              "select2:unselect" => "function() { $('#table_name').val(''); }"
          ],
          'options' => ['id' => 'getdata_db', 'placeholder' => 'Function']
      ]);
      ?>
    </div>

    <div class="form-group">
      <label>Table name</label>
      <?= Html::textInput('table_name', '', ['class' => 'form-control', 'id' => 'table_name']); ?>
    </div>

    <div class="form-group">
      <label>Step</label>
      <?= Html::textInput('step', '2000', ['class' => 'form-control']); ?>
    </div>

    <div class="form-group">
      <label>initdata</label>
      <?= Html::textarea('initdata', '[]', ['class' => 'form-control']); ?>
    </div>

    <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-primary']) ?>
    <br><br>
    <?php ActiveForm::end(); ?>
  </div>

</div>



<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
   
</script>
<?php \richardfan\widget\JSRegister::end(); ?>