<?php

use appxq\sdii\utils\SDdate;

$url = \yii\helpers\Url::to(['/reports/report-checkup', 'target'=> $model['ptid'], 'visitid' => $model['id'],
            'action' => 'que', 'report_status' => $report_status, 'pt_hn' => $model['pt_hn'], 'que_type' => $que_type, 'page' => $page]);
?>
<a href="<?= $main_url."&target=".$model['ptid']."&visitid=".$model['id']."&que_type=".$que_type."&action=que&report_status=".$report_status."&pt_hn=".$model['pt_hn']."&page=".$page ?>" 
   data-key="<?= $model['ptid'] ?>" data-visit="<?= $model['id'] ?>" class="list-group-item item <?= ($target == $model['ptid'] ? 'active' : '') ?>" style="padding: 5px 5px 5px;">
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
                
                echo date_diff(date_create($model['pt_bdate']), date_create('now'))->y. ' ปี';
            }
            ?> </strong></div>
    </div>     
  </div>
</a>