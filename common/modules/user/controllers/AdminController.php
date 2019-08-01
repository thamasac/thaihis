<?php

namespace common\modules\user\controllers;

use cpn\chanpan\classes\utils\CNProject;
use dektrium\user\controllers\AdminController as BaseAdminController;
use dms\aomruk\classese\Notify;
use yii\helpers\Url;
use common\modules\user\models\UserSearch;
use yii\helpers\ArrayHelper;
use Yii;
use common\modules\user\classes\AdminClasses;
use common\modules\user\models\Profile;
use common\modules\user\models\User;
use yii\data\ActiveDataProvider;

class AdminController extends BaseAdminController {

    public function actionIndex() {
        if (Yii::$app->request->isAjax) {
            Url::remember('', 'actions-redirect');
            //$searchModel = \Yii::createObject(UserSearch::className());
            //$dataProvider = $searchModel->search(\Yii::$app->request->get());   
           
            //$table_name = $query->modelClass::tableName();
//            if ($this->created_at !== null) {
//                $date = strtotime($this->created_at);
//                $query->andFilterWhere(['between', $table_name . '.created_at', $date, $date + 3600 * 24]);
//            }
            //$query->andFilterWhere(['like', $table_name . '.username', $query->username]);
    //              ->andFilterWhere(['like', $table_name . '.email', $this->email])
    //              ->andFilterWhere([$table_name . '.id' => $this->id])
    //                ->andFilterWhere(['like', 'profile.firstname', $this->firstname])
    //                ->andFilterWhere(['like', 'profile.lastname', $this->lastname])
    //                ->andFilterWhere(['like', 'profile.sitecode', $this->sitecode])
    //              ->andFilterWhere([$table_name . 'registration_ip' => $this->registration_ip]);
            if(!empty($_GET)){
                $serach_name = Yii::$app->request->get('serach_name','');
                $fromdate = Yii::$app->request->get('fromdate','');
                $todate = Yii::$app->request->get('todate','');
                $dataSearch=['search_name'=>$serach_name,'fromdate'=> $fromdate ,'todate'=>$todate];
                $dataProvider = \common\modules\user\classes\CNAdmin::queryUser($dataSearch);
            }else{
                $dataProvider = \common\modules\user\classes\CNAdmin::queryUser();
            }
            return $this->renderAjax('index', [
                        'dataProvider' => $dataProvider,
                        //'searchModel' => $searchModel,
            ]);
        }  
    }

    /* Active Status */

    public function actionStatus($id) {
        AdminClasses::GetResponse();
        if ($id == \Yii::$app->user->identity->getId()) {
            return AdminClasses::GetError($id);
        } else {
            $user = $this->findModel($id);
            if ($user->status == 1) {
                $user->updateAttributes(['status' => 0]);
                return AdminClasses::GetSuccess($id, '<strong><i class="glyphicon glyphicon-ok-sign"></i> Success!</strong> ' . Yii::t('user', 'User has been disabled.'));
            } else {
                $user->updateAttributes(['status' => 1]);
                return AdminClasses::GetSuccess($id, '<strong><i class="glyphicon glyphicon-ok-sign"></i> Success!</strong> ' . Yii::t('user', 'User has been active.'));
            }
        }
    }
    
    public function actionVerified() {
        $id = \Yii::$app->request->post('id', '');
        $model = User::findOne($id);
        if($model && Yii::$app->user->can('administrator')){
            $model->confirmed_at = time();
            if($model->save()){
                return \cpn\chanpan\classes\CNResponse::getSuccess("Success");
            }
             return \cpn\chanpan\classes\CNResponse::getError("Error {$model->errors}");
        }
    }

    /* Manager */

