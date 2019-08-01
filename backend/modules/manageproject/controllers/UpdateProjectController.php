<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\modules\manageproject\controllers;

/**
 * Description of UpdateProjectController
 *
 * @author Sammy Guergachi <sguergachi at gmail.com>
 */
use yii\helpers\Html;
class UpdateProjectController  extends \yii\web\Controller{
   public function actionUpdateDataForm() {
      $post = \Yii::$app->request->post();
      if(!empty($post)){
          $post = isset($post['EZ1523071255006806900']) ? $post['EZ1523071255006806900'] : '';
          
          $data=[
              'projectname'=>isset($post['projectname']) ? $post['projectname'] : '',
              'useTemplate'=>isset($post['useTemplate']) ? $post['useTemplate'] : '',
              'id_tctr'=>isset($post['id_tctr']) ? $post['id_tctr'] : '',
              'pi_name'=>isset($post['pi_name']) ? $post['pi_name'] : '',
              'projecticon'=>isset($post['projecticon']) ? $post['projecticon'] : '', //*
              'briefsummary'=>isset($post['briefsummary']) ? $post['briefsummary'] : '',
              'detail'=>isset($post['detail']) ? $post['detail'] : '',
              'tctrno'=>isset($post['tctrno']) ? $post['tctrno'] : '',
              'dataid'=>isset($post['id']) ? $post['id'] : '',
              'proj_home'=>isset($post['proj_home']) ? $post['proj_home'] : '',
              'sharing'=>isset($post['sharing']) ? $post['sharing'] : '',
          ];
          
          return \backend\modules\manageproject\classes\CNSettingProjectFunc::UpdateProject($data);
          
      }
       
   }
   
   public function actionUpdateDataFormBackend() {
       $post = \Yii::$app->request->post();
       if(!empty($post)){
            $post = isset($post['EZ1523071255006806900']) ? $post['EZ1523071255006806900'] : '';
            $dataid=isset($post['id']) ? $post['id'] : '';
            $data=(new \yii\db\Query())
                    ->select('*')
                    ->from('zdata_create_project')
                    ->where('id=:id',[':id'=>$dataid])
                    ->one();
            $id = $data['id'];
            unset($data['id']);
            $dataSaveCreateProject = \Yii::$app->db_main->createCommand()
                    ->update('zdata_create_project', $data, ['id'=>$id])
                    ->execute();
            if($dataSaveCreateProject){
               return \backend\modules\manageproject\classes\CNMessage::getSuccess('success');
           }else{
               return \backend\modules\manageproject\classes\CNMessage::getError('error');
           }
       }
   }
   public function actionViewFormUpdateProject(){
        $dataid             = isset($_GET['id']) ? $_GET['id'] : '';
        $status             = isset($_GET['status']) ? $_GET['status'] : '';
        
        $myproject          = [];
        if($status == 'delete'){
            $myproject      = \cpn\chanpan\classes\utils\CNProject::getDeleteMyProjectById($dataid, '');
             
        }else{
            $myproject      = \cpn\chanpan\classes\utils\CNProject::getMyProjectById($dataid, '');
        }     
        if(!empty($myproject['data_create'])){
            $myproject      = $myproject['data_create'];             
            $button         = '';
            if($myproject['rstat'] == '3')
            {
              $button       .=  Html::button("<i class='fa fa-refresh'></i> ".\Yii::t('project','Restore'),['data-action'=>'restore','data-id'=>$dataid,'data-url'=> \yii\helpers\Url::to(["/manageproject/setting-project/restore?id={$dataid}"]),'class'=>'btn btn-xs btn-info btnProject']).' ';
              $button     .=  Html::button("<i class='fa fa-trash'></i> ".\Yii::t('project','Permanently delete'),['data-action'=>'delete','data-id'=>$dataid,'data-url'=> \yii\helpers\Url::to(["/manageproject/setting-project/delete?id={$dataid}"]),'class'=>'btn btn-xs btn-danger btnProject']).' ';
            }else{      
                $button     .=  Html::button("<i class='fa fa-trash'></i> ".\Yii::t('project','Delete'),['data-action'=>'delete','data-id'=>$dataid,'data-url'=> \yii\helpers\Url::to(["/manageproject/center-project/delete?id={$dataid}"]),'class'=>'btn btn-xs btn-danger btnProject']).' ';
                $button     .=  Html::button("<i class='fa fa-wrench'></i> ".\Yii::t('project','Repair'),['data-action'=>'update','data-id'=>$dataid,'data-url'=> \yii\helpers\Url::to(["/manageproject/setting-project/repair?id={$dataid}"]),'class'=>'btn btn-xs btn-warning btnProject']).' ';
                $button     .=  Html::button("<i class='fa fa-clone'></i> ".\Yii::t('project','Clone'),['data-action'=>'clone','data-id'=>$dataid,'data-url'=> \yii\helpers\Url::to(["/manageproject/monitor-project/clone"]),'class'=>'btn btn-xs btn-info btnProject']).' ';
                $button     .=  Html::button("<i class='fa fa-refresh'></i> ".\Yii::t('project','Backup'),['data-action'=>'backup','data-id'=>$dataid,'data-url'=> \yii\helpers\Url::to(["/manageproject/monitor-project/clone"]),'class'=>'btn btn-xs btn-defaut btnProject']).' ';
            }
            
            return $this->renderAjax('view-form-update-project',[
                'id'=>$dataid,
                'status'=>'update',
                'button'=>$button,
                'rstat'=>$myproject['rstat'],
                'data'=>$myproject
            ]);
        }

    }
    
