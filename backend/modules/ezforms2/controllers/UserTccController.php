<?php

namespace backend\modules\ezforms2\controllers;
use Yii;
use yii\web\Controller;
use yii\web\Response;

class UserTccController extends Controller{ 
    
      
    public static function initUser($model, $modelFields) {
        $id = $model[$modelFields['ezf_field_name']];
        $str = '';
        if(isset($id) && !empty($id)){
            $sitecode = \Yii::$app->user->identity->profile->sitecode;
            $sql = "SELECT user_id as id, CONCAT(`firstname`,' ',`lastname`) as `name` FROM `profile` WHERE user_id = :id AND `sitecode`=:sitecode
                ";
            $data = Yii::$app->db->createCommand($sql, [':id'=>$id, ':sitecode'=>$sitecode])->queryOne();

            $str = $data['name'];
        }
        
        return $str;
    }
    
    public function actionGetUser($q = null, $id = null, $type='')
    {
        $user_mode = Yii::$app->params['user_mode'];
        $sitecode = \Yii::$app->user->identity->profile->sitecode;      
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => []];
        if (is_null($q)) {
            $q = '';
        }
        if($user_mode == 1){
            $sql = "
                SELECT p.user_id ,CONCAT(p.`firstname`,' ',p.`lastname`,' (',`u`.`email`,')') as `name` FROM `user` as u 
                INNER JOIN `user_profile` as p ON u.`id` = p.`user_id`
                WHERE CONCAT(p.`firstname`,' ',p.`lastname`) LIKE :q OR `u`.`email` LIKE :email  
                AND u.status_del = 0 AND u.status =1    
                LIMIT 0,50;
                ";
            if($type != ''){
                $sql = "
                     SELECT p.user_id ,u.email as `name` FROM `user` as u 
                    INNER JOIN `user_profile` as p ON u.`id` = p.`user_id`
                    WHERE CONCAT(p.`firstname`,' ',p.`lastname`) LIKE :q   
                    AND u.status_del = 0 AND u.status =1    
                    LIMIT 0,50;
               ";
                $data = Yii::$app->db_tcc->createCommand($sql, [':q'=>"%$q%"])->queryAll();
            }else {
                $data = Yii::$app->db_tcc->createCommand($sql, [':q'=>"%$q%", ':email'=>"%$q%"])->queryAll();
            }
            
            $i = 0;
            
            foreach($data as $value){
                $out["results"][$i] = ['id'=>$value['user_id'],'text'=>$value["name"]];
                $i++;
            } 
            
        }else{
             $sql = "
                SELECT user_id ,CONCAT(firstname,' ',lastname,' (',`u`.`email`,')') as `name`  
                FROM `profile` WHERE CONCAT(firstname,' ',lastname) LIKE :q   LIMIT 0,50
            ";//AND sitecode = :sitecode && , ":sitecode"=>$sitecode
            $data = Yii::$app->db->createCommand($sql, [':q'=>"%$q%"])->queryAll();
            $i = 0;
            
            foreach($data as $value){
                $out["results"][$i] = ['id'=>$value['user_id'],'text'=>$value["name"]];
                $i++;
            } 
        }
          
      
        return $out;
    }
    public function actionGetUserNcrc($q = null, $id = null, $type='')
    {
        
        $sitecode = \cpn\chanpan\classes\CNUser::getSiteCode();    
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => []];
        if (is_null($q)) {
            $q = '';
        }
        
       $sql = "
             SELECT p.user_id ,CONCAT(p.firstname,' ',p.lastname,' (',`u`.`email`,')') as `name`  
             FROM `user` as u INNER JOIN `profile` as p ON u.id=p.user_id
             WHERE CONCAT(p.firstname,' ',p.lastname) LIKE :q OR `u`.`email` LIKE :email  LIMIT 0,50
       ";
       if($type != ''){
           $sql = "
                SELECT p.user_id ,u.email as `name`  
                FROM `user` as u INNER JOIN `profile` as p ON u.id=p.user_id
                WHERE u.email LIKE :q   LIMIT 0,50
          ";
           $data = Yii::$app->db_main->createCommand($sql, [':q'=>"%$q%"])->queryAll();
       } else {
           $data = Yii::$app->db_main->createCommand($sql, [':q'=>"%$q%", ':email'=>"%$q%"])->queryAll();
       }
         
       
       $i = 0;            
       foreach($data as $value){
           $out["results"][$i] = ['id'=>$value['user_id'],'text'=>$value["name"]];
           $i++;
        } 
        return $out;
    }
    
    
    public function actionSave(){
        if(!empty($_POST) && !empty($_POST['_csrf'])){
            Yii::$app->response->format = Response::FORMAT_JSON;
            
            $post = $_POST['EZ1520656816059750900']; 
            $user_id = isset($post['user_id']) ? $post['user_id'] : "";
            $site_code = isset($post['site_code']) ? $post['site_code'] : "00";
            $id = isset($post['id']) ? $post['id'] : "";
            
            $sql="SELECT user_id FROM `profile` WHERE user_id=:user_id";
            $data = \Yii::$app->db->createCommand($sql, [':user_id'=>$user_id])->queryOne();

            if(!empty($data)){ //เช็คจาก profile ถ้ามี user แล้วจะแก้ไขแทน
                print_r("No");exit();
            }else{ 
                echo "Create";
               // $this->Create($user_id, $site_code);
            }
             
        }
    }
   
     
}
