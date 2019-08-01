<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\modules\api\v1\controllers;

use Yii;
use yii\web\Response;
use yii\db\Query;
use backend\modules\api\v1\classes\PurifyData;
use backend\modules\api\v1\classes\PurifyProject;

/**
 * Description of DataController
 *
 * @author chaiwat
 */
class DataController extends \yii\web\Controller {
    //put your code here
    private $active;
    private $user_id;
    private $sitecode;


    private function checkVersionAuthen(){
        $request = Yii::$app->request;
        $lg = new PurifyData();
        $lg->token = $request->get('token');                        // อนาคตจะใช้การเข้ารหัส 2 ทางเพิ่ม
        $lg->userProfile();
        $this->user_id = $lg->data['id'];
        $this->sitecode = $lg->data['sitecode'];
        $p = new PurifyData();
        $p->application = $request->get('application');
        $p->v = $request->get('v');
        $p->s = $request->get('s');
        $p->token = $request->get('token');
        $p->checkVersionAuthen();
        $this->active = $p->active;                   // ผ่านการตรวจสอบ
    }
  
    
    public function actionIndex()
    {
        header("Access-Control-Allow-Origin: *");
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $data['username'] = Yii::$app->request->getAuthUser();
        $data['passwords'] = Yii::$app->request->getAuthPassword();
        $query = (new Query())
                ->select(['username', 'id', 'email', 'password_hash', 'auth_key'])
                ->from('user')
                ->where(['username' => $data['username']]);
            $resUser = $query->one();
        if ($resUser) {
            if (Yii::$app->getSecurity()->validatePassword($data['passwords'], $resUser['password_hash'])) {
                $data['login_status'] = 'Success';
            }else{
                $data['login_status'] = 'Fail';
            }
        }
        $data['application'] = $request->get('application');
        //$data['application'] = $request->post('application');
        $data['x'] = 11;
        return $data;
    }
    
    public function actionLogin()
    {   
        header("Access-Control-Allow-Origin: *");
        Yii::$app->response->format = Response::FORMAT_JSON;
        if( strlen(Yii::$app->request->get('application'))>0 ){
            $lg = new PurifyData();
            $lg->login();
            $data = $lg->data;
        }else{
            $data = [
                'access' => 'error',
            ];
        }
        return $data;
    }
    
    public function actionUserProfile()
    {   
        header("Access-Control-Allow-Origin: *");
        $request = Yii::$app->request;
        Yii::$app->response->format = Response::FORMAT_JSON;
        if( strlen(Yii::$app->request->get('application'))>0 ){
            $lg = new PurifyData();
            $lg->token = $request->get('token');                        // อนาคตจะใช้การเข้ารหัส 2 ทางเพิ่ม
            $lg->userProfile();
            $data = $lg->data;
        }else{
            $data = [
                'access' => 'error',
            ];
        }
        return $data;
    }
    
    
    // project
    
    // module
    
    // Table
    
    // Data
    
    
    // Project 
    public function actionProjDet(){
        header("Access-Control-Allow-Origin: *");
        $request = Yii::$app->request;
        Yii::$app->response->format = Response::FORMAT_JSON;
        $this->checkVersionAuthen();
        if( $this->active ){
            $pj = new PurifyProject();
            $pj->url = $request->get('url');
            $pj->user_id = $this->user_id;
            $pj->projectDetail();
            $data = $pj->data;
        }
        return $data;
    }
    
    // data-table
    public function actionProj(){
        
        header("Access-Control-Allow-Origin: *");
        $request = Yii::$app->request;
        Yii::$app->response->format = Response::FORMAT_JSON;
        $this->checkVersionAuthen();
        if( $this->active ){
            $pj = new PurifyProject();
            $pj->user_id = $this->user_id;  
            $pj->hsitecode = $this->sitecode;
            $pj->sec = $request->get('sec');
            $pj->url = $request->get('url');
            $pj->ezf_id = $request->get('ezf');
            $pj->table = $request->get('t');        // ใช้เฉพาะ ldtb
            $pj->page = $request->get('p');
            $pj->purify_session = $request->get('usr');     // user session
            $pj->purify_token = $request->get('token');
            $pj->projSection();
            $data = $pj->data;
        }
        return $data;
    }
    
}
