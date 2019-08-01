<div id="visit-side-scroll" class="row">
  <div class="col-lg-12">                                                       
    <div id="visit-items">
      <?=
      \yii\widgets\ListView::widget([
          'id' => 'visit-list',
          'dataProvider' => $dataProvider,
          'itemOptions' => ['tag' => FALSE],
          'layout' => '<div class="list-group"><div class="list-group-item list-cpoe-header" style="background-color: #e5e5e5;padding-left: 5px;padding-right: 5px;">' .
          $this->render('_visit_date_search', ['model' => $searchModel, 'reloadDiv' => $reloadDiv, 'pt_id' => $pt_id, 'dept' => $dept])
          . '</div>{items}</div><div class="list-pager">{pager}</div>',
          'itemView' => function ($model, $key, $index)use($pt_id) {
              return $this->render('_visit_date_item', [
                          'model' => $model,
                          'index' => $index,
                          'pt_id' => $pt_id,
              ]);
          },
          'showOnEmpty' => false,
          'emptyText' => '<div class="list-group-item list-cpoe-header" style="background-color: #e5e5e5;padding-left: 5px;padding-right: 5px;">' .
          $this->render('_visit_date_search', ['model' => $searchModel, 'reloadDiv' => $reloadDiv, 'pt_id' => $pt_id, 'dept' => $dept]) .
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