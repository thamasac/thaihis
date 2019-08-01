<?php
/**
 * Created by PhpStorm.
 * User: kawin
 * Date: 8/24/2017
 * Time: 11:24 AM
 */


namespace backend\modules\api\v1\classes;
use backend\modules\api\v1\models\ApplicationInfo;
use Yii;
use \yii\base\Exception;


class LogStash
{
    /**
     * @param $user_id
     * @param $type
     * @param $name
     * @param $input
     * @param $res
     * @param $appInfo ApplicationInfo|string
     * @param null $create_date
     * @return bool|\Exception|Exception
     */
    private static function SaveLog($user_id,$type,$name,$input,$res,$appInfo,$create_date = null ){
        try{
            if( is_string($appInfo) ){
                $appInfo = new ApplicationInfo($user_id,"none",$appInfo,"none","undefined");
            }
            $logArr = [
                "type"=>$type,
                "log_name"=>$name,
                "input"=>$input,
                "result"=>$res,
                "user_id"=>$user_id,
                "application"=>$appInfo->application,
                "platform"=>$appInfo->platform,
                "version"=>$appInfo->version,
                "device_id"=>$appInfo->deviceId
            ];
            if($create_date != null)
                $logArr["create_date"] = $create_date;
            Yii::$app->db->createCommand()->insert("log_api",$logArr)->execute();
            return "ok";
//            (new DateTime())->format("Y-m-d H:i:s")
        }catch (Exception $e){
            return $e;
        }
    }

    public static function Custom($user_id,$alias,$name,$input,$res,$application = "undefined",$create_date = null){
        return self::SaveLog($user_id,$alias,$name,$input,$res,$application,$create_date);
    }

    public static function Log($user_id,$name,$input,$res,$application = "undefined"){
        self::SaveLog($user_id,"LOG",$name,$input,$res,$application);
    }

    public static function Info($user_id,$name,$input,$res,$application = "undefined"){

        self::SaveLog($user_id,"INFO",$name,$input,$res,$application);
    }

    public static function Error($user_id,$name,Exception $execption,$res,$application = "undefined"){
        $input = ["message" => $execption->getMessage()];
        $input["code"] = $execption->getCode();
        self::SaveLog($user_id,"ERROR",$name,var_export($input,true),$res,$application);
    }

    public static function ErrorEx($user_id,$name,\Exception $execption,$res,$application = "undefined"){
        $input = ["message" => $execption->getMessage()];
        self::SaveLog($user_id,"ERROR",$name,var_export($input,true),$res,$application);
    }

    public static function Api($user_id,$name,$input,$res,$application = "undefined")
    {
        self::SaveLog($user_id,"API",$name,$input,$res,$application);
    }
}