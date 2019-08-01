<?php

namespace backend\modules\ezforms2\classes;
use Yii;
use yii\base\Component;
use backend\modules\ezforms2\classes\EzfAuth;
use backend\modules\ezforms2\classes\EzfAuthFunc;
class EzfAuthFuncManage extends Component{
    /**
     * @inheritdoc
     * @return EzfAuthFuncManage the newly created [[EzfAuthFuncManage]] instance.
    */
    public static function auth() {
        return Yii::createObject(EzfAuthFuncManage::className());
    }
     /**
     * @param string $module_id module id
     * @param string $user_id user id
     * @return ถ้าไม่มีสิทธิ์อ่าน จะ radirect ไปหน้า access-denied
    */
    public function canRead($module_id, $user_id=""){
        $user_id = isset($user_id ) || $user_id!="" ?  $user_id : Yii::$app->user->id;
        $status = EzfAuth::auth()->getRole($module_id, $user_id)->canRead();
        if(!$status){
            return EzfAuthFuncManage::dontAccess();
        }
        
        
    }
     /**
     * @param string $module_id module id
     * @param string $user_id user id
     * @return ถ้าไม่มีสิทธิ์จัดการ จะ radirect ไปหน้า access-denied
    */
    public function canManage($module_id, $user_id=""){
        $user_id = isset($user_id ) || $user_id!="" ?  $user_id : Yii::$app->user->id;
        $manage = EzfAuth::auth()->getRole($module_id, $user_id)->canManage();
        if(!$status){
            return EzfAuthFuncManage::dontAccess();
        }
    } 
    /**
     * @param string $module_id module id
     * @param string $user_id user id
     * @return ถ้าไม่มีสิทธิ์อ่านและเขียน จะ radirect ไปหน้า access-denied
    */
    public function canReadWrite($module_id, $user_id=""){
        $user_id = isset($user_id ) || $user_id!="" ?  $user_id : Yii::$app->user->id;
        $status = EzfAuth::auth()->getRole($module_id, $user_id)->canReadWrite();
        if(!$status){
            return EzfAuthFuncManage::dontAccess();
        }
    }
    
    /**
     * @param string $module_id module id
     * @param string $user_id user id
     * @return ถ้าไม่มีสิทธิ์เข้าใช้งาน module จะ radirect ไปหน้า access-denied
    */
    public function canSite($module_id, $user_id=""){
        $user_id = isset($user_id ) || $user_id!="" ?  $user_id : Yii::$app->user->id;
        $status = EzfAuth::auth()->getSite($module_id, $user_id);
        //\appxq\sdii\utils\VarDumper::dump($status);
        if(!$status){
            return EzfAuthFuncManage::dontAccess();
        } 
    }
    public static function dontAccess($module_id){
//        $view = Yii::$app->getView();        
//        $js= "
//            location.href = '".\yii\helpers\Url::to(['/access-module/access-denied'])."';
//        ";
//        $view->registerJs($js);
        $url = \yii\helpers\Url::to(['/access-denied','module_id'=>$module_id]);
        Yii::$app->response->redirect($url);
    }
    
    /**
     * 
     * @param type $module_id คือ รหัส module ที่จะทำงาน
     * @param type $user_id คือ user ที่ login ถ้าไม่ใส่จะ default คนที่ login
     * @return boolean 
     */
   
    public static function accessBtn($module_id){
        $status = false;
        $per_r = \cpn\chanpan\classes\CNAuth::auth()->module($module_id)->permissionType('3')->Access();
        $per_rw = \cpn\chanpan\classes\CNAuth::auth()->module($module_id)->permissionType('2')->Access();
        //$per_m = \cpn\chanpan\classes\CNAuth::auth()->module($module_id)->permissionType('1')->Access();
        
        if($per_r && ($per_rw)){
            $status = TRUE;
        }
        return $status;
    }
    
    /**
     * 
     * @param type $module_id คือ รหัส module ที่จะทำงาน
     * @param type $user_id คือ user ที่ login ถ้าไม่ใส่จะ default คนที่ login
     * @return boolean
     */
    public static function accessBtnGrid($module_id){
        $status = false;
        $per_r = \cpn\chanpan\classes\CNAuth::auth()->module($module_id)->permissionType('3')->Access();
        $per_rw = \cpn\chanpan\classes\CNAuth::auth()->module($module_id)->permissionType('2')->Access();
        $per_m = \cpn\chanpan\classes\CNAuth::auth()->module($module_id)->permissionType('1')->Access();
        
        if($per_r === true && ($per_rw === true || $per_m === true)){ 
            $status = TRUE;
        }
        return !$status;
    }
    public static function accessAllow($module_id){
        $status = false;
        $per_r = \cpn\chanpan\classes\CNAuth::auth()->module($module_id)->permissionType('3')->Access();
        $per_rw = \cpn\chanpan\classes\CNAuth::auth()->module($module_id)->permissionType('2')->Access();
        $per_m = \cpn\chanpan\classes\CNAuth::auth()->module($module_id)->permissionType('1')->Access();
        
        if($per_r === true || $per_rw === true || $per_m === true){ 
            $status = TRUE;
        }
        return $status;
        
        
    }
    public static function accessManage($module_id, $type){
        $status = false;
        $per_r = \cpn\chanpan\classes\CNAuth::auth()->module($module_id)->permissionType('3')->Access();
        $per_rw = \cpn\chanpan\classes\CNAuth::auth()->module($module_id)->permissionType('2')->Access();
        $per_m = \cpn\chanpan\classes\CNAuth::auth()->module($module_id)->permissionType('1')->Access();
//        \appxq\sdii\utils\VarDumper::dump($per_m);
        if($per_rw || ($per_m)){ //if($per_r && ($per_m)){
            $status = TRUE;
        }
        if($type==2){
            return !$status;
        }else{
            return $status;
        }
        
    }
    
    /**
     * 
     * @param type $module_id คือ รหัส module ที่จะทำงาน
     * @param type $user_id คือ user ที่ login ถ้าไม่ใส่จะ default คนที่ login
     * @return boolean
     */
    
    public static function AccessRead($module_id){
        /**
         * 3=red
         */
        $permission_read = \cpn\chanpan\classes\CNAuth::auth()
                ->module($module_id)
                ->permissionType('3')
                ->AccessRead();
        if(!$permission_read){
            return EzfAuthFuncManage::dontAccess($module_id);
        }         
         
    }
}
