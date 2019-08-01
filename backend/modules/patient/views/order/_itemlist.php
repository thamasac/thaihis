
<div class="list-group-item" <?= (in_array($model['order_code'], $orderTranPt) ? 'style="cursor: not-allowed;"' : 'data-code="' . $model['order_code'] . '"' . ' data-type="' . $model['group_type'] . '"') ?>> 
  <h4 class="list-group-item-heading"><?= $model['order_name']; ?><?= $model['external_flag'] == 'Y' ? ' (External Lab)' : '' ?> </h4> 
  <div class="list-group-item-text" style="margin-bottom: 5px">
    <div>
      <?= Yii::t('patient', 'Group') ?> : <strong class="text-success"><?= $model['order_group_name']; ?></strong>
    </div>
    <?= Yii::t('patient', 'Type') ?> : <strong class="text-success"><?= $model['order_type_name']; ?></strong>
    <?= Yii::t('patient', 'Code') ?> : <strong class="text-danger"><?= $model['order_code']; ?></strong>
    <?= Yii::t('patient', 'Price') ?> : <strong class="text-info"><?= $model['full_price']; ?></strong>
    <?php if (!in_array($model['order_code'], $orderTranPt)) { ?>
        <button class="btn btn-sm btn-default pull-right" >เลือก <i class="fa fa-arrow-right"></i></button>
      <?php } ?>
  </div> 
</div>