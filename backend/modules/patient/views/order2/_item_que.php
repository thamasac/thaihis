<?php

use appxq\sdii\utils\SDdate;

$url = yii\helpers\Url::to(['/patient/order2/order-content', 'pt_id' => $model['pt_id'], 'visit_id' => $model['id']
            , 'visit_type' => $model['visit_type'], 'dept' => $dept, 'reloadDiv' => $reloadDiv]);
?>
<a href="<?= $url ?>" data-key="<?= $model['pt_id'] ?>" class="list-group-item item" style="padding: 5px 5px 5px;<?= isset($model['doc']) ? 'background-color: #ffec87;' : '' ?>">
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
        <?= Yii::t('patient', 'Age') ?> : 
        <strong><?php
            if ($model['pt_bdate']) {
                echo SDdate::getAge(SDdate::dateTh2bod($model['pt_bdate'])) . ' ปี';
            }
            ?> </strong></div>
      <div>
        การมา : 
        <strong><?= $model['visit_type_name'] ?> </strong></div>
    </div>     
  </div>
</a>