    public function actionManager($id, $auth) {
        AdminClasses::GetResponse();
        $user = '';
        $setAuth = '';
        $getAuth = \Yii::$app->authManager->getAssignment($auth, $id);
        $roles_db = ArrayHelper::map(Yii::$app->authManager->getRoles(), 'name', 'description');

        if ($id == \Yii::$app->user->identity->getId()) {
            return AdminClasses::GetError($id);
        } else {
            $user = $this->findModel($id);
            $authorRole = Yii::$app->authManager->getRole($auth);
            Yii::$app->authManager->revoke($authorRole, $id);
            $modelProfile = \common\modules\user\models\Profile::find()
                            ->where('user_id=:id', [':id' => $id])->one();
            $userProfile = \Yii::$app->user->identity->profile;
            //$modelComment = new \common\modules\user\models\UserLog();
            //$modelComment->user_id = $id;
            //$modelComment->name = $userProfile->firstname . ' ' . $userProfile->lastname;
            //$modelComment->action = $modelProfile->approved;
            if (isset($getAuth->roleName)) {
                //$modelComment->comment = "ยกเลิกสิทธิ์ {$roles_db[$auth]}";
            } else {
                $setAuth = Yii::$app->authManager->assign($authorRole, $id);
                //$modelComment->comment = "ให้สิทธิ์ {$roles_db[$auth]}";
            }
            //$modelComment->save();
            return AdminClasses::GetSuccess($id);
        }
    }


    public function actionCollaboratorsView() {
        return $this->renderAjax('collaborators_view');
    }

    public function actionCollaboratorApproveView($id) {

        if (Yii::$app->request->isPost) {
            $url = CNProject::getCurrentProjectUrl();
            $method = $_REQUEST['method'];
            $data = Yii::$app->db->createCommand("SELECT profile_data,from_site,firstname,lastname,user_data,var_text FROM zdata_project_join_request WHERE id = :id", [":id" => $id])
                ->queryOne();
            $userData = json_decode($data["user_data"],true);
            $profileData = json_decode($data["profile_data"],true);
            $cNUserForm = Yii::$app->request->post("CNUserForm" , null);

            // 0=accept , 1=denied
            switch ($method){
                case "1":
                    $cause = Yii::$app->request->post("cause" , null);
                    Yii::$app->db->createCommand()->update("zdata_project_join_request", ["permission"=>2],"id = :id",[":id"=>$id])->execute();
                    $message = "Denied completed.";
                    $projectName = Yii::$app->params["company_name"];
                    $messageNoti = "<h3>Your collaboration request is denied.</h3>";
                    Notify::setNotify()->send_email(true)->notify("Collaboration Request")->detail("$projectName $messageNoti $cause")->SendMailTemplate($profileData["public_email"]);
                    // notify
                    break;
                case "0":
                    //save user and profile
                    $profileData["sitecode"] = $cNUserForm["sitecode"];
                    $profileData["department"] = $cNUserForm["department"];
                    Yii::$app->db->createCommand()->insert("user", $userData)->execute();
                    Yii::$app->db->createCommand()->insert("profile", $profileData)->execute();
                    $auth = Yii::$app->authManager;
                    $authorRole = $auth->getRole('author');
                    $auth->assign($authorRole, $userData["id"]);

                    $auth = Yii::$app->authManager;
                    $authorRole = $auth->getRole('author');
                    $auth->assign($authorRole, $userData["id"]);
                    Yii::$app->db->createCommand()->update("zdata_project_join_request", ["permission"=>1],"id = :id",[":id"=>$id])->execute();
                    $message = "Approve completed.";
                    $projectName = Yii::$app->params["company_name"];
                    $messageNoti = "<h3>Your collaboration request is Approved.</h3><a href='$url'>$url</a>";
                    Notify::setNotify()->send_email(true)->notify("Collaboration Request")->detail("$projectName $messageNoti")->SendMailTemplate($profileData["public_email"]);

                    // notify
                    break;
            }
            $result = [
                'status' => 'success',
                'action' => 'create',
                'message' => \appxq\sdii\helpers\SDHtml::getMsgSuccess() . Yii::t('chanpan', $message),
            ];
            return json_encode($result);
        }else{
            return $this->renderAjax('collaborator_approve',["id"=>$id]);
        }
    }

