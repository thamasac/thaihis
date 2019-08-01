<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\controllers;
use yii\web\Controller;
class CheckLoginController extends Controller{
    //put your code here
    public function actionIndex(){
        $auth_key= isset($_GET['auth_key']) ? $_GET['auth_key'] : ''; //"HreXtADA4KJMP-7QC9wjF5U6NrZS5z84";
        $status=isset($_GET['status']) ? $_GET['status'] : ''; 
        
        if(!empty($auth_key) || $auth_key != ""){
            $usernCRC = \cpn\chanpan\classes\CNUser::GetUsernCRCByIdAll($auth_key);
            if(empty($usernCRC)){
               $user = \cpn\chanpan\classes\CNUser::GetUserTccByIdAll($auth_key);
               if(!empty($user)){
               \cpn\chanpan\classes\CNUser::SaveUserAndProfile($user);
               \cpn\chanpan\classes\CNUser::AutoLogin($auth_key);
               } 
            }else{
                \cpn\chanpan\classes\CNUser::AutoLogin($auth_key);
            }
            //ลิงค์ไปตรวจสอบการ clone ข้อมูล zdata_create_project
            return $this->redirect(['/manageproject/step/check-data']);
        }
    }
}
