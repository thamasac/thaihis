<?php

namespace backend\modules\manageproject\controllers;

use backend\modules\manageproject\classes\CNSettingProjectFunc;
use yii\db\Exception;
use backend\modules\ezforms2\classes\EzfFunc;
use backend\modules\manageproject\classes\CNCloneDb;
use yii\helpers\Html;
use Yii;
class CenterProjectController extends \yii\web\Controller {
    
    /* set attribute zdata_create_project */
    public function setDPAttribute($data_project, $url, $acronym, $project_name, $not_id = true) {
        $data = $data_project['data_create'];
        if ($not_id == true) {
            $id = \appxq\sdii\utils\SDUtility::getMillisecTime(); // gen id create project
            unset($data['id']); //delete ole id 
            $data['id'] = $id;  //create new id   
            $data['ptid'] = $id;  //create new id
            $data['target'] = $id;  //create new id
            $data['create_date'] = date('Y-m-d H:i:s');
        }
        $data['user_create'] = \cpn\chanpan\classes\CNUser::getUserId();
        $data['user_update'] = \cpn\chanpan\classes\CNUser::getUserId();
        $data['projurl'] = $url;
        $data['projectacronym'] = $acronym;
//        $data['useTemplate']    = $acronym;
        $data['projectname'] = $project_name;
        $data['update_date'] = date('Y-m-d H:i:s');
        return $data;
    }

    /* set dynamic_db attribute */
    public function setDyAttribute($data_dynamic, $data_project, $not_id = true) {
        $data = $data_dynamic;
        //return \yii\helpers\Json::encode($data);
        if ($not_id == true) {
            unset($data['id']); //delete ole id 
            $defaultDb = isset(\Yii::$app->params['default_db']) ? \Yii::$app->params['default_db'] : 'ncrc_';
            $data['config_db'] = "{$defaultDb}" . \cpn\chanpan\classes\utils\CNUtils::replaceString(\cpn\chanpan\classes\utils\CNUtils::strLowerCase($data_project['projectacronym'])) . "_" . date('YmdHis');
        }

        $data['project_template'] = $data['dbname']; //template name
        $data['dbname'] = $data['config_db'];
        $data['url'] = "{$data_project['projurl']}.{$data_project['projdomain']}";
        $data['data_id'] = $data_project['id'];
        $data['proj_name'] = $data_project['projectname'];
        $data['user_create'] = \cpn\chanpan\classes\CNUser::getUserId();
        $data['tctr_id'] = $data_project['id_tctr'];
        $data['pi_name'] = $data_project['pi_name'];
        $data['aconym'] = $data_project['projectacronym'];
        $data['create_at'] = date('Y-m-d H:i:s');
        $data['host'] = "localhost";
        return $data;
    }

    /* save zdata_create_project */
    public function saveProject($data, $dbname = '') {
        return \cpn\chanpan\classes\utils\CNProject::saveProject($data, '');
    }

    /* save zdata_create_project */
    public function saveDynamic($data) {
        return \cpn\chanpan\classes\utils\CNProject::saveProject($data, 'dynamic_db');
    }

