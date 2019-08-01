<?php

use backend\modules\patient\classes\PatientHelper;
use backend\modules\cpoe\classes\CpoeHelper;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;

$ezfPtProfile_id = \backend\modules\patient\Module::$formID['profile'];
$ezfAppoint_id = \backend\modules\patient\Module::$formID['appoint'];
?>
<div class="col-md-7 sdbox-col">
  <!----------- start search ----------->
  <div class="col-md-12" style="padding: 0px 0px;">
      <?=
      Yii::$app->controller->renderAjax('@backend/modules/patient/views/patient/_search', [
          'ezf_id' => $ezfPtProfile_id,
          'dataid' => $pt_id,
          'fullname' => 'HN : ' . $data['pt_hn'] . ' ' . Yii::t('patient', 'Name') . ' : ' . $data['fullname'],
          'reloadDiv' => $reloadDiv, 'action' => 'cpoe',
      ]);
      ?>    
  </div>    
  <!----------- end search ------------->

  <!----------- start radt ----------->
  <?php
  echo CpoeHelper::uiRadt($pt_id, $action_id, 'view-radt');
  ?>
  <!----------- end radt ------------->
  <?php if (!empty($visit_type)) : ?>
      <!----------- start nurse note ----------->
      <?php
      if ($visit_type == '3') {
          echo PatientHelper::uiReferReceive('', $action_id, 'view-rr');
      }
      if ($visit_type == '2') {
          echo PatientHelper::uiSOAP('', $action_id, 'view-soap');
      } elseif ($visit_type == '1' || $visit_type == '4' || $visit_type == '3') {
          echo PatientHelper::uiTK('', $action_id, 'view-tk');
          echo PatientHelper::uiPE('', $action_id, $pt_id, 'view-pe');
      }
      ?>   
      <!----------- end nurse note ----------->

      <!----------- start Diagenosis ----------->
      <?php
      echo PatientHelper::uiDI('', $action_id, 'view-di');
      echo PatientHelper::uiTreatment('', $action_id, 'view-treatment');
      ?>
      <!----------- end Diagenosis ----------->

      <!----------- start order ----------->
      <?=
      Yii::$app->controller->renderAjax('@backend/modules/patient/views/order/_ordermain', ['ezf_id' => \backend\modules\patient\Module::$formID['order_tran'], 'target' => $action_id, 'modal' => 'modal-ezform-main'
          , 'visit_type' => $visit_type, 'pt_id' => $pt_id, 'view' => 'cpoe', 'pt_hn' => $data['pt_hn']]);
      ?>
      <!----------- end order ----------->
  <?php elseif (!empty($action_id)) :
      echo PatientHelper::uiAppoint($action_id, '', 'view-appoint');
  endif;
  ?>
</div>
<div class="col-md-5 sdbox-col">
  <div class="col-md-12 form-group" style="padding: 0px 0px;">
    <div class="col-md-4 sdbox-col">
        <?= PatientHelper::btnAppoint('', $pt_id, 'btn-appoint', $userProfile['department']) ?>
    </div>
    <div class="col-md-4 sdbox-col">
        <?php
        if ($visit_tran_id) {
            echo PatientHelper::btnCloseVisit($visit_tran_id, $visit_type, $userProfile['position'], 'btn-close-visit');
        }
        ?>
    </div>
    <div class="col-md-4 sdbox-col"> 
        <?= PatientHelper::btnCertificate('', $pt_id, 'btn-certificate', $userProfile['department']) ?>
    </div>
  </div>
</div>
<?php if (!empty($visit_type)) : ?>
    <!----------- start Vital Sign ----------->    
    <?= PatientHelper::uiVS('', $action_id, 'view-vs'); ?>
    <?= PatientHelper::uiBMI('', $action_id, 'view-bmi'); ?>
    <!----------- end Vital Sign ----------->
<?php endif; ?>
<!----------- start Medical History ----------->    
<div class="card"> 
  <div class="card-block">
    <div class="row">
      <div class="col-md-4">
          <?= PatientHelper::listVisit($pt_id, $sitecode, 'cpoe', 'list-visit'); ?>
      </div>
      <div class="col-md-8 sdbox-col">
        <div id="view-detail">

        </div>
      </div>
    </div>
  </div>
</div>
<!----------- end  Medical History ----------->
<!-----------start Result Order -------------> 
<?php
echo CpoeHelper::uiResultOrderCpoe($pt_id, $data['pt_hn'], $action_id, 'view-result-order');
?>
<!-----------end Result Order --------------->
</div>
<?php
if (empty($visit_type)) {
    $url = \yii\helpers\Url::to(['/patient/patient/counter-visit', 'ptid' => $pt_id, 'cid' => $data['pt_cid'],
                'dept' => $userProfile['department'], 'appointid' => $action_id, 'visit_tran_new' => $close_visit]);

    if ($close_visit) {
        $jsAddon = "var txtConfirm = '<strong>ต้องการรับเข้า ผู้มารับบริการรายนี้ ?</strong>'";
    } else {
        $jsAddon = "var txtConfirm = '<strong>ต้องการรับเข้า ผู้มารับบริการรายนี้ ?</strong>'+
        '<div><strong>" . Yii::t('patient', 'Service') . "</<strong>'+
        '  <label class=\"radio-inline\"><input type=\"radio\" name=\"visit_type\" value=\"2\"> <span>" . Yii::t('patient', 'Follow up') . "  </span></label>'+
        '  <label class=\"radio-inline\"><input type=\"radio\" name=\"visit_type\" value=\"3\"> <span>" . Yii::t('patient', 'Refer') . "  </span></label>'+
        '  <label class=\"radio-inline\"><input type=\"radio\" name=\"visit_type\" value=\"4\" checked> <span>" . Yii::t('patient', 'Treatment') . "  </span> </label>'+
        '</div>'";
    }

    $this->registerJS("          
        $jsAddon
        yii.confirm(txtConfirm, function () {
            var visit_type = $('input[name=\"visit_type\"]:checked').val();
            $.get('$url',{visit_type:visit_type}).done(function(result) {
             " . SDNoty::show('result.message', 'result.status') . "
                location.reload();
            }).fail(function() {
            " . SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') . "
            console.log('server error');
            });
        });
    ");
}
?>