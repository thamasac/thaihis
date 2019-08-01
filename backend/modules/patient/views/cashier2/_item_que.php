<?php

use appxq\sdii\utils\SDdate;
use yii\helpers\Url;

$url = Url::to(['/patient/cashier2/cashier-receive-show', 'reloadDiv' => 'cashiercounter-index', 'cashier_status' => $model['order_tran_cashier_status'], 'visit_id' => $model['visit_id']
        ,'pt_hn'=>$model['pt_hn'],'visit_date'=>$model['visit_date']]);
?>
<a href="<?= $url ?>" class="list-group-item item" style="padding: 5px 5px 5px;">
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
        บริการ : 
        <strong><?= $model['visit_type_name'] ?> </strong></div>
    </div>     
  </div>
</a>