<?php

use backend\modules\patient\classes\PatientHelper;
use backend\modules\ezforms2\classes\EzfStarterWidget;
use backend\modules\cpoe\classes\CpoeHelper;

\backend\modules\ezforms2\assets\ListdataAsset::register($this);
\backend\modules\cpoe\assets\CpoeAsset::register($this);
$this->title = Yii::t('app', 'Report Checkup');
EzfStarterWidget::begin();
?>
<section id="items-side" class="items-sidebar navbar-collapse collapse" role="complementary" style="margin-left: 45px;width: 220px;">
  <div class="sidebar-nav-title text-center">
      <?php
      echo \yii\helpers\Html::radioList('que_type', $que_type, ['1' => 'CheckUp', '2' => 'Pap'], ['id' => 'que_type']);
      ?>
    <i class="fa fa-user-circle-o"></i> <?= Yii::t('patient', 'Patients Queue') ?></div>
  <div id="items-side-scroll" class="row">
    <div class="col-md-12" id="que-list-view">            
        <?php echo CpoeHelper::uiQueReportCheckup($pt_id, $report_status, $que_type, 'que-items', 'doctor', $page); ?>
    </div>
  </div>
</section>

<section id="items-views" role="complementary" style="margin-left: 218px">
  <div class="row">       
    <div class="col-md-8">
        <?php
        if ($pt_id) {
            if ($visit_id) {
                $initdata = [//'ckr_status' => '2', 
                    'ckr_doctor_check' => $data['visit_tran_doctor'], 'ckr_doctorverify' => $userProfile['user_id'], 'ckr_pt_id' => $pt_id, 'ckr_sum_bmi' => backend\modules\cpoe\controllers\ReportCheckupSendController::calBmi($data['bmi_bmi'])['value']
                    , 'report_date' => date('Y-m-d H:i:s')];
                $initdata = \backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String($initdata);
                echo CpoeHelper::uiReportCheckup($ezf_id, $data['report_id'], $initdata, 'report-checkup');
            } else {
                ?>
              <h1 class="text-center" style="font-size: 45px; color: #ccc;margin: 200px 0;">
                  <?= Yii::t('patient', 'Patient no data visit') ?>
              </h1>
              <?php
          }
      } else {
          ?>
          <h1 class="text-center" style="font-size: 45px; color: #ccc;margin: 200px 0;">
              <?= Yii::t('patient', 'Please choose patient') ?>
          </h1>
      <?php }
      ?>
    </div>
    <div class="col-md-4 sdbox-col">
      <?php
      if ($visit_id) {
          echo PatientHelper::uiVS('', $visit_id, 'report-checkup-vs', TRUE);
          echo PatientHelper::uiBMI('', $visit_id, 'report-checkup-bmi', TRUE);
          echo PatientHelper::uiTK('', $visit_id, 'report-checkup-tk', '_tk', TRUE);
          echo PatientHelper::uiPE('', $visit_id, $pt_id, 'report-checkup-pe', '_pe', FALSE);
          echo PatientHelper::uiDI('', $visit_id, 'report-checkup-di');
//          echo backend\modules\cpoe\classes\CpoeHelper::uiResultOrderCpoe($pt_id, $pt_hn, $visit_id, 'view-result-order');
      }
      ?>
    </div>
  </div>
</section>
<?php
EzfStarterWidget::end();
$url = yii\helpers\Url::to(['/cpoe/report-checkup/queue-view', 'pt_id' => $pt_id, 'report_status' => $report_status, 'reloadDiv' => 'que-list-view']);
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
    $('#que_type').on('change', function () {
      let que_type = $('#que_type [type="radio"]:checked').val();
      $.get('<?= $url ?>', {que_type: que_type}).done(function (result) {
        $('#que-list-view').html(result);
      }).fail(function () {
        console.log('server error');
      });
    });

    $('.footer').css('margin-left', '218px');
    itemsSidebar($('#items-side-scroll'));
    $('#main-nav-app .navbar-header').append('<a class="a-collapse glyphicon glyphicon-th-list navbar-toggle" data-toggle="collapse" data-target="#items-side">&nbsp;</a>');

    function  getHeight() {
      var sidebarHeight = $(window).height() - 51; //- $('.header').height()
      if ($('body').hasClass("page-footer-fixed")) {
        sidebarHeight = sidebarHeight - $('.footer').height();
      }
      return sidebarHeight;
    }

    function  itemsSidebar(id) {
      var itemside = id;

      if ($(window).width() >= 992) {
        var sidebarHeight = getHeight();

        itemside.slimScroll({
          size: '7px',
          color: '#a1b2bd',
          opacity: .8,
          position: 'right',
          height: sidebarHeight,
          //width: 250,
          allowPageScroll: false,
          disableFadeOut: false
        });
      } else {
        if (itemside.parent('.slimScrollDiv').length() === 1) {
          itemside.slimScroll({
            destroy: true
          });
          itemside.removeAttr('style');
          $('.items-sidebar').removeAttr('style');
        }
      }

    }

    $(document).on('hidden.bs.modal', '.modal', function (e) {
      var hasmodal = $('body .modal').hasClass('in');
      if (hasmodal) {
        $('body').addClass('modal-open');
      }
    });
</script>
<?php \richardfan\widget\JSRegister::end(); ?>