    public function actionValidationForm() {
        $user = \Yii::createObject([
                    'class' => User::className(),
                    'scenario' => 'create',
        ]);
        $profile = new Profile();
        $profile->department = 00;
        $profile->position = 0;
        if (Yii::$app->request->isPost && Yii::$app->request->isAjax) {
            $profile->load(Yii::$app->request->post());
            $user->load(Yii::$app->request->post());
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $resultUser = \yii\bootstrap\ActiveForm::validate($user);
            $resultProfile = \yii\bootstrap\ActiveForm::validate($profile);

            $result = ArrayHelper::merge($resultUser, $resultProfile);
            return $result;
        }
    }

    public function actionCreate() {
        /** @var User $user */
        $user = \Yii::createObject([
                    'class' => User::className(),
                    'scenario' => 'create',
        ]);
        $profile = new Profile();
        $profile->position = 0;
        //$event = $this->getUserEvent($user);

        //$this->trigger(self::EVENT_BEFORE_CREATE, $event);
        $auth_str = \common\modules\user\classes\SiteCodeFunc::getAuthList();
        if (($user->load(\Yii::$app->request->post()) && $user->validate()) || ($profile->load(\Yii::$app->request->post()) && $profile->validate())) {
           
            if ($user->create()) {
               
                $auth_assign = isset($_POST['Profile']['auth_str']) ? $_POST['Profile']['auth_str'] : "";
                $this->InsertAssignRole($auth_assign, $user->id); 
                \cpn\chanpan\classes\CNUser::saveUserProject($user->id);
                //$this->trigger(self::EVENT_AFTER_CREATE, $event);                
                $dataProfile=[
                    'department' => isset($_POST['Profile']['department']) ? $_POST['Profile']['department'] : '00',
                    'sitecode'=>isset($_POST['Profile']['sitecode']) ? $_POST['Profile']['sitecode'] : '00',
                    'firstname'=>isset($_POST['Profile']['firstname']) ? $_POST['Profile']['firstname'] : '',
                    'lastname'=>isset($_POST['Profile']['lastname']) ? $_POST['Profile']['lastname'] : '',
                    'tel'=>isset($_POST['Profile']['tel']) ? $_POST['Profile']['tel'] : '0',
                    'position'=>'0',
                    'user_type'=>'1'
                ];
                $statusUpdateProfile= \Yii::$app->db->createCommand()
                        ->update('profile', $dataProfile, ['user_id'=>$user->id])->execute();
                if($statusUpdateProfile){
                    $domain = \cpn\chanpan\classes\CNServerConfig::getDomainName();
                    $main_url = \backend\modules\core\classes\CoreFunc::getParams('main_url', 'url');
                    if($domain != $main_url){
                        $data = (new \yii\db\Query())->select('*')->from('user')->where('id=:id', [':id'=>$id])->one();
                        \Yii::$app->db_main->createCommand()->insert('user', $data)->execute();
                        \Yii::$app->db_main->createCommand()->insert('profile', $dataProfile)->execute();
                    }
                    return \backend\modules\manageproject\classes\CNMessage::getSuccess(Yii::t('chanpan', 'Save user completed.'));
                }else{
                    return \backend\modules\manageproject\classes\CNMessage::getError(Yii::t('chanpan', 'Can not create the user.'));
                }
               
            }
        }

        return $this->renderAjax('create', [
                    'user' => $user,
                    'profile' => $profile,
                    'auth_str' => $auth_str
        ]);
    }

