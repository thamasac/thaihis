<?php

namespace common\modules\user\controllers;


use common\modules\user\classes\InvitationInfo;
use dektrium\user\models\LoginForm;
use dektrium\user\controllers\SecurityController as BaseSecurityController;
use Yii;
use yii\helpers\Html;

use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class SecurityController extends BaseSecurityController
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    ['allow' => true, 'actions' => ['login', 'auth'], 'roles' => ['?']],
                    ['allow' => true, 'actions' => ['login', 'auth', 'logout'], 'roles' => ['@']],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['get'],
                ],
            ],
        ];
    }

    public function actionLogout()
    {
        \backend\modules\manageproject\classes\CNFunc::addLog('Logout');
        if(!\cpn\chanpan\classes\CNServerConfig::isLocal() && !\cpn\chanpan\classes\CNServerConfig::isPortal()){
            $portal_url = isset(Yii::$app->params['project_setup_portal_url'])?Yii::$app->params['project_setup_portal_url']:'';
            $is_http = isset(Yii::$app->params['project_setup_ishttps'])?Yii::$app->params['project_setup_ishttps']:'';//
            $http = 'http://';
            if($is_http == '1'){
                $http='https://';
            }
            $url = "{$http}{$portal_url}/user/security/logout";
            $this->performLogout();
            return $this->redirect($url);

        }
        
        $this->performLogout();
        if(\cpn\chanpan\classes\CNServerConfig::isLocal()){
            return $this->goHome();
        }
        if (\cpn\chanpan\classes\utils\CNDomain::isPortal()) {
            $redirect = Yii::$app->request->get("redirect",null);            
            $frontent_url = \cpn\chanpan\classes\utils\CNDomain::isFrontend();             
            if($redirect != null && $redirect == "login"){
                $token = Yii::$app->request->get("token",null);
                $email = Yii::$app->request->get("email",null);
                $project_id = Yii::$app->request->get("project_id",null);
                $url = "/user/security/login?email=". $email ."&token=".$token."&project_id=".$project_id;
                return $this->redirect($url);
            }else if ($frontent_url != '') {
                return "<script>location.href='" . $frontent_url . "';</script>";
            } 
            return $this->refresh();
        } else {            
            //\appxq\sdii\utils\VarDumper::dump(\cpn\chanpan\classes\utils\CNDomain::isHttps());
            if (\cpn\chanpan\classes\utils\CNDomain::isHttps()) {
                $url = \cpn\chanpan\classes\utils\CNDomain::getPortalFullUrl();                 
                return "<script>location.href='" . $url . "';</script>";
            } else {
                return $this->redirect(['/user/login']);
            }
        }

        // return $this->goHome();
    }

    public function actionFrontendLogout()
    {
        $this->performLogout();
        return $this->refresh();
        // return $this->goHome();
    }

    public function actionLogin()
    { 
        if (!\Yii::$app->user->isGuest) {
            $this->goHome();
        }
        $line_id = isset($_GET['lid'])?$_GET['lid']:'';
        Yii::$app->session['line_id'] = $line_id;
        
        /** @var LoginForm $model */
        $model = \Yii::createObject(LoginForm::className());
        $event = $this->getFormEvent($model);
        $inviteInfo = new InvitationInfo();
        $inviteInfo->init();
        $this->performAjaxValidation($model);

        $this->trigger(self::EVENT_BEFORE_LOGIN, $event);

        if ($model->load(\Yii::$app->getRequest()->post()) && $model->login()) {
//             $auty_key= ::GetAuthKey();
//            if(!empty($auty_key)){
//                $key="damasac!@#$%";
//                $auty_key = \backend\modules\manageproject\classes\CNCryptography::EncryptOpenssl($auty_key, $key);
//                \backend\modules\topic\classes\CNCookie::SetCookie('chanpanClone', $auty_key, '', false, true);
//                \backend\modules\topic\classes\CNCookie::SetCookie('chanpanStatus', 1, '', false, true);
//            }
            
            if(isset(Yii::$app->session['line_id']) && !empty(Yii::$app->session['line_id']) && !\Yii::$app->user->isGuest){
                $userProfile = \common\modules\user\models\Profile::find()->where('user_id=:uid', [':uid'=>\Yii::$app->user->id])->one();
                if($userProfile){
                    $userProfile->line_id = Yii::$app->session['line_id'];
                    if($userProfile->save()){
                        //unset(Yii::$app->session['line_id']);
                    }
                }
            }
            
            $this->trigger(self::EVENT_AFTER_LOGIN, $event);
            // if login and has token redirect to apply
            
            \backend\modules\manageproject\classes\CNFunc::addLog('Login');
            
            if ($inviteInfo->isHasInvite) {
                return $this->redirect("/user/registration/apply-invitation?token=".$inviteInfo->token."&project_id=".$inviteInfo->project_id. "&email=" . $inviteInfo->email);
            }
            return $this->goBack();
        }
        // For use in google / facebook login
        if ($inviteInfo->isHasInvite) {
            $inviteInfo->saveToSession();
        }
        return $this->render('login', [
            'model' => $model,
            'module' => $this->module,
        ]);

    }


    public function performLogout()
    {
        \backend\modules\topic\classes\CNCookie::RemoveCookie("chanpanClone");
        \backend\modules\topic\classes\CNCookie::RemoveCookie("chanpanStatus");
        $event = $this->getUserEvent(\Yii::$app->user->identity);

        $this->trigger(self::EVENT_BEFORE_LOGOUT, $event);

        \Yii::$app->getUser()->logout();

        $this->trigger(self::EVENT_AFTER_LOGOUT, $event);
    }

}
