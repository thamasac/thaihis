<?php

use yii\widgets\ListView;
use backend\modules\ezforms2\classes\EzfStarterWidget;
use backend\modules\cpoe\classes\CpoeHelper;

\backend\modules\ezforms2\assets\ListdataAsset::register($this);
\backend\modules\cpoe\assets\CpoeAsset::register($this);
$this->title = Yii::t('app', 'CPOE');
EzfStarterWidget::begin();
?>
<section id="items-side" class="items-sidebar navbar-collapse collapse" role="complementary" style="margin-left: 45px;width: 220px;">
  <div class="sidebar-nav-title text-center">
      <?php
      echo \yii\helpers\Html::radioList('que_type', '1', ['1' => 'Inside', '2' => 'Que', '3' => 'All'], ['id' => 'que_type']);
      ?>
  </div>

  <div id="items-side-scroll" class="row">
    <div class="col-lg-12" id="que-list-view"> 
        <?= CpoeHelper::uiQue($pt_id, 'que-items'); ?>
    </div>
  </div>

  <div class="sidebar-nav-title text-center"><i class="fa fa-calendar"></i> <?= Yii::t('patient', 'Appointment Lists') ?></div>
  <div id="items-side-scroll2" class="row">
    <div class="col-lg-12">
        <?= CpoeHelper::uiAppoint('appoint-items'); ?>
    </div>
  </div>
</section>

<section id="items-views" role="complementary" style="margin-left: 218px">
  <div class="row">       
    <div class="col-md-12">
        <?php
        if ($pt_id) {
            echo CpoeHelper::uiCpoe($pt_id, $action, $action_id, $visit_type, $visit_tran_id, 'cpoe-content');
        } else {
            echo Yii::$app->controller->renderPartial('@backend/modules/patient/views/patient/_search', [
                'ezf_id' => $ezfProfile_id,
                'dataid' => '', 'fullname' => '',
                'reloadDiv' => 'cpoe-content', 'action' => 'cpoe',
            ]);
            ?>
          <h1 class="text-center" style="font-size: 45px; color: #ccc;margin: 200px 0;">
            <?= Yii::t('patient', 'Please choose patient') ?>
          </h1>
      <?php } ?>
    </div>
  </div>
</section>
<?php
EzfStarterWidget::end();
$url = yii\helpers\Url::to(['/cpoe/default/queue-view', 'ptid' => $pt_id, 'reloadDiv' => 'que-items']);
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
    itemsSidebar($('#items-side-scroll2'));

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
          height: sidebarHeight / 2,
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