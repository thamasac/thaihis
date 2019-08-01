<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace cpn\chanpan\classes;

use yii\db\Exception;
use kartik\widgets\Select2;
use backend\modules\ezforms2\classes\EzfFunc;
use Yii;

/**
 * Description of CNUser
 *
 * @author chanpan
 */
class CNUser {

    private static $TB_USER = 'user';
    private static $TB_PROFILE = 'profile';
    private static $AUTH_ASSIGNMENT = 'auth_assignment';

    /**
     * 
     * @param type $user_id
     * @return type
     */
    public static function getUserProject($user_id) {
        try {
            $sql = "SELECT * FROM user_project WHERE user_id=:user_id";
            $data = \Yii::$app->db_main->createCommand($sql, [':user_id' => $user_id])->queryAll();
            return $data;
        } catch (Exception $ex) {
            EzfFunc::addErrorLog($ex);
        }
    }

    public static function saveUserProject($user_id) {
        try {
            $domain = CNServerConfig::getDomainName();
            $data_id = "";
            $dataDbDynamic = CNServerConfig::getServerModelDynamicDb();
            $data_id = isset($dataDbDynamic['data_id']) ? $dataDbDynamic['data_id'] : '';
            $data = [
                'url' => CNServerConfig::getDomainName(),
                'user_id' => $user_id,
                'create_by' => \Yii::$app->user->id,
                'create_at' => Date('Y-m-d'),
                'data_id' => $data_id
            ];
            $dataExecute = \Yii::$app->db_main->createCommand()->insert('user_project', $data)->execute();
            return $dataExecute;
        } catch (Exception $ex) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
        }
    }

    /* reuse utils/CNUser */

    /**
     *  
     * @param type $dbname string 'ncrc_xxx'
     * @param type $data array [username=>'', firstname=>'']
     * @param type $tbname string user | profile
     * @return boolean true or false
     */
    public static function saveUser($data, $dbname = '', $tbname = 'user') {
        try {
            $table = ($tbname == 'user') ? self::$TB_USER : self::$TB_PROFILE;
            $table = ($dbname != '') ? "`{$dbname}`.`{$table}`" : self::$TB_USER;
            $create = \Yii::$app->db_main->createCommand()->insert($table, $data)->execute();
            if ($create) {
                return true;
            }
        } catch (Exception $ex) {
            EzfFunc::addErrorLog($ex);
            return false;
        }
    }

    /**
     * 
     * @param type $item_name string 'administrator'
     * @param type $user_id  string user_id
     * @param type $db_name string 'ncrc_xxx'
     * @return boolean true or false
     */
    public static function saveRole($item_name, $user_id, $db_name) {
        try {
            $item_name = ($item_name == '') ? 'administrator' : $item_name;
            $table = "{$db_name}." . self::$AUTH_ASSIGNMENT;
            $data = ['item_name' => $item_name, 'user_id' => $user_id, 'created_at' => time()];
            $create = \Yii::$app->db_main->createCommand()->insert($table, $data)->execute();
            if ($create) {
                return true;
            }
        } catch (Exception $ex) {
            EzfFunc::addErrorLog($ex);
            return false;
        }
    }

    /**
     * 
     * @return type string user id
     */
    public static function getUserId() {
        return isset(\Yii::$app->user->id) ? \Yii::$app->user->id : '';
    }

    /**
     * 
     * @param type $userID
     * @param type $dbName
     * @return type
     */
    public static function checkUserDynamicDb($userID, $dbName) {
        try {
            $sql = "SELECT * FROM {$dbName}.user WHERE id=:id";
            return Yii::$app->db->createCommand($sql, [':id' => $userID])->queryOne();
        } catch (Exception $ex) {
            EzfFunc::addErrorLog($ex);
            return false;
        }
    }

    /**
     * 
     * @param type $id
     * @return type
     */
    public static function getUserNcrcById($id) {
        try {
            $dataUser = (new \yii\db\Query())
                    ->select("*")
                    ->from('user')
                    ->where(['id' => $id])
                    ->one();
            $dataProfile = (new \yii\db\Query())
                    ->select("*")
                    ->from('profile')
                    ->where(['user_id' => $id])
                    ->one();
            return[
                'user' => $dataUser,
                'profile' => $dataProfile
            ];
        } catch (Exception $ex) {
            EzfFunc::addErrorLog($ex);
            return false;
        }
    }

    public static function getEmailForm($form, $model, $attri = "email", $dbType = "tcc", $dataUser = "") {
        //\appxq\sdii\utils\VarDumper::dump($model);
        if ($dataUser == "") {
            $sql = "
                    SELECT p.user_id as id, u.email as `name`  
                    FROM `user` as u INNER JOIN `profile` as p ON u.id = p.user_id   
                    WHERE u.id = :id
                ";
            $dataUser = \Yii::$app->db->createCommand($sql, [':id' => $model['user_id']])->queryOne();
           // \appxq\sdii\utils\VarDumper::dump($dataUser);
            $url = "";
            if ($dbType == "tcc") {
                $url = \yii\helpers\Url::to(['/ezforms2/user-tcc/get-user', 'type'=>'email']);
            } else {
                $url = \yii\helpers\Url::to(['/ezforms2/user-tcc/get-user-ncrc', 'type'=>'email']);
            }
        }

        return $form->field($model, $attri)->widget(Select2::classname(), [
                    'value' => $dataUser['id'],
                    'initValueText' => $dataUser['name'],
                    'name' => 'user_id',
                    'options' => ['placeholder' => \Yii::t('rbac-admin', 'Select User')],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'minimumInputLength' => 0,
                        'ajax' => [
                            'url' => $url,
                            'dataType' => 'json',
                            'data' => new \yii\web\JsExpression('function(params) { return {q:params.term}; }')
                        ],
                        'escapeMarkup' => new \yii\web\JsExpression('function (markup) { return markup; }'),
                        'templateResult' => new \yii\web\JsExpression('function(user) { return user.text; }'),
                        'templateSelection' => new \yii\web\JsExpression('function (user) { return user.text; }'),
                    ],
                ])->label(Yii::t('chanpan', 'Email'));
    }
     

    public static function getUserForm($form, $model, $attri = "user_id", $dbType = "tcc", $dataUser = "") {
        if ($dataUser == "") {
            $sql = "
                    SELECT p.user_id as id, CONCAT(`p`.`firstname`,' ',`p`.`lastname`) as `name`  
                    FROM `user` as u INNER JOIN `profile` as p ON u.id = p.user_id   
                    WHERE u.id = :id
                ";
            $dataUser = \Yii::$app->db->createCommand($sql, [':id' => $model['user_id']])->queryOne();
            $url = "";
            if ($dbType == "tcc") {
                $url = \yii\helpers\Url::to(['/ezforms2/user-tcc/get-user']);
            } else {
                $url = \yii\helpers\Url::to(['/ezforms2/user-tcc/get-user-ncrc']);
            }
        }

        return $form->field($model, $attri)->widget(Select2::classname(), [
                    'value' => $dataUser['id'],
                    'initValueText' => $dataUser['name'],
                    'name' => 'user_id',
                    'options' => ['placeholder' => \Yii::t('rbac-admin', 'Select User')],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'minimumInputLength' => 0,
                        'ajax' => [
                            'url' => $url,
                            'dataType' => 'json',
                            'data' => new \yii\web\JsExpression('function(params) { return {q:params.term}; }')
                        ],
                        'escapeMarkup' => new \yii\web\JsExpression('function (markup) { return markup; }'),
                        'templateResult' => new \yii\web\JsExpression('function(user) { return user.text; }'),
                        'templateSelection' => new \yii\web\JsExpression('function (user) { return user.text; }'),
                    ],
                ])->label(Yii::t('chanpan', 'User'));
    }

    public static function getUserRoles() {
        try {
            return Yii::$app->db->createCommand("SELECT id,role_name,role_start,role_stop,expire_status FROM zdata_matching WHERE rstat NOT IN (0,3) AND user_id like :user_id", [":user_id" => Yii::$app->user->id])->queryAll();
        } catch (Exception $e) {
            return false;
        }
    }

   

    public static function getUserCoCreator() {

        $user_id = isset(\Yii::$app->user->id) ? \Yii::$app->user->id : '';
        if (!empty($user_id)) {
            $where = "co_creator LIKE '%$user_id%' ";
        } else {
            $where = '0';
        }
        return $where;
    }

    public static function getEmail() {
        $email = \common\modules\user\models\User::findOne(isset(Yii::$app->user->id) ? Yii::$app->user->id : '');
        return $email->email;
    }

    public static function getSiteCode() {
        return \Yii::$app->user->identity->profile->sitecode;
    }

    public static function canAdmin() {
        if (Yii::$app->user->can('administrator')) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * @params type string | array  *
     * @param type $conditiion
     * @return type array [sitecode=>'10980']
     */
    public static function getUserByCondition($select, $condition, $limit = "", $typeDb = "default") {
        $siteCode = CNUser::getSiteCode();

        if ($typeDb == 'default') {
            $dataUser = (new \yii\db\Query())
                    ->select($select)
                    ->from('profile')
                    ->where($condition)
                    ->andWhere(['sitecode' => $siteCode]);
        } else if ($typeDb == 'ncrc') {
            $dataUser = (new \yii\db_main\Query())
                    ->select($select)
                    ->from('profile')
                    ->where($condition)
                    ->andWhere(['sitecode' => $siteCode]);
        }

        if ($limit != "") {
            $dataUser->limit($limit);
        }
        $data = $dataUser->all();




        return isset($data) ? $data : '';
    }

    public static function getUserSelect2SingleAjaxBySite($name, $label, $userId, $typeDb = "default") {

        $userId = isset($userId) ? $userId : '';
        $siteCode = CNUser::getSiteCode();
        $data = CNUser::getUserByCondition(["user_id as id", "concat(firstname,' ', lastname) as text"], ['user_id' => $userId], $typeDb);
        $select2 = "<div class='form-group'>";
        $select2 .= '<label>' . $label . '</label>';
        $select2 .= Select2::widget([
                    'name' => $name,
                    'value' => $data['id'],
                    'initValueText' => $data['text'],
                    'options' => ['placeholder' => 'ค้นหา  ' . $label],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'minimumInputLength' => 0,
                        'ajax' => [
                            'url' => \yii\helpers\Url::to(['/ezforms2/user/get-find-user-form-id']),
                            'dataType' => 'json',
                            'data' => new \yii\web\JsExpression('function(params) { return {q:params.term}; }')
                        ],
                        'escapeMarkup' => new \yii\web\JsExpression('function (markup) { return markup; }'),
                        'templateResult' => new \yii\web\JsExpression('function(user) { return user.text; }'),
                        'templateSelection' => new \yii\web\JsExpression('function (user) { return user.text; }'),
                    ],
        ]);
        $select2 .= "</div>";
        return $select2;
    }

    /**
     *
     * @param type string $auth_key="n7QPoyOgkBnkr137EgiflWoHe--wt9P7";
     * @return type auto login
     */
    public static function AutoLogin($auth_key) {


        $identity = \common\modules\user\models\User::findOne(['auth_key' => $auth_key]);
        if (!empty($identity)) {
            return Yii::$app->user->login($identity);
        }
    }

    /**
     *
     * @param type string  $auth_key="n7QPoyOgkBnkr137EgiflWoHe--wt9P7";
     * @return type return user and profile
     */
    public static function GetUserTccByIdAll($auth_key) {
        try {
            $sql = "SELECT * FROM user WHERE auth_key=:auth_key";
            $dataUser = \Yii::$app->db_tcc->createCommand($sql, [':auth_key' => $auth_key])->queryOne();
            //\appxq\sdii\utils\VarDumper::dump($dataUser);
            $user = [
                'user' => $dataUser,
                'profile' => CNUser::GetUserProfileTccByIdAll($dataUser['id'])
            ];
            return isset($user) ? $user : '';
        } catch (\yii\db\Exception $ex) {
            
        }
    }

    public static function GetUserProfileTccByIdAll($user_id) {
        try {
            $sql = "SELECT * FROM user_profile WHERE user_id=:user_id";
            $dataUserProfile = \Yii::$app->db_tcc->createCommand($sql, [':user_id' => $user_id])->queryOne();
            return isset($dataUserProfile) ? $dataUserProfile : '';
        } catch (\yii\db\Exception $ex) {
            
        }
    }

    public static function SaveUserAndProfile($data, $roleName = "") {
        $roleName = isset($roleName) ? $roleName : "author";
        try {
            $dataUserAttribute = [
                'id' => $data["user"]["id"],
                'username' => $data["user"]['username'],
                'email' => isset($data["user"]['email']) ? $data["user"]['email'] : " ",
                'password_hash' => $data["user"]['password_hash'],
                'auth_key' => $data["user"]['auth_key'],
                'confirmed_at' => time(),
                'created_at' => time(),
                'updated_at' => time(),
                'flags' => 0
            ];
            $dataProfileAttribuite = [
                'user_id' => $data['profile']['user_id'],
                'public_email' => isset($data['profile']['email']) ? $data['profile']['email'] : " ",
                'tel' => isset($data['profile']['telephone']) ? $data['profile']['telephone'] : " ",
                'cid' => isset($data['profile']['cid']) ? $data['profile']["cid"] : "",
                'sitecode' => isset($data['profile']['sitecode']) ? $data['profile']['sitecode'] : '00',
                'firstname' => isset($data['profile']['firstname']) ? $data['profile']['firstname'] : '',
                'lastname' => isset($data['profile']['lastname']) ? $data['profile']['lastname'] : '',
                'department' => isset($data['profile']['department']) ? $data['profile']['department'] : '00',
                'certificate' => ' ',
                'position' => 0
            ];
            $saveUser = \Yii::$app->db->createCommand()->insert("user", $dataUserAttribute)->execute();
            if ($saveUser) {
                $saveProfile = \Yii::$app->db->createCommand()->insert("profile", $dataProfileAttribuite)->execute();
                $dataRole = ['item_name' => $roleName, 'user_id' => $data["user"]["id"], 'created_at' => time()];
                \Yii::$app->db->createCommand()->insert("auth_assignment", $dataRole)->execute();

                return $saveProfile;
            }
        } catch (\yii\db\Exception $ex) {
            
        }
    }

    public static function GetUsernCRCByIdAll($auth_key) {
        try {
           $user = \common\modules\user\models\User::find()
            ->where('email=:email AND blocked_at is null AND status = 10 AND confirmed_at is not null', [':email' => $auth_key])->one();
            //\appxq\sdii\utils\VarDumper::dump($user);
            return isset($user) ? $user : null;
        } catch (\yii\db\Exception $ex) {
            EzfFunc::addErrorLog($ex);
            return false;
        }
    }

    public static function GetAuthKey() {
        try {
            $id = CNUser::getUserId();
            $data = (new \yii\db\Query())->select("auth_key")->from("user")->where(['id' => $id])->one();
            return isset($data['auth_key']) ? $data['auth_key'] : '';
        } catch (\yii\db\Exception $ex) {
            EzfFunc::addErrorLog($ex);
            return false;
        }
    }
 
    public static function AddUserDyNamicDb($dbClone, $data, $site = '') {
        try {
            $dataUserAttribute = [
                'id' => $data["user"]["id"],
                'username' => $data["user"]['username'],
                'email' => isset($data["user"]['email']) ? $data["user"]['email'] : " ",
                'password_hash' => $data["user"]['password_hash'],
                'auth_key' => $data["user"]['auth_key'],
                'confirmed_at' => time(),
                'created_at' => time(),
                'updated_at' => time(),
                'flags' => 0
            ];
            $dataProfileAttribuite = [
                'user_id' => $data['profile']['user_id'],
                'public_email' => isset($data['profile']['email']) ? $data['profile']['email'] : " ",
                'tel' => isset($data['profile']['telephone']) ? $data['profile']['telephone'] : " ",
                'cid' => isset($data['profile']['cid']) ? $data['profile']["cid"] : "",
                'sitecode' => isset($data['profile']['sitecode']) ? $data['profile']['sitecode'] : '00',
                'firstname' => isset($data['profile']['firstname']) ? $data['profile']['firstname'] : '',
                'lastname' => isset($data['profile']['lastname']) ? $data['profile']['lastname'] : '',
                'department' => isset($data['profile']['department']) ? $data['profile']['department'] : '00',
                'certificate' => ' ',
                'position' => 0
            ];
            if ($site != '') {
                $dataProfileAttribuite['sitecode'] = '00';
            }
            $saveUser = \Yii::$app->db->createCommand()->insert("{$dbClone}.user", $dataUserAttribute)->execute();
            if ($saveUser) {
                $saveProfile = \Yii::$app->db->createCommand()->insert("{$dbClone}.profile", $dataProfileAttribuite)->execute();
                $dataRole = ['item_name' => "administrator", 'user_id' => $data["user"]["id"], 'created_at' => time()];
                \Yii::$app->db->createCommand()->insert("{$dbClone}.auth_assignment", $dataRole)->execute();

                return $saveProfile;
            }
        } catch (\yii\db\Exception $ex) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
        }
    }

    public static function SaveAuth($roleName, $email) {
        $dataUser = (new \yii\db\Query())
                        ->select("id")
                        ->from("user")
                        ->where(['email' => $email])->one();

        $dataRole = ['item_name' => $roleName, 'user_id' => $dataUser['id'], 'created_at' => time()];
        return \Yii::$app->db->createCommand()->insert("auth_assignment", $dataRole)->execute();
    }

    public static function GetSiteCodeByUserId($user_id, $dataSitecode = '') {
        $user_id = isset($user_id) ? $user_id : '0';
        if (!empty($dataSitecode)) {
            $sql = "SELECT site_name as id , site_detail as name FROM zdata_sitecode WHERE site_name=:site_name";
            $dataUser = Yii::$app->db->createCommand($sql, [':site_name' => $dataSitecode])->queryOne();
        } else {
            $dataUser = (new \yii\db\Query())
                            ->select(['s.site_name as id', 's.site_detail as name'])
                            ->from("profile as p")
                            ->innerJoin('zdata_sitecode as s', "p.sitecode=s.site_name")
                            ->where(['p.user_id' => $user_id])->one();
        }

        return $dataUser;
    }

    public static function GetDepartmentByUserId($user_id) {
        $user_id = isset($user_id) ? $user_id : '0';
        $dataUser = (new \yii\db\Query())
                        ->select(['s.site_name as id', 's.site_detail as name'])
                        ->from("profile as p")
                        ->innerJoin('zdata_sitecode as s', "p.sitecode=s.site_name")
                        ->where(['p.user_id' => $user_id])->one();
        return $dataUser;
    }
    
    
    public static function getUserProjectByUserId($user_id, $textsearch=''){
        try{
            $query = (new \yii\db\Query())->select(['up.id', 'up.url as url', 'ddb.dbname as dbname', 'ddb.create_at', 'ddb.proj_name as project_name'])
                ->from('user_project as up')
                ->innerJoin('profile as p', 'up.user_id = p.user_id')
                ->innerJoin('dynamic_db as ddb', 'up.url = ddb.url')
                ->where('up.user_id=:user_id',[':user_id'=>$user_id])
                ->groupBy(['up.url'])
                ->all();
            return $query;
        } catch (Exception $ex) {
            EzfFunc::addErrorLog($ex);
            return false;
        }
    }
    
     public static function getCountUser() {
         try{
                $sql = "
                    SELECT count(*) as count_user FROM `user` as u 
                    INNER JOIN `profile` as p ON u.id=p.user_id
                    WHERE blocked_at is null AND status = 10 AND confirmed_at is not null
                 ";
                 $count = \Yii::$app->db->createCommand($sql)->queryOne();
                 return $count['count_user'];
         } catch (Exception $ex) {
             EzfFunc::addErrorLog($x);
             return false;
         }
         
     }

}
