<?php

use backend\modules\patient\classes\PatientHelper;

$url = \yii\helpers\Url::to(['/patient/medical-history/show-detail', 'dataid' => $dataid, 'visitid' => $visit_id,
            'visittype' => $visit_type, 'visitdate' => $visit_date]);

yii\bootstrap\Modal::begin([
    'header' => '<h3 class="modal-title" id="itemModalLabel">Medical History </h3>',
    'footer' => \yii\helpers\Html::button('<i class="glyphicon glyphicon-remove"></i> ' . Yii::t('app', 'Close'), ['class' => 'btn btn-default', 'data-dismiss' => 'modal']),
    'id' => 'modal-history-cpoe',
    'size' => 'modal-xxl',
]);
?>
<div class="row">
  <div class="col-md-12">

  </div>
</div>
<?php
yii\bootstrap\Modal::end();
?>

<button type="button" class="btn btn-block btn-outline-info" data-url="<?= $url ?>" data-modal="modal-history-cpoe" onclick="modalMedicalHistory('<?= $url ?>', 'modal-history-cpoe')">
  <strong>Electronic Medical Record</strong>
</button>
<?= PatientHelper::uiTK('', $visit_id, 'view-tk-cpoe', '_tk_cpoe'); ?>

<?= PatientHelper::uiPE('', $visit_id, $dataid, 'view-pe-cpoe', '_pe_cpoe'); ?>

<?= PatientHelper::uiDICpoe('', $visit_id, 'view-di-cpoe'); ?>

<!--<dl class="dl-horizontal">
  <dt style="width: 35px;">Rx:</dt>
  <dd style="margin-left: 40px;">

  </dd>
</dl>-->
<?= PatientHelper::uiEmrDoctor($visit_id, 'view-doctor-treat-emr', 'emr'); ?>
<?php
$this->registerJS("
    function modalMedicalHistory(url, modal) {
            $('#'+modal+' .modal-body .col-md-12').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
            $('#'+modal).modal('show')
            .find('.modal-body .col-md-12')
            .load(url);
        }
        
    $('#modal-history-cpoe').on('hidden.bs.modal', function (e) {
        $('#modal-history-cpoe .modal-body .col-md-12').html(' ');
    });
    ");
?>