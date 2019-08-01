<?php

namespace cpn\chanpan\classes;
use Yii;
use yii\base\Component;
use appxq\sdii\utils\VarDumper;
class CNAuth extends Component {
    private $module_id='';
    private $status=FALSE;
    private $user_id = '';
    private $roles = [];
    private $sitecode='';
    private $permissions=[];
    private $ptype = 0;


    public function __construct() {
        $this->user_id = isset(\Yii::$app->user->id) ? \Yii::$app->user->id : '';
        $this->sitecode = isset(Yii::$app->user->identity->profile->sitecode) ? Yii::$app->user->identity->profile->sitecode : '';
        $this->roles = CNAuthQuery::get_role_name($this->user_id,$this->sitecode);   
        //VarDumper::dump($this->roles);
        return $this;
    }
    /**
     * @inheritdoc
     * @return CNAuth the newly created [[CNAuth]] instance.
     */
    public static function auth() {
        return Yii::createObject(CNAuth::className());
    }    
    /**
     * 
     * @param type $module_id string
     * @return $this 
     */
    public function module($module_id){
       $this->module_id = isset($module_id) ? $module_id : '';
       return $this;
    }
    /**
     * 
     * @param type $ptype strin  $ptype = '';  3=read , 2=read/write , 1=manage
     * @return $this 
     */
    public function permissionType($ptype){
       $this->ptype = isset($ptype) ? $ptype : '';
       return $this;
    }
    

    
    
    public function Access() {
        $status_access = 0;
        //query permission ตาม site
        $this->permissions = CNAuthQuery::getPermissionModule($this->module_id, $this->sitecode);
         
        
        $module = \backend\modules\ezmodules\models\Ezmodule::findOne($this->module_id);
        
        
        
        if($module['created_by'] == $this->user_id){
            //คนสร้างโมดูลจะเข้าได้หมด
            return true;
        }
        
        
        if (Yii::$app->user->can("administrator")) {// || Yii::$app->user->can("adminsite")
            return TRUE; //admin ใหญ่่
        }
        
        
        //ไม่กำหนด permission เข้าไม่ได้นะ false
        if(empty($this->permissions)){return FALSE;}    
       
        
        
        foreach($this->permissions as $k=>$p){//มีหลาย permission  
              
            if($p['permission_type'] == $this->ptype && $p['member'] == 1 && empty($p['user_read'])){//read and Every one                
               return TRUE;//เข้าได้ทุกคนใน site                  
              
            }else if($p['permission_type'] == $this->ptype && $p['member'] == 1 && !empty($p['user_read'])){
                
                //ใครไม่สามารถเข้าได้ นะ
                $user_id = \appxq\sdii\utils\SDUtility::string2Array($p['user_read']);               
                if(in_array($this->user_id, $user_id)){                    
                    return FALSE;//ไม่ให้ใครเข้า
                }
            }else{
                
                //VarDumper::dump('ok');
                //user ไหนอ่านได้ 
                if(!empty($p['user_id']) && isset($p['user_id'])){                    //
                    $user_id = \appxq\sdii\utils\SDUtility::string2Array($p['user_id']);                    
                    if(in_array($this->user_id, $user_id)){
                        $status_access = 1;
                    }else{
                        return FALSE;
                    }
                    
                } 
                
                
                //role ไหนอ่านได้ 
                $status_access = 0;//ไม่มี 
                if(!empty($p['role_name']) && isset($p['role_name'])){ 
                   
                    foreach($this->roles as $r=>$v){
                        if($p['role_name'] == $v['role_name']){
                            if(in_array($this->user_id, $v['user_id'])){
                                $status_access = 1; 
                            }                           
                        } 
                    }
                }
               // VarDumper::dump($status_access);
                
                if($status_access == 1){
                    
                    if(!empty($p['role_start'])){//วันที่เริ่มต้น
                        
                        $role_start = $p["role_start"];                        
                        
                        $role_start_diff = CNUtils::dateDiff(Date('Y-m-d') , $role_start)-1;
                        
                       // VarDumper::dump($role_start_diff); 
                        if($role_start_diff > 0){
                            //VarDumper::dump($role_start_diff);
                            return FALSE;
                            //ยังไม่ถึงวันที่เข้าใช้ module ได้
                        }else{
                            if (!empty($p['expire_status']) && $p['expire_status'] == '1'){
                                $diff_expiry = CNUtils::dateDiff(Date('Y-m-d'), $p["role_stop"])+1;
                                if($diff_expiry-1 > 0){                                  
                                    return TRUE;//เวลาเหลือ
                                }else{                                    
                                    return FALSE;//หมดเวลา
                                } 
                            }
                            return TRUE;
                        }
                    }else{
                        return TRUE;
                    }
                    
                }
                
            }
        }
    }
 
