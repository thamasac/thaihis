<?php
use backend\modules\ezforms2\classes\EzfStarterWidget;
use backend\modules\ezforms2\classes\EzfHelper;
use backend\modules\ezforms2\classes\EzfAuthFuncManage;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
if(!isset($options['information_ezf_id']) || empty($options['information_ezf_id'])){
    echo "<code>Please Choose Information Of CTA form. You can go to config widget.</code>";
}else{


    $ezfPermission = EzfHelper::ui($options['information_ezf_id'])
            ->data_column($options['information_fields'])
            ->default_column(false);

    $btnAdd = EzfHelper::btn($options['information_ezf_id'])->label("<i class='fa fa-plus'></i> Add new document")->buildBtnAdd();
    if (EzfAuthFuncManage::auth()->accessManage($module_id, 2)) { //module_id , 2     
        $ezfPermission->disabled(true);
        $ezfPermission->addbtn(false);
        $btnAdd = "";
    }
    
   $modelEzf = \backend\modules\ezforms2\classes\EzfQuery::getEzformOne($options['information_ezf_id']);

    echo $ezfPermission->title(backend\modules\ezforms2\classes\EzfUiFunc::getEzformIcon($modelEzf)." Information Of CTA ".$btnAdd)->addbtn(false)->buildGrid();
}

?>