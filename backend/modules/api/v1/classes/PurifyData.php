<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\modules\api\v1\classes;

use Yii;
use yii\db\Query;

/**
 * Description of PurifyData
 *
 * @author chaiwat
 */
class PurifyData {
    //put your code here
    
    public $application;
    public $v;
    public $s;
    public $token;
    public $site;
    public $data;
    public $active;
    private $password_hash;
    private $auth_key;
    private $userid;




    public function login(){
        $request = Yii::$app->request;
        $this->data['application'] = $request->get('application'); // $data['application'] = $request->get('application');
        // log for login
        
        // process for login
        $this->data['action'] = 'login';                                // $data['action'] = 'login';
        $this->data['status'] = 'checking';  
        $this->data['login_status'] = 'checking';
        $this->data['user']['username'] = Yii::$app->request->getAuthUser();    //$data['username'] = Yii::$app->request->getAuthUser();
        //$this->data['user']['passwords'] = Yii::$app->request->getAuthPassword();
        $query = (new Query())
                ->select(['username', 'id', 'email', 'password_hash', 'auth_key'])
                ->from('user')
                ->where(['username' => $this->data['user']['username']])
                ->orWhere(['email' => $this->data['user']['username']]);
        $resUser = $query->one();
        //$sql = 'select username,id,email,password_hash,auth_key from user where username=:username or e';
        // change login
        if ($resUser) {
            // พบ user ตรวจสอบ passwords
            $this->password_hash = $resUser['password_hash'];
            $this->auth_key = $resUser['password_hash'];
            $this->auth_key = $resUser['auth_key'];
            $this->userid = $resUser['id'];                                     
            $this->data['username'] = Yii::$app->request->getAuthUser();
            $this->data['userid'] = $resUser['id'];
            $this->data['auth_key'] = $resUser['auth_key'];                     // ตัวนี้ได้ใช้
            $this->data['password_hash'] = $resUser['password_hash'];           // ตัวนี้ ยังไม่ได้ใช้
            $this->checkValidPassword();
        }
        
    }
    
    // Set authentication for access by password
    private function checkValidPassword(){
        
        if (Yii::$app->getSecurity()->validatePassword(Yii::$app->request->getAuthPassword(), $this->password_hash)) {
            $this->data['status'] = 'success';  
            $this->data['login_status'] = 'Success';
            $this->data['user']['id'] = $this->userid;
            $this->data['user']['token'] = $this->auth_key;
            $this->token = $this->auth_key;
        }else{
            $this->data['login_status'] = 'Fail';
            $this->data['status'] = 'invalid password';
        }
    }
    
    public function checkVersionAuthen(){
        // use 
        //  application
        //  v
        //  s
        //  token
        $this->active = TRUE;                   // ผ่านการตรวจสอบ
    }
    private function updateLogCatch($ex){
        // พัฒนาต่อ เพื่อเก็บ Log error ต่างๆ ของการเข้าใช้งาน
    }

    /*
     * Check token =>
     *  Profile
     *  Privilege ในการใช้งาน
     *  Privilege ในกลุ่มที่ตัวเอง หรือ URL ที่ตัวเองสังกัด
     */
    
    public static function checkToken($token, $privilege){
        $data['token'] = $token;
        $data['plivilagegroup'] = $privilege;
        /*
         * Check token valid
         * return privilage
         */
        return $data;
    }

    public function userProfile(){
        $this->checkVersionAuthen();
        if( $this->active ):
            // After check version & authen...
            $this->setDataUserByToken();
            $this->setDataUserPrifileByToken();
        endif;
    }
    private function setDataUserByToken(){
        if( strlen($this->token)>0 ){
            try{
                $sql = 'select * from user where auth_key=:token ';
                $params = [
                    ':token' => $this->token,
                ];
                $result = Yii::$app->db->createCommand($sql, $params)->queryOne();
                $this->data['id'] = $result['id'];
                $this->data['username'] = $result['username'];
                $this->data['email'] = $result['email'];
                $this->userid = $result['id'];
            } catch (Exception $ex) {
                $this->updateLogCatch($ex);
            }
        }
    }
    private function setDataUserPrifileByToken(){
        if( strlen($this->userid)>0 ){
            try{
                $sql = 'select * from profile where user_id=:user_id ';
                $params = [
                    ':user_id' => $this->userid,
                ];
                $result = Yii::$app->db->createCommand($sql, $params)->queryOne();
                $this->data['sitecode'] = $result['sitecode'];
                $this->data['title'] = $result['title'];
                $this->data['firstname'] = $result['firstname'];
                $this->data['lastname'] = $result['lastname'];
                $this->data['cid'] = $result['cid'];
                $this->data['tel'] = $result['tel'];
            } catch (Exception $ex) {
                $this->updateLogCatch($ex);
            }
        }
    }
    
    
}
