<?php

use appxq\sdii\utils\SDdate;
use backend\modules\patient\classes\PatientFunc;
use backend\modules\patient\classes\PatientHelper;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use yii\helpers\Url;

$img = ($dataProfile['pt_pic'] ? Yii::getAlias('@storageUrl/ezform/fileinput') . '/' . $dataProfile['pt_pic'] : Yii::getAlias('@storageUrl/images') . '/nouser.png');
?>
<div class="row" style="margin-top: 5px">
  <div class="col-md-2"></div>
  <div class="col-md-7">
      <?=
      $this->render('_search', [
          'ezf_id' => $ezf_id,
          'dataid' => $dataid,
          'fullname' => 'HN : ' . $dataProfile['pt_hn'] . ' ' . Yii::t('patient', 'Name') . ' : ' . $dataProfile['fullname'],
          'reloadDiv' => $reloadDiv, 'action' => 'reg'])
      ?>
  </div>
  <div class="col-md-3"><?= (isset($dataid) ? PatientHelper::btnEdit($ezf_id, $dataid, '', $reloadDiv) : '') . ' ' . PatientHelper::btnAddTxt(Yii::t('patient', 'Add Patient'), $ezf_id, '', [], $reloadDiv); ?></div>      
</div>
<div class="row h4">
  <div class="col-md-2">
    <img class="img-responsive img-rounded" src=<?= $img ?> alt="patient-pic" style="width:100%">
  </div>
  <div class="col-md-7">
    <table class="table"> 
      <tbody>
        <tr>
          <td class="info" colspan="2"><?= Yii::t('patient', 'Personal information') ?></td>
        </tr>
        <tr>
          <td class="col-md-3 text-right"><?= Yii::t('patient', 'Citizen ID') ?> : </td>
          <td class="col-md-9"><label class="text-info"><?= $dataProfile['pt_cid'] ?></label></td>
        </tr>
        <tr>
          <td class="text-right"><?= Yii::t('patient', 'Name') ?> : </td>
          <td><label class="text-info"><?= $dataProfile['fullname']; ?></label></td>
        </tr>
        <tr>
          <td class="text-right"><?= Yii::t('patient', 'Birthday') . ' - ' . Yii::t('patient', 'Age') ?> : </td>
          <td><label class="text-info"><?php
                  if ($dataProfile['pt_bdate']) {
                      try {
                          echo SDdate::mysql2phpThDate(SDdate::dateTh2bod($dataProfile['pt_bdate'])) . ' อายุ ' . SDdate::getAge(SDdate::dateTh2bod($dataProfile['pt_bdate'])) . ' ปี';
                      } catch (Exception $ex) {
                          try {
                              $bdate = PatientFunc::integeter2date($dataProfile['pt_bdate']);
                              echo SDdate::mysql2phpThDate(SDdate::dateTh2bod($bdate)) . ' อายุ ' . SDdate::getAge(SDdate::dateTh2bod($bdate)) . ' ปี';
                          } catch (Exception $ex) {
                              echo 'ระบุ พุทธศักราช เกิดไม่ถูกต้องกรุณาระบุใหม่';
                          }
                      }
                  }
                  ?></label></td>
        </tr>
        <tr>
          <td class="text-right"><?= Yii::t('patient', 'Address') ?> : </td>
          <td><label class="text-info"><?php
                  if ($dataProfile['pt_id']) {
                      echo PatientFunc::getFulladdress($dataProfile);
                  }
                  ?></label></td>
        </tr>
      </tbody>
    </table>
  </div>
  <div class="col-md-3">
    <table class="table"> 
      <tbody>
        <tr><td class="info text-center"><?= Yii::t('patient', 'Service') ?></td></tr>
        <tr><td>
                <?php
                if (empty($dataProfile['pt_admit_an']) && $modelVisit) {
                    $url = Url::to(['/patient/patient/submit-visit', 'ezf_id' => $ezfVisit_id, 'dataid' => $modelVisit['id'], 'target' => $dataid, 'cid' => $dataProfile['pt_cid']]);
                    $form = ActiveForm::begin(['id' => 'form-visit', 'action' => $url]);
                    ?>
                    <?php
                    echo $form->field($modelVisit, 'visit_type')->radioList([
                        1 => Yii::t('patient', 'Checkup'),
                        2 => Yii::t('patient', 'Follow up'),
                        3 => Yii::t('patient', 'Refer'),
                        4 => Yii::t('patient', 'Treatment'),
                    ])->label(FALSE);
//                    $btnDisabled = ($dataProfile['pt_id'] ? '' : '');
                    $url = Url::to(['/patient/patient/submit-visit-checkup', 'visit_id' => $modelVisit['id'], 'pt_id' => $dataid, 'cid' => $dataProfile['pt_cid']]);
                    ?>
                <div id ="reloadCheckUp" data-url="<?= $url ?>">               
                    <?php
                    echo Html::submitButton('<i class="glyphicon glyphicon-send"></i> ' . Yii::t('patient', 'Submit treatment')
                            , ['class' => 'btn btn-warning btn-submit', 'name' => 'submit', 'value' => '1', 'disabled' => '',]);
                    ?>
                </div>
                <?php
                ActiveForm::end();
            } elseif (isset($dataProfile['pt_admit_an'])) {
                ?>
                <label class="text-warning"> <i class="fa fa-bed"></i> ผู้รับบริการ ถูกสั่งพักรักษา</label>
            <?php } ?>
          </td></tr>
      </tbody>
    </table>
  </div>
