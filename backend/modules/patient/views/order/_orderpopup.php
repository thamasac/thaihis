<?php

use yii\helpers\Html;
use yii\helpers\Url;
use backend\modules\patient\classes\PatientHelper;
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
          <?= $this->render('_searchorderlist', ['model' => $searchModel, 'target' => $target, 'reloadDiv' => 'item-lists',]); ?>
      </div>
      <div id="order-item-lists">
          <?= PatientHelper::uiOrderList($target, 'item-lists') ?>
      </div>      
    </div>
    <div class="col-md-7">
      <?= PatientHelper::uiGridOrder($ezf_id, $target, 'grid-order-tran') ?>
    </div>
  </div>
</div>
<?php
$urlSelect = Url::to(['/patient/order/order-select', 'ezf_id' => $ezf_id, 'target' => $target, 'visit_type' => $visit_type, 'pt_id' => $pt_id]);
$urlreload = Url::to(['/patient/order/grid-order', 'ezf_id' => $ezf_id, 'target' => $target, 'reloadDiv' => 'grid-order-tran']);
$this->registerJs("   
    $('#order-item-lists').on('click','#item-lists .list-group-item', function(e) {
        var item = $(this).attr('data-code');
        var grouptype = $(this).attr('data-type');
        if(item){
            $(this).remove();
            $.get('$urlSelect',{item:item,grouptype:grouptype}).done(function(result) {
             " . SDNoty::show('result.message', 'result.status') . "
                 //getUiAjax('$urlreload', 'grid-order-tran');  
                 $.ajax({
                    method: 'POST',
                    url: '$urlreload',
                    dataType: 'HTML',
                    success: function(result, textStatus) {
                        $('#grid-order-tran').html(result);
                    }
                });
            }).fail(function() {
            " . SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') . "
            console.log('server error');
            });
        }
    });
");
?>
