<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\modules\line\classes;

use backend\modules\ezforms2\classes\EzfFunc;

/**
 * Description of LineFn
 *
 * @author AR Soft
 */
class LineFn extends \yii\base\BaseObject {

    public $token = '';
    public $secret_token = '';
    public $message = 'New Message';
    public $altMessage = 'New Message';
    public $template = '';

    /**
     * 
     * @return LineFn the newly created [[LineFn]] instance.
     */
    public static function setLine() {
        return \Yii::createObject(LineFn::className());
    }
    
    

    /**
     * 
     * @param type string $message
     * @return content html
     */
    public function message($message) {
        $message = substr($message, 0, 200) . '...';
        $message = str_replace('<div>', '', $message);
        $message = str_replace('</div>', '', $message);
        $message = str_replace('<br>', '\n', $message);
        $message = str_replace('<p>', '', $message);
        $message = str_replace('</p>', '', $message);
        $message = strip_tags($message);
        $this->message = $message;
        return $this;
    }

    /**
     * 
     * @param type $altMessage
     * @return $this
     */
    public function altMessage($altMessage) {
        $this->altMessage = $altMessage;
        return $this;
    }
    
   

    public function typeTemplateConfirm($url,$label = 'Go to project') {
        $newUrl = '';
        if ($url == '') {
            $this->template = '{
                "type": "text",
                "text": "' . $this->message . '"
            }';
        } else {
//            $reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
            $reg_exUrl = "/(http|https)\:\/\/?/";
            if (preg_match($reg_exUrl, $url)) {
                $newUrl = $url;
            } else {
                $http = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
                $newUrl = $http . $_SERVER['SERVER_NAME'] . $url;
            }
            $this->template = '{ 
                "type": "template", 
                "altText": "'.$this->altMessage.'", 
                "template": { 
                    "type": "confirm", 
                    "text": "'.$this->altMessage.'", 
                    "actions": [ 
                            { 
                            "type": "message", 
                            "label": " ", 
                            "text": " " 
                            }, 
                            { 
                            "type": "uri", 
                            "label": "'.$label.'", 
                            "uri": "' . $newUrl . '" 
                            } 
                    ] 
                } 
            }';
        }
        return $this;
    }

    public function typeText() {
        $this->template = '{
                "type": "text",
                "text": "' . $this->message . '"
            }';
        return $this;
    }

    public function template($template) {
        $this->template = $template;
        return $this;
    }

    public function token($token) {
        $this->token = $token;
        return $this;
    }

    public function secret_token($secret_token) {
        $this->secret_token = $secret_token;
        return $this;
    }

    /**
     * 
     * @return type
     */
    public function pushMessage($line_id = '') {
        try {
            if ($this->template == '') {
                $this->typeText();
            }
            $curl = curl_init();
            if (isset($line_id) && $line_id != '') {

                curl_setopt_array($curl, array(
                    CURLOPT_URL => "https://api.line.me/v2/bot/message/push",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
//CURLOPT_POSTFIELDS => "{\"to\":\"{$chatid}\",\"messages\":[{\"type\": \"text\",\"text\": {$messageline}}]}", 
                    CURLOPT_POSTFIELDS =>
                    '{ 
    "to": "' . $line_id . '", 
    "messages":[ 
        ' . $this->template . ' 
    ] 
}',
                    CURLOPT_HTTPHEADER => array(
                        "authorization: Bearer {$this->token}",
                        "cache-control: no-cache",
                        "content-type: application/json",
                        "postman-token: 92ca101e-dcc3-9f50-615d-76cffac0b616"
                    ),
                ));

                $response = curl_exec($curl);
                $errline = curl_error($curl);
                curl_close($curl);
                return ['error' => $errline, 'res' => $response];
            }
        } catch (\Exception $error) {
            EzfFunc::addErrorLog($error);
        }
    }

    public function replyMessage($replyToken = '') {
        try {
            if ($this->template == '') {
                $this->typeText();
            }
            $curl = curl_init();
            if (isset($replyToken) && $replyToken != '') {

                curl_setopt_array($curl, array(
                    CURLOPT_URL => "https://api.line.me/v2/bot/message/reply",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
//CURLOPT_POSTFIELDS => "{\"to\":\"{$chatid}\",\"messages\":[{\"type\": \"text\",\"text\": {$messageline}}]}", 
                    CURLOPT_POSTFIELDS =>
                    '{ 
    "replyToken": "' . $replyToken . '", 
    "messages":[ 
        ' . $this->template . ' 
    ] 
}',
                    CURLOPT_HTTPHEADER => array(
                        "authorization: Bearer {$this->token}",
                        "cache-control: no-cache",
                        "content-type: application/json",
                        "postman-token: 92ca101e-dcc3-9f50-615d-76cffac0b616"
                    ),
                ));

                $response = curl_exec($curl);
                $errline = curl_error($curl);
                curl_close($curl);
                return ['error' => $errline, 'res' => $response];
            }
        } catch (\Exception $error) {
            EzfFunc::addErrorLog($error);
        }
    }

    public function getProfile($line_id = '') {
        try {
            $curl = curl_init();
            if (isset($line_id) && $line_id != '') {

                curl_setopt_array($curl, array(
                    CURLOPT_URL => "https://api.line.me/v2/bot/profile/{$line_id}",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "GET",
//CURLOPT_POSTFIELDS => "{\"to\":\"{$chatid}\",\"messages\":[{\"type\": \"text\",\"text\": {$messageline}}]}", 
                    CURLOPT_POSTFIELDS => '',
                    CURLOPT_HTTPHEADER => array(
                        "authorization: Bearer {$this->token}",
                        "cache-control: no-cache",
                        "content-type: application/json",
                        "postman-token: 92ca101e-dcc3-9f50-615d-76cffac0b616"
                    ),
                ));

                $response = curl_exec($curl);
                $errline = curl_error($curl);
                curl_close($curl);
                return ['error' => $errline, 'res' => $response];
            }
        } catch (\Exception $error) {
            EzfFunc::addErrorLog($error);
        }
    }

}