    /**
     * Updates an existing User model.
     * @param integer $id
     * @return mixed
     */
    public function InsertAssignRole($auth_assign, $user_id) {
        try {

            foreach ($auth_assign as $value) {
                $auth_assign = \Yii::$app->db->createCommand()
                                ->insert('auth_assignment', [
                                    'item_name' => $value,
                                    'user_id' => $user_id,
                                    'created_at' => date('Y-m-d H:i:s')
                                ])->execute();
            }
            return $auth_assign;
        } catch (\yii\db\Exception $e) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
        }
    }

    public function DeleteAssignRole($user_id) {
        try {

            $auth_assign = \Yii::$app->db->createCommand()
                    ->delete('auth_assignment', ['user_id' => $user_id])
                    ->execute();
            return $auth_assign;
        } catch (\yii\db\Exception $e) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
        }
    }

    public function actionValidateAjax() {
        $model = new Profile();
        $model->position = 0;
        if (\Yii::$app->request->isAjax && !\Yii::$app->request->isPjax) {
            if ($model->load(\Yii::$app->request->post())) {
                \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                echo json_encode(\yii\bootstrap\ActiveForm::validate($model));
                \Yii::$app->end();
            }
        }
    }

    public function actionUpdateProfile($id) {

        Url::remember('', 'actions-redirect');
        $user = $this->findModel($id);
        $profile = $user->profile;
        $modelFields = \backend\modules\core\classes\CoreQuery::getAllOptionsTable('profile');

        if ($profile == null) {
            $profile = \Yii::createObject(Profile::className());
            $profile->link('user', $user);
        }
        $event = $this->getProfileEvent($profile);

        //$this->performAjaxValidation($profile);

        $this->trigger(self::EVENT_BEFORE_PROFILE_UPDATE, $event);

        $auth_str = \common\modules\user\classes\SiteCodeFunc::getAuthList();
        $profile->auth_str = \common\modules\user\classes\SiteCodeFunc::getAuthAssign($id);

        if ($profile->load(\Yii::$app->request->post())) {

            if (!empty($_FILES['Profile']['name']['secret_file'])) {
                $profile->secret_file = AdminClasses::UploadFiles($profile, "secret_file");
            }
            if (!empty($_FILES['Profile']['name']['citizenid_file'])) {
                $profile->citizenid_file = AdminClasses::UploadFiles($profile, "citizenid_file");
            }

            $this->DeleteAssignRole($profile->user_id);
            $this->InsertAssignRole($_POST['Profile']['auth_str'], $profile->user_id);
            $profile->tel = isset($_POST['Profile']['tel']) ? $_POST['Profile']['tel'] : '';
            $profile->site_permission = isset($_POST['Profile']['site_permission']) ? $_POST['Profile']['site_permission'] : '';

            if ($profile->update()) {
                //\Yii::$app->getSession()->setFlash('success', \Yii::t('user', 'Profile details have been updated'));
                $this->trigger(self::EVENT_AFTER_PROFILE_UPDATE, $event);
                //return $this->refresh();
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                $result = [
                    'status' => 'success',
                    'action' => 'create',
                    'message' => \appxq\sdii\helpers\SDHtml::getMsgSuccess() . Yii::t('chanpan', 'Update profile completed.'),
                ];
                return $result;
            } else {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                $result = [
                    'status' => 'success',
                    'action' => 'create',
                    'message' => \appxq\sdii\helpers\SDHtml::getMsgSuccess() . Yii::t('chanpan', 'Update profile completed.'),
                ];
                return $result;
            }
        }
        if (Yii::$app->request->isAjax) {

            return $this->renderAjax('_profile', [
                        'user' => $user,
                        'model' => $profile,
                        'modelFields' => $modelFields,
                        'auth_str' => $auth_str
            ]);
        }
        return $this->render('_profile', [
                    'user' => $user,
                    'model' => $profile,
                    'modelFields' => $modelFields,
                    'auth_str' => $auth_str
        ]);
        \dektrium\user\models\User::AFTER_CONFIRM();
    }

     

    public function actionDelete($id) {
        $model = $this->findModel($id);
        $event = $this->getUserEvent($model);
        $this->trigger(self::EVENT_BEFORE_DELETE, $event);
        
        $this->trigger(self::EVENT_AFTER_DELETE, $event);
        if($model->delete() && Profile::findOne($id)->delete()){
            \common\modules\user\classes\CNUserQuery::deleteUserProject($id); 
            
            
            if(!\cpn\chanpan\classes\CNServerConfig::isPortal()){
                $project = isset(Yii::$app->params['my_project']) ? Yii::$app->params['my_project'] : "";
                if ($project['data_dynamic']) {
                    $project_id = $project['data_dynamic']['data_id'];
                    $project_id = $project['data_dynamic']['data_id'];
                    $sql = "DELETE FROM discontinuatios WHERE project_id=:project_id AND user_id=:user_id";
                    $delete_discontinuations = \Yii::$app->db_main->createCommand($sql, [':project_id' => $project_id, ':user_id' => $id])->execute();
                }
            }

            \Yii::$app->db->createCommand()->delete('auth_assignment', ['user_id'=>$id])->execute();            
            return \backend\modules\manageproject\classes\CNMessage::getSuccess("Delete user completed.");
        }else{
            return \backend\modules\manageproject\classes\CNMessage::getError("Server Error!");
        }  
    }
    public function actionDeleteAll() {
        $id = Yii::$app->user->id;
        
        $data = isset($_POST['data']) ? $_POST['data'] : '';
        $status = [];
        if(!empty($data)){           
            foreach($data as $k=>$v){
                if($v['id'] == $id){
                    array_push($status, 0);
                }else{
                    $model = $this->findModel($v['id']);
                    $event = $this->getUserEvent($model);
                    $this->trigger(self::EVENT_BEFORE_DELETE, $event);
                    $this->trigger(self::EVENT_AFTER_DELETE, $event);
                    if($model->delete() && Profile::findOne($v['id'])->delete()){
                        \common\modules\user\classes\CNUserQuery::deleteUserProject($v['id']);
                        \Yii::$app->db->createCommand()->delete('auth_assignment', ['user_id'=>$v['id']])->execute();
                        
                        array_push($status, 1);
                    }else{
                        array_push($status, 0);
                    }  
                }
                
            }
            
            if(in_array(0, $status)){
                return \backend\modules\manageproject\classes\CNMessage::getError("Server Error!");
            }else{
                return \backend\modules\manageproject\classes\CNMessage::getSuccess("Delete user completed.");
            }
             
        }
      
    }
    public function setStatusDisConnect($userId, $dataCreate, $status){
        $projectName = isset($dataCreate['projectname']) ? isset($dataCreate['projectname']) : '';
        $projectAcronym = isset($dataCreate['projectacronym']) ? $dataCreate['projectacronym'] : '';
        $projectId = isset($dataCreate['id']) ? $dataCreate['id'] : '';
        $icon = isset($dataCreate['projecticon']) ? $dataCreate['projecticon'] : '';
        $user_create = isset($dataCreate['user_create']) ? $dataCreate['user_create'] : '';
        if ($projectId) {
            if($status == "10"){
                $sql="DELETE FROM discontinuatios WHERE user_id=:user_id AND project_id=:project_id";
                $params = [':user_id'=>$userId, ':project_id'=>$projectId];
                return \Yii::$app->db_main->createCommand($sql, $params)->execute();
            }
            $sql="UPDATE discontinuatios SET status =:status WHERE user_id=:user_id AND project_id=:project_id";
            $params = [':status'=>$status, ':user_id'=>$userId, ':project_id'=>$projectId];
            return \Yii::$app->db_main->createCommand($sql, $params)->execute();
//            $model = \backend\modules\manageproject\models\Discontinuatios::find()
//                            ->where(['project_id' => $projectId, 'user_id' => $userId])->one();
//            $model->status = $status;
//            $model->save();
        }
    }
    public function actionBlock($id) {
                
        if(Yii::$app->request->isAjax){
            $user = $this->findModel($id);
            $event = $this->getUserEvent($user);
            if ($user->getIsBlocked()) {
                $this->trigger(self::EVENT_BEFORE_UNBLOCK, $event);
                $user->unblock();
                $this->trigger(self::EVENT_AFTER_UNBLOCK, $event);
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                $myProject = \cpn\chanpan\classes\utils\CNProject::getMyProject(); 
                $dataCreate = $myProject['data_create'];
                if($dataCreate){
                    $this->setStatusDisConnect($id, $dataCreate, 10);
                }
                
                $profile = Profile::findOne($id);
                if($profile->invite == '4'){
                    $profile->invite = 1;
                    $profile->save();
                    if(!\cpn\chanpan\classes\CNServerConfig::isPortal()){
                       $project = isset(Yii::$app->params['my_project'])?Yii::$app->params['my_project']:"";
                       if($project['data_dynamic']){
                           $project_id = $project['data_dynamic']['data_id']; 
                            $project_id = $project['data_dynamic']['data_id']; 
//                            $model      = \backend\modules\manageproject\models\Discontinuatios::find()
//                           ->where(['project_id'=>$project_id, 'user_id'=>$id])->one(Yii::$app->db_main);
//                            $model->delete();
                            
                            $sql = "DELETE FROM discontinuatios WHERE project_id=:project_id AND user_id=:user_id";
                            $delete_discontinuations = \Yii::$app->db_main->createCommand($sql,[':project_id'=>$project_id,':user_id'=>$id])->execute();
                       }
                    }
                    
                }
                $result = [
                    'status' => 'success',
                    'action' => 'create',
                    'message' => \appxq\sdii\helpers\SDHtml::getMsgSuccess() . Yii::t('chanpan', 'Update profile completed.'),
                ];
                return $result;
            } else {
                $this->trigger(self::EVENT_BEFORE_BLOCK, $event);
                $user->block();
                
                $myProject = \cpn\chanpan\classes\utils\CNProject::getMyProject(); 
                $dataCreate = $myProject['data_create'];
                if($dataCreate){
                    $this->setStatusDisConnect($id, $dataCreate, 20);
                }
                
                $this->trigger(self::EVENT_AFTER_BLOCK, $event);
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                $result = [
                    'status' => 'success',
                    'action' => 'create',
                    'message' => \appxq\sdii\helpers\SDHtml::getMsgSuccess() . Yii::t('chanpan', 'Update profile completed.'),
                ];
                return $result;
            }
            
        }else{
            return $this->redirect(['/user/admin/index']);
        }
    }
    
    public function actionInfo($id)
    {
        Url::remember('', 'actions-redirect');
        $user = $this->findModel($id);

        return $this->renderAjax('_info', [
            'user' => $user,
        ]);
    }
    public function actionUpdateValidate()
    {
        $id = isset($_GET['id']) ? $_GET['id'] : '';
        
        $model = \common\modules\user\models\User::findOne($id);
        if (\Yii::$app->request->isAjax && !\Yii::$app->request->isPjax) {
            if ($model->load(\Yii::$app->request->post())) {
                \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                echo json_encode(\yii\bootstrap\ActiveForm::validate($model));
                \Yii::$app->end();
            }
        }
    }
    public function actionUpdate($id)
    {
        //return '';
        Url::remember('', 'actions-redirect');
        $user = $this->findModel($id);
        $user->scenario = 'update';
        $event = $this->getUserEvent($user);
        //$this->performAjaxValidation($user);

        $this->trigger(self::EVENT_BEFORE_UPDATE, $event);
        if ($user->load(\Yii::$app->request->post())) {
            if($user->save()){
                //\Yii::$app->getSession()->setFlash('success', \Yii::t('user', 'Account details have been updated'));
                $this->trigger(self::EVENT_AFTER_UPDATE, $event);
                //return $this->refresh();
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                $result = [
                    'status' => 'success',
                    'action' => 'create',
                    'message' => \appxq\sdii\helpers\SDHtml::getMsgSuccess() . Yii::t('chanpan', 'Save user completed.'),
                ];
                return $result;
            }        
        }

        return $this->renderAjax('_account', [
            'user' => $user,
        ]);
    }

}
