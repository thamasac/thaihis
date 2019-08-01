<?php

namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use common\models\LoginForm;
use yii\filters\VerbFilter;
use backend\modules\manageproject\classes\CNSDatabaseFunc;
use common\modules\user\models\User;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['error', 'index', 'check-data', 'auth', 'update-form', 'frontend', 'popup-help'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'main', 'help'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'auth' => [
                'class' => 'yii\authclient\AuthAction',
                'successCallback' => [$this, 'onAuthSuccess'],
            ],
        ];
    }

    public function onAuthSuccess($client)
    {
        (new \common\modules\user\classes\AuthHandler($client))->handle();
    }


    public function beforeAction($action)
    {
        if ($action->id == 'index') {
            $chanpanClone = isset($_GET['auth_key']) ? $_GET['auth_key'] : '';
            $status = isset($_GET['status']) ? $_GET['status'] : '';
            if (!empty($chanpanClone)) {
                $auth_key = \cpn\chanpan\classes\CNEncript::encrypt_decrypt('decrypt', $chanpanClone);
                if (!empty($auth_key) || $auth_key != "") {
                    $usernCRC = \cpn\chanpan\classes\CNUser::GetUsernCRCByIdAll($auth_key);
                    if(!empty($usernCRC) && isset($usernCRC)){
                        //::AutoLogin($auth_key);
                        \common\modules\user\classes\CNSocialFunc::autoLogin($usernCRC);
                    }
                }
            }
            return parent::beforeAction($action);
        }
        return true;
    }

    public function  actionPopupHelp($id)
    {
        $model = \backend\modules\ezforms2\models\EzformFields::find()->where(['ezf_field_id' => $id])->one();
        
        return $this->render('popup_help', [
            'model' => $model
        ]);
    }
    
    public function CloneDatabase()
    {

        $domain = \Yii::$app->params['current_url'];//\cpn\chanpan\classes\CNServer::getDemain();
        $main_url = \Yii::$app->params['main_url']; //\backend\modules\core\classes\CoreFunc::getParams('main_url', 'url');

        if ($domain == $main_url || $domain == "backend.ncrc.local") {
            $dataConfig = CNSDatabaseFunc::GetDatabaseName();
            if (!empty($dataConfig)) {
                foreach ($dataConfig as $dataConfig) {
                    $dbnameClone = $dataConfig['dbname'];
                    $template = $dataConfig['project_template'];
                    $user_id = $dataConfig['user_create'];
                    $checkDataBase = CNSDatabaseFunc::CheckDatabaseByName($dbnameClone, $template, $user_id);
                    //echo $checkDataBase;
                    if ($checkDataBase == 0) {
                    }

                }
            }
        }

    }

    public function actionMain()
    {
        $this->CloneDatabase();
        //$this->CloneDatabase();
    }

    public function actionUpdateCommand()
    {
        $status = [];
        $dataLogUpdate = \backend\modules\manageproject\classes\CNUpdateCommandFunc::getLogUpdate();

        if (!empty($dataLogUpdate)) {
            foreach ($dataLogUpdate as $d) {
                if ($d['update_id'] != '1') {//มีบางอันอัปเดทไม่สำเร็จ ต้องอัปเดทใหม่
                    $data2 = \backend\modules\manageproject\classes\CNUpdateCommandFunc::getDataUpdateProjectById($d['id']);
                    $exect = \backend\modules\manageproject\classes\CNUpdateCommandFunc::updateCommand($data2['sql_command'], $data2['id'], 'getid');
                    if ($exect) {
                        array_push($status, 1);
                    }
                }
            }
        }
        //\appxq\sdii\utils\VarDumper::dump($dataLogUpdate);
//        exit();
        $maxId = \backend\modules\manageproject\classes\CNUpdateCommandFunc::getMaxIdLogUpdate();
        //\appxq\sdii\utils\VarDumper::dump($maxId);
        $dataLogUpdate = \backend\modules\manageproject\classes\CNUpdateCommandFunc::getDataUpdateProject($maxId);

        if (empty($dataLogUpdate)) {
            return \backend\modules\manageproject\classes\CNMessage::getSuccess("Success");
        }

        if (!empty($dataLogUpdate)) {
            foreach ($dataLogUpdate as $d) {
                $exect = \backend\modules\manageproject\classes\CNUpdateCommandFunc::updateCommand($d['sql_command'], $d['id']);
                if ($exect) {
                    array_push($status, 1);
                }
            }
        }
        if (in_array(1, $status)) {
            return \backend\modules\manageproject\classes\CNMessage::getSuccess("Success");
        } else {
            return \backend\modules\manageproject\classes\CNMessage::getError("Error");
        }
    }
    public function actionGetToken(){
        $user_id = \cpn\chanpan\classes\CNUser::getUserId();
        $auth_key = \cpn\chanpan\classes\CNUser::GetAuthKey();
        return $auth_key;
        
        $token = \cpn\chanpan\classes\CNEncript::generator_token($user_id);
        if($token['status'] == 'success'){
            return \backend\modules\manageproject\classes\CNMessage::getSuccess('Success', $token);
        } 
        return \backend\modules\manageproject\classes\CNMessage::getError("Error");
        
    }
    public function actionAutoLogin(){
        //\appxq\sdii\utils\VarDumper::dump(\cpn\chanpan\classes\utils\CNProject::get_project_all());
        $token = Yii::$app->request->get('token', '');
        $auto_login = \cpn\chanpan\classes\CNUser::AutoLogin($token);
        //auto login
        
        $param_url   = Yii::$app->request->get('param_url', '');
        if($token != ''){
            $auth_key = \cpn\chanpan\classes\CNEncript::validate_token($token);
            if($auth_key){ 
                 $auto_login = \cpn\chanpan\classes\CNUser::AutoLogin($auth_key);
                 \cpn\chanpan\classes\CNEncript::remove_token($token);
                 //http://backend.ncrc.local/site/auto-login?token=bLnkL10kxClVFxoauQ6eLWk6WYm9bE8Z&param_url=/ezforms2/ezform/index
                 if($auto_login){
                     return $this->redirect([$param_url]);
                 }
            } else {
                throw new \yii\web\HttpException(401, 'You get a 401 error without any authentication challenge.');
            }
        }
    }//auto login 
    
    
    public function  actionIndex()
    { 
        \backend\modules\manageproject\classes\CNFunc::addLog('View site/index');
        if(isset(Yii::$app->session['line_id']) && !empty(Yii::$app->session['line_id']) && !\Yii::$app->user->isGuest){
            $this->layout = 'main2';
            return $this->render("index_line");
        }
        
        
        
        $proj_h =   \Yii::$app->request->get('proj_h', '');  
        $authKey = \Yii::$app->request->get('auth_key', ''); 
        $portalUrl = \Yii::$app->params['main_url'];
        
        $currentDomain = \Yii::$app->params['current_url']; 
        $localUrl = isset(\Yii::$app->params['local_url']) ? \Yii::$app->params['local_url'] : [];
        if(!$localUrl){
            $localUrl ='backend.ncrc.local';
        } 
        $localUrlArr = explode(',', $localUrl);
        
        $urlModuleDefault = isset(\Yii::$app->params['url_index']) ? \Yii::$app->params['url_index'] : '/ezmodules/ezmodule/view?id=1521647584047559700&addon=0&tab=1528945511006792400';
        
        if(\cpn\chanpan\classes\CNServerConfig::isLocal() && !\cpn\chanpan\classes\CNServerConfig::isPortal()){
            return $this->redirect(["{$urlModuleDefault}"]);
        }
        
        $urlTest = isset(\Yii::$app->params['url_test']) ? \Yii::$app->params['url_test'] : '';
        if(in_array($currentDomain, $localUrlArr) || $currentDomain == $urlTest) {
            return $this->render("index");
        }
       
        
        if ($currentDomain != $portalUrl) {
            
            $getProject = \Yii::$app->params['model_dynamic'];
            
            
            if (!empty($getProject)) {
                $myproject = \Yii::$app->params['my_project'];
                if (!empty($myproject)) {
                    $proj_home = isset($myproject['data_create']['proj_home']) ? $myproject['data_create']['proj_home'] : '2';
                    $projaconym = isset($myproject['data_create']['projectacronym']) ? $myproject['data_create']['projectacronym'] : '';
                    \Yii::$app->params['proj_aconym'] = $projaconym;
                    $this->layout = '@backend/views/layouts/project_home';
                    
                    if ($proj_h == '1' || !empty($authKey)) {
                        if ($proj_home == '1') {
                            return $this->redirect(Yii::$app->homeUrl, 302)->send();
                        }
                    } elseif ($proj_h == '' && $proj_h != '0' && $proj_h != '1') {
                        return $this->render("project_home/index");
                    }else{
                        //\appxq\sdii\utils\VarDumper::dump($urlModuleDefault);
                        return $this->redirect(["{$urlModuleDefault}"]);
                    }
                } else {
                    return $this->redirect(["{$urlModuleDefault}"]);
                }
            } else {
                return $this->redirect(["{$urlModuleDefault}"]);
            }
        }
         
        
        if( !isset ( $authKey ) && \Yii::$app->user->isGuest ){//ยังไม่ login
            return $this->redirect(['/user/login']);
        }else if( isset ($authKey) && \Yii::$app->user->isGuest ){//auth_key มา 
            $auth_key = \cpn\chanpan\classes\CNEncript::encrypt_decrypt('decrypt', $authKey);
            if (isset ( $authKey ) && $authKey != "") {
                $usernCRC = \cpn\chanpan\classes\CNUser::GetUsernCRCByIdAll($authKey);
                if(!empty($usernCRC)){ 
                    \common\modules\user\classes\CNSocialFunc::autoLogin($usernCRC);
                } else {
                    return $this->redirect(['/user/login']);
                }
            } else {
                return $this->redirect(['/user/login']);
            }
        }
        if ($currentDomain == $portalUrl) {
            return $this->render('index');
        }
        
        return $this->redirect(["{$urlModuleDefault}"]);
    }

    public function actionChangePassword()
    {
        if(!empty($_GET['cancel'])){
            $user_id = \cpn\chanpan\classes\CNUser::getUserId();
            $profile = \common\modules\user\models\Profile::findOne(['user_id' => $user_id]);
            $profile->status_update = '2';
            if ($profile->save()) {
                return \backend\modules\manageproject\classes\CNMessage::getSuccess("Chanpan password success.");
            } else {
                return \backend\modules\manageproject\classes\CNMessage::getError(json_encode($model->errors));
            }
        }
        if (!empty($_POST)) {
            $status_update = isset(Yii::$app->user->identity->profile->status_update) ? Yii::$app->user->identity->profile->status_update : '1';
            if ($status_update == 1 && !\Yii::$app->user->isGuest) {

                $model = new \common\modules\user\models\ChangePasswordForm();
                if (Yii::$app->request->isAjax) {

                    if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                        //print_r($_POST);exit();
                        $user_id = \cpn\chanpan\classes\CNUser::getUserId();
                        $user = User::findOne($user_id);
                        $profile = \common\modules\user\models\Profile::findOne(['user_id' => $user_id]);
                        $profile->status_update = '2';
                        $user->password = $model->new_password;
                        if ($user->save() && $profile->save()) {

                            return \backend\modules\manageproject\classes\CNMessage::getSuccess("Chanpan password success.");
                        } else {
                            return \backend\modules\manageproject\classes\CNMessage::getError(json_encode($model->errors));
                        }
                    }
                }
            }
        }
    }

    public function actionHelp()
    {
        return $this->render('help');
    }

    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    public function actionLogout()
    {

        \Yii::$app->getUser()->logout();
        return $this->goHome();
    }

    public function actionUpdateForm()
    {

    }

    public function actionMyOwn()
    {
        return $this->render("my-own");
    }

    public function actionAssignToMe()
    {
        return $this->render("assigned_to_me");
    }

    public function actionCoCreator()
    {
        return $this->render("co-creator");
    }

    public function actionProjectTrash()
    {
        return $this->render("project_trash");
    }

    public function actionProjectTemplates()
    {
        return $this->render("project_templates");
    }

    public function actionProjectSeeking()
    {
        return $this->render("project_seeking");
    }

    public function actionEditTemplates()
    {
        if (Yii::$app->user->can('administrator')) {
            return $this->render('templates/edit-templates', [

            ]);
        }
    }

    public function actionInviteTemplates()
    {
        if (Yii::$app->user->can('administrator')) {
            $paramName = 'invite_email';
            $detail = \backend\modules\core\classes\CoreFunc::getParams($paramName, 'invite');
            if (\Yii::$app->request->post()) {
                $detail = \Yii::$app->request->post('detail', '');
                $data = ['option_value' => $detail];
                //print_r($data);return;
                if (\backend\modules\core\classes\CoreFunc::updateCoreOptionValueByNameInDb($paramName, $data)) {
                    return \backend\modules\manageproject\classes\CNMessage::getSuccess('Save success.');
                } else {
                    return \backend\modules\manageproject\classes\CNMessage::getError('error');
                }
            }
            $html = $this->renderAjax("templates/invite", [
                'detail' => isset($detail) ? $detail : 'default'
            ]);
            return \yii\helpers\Json::encode($html);
        }
    }

    public function actionVerifyEmailTemplates()
    {
        if (Yii::$app->user->can('administrator')) {
            $paramName = 'email_register';
            $detail = \backend\modules\core\classes\CoreFunc::getParams($paramName, 'Verify');
            if (\Yii::$app->request->post()) {
                $detail = \Yii::$app->request->post('detail', '');
                $data = ['option_value' => $detail];
                //print_r($data);return;
                if (\backend\modules\core\classes\CoreFunc::updateCoreOptionValueByNameInDb($paramName, $data)) {
                    return \backend\modules\manageproject\classes\CNMessage::getSuccess('Save success.');
                } else {
                    return \backend\modules\manageproject\classes\CNMessage::getError('error');
                }
            }
            $html = $this->renderAjax("templates/verify-email", [
                'detail' => isset($detail) ? $detail : 'default'
            ]);
            return \yii\helpers\Json::encode($html);
        }
    }

    public function actionRecoverPasswordTemplates()
    {
        if (Yii::$app->user->can('administrator')) {
            $paramName = 'email_recover';
            $detail = \backend\modules\core\classes\CoreFunc::getParams($paramName, 'email_recover');
            if (\Yii::$app->request->post()) {
                $detail = \Yii::$app->request->post('detail', '');

                $data = ['option_value' => $detail];
                if (\backend\modules\core\classes\CoreFunc::updateCoreOptionValueByNameInDb($paramName, $data)) {
                    return \backend\modules\manageproject\classes\CNMessage::getSuccess('Save success.');
                } else {
                    return \backend\modules\manageproject\classes\CNMessage::getError('error');
                }
            }

            $html = $this->renderAjax('templates/recover-password', ['detail' => $detail]);
            return \yii\helpers\Json::encode($html);
        }
    }
    
    public function actionShortModule(){
        return $this->renderAjax('module/short-module');
    }
    public function actionViewShortModule(){
        return $this->renderAjax('module/view-short-module');
    }
    public function actionShortModuleAll(){
        $term = Yii::$app->request->get('term', '');
        $moduleAll = \backend\modules\ezmodules\models\Ezmodule::find()->where(['active'=>1]);
        $dataModule = '';
        if($term != ''){
            $dataModule = $moduleAll->andWhere('ezm_short_title LIKE :title OR ezm_name LIKE :name',[
                ':title'=>"%{$term}%",
                ':name'=>"%{$term}%"
            ])->asArray()->all();
        }else{
            $dataModule = $moduleAll->asArray()->all();
        }
        $moduleAllProvider = new \yii\data\ArrayDataProvider([
            'allModels' => $dataModule,
//            'sort' => [
//                'attributes' => ['id', 'username', 'email'],
//            ],
            'pagination' => [
                'pageSize' => 100,
            ],
        ]);
        return $this->renderAjax('module/short-module-all', [
            'moduleAllProvider'=>$moduleAllProvider
        ]);
    }
    public function actionShortModuleSelect(){
        $editMode = Yii::$app->request->get('edit_mode', 10);
        $mocStr = isset(Yii::$app->params['shot_menu_head']) ? Yii::$app->params['shot_menu_head']  : '';
        $mocArr = explode(",", $mocStr);
        
        $output=[];
        
        foreach($mocArr as $key => $id){
            $module = \backend\modules\ezmodules\models\Ezmodule::find()->where(['active'=>1, 'ezm_id'=>$id])->one();
            //'approved'=>1
            if(!empty($module)){
                 $output[$key]=[
                    'ezm_id'=>$module['ezm_id'],
                    'ezm_icon'=> isset($module['ezm_icon']) ? $module['ezm_icon'] : '',
                    'icon_base_url'=>isset($module['icon_base_url']) ? $module['icon_base_url'] : '',
                    'ezm_short_title'=> isset($module['ezm_short_title']) ? $module['ezm_short_title'] : '',
                    'ezm_name'=> isset($module['ezm_name']) ? $module['ezm_name'] : ''
                ];
            }
            
        }
        return $this->renderAjax('module/short-module-select', [
            'output'=>$output,
            'editMode'=>$editMode
        ]);
        //\appxq\sdii\utils\VarDumper::dump($output);
    }
    public function actionDeleteShortModuleSelect(){
        $ezmId = Yii::$app->request->post('ezm_id', '');
        $mocStr = isset(Yii::$app->params['shot_menu_head']) ? Yii::$app->params['shot_menu_head']  : '';
        $mocArr = explode(",", $mocStr);
        foreach($mocArr as $key => $id){
            if($id == $ezmId){
                unset($mocArr[$key]);
            }
        }
        $out = implode(',', $mocArr);
        $data=['option_value'=>$out];
        $update = \backend\modules\core\classes\CoreFunc::updateCoreOptionValueByNameInDb("shot_menu_head", $data);
        
        if(isset(Yii::$app->session['short-module-title'])){
            unset(Yii::$app->session['short-module-title']);
        }
        //Yii::$app->session['short-module-title'] = $module;
        if($update){
$clear = \backend\modules\manageproject\classes\CNHeaderSetting::setShortModule();
            return \cpn\chanpan\classes\CNResponse::getSuccess("Delete Success");
        }else{
            return \cpn\chanpan\classes\CNResponse::getError("Delete Fail");
        }
    }
    public function actionSortShortModuleSelect(){
        $data = Yii::$app->request->post('data', '');
        
        $data=['option_value'=>$data];
        //\appxq\sdii\utils\VarDumper::dump($data);
        $update = \backend\modules\core\classes\CoreFunc::updateCoreOptionValueByNameInDb("shot_menu_head", $data);
        if($update){
$clear = \backend\modules\manageproject\classes\CNHeaderSetting::setShortModule();
            return \cpn\chanpan\classes\CNResponse::getSuccess("Delete Success");
        }else{
            return \cpn\chanpan\classes\CNResponse::getError("Delete Fail");
        }
    }
    public function actionAddShortModuleSelect(){
        $ezmId = Yii::$app->request->post('ezm_id', '');
        $ezmodule = \backend\modules\ezmodules\models\Ezmodule::findOne($ezmId);
        //\appxq\sdii\utils\VarDumper::dump($ezmodule);
        $mocStr = isset(Yii::$app->params['shot_menu_head']) ? Yii::$app->params['shot_menu_head']  : '';
        $mocArr = explode(",", $mocStr);
        $dataArr = [];
        if(in_array($ezmId, $mocArr)){
            return \cpn\chanpan\classes\CNResponse::getError("This {$ezmodule['ezm_name']} is already in use.");
        }
        
        
        array_push($dataArr, $ezmId);
        $mocArr = \yii\helpers\ArrayHelper::merge($dataArr,$mocArr); 
        $out = implode(',', $mocArr);
        $data=['option_value'=>$out];
        $update = \backend\modules\core\classes\CoreFunc::updateCoreOptionValueByNameInDb("shot_menu_head", $data);
        if(isset(Yii::$app->session['short-module-title'])){
            unset(Yii::$app->session['short-module-title']);
        }
        if($update){
$clear = \backend\modules\manageproject\classes\CNHeaderSetting::setShortModule();
            return \cpn\chanpan\classes\CNResponse::getSuccess("Add Success");
        }else{
            return \cpn\chanpan\classes\CNResponse::getError("Add Fail");
        }
    }
    
    public function actionShowProjectDetail(){
        $modelDynamic = isset(Yii::$app->params['my_project']) ? Yii::$app->params['my_project'] : '';
        return $this->renderAjax('show-project-detail', ['model'=>$modelDynamic]);
//        \appxq\sdii\utils\VarDumper::dump($modelDynamic);
    }
    
    public function actionMyFavorites(){
        return $this->renderAjax('my_favorites/_myfav');
    }
    
    
    //site-navigator
    public function actionSiteNavigator(){
        return $this->renderAjax('site-navigator');
    }
    
    public function actionUpdateDatabase(){
        $dbname = ['ncrc_RCT_1531221785032641100','ncrc_scratch_1530522693000','ncrc_Experiment_1531218956057448300','ncrc_Cross_Sec_Single_1531219155046435300',
'ncrc_Cross_Sec_Multi_1531221317072401800','ncrc_Case_Control_1531221352014490200','ncrc_RetroCohort_1531221374004795500','ncrc_Prospective_Gen_1531221587079994200','ncrc_Prospective_Post_1531221629050023400',
'ncrc_dreg_1531221697019761700','ncrc_Experimental_1531221730050732600','ncrc_RCT_1531221785032641100','ncrc_scratch1531376266_1531376278057487300',
'ncrc_poll1531460069_1531460077023802500','ncrc_poll1531460495_1531460512069673100','ncrc'];
//\appxq\sdii\utils\VarDumper::dump('ok');
//        $data = (new \yii\db\Query())
//                ->select('*')->from('dynamic_db')->where('rstat not in(0,3)')->all();
        
        $sql="SELECT * FROM dynamic_db";
        $data = \Yii::$app->db->createCommand($sql)->queryAll();
       // \appxq\sdii\utils\VarDumper::dump($data);
        
        //site_switch
        $output= [];
       /*
            ALTER TABLE `{$db['dbname']}`.`profile` 
                        ADD COLUMN `site_switch` varchar(255) NULL COMMENT 'Site Switch' AFTER `invite`;  
        * 
        *   ALTER TABLE `ncrc_RCT_1531221785032641100`.`profile` 
            DROP COLUMN `original_site`,
            DROP COLUMN `site_switch`; 
        */
        
        foreach($dbname as $k=>$db){
            try{
                //if(!in_array(['dbname'], $dbname))   {
                    $sql="
                        ALTER TABLE `{$db}`.`profile` 
                        DROP COLUMN `site_switch`;       
                    ";
                   \Yii::$app->db->createCommand($sql)->execute();   
                //} 
            } catch (\yii\db\Exception $ex) {
                $output[$k]=['msg'=>$ex->getMessage()];
            }
        }
        \appxq\sdii\utils\VarDumper::dump($output);
        
    }
    
    public function  actionSwitchSite(){
        if(\Yii::$app->request->post()){
            $switch_site = \Yii::$app->request->post('switch_site', '');
            $sql="UPDATE `profile` SET sitecode=:switch_site";
            $exec = \Yii::$app->db->createCommand($sql, [':switch_site'=>$switch_site])->execute();
            if($exec){
                return \backend\modules\manageproject\classes\CNMessage::getSuccess('Switch Site Success');
            }else{
                return \backend\modules\manageproject\classes\CNMessage::getSuccess('Switch Site Fail');
            }
        }
        return $this->render('switch-site');
    }
    public function getOriginalSite($user_id = ''){
        if($user_id == ''){
            $user_id = \cpn\chanpan\classes\CNUser::getUserId();
        }
        $site_arr = [];
        $site_name='';
        $site_detail = '';
        $site_original = (new \yii\db\Query())->select('*')->from('zdata_1552458740063735000')->where('user_id=:user_id AND rstat not in(0,3)',[':user_id'=>$user_id])->one();
        if($site_original){
            $site_name = isset($site_original['original_site'])?$site_original['original_site']:'';
            $site_detail = \common\modules\user\classes\CNSitecode::getSiteValue($site_name); 
        }else{
            $site_name = \common\modules\user\classes\CNSitecode::getSiteCodeCurrent();
            $site_detail = \common\modules\user\classes\CNSitecode::getSiteValue();
        }
        $site_arr =['site_name'=>$site_name, 'site_detail'=>$site_detail];
        return $site_arr;
    }
    public function getSwitchSite(){
        $site_arr = [];
        $site_name = \common\modules\user\classes\CNSitecode::getSiteCodeCurrent();
        $site_detail = \common\modules\user\classes\CNSitecode::getSiteValue();
        $site_arr =['site_name'=>$site_name, 'site_detail'=>$site_detail];
        return $site_arr;
    }
    public function  actionGetUser(){
        try{
            $site_arr = $this->getOriginalSite();
            $switch_site = $this->getSwitchSite();
            
            $user_id = \cpn\chanpan\classes\CNUser::getUserId();
            $users = \cpn\chanpan\classes\CNUser::getUserNcrcById($user_id);
            $name = '';
            if($users){
                $name = "{$users['profile']['firstname']} {$users['profile']['lastname']}";
            }
            $data = ['user_id'=>$user_id, 'name'=>$name, 'site'=>$site_arr['site_name'], 'site_detail'=>$site_arr['site_detail'],
                'switch_site_name'=>$switch_site['site_name'], 'switch_site_detail'=>$switch_site['site_detail']
                ];
            return \yii\helpers\Json::encode($data);
        } catch (\yii\db\Exception $ex) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
            return false;
        }
        
    }
    
    public function actionGetOriginalSite(){
        $user_id = \Yii::$app->request->post('user_id', '');
        $data = $this->getOriginalSite();
        return \yii\helpers\Json::encode($data);
    }
    /**
     * 
     * @param type $user_id | Bigin User id
     * @param type $dbname | String Database name
     * @return boolean true|false
     */
    private function set_user($user_id,$dbname){
        try {
            $table_user = 'user';
            $table_profile = 'profile';
            $sql = "REPLACE INTO {$dbname}.{$table_user} (SELECT * FROM {$table_user} WHERE id={$user_id})";
            \Yii::$app->db_main->createCommand($sql)->execute();
            $sql = "REPLACE INTO {$dbname}.{$table_profile} (SELECT * FROM {$table_profile} WHERE user_id={$user_id})";
            return \Yii::$app->db_main->createCommand($sql)->execute();
        } catch (\yii\db\Exception $ex) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
            return false;
        }
    }
    /**
     * 
     * @param type $dbname | String Database name
     * @param type $user_id | Bigin User id
     * @param type $role_name | String Role Name
     * @return boolean true|false
     */
    private function set_role($dbname='', $user_id, $role_name){
        $dbname = isset($dbname)&&$dbname != ''?"{$dbname}.":'';
        try {
            $table = 'auth_assignment';
            $check_role = (new \yii\db\Query())->select('*')->from($table)->where(['user_id'=>$user_id, 'item_name'=>$role_name])->one();
            if($check_role){
              $sql = "REPLACE INTO {$dbname}{$table} (SELECT * FROM {$table} WHERE user_id='{$user_id}' AND item_name='{$role_name}')";  
              return \Yii::$app->db_main->createCommand($sql)->execute();
            }else{
                $sql = "REPLACE INTO(user_id, item_name, created_at) {$dbname}{$table} VALUES(:user_id, :item_name, :created_at)";
                return \Yii::$app->db_main->createCommand($sql,[
                    ':user_id'=>$user_id,
                    ':item_name'=>$role_name, 
                    ':created_at'=>time()
                ])->execute();
            }
            
            
        } catch (\yii\db\Exception $ex) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
            return false;
        }
    }
    private function delete_role($dbname='', $user_id, $role_name){
        $dbname = isset($dbname)&&$dbname != ''?"{$dbname}.":'';
        try {
            $table = 'auth_assignment';
            //$sql="DELETE FROM {$dbname}{$table} WHERE user_id=:user_id AND item_name=:item_name";
            
            $sql="DELETE FROM {$dbname}{$table} WHERE user_id={$user_id} AND item_name='{$role_name}'";
            $del = \Yii::$app->db_main->createCommand($sql)->execute();
            //$del = \Yii::$app->db_main->createCommand($sql,[':user_id'=>$user_id, ':item_name'=>$role_name])->execute();
            if($del){
                return \backend\modules\manageproject\classes\CNMessage::getSuccess("success");
            }
        } catch (\yii\db\Exception $ex) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
            return false;
        }
    }
    public function actionDelCoCreator(){
        if(\Yii::$app->user->can('administrator')){
            $id = Yii::$app->request->get('id', '');
            $ezf_id = Yii::$app->request->get('ezf_id', '');
            $action = Yii::$app->request->get('data_action', '');
            $table = 'zdata_1550237431020291300';
            $table_auth = 'auth_assignment';
            $sql="SELECT * FROM {$table} WHERE id=:data_id";
            $data_co = \Yii::$app->db->createCommand($sql, [':data_id'=>$id])->queryOne();
            if($data_co){
                $role_admin = 'administrator';
                $co_user    = isset($data_co['co_user'])?$data_co['co_user']:'';
                $project_id = isset($data_co['co_target']) ? $data_co['co_target'] : '';
                $data_project = \cpn\chanpan\classes\utils\CNProject::getMyProjectById($project_id);
                $dbname = isset($data_project['data_dynamic']['dbname']) ? $data_project['data_dynamic']['dbname'] : '';
                $role_name = 'administrator';
                $this->delete_role($dbname, $co_user, $role_name); 
            }
        }
    }
    public function actionSetCoCreator(){
        $id = Yii::$app->request->post('id', '');
        $ezf_id = Yii::$app->request->post('ezf_id', '');
        $action = Yii::$app->request->post('data_action', '');
        $table = 'zdata_1550237431020291300';
        $table_auth = 'auth_assignment';
        $sql="SELECT * FROM {$table} WHERE id=:data_id";
        $data_co = \Yii::$app->db->createCommand($sql, [':data_id'=>$id])->queryOne();
        $role_admin = 'administrator';
        try{
            if(!$data_co){return;}
            $co_user    = isset($data_co['co_user'])?$data_co['co_user']:'';
            if (\cpn\chanpan\classes\CNServerConfig::isPortal()) { //Portal
                $project_id = isset($data_co['co_target']) ? $data_co['co_target'] : '';
                $data_project = \cpn\chanpan\classes\utils\CNProject::getMyProjectById($project_id);
                $dbname = isset($data_project['data_dynamic']['dbname']) ? $data_project['data_dynamic']['dbname'] : '';
                try {
                    $sql = "REPLACE INTO {$dbname}.{$table} (SELECT * FROM {$table} WHERE id={$id})";
                    \Yii::$app->db_main->createCommand($sql)->execute();
                } catch (\yii\db\Exception $ex) {
                    \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
                }
                $data_cocreate = (new \yii\db\Query())->select('*')->from($table)->where('rstat not in(0,3)')->all();
                if ($data_cocreate) { //set role
                    
                    $this->set_user($co_user, $dbname);
                    $this->set_role($dbname, $co_user, $role_admin);
                    $this->set_role($dbname, $co_user, 'author');
                    if ($action == 'delete') {
                        $this->delete_role($dbname, $co_user, $role_admin);
                    }  
                }
            } //Portal
            else {
                try {
                    $dbname = \cpn\chanpan\classes\CNServerConfig::get_db();
                    $sql = "REPLACE INTO `ncrc`.{$table} (SELECT * FROM {$dbname}.{$table} WHERE id={$id})";
                    \Yii::$app->db_main->createCommand($sql)->execute();

                    if ($action == 'delete') {
                        $this->delete_role($dbname, $co_user, $role_admin);
                        $this->set_role($dbname, $co_user, 'author');
                    } else {
                        $this->set_user($co_user, $dbname);
                        $this->set_role($dbname, $co_user, $role_admin);
                        $this->set_role($dbname, $co_user, 'author');
                    }
                } catch (\yii\db\Exception $ex) {
                    \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
                }
            }
        } catch (\yii\db\Exception $ex) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
        }
        
