<?php

use backend\modules\patient\classes\PatientHelper;
use appxq\sdii\widgets\ModalForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;

\backend\modules\patient\assets\PatientAsset::register($this);
?>
<div class="row" style="margin-top: 15px;">
    <?php if ($dataVisit) : ?>
      <div class="col-md-6">    
        <div class="col-md-12">
            <?= PatientHelper::uiPatientShow($pt_id, $dataVisit['visit_id'], 'view-patient-show', 'O'); ?>
        </div>  
        <div class="col-md-12" style="margin-top: 5px;">
            <?= Yii::$app->controller->renderPartial('/order/_ordermain', ['ezf_id' => \backend\modules\patient\Module::$formID['order_tran'], 'target' => $dataVisit['visit_id'], 'modal' => 'modal-ezform-main']); ?>
        </div>           
      </div>
      <div class="col-md-6 sdbox-col">
        <div class="col-md-6"> 
            <?= PatientHelper::uiVS('', $dataVisit['visit_id'], 'view-vs'); ?>
        </div>  
        <div class="col-md-6 sdbox-col">
            <?= PatientHelper::uiBMI('', $dataVisit['visit_id'], 'view-bmi'); ?>
        </div> 
        <?php if ($dataVisit['visit_type'] == '2') : ?>
            <div class="col-md-12">
                <?= PatientHelper::uiSOAP('', $dataVisit['visit_id'], 'view-soap'); ?>
            </div>
            <div class="col-md-12">
                <?= PatientHelper::uiDI('', $dataVisit['visit_id'], 'view-di', 'EMR'); ?>
            </div> 
        <?php elseif ($dept == 'S048') : ?>
            <div class="col-md-12">
                <?= PatientHelper::uiReferReceive('', $dataVisit['visit_id'], 'view-rr'); ?>
            </div>
        <?php elseif ($dataVisit['visit_type'] == '1' || $dataVisit['visit_type'] == '4' || $dataVisit['visit_type'] == '3') : ?>
            <div class="col-md-12">
                <?= PatientHelper::uiTK('', $dataVisit['visit_id'], 'view-tk'); ?>
            </div>
            <div class="col-md-12">
                <?= PatientHelper::uiPE('', $dataVisit['visit_id'], 'view-pe'); ?>
            </div>
            <div class="col-md-12">
                <?= PatientHelper::uiDI('', $dataVisit['visit_id'], 'view-di', 'EMR'); ?>
            </div>            
        <?php endif; ?>     
      </div>
  <?php else : ?>
      <div class="col-md-6">
        <?= PatientHelper::uiPatientShow($pt_id, $dataVisit['visit_id'], 'view-patient-show', 'O'); ?>
      </div>  
  <?php endif; ?>
</div>
<?php
$jsAddon = '';
if (empty($dataVisit)) {
    $dataProfile = \backend\modules\patient\classes\PatientQuery::getPtProfile($pt_id);
    $url = \yii\helpers\Url::to(['/patient/patient/counter-visit', 'ptid' => $pt_id, 'cid' => $dataProfile['pt_cid'], 'dept' => $dept,]);
    $jsAddon = "
        var txtConfirm = '<strong>ต้องการรับเข้า ผู้รับบริการรายนี้ ?</strong>';
        yii.confirm(txtConfirm, function () {
            $.get('$url').done(function(result) {
             " . SDNoty::show('result.message', 'result.status') . "
                 location.reload();
            }).fail(function() {
            " . SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') . "
            console.log('server error');
            });
        });
    ";
}

$this->registerJs(" 
    $jsAddon
        
    function getUiAjax(url, divid) {
        $.ajax({
            method: 'POST',
            url: url,
            dataType: 'HTML',
            success: function (result, textStatus) {
                $('#' + divid).html(result);
            }
        });
    }
");

?>