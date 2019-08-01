<?php
$url = \yii\helpers\Url::to(['/cpoe', 'ptid' => $model['pt_id'], 'appid' => $model['id']
            , 'action' => 'appoint',]);

if ($model['app_status'] == '1') {
    ?>
    <a href="<?= $url ?>" class="list-group-item item" style="padding: 5px 5px 5px;">
    <?php } else { ?>
        <a href="#" class="list-group-item" style="padding: 5px 5px 5px;background-color: #ffec87;cursor: not-allowed;">
    <?php } ?>


        <div class="media">
            <div class="media-left">  
                <img src="<?= $model['pt_pic'] ?>" class="img-rounded" alt="User Image" style="width:45px;"/>  
            </div>  
            <div class="media-body" style="font-size: 12px">  
                <div><strong><?= $model['fullname'] ?> </strong></div>
                <div>
                    <?= Yii::t('patient', 'นัดมา') ?> : 
                    <strong><?= $model['ins_name'] ?> </strong>
                </div>
                <div>
                    <?= Yii::t('patient', 'Doctor') ?> : 
                    <strong><?= $model['doctor_name'] ?> </strong>
                </div>
            </div>          
        </div>
    </a>