//        try {
//            $columns = [
//                'user_id' => $co_user,
//                'item_name' => $role_admin,
//                'created_at' => time()
//            ];
//            $insert = \Yii::$app->db_main->createCommand()->insert('auth_assignment', $columns)->execute();
//        } catch (\yii\db\Exception $ex) {
//            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
//        }
    }//site/set-co-creator
    
    public function actionProjectSetting(){
        return $this->render('project-setting');
    }
    
    public function actionSetProjectName(){
       try{
           $name = \Yii::$app->request->post('name', '');
           $dataOption = ['option_value' => $name];
           $update = \backend\modules\core\classes\CoreFunc::updateCoreOptionValueByName("company_name", $dataOption, '');
           if($update){
                return \backend\modules\manageproject\classes\CNMessage::getSuccess('success');
           }
           return \backend\modules\manageproject\classes\CNMessage::getError('error');
       } catch (\yii\db\Exception $ex) {
           \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
       }
    }
    
    public function actionTest(){
        try{
            $model = (new \yii\db\Query())->select('*')
                ->from('dynamic_db')
                ->where('rstat not in(0,3)')->all();
               // \appxq\sdii\utils\VarDumper::dump($model);
            if($model){
                foreach($model as $k=>$v){
                    if(isset($v['dbname']) && $v['dbname'] != ''){
                        $sql="
                            CREATE TABLE IF NOT EXISTS {$v['dbname']}.`system_log` (
                                `id` bigint(20) NOT NULL AUTO_INCREMENT,
                                `create_date` datetime DEFAULT NULL COMMENT 'Time/Date',
                                `create_by` varchar(255) DEFAULT NULL COMMENT 'Name',
                                `action` varchar(255) DEFAULT NULL COMMENT 'Action',
                                `detail` longtext,
                                PRIMARY KEY (`id`)
                              ) ENGINE=InnoDB AUTO_INCREMENT=1560919037087258801 DEFAULT CHARSET=utf8
                        ";
                        Yii::$app->db->createCommand($sql)->execute();
                    }
                    
                }
            }
        } catch (\yii\db\Exception $ex) {

        }
    }
}