</div>
<div class="row h4">
  <div class="col-md-2"></div>
  <div class="col-md-7">
    <table class="table"> 
      <tbody>
        <tr>
          <td class="info" colspan="2"><?= Yii::t('patient', 'Treatment') ?></td>
        </tr>
        <tr>
          <td class="col-md-3 text-right"><?= Yii::t('patient', 'Congenital disease') ?> : </td>
          <td class="col-md-9"><label class="text-info"> ไม่มี</label></td>
        </tr>
        <tr>
          <td class="text-right"><?= Yii::t('patient', 'Drug identification') ?> : </td>
          <td><label class="text-info"> ไม่มี </label></td>
        </tr>
        <tr>
          <td class="text-right"><?= Yii::t('patient', 'Allergic') ?> : </td>
          <td><label class="text-info"> ไม่มี </label></td>
        </tr>
        <tr>
          <td class="text-right"><?= Yii::t('patient', 'Blood') ?> : </td>
          <td><label class="text-info"> - </label></td>
        </tr>
        <tr>
          <td class="text-right info"><?= Yii::t('patient', 'Right') ?> : </td>
          <td class="info">
            <label class="text-info">
                <?php
                if ($dataRight['connect'] == 'ok') {
                    echo $dataRight['maininscl_name'];
                } elseif ($dataRight['connect'] == 'error') {
                    echo 'ไม่สามารถเชื่อต่อระบบตรวจสอบสิทธิได้';
                }
                ?>
            </label>
          </td>
        </tr>
        <tr>
          <td class="text-right info">
              <?php
              $appchk_right = '2';
              if ($project_id) {
                  $appchk_right = '1';
              } elseif ($dataRight['maininscl'] == 'LGO' || $dataRight['maininscl'] == 'OFC') {
                  if ($dataRight['maininscl'] == 'LGO' && $dataRight['subinscl'] == 'L1') {
                      $appchk_right = '3';
                  } elseif (in_array($dataRight['subinscl'], ['E1', 'E2'])) {
                      $appchk_right = '4';
                  } elseif ($dataRight['maininscl'] == 'LGO') {
                      $appchk_right = '2';
                  } elseif ($dataRight['maininscl'] == 'OFC' && $dataRight['subinscl'] == 'O4') {
                      $appchk_right = '2';
                  } elseif ($dataRight['maininscl'] == 'OFC' && $dataRight['subinscl'] == 'O3') {
                      $appchk_right = '5';
                  } elseif ($dataRight['maininscl'] == 'OFC') {
                      $appchk_right = '5';
                  }
                  echo Yii::t('patient', 'Right sub');
              } elseif ($dataRight['maininscl'] == 'PVT') {
                  $appchk_right = '5';
              } else {
                  echo Yii::t('patient', 'Right Hospital');
              }
              ?> 
            : </td>
          <td class="info">
            <label class="text-info">
                <?php
                if ($dataRight['maininscl'] == 'LGO' || $dataRight['maininscl'] == 'OFC') {
                    echo $dataRight['subinscl_name'];
                } else {
                    echo (isset($dataRight['hmain_name']) ? $dataRight['hmain_name'] : '');
                }
                ?>
            </label>
          </td>           
        </tr>
      </tbody>
    </table>
  </div>
  <div class="col-md-3">
    <table class="table"> 
      <tbody>              
        <tr><td class="info text-center"><?= Yii::t('patient', 'Authentication') ?></td></tr>
         <!--   <tr><td class="text-center"><label><?= Yii::t('patient', 'OTP Password') ?> <input type="password" name="otp_pass" value="1234" class="form-control"></label></td></tr>
          <tr><td class="text-center"><label><?= Yii::t('patient', 'Finger print') ?></label></td></tr>
          <tr><td><img style="width: 80px;height: 100px;" class="img-responsive center-block" src="https://static1-velaeasy.readyplanet.com/www.barcode-dd.com/images/content/original-1414158598529.jpg" alt="patient-pic"></td></tr>-->                
      </tbody>
    </table>
  </div>
