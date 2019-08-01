<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\modules\ezmodules\classes;
use Yii;
use yii\helpers\Html;
/**
 * Description of ModuleIcon
 *
 * @author damasac
 */
class ModuleIconBtn {
    public static function btnPermission($module){
//        $read = \backend\modules\ezforms2\classes\EzfAuthFunc::canRead($module,'');
//        $readWrite  = \backend\modules\ezforms2\classes\EzfAuthFunc::canReadWrite($module,'');
//        //\appxq\sdii\utils\VarDumper::dump($readWrite);
//        $manage     = \backend\modules\ezforms2\classes\EzfAuthFunc::canManage($module, '');
        
        //if(\backend\modules\ezforms2\classes\EzfAuthFuncManage::accessBtn($module)){
            echo Html::a('', ["/ezmodules/ezmodule/permission", 'id'=>$module], [
                    'id'=>'modal-btn-ezmodule2',
                    'class'=>'fa fa-users fa-2x pull-right underline',
                    'data-toggle'=>'tooltip',
                    'title'=>Yii::t('ezmodule', 'Module'),
                    'style'=>'margin-top: 15px;'
            ]);
       // }
        
    }
}
