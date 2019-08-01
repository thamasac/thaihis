<?php

namespace backend\modules\manage_user\controllers;

use appxq\sdii\helpers\SDHtml;
use appxq\sdii\utils\SDUtility;
use backend\modules\manage_user\models\InvitationModel;
use cpn\chanpan\classes\utils\CNProject;
use backend\modules\study_manage\classes\StudyQuery;
use dms\aomruk\classese\Notify;
use Yii;
use common\modules\user\classes\CNUserFunc;
use yii\helpers\ArrayHelper;

class UserController extends \yii\web\Controller
{
    public function actionIndex(){
        return $this->renderAjax("index");
    }
    //put your code here
    public function actionCreate()
    {
        $model = new \backend\modules\manage_user\models\CNUserForm();
        $dbType = isset($_GET['db_type']) ? $_GET['db_type'] : '';
        $rolesArr = (new \yii\db\Query())->select(['id','role_detail'])->from('zdata_role')->where('rstat NOT IN (0,3)')->all();
        $roles = ArrayHelper::map($rolesArr, 'id', 'role_detail');
        if ($model->load(\Yii::$app->request->post())) {
             
            $sitecode = isset($_POST['CNUserForm']['sitecode']) ? $_POST['CNUserForm']['sitecode'] : '';
            $department =  '1524628831068141000';//isset($_POST['CNUserForm']['department']) ? $_POST['CNUserForm']['department'] : '';
            $user = CNUserFunc::getUserById($dbType, $_POST['CNUserForm']['user_id']);
            if (!empty($user)) {
                $user['sitecode'] = $sitecode;
                $user['department'] = $department;
                
                $detail         = \backend\modules\core\classes\CoreFunc::getParams('invite_email', 'invite');
                $site_detail    = \common\modules\user\classes\CNSitecode::getSiteValue($sitecode);
                $myproject      = \cpn\chanpan\classes\utils\CNProject::getMyProject();
                $project_name   = "";
                $id             = "";
                $pi_name        = "";
                if(!empty($myproject)){
                    $project_name = $myproject['data_create']['projectname'];
                    $id         = $myproject['data_create']['id'];
                    $pi_name    = isset($myproject['data_create']['pi_name']) ? $myproject['data_create']['pi_name'] : '';    
                }else{
                    if(\cpn\chanpan\classes\utils\CNDomain::isPortal()){
                        $project_name = 'nCRC';
                    }                    
                }
                $myproject = \cpn\chanpan\classes\utils\CNProject::getMyProject();
                
                $email = $user['email'];
                $user_name = "";
                $user_email = "";
                if($myproject['data_create']){
                    $user_create = $myproject['data_create']['user_create'];
                    $users = \cpn\chanpan\classes\CNUser::GetUserNcrcById($user_create);
                    $user_email = $users['user']['email'];
                    $user_name   = "{$users['profile']['firstname']} {$users['profile']['lastname']}";
                }
                
                $project_study_label = Yii::$app->db->createCommand("SELECT ezform_choice.ezf_choicelabel FROM ezform_choice INNER JOIN zdata_create_project ON zdata_create_project.studydesign collate utf8_general_ci = ezform_choice.ezf_choicevalue collate utf8_general_ci WHERE ezform_choice.ezf_field_id = '1523071383096379200'  AND zdata_create_project.id = :id",[":id" => $id])->queryScalar();
                
                $message = isset($_POST['message']) ? $_POST['message'] : '';
                $domain = \cpn\chanpan\classes\utils\CNDomain::getCurrentProjectUrl();
               
                $output = base64_encode(SDUtility::array2String( [
                    'token'=>$user['id'],
                    'email'=>$email,
                ]));
                $token = $output;
                $url =  "https://{$domain}/manage_user/user/access-invite?token={$token}";  
                 
                $modelForm = [
                    'url'=>$url,
                    'project_name'=>$project_name,
                    'project_study_label'=>$project_study_label,
                    'user_email'=>$user_email,
                    'user_name'=>$user_name,
                    'site_detail'=>$site_detail,
                    'message'=>$message,
                    'pi_name'=>$pi_name
                ];
                $path = [];
                     foreach ($modelForm as $key => $value) {
                         $path["{" . $key . "}"] = $value; 
                     }
                $d = strtr($detail, $path);                
                $user['invite'] = 3;
                if(CNUserFunc::createUser($dbType, $user, 'notset')){
                    $this->sendMail("Invitation to join a research project at nCRC", $d, $email);
                    return \backend\modules\manageproject\classes\CNMessage::getSuccess("Invite Member {$user['firstname']} {$user['lastname']} successfully");
                }else{
                    \backend\modules\manageproject\classes\CNMessage::getError("Invite Member not success"); 
                }
                
            }
        }
        return $this->renderAjax("create", ['model' => $model, 'dbType' => $dbType, 'roles'=>$roles]);
    }
    public function sendMail($title, $detail, $email){
        try{
            return Notify::setNotify()
                    ->send_email(true)
                    ->notify($title)
                    ->detail($detail)
                    ->SendMailTemplate($email , true);
                     
        } catch (\yii\db\Exception $ex) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
        }
    }
    public function actionAccessInvite(){        
        $token = Yii::$app->request->get('token', '');
        $token = base64_decode($token);        
        $data= SDUtility::string2Array($token);     
        
        $check_user = \cpn\chanpan\classes\CNUser::GetUserNcrcById($data['token']);
        if($check_user['user'] == FALSE){
            echo 'User not found!';return;
        }
        return $this->render('access-invite',[
            'data'=>$data
        ]);
  }
  public function actionSaveUserProject(){
    if(!Yii::$app->request->isAjax){
        return 'Request not found';
    }
    try{
        $id = Yii::$app->request->post('id', '');
        $value = Yii::$app->request->post('value', '');
        $table = 'profile';
        $update = Yii::$app->db->createCommand()->update($table, ['invite'=>$value], 'user_id=:id', [':id'=>$id])->execute(); 
        //if($update){
           if($value == 1){
               
               \common\modules\user\classes\CNUserQuery::saveUserProject($id); 
               $user = \common\modules\user\models\User::findOne($id);
               $user->unblock();
               
                return \backend\modules\manageproject\classes\CNMessage::getSuccessObj('Success', ['status'=>true]);
           }else if($value == 2){
               \common\modules\user\classes\CNUserQuery::deleteUserProject($id);
               \Yii::$app->db->createCommand()->update('user', ['blocked_at'=> time()], 'id=:id', [":id"=>$id])->execute();
               \Yii::$app->user->logout();
               return \backend\modules\manageproject\classes\CNMessage::getSuccessObj('Success', ['status'=>false]);
           }
           //return \backend\modules\manageproject\classes\CNMessage::getSuccess("Success");           
        //}else{
            //return \backend\modules\manageproject\classes\CNMessage::getSuccessObj('Success', ['status'=>false]);
        //}
    } catch (\yii\db\Exception $ex) {
        \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex); 
    } 
  }
    
    public function actionGetSitecode()
    {
        $user_id = isset(\Yii::$app->user->id) ? \Yii::$app->user->id : $_GET['user_id'];
        $sql = "SELECT `profile`.sitecode FROM `profile` WHERE `profile`.`user_id` = :user_id";
        $dataSitecode = Yii::$app->db->createCommand($sql, [':user_id' => $user_id])->queryOne();
        $dataSitecode = isset($dataSitecode['sitecode']) ? $dataSitecode['sitecode'] : '';

        $user = \cpn\chanpan\classes\CNUser::GetSiteCodeByUserId($user_id, $dataSitecode);
        $data = [];
        if (empty($user)) {
            $data['status'] = 'success';
            $data['results'] = ['id' => '', 'name' => ''];
        } else {
            $data['status'] = 'success';
            $data['results'] = $user;
        }
        return json_encode($data);
    }
    public function actionGetName()
    {
        $user_id = Yii::$app->request->get('user_id', '');
        $type = Yii::$app->request->get('type', '');
        if($type == 'tcc'){
            $sql = "SELECT p.user_id as id , CONCAT(p.firstname,' ',p.lastname) as `name`  FROM `user_profile` as p WHERE `p`.`user_id` = :user_id";
            $user = Yii::$app->db_tcc->createCommand($sql, [':user_id' => $user_id])->queryOne();
        }else{
            $sql = "SELECT p.user_id as id , CONCAT(p.firstname,' ',p.lastname) as `name`  FROM `profile` as p WHERE `p`.`user_id` = :user_id";
            $user = Yii::$app->db_main->createCommand($sql, [':user_id' => $user_id])->queryOne();
        }
        //db_tcc
        
        //\appxq\sdii\utils\VarDumper::dump($user);
        $data = [];
        if (empty($user)) {
            $data['status'] = 'success';
            $data['results'] = ['id' => '', 'name' => ''];
        } else {
            $user = ['id'=>$user['id'], 'name'=>$user['name']];
            $data['status'] = 'success';
            $data['results'] = $user;
        }
        //\appxq\sdii\utils\VarDumper::dump($data);
        return json_encode($data);
    }

    public function actionCreateProjectInvitationView()
    {
        $model = new \backend\modules\manage_user\models\CNUserForm();
        if (Yii::$app->request->isPost) {
            try {

                $response = Yii::$app->response;
                $response->format = \yii\web\Response::FORMAT_JSON;
                $sitecode = isset($_POST['CNUserForm']['sitecode']) ? $_POST['CNUserForm']['sitecode'] : '';
                $department = isset($_POST['CNUserForm']['department']) ? $_POST['CNUserForm']['department'] : '';
                $enable_expire = Yii::$app->request->post("enable_expire", null);
                $enable_expire = isset($enable_expire);
                if($enable_expire){
                    $expire_date = Yii::$app->request->post("expire_date", null);
                    $expire_date = date_format(date_create($expire_date." 23:59:59"),"Y/m/d H:i:s");
                }else{
                    $expire_date = null;
                }

                $email = Yii::$app->request->post("email", null);
                $role = Yii::$app->request->post("role", null);
                $duplicateEmail = Yii::$app->db->createCommand("SELECT * FROM profile WHERE public_email = :email", [":email" => $email])->queryOne();
                if ($duplicateEmail) {
                    $result = [
                        'status' => 'error',
                        'action' => 'create',
                        'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'This email exist in this project.'),
                    ];
                    return $result;
                }
                $siteData = Yii::$app->db->createCommand("SELECT id, site_detail FROM zdata_sitecode WHERE site_name=:sitecode", [':sitecode' => $sitecode])->queryOne();
                $site_detail = $siteData["site_detail"];
                $roleDetail = Yii::$app->db->createCommand("SELECT role_detail FROM ncrc.zdata_role WHERE id=:id", [':id' => $role])->queryScalar();
                if(!$roleDetail) $roleDetail = "Not Defined";
                $message = Yii::$app->request->post("message", null);
                $token = Yii::$app->security->generateRandomString(64);
                $id = SDUtility::getMillisecTime();
                $project_id = CNProject::getProject()["data_id"];// .id = :id
                $project_study_label = Yii::$app->db->createCommand("SELECT ezform_choice.ezf_choicelabel FROM ezform_choice INNER JOIN zdata_create_project ON zdata_create_project.studydesign collate utf8_general_ci = ezform_choice.ezf_choicevalue collate utf8_general_ci WHERE ezform_choice.ezf_field_id = '1523071383096379200'  AND zdata_create_project.id = :id",[":id" => $project_id])->queryScalar();
                $project_name = isset(Yii::$app->params['project_name']) ? Yii::$app->params['project_name'] : '';
                // Generated invitation record.
                // Gen token
                $inviteModel = new InvitationModel();
                $inviteModel->setAttributes([
                    "sitecode" => Yii::$app->user->identity->profile->sitecode,
                    "target_site" => $siteData["id"],
                    "department" => $department,
                    "token" => $token,
                    "role" => $role,
                    "rstat" => 0,
                    "user_create" => Yii::$app->user->id,
                    "user_update" => Yii::$app->user->id,
                    "create_date" => new \yii\db\Expression('NOW()'),
                    "update_date" => new \yii\db\Expression('NOW()'),
                    "expire_date" => $expire_date,
                    "id" => $id,
                    "email" => $email,
                    "message" => $message
                ], false);
                $success = $inviteModel->save();
                $user_email = Yii::$app->user->identity->profile->public_email;
                $user_name = Yii::$app->user->identity->profile->firstname." ".Yii::$app->user->identity->profile->lastname;
                $url = "https://portal.ncrc.in.th/user/register?token=$token&email=$email&project_id=$project_id";
                $detail = <<<HTML
<h2>
Invitation to join a research project at nCRC
</h2>
<p>
This is an invitation from Invitor who are working with a research project at nCRC- the National Clinical Research Center of Thailand (www.ncrc.in.th). The project title is '$project_name' which was designed as a '$project_study_label'. 
<p> 
<br>
<p>It would be very much appreciated if you could become part of the project team. Here are details of the invitation</p>
<ol>
  <li> Site Name: $site_detail</li>
  <li> Role Name: $roleDetail</li>
</ol>
<p>Kindly click the link below to proceed. </p>
  <a href='$url'> Join the Project </a>
  <br>
  <p>
  We are looking forward to working with you. If you require any additional information, please feel free to contact me.
</p>
<br>
Truly yours,
<br>
My Name: $user_name
<br>
e-Mail Address: $user_email
<hr>
<h3>Additional notes from me:</h3>
<p>$message</p>
HTML;
                if ($success) {
                    Notify::setNotify()->send_email(true)->notify("Letter of invitation")->detail($detail)->SendMailTemplate($email, true);
                    $result = [
                        'status' => 'success',
                        'action' => 'create',
                        'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Invite completed.'),
                    ];
                    return $result;
                } else {
                    $result = [
                        'status' => 'error',
                        'action' => 'create',
                        'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Invite failed.'),
                    ];
                    return $result;
                }
            } catch (\Throwable $e) {
                $result = [
                    'status' => 'error',
                    'action' => 'create',
                    'message' => SDHtml::getMsgSuccess() . Yii::t('app', $e->getMessage()),
                ];
                return $result;
            }
        }