    /* save zdata_create_project and dynamic_db*/
    public function actionUpdateDynamicDb(){
      try{
          $post = \Yii::$app->request->post();
           
      if(!empty($post)){
            $post = isset($post['EZ1523071255006806900']) ? $post['EZ1523071255006806900'] : '';          
            /*get myproject*/
            $dynamic_db = \cpn\chanpan\classes\utils\CNProject::getMyProject();
           
            if(!empty($dynamic_db)){
              $id = $dynamic_db['data_dynamic']['id'];            
              $url = isset($post['projurl']) ? $post['projurl'] : '';
              $domain = isset($post['projdomain']) ? $post['projdomain'] : '';
              $isUrl = \cpn\chanpan\classes\utils\CNProject::checkRequireUrl($id,"{$url}.{$domain}");
              //print_r($isUrl);return;
              if($isUrl){
                  return;
              } 
              $data=[
                //'url_change'=>$dynamic_db['data_dynamic']['url'],
                //'url'=> "{$url}.{$domain}",
                'proj_name'=>isset($post['projectname']) ? $post['projectname'] : '',               
                'tctr_id'=>isset($post['id_tctr']) ? $post['id_tctr'] : '',
                'pi_name'=>isset($post['pi_name']) ? $post['pi_name'] : '',              
                'aconym'=>isset($post['projectacronym']) ? $post['projectacronym'] : '',
              ];
              $dataCompany = ['option_value'=>$data['aconym']];
              \backend\modules\core\classes\CoreFunc::updateCoreOptionValueByNameInDb('company_name', $dataCompany);
            //save dynamic_dbs
                $saveDynamic=\cpn\chanpan\classes\utils\CNProject::updateProject($data, $id, 'dynamic_db'); 
                
                $dataid = $dynamic_db['data_create']['id'];
                $myProject = \cpn\chanpan\classes\utils\CNProject::getMyProjectById($dataid);
                
                if(!empty($myProject)){
                    $myProject=$myProject['data_create'];
                    unset($myProject['id']);
                    $myProject['user_create']= \cpn\chanpan\classes\CNUser::getUserId();
                    $saveDynamic=\cpn\chanpan\classes\utils\CNProject::updateProject($myProject, $dataid, ''); 
                    if($saveDynamic){ 
                        $out = ['status'=>'success', 'url'=>"{$url}.{$domain}"];
                        return json_encode($out);
                    }
                }
                
                 
            }else{
                return \backend\modules\manageproject\classes\CNMessage::getError("error");
            }          
        }
      } catch (\yii\db\Exception $ex) {
          \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
          return \backend\modules\manageproject\classes\CNMessage::getError($ex->getMessage());
      }
        
      return;
    }
}
