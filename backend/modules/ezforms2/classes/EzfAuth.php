<?php

namespace backend\modules\ezforms2\classes;
use Yii;
use yii\base\Component;
class EzfAuth extends Component{
    private $module_id='';
    private $user_id = '';
    private $permission = []; 
    private $role = [];
    private $user = [];
    private $expire = [];
    private $role_start = [];
    private $every_one = [];
    
    private $status = FALSE;
    private $restricted=0;


    /**
     * @inheritdoc
     * @return EzfAuth the newly created [[EzfAuth]] instance.
    */
    public static function auth() {
        return Yii::createObject(EzfAuth::className());
    }
    
    /**
     * @param string $module_id module id
     * @return $this the query object itself
    */
    public function module($module_id){
       $this->module_id = isset($module_id) ? $module_id : '';
       return $this;
    }
    
    /**
     * @param string $user_id user id
     * * @param string $user_id user id
     * @return $this the query object itself
    */
    public function user($user_id=""){
       $this->user_id = isset($user_id ) || $user_id!="" ?  $user_id : Yii::$app->user->id;
       return $this;
    }
    
    public function dateDiff($str_start, $str_end){
        $str_start = strtotime($str_start); // ทำวันที่ให้อยู่ในรูปแบบ timestamp
        $str_end = strtotime($str_end); // ทำวันที่ให้อยู่ในรูปแบบ timestamp
        $nseconds = $str_end - $str_start; // วันที่ระหว่างเริ่มและสิ้นสุดมาลบกัน
        $ndays = round($nseconds / 86400); // หนึ่งวันมี 86400 วินาที
        return $ndays;
    }
    
    /**
     * @param string $module_id module id
     * @param string $user_id user id
     * @return $this the query object itself
    */
    public function getSite($module_id, $user_id){
        $this->module($module_id);
        $sitecode = \Yii::$app->user->identity->profile->sitecode;
        $data_access = [];
        $sql="SELECT * FROM zdata_permission_module WHERE module_id = :module_id  AND (permission_type=4 OR permission_type=null) AND rstat <> 3";
        $data = \Yii::$app->db->createCommand($sql,[":module_id"=>$this->module_id])->queryAll();
        if(Yii::$app->user->can("administrator")){
           return TRUE;
        }
        if(!empty($data)){
             foreach($data as $key=>$value){
                 if(empty($value['permission_type']) || $value['permission_type'] != 4){//ถ้าไม่ได้เลือก Specify sitecode หรือไม่เท่ากับ 4
                     array_push($data_access, 1);//ถือว่าเข้าได้
                     return 'ok ถ้าไม่ได้เลือก Specify sitecode หรือไม่เท่ากับ 4';exit();
                 }else{
                       
                     //ถ้าเลือก Specify sitecode
                     if(empty($value['permission_site'])){ //ถ้าไม่เลือก permission site
                         array_push($data_access, 1);//ถือว่าเข้าได้  
                        // return 'เลือก Specify sitecode แต่ไม่เลือก permission site';exit();
                     }else{
                         if($value['permission_site'] == 1){
                             if(empty($value['cancel_site'])){
                                 //ทุก site สามารถเข้าได้
                                array_push($data_access, 1);
                                //return "เลือก ทุก site";exit();
                             }else{
                                //return $sitecode;
                                $cancel_site = \appxq\sdii\utils\SDUtility::string2Array($value['cancel_site']);
                                if(in_array($sitecode,  $cancel_site)){
                                    return FALSE;
                                }else{
                                    return TRUE;
                                }

                            }
                         }else if($value['permission_site'] == 2){ //ถ้าเลือก Restricted
                             if(empty($value["sitecode_data"])){ //ถ้าไม่เลือก Sitecode                                 
                                 array_push($data_access, 0); //เข้าไม่ได้
                             }else{
                                 if(in_array($sitecode,  \appxq\sdii\utils\SDUtility::string2Array($value['sitecode_data']))){
                                    return TRUE; //ถ้ามี site ที่ระบุ
                                }else{
                                    return FALSE;//ไม่ม่ site
                                }
                                
                             }
                             
                         }
                     }
                 }
             }
        }else{
           array_push($data_access, 0);
           //return 'ok ถ้าไม่ได้เลือก Specify sitecode หรือไม่เท่ากับ 4';exit();
        }
        
        if(in_array(1, $data_access)){
            return TRUE;
        }else{
            return FALSE;
        }
        
        
       
    }
    
