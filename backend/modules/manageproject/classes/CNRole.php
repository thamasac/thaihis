<?php
 

namespace backend\modules\manageproject\classes;
use Yii; 
class CNRole {
    public static function getRoleName($role_name){
        $data = (new \yii\db\Query())
                ->select('*')
                ->from('zdata_role')
                ->where(['role_name'=>$role_name])
                ->one();
         //['role_name'=>, 'role_detail'=>$data['role_detail']];
        return $data['role_detail'].' ('.$data['role_name'].')';
                
    }
    public static function getRoleNames(){
        $sitecode = isset(Yii::$app->user->identity->profile->sitecode)?Yii::$app->user->identity->profile->sitecode:''; 
        $userId = \cpn\chanpan\classes\CNUser::getUserId(); 
        $roleArr = \cpn\chanpan\classes\CNAuthQuery::getRoleName($userId, $sitecode);
        $out = [];
        $roleNameStr = "";
        foreach($roleArr as $k => $role){
            array_push($out, $role['role_name']);
        }
        //\appxq\sdii\utils\VarDumper::dump($out);
        $roleNameStr = implode(",", $out);
        return $roleNameStr;
    }
}
