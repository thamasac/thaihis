<?php

namespace backend\modules\ezforms2\controllers;
use Yii;
use yii\web\Controller;
use yii\web\Response;

class UserController extends Controller{
     
      
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
    public function actionGetUser($q = null, $id = null)
    {
        $sitecode = \Yii::$app->user->identity->profile->sitecode;      
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => []];
        if (is_null($q)) {
            $q = '';
        }
            $sql = "
                SELECT user_id ,CONCAT(firstname,' ',lastname) as `name`  
                FROM `profile` WHERE CONCAT(firstname,' ',lastname) LIKE :q  AND sitecode = :sitecode LIMIT 0,50
            ";
            $data = Yii::$app->db->createCommand($sql, [':q'=>"%$q%", ":sitecode"=>$sitecode])->queryAll();
            $i = 0;
            
            foreach($data as $value){
                $out["results"][$i] = ['id'=>$value['user_id'],'text'=>$value["name"]];
                $i++;
            }
      
        return $out;
    }

     public function actionGetFindUserFormId($q = null, $id = null){
        
        $out = ['results' => []];
        if (is_null($q)) {
            $q = '';
        } 
        $condition = ["LIKE", "CONCAT(firstname,' ',lastname)", ':q',[':q'=>"%$q%"]];
        $select = ["user_id as id","concat(firstname,' ', lastname) as text"];
        $data = \cpn\chanpan\classes\CNUser::getUserByCondition($select, $condition, 20);      
        $i = 0;
        foreach ($data as $value) {
            $out["results"][$i] = ['id' => $value['id'], 'text' => $value["text"]];
            $i++;
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $out;
    }
    
     
}