    /* create project */
    public function actionCreateProject() {
        if (!\Yii::$app->request->isAjax) {
            return 'not found request';
        } 
        try {
            $data_id            = isset($_POST['id']) ? $_POST['id'] : '';
            $project_name       = isset($_POST['projname']) ? $_POST['projname'] : '';
            $acronym            = isset($_POST['acronym']) ? $_POST['acronym'] : '';
            $url                = isset($_POST['url']) ? $_POST['url'] : '';
            $change_icon        = isset($_POST['change_icon']) ? $_POST['change_icon'] : '';
            $type               = \Yii::$app->request->post('type', '');
                        
            $my_project = \cpn\chanpan\classes\utils\CNProject::getMyProjectById($data_id, 'db_main', 'query');
            if (empty($my_project)) {
                return;
            }
//            return \yii\helpers\Json::encode($my_project);
            //\appxq\sdii\utils\VarDumper::dump($my_project);
            /*check url ซ้ำ*/
            $model= \backend\modules\manageproject\models\CreateProject::find()->where(['projurl'=>$url])->one();
            if($model){
                return \backend\modules\manageproject\classes\CNMessage::getError("Specify URL {$url} has already been taken.");
            }
            
            //return \yii\helpers\Json::encode($my_project);
            $db_template = $my_project['data_dynamic']['config_db']; //template 
            $data_project = $this->setDPAttribute($my_project, $url, $acronym, $project_name, true);
            $data_project['change_icon'] = $change_icon;
            $data_project['projecticon'] = $change_icon;
            $data_dynamic = $this->setDyAttribute($my_project['data_dynamic'], $data_project, true);
            $dbName = $data_dynamic['config_db'];
            $create = false;
            
            
//            print_r($data_project);return; 
            /* save zdata_create_project and save dynamic_db */
            $forder = \cpn\chanpan\classes\utils\CNProject::generateForder($data_project['user_create']); 
            $data_project['forder'] = 0;         
            if ($this->saveProject($data_project, '') && $this->saveDynamic($data_dynamic)) {
                /* create database myproject */

                $create_db = CNCloneDb::createDatabaseByName($dbName);
                if ($create_db) {
                    
                    /* create table project all */
                    $create_tb_all = CNCloneDb::createTableAll($dbName, $db_template, $data_dynamic['aconym'], $type);
                    
                    //if ($create_tb_all) {
                         
                        /* update project */
                        $this->updateProject($data_project, $data_project['id'], '', $dbName);
//                        if($type == "clone"){
//                            $dataID = $data_project['id'];
//                            $dataProject = $data_project;
//                            unset($dataProject['id']);
//                            $sql= \Yii::$app->db->createCommand()->update('zdata_create_project', $dataProject, 'id=:id',[':id'=>$dataID])->execute();
//                        }
                        
                        /* delete user , sitecoe , matching */
                        $deleteUser = CNCloneDb::deleteUserNotAdmin($dbName);
                        $deleteMatching = CNCloneDb::deleteMatching($dbName);
                        $deleteSitecode = CNCloneDb::deleteSitecode($dbName);

                        /* check user */
                        $user_id = \cpn\chanpan\classes\CNUser::getUserId();
                        $user_check = \cpn\chanpan\classes\CNUser::checkUserDynamicDb($user_id, $dbName); //check user in project
                        if (!$user_check) {
                            $data_user = \cpn\chanpan\classes\CNUser::getUserNcrcById($user_id);
                            $data_user['profile']['sitecode'] = '00';
                            $data_user['profile']['line_id']="";
                            \cpn\chanpan\classes\CNUser::saveUser($data_user['user'], $dbName, 'user');
                            
                            if($data_user['profile']){
                                unset($data_user['profile']['last_sitecode']);
                                unset($data_user['profile']['status_update']);
                                unset($data_user['profile']['site_permission']);
                                unset($data_user['profile']['invite']);
                                unset($data_user['profile']['v_dept']);
                            } 
                            
                            \cpn\chanpan\classes\CNUser::saveUser($data_user['profile'], $dbName, 'profile');
                            \cpn\chanpan\classes\CNUser::saveRole('administrator', $user_id, $dbName);
                            
                            //save user , profile and role
                        }

                        /* set option config */
                        $dataOption = ['option_value' => 2];
                        $dataOption2 = ['option_value' => $acronym];
                        $dataOption3 = ['option_value' => ''];
                        \backend\modules\core\classes\CoreFunc::updateCoreOptionValueByName("step", $dataOption, $dbName);
                        \backend\modules\core\classes\CoreFunc::updateCoreOptionValueByName("company_name", $dataOption2, $dbName);
                        \backend\modules\core\classes\CoreFunc::updateCoreOptionValueByName('site_text', $dataOption3, $dbName);
                        unset(\Yii::$app->session['highlight']);
                    \Yii::$app->session['highlight'] = [
                        'data_id' => $data_project['id'],
                        'bg_color' => '#fff13b47',
                        'num' => 0
                    ];
                    if($type=="clone"){
                            $data_url = ['url'=>"https://{$data_dynamic['url']}", 'name'=>$data_dynamic['proj_name']];
                            return \backend\modules\manageproject\classes\CNMessage::getSuccessObj("Create project {$acronym} successfully", $data_url);
                        }
                        return \backend\modules\manageproject\classes\CNMessage::getSuccess("Create project {$acronym} successfully");
                    //}
                }
            } else {
                return \backend\modules\manageproject\classes\CNMessage::getError("Create project error");
            }
            return;
        } catch (Exception $ex) {
            EzfFunc::addErrorLog($ex);
            return \backend\modules\manageproject\classes\CNMessage::getError("Create project error");
        }
        return;
    }

