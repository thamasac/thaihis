<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\modules\api\v1\controllers;

use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\api\v1\classes\LogStash;
use Yii;
use backend\modules\api\v1\classes\MainQuery;
use yii\db\Query;
use yii\web\Response;

/**
 * THE CONTROLLER ACTION
 */
class MainController extends BaseApiController
{
    public $request = null;
    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        header("Access-Control-Allow-Methods: POST");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: Origin,Content-Type,Authorization,user_id,application,platform,uuid,version");
        $this->request = Yii::$app->request;
        return parent::beforeAction($action);
    }

    /**
     * @return array
     */
    public function actionGetEzformData(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $ezf_id = Yii::$app->request->post('ezf_id', null);
        $data_id = Yii::$app->request->post('data_id', null);
        if($ezf_id == null || $data_id == null){
            throw new \InvalidArgumentException('ezf_id');
        }
        $ezform = EzfQuery::getEzformById($ezf_id);
        $res = (new Query)->select("*")->from($ezform['ezf_table'])->where(['id'=>$data_id])->one();
        if($res){
            return [ 'success' => true, 'data'=>$res];
        }
        return ['success'=>false];
    }

    public function actionLogstash(){
        $logName = Yii::$app->request->post('name', null);
        $alias = Yii::$app->request->post('alias', null);
        $data = Yii::$app->request->post('data', null);
        $create_date = Yii::$app->request->post('create_date', null);
        if($create_date != null)
            $create_date =  MainQuery::ConvertUtcDate($create_date);
        try {
            print_r( LogStash::Custom($this->appInfo->user_id,$alias,$logName,$data,null,$this->appInfo,$create_date->format('Y-m-d H:i:s')));
        } catch (\Exception $e) {
            LogStash::ErrorEx($this->appInfo->user_id,"Insert Log",$e,"",$this->appInfo);
        }
    }


}