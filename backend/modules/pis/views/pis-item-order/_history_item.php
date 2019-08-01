<?php
$urlSelect = \yii\helpers\Url::to(['/pis/pis-item-order/package-show-items',
            'dataid' => $model['id']//package_id
            , 'visit_id' => $visit_id
            , 'right_code' => $right_code
            , 'options' => $options
            , 'order_id' => $order_id]);
?>

<div class="list-group-item" data-id="<?= $model['id'] ?>" data-url="<?= $urlSelect . '&action=SELECT&mode=VISIT' ?>"> 

  <div class="click-package-item">
    <span>วันที่ : </span>
    <strong class="list-group-item-heading text-primary">
        <?= appxq\sdii\utils\SDdate::mysql2phpDate($model['visit_date']) ?> 
    </strong> 

    <div class="list-group-item-text" style="margin-left: 5px;">
      <strong style="color:#999;">Dx : <?= $model['diag_name']; ?></strong>
    </div>  
  </div>

</div>