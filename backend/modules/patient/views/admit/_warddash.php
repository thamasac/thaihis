<div class="row"> 
  <?php
    $admit_status = backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String(["1", "2"]);
    $url = \yii\helpers\Url::to(['/patient/admit/ward-ptadmit', 'admit_status' => $admit_status, 'bed_status' => '1', 'dept' => $dept, 'reloadDiv' => $reloadDiv, 'mode' => 2]);
    ?>
    <div class="col-md-3">
      <div class="alert-patient alert-patient-secondary ezform-main-open" data-url="<?= $url ?>" data-modal="modal-ezform-main-lg" style="cursor: pointer;" >
           <strong style="font-size: 20px"><code> <i class="fa fa-download"></i> ผู้ป่วยรอรับเข้า <?= $count_admit['Cpadmit'] ?> คน </code></strong>
        </div>
    </div>
  
</div>
<div class="row"> 
    <?php
    $admit_status2 = backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String(["3", "2"]);
    $url2 = \yii\helpers\Url::to(['/patient/admit/ward-ptadmit', 'admit_status' => $admit_status2, 'bed_status' => '2', 'dept' => $dept, 'reloadDiv' => $reloadDiv, 'mode' => 1, 'module' => $module, 'tab' => $tab]);
  ?>
    <div class="col-md-3">
        <div class="alert-patient alert-patient-info ezform-main-open" data-url="<?= $url2 ?>" data-modal="modal-ezform-main-lg" style="cursor: pointer;">
            <i class="fa fa-bed"></i> <strong> จำนวนผู้ป่วยทั้งหมด <?= $count_admit['Cadmit'] ?> คน</strong>
        </div>
    </div>
    <?php
    $admit_status3 = backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String(["3", "2"]);
    $url3 = \yii\helpers\Url::to(['/patient/admit/ward-ptadmit', 'admit_status' => $admit_status3, 'bed_status' => '2' , 'bed_type' => '1' , 'dept' => $dept, 'reloadDiv' => $reloadDiv, 'mode' => 1, 'module' => $module, 'tab' => $tab]);
  ?>
    <div class="col-md-3">
        <div class="alert-patient alert-patient-danger ezform-main-open" data-url="<?= $url3 ?>" data-modal="modal-ezform-main-lg" style="cursor: pointer;">
            <i class="fa fa-warning"></i> <strong> ผู้ป่วยเฝ้าระวัง <?= $count_admit['cp_alert'] ?> คน</strong>
        </div>
    </div>
  <?php
    $admit_status4 = backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String(["3", "2"]);
    $url4 = \yii\helpers\Url::to(['/patient/admit/ward-ptadmit', 'admit_status' => $admit_status4, 'bed_status' => '2', 'bed_type' => '2' , 'dept' => $dept, 'reloadDiv' => $reloadDiv, 'mode' => 1, 'module' => $module, 'tab' => $tab]);
  ?>
    <div class="col-md-3">
        <div class="alert-patient alert-patient-primary ezform-main-open" data-url="<?= $url4 ?>" data-modal="modal-ezform-main-lg" style="cursor: pointer;">
            <i class="fa fa-download"></i> <strong> ผู้ป่วยเตียงเสริม <?= $count_admit['cp_addons'] ?> คน</strong>
        </div>
    </div>
  <?php
    $admit_status5 = backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String(["3"]);
    $url5 = \yii\helpers\Url::to(['/patient/admit/ward-ptadmit', 'admit_status' => $admit_status5, 'bed_status' => '2', 'dept' => $dept, 'reloadDiv' => $reloadDiv, 'mode' => 1, 'module' => $module, 'tab' => $tab]);
  ?>
    <div class="col-md-3">
        <div class="alert-patient alert-patient-warning ezform-main-open" data-url="<?= $url5 ?>" data-modal="modal-ezform-main-lg" style="cursor: pointer;">
            <i class="fa fa-share"></i> <strong> ผู้ป่วย Pre-Discharge <?= $count_admit['Cpdis'] ?> คน</strong>
        </div>
    </div>
    
</div>