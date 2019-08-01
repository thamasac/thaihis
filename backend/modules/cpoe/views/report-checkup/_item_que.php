<?php

use appxq\sdii\utils\SDdate;

$url = \yii\helpers\Url::to(['/cpoe/report-checkup', 'ptid' => $model['pt_id'], 'visitid' => $model['id'],
            'action' => 'que', 'report_status' => $report_status, 'pt_hn' => $model['pt_hn'], 'que_type' => $que_type, 'page' => $page]);
?>
<a href="<?= $url ?>" data-key="<?= $model['pt_id'] ?>" data-visit="<?= $model['id'] ?>" class="list-group-item item <?= ($pt_id == $model['pt_id'] ? 'active' : '') ?>" style="padding: 5px 5px 5px;">
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
    </div>     
  </div>
</a>