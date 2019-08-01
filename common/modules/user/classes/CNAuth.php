<?php
 

namespace common\modules\user\classes;
 
class CNAuth {
    public static function canAdmin($user_id){
        $user_id = isset($user_id) ? $user_id : '';
        $data = (new \yii\db\Query())
                ->select('*')
                ->from('auth_assignment')
                ->where(['user_id'=>$user_id, 'item_name'=>'administrator'])
                ->all();
        return isset($data) ? $data : false;
    }
}
