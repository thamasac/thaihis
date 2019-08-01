<?php

use backend\modules\patient\classes\PatientHelper;

$url = yii\helpers\Url::to(['/cpoe/cpoe/radt', 'reloadDiv' => $reloadDiv, 'pt_id' => $pt_id,
            'visit_id' => $visit_id,]);
?>

<div class="card card-cpoe">
  <div class="card-header">
    <div class="pull-right" id="btn-right">

    </div>
    <ul class="nav nav-tabs card-header-tabs" id="radt-tab">
      <li id="R" role="presentation"><a href="<?= $url . '&view_type=R' ?>">Register</a></li>
      <li id="A" role="presentation"><a href="<?= $url . '&view_type=A' ?>">Admit</a></li>
      <li id="D" role="presentation"><a href="<?= $url . '&view_type=D' ?>">Discharge</a></li>
      <li id="T" role="presentation"><a href="<?= $url . '&view_type=T' ?>">Transfer</a></li>
    </ul>
  </div>
  <div class="card-block">
    <div id="view-radt-body">
      <div class="col-md-2" style="padding: 5px 5px">
          <?php
          echo PatientHelper::uiPatientPic($pt_id, 'pic-patient-cpoe', ['width' => '137px']);
          ?>
      </div>
      <div class="col-md-10">
        <?php
        if ($view_type == 'R') {
            echo PatientHelper::uiPatientCpoe($pt_id, 'view-patient-cpoe', null, $btnDisabled);
        } elseif ($view_type == 'A') {
            echo PatientHelper::uiAdmitCpoe($pt_id, $visit_id, 'view-admit-cpoe', $btnDisabled);
        } elseif ($view_type == 'D') {
            echo PatientHelper::uiDischargeShow($pt_id, $visit_id, 'cpoe', 'view-discharge-cpoe', $btnDisabled);
        } elseif ($view_type == 'T') {
            echo PatientHelper::uiTransferShow($pt_id, $visit_id, 'cpoe', 'view-transfer-cpoe', $btnDisabled);
        }
        ?>
      </div>           
    </div>
  </div>
</div>
<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
    $('#<?= $view_type ?>').attr('class', 'active');

    $('#radt-tab li a').on('click', function () {
      var url = $(this).attr('href');
      if (url) {
        $.get(url).done(function (result) {
          $('#<?= $reloadDiv ?>').html(result);
        }).fail(function () {
          console.log('server error');
        });
      }
      return false;
    });

</script>
<?php \richardfan\widget\JSRegister::end(); ?>