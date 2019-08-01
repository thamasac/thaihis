<?php

use backend\modules\patient\classes\PatientHelper;
use backend\modules\cpoe\classes\CpoeHelper;

\backend\modules\cpoe\assets\CpoeAsset::register($this);

$ezfPtProfile_id = \backend\modules\patient\Module::$formID['profile'];
$ezfAppoint_id = \backend\modules\patient\Module::$formID['appoint'];
?>
<div class="modal-body">
  <div class="row">     
    <div class="col-md-12">  
      <div class="col-md-7 sdbox-col">
        <!----------- start radt ----------->
        <?php
        echo CpoeHelper::uiRadt($pt_id, $visit_id, 'view-radt', TRUE);
        ?>
        <!----------- end radt ------------->
        <?php if (!empty($visit_type)) : ?>
            <!----------- start nurse note ----------->
            <?php
            if ($visit_type == '3') {
                echo PatientHelper::uiReferReceive('', $visit_id, 'view-rr');
            }
            if ($visit_type == '2') {
                echo PatientHelper::uiSOAP('', $visit_id, 'view-soap', TRUE);
            } elseif ($visit_type == '1' || $visit_type == '4' || $visit_type == '3') {
                echo PatientHelper::uiTK('', $visit_id, 'view-tk', '_tk', TRUE);
                echo PatientHelper::uiPE('', $visit_id, $pt_id, 'view-pe', '_pe', TRUE);
            }
            ?>   
            <!----------- end nurse note ----------->

            <!----------- start Diagenosis ----------->
            <?php
            echo PatientHelper::uiDI('', $visit_id, 'view-di');
            ?>
            <!----------- end Diagenosis ----------->

            <!----------- start order ----------->
            <?=
            Yii::$app->controller->renderAjax('@backend/modules/patient/views/order/_ordermain', ['ezf_id' => \backend\modules\patient\Module::$formID['order_tran'], 'target' => $visit_id, 'modal' => 'modal-ezform-main'
                , 'visit_type' => $visit_type, 'pt_id' => $pt_id, 'view' => 'cpoe', 'pt_hn' => $data['pt_hn'], 'btnDisabled' => TRUE]);
            ?>
            <!----------- end order ----------->
            <?php
        endif;
        ?>
      </div>
      <div class="col-md-5">
          <?php if (!empty($visit_type)) : ?>
            <!----------- start Vital Sign ----------->    
            <?= PatientHelper::uiVS('', $visit_id, 'view-vs', TRUE); ?>
            <?= PatientHelper::uiBMI('', $visit_id, 'view-bmi', TRUE); ?>
            <!----------- end Vital Sign ----------->
        <?php endif; ?>
        <!----------- start Medical History ----------->    
        <div class="card"> 
          <div class="card-block">
            <div class="row">
              <div class="col-md-4">
                  <?= PatientHelper::listVisit($pt_id, '12276', 'cpoe', 'list-visit'); ?>
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
        echo CpoeHelper::uiResultOrderCpoe($pt_id, $data['pt_hn'], $visit_id, 'view-result-order');
        ?>
      </div>
    </div>
  </div>
</div>
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