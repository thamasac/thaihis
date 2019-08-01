<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\modules\api\v1\controllers;

//use LINE\LINEBot;
//use LINE\LINEBot\HTTPClient;
//use LINE\LINEBot\HTTPClient\CurlHTTPClient;
////use LINE\LINEBot\Event;
////use LINE\LINEBot\Event\BaseEvent;
////use LINE\LINEBot\Event\MessageEvent;
//use LINE\LINEBot\MessageBuilder;
//use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;
//use LINE\LINEBot\MessageBuilder\TemplateBuilder;
//use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
//use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
//use LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder;
//use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;
//use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;
use appxq\sdii\utils\VarDumper;

/**
 * Description of NotifyApiController
 *
 * @author AR Soft
 */
class NotifyApiController extends \yii\web\Controller {

    //put your code here
    public function actionSendMail() {
        try {
            $query = new \yii\db\Query();
//            $model = new \backend\modules\ezforms2\models\TbdataAll();
//            $model->setTableName('notify_email');
            $datas = $query->select('*')->from('notify_email')->where('IFNULL(TIMESTAMP(delay_date) <= NOW() ,true) AND IFNULL(TIMESTAMP(due_date_assign) <= NOW() ,true)  AND (status is null OR status = 1)')->limit(5)->all(\Yii::$app->db_main);
//            \appxq\sdii\utils\VarDumper::dump($data->url);
            foreach ($datas as $data) {
//                if ($data) {
                $result = \dms\aomruk\classese\Notify::setNotify()
                        ->notify($data['notify'])
                        ->detail($data['detail'])
                        ->url($data['url'])
                        ->SendMailTemplate($data['email']);
//                $data->status = 3;
                if ($result == true)
                    $query->createCommand(\Yii::$app->db_main)
                            ->update('notify_email', ['status' => 3], ['id' => $data['id']])
                            ->execute();
//                    return true;
//                }else {
//                    return true;
//                }
            }
            return true;
        } catch (\yii\db\Exception $exc) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($exc);
            return FALSE;
        }
    }

    public function actionSendLine() {
        try {
            $query = new \yii\db\Query();
            $datas = $query->select('*')->from('notify_line')->where('IFNULL(TIMESTAMP(delay_date) <= NOW() ,true) AND IFNULL(TIMESTAMP(due_date_assign) <= NOW() ,true)  AND (status is null OR status = 1)')->limit(5)->all(\Yii::$app->db_main);
            foreach ($datas as $data) {
//                if ($data) {
               $result = \backend\modules\line\classes\LineFn::setLine()
                                    ->message($data['detail'])
                                    ->altMessage($data['notify'])
                                    ->typeTemplateConfirm($data['url'])
                                    ->token($user['line_token'])
                                    ->pushMessage($user['line_id']);
//                $data->status = 3;
//                if ($result)
                $query->createCommand(\Yii::$app->db_main)
                        ->update('notify_line', [
                            'status' => 3,
                            'error' => isset($result['error']) ? $result['error'] : '',
                            'result' => isset($result['res']) ? $result['res'] : ''
                                ], ['id' => $data['id']])
                        ->execute();
//                    return true;
//                }else {
//                    return true;
//                }
            }
        } catch (\Exception $ex) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
        }
    }

    public function actionSendNotify() {
        try {
            $query = new \yii\db\Query();
            $datas = $query->select('*')->from('notify_email')->where('IFNULL(TIMESTAMP(delay_date) <= NOW() ,true) AND IFNULL(TIMESTAMP(due_date_assign) <= NOW() ,true)  AND (status is null OR status = 1)')->limit(5)->all(\Yii::$app->db_main);

            foreach ($datas as $data) {
                $result = \dms\aomruk\classese\Notify::setNotify()
                        ->notify($data['notify'])
                        ->detail($data['detail'])
                        ->url($data['url'])
                        ->SendMailTemplate($data['email'],false);
                if ($result == true || !$data['email']) {
                    $query->createCommand(\Yii::$app->db_main)
                        ->update('notify_email', ['status' => 3], ['id' => $data['id']])
                        ->execute();
                }else{
                    $query->createCommand(\Yii::$app->db_main)
                        ->update('notify_email', ['status' => 4], ['id' => $data['id']])
                        ->execute();
                }
            }
        } catch (\yii\db\Exception $exc) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($exc);
        }

        try {
            $query = new \yii\db\Query();
            $datas = $query->select('*')->from('notify_line')->where('IFNULL(TIMESTAMP(delay_date) <= NOW() ,true) AND IFNULL(TIMESTAMP(due_date_assign) <= NOW() ,true)  AND (status is null OR status = 1)')->limit(5)->all(\Yii::$app->db_main);
            foreach ($datas as $data) {
//                $result = \dms\aomruk\classese\Notify::setNotify()
//                        ->notify($data['notify'])
//                        ->detail($data['detail'])
//                        ->url($data['url'])
//                        ->Line($data['assign_to']);
                $query = new \yii\db\Query();
                $user = $query->select('*')->from('line_user')->where(['user_id' => $data['assign_to']])->one(\Yii::$app->db_main);
                if ($user) {
                    $result = \backend\modules\line\classes\LineFn::setLine()
                                    ->message($data['detail'])
                                    ->altMessage($data['notify'])
                                    ->typeTemplateConfirm($data['url'])
                                    ->token($user['line_token'])
                                    ->pushMessage($user['line_id']);
//                    \appxq\sdii\utils\VarDumper::dump($result);
                }
                $query->createCommand(\Yii::$app->db_main)
                        ->update('notify_line', [
                            'status' => 3,
                            'error' => isset($result['error']) ? $result['error'] : '',
                            'result' => isset($result['res']) ? $result['res'] : ''
                                ], ['id' => $data['id']])
                        ->execute();
            }
        } catch (\Exception $ex) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
        }
    }
    
    

