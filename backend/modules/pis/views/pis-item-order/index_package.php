<?php

use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use backend\modules\ezforms2\classes\EzfFunc;

$modal = "modal-" . $ezf_id;
?>
<div class="modal-body">
  <div class="row">
      <?php
      if ($action <> 'SELECT' && $mode == 'PACKAGE') :
          $options = EzfFunc::stringDecode2Array($options);
          $options['order_id'] = $item_dataid; //package_id เพื่องส่งไป init Package
          $options = EzfFunc::arrayEncode2String($options);
          ?>
        <div class="col-md-4" id="pis-order-tab">
          <div class="list-group-item list-cpoe-header" style="background-color: #e5e5e5;">
              <?=
              $this->render('_search_package_item', [
                  'itemGroup' => $itemGroup
                  , 'reloadDiv' => 'package-item-trad', 'options' => $options
              ]);
              ?>  
          </div>
          <?= backend\modules\pis\classes\PisHelper::uiOrderLists('', 'package-item-trad', $options, 'PACKAGE'); ?>
        </div>
        <div class="col-md-8">
            <?= backend\modules\pis\classes\PisHelper::uiPackageProfile($item_dataid, 'package-profile',$modal); ?>
            <?= backend\modules\pis\classes\PisHelper::uiPackageGridItem($item_dataid, 'package-item-order', $action, $mode); ?>
        </div>
        <?php
    else :
        ?>
        <div class="col-md-12"> 
            <?php
            if ($mode == 'PACKAGE') {
                echo backend\modules\pis\classes\PisHelper::uiPackageGridItem($item_dataid, 'package-item-order', $action, $mode);
            } elseif ($mode == 'VISIT') {
                echo backend\modules\pis\classes\PisHelper::uiPackageGridItem($item_dataid, 'visit-item-order', $action, $mode);
            }
            ?>
        </div>
    <?php
    endif;
    ?>
  </div>
</div>
<div class="modal-footer">
    <?php
    if ($action == 'SELECT') {
        $urlSelect = \yii\helpers\Url::to(['/pis/pis-item-order/package-add-order'
                    , 'item_dataid' => $item_dataid
                    , 'order_id' => $order_id
                    , 'right_code' => $right_code
                    , 'visit_id' => $visit_id, 'mode' => $mode]);
        ?>
      <button type="submit" class="btn btn-primary btn-select" name="submit" value="1" data-url="<?= $urlSelect ?>">
          <?= Yii::t('app', 'Select') ?>
      </button>    
  <?php } ?>
  <button type="button" class="btn btn-default" data-dismiss="modal">
    <i class="glyphicon glyphicon-remove"></i> <?= Yii::t('app', 'Close') ?>
  </button>
</div>
<?php
\richardfan\widget\JSRegister::begin([
//'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
    $('#ezf-fix-modal-box').append('<div id="<?= $modal ?>" class="fade modal" role="dialog"><div class="modal-dialog modal-xxl"><div class="modal-content"></div></div></div>');

    $('#package-item-trad').on('click', '.pagination li a', function () { //Next 
      var url = $(this).attr('href');
      getUiAjax(url, 'pis-item-trad');
      return false;
    });

    $('#package-item-trad').on('click', '.list-group-item', function () {
      let url = $(this).attr('data-url');
      let modal = $(this).attr('data-modal');
      let tmt_item = $(this).attr('data-tmt');
      let drugName = $(this).children('strong').text();
      let chkDupStatus = 1;

//      $('#package-item-order tbody tr').each(function () {
//        if (tmt_item === $(this).attr('data-tmt')) {
//          chkDupStatus = 2;
//          drugName = "พบว่ามีรายการ " + drugName + " แล้ว";
//        }
//      });

      if (chkDupStatus === 1) {
        modalEzformMain(url, modal);
      } else {
        if (chkDupStatus === 2) {
          bootbox.alert(drugName);
        }
      }
    });

    $('.btn-select').on('click', function () {

      let url = $(this).attr('data-url');
      $.get(url).done(function (result) {
        if (result.status == 'success') {
<?= SDNoty::show('result.message', 'result.status') ?>;
          $('#modal-<?= \backend\modules\patient\Module::$formID['pis_order_tran'] ?>').modal('hide');

          let url = $('#<?= $reloadDivOrder ?>').attr('data-url');
          getUiAjax(url, '<?= $reloadDivOrder ?>');
        }
      }).fail(function () {
<?= SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') ?>;
        console.log('server error');
      });

    });

</script>
<?php \richardfan\widget\JSRegister::end(); ?>
