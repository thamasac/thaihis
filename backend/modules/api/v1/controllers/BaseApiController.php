<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\modules\api\v1\controllers;

use common\modules\user\models\Profile;
use backend\modules\api\v1\models\ApplicationInfo;
use yii\base\ErrorException;
use yii\web\Controller;
use Yii;
use backend\modules\api\v1\classes\MainQuery;
use yii\base\Exception;
use backend\modules\api\v1\classes\LogStash;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;

/**
 * THE CONTROLLER ACTION
 */
class BaseApiController extends Controller
{
    public $enableCsrfValidation = false;
    public $user_id = null;
    /**
     * @var ApplicationInfo
     */
    public $appInfo = null;

    public function init()
    {
        parent::init();
        \Yii::$app->user->enableSession = false;
    }

    public function beforeAction($action)
    {
        $origin = "*";
        if (array_key_exists('HTTP_ORIGIN', $_SERVER)) {
            $origin = $_SERVER['HTTP_ORIGIN'];
        }
        header("Access-Control-Allow-Origin: $origin", true);
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: Origin,Content-Type,x-token,Authorization,user_id,application,platform,uuid,version");

        /**
         * CORS
         */
        if (Yii::$app->getRequest()->getMethod() === 'OPTIONS') {
            Yii::$app->end();
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        $log = [];
        $log["header"] = Yii::$app->request->headers;
        $log["body"] = Yii::$app->request->bodyParams;
        $strLog = json_encode($log, JSON_PRETTY_PRINT);
        $this->user_id = $this->validateUser();
        $this->appInfo = new ApplicationInfo($this->user_id, $log["header"]["platform"], $log["header"]["application"], $log["header"]["version"], $log["header"]["uuid"]);
        if (Yii::$app->controller->action->id != "logstash")
            LogStash::Api($this->user_id, Yii::$app->controller->action->id, $strLog, "API REQUEST", $this->appInfo);
        return parent::beforeAction($action);
    }

    /**
     * @return int|string
     * @throws UnauthorizedHttpException
     */
    function validateUser()
    {
        if(isset(Yii::$app->user->id)){
            return Yii::$app->user->id;
        }
        $headers = Yii::$app->request->headers;
        $authHeader = $headers->get('Authorization');
        $token = null;

        if ($authHeader !== null && preg_match("/^Bearer\\s+(.*?)$/", $authHeader, $matches)) {
            $token = $matches[1];
        }else if ($headers->has('x-token')) {
            $token =  $headers->get('x-token');
        }else {
            throw new UnauthorizedHttpException("token require please login.");
        }

        if (!MainQuery::CheckToken($token)) {
            throw new UnauthorizedHttpException("invalid token.");
        } else {
            return Yii::$app->user->id;
        }
    }


    /**
     * @param $data
     * @param null|string $message
     * @return array
     */
    function createResponseByVerifyData($data, $message = null)
    {
        if (!is_array($data) && $data == null) {
            return $this->createResponse(false, null, "result is null.");
        }
        if ($data instanceof \Exception || $data instanceof ErrorException) {
            return $this->createResponse(false, null, $data->getMessage());
        }
        return $this->createResponse(true, $data, $message);
    }

    /**
     * @param $success
     * @param $data
     * @param $message
     * @return array
     */
    function createResponse($success, $data, $message)
    {
        $result = ["success" => $success];
        if ($message != null) {
            $result["message"] = $message;
        } else {
            if ($message == null) {
                if (!$success) {
                    $result["message"] = "something not correct!";
                }
            }
        }
        if ($data != null || is_array($data)) {
            $result["data"] = $data;
        }
        return $result;
    }
}