<?php

use yii\helpers\Url;
use appxq\sdii\helpers\SDNoty;

$urlReport = Url::to(['/reports/report-checkup-send/print-report', 'visit_id' => $data['visit_id'], 'pt_hn' => $data['pt_hn']]);
\appxq\sdii\widgets\ModalForm::begin([
    'id' => 'modal-report-approve',
    'size' => 'modal-xxl',
    'tabindexEnable' => false,
]);
?>
<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
  <h3 class="modal-title" id="itemModalLabel">Apporve Report : <?= $data['fullname'] ?></h3>
</div>
<div class="modal-body"></div>
<div class="modal-footer">   
  <button type="submit" class="btn btn-primary btn-approve" name="submit" value="1" data-loading-text="Loading...">Approve</button>    
  <button type="button" class="btn btn-default" data-dismiss="modal">
    <i class="glyphicon glyphicon-remove"></i> ปิด
  </button>    
</div>
<?php
\appxq\sdii\widgets\ModalForm::end();

$urlSaveReport = Url::to(['/reports/report-checkup/report-save-approve', 'report_status' => '2', 'report_id' => $data['report_id']]);
$urlNextPt = Url::to(['/reports/report-checkup/next-pt', 'dataid' => $data['report_id'], 'ezm_id' => $ezm_id, 'que_type' => $que_type]);

echo yii\helpers\Html::tag('div', '', ['id' => 'report-next', 'data-url' => $urlNextPt]);
$this->registerJS("
    $('#modal-report-approve .modal-content .modal-body').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    $('#modal-report-approve').modal('show')
    $('#modal-report-approve .modal-content .modal-body').html('<iframe src=\"$urlReport\" width=\"99.6%\" height=\"600\" frameborder=\"0\"></iframe>');
    $('.btn-submit').removeAttr('disabled');
    $('.btn-approve').on('click',function(){
        $.get('$urlSaveReport').done(function(result) {
            " . SDNoty::show('result.message', 'result.status') . "
            getUiAjax('$urlNextPt', 'report-next');
	}).fail(function() {
            " . SDNoty::show("'" . \appxq\sdii\helpers\SDHtml::getMsgError() . "Server Error'", '"error"') . "
            console.log('server error');
	});
    });
    ");
