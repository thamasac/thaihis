<?php

use yii\helpers\Url;
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
      echo \yii\helpers\Html::radioList('que_type', '1', ['1' => 'CheckUp', '2' => 'Pap'], ['id' => 'que_type']);
      ?>
    <i class="fa fa-user-circle-o"></i> <?= Yii::t('patient', 'Patients Queue') ?></div>
  <div id="items-side-scroll" class="row">
    <div class="col-md-12" id="que-list-view">            
        <?php echo CpoeHelper::uiQueReportCheckup($target, $report_status, '', 'que-items', 'r2d',1,$module_id); ?>
    </div>
  </div>
</section>

<section id="items-views" role="complementary">
  <div class="row" id="senddoc-view">

  </div>
</section>
<?php
EzfStarterWidget::end();
$urlQue = Url::to(['/reports/report-checkup/que-view-r2d', 'report_status' => $report_status, 'reloadDiv' => 'que-items']);

$url = Url::to(['/reports/report-checkup/report-to-doc-view']);
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
    $(function(){
       var screenWidth = $('.sdbox');
       screenWidth.css('margin-left','230px');
    });
    $('#que_type').on('change', function () {
      let que_type = $('#que_type [type="radio"]:checked').val();
      $.get('<?= $urlQue ?>', {que_type: que_type}).done(function (result) {
        $('#que-list-view').html(result);
      }).fail(function () {
        console.log('server error');
      });
    });

    $('#senddoc-view').on('submit', '#form-doc-lists', function () {
      let que_type = $('#que_type [type="radio"]:checked').val();
      let url = $(this).attr('action') + '&que_type=' + que_type;
      $.post(url, $(this).serialize()).done(function (result) {
        $('#senddoc-view').html(result);
      });
      
      $.get('<?= $urlQue ?>', {que_type: que_type}).done(function (result) {
        $('#que-list-view').html(result);
      }).fail(function () {
        console.log('server error');
      });
      $('#senddoc-view').html('result');
      return false;
    });

    $('#que-list-view').on('click', '#que-list a', function () {
      $('#que-list-view .list-group a').removeClass('active');
      $(this).addClass('active');
      let que_type = $('#que_type [type="radio"]:checked').val();
      let visit_id = $(this).attr('data-visit');
      if (visit_id) {
        $.get('<?= $url ?>', {visit_id: visit_id, que_type: que_type}).done(function (result) {
          $('#senddoc-view').html(result);
        });
      }
      return false;
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
