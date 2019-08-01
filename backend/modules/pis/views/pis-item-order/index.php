<?php

use kartik\dialog\Dialog;

// widget with default options
echo Dialog::widget();

$options = \backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String($options);

$urlPackage = \yii\helpers\Url::to(['/pis/pis-item-order/package-lists', 'user_id' => $user_id
            , 'visit_id' => $visit_id
            , 'order_id' => $model['id']
            , 'options' => $options
            , 'right_code' => $dataRight['right_code']
        ]);

$urlRemed = \yii\helpers\Url::to(['/pis/pis-item-order/order-history', 'ptid' => $ptid
            , 'visit_id' => $visit_id
            , 'order_id' => $model['id']
            , 'options' => $options
            , 'right_code' => $dataRight['right_code'], 'q' => '']);
?>
<div class="modal-body">
  <div class="row">    
    <div class="col-md-4" id="pis-order-tab">
      <ul class="nav nav-tabs" role="tablist" style="margin-bottom: 15px;border-bottom: 1px solid #ddd;">
        <li role="presentation" class="active">
          <a href="#order-tab" aria-controls="order" role="tab" data-toggle="tab">Order</a>
        </li>
        <li role="presentation">
          <a href="#package-tab" aria-controls="package" role="tab" data-toggle="tab" data-reload="package-tab-item" data-url="<?= $urlPackage ?>">Package</a>
        </li>    
        <li role="presentation">
          <a href="#remed-tab" aria-controls="remed" role="tab" data-toggle="tab" data-reload="remed-tab-item" data-url="<?= $urlRemed ?>">Re Med</a>
        </li>    
      </ul>
      <!-- Tab panes -->
      <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="order-tab">
          <div class="list-group-item list-cpoe-header" style="background-color: #e5e5e5;">            
                  <?=
                  $this->render('_searchorderlist', ['model' => $searchModel
                      , 'itemGroup' => $itemGroup
                      , 'reloadDiv' => 'pis-item-trad'
                      , 'options' => $options
                      , 'right_code' => $dataRight['right_code'], 'ptid' => $model['ptid']]);
                  ?>  
            </div>
            <?php echo backend\modules\pis\classes\PisHelper::uiOrderLists($dataRight['right_code'], 'pis-item-trad', $options, 'ORDER',$ptid); ?>
          </div>

          <div role="tabpanel" class="tab-pane" id="package-tab">
            <div class="list-group-item list-cpoe-header" style="background-color: #e5e5e5;">
              <div class="row">
                <div class="col-md-10">
                    <?php
                    echo $this->render('_search_package', [
                        'reloadDiv' => 'package-list'
                        , 'visit_id' => $visit_id
                        , 'order_id' => $model['id']
                        , 'user_id' => $user_id
                        , 'options' => $options
                        , 'right_code' => $dataRight['right_code']]);
                    ?>
                </div>
                <div class="col-md-2 sdbox-col">
                    <?php
                    $ezf_id = backend\modules\patient\Module::$formID['pis_package'];
                    echo backend\modules\ezforms2\classes\BtnBuilder::btn()
                            ->ezf_id($ezf_id)
                            ->label('<i class="glyphicon glyphicon-plus"></i>')
                            ->reloadDiv('add-package-item')
                            ->modal('modal-' . $ezfOrderTran_id)
                            ->buildBtnAdd();
                    //แก้ไขการ reload หลังกด add
                    $urlAddPackageItem = \yii\helpers\Url::to(['/pis/pis-item-order/open-package-show-items',
                                //'item_dataid' => $model['id']//package_id
                                'visit_id' => $visit_id
                                , 'right_code' => $dataRight['right_code']
                                , 'options' => $options
                                , 'order_id' => $model['id']
                                , 'mode' => 'PACKAGE'
                                , 'action' => 'EDIT'
                    ]);
                    ?>
                  <div id="add-package-item" data-url="<?= $urlAddPackageItem ?>"></div>
                </div> 
              </div>
            </div>
            <div id="package-tab-item">package</div>
          </div>

          <div role="tabpanel" class="tab-pane" id="remed-tab">
            <div class="list-group-item list-cpoe-header" style="background-color: #e5e5e5;">
              <div class="row">
                <div class="col-md-12">
                    <?php
                    echo $this->render('_search_history', [
                        'reloadDiv' => 'remed-tab-item'
                        , 'visit_id' => $visit_id
                        , 'order_id' => $model['id']
                        , 'ptid' => $ptid
                        , 'options' => $options
                        , 'right_code' => $dataRight['right_code']]);
                    ?>
                </div>
              </div>
            </div>
            <div id="remed-tab-item">re-med</div>
          </div>
        </div>                 
      </div>
      <div class="col-md-8">    
        <?php echo backend\modules\pis\classes\PisHelper::uiGridOrder($visit_id, 'pis-item-order', $options, $dataRight['right_code']); ?>
      </div>
    </div>
  </div>
  <?php
  \richardfan\widget\JSRegister::begin([
      //'key' => 'bootstrap-modal',
      'position' => \yii\web\View::POS_READY
  ]);
  $urlDrugAll = yii\helpers\Url::to(['/pis/pis-item/drug-allergy']);
  ?>
  <script>
      $('#ezf-fix-modal-box').append('<div id="modal-<?= $ezfOrderTran_id ?>" class="fade modal" role="dialog"><div class="modal-dialog modal-xxl"><div class="modal-content"></div></div></div>');

      $('#pis-item-trad').on('click', '.pagination li a', function () { //Next 
        var url = $(this).attr('href');
        getUiAjax(url, 'pis-item-trad');
        return false;
      });

      function chkDrugAllergy(params) {
        let status = '';

        $.ajax({
          method: 'GET',
          url: '<?= $urlDrugAll ?>',
          data: {params: params},
          dataType: 'JSON',
          async: false,
          success: function (result) {
            status = result.all_name;
          }
        });

        return status;
      }

      $('#pis-item-trad').on('click', '.list-group-item', function () {
        let tmt_item = $(this).attr('data-tmt');
        let url = $(this).attr('data-url');
        let modal = $(this).attr('data-modal');
        let chkDupStatus = 1;
        let drugName = $(this).children('strong').text();
        let txtDrugAllergy = chkDrugAllergy($(this).attr('data-allergy'));

        if (txtDrugAllergy) {
          chkDupStatus = 3;
          drugName = "พบข้อมูลแพ้ " + drugName + " ยืนยันการสั่งหรือไม่ ?";
        }
//ห้องยา ให้แก้ไขให้สั่งยาซ้ำได้กรณี 2 วิธีใช้ยาตัวเดียวกัน ByOak
//        $('#pis-item-order tbody tr').each(function () {
//          if (tmt_item === $(this).attr('data-tmt')) {
//            chkDupStatus = 2;
//            drugName = "พบว่ามีรายการ " + drugName + " แล้ว";
//          }
//        });

        if (chkDupStatus === 1) {
          modalEzformMain(url, modal);
        } else {
          if (chkDupStatus === 2) {
            bootbox.alert(drugName);
          } else if (chkDupStatus === 3) {
            bootbox.confirm(drugName, function (result) {
              if (result) { // ok button was pressed
                modalEzformMain(url, modal);
              }
            });
          }
        }
      });

      function getUiAjax(url, reloadDiv) {
        var div = $('#' + reloadDiv);
        div.html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
        $.get(url, function (result) {
          div.empty();
          div.html(result);
        });
      }

      function modalEzformMain(url, modal) {
        $('#' + modal + ' .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
        $('#' + modal).modal('show')
                .find('.modal-content')
                .load(url);
      }
      //next tab 
      $('#pis-order-tab a').on('click', function () {
        let url = $(this).attr('data-url');
        let reload = $(this).attr('data-reload');
        if (url) {
          getUiAjax(url, reload);
        }
      });
      //select visit date
      $('#remed-tab-item').on('click', '.list-group-item .click-package-item', function () {
        let url = $(this).parent().attr('data-url');
        if (url) {
          modalEzformMain(url, 'modal-<?= $ezfOrderTran_id ?>');
        }
      });
      //select show package
      $('#package-tab-item').on('click', '.list-group-item .click-package-item', function () {
        let url = $(this).parent().attr('data-url');
        if (url) {
          modalEzformMain(url, 'modal-<?= $ezfOrderTran_id ?>');
        }
      });
      //edit package
      $('#package-tab-item').on('click', '.btn-package-edit', function () {
        let url = $(this).attr('data-url');
        if (url) {
          modalEzformMain(url, 'modal-<?= $ezfOrderTran_id ?>');
        }
      });
      //select add,edit item package
      $('#package-item-trad').on('click', '.list-group-item', function () {
        let url = $(this).attr('data-url');
        let modal = $(this).attr('data-modal');

        modalEzformMain(url, modal);
      });

  </script>
  <?php \richardfan\widget\JSRegister::end(); ?>
