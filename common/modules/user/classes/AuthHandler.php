<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace common\modules\user\classes;

use common\modules\user\models\Auth;
use common\modules\user\models\User;
use Yii;
use yii\authclient\ClientInterface;
use yii\helpers\ArrayHelper;
use common\modules\user\classes\CNSocialFunc;
use appxq\sdii\utils\VarDumper;
use yii\helpers\Url;

class AuthHandler {
 /**
     * @var ClientInterface
     */
    private $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function handle()
    {  
        
        $attributes = $this->client->getUserAttributes();
        $email = ArrayHelper::getValue($attributes, 'email');
        $id = ArrayHelper::getValue($attributes, 'id');
        $username = ArrayHelper::getValue($attributes, 'name');
        $data=[];
        $clientObj = ['id'=>$id, 'getId'=> $this->client->getId()];
        if($this->client->getId() == 'facebook'){
            $data['name']=$attributes['name'];
            $data['email']=$attributes['email'];
            $data['id']=$attributes['id'];
        }else if($this->client->getId() == 'google'){
            $data['name']=$attributes['name'];
            $data['email']=$attributes['email'];
            $data['image']=$attributes['image']['url'];
        }
        $auth = CNSocialFunc::checkAuth($clientObj);
        if (\Yii::$app->user->isGuest) {            
            $user = CNSocialFunc::checkUser('', $data['email']);
            if($user->blocked_at != null || $user->blocked_at != ''){
               $msg = \Yii::t('user','Your account has been blocked');
               throw new \yii\base\UserException($msg);
            }
            if($user){
                    $user->confirm();
                    CNSocialFunc::autoLogin($user);
            }else{
                    CNSocialFunc::saveUser($data, $clientObj);
            }
            $inviteInfo = new InvitationInfo();
            $inviteInfo->initFromSession();

            if(isset(Yii::$app->session['line_id']) && !empty(Yii::$app->session['line_id']) && !\Yii::$app->user->isGuest){
                $userProfile = \common\modules\user\models\Profile::find()->where('user_id=:uid', [':uid'=>\Yii::$app->user->id])->one();
                if($userProfile){
                    $userProfile->line_id = Yii::$app->session['line_id'];
                    if($userProfile->save()){
                        unset(Yii::$app->session['line_id']);
                    }
                }
            }
            
            if($inviteInfo->isHasInvite){
                Yii::$app->response->redirect(Url::to(["/user/registration/apply-invitation?email=". $inviteInfo->email ."&token=" . $inviteInfo->token . "&project_id=" . $inviteInfo->project_id]), 301)->send();
            }
        }

    }//handle

    /**
     * @param User $user
     */
    private function updateUserInfo(User $user)
    {
        $attributes = $this->client->getUserAttributes();
        $github = ArrayHelper::getValue($attributes, 'login');
        if ($user->github === null && $github) {
            $user->github = $github;
            $user->save();
        }
    }
}
