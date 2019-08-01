<?php
namespace cpn\chanpan\classes;
use Yii;
class CNResponse {
    public static function init(){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    }
    
    /**
     * 
     * @param type $message string message
     * @param type $model object or array  
     * @param type $action string 'CRUD'
     */
    public static function getSuccess($message, $model="", $action=''){
        self::init();
        $result = [
            'status' => 'success',
            'action' => "{$action}",
            'message' => "<strong><i class='glyphicon glyphicon-ok-sign'></i> Success!</strong> {$message}",
            'data' => $model,
        ];
        return $result;
    }
    /**
     * 
     * @param type $message string message
     * @param type $model object or array  
     * @param type $action string 'CRUD'
     */
    public static function getError($message , $model="", $action=""){
        self::init();
        $result = [
            'status' => 'error',
            'message' => "<strong><i class='glyphicon glyphicon-warning-sign'></i> Error!</strong> {$message}",
            'data' => $model,
        ];
        return $result;
    }
    
    public static function notFoundAlert(){
        return "<h3><div class='alert alert-info'><i class='fa fa-exclamation-circle'></i> ".\Yii::t('chanpan','You have no project!')."</div></h3>";
    }
    public static function notFoundCoCreator(){
        return "<h3><div class='alert alert-info'><i class='fa fa-exclamation-circle'></i> ".\Yii::t('chanpan','You have no project Co-Creator!')."</div></h3>";
    }
}
