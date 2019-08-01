<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\controllers;

/**
 * Description of AccessModuleController
 *
 * @author Sammy Guergachi <sguergachi at gmail.com>
 */
use Yii;
use yii\web\Controller;
class AccessModuleController extends Controller{
    //put your code here
    public function actionAccessDenied(){
        $module_id = \Yii::$app->request->get('module_id');         
        return $this->render("access-denied", ['module_id'=>$module_id]);
    }
//    public function actionTestEmail(){
//        \backend\mail\classes\CNSendMail::SendMailTemplate('chanpan.nuttaphon1993@gmail.com','ทดสอบการส่ง email ครับ');
//        \backend\mail\classes\CNSendMail::SendMailNotTemplate('chanpan.nuttaphon1993@gmail.com','ทดสอบการส่ง email ครับ');
//    }
}