//        $rolesArr = Yii::$app->db->createCommand("SELECT zdata_matching.id,concat(zdata_role.role_detail,' (',zdata_matching.role_name,')',' ',COALESCE(role_start,'none'),' to ',COALESCE(role_stop,'none')) as title FROM zdata_matching inner join zdata_role on zdata_role.role_name = zdata_matching.role_name WHERE zdata_matching.rstat NOT IN (0,3) and expire_status = 0",[])->queryAll();
        $rolesArr = Yii::$app->db->createCommand("SELECT id,role_detail as title FROM zdata_role WHERE rstat NOT IN (0,3)",[])->queryAll();
        $roles = ArrayHelper::map($rolesArr, 'id', 'title');
        return $this->renderAjax("invitation", ["model" => $model,"roles"=>$roles]);
    }

  /*access-invite*/
    
    public function actionMonitorUsers(){
        if(!\Yii::$app->user->can('administrator')){
           return $this->goHome(); 
        }
        //\backend\modules\manageproject\classes\CNHeaderSetting::widget();
        $textsearch = \Yii::$app->request->get('textsearch', '');
        $params=[];
        $condition='';
        if($textsearch != ''){
            $condition = "AND (CONCAT(p.firstname,' ',p.lastname) LIKE :textsearch)";
            $params = [
                ':textsearch'=>"%{$textsearch}%",
                //':firstname'=>"%{$textsearch}%", 
                //':lastname'=>"%{$textsearch}%"         
            ];
            //\appxq\sdii\utils\VarDumper::dump($condition);    
        }
        
        $sql="
            SELECT 
                u.`id` as user_id, concat(p.`firstname`,'', p.`lastname`,' (', u.`email`,') ') as name, concat(sc.`site_detail`, ' (',sc.`site_name`, ') ') as sitecode 
                ,(
                    SELECT count(DISTINCT sup.url) as count_user FROM `user_project` as sup 
                    INNER JOIN `profile` as sp ON sup.user_id = sp.user_id
                    INNER JOIN `dynamic_db` as sdb ON sup.url = sdb.url
                    WHERE sup.user_id = u.id 
                ) as count_project
                 FROM `user` as u 
            INNER JOIN `profile` as p ON u.`id`=p.`user_id`
            INNER JOIN `zdata_sitecode` as sc ON p.`sitecode`=sc.`site_name`
            WHERE (u.`confirmed_at` is not null AND u.blocked_at is null AND u.status = 10)
            {$condition}
            
            ORDER BY count_project DESC
        ";
        //$sql='SELECT count(up.id) as counts FROM `user_project` as up GROUP BY up.url ORDER BY up.id desc';
        
        $query = \Yii::$app->db->createCommand($sql, $params)->queryAll();
        //\appxq\sdii\utils\VarDumper::dump($query);
        
        $dataProvider = new \yii\data\ArrayDataProvider([
            'allModels' => $query,
            'sort' => [
                'attributes' => ['name', 'sitecode','count_project'],
            ],
            'pagination' => [
                'pageSize' => 100,
            ],
        ]);
        //\appxq\sdii\utils\VarDumper::dump($query);
        return $this->render('monitor-users',[
            'dataProvider'=>$dataProvider
        ]);
    }
    public function actionGetUserProject(){
        if(!\Yii::$app->user->can('administrator')){
           return $this->goHome(); 
        }
        $user_id = \Yii::$app->request->get('user_id', '');
        $query = \cpn\chanpan\classes\CNUser::getUserProjectByUserId($user_id);
        
        $dataProvider = new \yii\data\ArrayDataProvider([
            'allModels' => $query,
            'sort' => [
                'attributes' => ['dbname', 'create_at', 'project_name', 'url'],
            ],
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);
        
        return $this->renderAjax('get-user-project',[
            'dataProvider'=>$dataProvider
        ]);
    }
    
    public function actionGetKeywordSearchAutoComplete(){
        $term    = Yii::$app->request->get('term', '');
        $sql="
            SELECT p.user_id ,CONCAT(p.firstname,' ',p.lastname) as `name`  
             FROM `user` as u INNER JOIN `profile` as p ON u.id=p.user_id
             WHERE CONCAT(p.firstname,' ',p.lastname) LIKE :q OR `u`.`email` LIKE :email  LIMIT 0,50  
        ";
        
        $keyword = \Yii::$app->db->createCommand($sql, [":q"=>"%{$term}%", ":email"=>"%$term%"])->queryAll();
        $output  = [];
        foreach($keyword as $k=>$v){
            $output[$k] = ['label'=>$v['name'], 'value'=>$v['name']];
        }
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $output;
    }
  
}
