<?php

namespace backend\modules\line\controllers;

use appxq\sdii\utils\VarDumper;
use backend\modules\ezforms\components\EzformFunc;
use backend\modules\ezforms2\classes\EzfFunc;
use yii\db\Exception;
use yii\web\Controller;
use yii\db\Query;

/**
 * Default controller for the `line` module
 */
class DefaultController extends Controller
{

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        try {
            $dbname = explode('dbname=', \Yii::$app->db->dsn);
            $query = new Query();
            $lineData = $query->select('*')->from('line')->where('line_status = 1')->one(\Yii::$app->db_main);
            $lineid = '';
            $lineimg = '';
            $line_name = '';
            $user_id = '';
            $line_message = [];
            $lineuser = $query->select('*')->from('line_user')->where(['user_id' => \Yii::$app->user->identity->id])->one(\Yii::$app->db_main);

            if (!$lineuser) {
                while (true) {
                    $pincode = str_pad(rand(10000, 99999), 5, '0', STR_PAD_LEFT);
                    if (!$query->select('*')->from('line_user')->where(['pincode' => $pincode])->one(\Yii::$app->db_main)) {
//                        $lineuser = new LineUser();
//                        $lineuser->user_id = \Yii::$app->user->identity->id;
//                        $lineuser->pincode = $pincode;
//                        $lineuser->dupdate = date("Y-m-d H:i:s");
//                        $lineuser->line_token = $lineData->line_token;
//                        $lineuser->line_secret = $lineData->line_secret;
//                        $lineuser->save();
                        $query->createCommand(\Yii::$app->db_main)->insert('line_user', [
                            'user_id' => \Yii::$app->user->identity->id,
                            'pincode' => $pincode,
                            'dupdate' => date("Y-m-d H:i:s"),
                            'line_token' => $lineData['line_token'],
                            'line_secret' => $lineData['line_secret'],
                            'db_serve' => isset($dbname[1]) ? $dbname[1] : ''
                        ])->execute();
                        break;
                    }
                }
            } else {
                if ($lineuser['line_id'] == '') {
                    while (true) {
                        $pincode = str_pad(rand(10000, 99999), 5, '0', STR_PAD_LEFT);
                        if (!$query->select('*')->from('line_user')->where(['pincode' => $pincode])->one(\Yii::$app->db_main)) {
//                            $lineuser->pincode = $pincode;
//                            $lineuser->dupdate = date("Y-m-d H:i:s");
//                            $lineuser->save();
                            $query->createCommand(\Yii::$app->db_main)->update('line_user', [
                                'pincode' => $pincode,
                                'dupdate' => date("Y-m-d H:i:s"),
                            ], ['user_id' => \Yii::$app->user->identity->id])->execute();
                            break;
                        }
                    }
                } else {
                    $user_id = $lineuser['user_id'];
                    $pincode = $lineuser['pincode'];
                    $lineid = $lineuser['line_id'];
                    $line_name = $lineuser['line_name'];
                    try {
                        $array = get_headers($lineuser['line_image']);
                        $string = $array[0];
                        if (!strpos($string, "200")) {
                            $curl = curl_init();
                            curl_setopt_array($curl, array(
                                CURLOPT_URL => "https://api.line.me/v2/bot/profile/{$lineuser['line_id']}",
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_ENCODING => "",
                                CURLOPT_MAXREDIRS => 10,
                                CURLOPT_TIMEOUT => 30,
                                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                CURLOPT_CUSTOMREQUEST => "GET",
                                CURLOPT_POSTFIELDS => '',
                                CURLOPT_HTTPHEADER => array(
                                    "authorization: Bearer {$lineuser['line_token']}",
                                    "cache-control: no-cache",
                                    "content-type: application/json",
                                    "postman-token: 92ca101e-dcc3-9f50-615d-76cffac0b616"
                                ),
                            ));

                            $response = curl_exec($curl);
                            $errline = curl_error($curl);
                            curl_close($curl);
                            $response = json_decode($response);
                            $line_message = ['res' => $response ,'err' => $errline,'status' => $array,'line_user' => $lineuser,'url' => "https://api.line.me/v2/bot/profile/{$lineuser['line_id']}",'auth'=> "authorization: Bearer {$lineData['line_token']}"];
                            $lineuser['line_image'] = !isset($response->message) ? $response->pictureUrl : \Yii::getAlias('@storageUrl') . '/images/nouser.png';
                            try {
                                $query->createCommand(\Yii::$app->db_main)->update('line_user', [
                                    'line_image' => $lineuser['line_image'],
                                    'dupdate' => date("Y-m-d H:i:s"),
                                ], ['user_id' => \Yii::$app->user->identity->id])->execute();
                            } catch (Exception $e) {
                                $lineuser['line_image'] = \Yii::getAlias('@storageUrl') . '/images/nouser.png';
                                EzfFunc::addErrorLog($e);
                            }
                        }else{
                            $line_message = ['status' => $array];
                        }


                    } catch (\Exception $e) {
                        $lineuser['line_image'] = \Yii::getAlias('@storageUrl') . '/images/nouser.png';
                        $line_message = ['status' => $e];
                        EzfFunc::addErrorLog($e);
                    }
                }
                $lineimg = $lineuser['line_image'];
            }

            //$pincode= rand(10000, 99999);
            return $this->render('index', [
                'pincode' => $pincode,
                'lineid' => $lineid,
                'lineimg' => $lineimg,
                'line_name' => $line_name,
                'line_qrcode' => $lineData['line_qrcode'],
                'user_id' => $user_id,
                'line_message' => $line_message
            ]);
        } catch (\yii\db\Exception $error) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($error);
        }
    }

    public function actionAjax()
    {
        try {
            $reloadDiv = \Yii::$app->request->get('reloadDiv', 'line-notify-' . \appxq\sdii\utils\SDUtility::getMillisecTime());
            $title = \Yii::$app->request->get('title', \Yii::t('line', 'Line Notification'));
            $dbname = explode('dbname=', \Yii::$app->db->dsn);
            $query = new Query();
            $lineData = $query->select('*')->from('line')->where('line_status = 1')->one(\Yii::$app->db_main);
            $lineid = '';
            $lineimg = '';
            $line_name = '';
            $user_id = '';
            $line_message = [];
            $lineuser = $query->select('*')->from('line_user')->where(['user_id' => \Yii::$app->user->identity->id])->one(\Yii::$app->db_main);

            if (!$lineuser) {
                while (true) {
                    $pincode = str_pad(rand(10000, 99999), 5, '0', STR_PAD_LEFT);
                    if (!$query->select('*')->from('line_user')->where(['pincode' => $pincode])->one(\Yii::$app->db_main)) {
//                        $lineuser = new LineUser();
//                        $lineuser->user_id = \Yii::$app->user->identity->id;
//                        $lineuser->pincode = $pincode;
//                        $lineuser->dupdate = date("Y-m-d H:i:s");
//                        $lineuser->line_token = $lineData->line_token;
//                        $lineuser->line_secret = $lineData->line_secret;
//                        $lineuser->save();
                        $query->createCommand(\Yii::$app->db_main)->insert('line_user', [
                            'user_id' => \Yii::$app->user->identity->id,
                            'pincode' => $pincode,
                            'dupdate' => date("Y-m-d H:i:s"),
                            'line_token' => $lineData['line_token'],
                            'line_secret' => $lineData['line_secret'],
                            'db_serve' => isset($dbname[1]) ? $dbname[1] : ''
                        ])->execute();
                        break;
                    }
                }
            } else {
                if ($lineuser['line_id'] == '') {
                    while (true) {
                        $pincode = str_pad(rand(10000, 99999), 5, '0', STR_PAD_LEFT);
                        if (!$query->select('*')->from('line_user')->where(['pincode' => $pincode])->one(\Yii::$app->db_main)) {
//                            $lineuser->pincode = $pincode;
//                            $lineuser->dupdate = date("Y-m-d H:i:s");
//                            $lineuser->save();
                            $query->createCommand(\Yii::$app->db_main)->update('line_user', [
                                'pincode' => $pincode,
                                'dupdate' => date("Y-m-d H:i:s"),
                            ], ['user_id' => \Yii::$app->user->identity->id])->execute();
                            break;
                        }
                    }
                } else {
                    $user_id = $lineuser['user_id'];
                    $pincode = $lineuser['pincode'];
                    $lineid = $lineuser['line_id'];
                    $line_name = $lineuser['line_name'];
                    try {
                        $array = get_headers($lineuser['line_image']);
                        $string = $array[0];
                        if (!strpos($string, "200")) {
                            $curl = curl_init();
                            curl_setopt_array($curl, array(
                                CURLOPT_URL => "https://api.line.me/v2/bot/profile/{$lineuser['line_id']}",
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_ENCODING => "",
                                CURLOPT_MAXREDIRS => 10,
                                CURLOPT_TIMEOUT => 30,
                                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                CURLOPT_CUSTOMREQUEST => "GET",
                                CURLOPT_POSTFIELDS => '',
                                CURLOPT_HTTPHEADER => array(
                                    "authorization: Bearer {$lineuser['line_token']}",
                                    "cache-control: no-cache",
                                    "content-type: application/json",
                                    "postman-token: 92ca101e-dcc3-9f50-615d-76cffac0b616"
                                ),
                            ));

                            $response = curl_exec($curl);
                            $errline = curl_error($curl);
                            curl_close($curl);
                            $response =  json_decode($response);
                            $line_message = ['res' => $response ,'err' => $errline,'status' => $array,'line_user' => $lineuser,'url' => "https://api.line.me/v2/bot/profile/{$lineuser['line_id']}",'auth'=> "authorization: Bearer {$lineData['line_token']}"];
                            $lineuser['line_image'] = !isset($response->message)  ? $response->pictureUrl : \Yii::getAlias('@storageUrl') . '/images/nouser.png';
                            try {
                                $query->createCommand(\Yii::$app->db_main)->update('line_user', [
                                    'line_image' => $lineuser['line_image'],
                                    'dupdate' => date("Y-m-d H:i:s"),
                                ], ['user_id' => \Yii::$app->user->identity->id])->execute();
                            } catch (Exception $e) {
                                $lineuser['line_image'] = \Yii::getAlias('@storageUrl') . '/images/nouser.png';
                                EzfFunc::addErrorLog($e);
                            }
                        }else{
                            $line_message = ['status' => $array];
                        }


                    } catch (\Exception $e) {
                        $lineuser['line_image'] = \Yii::getAlias('@storageUrl') . '/images/nouser.png';
                        $line_message = ['status' => $e];
                        EzfFunc::addErrorLog($e);
                    }
                    $lineimg = $lineuser['line_image'];
                }
            }

            //$pincode= rand(10000, 99999);
            return $this->renderAjax('index', [
                'pincode' => $pincode,
                'lineid' => $lineid,
                'lineimg' => $lineimg,
                'line_name' => $line_name,
                'line_qrcode' => $lineData['line_qrcode'],
                'reloadDiv' => $reloadDiv,
                'title' => $title,
                'user_id' => $user_id,
                'line_message' => $line_message
            ]);
        } catch (\yii\db\Exception $error) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($error);
        }
    }

    public function actionPincode()
    {
        try {
            $query = new Query();
            $pincode = \Yii::$app->request->get('pincode', '');

            $lineuser = $query->select('*')->from('line_user')->where(['pincode' => $pincode, 'user_id' => \Yii::$app->user->id])->one(\Yii::$app->db_main);
            if ($lineuser) {
                $query->createCommand(\Yii::$app->db_main)->delete('line_user', ['pincode' => $pincode, 'user_id' => \Yii::$app->user->id])->execute();
//                $lineuser->delete();
            } else {
                return 'Not PIN Code';
            }
        } catch (\yii\db\Exception $error) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($error);
        }
    }

    public function actionCheckOtp()
    {
        try {
            $query = new Query();
            $pincode = \Yii::$app->request->get('pincode', '');

            $lineuser = $query->select('*')->from('line_user')->where(['pincode' => $pincode, 'user_id' => \Yii::$app->user->id])->one(\Yii::$app->db_main);
            if ($lineuser) {
                if ($lineuser['line_id'] != '' && $lineuser['line_id'] != null) {
                    return 'success';
                }
            } else {
                return 'Not PIN Code';
            }
        } catch (\yii\db\Exception $error) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($error);
        }
    }

    public function actionEdit()
    {
        $reloadDiv = \Yii::$app->request->get('reloadDiv', 'line-notify' . \appxq\sdii\utils\SDUtility::getMillisecTime());
        $modal = \Yii::$app->request->get('modal', 'modal-line-' . \appxq\sdii\utils\SDUtility::getMillisecTime());
        $query = new Query();
        $dataProvider = new \yii\data\ArrayDataProvider([
            'allModels' => $query->select('*')->from('line')->all(\Yii::$app->db_main),
            'pagination' => [
                'pageSize' => '20',
                //'route' => '/ezforms2/fileinput/grid-update',
            ],
            'sort' => [
                //'route' => '/ezforms2/fileinput/grid-update',
                'attributes' => [
                    'line_qrcode',
                    'line_token',
                    'line_secret',
                    'line_status'
                ]
            ]
        ]);
        return $this->renderAjax('edit', [
            'dataProvider' => $dataProvider,
            'reloadDiv' => $reloadDiv,
            'modal_line' => $modal
        ]);
    }

    public function actionAdd()
    {
        $post = \Yii::$app->request->post();
        $reloadDiv = \Yii::$app->request->get('reloadDiv', 'line-notify' . \appxq\sdii\utils\SDUtility::getMillisecTime());
        $modal = \Yii::$app->request->get('modal', 'modal-line-' . \appxq\sdii\utils\SDUtility::getMillisecTime());
        if ($post) {
            try {
//                return false;
                $query = new Query();
                if (isset($post['id']) && $post['id'] != '') {
                    $query->createCommand(\Yii::$app->db_main)->update('line', [
                        'line_name' => isset($post['line_name']) ? $post['line_name'] : '',
                        'line_qrcode' => isset($post['line_qrcode']) ? $post['line_qrcode'] : '',
                        'line_secret' => isset($post['line_secret']) ? $post['line_secret'] : '',
                        'line_token' => isset($post['line_token']) ? $post['line_token'] : '',
                        'chanel_id' => isset($post['chanel_id']) ? $post['chanel_id'] : '',
                        'line_status' => isset($post['line_status']) ? $post['line_status'] : ''
                    ], ['id' => $post['id']])->execute();
                } else {
                    $query->createCommand(\Yii::$app->db_main)->insert('line', [
                        'line_name' => isset($post['line_name']) ? $post['line_name'] : '',
                        'line_qrcode' => isset($post['line_qrcode']) ? $post['line_qrcode'] : '',
                        'line_secret' => isset($post['line_secret']) ? $post['line_secret'] : '',
                        'line_token' => isset($post['line_token']) ? $post['line_token'] : '',
                        'chanel_id' => isset($post['chanel_id']) ? $post['chanel_id'] : '',
                        'line_status' => isset($post['line_status']) ? $post['line_status'] : ''
                    ])->execute();
                }
                return true;
            } catch (Exception $ex) {
                \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
                return false;
            }
        } else {
            return $this->renderAjax('add',[ 'reloadDiv' => $reloadDiv, 'modal_line' => $modal,'readonly' => false,'data'=>[]]);
        }
    }

    public function actionUpdate()
    {
        try {
            $reloadDiv = \Yii::$app->request->get('reloadDiv', 'line-notify' . \appxq\sdii\utils\SDUtility::getMillisecTime());
            $modal = \Yii::$app->request->get('modal', 'modal-line-' . \appxq\sdii\utils\SDUtility::getMillisecTime());
            $id = \Yii::$app->request->get('dataid', '');
            if ($id != '') {
                $data = (new Query())->select('*')->from('line')->where(['id' => $id])->one(\Yii::$app->db_main);
                return $this->renderAjax('add', [
                    'data' => $data,
                    'data' => $data,
                    'reloadDiv' => $reloadDiv,
                    'modal_line' => $modal,
                    'readonly' => false,
                ]);
            } else {
                return $this->renderAjax('add',[ 'reloadDiv' => $reloadDiv,
                    'data' => [],
                    'readonly' => false,
                    'modal_line' => $modal]);
            }
        } catch (Exception $ex) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
            return '<div class="alert alert-danger text-center">Error</div>';
        }
    }

    public function actionView()
    {
        try {
            $reloadDiv = \Yii::$app->request->get('reloadDiv', 'line-notify' . \appxq\sdii\utils\SDUtility::getMillisecTime());
            $modal = \Yii::$app->request->get('modal', 'modal-line-' . \appxq\sdii\utils\SDUtility::getMillisecTime());
            $id = \Yii::$app->request->get('dataid', '');
            if ($id != '') {
                $data = (new Query())->select('*')->from('line')->where(['id' => $id])->one(\Yii::$app->db_main);
                return $this->renderAjax('add', [
                    'data' => $data,
                    'readonly' => true,
                    'reloadDiv' => $reloadDiv,
                    'modal_line' => $modal
                ]);
            } else {
                return $this->renderAjax('add',[
                    'data' => [],
                    'reloadDiv' => $reloadDiv,
                    'modal_line' => $modal,
                    'readonly' => false,
                ]);
            }
        } catch (Exception $ex) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
            return '<div class="alert alert-danger text-center">Error</div>';
        }
    }

    public function actionDelete()
    {
        try {
            $id = \Yii::$app->request->post('dataid', '');
            if ($id != '') {
                $data = (new Query())->createCommand(\Yii::$app->db_main)->delete('line', ['id' => $id])->execute();
                return true;
            } else {
                return false;
            }
        } catch (Exception $ex) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
            return false;
        }
    }

    public function actionUpdateStatus()
    {
        try {
            $id = \Yii::$app->request->get('dataid', '');
            $status = \Yii::$app->request->get('data-status', '');
            if ($id != '') {
                (new Query())->createCommand(\Yii::$app->db_main)->update('line', ['line_status' => 0], ['line_status' => 1])->execute();
                (new Query())->createCommand(\Yii::$app->db_main)->update('line', ['line_status' => $status == 0 ? 1 : 0], ['id' => $id])->execute();
                return true;
            } else {
                return false;
            }
        } catch (Exception $ex) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
            return false;
        }
    }

    public function actionDisconnect()
    {
        $user_id = \Yii::$app->request->post('user_id', '');
        if ($user_id != '') {
            try {
                if ((new Query())->createCommand(\Yii::$app->db_main)->delete('line_user', ['user_id' => $user_id])->execute()) {
                    return 'success';
                }
            } catch (Exception $ex) {
                EzfFunc::addErrorLog($ex);
                return 'error';
            }

        } else {
            return 'error';
        }
    }

    public function actionBot()
    {
        $query = new Query();
        $db = \Yii::$app->db_main;
        $chId = \Yii::$app->request->get('ch_id', '');
        $lineData = $query->select('*')->from('line')->where(['ch_id' => $chId])->one($db);
        $content = file_get_contents('php://input');
        $events = json_decode($content, true);
        $curl = curl_init();

        if (!is_null($events)) {
            // ถ้ามีค่า สร้างตัวแปรเก็บ replyToken ไว้ใช้งาน
            $replyToken = $events['events'][0]['replyToken'];
            $userId = $events['events'][0]['source']['userId'];
            $typeMessage = $events['events'][0]['message']['type'];
            $userMessage = $events['events'][0]['message']['text'];

            $user = mysqli_query($conn, "SELECT * FROM line_user WHERE line_id = '" . $userId . "' limit 1") or die(mysqli_error());


            $result = mysqli_query($conn, "SELECT * FROM line_user WHERE pincode = '" . $userMessage . "' limit 1") or die(mysqli_error());
            while ($row = mysqli_fetch_array($result)) {
                $access_token = $row['line_token'];
                $channelSecret = $row['line_secret'];
                $pincode = $row['pincode'];
                $status = $row['status'];
            }
            if ($user['status'] != 1) {
                if ($userMessage == $pincode && $typeMessage == 'text') {

                    $textMessageBuilder = new TextMessageBuilder('ยืนยันตัวตนเรียบร้อย');
                    $response = $bot->replyMessage($replyToken, $textMessageBuilder);
                    if ($response->isSucceeded()) {
                        $res = $bot->getProfile($userId);
                        $res = $res->getJSONDecodedBody();
                        $url = $res['pictureUrl'];

                        $img = \Yii::getAlias('@storageUrl') . '/source/1/' . \appxq\sdii\utils\SDUtility::getMillisecTime() . '.png';
                        file_put_contents($img, file_get_contents($url));
                        mysqli_query($conn, "UPDATE line_user SET line_id = '" . $userId . "',line_name = '" . $res['displayName'] . "',line_image = '" . $img . "' , status = 1 WHERE pincode = '" . $userMessage . "'") or die(mysqli_error());
                        // echo 'Succeeded!';
                        // return;

                    }
                } else if ($typeMessage == 'text') {
                    $result = mysqli_query($conn, "SELECT * FROM line WHERE line_status = 1 limit 1") or die(mysqli_error());
                    while ($row = mysqli_fetch_array($result)) {
                        $access_token = $row['line_token'];
                        $channelSecret = $row['line_secret'];
                    }

                    $response = $bot->replyMessage($replyToken, $textMessageBuilder);
                }
            } else {


                $message = ['KKU', 'Damasac', 'nCRC'];
                $textMessageBuilder = new TextMessageBuilder($message[rand(1, 3) - 1] . $_GET['ch_id']);
                $response = $bot->replyMessage($replyToken, $textMessageBuilder);
            }
        }
    }

}
