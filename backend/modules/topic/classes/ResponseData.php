<?php
 
namespace backend\modules\topic\classes;
 
class ResponseData {
   public static function formatJson($type, $msg, $icon){
       \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $result = [
            'status' => $type,
            'message' => $icon.$msg
        ];
       return $result;
   }
   public static function Success($msg){
       return ResponseData::formatJson("success", $msg,'<i class="fa fa-check"></i> ');      
    }
   public static function Error($msg){ 
       return ResponseData::formatJson("error", $msg,'<i class="fa fa-warning"></i> ');
   }
}
