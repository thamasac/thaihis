<?php
$initdata = \backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String([
    'order_trad_id'         => $model['id'], 
    'order_generic_id'      => $model['trad_generic_id'], 
    'order_tran_pertime'    => $model['use_pertime'], 
    'order_tran_unit_id'    => $model['use_unit_id'], 
    'order_tran_use_id'     => $model['use_use_id'], 
    'order_tran_timeframe_id' => $model['use_timeframe_id'], 
    'order_tran_usetime_id' => $model['use_usetime_id'], 
    'order_tran_note'       => $model['use_note'], 
    'order_tran_type_status' => $model['trad_drug_type'], 
    'order_tran_label'      => $model['use_label'], 
    'unit_price'            => $model['trad_price'], 
    'order_tran_flagstatus' => '1',
    'order_tran_drugtype'      => $model['generic_type'],
    'order_tran_zeropay_status' => ($model['trad_all_pay'] == '1') ? '3' : ($model['trad_extra']=='1' ? '2' :'1'),
    'order_tran_chemo_cal'     => $model['trad_chemo_cal']  
        ]);
$ezf_id = \backend\modules\patient\Module::$formID['pis_package_item'];
$urlSelect = yii\helpers\Url::to(['/ezforms2/ezform-data/ezform', 'ezf_id' => $ezf_id, 'target' => $order_id,
            'modal' => "modal-{$ezf_id}", 'reloadDiv' => 'package-item-order', 'initdata' => $initdata]);

$img = ($model['trad_item_pic'] ? Yii::getAlias('@storageUrl/ezform/fileinput') . '/' . $model['trad_item_pic'] : Yii::getAlias('@storageUrl/images') . '/noimg.png');
?>
<div class="list-group-item" data-tmt="<?= $model['trad_tmt'] ?>" data-url="<?= $urlSelect ?>" data-modal="modal-<?= $ezf_id ?>">
  <span class="pull-left">
    <img class="img-responsive img-rounded" src="<?= $img ?>" alt="drug-item-pic" style="width:53px;">
  </span>  
  <strong class="list-group-item-heading text-primary"><?= $model['trad_itemname']; ?> </strong> 
  <div class="list-group-item-text">
    Sig : <strong class="text-success"><?= $model['use_label']; ?></strong>
  </div> 
</div>