<?php

use backend\modules\ezmodules\classes\ModuleFunc;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//appxq\sdii\utils\VarDumper::dump($value,1,0);
$user_id = Yii::$app->user->id;
if($value->send_to!=''){
    $to_name = $value->send_to;
    $send_to_name = \backend\modules\ezforms2\classes\EzfQuery::getUserProfileIn($to_name);
    if($send_to_name){
        $send_to_name = yii\helpers\ArrayHelper::getColumn($send_to_name, 'fullname');
        $value->send_to_name = implode(', ', $send_to_name);
    }
}
$theme = "alert-info";

if($value['created_by'] == $user_id){
    $theme = "alert-success";
}
?>
<li class="media alert <?=$theme?>"> 
    <?php
    if (Yii::$app->user->can('administrator') || $value['created_by'] == $user_id) {
        ?>
        <button type="button" class="close commt-del-btn pull-right" data-id="<?= $value['id'] ?>" ><span aria-hidden="true">&times;</span></button>
    <?php } ?>
    <div class="media-left"> 
        <a > 
            <img class="media-object img-rounded" style="width: 64px; height: 64px;" src="<?= (isset($value->avatar_path) && $value->avatar_path != '') ? Yii::getAlias('@storageUrl/source') . '/' . $value['avatar_path'] : ModuleFunc::getNoUserImage() ?>" data-holder-rendered="true"> 
        </a> 
    </div> 
    <div class="media-body" > 
      <h5 class="media-heading ">
        <i class="glyphicon glyphicon-user"></i> <?= $value['user_name'] ?> 
        <small><i class="glyphicon glyphicon-calendar"></i> <?= \appxq\sdii\utils\SDdate::mysql2phpDateTime($value['created_at']) ?></small> 
      </h5> 
        <div style="background-color:#fff;padding: 10px;border-radius:10px;" ><?= $value['content'] ?></div> 
    </div> 
</li> 


