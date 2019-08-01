<?php
$url = yii\helpers\Url::to(['/pis/pis-order-counter/order-tran', 'order_id' => $model['order_id']
            , 'order_status' => $order_status]);
?>
<a href="<?= $url ?>" class="list-group-item item <?= ($index == 0 ? 'active' : ''); ?>" style="padding: 5px 5px 5px;">
  <div class="media">
    <div class="media-left">  
      <img src="<?= $model['pt_pic'] ?>" class="img-rounded" alt="User Image" style="width:45px;" />  
    </div>  
    <div class="media-body" style="font-size: 13px">  
      <div>
        HN : 
        <strong><?= $model['pt_hn'] ?> </strong></div>
      <div><strong><?= $model['fullname'] ?> </strong></div>
      <div>
        <?= Yii::t('patient', 'Right') ?> : 
        <strong><?= $model['right_name'] ?></strong>
      </div>
      <div>
        No. : 
        <strong><?= $model['order_no'] ?></strong>
      </div>
    </div>     
  </div>
</a>
<?php
if ($index == 0) {
    $this->registerJS("
        $.ajax({
            method: 'POST',
            url: '$url',
            dataType: 'HTML',
            success: function(result, textStatus) {
		$('#view-order-counter').html(result);
		}
            });
    ");
}
?>