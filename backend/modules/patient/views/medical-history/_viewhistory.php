<?php

use backend\modules\patient\classes\PatientHelper;
?>
<div class = "col-md-6">
    <?= PatientHelper::uiVS('', $visit_id, 'view-vs-history', TRUE); ?>
</div>
<div class = "col-md-6 sdbox-col">
    <?= PatientHelper::uiBMI('', $visit_id, 'view-bmi-history', TRUE); ?>
</div>
<?php
if (in_array($visit_type, ['1', '3', '4'])) {
    ?>
    <div class = "col-md-6">
        <?= PatientHelper::uiTK('', $visit_id, 'view-tk-history', '_tk', TRUE); ?>
    </div>
    <div class="col-md-6 sdbox-col">
        <?= PatientHelper::uiPE('', $visit_id, $dataid, 'view-pe-history', '_pe', TRUE); ?>
    </div>   
    <div class="col-md-12">
        <?= backend\modules\cpoe\classes\CpoeHelper::uiResultOrderCpoe($dataid, $pt_hn, $visit_id, 'view-result-order'); ?>
    </div> 
    <div class="col-md-12">
        <?= PatientHelper::uiDI('', $visit_id, 'view-di-history', TRUE); ?>
    </div>
    <?php
} elseif ($visit_type == '2' && empty($visit_an)) {
    ?>
    <div class="col-md-12">
        <?= PatientHelper::uiSOAP('', $visit_id, 'view-soap-history', TRUE); ?>
    </div>
    <div class="col-md-12">
        <?= backend\modules\cpoe\classes\CpoeHelper::uiResultOrderCpoe($dataid, $pt_hn, $visit_id, 'view-result-order'); ?>
      <div class="col-md-12">
          <?= PatientHelper::uiDI('', $visit_id, 'view-di-history', TRUE); ?>
      </div>
    </div>
<?php } elseif (isset($visit_an)) { ?>
    <div class="col-md-12">
        <?= PatientHelper::uiDischargeShow($dataid, $visit_id, 'emr', 'view-discharge-history'); ?>
    </div>
<?php } ?>
<div class="col-md-12">
  <?= PatientHelper::uiTreatment('', $visit_id, 'view-treatment-history', TRUE); ?>
</div>
<?=
Yii::$app->controller->renderAjax('@backend/modules/patient/views/order/_ordermain', ['ezf_id' => \backend\modules\patient\Module::$formID['order_tran']
    , 'target' => $visit_id, 'modal' => 'modal-ezform-main', 'visit_type' => $visit_type, 'pt_hn' => $pt_hn
    , 'pt_id' => $dataid, 'view' => 'emr', 'btnDisabled' => TRUE, 'visit_date' => $visit_date]);
?>

<?php
$this->registerJS("
    function getUiAjax(url, divid) {
        $.ajax({
            method: 'POST',
            url: url,
            dataType: 'HTML',
            success: function(result, textStatus) {
                $('#'+divid).html(result);
            }
        });
    }
    ");
?>