<?php 

\backend\modules\ezforms2\assets\ListdataAsset::register($this);
\backend\modules\cpoe\assets\CpoeAsset::register($this);
$id = \appxq\sdii\utils\SDUtility::getMillisecTime();

?>
<section id="items-side-<?= $id ?>" class="items-sidebar navbar-collapse collapse" role="complementary" style="margin-left: 5px;width: 100%; margin-top:10px; position: unset;">
    <div class="sidebar-nav-title text-center"><i class="fa fa-calendar"></i> <?= Yii::t('patient', 'Appointment Lists') ?></div>
    <div id="items-side-scroll-<?= $id ?>" class="row">
        <div class="col-lg-12" id="que-list-view">   
            <?=
            \yii\widgets\ListView::widget([
                'id' => 'appoint_list',
                'dataProvider' => $dataProviderAppoint,
                'itemOptions' => ['tag' => false],
                'layout' => '<div class="list-group">{items}</div><div class="list-pager">{pager}</div>',
                'itemView' => function ($model) {
                    return $this->render('_item_appoint', [
                                'model' => $model,
                    ]);
                },
            ]);
            ?>

        </div>
    </div>
</section>

<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);

?>

<script>
    /* $('#items-side').on('click', '.list-group .item', function () {
     $('#items-side .list-group a').removeClass('active');
     $(this).addClass('active');
     $('#que-list-view').attr('data-keyselect', $(this).attr('data-key'));
     var url = $(this).attr('href');
     if (url) {
     $.get(url, {reloadDiv: 'cpoe-content'}).done(function (result) {
     $('#items-views .cpoe-content').html(result);
     }).fail(function () {
     console.log('server error');
     });
     }
     
     return false;
     });*/

    /*$('body').removeClass('page-sidebar-fixed page-sidebar-closed');
     $('.page-content').removeClass('page-container');
     $('#main-nav-app').removeClass('page-container');
     $('#slide-collapse').remove();*/
    
    

    $('.footer').css('margin-left', '218px');
    itemsSidebar($('#items-side-scroll-<?=$id?>'));
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
      
      if ($(window).width() >= 350) {
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
          $('#items-side-<?=$id?>').removeAttr('style');
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