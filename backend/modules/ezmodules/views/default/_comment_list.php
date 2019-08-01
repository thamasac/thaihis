<?php

use backend\modules\ezmodules\classes\ModuleFunc;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>

<?php
if (isset($model) && !empty($model)) {
    echo '<li class="media"><p class="lead" style="font-size: 16px;margin-bottom: 0;"> '.Yii::t('ezmodule', 'Total'). ' ' . $count. ' '.Yii::t('ezmodule', 'items').'</p></li>';
    
    foreach ($model as $key => $value) {
        ?>
        <li class="media"> 
            <?php
            if (Yii::$app->user->can('administrator')) {
                ?>
                <button type="button" class="close commt-del-btn" data-id="<?= $value['commt_id'] ?>" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <?php } ?>
            <div class="media-left"> 
                <a > 
                    <img class="media-object img-rounded" style="width: 64px; height: 64px;" src="<?= (isset($value->avatar_path) && $value->avatar_path != '') ? Yii::getAlias('@storageUrl/source') . '/' . $value['avatar_path'] : ModuleFunc::getNoUserImage() ?>" data-holder-rendered="true"> 
                </a> 
            </div> 
            <div class="media-body"> 
                <h4 class="media-heading"><?= $value['user_name'] ?> <small><i class="glyphicon glyphicon-calendar"></i> <?= \appxq\sdii\utils\SDdate::mysql2phpDateTime($value['created_at']) ?></small></h4> 
                <p><?= $value['message'] ?></p> 
            </div> 
        </li> 
        <?php
    }
}
if ($moreitem == 1) {
    ?>
    <li class="media text-center more-item">
        <button id="more_comment" data-start="<?= $start ?>" class="btn btn-default btn-sm"><?= Yii::t('ezmodule', 'More Comment') ?></button>
    </li>
<?php } ?>

<?php $this->registerJs("



"); ?>

