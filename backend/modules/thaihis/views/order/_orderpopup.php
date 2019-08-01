<?php

use yii\helpers\Url;
use backend\modules\thaihis\classes\ThaiHisHelper;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
?>
<div class="modal-header" style="background-color: #fff">
  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
  <h3 class="modal-title" id="itemModalLabel">Order Lists <small></small></h3>
</div>
<div class="modal-body">
  <div class="row">
    <div class="col-md-5">       
      <div class="list-group-item list-cpoe-header" style="background-color: #e5e5e5;">
          <?=
          $this->render('_searchorderlist', ['model' => $searchModel, 'visit_id' => $visit_id, 'reloadDiv' => 'item-lists'
              , 'options' => $options, 'filterOrderType' => isset($options['filterOrderType']) ? ['enable' => $options['filterOrderType'], 'ezf_id' => $options['ordertype_ezf_id']] : 0]);
          ?>
      </div>
      <div id="order-item-lists">
          <?= ThaiHisHelper::uiOrderList($visit_id, 'item-lists', $options) ?>
      </div>      
    </div>
    <div class="col-md-7">
      <?php
      if (isset($options['oipd_type']) && $options['oipd_type'] == "IPD") {
          $options['fix_grid_ipd_date'] = date('Y-m-d');
          $options['fix_grid_ipd'] = TRUE;
      }
      $id = \appxq\sdii\utils\SDUtility::getMillisecTime();
      echo ThaiHisHelper::uiGridOrder($ezf_id, $visit_id, 'grid-order-tran' . $id, $btnDisabled, $options);
      ?>   
    </div>
  </div>
</div>
<?php
$options = \backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String($options);
$urlSelect = Url::to(['/thaihis/order/order-select', 'ezf_id' => $ezf_id, 'order_header_id' => $order_header_id, 'visit_type' => $visit_type, 'pt_id' => $pt_id, 'visit_date' => $visit_date, 'options' => $options]);
$urlreload = Url::to(['/thaihis/order/grid-order', 'ezf_id' => $ezf_id, 'visitid' => $visit_id, 'reloadDiv' => 'grid-order-tran' . $id, 'options' => $options]);
$this->registerJs("   
    $('#item-lists').on('click', '.pagination li a', function () { //Next 
      var url = $(this).attr('href');
      getUiAjax(url, 'item-lists');
      return false;
    });

    $('#order-item-lists').on('click','#item-lists .list-group-item', function(e) {
        var item_code = $(this).attr('data-code');
        var item_name = $(this).attr('data-name');
        var grouptype = $(this).attr('data-type');
        var status = 1;
        let item_list = this;
        
        $('#grid-order-tran{$id} tbody tr').each(function () {
            if (item_code === $(this).attr('data-code')) {
                item_name = 'มีการสั่ง ' + item_name + ' แล้วยืนยันการสั่งซ้ำ หรือไม่ ?';
                
                //krajeeDialog.alert(item_name);
                status = 2;
                //return;
            }
        });

        if(item_code && status===1){
            addItem(item_list,item_code,grouptype);
        }else if(status === 2){
            bootbox.confirm(item_name, function (result) {
              if (result) { // ok button was pressed
                addItem(item_list,item_code,grouptype);
              }
            });
        }
    });
    
    function addItem(item_list,item_code,grouptype){
        $.get('$urlSelect',{item:item_code,grouptype:grouptype}).done(function(result) {
            " . SDNoty::show('result.message', 'result.status') . "
            $.ajax({
                method: 'POST',
                url: '$urlreload',
                dataType: 'HTML',
                success: function(result, textStatus) {
                    $('#grid-order-tran{$id}').html(result);
                    $(item_list).remove();
                }
            });
        }).fail(function() {
            " . SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') . "
            console.log('server error');
        });
    }
");
?>
