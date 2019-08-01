<?php

use backend\modules\ezmodules\classes\ModuleFunc;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//appxq\sdii\utils\VarDumper::dump($value,1,0);

if($value->send_to!=''){
    $to_name = $value->send_to;
    $send_to_name = \backend\modules\ezforms2\classes\EzfQuery::getUserProfileIn($to_name);
    if($send_to_name){
        $send_to_name = yii\helpers\ArrayHelper::getColumn($send_to_name, 'fullname');
        $value->send_to_name = implode(', ', $send_to_name);
    }
}
?>
<li class="media"> 
    <?php
    if (Yii::$app->user->can('administrator')) {
        ?>
        <button type="button" class="close commt-del-btn" data-id="<?= $value['id'] ?>" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <?php } ?>
    <div class="media-left"> 
        <a > 
            <img class="media-object img-rounded" style="width: 64px; height: 64px;" src="<?= (isset($value->avatar_path) && $value->avatar_path != '') ? Yii::getAlias('@storageUrl/source') . '/' . $value['avatar_path'] : ModuleFunc::getNoUserImage() ?>" data-holder-rendered="true"> 
        </a> 
    </div> 
    <div class="media-body"> 
      <h4 class="media-heading">
        <i class="glyphicon glyphicon-user"></i> <?= $value['user_name'] ?> 
        <small><i class="glyphicon glyphicon-calendar"></i> <?= \appxq\sdii\utils\SDdate::mysql2phpDateTime($value['created_at']) ?></small> 
        <?= (isset($value->send_to_name)?'<code>@'.$value['send_to_name'].'</code>':'')?> 
        <?php if($value['type']=='ezform' && $value['dataid']>0 && $value['dataid']!=$dataid):?>
        <?= backend\modules\ezforms2\classes\EzfHelper::btn($value['object_id'])->modal($modal)->options(['class'=>'btn btn-info btn-xs'])->buildBtnView($value['dataid'])?>
        <?php endif;?>
      </h4> 
        <p><?= $value['content'] ?></p> 
    </div> 
</li> 