//
//    public function actionEvent() {
//        try {
//            $query = new \yii\db\Query();
//            $db = \Yii::$app->db_main;
//            $access_token = '';
//            $channelSecret = '';
//            $pincode = '';
//            $userId = '';
//            $status = '';
//            $content = file_get_contents('php://input');
//            $events = json_decode($content, true);
//            if (!is_null($events)) {
//                // ถ้ามีค่า สร้างตัวแปรเก็บ replyToken ไว้ใช้งาน
//                $replyToken = $events['events'][0]['replyToken'];
//                $userId = $events['events'][0]['source']['userId'];
//                $typeMessage = $events['events'][0]['message']['type'];
//                $userMessage = $events['events'][0]['message']['text'];
//
//                $user = $query->select('*')->from('line_user')->where(['line_id' => $userId])->one($db);
////                $user = mysqli_query($conn, "SELECT * FROM line_user WHERE line_id = '" . $userId . "' limit 1") or die(mysqli_error());
////                $user = mysqli_fetch_array($user);
////                $result = mysqli_query($conn, "SELECT * FROM line_user WHERE pincode = '" . $userMessage . "' limit 1") or die(mysqli_error());
//                $result = $query->select('*')->from('line_user')->where(['pincode' => $userMessage])->one($db);
////                while ($result = mysqli_fetch_array($result)) {
//                if ($result) {
//                    $access_token = $result['line_token'];
//                    $channelSecret = $result['line_secret'];
//                    $pincode = $result['pincode'];
//                    $status = $result['status'];
//                }
////                }
//
//                if ($user['status'] != 1) {
//                    if ($userMessage == $pincode && $typeMessage == 'text') {
//
//                        $httpClient = new CurlHTTPClient($access_token);
//                        $bot = new LINEBot($httpClient, ['channelSecret' => $channelSecret]);
//                        $textMessageBuilder = new TextMessageBuilder('ยืนยันตัวตนเรียบร้อย');
//                        $response = $bot->replyMessage($replyToken, $textMessageBuilder);
//                        if ($response->isSucceeded()) {
//                            $res = $bot->getProfile($userId);
//                            $res = $res->getJSONDecodedBody();
////                            mysqli_query($conn, "UPDATE line_user SET line_id = '" . $userId . "',line_name = '" . $res['displayName'] . "',line_image = '" . $res['pictureUrl'] . "' , status = 1 WHERE pincode = '" . $userMessage . "'") or die(mysqli_error());
//                            if ($query->createCommand($db)->update('line_user', [
//                                        'line_id' => isset($userId) ? $userId : '',
//                                        'line_name' => isset($res['displayName']) ? $res['displayName'] : '',
//                                        'line_image' => isset($res['pictureUrl']) ? $res['pictureUrl'] : '',
//                                        'status' => isset($userId) ? 1 : 0
//                                            ], ['pincode' => $userMessage])->execute())
//                                echo 'Succeeded!';
//                            // return;
//                        }
//                    } else if ($typeMessage == 'text') {
////                        $result = mysqli_query($conn, "SELECT * FROM line WHERE line_status = 1 limit 1") or die(mysqli_error());
//                        $result = $query->select('*')->from('line')->where(['line_status' => 1])->one($db);
////                        while ($result = mysqli_fetch_array($result)) {
//                        if ($result) {
//                            $access_token = $result['line_token'];
//                            $channelSecret = $result['line_secret'];
//                        }
////                        }
//                        $httpClient = new CurlHTTPClient($access_token);
//                        $bot = new LINEBot($httpClient, ['channelSecret' => $channelSecret]);
//                        if ($status == 1) {
//                            $textMessageBuilder = new TextMessageBuilder('คุณยืนยันตัวตนเรียบร้อยแล้ว');
//                        } else {
//                            $textMessageBuilder = new TextMessageBuilder('รหัส OTP ไม่ถูกต้อง');
//                        }
//                        $response = $bot->replyMessage($replyToken, $textMessageBuilder);
//                        if ($response->isSucceeded()) {
//                            // echo 'Succeeded!';
//                            // return;
//                        }
//                    }
//                } else {
//
//                    $access_token = $user['line_token'];
//                    $channelSecret = $user['line_secret'];
//                    $httpClient = new CurlHTTPClient($access_token);
//                    $bot = new LINEBot($httpClient, ['channelSecret' => $channelSecret]);
//                    $message = ['สวัสดี', 'Hello', 'nCRC'];
//                    $textMessageBuilder = new TextMessageBuilder($message[rand(1, 3) - 1]);
//                    $response = $bot->replyMessage($replyToken, $textMessageBuilder);
//                    if ($response->isSucceeded()) {
//                        // echo 'Succeeded!';
//                        // return;
//                    }
//                }
//            }
//
//            echo $response->getHTTPStatus() . ' ' . $response->getRawBody();
//        } catch (\yii\db\Exception $error) {
//            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($error);
//        }
//    }
}