    /**
     * @param string $module_id module id
     * @param string $user_id user 
     * @return $this the query object itself
    */
    private function getAccessUser(){
        $data = (new \yii\db\Query())
                ->select("*")
                ->from("zdata_permission_module")
                ->where(["user_id"=> $this->user_id])
                ->andWhere("rstat != 3")
                ->all();
        foreach($data as $key=>$value){
            array_push($this->permission, $value['permission_type']);
        }
        return $this;
    }
    private function getUserRole(){
        $users = [];
        foreach($this->role as $key=>$value){
            $sql="SELECT * FROM zdata_matching WHERE role_name=:role_name";
            $data = Yii::$app->db->createCommand($sql, [":role_name"=>$value])->queryOne();
            if(!empty($data['user_id'])){
                array_push($users, \appxq\sdii\utils\SDUtility::string2Array($data['user_id']));
            }
        }
        return $users;
    }
    /**
     * @param string $module_id module id
     * @param string $user_id user 
     * @return $this the query object itself
    */
    public function getRole($module_id,$user_id){
        $this->module($module_id);
        $this->user($user_id);
        $sql = "SELECT * FROM zdata_permission_module WHERE module_id=:module_id AND permission_type <> 4 AND rstat <> 3";
        $data = Yii::$app->db->createCommand($sql,[
            ':module_id'=>$module_id
        ])->queryAll();
        
        
        if(empty($data)){
            array_push($this->expire, 0);
            array_push($this->role_start, 0);
            array_push($this->every_one, 0);
            return $this; 
        }  
         
        foreach($data as $value){
            array_push($this->permission, $value['permission_type']);
             
            $member = $value['member'];            
            if (!empty($member) && $member == '1') { //เมื่อติ๊ก Every one
                array_push($this->every_one, 1);
            } else {
                array_push($this->every_one, 0);
            }
       
            if(!empty($value["role_name"]) && empty($value['user_id'])){ //Restricted เลือก role_name แต่ไม่เลือก user_id
                array_push($this->role, $value['role_name']);
            }else if(!empty($value["role_name"]) && !empty($value['user_id'])){ //เลือก role_name และเลือก user_id
                array_push($this->role, $value['role_name']);
                $this->user = \appxq\sdii\utils\SDUtility::string2Array($value['user_id']);
            }else if(empty($value["role_name"]) && !empty($value['user_id'])){ //ไม่เลือก role แต่เลือก user
                $this->user = \appxq\sdii\utils\SDUtility::string2Array($value['user_id']);
            }else{
                //Restricted แต่ไม่เลือก อะไรเลย
                $this->restricted = 1;
                //\appxq\sdii\utils\VarDumper::dump('bug');
            }
            
            if(!empty($value["role_start"])){ //Role Start
                $role_start = $value["role_start"];
                $role_start_diff = $this->dateDiff(Date('Y-m-d') , $role_start)-1;
                
                if($role_start_diff >= 0){
                    array_push($this->role_start, 0);
                    //ยังไม่ถึงวันที่เข้าใช้ module ได้
                }else{
                    array_push($this->role_start, 1);
                }
            }
            
            if(!empty($value['expire_status']) && $value['expire_status'] == '1'){ //expiry status
                $diff_expiry = $this->dateDiff(Date('Y-m-d'), $value["role_stop"])+1;
                if($diff_expiry > 0){
                    array_push($this->expire, 1);
                }else{
                    array_push($this->expire, 0);
                } 
            }   
        }
        return $this;

    }
    /**
     * @return true
    */
    public function canManage(){

        return $this->canAccess("1");//Manage
    }
    
    /**
     * @return true
    */
    public function canRead(){
 
        return $this->canAccess("3");//Read
    }
    
    /**
     * @return true
    */
    public function canReadWrite(){

        return $this->canAccess("2");//ReadWrite
    }
    
    private function canAccess($per_type){
 
            $this->user(Yii::$app->user->id);
            $status_role = FALSE;
            $status_permission = FALSE;
            $status_role_start = FALSE;
            $status_expire = FALSE;
            $data_user = [];
            if(in_array('1', $this->every_one)){ //member every_one
                $this->status = TRUE;
            }else{

                if(!empty($this->role)){ //member Restricted

                    for($i=0; $i<count($this->getUserRole()); $i++){
                       for($j=0; $j<count($this->getUserRole()[$i]); $j++){
                           if(!empty($this->getUserRole()[$i][$j])){
                               array_push($data_user, $this->getUserRole()[$i][$j]);
                           }

                       }
                    }
                   if(in_array($this->user_id, $data_user) || in_array($this->user_id, $this->user)){ //ทุกคนที่อยู่ใน Role  สามารถ เข้า module ได้ หรือ User ที่ถูกเลือก
                       $status_role = TRUE;
                   }
                   //\appxq\sdii\utils\VarDumper::dump($data_user); 
                }else if(!empty($this->user) && in_array($this->user_id, $this->user)){//ไม่เลือก role แต่เลือก user
                    $status_role = TRUE;
                }//role  

                if(!empty($this->permission) && in_array($per_type, $this->permission)){  
                    $status_permission = TRUE;
                }

                if(!empty($this->role_start) && in_array('1', $this->role_start)){ //Role Start
                    $status_role_start = TRUE;
                }


                if(!empty($this->expire) && in_array('1',$this->expire)){// Expiry 
                    $status_expire = TRUE;
                }

               // return $this->expire;
                if(($status_role && $status_permission && $status_role_start && $status_expire)){//ทุกอย่างเป็นจริงหมด และกำหนด expiry role 
                    $this->status = TRUE;
                }else if(($status_role && $status_permission && $status_role_start) && empty($this->expire)){//ทุกอย่างเป็นจริงหมด และไม่กำหนด expiry role 
                     $this->status = TRUE;
                }

                

            }//if main
            if (Yii::$app->user->can("administrator") || Yii::$app->user->can("adminsite")) {
                //allow all administrator
                $this->status = TRUE;
            } else {
                if ($this->restricted == '1') {
                    return FALSE;
                }
            }
        return $this->status;
    }
    
 
 
}
