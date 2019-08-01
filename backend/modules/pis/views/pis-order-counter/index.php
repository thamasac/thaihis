<?php

use backend\modules\ezforms2\classes\EzfStarterWidget;

\backend\modules\ezforms2\assets\ListdataAsset::register($this);
\backend\modules\cpoe\assets\CpoeAsset::register($this);
$this->title = Yii::t('app', 'PIS Counter');
EzfStarterWidget::begin();
?>
<section id="items-side" class="items-sidebar navbar-collapse collapse" role="complementary" style="margin-left: 45px;width: 220px;">
  <div id="items-side-scroll" class="row">
    <div class="col-md-12">
      <div class=" sidebar-nav-title" ><?= Yii::t('patient', 'Patients Queue') ?></div>
      <?= backend\modules\pis\classes\PisHelper::uiOrderQue($order_id, 'que-order'); ?>
    </div>   
  </div>
</section>

<section id="items-views" role="complementary" style="margin-left: 218px">
  <div class="row">       
    <div class="col-md-12" id="view-order-counter">
      <h1 class="text-center" style="font-size: 45px; color: #ccc;margin: 200px 0;">
        <?= Yii::t('patient', 'Please choose patient') ?>
      </h1>
    </div>
  </div>
</section>
<?php
EzfStarterWidget::end();

\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
//    $('#ezf-modal-box').append('<div id="modal-<?= $ezf_id ?>" class="fade modal" role="dialog"><div class="modal-dialog modal-xxl"><div class="modal-content"></div></div></div>');
    
    $('#que-order').on('click', '.list-group a', function () {
      $('#que-order .list-group a').removeClass('active');
      $(this).addClass('active');
      var url = $(this).attr('href');
      getUiAjax(url, 'view-order-counter');
      $('#view-order-counter').attr('data-url', url);
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