    /* update zdata_create_project */
    public function updateProject($data, $id, $table = '', $dbname = '') {
        return \cpn\chanpan\classes\utils\CNProject::updateProject($data, $id, $table, $dbname);
    }

    /* update dynamicdb portal site */
    public function updateDynamic($data, $id) {
        return \cpn\chanpan\classes\utils\CNProject::updateProject($data, $id, 'dynamic_db');
    }

    /* update project */
    public function actionUpdate() {
        if (!\Yii::$app->request->isAjax) {
            return 'Not found request';
        }
        try {

            $dataid = \Yii::$app->request->post('id', '');
            $my_project = \cpn\chanpan\classes\utils\CNProject::getMyProjectById($dataid);
            
            if (!empty($my_project)) {
                $acronym = $my_project['data_create']['projectacronym'];
                $url = $my_project['data_create']['projurl'];
                $project_name = $my_project['data_create']['projectname'];
                $data_project = $this->setDPAttribute($my_project, $url, $acronym, $project_name, false);
                $data_dynamic = $this->setDyAttribute($my_project['data_dynamic'], $data_project, false);
                $db_name = $data_dynamic['dbname'];              
                $this->updateDynamic($data_dynamic, $data_dynamic['id']);
                if(!\cpn\chanpan\classes\utils\CNDomain::isPortal()){            
                   unset($data_project['useTemplate']);
                   
                   $db_arr = explode('dbname=', Yii::$app->db_main->dsn);
                   $dbname_default = end($db_arr);
                   
                   $udpate = \cpn\chanpan\classes\utils\CNProject::updateProject($data_project, $data_project['id'], '', $dbname_default);
                   $dataOption2 = ['option_value' => $acronym];
                   \backend\modules\core\classes\CoreFunc::updateCoreOptionValueByName("company_name", $dataOption2, $db_name);
                   return "https://{$data_dynamic['url']}/ezmodules/ezmodule/view?id=1521647584047559700";
                    
                }
                /* check url unique */
                //$url_change = "{$data_project['projurl']}.{$data_project['projdomain']}";
 
                /* save zdata_create and dynamic_db */
                if ($this->updateProject($data_project, $data_project['id'], '', $db_name) && $this->updateDynamic($data_dynamic, $data_dynamic['id'])) {
                    /* update core option */
                    $dataOption2 = ['option_value' => $acronym];
                    \backend\modules\core\classes\CoreFunc::updateCoreOptionValueByName("company_name", $dataOption2, $db_name);
                    if(!\cpn\chanpan\classes\utils\CNDomain::isPortal()){
                        return "https://{$data_dynamic['url']}/ezmodules/ezmodule/view?id=1521647584047559700";
                    }
                    return \backend\modules\manageproject\classes\CNMessage::getSuccess("Update project {$acronym} successfully");
                } else {
                    return \backend\modules\manageproject\classes\CNMessage::getError("Update project error");
                }
            } else {
                return \backend\modules\manageproject\classes\CNMessage::getError("Not found project");
            }
        } catch (Exception $ex) {
            EzfFunc::addErrorLog($ex);
            return \backend\modules\manageproject\classes\CNMessage::getError("Update project error");
        }


        return;
    }
    
    /*check url*/
    public function actionCheckUrl() {
        $url            = isset($_GET['url']) ? $_GET['url'] : '';
        $id             = isset($_GET['id']) ? $_GET['id'] : '';
        $action         = isset($_GET['action']) ? $_GET['action'] : '';
        $status         = 0;
        $myproject      = \cpn\chanpan\classes\utils\CNProject::getMyProjectById($id);
        $data_url       = $url;
        $url            = "{$url}.{$myproject['data_create']['projdomain']}";
        
        $found_url = \cpn\chanpan\classes\utils\CNProject::checkRequireUrl($id, $url, $action);        
        if ($found_url) {
            $status     = 1;//มี url แล้ว
        }
        $checkString    = \cpn\chanpan\classes\utils\CNUtils::searchString($url, "_");
        
        if ($checkString != false) {
            $status     = 3;//เครื่องหมาแปลๆ
        }       
        $checkString    = \cpn\chanpan\classes\utils\CNUtils::checkLanguageNotThai($data_url);
        //\appxq\sdii\utils\VarDumper::dump($checkString);
        if ($checkString) {
            $status     = 4; //มีภาษาไทยปน
        }
         
        return $status;
    }