</div>
<?php
$url = Url::to(['/patient/restful/report-navi', 'dataid' => $dataid]);
if (isset($dataCheckUp['id'])) {
    $appchk['appchk_status'] = '2';
    $appchk['app_chk_pt_id'] = $dataid;
} else {
    $appchk['appchk_status'] = '1';
    $appchk['appchk_right'] = $appchk_right;
    $appchk['app_chk_pt_id'] = $dataid;
    $appchk['appchk_project_id'] = $project_id;
}
$initdata = \backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String($appchk);
$this->registerJs("        
    $('#$reloadDiv').attr('data-dataid','$dataid');            
        
    $('form#form-visit').on('beforeSubmit', function(e) {             
        if($('#form-visit input[type=\"radio\"]:checked').val() === '1'){
            formCheckUp();
            return false;
        }
        
        $('#form-visit .btn-submit').attr('disabled', true);        
        var url = $('#form-visit').attr('action');
        $.post(url,$('#form-visit').serialize()).done(function(result) {
        if(result.status === 'error'){
         $('#form-visit .btn-submit').removeAttr('disabled'); 
        }
             " . SDNoty::show('result.message', 'result.status') . "   
                 
        if(result.status === 'success'){
            var url = '$url'+'&dept='+result.data.visit_tran_dept+'&date=" . date('Y-m-d') . "';            
             myWindow = window.open(url, '_blank');
             myWindow.focus();
             myWindow.print();
        }                  
        }).fail(function() {
            " . SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') . "
            console.log('server error');
        });
        return false;
    });
    
    function formCheckUp(){
       var url = '" . Url::to(['/ezforms2/ezform-data/ezform', 'ezf_id' => '1511490170071641200',
            'modal' => 'modal-ezform-main', 'reloadDiv' => 'reloadCheckUp', 'dataid' => isset($dataCheckUp['id']) ? $dataCheckUp['id'] : '',
            'initdata' => $initdata, 'target' => $modelVisit['id']]) . "';
            
	modalEzformMain(url,'modal-ezform-main'); 
    }
    
    function modalEzformMain(url, modal) {
        $('#'+modal+' .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
        $('#'+modal).modal('show')
        .find('.modal-content').load(url);
    }
    
    $('#form-visit input[type=\"radio\"]').on('change',function(){
        $('#reloadCheckUp button').removeAttr('disabled');
    });
");
?>