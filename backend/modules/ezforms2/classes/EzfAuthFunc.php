<?php

namespace backend\modules\ezforms2\classes;
use Yii;
use yii\base\Component;
use backend\modules\ezforms2\classes\EzfAuth;
class EzfAuthFunc{
    /**
     * @param string $module_id module id
     * @param string $user_id user id
     * @return true or false
    */
    public static function canManage($module_id, $user_id=""){
        $user_id = isset($user_id ) || $user_id!="" ?  $user_id : Yii::$app->user->id;
        return EzfAuth::auth()->getRole($module_id, $user_id, 1)->canManage();
    }
    
   /**
     * @param string $module_id module id
     * @param string $user_id user id
     * @return true or false
    */
    public static function canRead($module_id, $user_id=""){
        $user_id = isset($user_id ) || $user_id!="" ?  $user_id : Yii::$app->user->id;
        return EzfAuth::auth()->getRole($module_id, $user_id, 3)->canRead();
    }
    
    /**
     * @param string $module_id module id
     * @param string $user_id user id
     * @return true or false
    */
    public static function canReadWrite($module_id, $user_id=""){
        $user_id = isset($user_id ) || $user_id!="" ?  $user_id : Yii::$app->user->id;
        return EzfAuth::auth()->getRole($module_id, $user_id, 2)->canReadWrite();
         
    }
    /**
     * @param string $module_id module id
     * @param string $user_id user id
     * @return true or false
    */
    public static function canSite($module_id, $user_id=""){
        $user_id = isset($user_id ) || $user_id!="" ?  $user_id : Yii::$app->user->id;
        return EzfAuth::auth()->getSite($module_id, $user_id);
    }
    
    
    public static function dontAccess(){
        $modal = "
            <div class='modal-header'>
                <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                <h4 class='modal-title' id='itemModalLabel'>".Yii::t('ezmodule', 'Permission  ')."</h4>
            </div>
        ";
        $modal .= "<div class='modal-body'><h1 class='alert alert-danger'><i class='glyphicon glyphicon-warning-sign'></i> <b>".\Yii::t('ezform2', 'Access Denied').'</b></h1></div>'; 
        echo $modal;exit();
    }
}
