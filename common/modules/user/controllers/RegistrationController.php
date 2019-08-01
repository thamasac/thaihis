<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace common\modules\user\controllers;

use common\modules\user\classes\InvitationInfo;
use common\modules\user\models\User;
use dektrium\user\controllers\RegistrationController as BaseRegistrationController;
use common\modules\user\models\RegistrationForm;
use yii\helpers\Url;
use yii\httpclient\Client;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use Yii;
/**
 * RegistrationController is responsible for all registration process, which includes registration of a new account,
 * resending confirmation tokens, email confirmation and registration via social networks.
 *
 * @property \dektrium\user\Module $module
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class RegistrationController extends BaseRegistrationController
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    ['allow' => true, 'actions' => ['connect'], 'roles' => ['?']],
                    ['allow' => true, 'actions' => ['register','confirm', 'resend','apply-invitation'], 'roles' => ['?', '@']],
                ]
            ],
        ];
    }

    public function actionRegister()
    {
        $inviteInfo = new InvitationInfo();
        $inviteInfo->init();
        if($inviteInfo->isHasInvite && !Yii::$app->user->isGuest ){
            $url = Url::to(['/user/registration/apply-invitation',  'email' => $inviteInfo->email, 'token' => $inviteInfo->token,'project_id' => $inviteInfo->project_id]);
            return $this->redirect($url);
        }
        
        if(!Yii::$app->user->isGuest){
            return $this->redirect(['/user/login']);
        }
        
        
        $urlMain = \backend\modules\core\classes\CoreFunc::getParams('main_url', 'url'); 
        $domain = \cpn\chanpan\classes\CNServerConfig::getDomainName();
         
         if(!\cpn\chanpan\classes\CNServerConfig::isLocal() && $domain != $urlMain && !\cpn\chanpan\classes\CNServerConfig::isTest() && !\cpn\chanpan\classes\CNServerConfig::isPortal()){
             $backendRegister = 'https://'.$urlMain.'/user/register';
             return $this->redirect($backendRegister);
         }
//         \appxq\sdii\utils\VarDumper::dump('ok');

        if (!$this->module->enableRegistration) {
            throw new NotFoundHttpException;
        }

        $line_id = isset($_GET['lid'])?$_GET['lid']:'';
        Yii::$app->session['line_id'] = $line_id;
        
        $model = \Yii::createObject(RegistrationForm::className());
        $this->performAjaxValidation($model);

        if ($model->load(\Yii::$app->request->post())) {

            $username = isset($_POST['register-form']['username']) ? $_POST['register-form']['username'] : '';
            $email = isset($_POST['register-form']['email']) ? $_POST['register-form']['email'] : '';
            
            if(\common\modules\user\classes\CNUserFunc::checkUser('username', $username)){                
                \Yii::$app->session->setFlash('error', Yii::t('user', 'This username has already been taken'));
            }            
            if(\common\modules\user\classes\CNUserFunc::checkUser('email', $email)){
                \Yii::$app->session->setFlash('error', Yii::t('user', 'This email address has already been taken'));
            }
//            \Yii::$app->session->setFlash("invitation","1");
            if($model->register()){
                \Yii::$app->session->removeFlash("invitation");


                //get user id by username
                if($inviteInfo->isHasInvite){
                    $user_id = Yii::$app->db->createCommand("SELECT id FROM user where username = :username",[":username" => $model->username])->queryScalar();
                    if(!$user_id){
                        return false;
                    }

                    $redirect = self::applyInvitationToProject($inviteInfo->token,$user_id,$inviteInfo->project_id);
                    if(get_class ($redirect) ==  "yii\web\Response")
                        return $redirect;
                    if(!$redirect || isset($redirect["message"])) {
                        return isset($redirect["message"]) ? $redirect["message"]:"Error : Project may not found";
                    }
                    $projectName = Yii::$app->db->createCommand("SELECT projectname FROM zdata_create_project WHERE id= :id",[":id" => $inviteInfo->project_id])->queryScalar();
                    if(!$projectName) $projectName = "Unknow project";
                    return $this->render("confirmed",["redirect"=>$redirect,"projectName"=>$projectName]);
                }else{
                    return $this->render('messages', [
                        'email'=>$model->email
                    ]);
                }
            }else{
                \Yii::$app->session->remove("invitation");
            }
        }

        $email = Yii::$app->request->get("email" , null);
        if($email !=null)
            $model->email = $email;
        return $this->render('register', [
            'model'  => $model,
            'inviteInfo'  => $inviteInfo,
            'module' => $this->module,
        ]);
    }
    
    public function actionConfirm($id, $code)
    {
        $user = $this->finder->findUserById($id);

        if ($user === null || $this->module->enableConfirmation == false) {
            throw new NotFoundHttpException();
        }

        $event = $this->getUserEvent($user);

        $this->trigger(self::EVENT_BEFORE_CONFIRM, $event);

        $user->attemptConfirmation($code);

        $this->trigger(self::EVENT_AFTER_CONFIRM, $event);
        return $this->redirect(['/site/index']);
//        return $this->render('/message', [
//            'title'  => \Yii::t('user', 'Account confirmation'),
//            'module' => $this->module,
//        ]);
    }
    
    public function actionResend()
    {
        if ($this->module->enableConfirmation == false) {
            throw new NotFoundHttpException();
        }

        /** @var ResendForm $model */
        $model = \Yii::createObject(\common\modules\user\models\ResendForm::className());
        $event = $this->getFormEvent($model);

        $this->trigger(self::EVENT_BEFORE_RESEND, $event);

        $this->performAjaxValidation($model);

        if ($model->load(\Yii::$app->request->post())) {
            
            if($model->resend()){
                $this->trigger(self::EVENT_AFTER_RESEND, $event);

                return $this->render('/message', [
                    'title'  => \Yii::t('user', 'A new confirmation link has been sent'),
                    'module' => $this->module,
                ]);
            }
            
        }

        if(\Yii::$app->request->isAjax){
            return $this->renderAjax('resend-ajax', [
                'model' => $model,
            ]);
        }else{
            return $this->render('resend', [
                'model' => $model,
            ]);
        }
    }

    function actionApplyInvitation(){
        if(Yii::$app->user->isGuest)
            return $this->goBack();
        $inviteInfo = new InvitationInfo();
        $inviteInfo->init();
        $projectName = Yii::$app->db->createCommand("SELECT projectname FROM zdata_create_project WHERE id= :id",[":id" => $inviteInfo->project_id])->queryScalar();
        if(!$projectName) $projectName = "Unknow project";
        if(Yii::$app->request->isPost){
            $reject = Yii::$app->request->post("reject-invite");
            $reject = isset($reject) ? 1 : 0;
            $redirect = self::applyInvitationToProject($inviteInfo->token,Yii::$app->user->id,$inviteInfo->project_id,$reject);
            if(get_class ($redirect) ==  "yii\web\Response")
                return $redirect;
            if(!$redirect || isset($redirect["message"])) {
                return isset($redirect["message"]) ? $redirect["message"]:"Error : Project may not found";
            }
            return $this->render("confirmed",["redirect"=>$redirect,"projectName"=>$projectName]);
//            return $this->redirect($redirect);
        }

        return $this->render("apply-invite",["projectName"=>$projectName , "inviteInfo"=>$inviteInfo]);
    }

    function applyInvitationToProject($token,$user_id,$project_id,$reject = 0)
    {
        $projectData = \backend\modules\manageproject\classes\CNEzform::getDynamicTableAll("zdata_create_project", ["id" => $project_id]);
        if($projectData == null){
            // TODO cause no project found
            return false;
        }
        $projectData = $projectData[0];
        $projectUrl = $projectData["projurl"];
        $projDomain = $projectData["projdomain"];

        $userData = Yii::$app->db->createCommand("SELECT * FROM user WHERE id = :user_id", [":user_id" => $user_id])->queryOne();
        $userProfileData = Yii::$app->db->createCommand("SELECT * FROM profile WHERE user_id = :user_id", [":user_id" => $user_id])->queryOne();

        $package = [
            "userData" => json_encode($userData),
            "userProfileData" => json_encode($userProfileData),
            "reject" => $reject,
            "token" => $token
        ];
        $url = "https://" . $projectUrl . "." . $projDomain . "/api/ncrc-project/apply-invite-project";
        $client = new Client();
        $response = $client->createRequest()
            ->setMethod('POST')
            ->setUrl($url)//.$url
            ->setData($package)
            ->send();
        if ($response->isOk) {
            $res = $response->getData();
            if($res["success"]){
                if($res["action"] == "reject"){
                    return $this->goBack();
                }
                // Add project for user.
                $data = [
                    'url'=> \cpn\chanpan\classes\CNServerConfig::getDomainName(),
                    'user_id'=>$user_id,
                    'create_by'=> "1",
                    'create_at'=>Date('Y-m-d'),
                    'data_id'=> $project_id
                ];
                \Yii::$app->db_main->createCommand()->insert('user_project', $data)->execute();
                return "https://" . $projectUrl . "." . $projDomain;
            }else{
                return $res;
            }
        }else{
            return false;
        }
    }


}
