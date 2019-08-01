<?php

use backend\modules\ezforms2\classes\EzfStarterWidget;
use backend\modules\patient\classes\Order2Helper;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;

\backend\modules\ezforms2\assets\ListdataAsset::register($this);
\backend\modules\cpoe\assets\CpoeAsset::register($this);
$this->title = Yii::t('app', 'Order Lists');
EzfStarterWidget::begin();
?>
<section id="items-side" class="items-sidebar navbar-collapse collapse" role="complementary" style="margin-left: 45px;width: 220px;">
  <div class=" sidebar-nav-title" ><?= Yii::t('patient', 'Patients Queue') ?></div>

  <div id="items-side-scroll" class="row">
    <div class="col-lg-12" id="que-list-view"> 
        <?= Order2Helper::uiQue('que-items'); ?>
    </div>
  </div>  
</section>

<section id="items-views" role="complementary" style="margin-left: 218px">
  <div class="row" id="order-content">       
    <h1 class="text-center" style="font-size: 45px; color: #ccc;margin: 200px 0;">
      <?= Yii::t('patient', 'Please choose patient') ?>
    </h1>
  </div>
</section>
<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
    $('#order-content').on('click', '#visit-list .list-group a', function () {
      $('#visit-list .list-group a').removeClass('active');
      $(this).addClass('active');
      var url = $(this).attr('href');
      if (url) {
        var modal = $(this).attr('data-modal');
        modalEzformMain(url, modal);
      }
      return false;
    });

    $('#que-items').on('click', '#que-list a', function () {
      $('#que-items .list-group a').removeClass('active');
      $(this).addClass('active');
      let order_status = $('#ez1504537671028647300-order_tran_status').val();
      let url = $(this).attr('href');
      $('#order-content').html('');
      $.get(url, {order_status: order_status}).done(function (result) {
        $('#order-content').html(result);
      });
      return false;
    });

    $('#order-content').on('submit', '#order-receive', function () {
      var url = $(this).attr('action');
      $.post(url, $(this).serialize()).done(function (result) {
        <?= SDNoty::show('result.message', 'result.status') ?>
        actionGet();
        $('#order-content').html('<h1 class="text-center" style="font-size: 45px; color: #ccc;margin: 200px 0;"><?= Yii::t('patient', 'Please choose patient') ?>');
      }).fail(function () {
        <?= SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') ?>
        console.log('server error');
      });
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
<?php
\richardfan\widget\JSRegister::end();
EzfStarterWidget::end();
?>