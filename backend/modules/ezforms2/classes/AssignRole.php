<?php

namespace backend\modules\ezforms2\classes;
use Yii;
use backend\modules\patient\classes\PatientFunc;
class AssignRole {
    public static function getDataAssign($where){
        $data = (new \yii\db\Query())
                ->select([
                    'assign.id',
                    'assign.role_name as role_id',
                    'assign.user_id',
                    'assign.start_date',
                    'assign.expiry_date',
                    'assign.role_start',
                    'assign.expire_status',
                    'assign.role_stop',
                    'role.role_name',
                    'role.role_detail',
                    ])
                ->from("zdata_matching as assign")
                ->join('INNER JOIN', "zdata_role as role", "role.id = assign.role_name")
                ->where($where)
                ->all();
        return $data;
    }
    public static function getIdNewRole(){
        $data = (new \yii\db\Query())
                ->select('*')
                ->from("zdata_role")
                ->where("rstat = 0")
                ->one();
        return $data["id"];
    }

    public static function saveDataAssign($role_id, $dataid, $data=[]){
        //["role_name"=>$role_id,'ezf_version'=>'v1', 'rstat'=>$rstat]
       return PatientFunc::backgroundInsert("1520249845081836400", $dataid, "",$data);
    }

    public static function saveDataRole($dataid,$data=[]){
        
       return PatientFunc::backgroundInsert("1519706984056553600", $dataid, "",$data);
    }
    
    public static function Success($msg){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return ['status'=>'success', 'message'=>$msg];
    }
}
