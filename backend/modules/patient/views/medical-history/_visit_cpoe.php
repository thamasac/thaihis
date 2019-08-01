<div id="visit-side-scroll" class="row">
  <div class="col-lg-12">                                                       
    <div id="visit-items">
      <?php 

      echo \yii\widgets\ListView::widget([
          'id' => 'visit-list',
          'dataProvider' => $dataProvider,
          'itemOptions' => ['tag' => FALSE],
          'layout' => '<div class="list-group"><div class="list-group-item list-cpoe-header" style="background-color: #e5e5e5;padding-left: 5px;padding-right: 5px;">' .
          $this->render('_search', ['model' => $searchModel, 'reloadDiv' => $reloadDiv,
              'sitecode' => $sitecode, 'target' => $target,'options'=>$options,])
          . '</div>{items}</div><div class="list-pager">{pager}</div>',
          'itemView' => function ($model, $key, $index)use($target, $reloadChildDiv,$options,$modal) {
              return $this->render('_viewvisit_cpoe', [
                          'model' => $model,
                          'index' => $index,
                          'target' => $target,
                          'options'=> $options,
                          'reloadChildDiv' => $reloadChildDiv,
                          'modal'=>$modal,
              ]);
          },
          'showOnEmpty' => false,
          'emptyText' => '<div class="list-group-item list-cpoe-header" style="background-color: #e5e5e5;padding-left: 5px;padding-right: 5px;">' .
          $this->render('_search', ['model' => $searchModel, 'reloadDiv' => $reloadDiv,
              'sitecode' => $sitecode, 'target' => $target,'options'=>$options,]) .
          '</div><div class="list-group-item"><span><i class="fa"></i> ไม่พบผลลัพธ์</span></div>',
      ])
      ?>
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
    $('#visit-list .list-group').on('click', 'a', function () {
      $('#visit-list .list-group a').removeClass('active');
      $(this).addClass('active');
      var url = $(this).attr('href');
      if (url) {
        $.get(url).done(function (result) {
          $('#<?= $reloadChildDiv ?>').html(result);
        }).fail(function () {
          console.log('server error');
        });
      }
      return false;
    });
    
    $('#visit-list').on('click', '.pagination li a', function () { //Next
      var url = $(this).attr('href');
      getUiAjax(url, 'list-visit');
      return false;
    });

    itemsSidebar();

    function  itemsSidebar() {
      var itemside = $('#visit-side-scroll');
      
      if (itemside.height() >= 300) {          
        itemside.slimScroll({
          size: '7px',
          color: '#a1b2bd',
          opacity: .8,
          position: 'right',
          height: 300,
          allowPageScroll: false,
          disableFadeOut: false
        });
      } else {
        if (itemside.parent('.slimScrollDiv').length === 1) {
          itemside.slimScroll({
            destroy: true
          });
          itemside.removeAttr('style');
          $('.items-sidebar').removeAttr('style');
        }
      }
      
    }

</script>
<?php \richardfan\widget\JSRegister::end(); ?>