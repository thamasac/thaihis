<?php
 
namespace common\modules\user\classes;
use common\modules\user\models\User;
use common\modules\user\models\Profile;
use Yii;
use common\modules\user\models\Auth;
class CNSocialFunc {
   //1
    /**
     * 
     * @param type $clientObj array
     * @return type Object data auth
     */
   public static function checkAuth($clientObj){
       $auth = CNSocialQuery::getAuth($clientObj);
       return $auth;
   }    
   //2
   /**
    * 
    * @param $user_id string user_id
    * @param $email   string email address
    * @return \dektrium\user\models\User
    */
   public static function checkUser($user_id, $email=''){
       $user = CNSocialQuery::getUser($user_id, $email);
       return $user;
   }
   
   /**
    * 
    * @param type $user object user
    * @return type
    */
   public static function autoLogin($user){
       return \Yii::$app->user->login($user);
   }

   /**
    * 
    * @param type $data array user
    * @param type $clientObj array
    */
   public static function saveUser($data, $clientObj){
       try{
           $user = CNSocialQuery::saveUser($data);
           //\appxq\sdii\utils\VarDumper::dump($user);
            if($user['status'] == 'success'){                
                CNSocialQuery::saveAuth($user['id'], $clientObj);
            }
       } catch (\yii\db\Exception $ex) {
            
       }
   }
 
   
   
}
