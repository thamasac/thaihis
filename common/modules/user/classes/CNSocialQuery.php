<?php
 
namespace common\modules\user\classes;
use common\modules\user\models\User;
use common\modules\user\models\Profile;
use Yii;
use common\modules\user\models\Auth;
use common\modules\user\models\RegistrationForm;
class CNSocialQuery {
    /**
     *
     * @param type $user_id
     * @param type $email
     * @return array|User|null|\yii\db\ActiveRecord
     */
  public static function getUser($user_id,$email){
       if($email != ''){
           $user = User::find()->where('email=:email',[':email'=>$email])->one();
       }else{
           $user = User::findOne($user_id);
       }

       if(!empty($user)){
           return $user;
       }
   }
   /**
    * 
    * @param type $clientObj 
    * @return type
    */
   public static function getAuth($clientObj){
       $auth = Auth::find()->where([
           'source' => $clientObj['getId'],
           'source_id' => $clientObj['id'],
       ])->one();
       if(!empty($auth)){
           return $auth;
       }
   }  
    
   /**
    * 
    * @param type $data 
    * @return array $dataStats=['status'=>'success', 'id'=>'']
    */ 
   public static function saveUser($data){
       $dataStatus=['status'=>'error','id'=>''];
       try{           
           //$this->performAjaxValidation($model);
            $user = new User();
            $user->id = \appxq\sdii\utils\SDUtility::getMillisecTime();
            $user->username = date('YmdHis'). rand(0,10000).time();
            $user->password = Yii::$app->security->generateRandomString(12);
            $user->email = isset($data['email']) ? $data['email'] : '';
            $user->status=10;
            $user->created_at = time();
            $user->confirmed_at = time();
            $user->updated_at =time();
            $user->flags = 0;     
            $user->password_hash = Yii::$app->security->generateRandomString(12);      
            $user->auth_key = Yii::$app->security->generateRandomString();            
             
            if($user->save()){
                CNSocialQuery::saveProfile($data, $user->id);
                try{
                    $assignData = ['item_name'=>'author','user_id'=>$user->id, 'created_at'=> time()];
                    Yii::$app->db->createCommand()->insert('auth_assignment', $assignData)->execute();
                } catch (\yii\db\Exception $ex){
                    \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
                }
                $dataStatus=['status'=>'success','id'=>$user->id];
            }else{                
                $dataStatus['message']= json_encode($user->errors);
            }
            
       } catch (\yii\db\Exception $ex) {
            $dataStatus['message']= json_encode($ex->getMessage());
       }
       return $dataStatus;
   }
   /**
    * 
    * @param type $data array $data=['email'=>'', 'name'=>'']
    * @param type $user_id
    * @return boolean true/false
    */
   public static function saveProfile($data, $user_id){
       $profile = Profile::findOne($user_id);
       $profile->user_id = $user_id;
       $profile->name = isset($data['name']) ? $data['name'] : '';
       $profile->public_email = isset($data['email']) ? $data['email'] : '';
       $profile->gravatar_email = isset($data['email']) ? $data['email'] : '';
       $profile->dob = '00/00/0000';
       $profile->firstname = explode(' ', $data['name'])[0];
       $profile->lastname = explode(' ', $data['name'])[1];
       $profile->department = '00'; //$this->department,
       $profile->position = 0;
       $profile->sitecode = '00'; //$this->sitecode,
       if($profile->save()){           
           
           $user = User::findOne($user_id);
           return CNSocialFunc::autoLogin($user);           
       } 
   }
   /**
    * 
    * @param type $user_id string user id
    * @param type $clientObj array $clientObj=['getId'=>'', 'id'=>'']
    * @return boolean true/false
    */
   public static function saveAuth($user_id,$clientObj){
       $auth = new Auth();
       $auth->user_id = $user_id;
       $auth->source = $clientObj['getId'];
       $auth->source_id = (string)$clientObj['id'];        
       if($auth->save()){
           return TRUE;
       }
   }
   
   
}