    public function accessManage() {
        //role admin and user create access
        if ($this->userCreateModule()) {
            return true;
        }
        $access = false;
        $permissions = CNAuthQuery::getPermissionModule($this->module_id, $this->sitecode);
        //access everyone
        if ($this->accessEveryOne()) {
            return true;
        }
        //check role assign 
        foreach ($permissions as $k => $p) {
            //(role != '' and user_id ='' )  || (role != '' and user_id != '')
            if (($p['role_name'] != '' && $p['user_id'] == '') || ($p['role_name'] != '' && $p['user_id'] != '')) {

                $roles = [];
                $role_name = $p['role_name'];
                foreach ($this->roles as $kr => $r) {
                    array_push($roles, $role_name);
                }
                if (in_array($role_name, $roles)) {//role_name = role
                    if (isset($p['role_start']) || !empty($p['role_start'])) {
                        if ($this->getStartStopDate($p)) {
                            return true;
                        }
                    } else {
                        return true;
                    }
                }
            } else if (($p['role_name'] != '' && $p['user_id'] != '') || ($p['role_name'] == '' && $p['user_id'] != '')) {//role != '' and user_id != or role == '' and user_id != ''
                $user_obj = \appxq\sdii\utils\SDUtility::string2Array($p['user_id']);

                if (in_array($this->user_id, $user_obj)) {
                    return true;
                }
            }
        }//end check role assign
        //VarDumper::dump($permissions);

        return $access;
    }
    
   
    /**
     * 1 manage 2 read/write 3 read
     * 
     * @return boolean
     */
    public function accessRead() {
        //role admin and user create access
        if($this->userCreateModule()){
            return true;
        }
        $access = false;
        $permissions = CNAuthQuery::getPermissionModule($this->module_id, $this->sitecode);
        //access everyone
        if($this->accessEveryOne()){
            return true;
        }
        //check role assign 
        foreach($permissions as $k=>$p){
            //(role != '' and user_id ='' )  || (role != '' and user_id != '')
            if(($p['role_name'] != '' && $p['user_id'] == '') || ($p['role_name'] != '' && $p['user_id'] != '')){
                
                $roles=[];
                $role_name = $p['role_name']; 
                foreach($this->roles as $kr=>$r){
                    array_push($roles, $role_name);
                }
                if(in_array($role_name, $roles)){//role_name = role
                    if(isset($p['role_start']) || !empty($p['role_start'])){
                        if($this->getStartStopDate($p)){
                            return true;
                        }
                    }else{
                        return true;
                    }
                    
                }
                if($p['user_id'] != ''){
                    $user_arr = \appxq\sdii\utils\SDUtility::string2Array($p['user_id']);
                    if(in_array($this->user_id, $user_arr)){
                        return true;
                    }
                }
                
                
                //VarDumper::dump($p);
            }else if(($p['role_name'] != '' && $p['user_id'] != '') || ($p['role_name'] == '' && $p['user_id'] != '')){//role != '' and user_id != or role == '' and user_id != ''
                $user_obj = \appxq\sdii\utils\SDUtility::string2Array($p['user_id']);
                
                if(in_array($this->user_id, $user_obj)){
                    return true;
                }
                
            }
            
            
        }//end check role assign
        
        
        //VarDumper::dump($permissions);
        
       return $access; 
         
    }
    public function getStartStopDate($p) {
        //start and stop date 
        if (!empty($p['role_start'])) {//วันที่เริ่มต้น
            $role_start = $p["role_start"];

            $role_start_diff = CNUtils::dateDiff(Date('Y-m-d'), $role_start) - 1;

            // VarDumper::dump($role_start_diff); 
            if ($role_start_diff > 0) {
                //VarDumper::dump($role_start_diff);
                return FALSE;
                //ยังไม่ถึงวันที่เข้าใช้ module ได้
            } else {
                if (!empty($p['expire_status']) && $p['expire_status'] == '1') {
                    $diff_expiry = CNUtils::dateDiff(Date('Y-m-d'), $p["role_stop"]) + 1;
                   // VarDumper::dump($diff_expiry);
                    if ($diff_expiry  > 0) {
                        return TRUE; //เวลาเหลือ
                    } else {
                        return FALSE; //หมดเวลา
                    }
                }
                return TRUE;
            }
        } else {
            return TRUE;
        }

//        $role_start = $p["role_start"];
//        $role_start_diff = CNUtils::dateDiff(Date('Y-m-d'), $role_start);
//
//        if ((isset($p['role_start']) || !empty($p['role_start'])) && $p['expire_status'] != '1') {
//            if ($role_start_diff >= 0) {
//                $status = true;
//            }
//        } else if ((isset($p['expire_status']) || !empty($p['expire_status'])) && $p['expire_status'] == '1') {
//            //VarDumper::dump($p);
//            $diff_expiry = CNUtils::dateDiff(Date('Y-m-d'), $p["role_stop"]) + 1;
//            VarDumper::dump($diff_expiry);
//            if ($diff_expiry > 0) {
//                $status = TRUE; //เวลาเหลือ
//            }
//        }
//        return $status;
    }

 
    /**
     * access everyone
     * @return boolean
     */
    public function accessEveryOne() {
        $permissions = CNAuthQuery::getPermissionModule($this->module_id, $this->sitecode);
        //check Every one
        foreach ($permissions as $k => $p) {

            if ($p['member'] == '1' && $p['user_read'] == '') {
                //Every one and Except == ''
                return true;
            } else if ($p['member'] == '1' && $p['user_read'] != '') {
                $user_obj = \appxq\sdii\utils\SDUtility::string2Array($p['user_read']);
                //Every one and Except != 'เรา'

                if (!in_array($this->user_id, $user_obj)) {
                    return true;
                }
            }
        }//endforeach check Every one
        return false;
    }

    /**
     * user create module or administrator
     * @return boolean
     */
    public function userCreateModule() {
        $module = \backend\modules\ezmodules\models\Ezmodule::findOne($this->module_id);
        if ($module['created_by'] == $this->user_id || Yii::$app->user->can("administrator")) {
            return true;
        }
        return false;
    }

}
