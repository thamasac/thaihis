<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\modules\manageproject\controllers;
use Yii;
use yii\web\Controller;
class SendMailController extends Controller{
    public function actionIndex(){
        if(Yii::$app->request->isAjax){
            
            $post = Yii::$app->request->post('EZ1520249845081836400');
            $userIdArr = isset($post['user_id']) ? $post['user_id'] : '';
            $roleNameStr = isset($post['role_name']) ? $post['role_name'] : '';            
            $role_start = isset($post['role_start']) ? $post['role_start'] : '';
            $role_stop = isset($post['role_stop']) ? $post['role_stop'] : '';          
            $projectName = isset(\Yii::$app->params['model_dynamic']['proj_name']) ? \Yii::$app->params['model_dynamic']['proj_name'] : '';//\backend\modules\manageproject\classes\CNProject::getProjectName();
            $role = \backend\modules\manageproject\classes\CNRole::getRoleName($roleNameStr);
            foreach ($userIdArr as $key=>$value){
                $user = (new \yii\db\Query())
                        ->select(['u.email','p.firstname','p.lastname'])
                        ->from('user as u')
                        ->innerJoin('profile as p', 'u.id=p.user_id')
                        ->where(['u.id'=>$value])
                        ->one();
                
                $domain = \cpn\chanpan\classes\CNServerConfig::getDomainName();
                $name = isset(\Yii::$app->params['model_dynamic']['pi_name']) ? \Yii::$app->params['model_dynamic']['pi_name'] : '';
                $detail="
                    An invitation to join the project. <br/><br/> 
                    This notification  datailed specific invitation to join the project as below .<br/><br/> 
                    Project Title <b>{$projectName}</b><br/> 
                    Principal Investigator  <b>{$name}</b><br/>
                    Your Role is <b>{$role}</b><br/><br>
                    If you need more information, please directly contact to Principal Investigator.<br/><br/>
                    Best Regards,<br/>
                    <b>".Yii::$app->user->identity->profile->firstname.' '.Yii::$app->user->identity->profile->lastname."</b> 
                ";
                
                if(\Yii::$app->session['user_assign'] != null){
                    $user_session = \Yii::$app->session['user_assign'];
                    $user_session = explode(',', $user_session);
                    if(!in_array($value, $user_session)){
                        $send_mail = \dms\aomruk\classese\Notify::setNotify()
                            ->notify($projectName)
                            ->detail($detail)
                            ->SendMailTemplateNCRC($user['email']);
                    }
                }else{
                    $send_mail = \dms\aomruk\classese\Notify::setNotify()
                        ->notify($projectName)
                        ->detail($detail)
                        ->SendMailTemplateNCRC($user['email']);
                }
                
                
                
                    
                  
                
//                        ->notify($projectName)
//                        ->detail($detail)
//                        ->SendMailTemplateNCRC($user['email']); 
                
            }//foreach 
            
            
            
        }
    }
}