    /*check acronym*/
    public function actionAcronyml() {
        $acronym = isset($_GET['acronym']) ? $_GET['acronym'] : '';
        $id = isset($_GET['id']) ? $_GET['id'] : '';
        $action = isset($_GET['action']) ? $_GET['action'] : '';
        $status = 0;
        if (\cpn\chanpan\classes\utils\CNProject::checkAcronym($id, $acronym, $action)) {
            $status = 1;
        }
        return $status;
    }

    /*delete*/
    public function actionDelete() {
        if (!\Yii::$app->request->isAjax) {
            return 'Not found request';
        }        
        try {
            $dataid                     = isset($_POST['id']) ? $_POST['id'] : '';
            $my_project                 = \cpn\chanpan\classes\utils\CNProject::getMyProjectById($dataid);  
            $data_project               = $my_project['data_create'];            
            $data_project['projurl']    = "del" . \appxq\sdii\utils\SDUtility::getMillisecTime();
            $data_project['rstat']      = "3";            
            $data_dynamic               = $my_project['data_dynamic'];
            $data_dynamic['rstat']      = "3";
            $data_dynamic['url_change'] = $data_dynamic['url'];
            $data_dynamic['url']        = "{$data_project['projurl']}.{$data_project['projdomain']}";
            
            $dynamic_id                 = $data_dynamic['id'];
            $proj_id                    = $data_project['id'];
            unset($data_project['id']);
            unset($data_dynamic['id']);
            
            $update_project             = $this->updateProject($data_project, $proj_id, '', '');
            $update_dynamic             = $this->updateDynamic($data_dynamic, $dynamic_id);
             
            if ($update_project && $update_dynamic) {
                return \backend\modules\manageproject\classes\CNMessage::getSuccess("Delete successfully");
            } else {
                return \backend\modules\manageproject\classes\CNMessage::getError("Delete error");
            }
        } catch (Exception $ex) {
            EzfFunc::addErrorLog($ex);
            return \backend\modules\manageproject\classes\CNMessage::getError("Update project error");
        }
    }
    /*delete*/
    public function actionDestroy() {
        if (!\Yii::$app->request->isAjax) {
            return 'Not found request';
        }        
        try {
            $dataid = isset($_POST['id']) ? $_POST['id'] : '';           
            if (\cpn\chanpan\classes\utils\CNProject::desTroyProjectById($dataid)) {
                return \backend\modules\manageproject\classes\CNMessage::getSuccess("Permanently delete successfully");
            } else {
                return \backend\modules\manageproject\classes\CNMessage::getError("Permanently delete error");
            }
        } catch (Exception $ex) {
            EzfFunc::addErrorLog($ex);
            return \backend\modules\manageproject\classes\CNMessage::getError("Permanently delete error");
        }
    }

    /*restore*/
    public function actionRestore() {
        if (!\Yii::$app->request->isAjax) {
            return 'Not found request';
        }        
        try {
            $dataid                     = isset($_POST['id']) ? $_POST['id'] : '';
            $my_project                 = \cpn\chanpan\classes\utils\CNProject::getDeleteMyProjectById($dataid);  
 
            $data_project               = $my_project['data_create'];            
            $data_project['projurl']    = "re".\appxq\sdii\utils\SDUtility::getMillisecTime();
            $data_project['rstat']      = "1";            
            $data_dynamic               = $my_project['data_dynamic'];
            $data_dynamic['rstat']      = "1";
            $data_dynamic['url']        = "{$data_project['projurl']}.{$data_project['projdomain']}";
            
            $dynamic_id                 = $data_dynamic['id'];
            $proj_id                    = $data_project['id'];
            unset($data_project['id']);
            unset($data_dynamic['id']);
            
            $update_project             = $this->updateProject($data_project, $proj_id, '', '');
            $update_dynamic             = $this->updateDynamic($data_dynamic, $dynamic_id);
             
            if ($update_project && $update_dynamic) {
                return \backend\modules\manageproject\classes\CNMessage::getSuccess("Restore successfully");
            } else {
                return \backend\modules\manageproject\classes\CNMessage::getError("Restore error");
            }
        } catch (Exception $ex) {
            EzfFunc::addErrorLog($ex);
            return \backend\modules\manageproject\classes\CNMessage::getError("Update project error");
        } 
    }

