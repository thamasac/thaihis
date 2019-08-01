<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\modules\api\v1\controllers;

use backend\modules\line\classes\LineFn;

/**
 * Description of LineApiController
 *
 * @author AR Soft
 */
class LineApiController extends \yii\web\Controller {

    //put your code here

    public function actionEvent() {
        try {
            $query = new \yii\db\Query();
            $db = \Yii::$app->db_main;
            if (\cpn\chanpan\classes\CNServerConfig::isPortal() || \cpn\chanpan\classes\CNServerConfig::isLocal()) {
               $db = \Yii::$app->db;
            }

            $ch_id = \Yii::$app->request->get('ch_id', '');
            $access_token = '';
            $channelSecret = '';
            $line = $query->select('*')->from('line')->where(['chanel_id' => $ch_id])->one($db);
            if ($line) {
                $access_token = $line['line_token'];
            }
            $pincode = '';
            $userId = '';
            $status = '';
            $bot = new LineFn();
            $content = file_get_contents('php://input');
            $events = json_decode($content, true);
            if (!is_null($events) && !empty($events)) {
                // ถ้ามีค่า สร้างตัวแปรเก็บ replyToken ไว้ใช้งาน
                $replyToken = $events['events'][0]['replyToken'];
                $userId = $events['events'][0]['source']['userId'];
                $typeMessage = $events['events'][0]['message']['type'];
                $userMessage = $events['events'][0]['message']['text'];
                $user = $query->select('*')->from('line_user')->where(['line_id' => $userId])->one($db);
                if($user){
                    $result = $query->select('*')->from('line_user')->where(['pincode' => $userMessage])->one($db);
                    if(!$result){
                        if($result['line_status'] != 1){
                            $res = $bot->message('Success')->typeText()->token($access_token)->replyMessage($replyToken);
                            if($res){
                                $response = \appxq\sdii\utils\SDUtility::string2Array($res['res']);
                                if(empty($response) || is_null($response)){
                                    $query->createCommand($db)->update('line_user', [
                                        'line_status' => 1
                                    ], ['pincode' => $result['pincode']])->execute();
                                }
                            }
                        }
                    }
                }else{
                    $result = $query->select('*')->from('line_user')->where(['pincode' => $userMessage])->one($db);
                    if($result){
                        if($result['line_status'] != 1){
                            $res = $bot->message('Success')->typeText()->token($access_token)->replyMessage($replyToken);
                            if($res){
                                $response = \appxq\sdii\utils\SDUtility::string2Array($res['res']);
                                if(empty($response) || is_null($response)){
                                    $query->createCommand($db)->update('line_user', [
                                        'line_status' => 1
                                    ], ['pincode' => $result['pincode']])->execute();
                                }
                            }
                        }
                    }else{
                        $res = $bot->message('Invalid OTP')->typeText()->token($access_token)->replyMessage($replyToken);
                    }
                }
            }
            return TRUE;
        } catch (\yii\db\Exception $error) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($error);
            return FALSE;
        }
    }

}
