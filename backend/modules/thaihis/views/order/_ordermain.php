<?php

use backend\modules\patient\classes\PatientHelper;
use yii\helpers\Url;
use appxq\sdii\widgets\ModalForm;

$user = $user_id = Yii::$app->user->identity->username;
$modal = (isset($modal) ? $modal : 'modal-order-xxl');
$btnDisabled = isset($btnDisabled) ? $btnDisabled : 0;
$pt_hn = (isset($pt_hn) ? $pt_hn : '');
$visit_date = (isset($visit_date) ? $visit_date : date('Y-m-d'));
?>
<div class="card card-cpoe card-order-<?= $view ?>">
  <div class="card-header">   
      <?php if ($btnDisabled <> 1) : ?>
        <div class="pull-right">
          <button id="btn-order-add" type="button" class="btn btn-success btn-sm pull-right ezform-main-open" data-modal="modal-ezform-main" data-url="">
            <i class="glyphicon glyphicon-plus"></i>
          </button>
        </div>
    <?php endif; ?>
    <ul class="nav nav-tabs card-header-tabs" role="tablist" data-url="">
      <li role="presentation">       
          <?php $url = Url::to(['/pis/pis-item-order', 'visit_id' => $target]) ?>
        <a href="#tab_drug-<?= $view ?>" data-reload="view-drug-<?= $view ?>" aria-controls="tab_drug" role="tab" 
           data-toggle="tab" data-urlbtn="<?= $url ?>" data-url="<?= Url::to(['/pis/pis-item-order/grid-order', 'visit_id' => $target, 'page' => 'ordermain', 'reloadDiv' => 'view-drug-' . $view]) ?>">
               <?= Yii::t('patient', 'Drug prescribe') ?>
        </a>
      </li>
      <li role="presentation" class="active">
          <?php $url = Url::to(['/patient/order/orderpopup', 'target' => $target, 'reloadDiv' => '', 'visit_type' => $visit_type, 'pt_id' => $pt_id]) ?>
        <a href="#tab_order-<?= $view ?>" data-reload="view-order-<?= $view ?>" aria-controls="tab_order" role="tab" data-toggle="tab" data-urlbtn="<?= $url ?>" data-url="<?= Url::to(['/patient/order/grid-order', 'ezf_id' => $ezf_id, 'target' => $target, 'reloadDiv' => 'view-order-' . $view]) ?>"><?= Yii::t('patient', "Doctor's order") ?></a></li>
      <li role="presentation">
        <a href="#tab_invest-<?= $view ?>" data-reload="view-invest-<?= $view ?>" aria-controls="tab_invest" role="tab" data-toggle="tab" data-urlbtn="<?= $url ?>" data-url="<?= Url::to(['/patient/order/grid-order', 'ezf_id' => $ezf_id, 'target' => $target, 'reloadDiv' => 'view-order-' . $view]) ?>"><?= Yii::t('patient', 'Investigation') ?></a></li>
    </ul>
  </div>
  <div class="card-block" style="padding:5px;">
    <div class="tab-content">
      <div role="tabpanel" class="tab-pane" id="tab_drug-<?= $view ?>">  
        <div id="view-drug-<?= $view ?>" data-url="<?= Url::to(['/pis/pis-item-order/grid-order', 'visit_id' => $target, 'reloadDiv' => 'view-drug-' . $view]) ?>"></div>
      </div>
      <div role="tabpanel" class="tab-pane active" id="tab_order-<?= $view ?>">       
          <?= backend\modules\thaihis\classes\ThaiHisHelper::uiGridOrder($ezf_id, $target, 'view-order-' . $view, $btnDisabled, $option); ?>        
      </div>
      <div role="tabpanel" class="tab-pane" id="tab_invest-<?= $view ?>">
        <div id="view-invest-<?= $view ?>" data-url="<?= Url::to(['/patient/order/grid-order', 'ezf_id' => $ezf_id, 'target' => $target, 'reloadDiv' => 'view-order-' . $view]) ?>"></div>
      </div>
    </div>
  </div>
</div>
<?php
echo ModalForm::widget([
    'id' => 'modal-order-xxl',
    'size' => 'modal-xxl',
    'tabindexEnable' => false,
]);

$this->registerJs(" 
$('#btn-order-add').attr('data-url',$('.card-order-$view .card-header-tabs .active a').attr('data-urlbtn')); //เริ่มที่ Order เป็นอันแรก

$('.card-order-$view .card-header-tabs a').on('click', function (e) {
    var url = $(this).attr('data-url');
    var reloadDiv = $(this).attr('data-reload');
    var btnUrl = $(this).attr('data-urlbtn');
    $('#btn-order-add').attr('data-url',btnUrl); 
    if(url){
        getUiAjax(url, reloadDiv);
    }else{
        $('#'+reloadDiv).html('');
    }      
    
    //return false;
});

 $('#$modal').on('hidden.bs.modal', function (e) {
    $('#modal-order-xxl .modal-content').html('');
    var objOrder = $('.card-order-$view .card-header-tabs .active a');
    getUiAjax(objOrder.attr('data-url'), objOrder.attr('data-reload'));
});
");
?>