    /* form upload images */
    public function actionImageUpload() {
        $image = isset($_POST['image']) ? $_POST['image'] : '';
        if ($image == '') {
            $image = "/img/health-icon.png";
        }

        return $this->renderAjax("image-upload", [
                    'image' => $image
        ]);
    }
    
    /*get view form update project*/
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
              $button       .=  Html::button("<i class='fa fa-refresh'></i> ".\Yii::t('project','Restore'),['data-action'=>'restore','data-id'=>$dataid,'data-url'=> \yii\helpers\Url::to(["/manageproject/center-project/restore?id={$dataid}"]),'class'=>'btn btn-xs btn-info btnProject']).' ';
              $button       .=  Html::button("<i class='fa fa-trash'></i> ".\Yii::t('project','Permanently delete'),['data-action'=>'destroy','data-id'=>$dataid,'data-url'=> \yii\helpers\Url::to(["/manageproject/center-project/destroy?id={$dataid}"]),'class'=>'btn btn-xs btn-danger btnProject']).' ';
            }else{      
                $button     .=  Html::button("<i class='fa fa-trash'></i> ".\Yii::t('project','Delete'),['data-action'=>'delete','data-id'=>$dataid,'data-url'=> \yii\helpers\Url::to(["/manageproject/center-project/delete?id={$dataid}"]),'class'=>'btn btn-xs btn-danger btnProject']).' ';
                //$button     .=  Html::button("<i class='fa fa-wrench'></i> ".\Yii::t('project','Repair'),['data-action'=>'update','data-id'=>$dataid,'data-url'=> \yii\helpers\Url::to(["/manageproject/setting-project/repair?id={$dataid}"]),'class'=>'btn btn-xs btn-warning btnProject']).' ';
                //$button     .=  Html::button("<i class='fa fa-clone'></i> ".\Yii::t('project','Clone'),['data-action'=>'clone','data-id'=>$dataid,'data-url'=> \yii\helpers\Url::to(["/manageproject/monitor-project/clone"]),'class'=>'btn btn-xs btn-info btnProject']).' ';
                $button     .=  Html::button("<i class='fa fa-refresh'></i> ".\Yii::t('project','Backup'),['data-action'=>'backup','data-id'=>$dataid,'data-url'=> \yii\helpers\Url::to(["/manageproject/backup-restore/backup"]),'class'=>'btn btn-xs btn-default btnProject']).' ';
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
    //Request for discontinuation
    public function actionRequestForDiscontinuation(){
        //\appxq\sdii\utils\VarDumper::dump(\cpn\chanpan\classes\utils\CNProject::getMyProject());
        $project_id = \Yii::$app->request->post('id', '');
        $project_type = \Yii::$app->request->post('project_type', '');
        $user_id    = isset(\Yii::$app->user->id) ? \Yii::$app->user->id : '1';
        \Yii::$app->session['project_type'] = $project_type; //10 = assign 20 co creator
        $model      = \backend\modules\manageproject\models\Discontinuatios::find()
                ->where(['project_id'=>$project_id, 'user_id'=>$user_id])->one();

        if($model){
            $userProject = \cpn\chanpan\classes\utils\CNProject::getUserProject($user_id);
            $output = [];
            foreach($userProject as $k=>$up){
                $myProject = \cpn\chanpan\classes\utils\CNProject::getMyProjectById($up['data_id']);
                //\appxq\sdii\utils\VarDumper::dump($myProject);
                $dataCreate = $myProject['data_create'];
                $projectName = $dataCreate['projectname'];
                $projectAcronym = $dataCreate['projectacronym'];
                $projectId      = $dataCreate['id'];
                $icon           = $dataCreate['projecticon'];
                $user_create    = $dataCreate['user_create'];
                
                $dataDis = \cpn\chanpan\classes\utils\CNProject::getDisconByProjectId($dataCreate['id'], $user_id);
                $descriptions = isset($dataDis['descriptions']) ? $dataDis['descriptions'] : '';
                $status = isset($dataDis['status']) ? $dataDis['status'] : '';
                $project_type = isset($dataDis['project_type']) ? $dataDis['project_type'] : '';
                
                $output[$k] = [
                    'project_id'=>$projectId,
                    'project_name'=>$projectName,
                    'project_acronym'=>$projectAcronym,
                    'descriptions'=>$descriptions,
                    'status'=>$status,
                    'icon'=>$icon,
                    'project_type'=>$project_type, //assign to me co creator
                    'project_type_text'=> \Yii::t('project', 'Assigned to me')    
                ];
 
            }
            $dataProvider = new \yii\data\ArrayDataProvider([
                'allModels' => $output,
                'sort' => [
                    //'attributes' => ['project_id', 'project_name', 'project_acronym', 'descriptions', 'status','project_type'],
                ],
                'pagination' => [
                    'pageSize' => 10,
                ],
            ]);
            //\appxq\sdii\utils\VarDumper::dump($dataProvider);
            return $this->renderAjax("view-discontinuation",['dataProvider'=>$dataProvider]);
        }//found project id
        
        if(!$model){
           $model      = new \backend\modules\manageproject\models\Discontinuatios(); 
        } 
        $myProject = \cpn\chanpan\classes\utils\CNProject::getMyProjectById($project_id);
        
        $projectName = isset($myProject['data_dynamic']['proj_name']) ? $myProject['data_dynamic']['proj_name'] : '';
        $projectAconym = isset($myProject['data_dynamic']['aconym']) ? $myProject['data_dynamic']['aconym'] : '';
         
        if($model->load(\Yii::$app->request->post())){ 
            $post   = \Yii::$app->request->post(); 
            $model->id     = \appxq\sdii\utils\SDUtility::getMillisecTime();
            $model->descriptions = $post['Discontinuatios']['descriptions'];
            $model->project_type = $post['Discontinuatios']['project_type'];;
            $model->project_id =   $post['Discontinuatios']['project_id'];;
            $model->user_id = $user_id;
            $model->status = 10;
            $myProject = \cpn\chanpan\classes\utils\CNProject::getMyProjectById($model->project_id);
            $this->sendEmail($model->descriptions, $myProject, $model->user_id);
            
            
            $user = \common\modules\user\models\User::findOne($user_id);
            $user->block();
            $profile = \common\modules\user\models\Profile::findOne($user_id);
            $profile->invite = 4;
            $profile->save();
            
            if($model->save()){
                return \cpn\chanpan\classes\CNResponse::getSuccess(\Yii::t('project', 'Discontinuation requested'));
            }else{
                return \cpn\chanpan\classes\CNResponse::getError($model->errors);
            }
            
        }
        return $this->renderAjax('request-for-discontinuation', [
            'model'=>$model,
            'project_id'=>$project_id,
            'project_type'=>$project_type,
            'project_name'=>$projectName,
            'project_aconym'=>$projectAconym
        ]);
    }
    public function sendEmail($detail, $myProject, $userId){
        $dataCreate = $myProject['data_create']; 
        $user_create    = $dataCreate['user_create'];
        $userCreate = \cpn\chanpan\classes\CNUser::getUserNcrcById($user_create);
        $myUser = \cpn\chanpan\classes\CNUser::getUserNcrcById($userId);
        $email = $userCreate['user']['email'];
        
        $dataCreate = $myProject['data_create'];
        $projectName = $dataCreate['projectname'];
        $projectAcronym = $dataCreate['projectacronym'];
        
        $my_name = "{$myUser['profile']['firstname']} {$myUser['profile']['firstname']}";
        $my_email = "{$myUser['user']['email']}";
        $my_sitecode = "{$myUser['profile']['sitecode']}"; 
        $my_sitecode = \common\modules\user\classes\CNSitecode::getSiteValue($my_sitecode);
        $tel = $myUser['profile']['tel'];
        $template = \backend\modules\core\classes\CoreFunc::getParams('template_request_for_discontinuation', 'x');
       
        $modelForm = ['detail'=>$detail,'name'=>$my_name, 'tel'=>$tel, 'sitecode'=>$my_sitecode, 'project_name'=>$projectName, 'project_acronym'=>$projectAcronym];
        $path = [];
        foreach ($modelForm as $key => $value) {
            $path["{" . $key . "}"] = $value;
        }
        $title = \Yii::t('project','Request for discontinuation');
        $template = strtr($template, $path); 
        try{
            return \dms\aomruk\classese\Notify::setNotify()
                    ->send_email(true)
                    ->notify($title)
                    ->detail($template)
                    ->SendMailTemplate($email, true);
        } catch (\yii\db\Exception $ex) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
        }
         
    }
    
    /* set user assign ment*/
    public function actionSetUserAssign(){
        $id = Yii::$app->request->get('id');
        $ezf_id = Yii::$app->request->get('ezf_id');
        $data = (new \yii\db\Query())->select('*')->from('ezform')->where('ezf_id=:id',[':id'=>$ezf_id])->one();
        if($data){
            $ezf_table = isset($data['ezf_table'])?$data['ezf_table']:'';
            $data_matching = (new \yii\db\Query())->select(['user_id'])->from("`{$ezf_table}`")->where('id=:id',[':id'=>$id])->one();
            $data_user = \appxq\sdii\utils\SDUtility::string2Array($data_matching['user_id']);  
            $user = join(',', $data_user); 
            Yii::$app->session['user_assign']=$user;
            return $user;
        }
        return false;
    }
    
    /**
    * 
    * @param type $url | String
    * @param type $data_id | Bigint id zdata_create_project
    * @param type $db | Database name
    * @return type boolean true|false
    */
    private function update_data_create($url,$data_id, $db=''){
       return \backend\modules\manageproject\classes\CNSettingProjectQuery::update_url_for_dynamic_db($url, $data_id, $db);
    }
    public function actionUpdateProject(){
        $id = Yii::$app->request->post('id');
        $url = Yii::$app->request->post('url');
        $data_project = \cpn\chanpan\classes\utils\CNProject::getMyProjectById($id); //get data project 
        
        $db_name = isset($data_project['data_dynamic']['dbname'])?$data_project['data_dynamic']['dbname']:''; 
        $data_create = $data_project['data_create'];
        $url = isset($data_create['projurl']) ? $data_create['projurl'] : '';
        $domain = isset($data_create['projdomain']) ? $data_create['projdomain'] : '';
        $projectacronym = isset($data_create['projectacronym']) ? $data_create['projectacronym'] : '';
        $pi_name = isset($data_create['pi_name']) ? $data_create['pi_name'] : '';
        try {
            //is not portal
            $dynamic = '';
            if(!\cpn\chanpan\classes\CNServerConfig::isPortal()){
                $dynamic = \Yii::$app->db_main->createCommand();
            }else{
                \cpn\chanpan\classes\CNServerConfig::get_dynamic_db_by_dbname($db_name);
                $dynamic = \Yii::$app->db_dynamic->createCommand();
            }
            if (isset($data_create['id'])) {
                unset($data_create['id']);
            }
            if (isset($data_create['co_creator'])) {
                unset($data_create['co_creator']);
            }
            $dynamic->update('zdata_create_project', $data_create, "id={$id}")->execute();

            $sql = "
                UPDATE dynamic_db SET url=:url , aconym=:aconym ,pi_name=:pi_name WHERE data_id=:data_id
            ";
            $params = [
                ':url' => "{$url}.{$domain}",
                ':aconym' => $projectacronym,
                ':pi_name'=>$pi_name,
                ':data_id'=>$id        
            ];
            
            $data = \Yii::$app->db_main->createCommand($sql, $params)->execute();  
            //update company_name
            $dataOption = ['option_value' => $projectacronym]; 
            if(!\cpn\chanpan\classes\CNServerConfig::isPortal()){ //project
                \backend\modules\core\classes\CoreFunc::updateCoreOptionValueByName("company_name", $dataOption, '');
            }else{
                \backend\modules\core\classes\CoreFunc::updateCoreOptionValueByName("company_name", $dataOption, $db_name);
            }
            

            return \backend\modules\manageproject\classes\CNMessage::getSuccess("Success");
                
        } catch (Exception $ex) {
            EzfFunc::addErrorLog($ex);
            return \backend\modules\manageproject\classes\CNMessage::getError("Error");
        }
    }
}
