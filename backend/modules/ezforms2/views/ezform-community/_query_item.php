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
<a class="list-group-item query-item" data-id="<?=$value['id']?>" data-url="<?=$url?>">
  <span class="badge"><?=$count_field?></span> <strong>Field Name:<code><?=$value['field']?></code></strong> <i class="glyphicon glyphicon-user"></i> <?= $value['user_name'] ?> <small><i class="glyphicon glyphicon-calendar"></i> <?= \appxq\sdii\utils\SDdate::mysql2phpDateTime($value['created_at']) ?></small> 
</a>



