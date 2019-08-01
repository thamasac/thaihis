<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace common\modules\user\classes;
use dektrium\user\models\Token;
use dektrium\user\models\User;
 
class CNMail {
   public static function sendMail(User $user, Token $token){
        
       $email_to = isset($user->email) ? $user->email : '';
       $user_id = isset($user->id) ? $user->id : '';
       $main_url = \backend\modules\core\classes\CoreFunc::getParams('main_url', 'url');
       
       $data = (new \yii\db\Query())->select('code')->from('token')->where('user_id=:user_id',[':user_id'=>$user_id])->one();
       $data['code'] = isset($data['code']) ? $data['code'] : '';
       $url = \cpn\chanpan\classes\CNServerConfig::getProtocol();
       $domain = \cpn\chanpan\classes\CNServerConfig::getDomainName();
       $url = $url.$domain;
       $conf_url = "https://{$main_url}/user/registration/confirm?id={$user_id}&code={$data['code']}";
           
       
           
       $title = "Welcome to nCRC accounts.";
       $detail = \backend\modules\core\classes\CoreFunc::getParams('email_register', 'url');
       $modelForm = ['name'=> isset($user->profile->name) ? $user->profile->name : '', 'url'=> isset($conf_url) ? $conf_url : '', 'verify_text'=> isset($conf_url) ? $conf_url : ''];
       $path = [];
            foreach ($modelForm as $key => $value) {
                $path["{" . $key . "}"] = $value;
            }
       $d = strtr($detail, $path); 
       
        return \dms\aomruk\classese\Notify::setNotify()
                        ->notify($title)
                        ->detail($d)
                        ->SendMailTemplateRegistration($email_to);
   }
   public static function sendRecoveryMessage(User $user, Token $token){
       $main_url = \cpn\chanpan\classes\utils\CNDomain::getCurrentProjectUrl();// \backend\modules\core\classes\CoreFunc::getParams('main_url', 'url');
       
       $conf_url = "https://{$main_url}/user/recover/{$user['id']}/{$token['code']}";
       $detail = \backend\modules\core\classes\CoreFunc::getParams('email_recover', 'email');       
       $email_to = isset($user->email) ? $user->email : '';
       $title = "Complete password reset on nCRC";
       $modelForm = ['url'=>$conf_url];
       $path = [];
            foreach ($modelForm as $key => $value) {
                $path["{" . $key . "}"] = $value;
            }
       $d = strtr($detail, $path);
       //\appxq\sdii\utils\VarDumper::dump($d);
       $setForm=['ncrc.damasac@gmail.com' => "nCRC Recover your password"];
       return \dms\aomruk\classese\Notify::setNotify()
                        ->notify($title)
                        ->detail($d)
                        ->SendMailTemplateAll($email_to,$setForm);
 
   }
    
}
