<?php

namespace backend\modules\api\v1\controllers;

use appxq\sdii\utils\SDUtility;
use common\modules\user\classes\CNSocialFunc;
use cpn\chanpan\classes\CNUser;
use cpn\chanpan\classes\utils\CNProject;
use yii\db\Query;
use yii\web\Response;
use backend\modules\api\v1\classes\NcrcProjectApi;
use backend\modules\api\v1\models\ProjectRequest;
use common\modules\user\models\User;
use dms\aomruk\classese\Notify;
use Yii;
use yii\db\Exception;
use yii\httpclient\Client;
use yii\web\Controller;
use yii\web\MethodNotAllowedHttpException;


class NcrcProjectController extends Controller
{

    public function beforeAction($action)
    {
        header('Access-Control-Allow-Origin: *');
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionByPassLogin(){
        $cookies = Yii::$app->response->cookies;
        $ref = Yii::$app->request->get('ref');
        $token = Yii::$app->request->get('token');
        $user = CNSocialFunc::checkUser($ref, '');
        $cookies->add( new \yii\web\Cookie([
            'name' => 'PLATFORM_ACCESS',
            'value' => 'MOBILE' ,
            'httpOnly' => true
        ]));
        if($user){
            if($user->auth_key == $token){
                CNSocialFunc::autoLogin($user);
            }
        }
        return $this->render('bypass');
    }

    //put your code here
//    public function actionIndex()
//    {
//        $data = \backend\modules\manageproject\classes\CNEzform::getDynamicTableAll("zdata_create_project");
//        return json_encode($data);
//    }

    public function actionGetDataByid()
    {
        header('Access-Control-Allow-Origin: *');
        $this->enableCsrfValidation = false;

        $where = ['id' => isset($_GET['data_id']) ? $_GET['data_id'] : ''];

        $data = \backend\modules\manageproject\classes\CNEzform::getDynamicTableAll("zdata_create_project", $where);
        return json_encode($data);
    }

    public static function getMillisecTime()
    {
        list($t1, $t2) = explode(' ', microtime());
        $mst = str_replace('.', '', $t2 . $t1);
        return $mst;
    }

    public function actionGetProjectDomain($project_id)
    {
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $domain = Yii::$app->db->createCommand("SELECT CONCAT(projurl,'.',projdomain )FROM zdata_create_project WHERE id = :project_id", [":project_id" => $project_id])->queryScalar();
        $datapack = ["success" => true, "domain" => $domain];
        $response->data = $datapack;
        return $response;
    }


    public function actionCheckRequestJoinProject()
    {
        $response = Yii::$app->response;
        $response->format = Response::FORMAT_JSON;
        $response = ['success'=>true ];
        $user_id = Yii::$app->request->post("user_id", null);
        $joinRequest = ProjectRequest::find()->where(['user_id'=>$user_id , 'permission' => [null,'1']])->one();
        $user = User::findOne(['id'=>$user_id]);
        $response['has_join'] = $user != null ;
        $response['has_requested'] = $joinRequest != null ;
        return $response;
    }

    public function actionRequestJoinProject()
    {
        $response = Yii::$app->response;
        $response->format = Response::FORMAT_JSON;
        try {
            $headers = Yii::$app->request->headers;
            $authHeader = $headers->get('Authorization');
            $token = null;
            $userDataJson = Yii::$app->request->post("userData", null);
            $profileDataJson = Yii::$app->request->post("userProfileData", null);
            $project_id = Yii::$app->request->post("projectId", null);
            $compose = Yii::$app->request->post("compose", null);
            $projectData = \backend\modules\manageproject\classes\CNEzform::getDynamicTableAll("zdata_create_project", ["id" => $project_id]);
            $collaboration = $projectData[0]["collaboration"];
            $profileData = json_decode($profileDataJson, true);
            $userData = json_decode($userDataJson, true);
            $result = ["success" => false, "collaboration" => $collaboration, "authHeader" => $authHeader];


            if ($authHeader !== null && preg_match("/^Bearer\\s+(.*?)$/", $authHeader, $matches)) {
                $token = $matches[1];
                $user = \common\modules\user\models\User::findIdentityByAccessToken($token);
                if ($user != null) {
                    // Check
                    switch ($collaboration) {
                        case 1:
                            // is user send before
                            $foundUser = Yii::$app->db->createCommand("SELECT * FROM zdata_project_join_request WHERE user_id = :user_id AND (permission = 0 OR permission IS NULL )", [":user_id" => $userData["id"]])->queryOne();
                            if ($foundUser) {
                                $result = ["success" => false, "message" => "User is pending in approve."];
                                return json_encode($result);
                            }

                            Yii::$app->db->createCommand("ALTER TABLE zdata_project_join_request ADD COLUMN IF NOT EXISTS var_text LONGTEXT;",[])->execute();
                            // add to form
                            // wait for approve
                            $projectReq = new ProjectRequest();
                            $projectReq->setAttribute("profile_data", $profileDataJson);
                            $projectReq->setAttribute("user_data", $userDataJson);
                            $projectReq->setAttribute("user_id", $userData["id"]);
                            $projectReq->setAttribute("var_text", $compose);
                            $projectReq->setAttribute("firstname", $profileData["firstname"]);
                            $projectReq->setAttribute("lastname", $profileData["lastname"]);
                            $projectReq->setAttribute("from_site", $profileData["sitecode"]);
                            $projectReq->setAttribute("rstat", 0);
                            $projectReq->setAttribute("id", self::getMillisecTime());
                            if (!$projectReq->save()) {
                                $result = ["success" => false, "message" => "save error"];
                            } else {
                                $url = CNProject::getCurrentProjectUrl();
                                Notify::setNotify()->send_email(true)->send_system(true)->type_link(3)->notify("Collaboration Request")->detail("<h3>New Collaborator request from Portal.</h3><a href='$url'>$url</a>")->assign([$user->id])->sendStatic();
                                $result = ["success" => true, "collaboration" => $collaboration];
                            }
                            break;
                        case 2:
                            $siteData["user_create"] = "1";
                            $siteData["user_update"] = "1";
                            if (User::findIdentity($userData["id"]) == null) {
                                Yii::$app->db->createCommand()->insert("user", $userData)->execute();
                                Yii::$app->db->createCommand()->insert("profile", $profileData)->execute();
                                $memberName =  $profileData["firstname"]." ".$profileData["lastname"];
                                Notify::setNotify()->send_email(true)->send_system(true)->type_link(3)->notify("Collaboration join")->detail("New Collaborator($memberName) is join your project from Portal.")->assign([$user->id])->sendStatic();
                                $result = ["success" => true, "collaboration" => $collaboration];
                                return $result;
                            } else {
                                $result = ["success" => false, "message" => "user already exist."];
                                return $result;
                            }


                            $foundSite = Yii::$app->db->createCommand("SELECT * FROM zdata_sitecode WHERE site_name = :site_name", [":site_name" => $siteData["site_name"]])->queryOne();
                            // add user and profile


                            $auth = Yii::$app->authManager;
                            $authorRole = $auth->getRole('author');
                            $auth->assign($authorRole, $userData["id"]);
                            $result = ["success" => true, "collaboration" => $collaboration];
                            break;
                    }
                }
            }
        } catch (Exception $e) {
            $result = ["success" => false, "cause" => "db", "message" => $e->getMessage()];
        } catch (\Exception $e) {
            $result = ["success" => false, "message" => $e->getMessage()];

        }

        return $result;
    }

    public function actionApplyInviteProject()
    {
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        try {
            $token = Yii::$app->request->post("token", null);
            $userDataJson = Yii::$app->request->post("userData", null);
            $userData = json_decode($userDataJson, true);
            $result = ["success" => false];
            if ($token !== null) {

                $user = User::findIdentity($userData["id"]);
                $inviteRecord = Yii::$app->db->createCommand("SELECT * FROM invitation_project WHERE token = :token AND result = 0 AND rstat <> 3", [":token" => $token])->queryOne();
                if($inviteRecord["expire_date"] != null){
                    if(strtotime($inviteRecord["expire_date"]) < strtotime(date("Y/m/d H:i:s"))){
                        $result = ["success" => false, "action" => "expire", "message" => "invite was expired."];
                        return $result;
                    }
                }
                if ($inviteRecord != null) {
                    $isReject = Yii::$app->request->post("reject", null);
                    if ($isReject != null && $isReject == "1") {
                        //2 mean reject
                        $success = Yii::$app->db->createCommand()->update("invitation_project", ["result" => 2], ['token' => $token])->execute();
                        if ($success) {
                            $result = ["success" => true, "action" => "reject", "message" => "reject is apply."];
                            return $result;
                        } else {
                            $result = ["success" => false, "action" => "reject", "message" => "reject failed."];
                            return $result;
                        }
                    }
                    $profileDataJson = Yii::$app->request->post("userProfileData", null);
                    $profileData = json_decode($profileDataJson, true);
                    $sitecode = Yii::$app->db->createCommand("SELECT site_name FROM zdata_sitecode WHERE id=:id", [':id' => $inviteRecord["target_site"]])->queryScalar();
                    if ($user == null) {
                        $profileData["sitecode"] = $sitecode;
                        $profileData["department"] = $inviteRecord["department"];
                        Yii::$app->db->createCommand()->insert("user", $userData)->execute();
                        Yii::$app->db->createCommand()->insert("profile", $profileData)->execute();
                        $auth = Yii::$app->authManager;
                        $authorRole = $auth->getRole('author');
                        try{
                            $auth->assign($authorRole, $userData["id"]);
                        }catch (\Exception $e){

                        }
                    } else {
                        Yii::$app->db->createCommand()->update("profile", ["sitecode" => $sitecode], []);
                    }

                    // ----
                    if(isset($inviteRecord["role"])&& !empty($inviteRecord["role"])){
                        $roleName = Yii::$app->db->createCommand("SELECT role_name FROM zdata_role WHERE id=:id", [':id' => $inviteRecord["role"]])->queryScalar();
                        $invitedUserId = $userData["id"];
                        $id = SDUtility::getMillisecTime();
                        $roleArr = [
                            "id" => $id,
                            "ptid" => $id,
                            "ptcodefull" => "00",
                            "target" => $id,
                            "ptcode" => "",
                            "hptcode" => "",
                            "rstat" => "1",
                            "hsitecode" => "00",
                            "xsourcex" => "00",
                            "role_name" => $roleName,
                            "sitecode" => "00",
                            "user_id" => '["'.$invitedUserId.'"]',
                            // Cause create this function base on this version
                            "ezf_version" => 'v_1520785985056188700',
                            "create_date" => new \yii\db\Expression('NOW()'),
                            "update_date" => new \yii\db\Expression('NOW()'),
                            "user_create" => "1",
                            "user_update" => "1",
                            "start_date" =>  date("Y/m/d"),
                            "role_start" =>  date("Y/m/d"),
                            "expire_status" => 0
                        ];
                        $success = Yii::$app->db->createCommand()->insert("zdata_matching",$roleArr)->execute();
                    }

                    $success = Yii::$app->db->createCommand()->update("invitation_project", ["result" => 1], ['token' => $token])->execute();
                    if ($success)
                        $result = ["success" => true, "message" => "user is apply."];
                    else {
                        $result = ["success" => false, "message" => "apply user failed."];
                    }
                    return json_encode($result);
                } else {
                    $result = ["success" => false, "cause" => "token", "message" => "token not valid."];
                }
            }
        } catch (Exception $e) {
            $result = ["success" => false, "cause" => "db", "message" => $e->getMessage()];
        } catch (\Exception $e) {
            $result = ["success" => false, "message" => $e->getMessage()];
        }
        return $result;
    }

    public function actionGetAdminList()
    {
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $project_id =Yii::$app->request->get("project_id",'');
        try {
            $project = \backend\modules\manageproject\classes\CNEzform::getDynamicTableAll("zdata_create_project", ["id" => $project_id]);
            $sql = "SELECT id,firstname,lastname FROM user INNER JOIN auth_assignment ON auth_assignment.user_id = id INNER JOIN profile ON profile.user_id = id WHERE auth_assignment.item_name = 'administrator' and user.id <> :user_create  ";
            $res = Yii::$app->db->createCommand($sql,[':user_create' => $project[0]['user_create']])->queryAll();
            $datapack = ["success" => true,
                "admin_user" => $res
            ];
            $response->data = $datapack;

        } catch (Exception $e) {
            $datapack = ["success" => false, 'message' => $e->getMessage()];
            $response->data = $datapack;
        }
        return $response;
    }


    public function actionAddAdminSender()
    {
        $response = Yii::$app->response;
        $user_id = Yii::$app->request->post("user_id",null);
        $project_id =Yii::$app->request->post("project_id",null);
        $response->format = \yii\web\Response::FORMAT_JSON;
        try {
            foreach ($user_id as $key => $id) {
                $userData = Yii::$app->db->createCommand("SELECT * FROM user WHERE id = :user_id", [":user_id" => $id])->queryOne();
                $userProfileData = Yii::$app->db->createCommand("SELECT * FROM profile WHERE user_id =:user_id ", [":user_id" => $id])->queryOne();
                $projectData = \backend\modules\manageproject\classes\CNEzform::getDynamicTableAll("zdata_create_project", ["id" => $project_id]);
                $projectData = $projectData[0];
                $projectUrl = $projectData["projurl"];
                $projDomain = $projectData["projdomain"];
                $url = "https://" . $projectUrl . "." . $projDomain . "/api/ncrc-project/add-admin-receiver";
                $package = [
                    "userData" => json_encode($userData),
                    "userProfileData" => json_encode($userProfileData),
                ];
                $client = new Client();
                $response = $client->createRequest()
                    ->setMethod('POST')
                    ->setUrl($url)//.$url
                    ->setData($package)
                    ->send();
                if ($response->isOk) {
                    $res = $response->getData();
                    $result[] = [
                        'status' => 'success',
                        'action' => 'create',
                        'debug' => json_encode($res),
                        'message' => \appxq\sdii\helpers\SDHtml::getMsgSuccess() . Yii::t('chanpan', "Sended CreateAdmin project Request."),
                    ];
                } else {
                    $result[] = [
                        'status' => 'error',
                        'action' => 'create',
                        'message' => \appxq\sdii\helpers\SDHtml::getMsgSuccess() . Yii::t('chanpan', "Request CreateAdmin project process was failed."),
                    ];

                }
            }
            $currentAdmins = Yii::$app->db->createCommand("SELECT id FROM user INNER JOIN auth_assignment ON user_id = id WHERE item_name = 'administrator' and user_id <> '1'", [])->queryAll();
            $auth = Yii::$app->authManager;
            $authorRole = $auth->getRole('administrator');
            foreach ($currentAdmins as $admin) {
                if (!in_array($admin['id'], $user_id)) {
                    $sql =  "DELETE FROM auth_assignment WHERE user_id =:user_id and item_name='administrator' ";
                    Yii::$app->db->createCommand($sql,['user_id' => $admin['id'] ])->execute();

//                    $auth->revoke($authorRole, $admin['user_id']);
                }
            }
            foreach ($user_id as $user) {
                $sql =  "REPLACE INTO auth_assignment ('administrator',:user_id,'2018') ";
                Yii::$app->db->createCommand($sql,[':user_id' => $user ])->execute();
//                $isAdmin = $auth->getAssignment('administrator', $user);
//                if ($isAdmin)
//                    $auth->assign($authorRole, $user);
            }
            return json_encode($result);
        } catch (Exception $e) {
            return $result = [
                'status' => 'error',
                'action' => 'create',
                'debug' => json_encode($e->getMessage()),
                'message' => \appxq\sdii\helpers\SDHtml::getMsgSuccess() . Yii::t('chanpan', "Request CreateAdmin project process was failed."),
            ];
        }
    }

    public function actionAddAdminReceiver()
    {
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        try {
            $token = null;
            $userDataJson = Yii::$app->request->post("userData", null);
            $profileDataJson = Yii::$app->request->post("userProfileData", null);
            $profileData = json_decode($profileDataJson, true);
            $userData = json_decode($userDataJson, true);
            $result = ["success" => false];
            if (true) {
                $res = NcrcProjectApi::addAdminUser($userData, $profileData, 'administrator');
                return $res;
            }
        } catch (Exception $e) {
            $result = ["success" => false, "cause" => "db", "message" => $e->getMessage()];
        } catch (\Exception $e) {
            $result = ["success" => false, "message" => $e->getMessage()];

        }
        return $result;
    }




    public function actionAssignAdminRole()
    {
        try {
            $response = Yii::$app->response;
            $response->format = \yii\web\Response::FORMAT_JSON;
            $userNeedToAssign = Yii::$app->request->post("users", null);
            $userNeedToAssign = json_decode($userNeedToAssign, true);
            $currentAdmins = Yii::$app->db->createCommand("SELECT id FROM user INNER JOIN auth_assignment ON user_id = id WHERE item_name = 'administrator' and user_id <> '1'", [])->queryAll();

            $auth = Yii::$app->authManager;
            $authorRole = $auth->getRole('administrator');

            foreach ($currentAdmins as $admin) {
                if (!in_array($admin['id'], $userNeedToAssign["users"])) {
                    $auth->revoke($authorRole, $admin['user_id']);
                }
            }
            foreach ($userNeedToAssign["users"] as $user) {
                $isAdmin = $auth->getAssignment('administrator', $user);
                if ($isAdmin)
                    $auth->assign($authorRole, $user);
            }
            $datapack = ["success" => true];
            $response->data = $datapack;
            return $response;
        } catch (Exception $e) {
            $datapack = ["success" => false, 'message' => $e->getMessage()];
            $response->data = $datapack;
            return $response;
        } catch (\Exception $e) {
            $datapack = ["success" => false, 'message' => $e->getMessage()];
            $response->data = $datapack;
            return $response;
        }
    }

    public function actionAddUserProject(){
        $user_id = Yii::$app->request->post("user_id",null);
        $project_id = Yii::$app->request->post("project_id",null);
        if($user_id == null || $project_id == null){
            throw new \yii\web\HttpException(404, 'The requested params not complete.');
        }
        $projectExist = Yii::$app->db->createCommand("SELECT * FROM zdata_create_project WHERE id = :id",[":id" => $project_id])->queryOne();
        if(!$projectExist)
            throw new \yii\web\HttpException(400, 'The project not exist.');
        $data = [
            'url'=> \cpn\chanpan\classes\CNServerConfig::getDomainName(),
            'user_id'=>$user_id,
            'create_by'=> "1",
            'create_at'=>Date('Y-m-d'),
            'data_id'=> $project_id
        ];
        if (\cpn\chanpan\classes\CNServerConfig::isPortal() || \cpn\chanpan\classes\CNServerConfig::isLocal()) {
            \Yii::$app->db->createCommand()->insert('user_project', $data)->execute();   
        }else{
            \Yii::$app->db_main->createCommand()->insert('user_project', $data)->execute();
        }
        
    }

    public function actionSaveProfile() {
        if (Yii::$app->request->post()) {
            $ezf_id = \backend\modules\patient\Module::$formID['profile'];
            $dataProfile = Yii::$app->request->post('EZ1503378440057007100');
            $data['pt_cid'] = str_replace('-', '', $dataProfile['pt_cid']);
            $profileData = \backend\modules\patient\classes\PatientQuery::getPatientSearch($data['pt_cid'], '12276');
                        
            if (empty($profileData)) {
                $data['pt_pic'] = '';

                $name = explode(" ", $dataProfile['fullname_th']);
                $data['pt_bdate'] = $dataProfile['bdate'];
                $dataSex = PatientQuery::getPrefixId($name[0]);
                $data['pt_sex'] = $dataSex['prefix_sex'];
                $data['pt_prefix_id'] = $dataSex['prefix_id'];
                $data['pt_firstname'] = $name[1];
                $data['pt_lastname'] = $name[2];
                $address = explode("#", trim($dataProfile['address']));
                $arrLength = count($address) - 1;
                //\appxq\sdii\utils\VarDumper::dump($arrLength);
                $data['pt_address'] = $address[0];
                $data['pt_moi'] = str_replace("หมู่ที่", "", $address[1]);
                $data['pt_addr_tumbon'] = str_replace("ตำบล", "", $address[$arrLength - 2]);
                $data['pt_addr_amphur'] = str_replace("อำเภอ", "", $address[$arrLength - 1]);
                $data['pt_addr_province'] = str_replace("จังหวัด", "", $address[$arrLength]);

                $dataTAC = PatientQuery::getProviceByName($data['pt_addr_tumbon'], $data['pt_addr_amphur'], $data['pt_addr_province']);
                $data['pt_addr_tumbon'] = $dataTAC['DISTRICT_CODE'];
                $data['pt_addr_amphur'] = $dataTAC['AMPHUR_CODE'];
                $data['pt_addr_province'] = $dataTAC['PROVINCE_CODE'];
                $data['pt_addr_zipcode'] = $dataTAC['zipcode'];

                $dataSerene = \backend\modules\patient\classes\PatientFunc::checkPtProfileOld($data['pt_cid']);
                if ($dataSerene['value']['status'] == 'OLD') {
                    $dataSerene = $dataSerene['value'];
                    $data['pt_hn'] = $dataSerene['pt_hn'];
                    $data['pt_national_id'] = $dataSerene['pt_national_id'];
                    $data['pt_origin_id'] = $dataSerene['pt_national_id'];
                    $data['pt_religion_id'] = $dataSerene['pt_religion_id'];
                    $data['pt_mstatus'] = $dataSerene['pt_mstatus'];
                    $data['pt_occ'] = $dataSerene['pt_occ'];
                    $data['pt_phone2'] = $dataSerene['pt_phone2'];
                    $data['pt_contact_name'] = $dataSerene['pt_contact_name'];
                    $data['pt_contact_status'] = $dataSerene['pt_contact_status'];
                    $data['pt_contact_phone'] = $dataSerene['pt_contact_phone'];
                }
                $dataid = \backend\modules\patient\classes\PatientFunc::backgroundInsert($ezf_id, '', '', $data)['data']['id'];
            } else {
                $dataid = $profileData[0]['id'];
            }
            return $dataid;
        } else {
            throw new MethodNotAllowedHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionValidateSocialToken(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $project = Yii::$app->request->post('project', null);
        $token = Yii::$app->request->post('token', null);
        $result = NcrcProjectApi::validateSocialToken($token, $project);
        if($result){
            return ['success'=>true , 'data'=>$result->getAttribute('user_id')];
        }
        return ['success'=>false,'input'=>[$token, $project]];
    }
}
