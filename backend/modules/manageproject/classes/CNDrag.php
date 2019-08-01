<?php
 
namespace backend\modules\manageproject\classes;
 
class CNDrag {
    public static function getInvOrder(){
        $data = (new \yii\db\Query())
                ->select("*")
                ->from("inv_order")
                ->all();
        return isset($data) ? $data : '';
    }
}
