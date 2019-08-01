<?php

if ($dataCheckup) {
    echo \yii\helpers\Html::submitButton('<i class="glyphicon glyphicon-send"></i> ' . Yii::t('patient', 'Submit treatment')
            , ['class' => 'btn btn-warning btn-submit disable', 'name' => 'submit', 'value' => '1', 'disabled' => 'disabled']);

    $url = yii\helpers\Url::to(['/patient/restful/report-navi', 'dataid' => $pt_id, 'dept' => $dept, 'date' => date('Y-m-d'), 'visit_type' => '1']);
    $url_reload = yii\helpers\Url::to(['/patient/patient']);
    //$alert = \appxq\sdii\helpers\SDNoty::show('<strong><i class="glyphicon glyphicon-ok-sign"></i> Success!</strong> Save completed.', 'success');
    $alert = " var url = '$url';
            window.open(url, '_blank');
            //window.reload();
            window.location.href = '$url_reload';";
} else {
    $alert = \appxq\sdii\helpers\SDNoty::show("'" . appxq\sdii\helpers\SDHtml::getMsgError() . "Server Error'", '"error"');
}
$this->registerJS("
  $alert
  ");
?>