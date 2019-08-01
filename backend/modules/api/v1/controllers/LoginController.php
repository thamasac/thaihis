<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\modules\api\v1\controllers;

use backend\modules\api\v1\classes\NcrcProjectApi;
use common\modules\user\classes\CNSocialFunc;
use DateTime;
use backend\modules\api\v1\classes\LogStash;
use backend\modules\api\v1\models\ApplicationInfo;
use dektrium\user\models\LoginForm;
use Exception;
use yii\base\ErrorException;
use yii\web\Controller;
use Yii;
use backend\modules\api\v1\classes\MainQuery;
use dektrium\user\controllers\SecurityController as BaseSecurityController;
use yii\web\Response;

/**
 * THE CONTROLLER ACTION
 */
class LoginController extends Controller
{

    /**
     * @var ApplicationInfo
     */
    public $appInfo = null;

    public function beforeAction($action)
    {
        $origin = "*";
        if (array_key_exists('HTTP_ORIGIN', $_SERVER)) {
            $origin = $_SERVER['HTTP_ORIGIN'];
        }
        header("Access-Control-Allow-Origin: $origin", true);
        header("Access-Control-Allow-Headers: Origin,Content-Type,Authorization,application,uuid,version,platform");
        $this->enableCsrfValidation = false;
        $log = [];
        $log["header"] = Yii::$app->request->headers;
        $log["body"] = Yii::$app->request->bodyParams;
        $this->appInfo = new ApplicationInfo("", $log["header"]["platform"], $log["header"]["application"], $log["header"]["version"], $log["header"]["uuid"]);
        return true;
    }

    public function actionAuth(){
        /** @var LoginForm $model */
        $this->layout = "@backend/views/layouts/no_layout";

        $model = \Yii::createObject(LoginForm::className());
        return  $this->render('auth',['model'=>$model, 'module'=> BaseSecurityController]);
    }

    public function actionIndex()
    {
        header("Access-Control-Allow-Methods: POST");
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $headers = Yii::$app->request->headers;
        $username = Yii::$app->request->getAuthUser();
        $password = Yii::$app->request->getAuthPassword();
        $authHeader = $headers->get('Authorization');
        if ($username == null || $password == null || $password == "none") {
            if ($authHeader !== null && preg_match("/^Bearer\\s+(.*?)$/", $authHeader, $matches)) {
                $token = $matches[1];
            } else {
                $res = ["success" => false, "message" => "require login information."];
                return $res;
            }
            $res = MainQuery::LoginWithToken($this->appInfo, $username, $token);
        } else {
            $res = MainQuery::Login($this->appInfo, $username, $password);
        }

        if ($res) {
            if ($res instanceof Exception || $res instanceof ErrorException) {
                $errRes = [
                    "success" => false,
                    "error" => $res->getMessage(),
                    "message" => "Login function error."
                ];
                return $errRes;
            }else if ($res["success"]) {
                $res["servertime"] = (new DateTime())->format(DateTime::ISO8601);
                LogStash::Api($res["data"]["id"], "API_LOGIN", "", "success", $this->appInfo->application);
                return $res;
            }
        }
        $errRes = [
            "success" => false,
            "message" => "Login function error. #Unknow"
        ];
        return $errRes;
    }


    public function actionSocialLogin(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $validRes = NcrcProjectApi::sendValidateSocialToken(Yii::$app->request->get('token', null));
        if($validRes->isValid){
            // login User
            // valid
            $user = CNSocialFunc::checkUser($validRes->userId, '');
            if($user){
                CNSocialFunc::autoLogin($user);
            }
            return ['success'=>true,'data'=> $validRes->userId];
        }else{
            //deny
            return ['success'=>false,'data'=> $validRes->userId];
        }
    }

}