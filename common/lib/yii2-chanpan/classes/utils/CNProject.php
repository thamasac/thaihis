<?php
 

namespace cpn\chanpan\classes\utils;
use yii\db\Exception; 
use Yii;
class CNProject {
    public static function dynamic_db(){
        return "dynamic_db";
    }
    public static function zdata_create_project(){
        return "zdata_create_project";
    }

    /**
     * 
     * @return type object  db_main
     * [
           'data_dynamic'=>[ 'url' => 'backend.ncrc.local', ...],
           'data_create'=>['projectacronym' => 'chanpan',....]
       ]
     */
    public static function getMyProject(){
       try{
            $dataDynamic = \common\modules\user\classes\CNDynamicDb::Query();
            $dataCreate = \backend\modules\manageproject\classes\CNSettingProjectFunc::MyProjectByidNoUser($dataDynamic['data_id']);
            $out=[
                'data_dynamic'=>(!empty($dataDynamic)) ? $dataDynamic : false,
                'data_create'=>(!empty($dataCreate)) ? $dataCreate[0] : false,
            ];
            return $out;
       } catch (Exception $ex) {
           \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
           return;
       }
    }
    /**
     * 
     * @param type $dataid 
     * @params $db  if $db=='' db  else db_main
     * @return obj zdata_create_project  
     */
    public static function getMyProjectById($dataid, $db='', $query=''){         
        $db = (empty($db) || $db == '') ? \Yii::$app->db : \Yii::$app->db_main;
        if ($db == '' && \cpn\chanpan\classes\CNServerConfig::isPortal() || \cpn\chanpan\classes\CNServerConfig::isLocal()) {
            $db = \Yii::$app->db;
        }
        if(\cpn\chanpan\classes\CNServerConfig::isPortal() || \cpn\chanpan\classes\CNServerConfig::isLocal()){ 
           $db = \Yii::$app->db;
        }
//       if(Yii::$app->user->identity->email == 'chanpan.nuttaphon1993@gmail.com'){
//           \appxq\sdii\utils\VarDumper::dump($db);
//       }
        //\appxq\sdii\utils\VarDumper::dump($dataid);
        try{
            $dataCreate=(new \yii\db\Query())->select('*')
                    ->from(self::zdata_create_project())
                    ->where('id=:id AND rstat not in(0,3)',[':id'=>$dataid])
                    ->one($db);            
            $dataDynamic = \common\modules\user\classes\CNDynamicDb::Query($dataid, $query);
                        $out=[
                            'data_dynamic'=>(!empty($dataDynamic)) ? $dataDynamic : false,
                            'data_create'=>(!empty($dataCreate)) ? $dataCreate : false,
                        ];
            return $out; 
        } catch (Exception $ex) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
            return FALSE;
        }
    }
    /**
     * 
     * @param type $dataid 
     * @params $db  if $db=='' db  else db_main
     * @return obj zdata_create_project  
     */
    public static function getDeleteMyProjectById($dataid, $db=''){         
        $db = (empty($db) || $db == '') ? \Yii::$app->db : \Yii::$app->db_main;
        try{
            $dataCreate=(new \yii\db\Query())->select('*')
                    ->from(self::zdata_create_project())
                    ->where('id=:id AND rstat = 3',[':id'=>$dataid])
                    ->one($db);
            $dataDynamic = \common\modules\user\classes\CNDynamicDb::Query($dataid);
                        $out=[
                'data_dynamic'=>(!empty($dataDynamic)) ? $dataDynamic : false,
                'data_create'=>(!empty($dataCreate)) ? $dataCreate : false,
            ];
            return $out; 
        } catch (Exception $ex) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
            return FALSE;
        }
    }
    
    /**
     * 
     * @param type $data zdata_create_project or dynamic_db
     * @param type $table if table != dynamic_db else zdata_create_project
     * @return boolean
     */
    public static function saveProject($data, $table='', $dbname=''){
       $table = ($table != '') ? self::dynamic_db() : self::zdata_create_project();
       $table = ($dbname != '') ? "`{$dbname}`.`{$table}`" : "`{$table}`"; 
       
       try{
           unset($data['useTemplate']);
           $create = \Yii::$app->db_main->createCommand()
                ->insert($table, $data)
                ->execute();
           return $create;
       } catch (Exception $ex) {
           
           \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
           return FALSE;
       }
    }

    /**
     *  db_main
     * @param type $data get in table dynamic_db or zdata_create_project delete id
     * @param type $id
     * @param type $table if table != '' dynamic_db else zdata_create_project
     * @return boolean
     */
    public static function updateProject($data, $id, $table='', $dbname=''){
       $table = ($table != '') ? self::dynamic_db() : self::zdata_create_project();
       $table = ($dbname != '') ? "`{$dbname}`.`{$table}`" : "`{$table}`";       
       try{
           if($dbname != ''){
                $my_project = (new \yii\db\Query())
                        ->select('*')
                        ->from($table)
                        ->where('id=:id',[':id'=>$id])->one();                
                if(empty($my_project)){
                    self::saveProject($data, '', $dbname);
                }
            }
             
           $update = \Yii::$app->db_main->createCommand()
                ->update($table, $data, ['id'=>$id])
                ->execute();
           if($update){
//               \appxq\sdii\utils\VarDumper::dump($update);
               return TRUE;
           }else{
               return FALSE;
           }
       } catch (Exception $ex) {           
           \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
           return FALSE;
       }
    }
    
    /**
     * 
     * @param type $url check url dynamic_db
     * @return boolean 
     */
    public static function checkRequireUrl($id='', $url, $action=''){
        $action = ($action == '') ? 'create' : $action;
        
        try { 
            $dataDynamic =  (new \yii\db\Query())->select('*')->from(self::dynamic_db())
                    ->where('url=:url',[':url'=>$url]);
                    
            if($action == 'update'){
                $dataDynamic->andWhere('data_id <> :id', [':id'=>$id]);
            } 
            $data = $dataDynamic->one(\Yii::$app->db_main);     
//            return $action;
            if($data){
                return true;
            }
            
            return false;
        } catch (Exception $ex) {            
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
            return FALSE;
        }
    }
    
    /**
     * 
     * @param type $acronym check acronym dynamic_db
     * @return boolean 
     */
    public static function checkAcronym($id, $acronym, $action=''){
        $action = ($action == '') ? 'create' : $action;
        try { 
            $dataDynamic =  (new \yii\db\Query())->select('*')->from(self::dynamic_db())
                    ->where('aconym=:aconym',[':aconym'=>$acronym]);
                    
            if($action == 'update'){
                $dataDynamic->andWhere('id <> :id', [':id'=>$id]);
            } 
            $data =  $dataDynamic->one(\Yii::$app->db_main);       
            if($data){
                return true;
            }
            return false;
        } catch (Exception $ex) {            
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
            return FALSE;
        }
    }
    
    
    public static function desTroyProjectById($id){          
       try{
            $myproject= \cpn\chanpan\classes\utils\CNProject::getDeleteMyProjectById($id);           
            if(!empty($myproject)){
                $dbname = $myproject['data_dynamic']['dbname'];
                $database = \backend\modules\manageproject\classes\CNDatabaseFunc2::getDatabase();
                if(!in_array($dbname, $database) || $dbname == 'ncrc'){
                    //ไม่พบ database
                    self::desTroyDataCreate($id);
                    self::desTroyDynamic($id);
                    return true;
                }  
                if($dbname != 'ncrc'){
                    $sql="DROP DATABASE `{$dbname}`;";
                    $destroy = \Yii::$app->db->createCommand($sql)->execute();
                    if($destroy){
                        self::desTroyDataCreate($id);
                        self::desTroyDynamic($id);
                        return true;
                    }
                }
                return false;
            }
       } catch (Exception $ex) {            
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
            return FALSE;
       }
    }
    public static function desTroyDataCreate($id){
       try{
           $destroy = \Yii::$app->db_main->createCommand()
                   ->delete(self::zdata_create_project(), 'id=:id', ['id'=>$id])                   
                   ->execute(); 
           return $destroy;
       } catch (Exception $ex) {            
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
            return FALSE;
       }
    }
    public static function desTroyDynamic($id){
        try{
            $myproject=\cpn\chanpan\classes\utils\CNProject::getMyProjectById($id);
            if(!empty($myproject)){
                $dataid = $myproject['data_dynamic']['data_id'];
                $destroy = \Yii::$app->db_main->createCommand()
                   ->delete(self::dynamic_db(), 'data_id=:id', ['id'=>$dataid])                   
                   ->execute(); 
                return $destroy;
            }
       } catch (Exception $ex) {            
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
            return FALSE;
       }
    }
    
    /**
     * 
     * @param type $user_create user login
     * @return integer max forder
     */
    public static function getMaxfOrder($user_create){
        try{
            $myproject=(new \yii\db\Query())
                    ->select('*')->from(self::zdata_create_project())
                    ->where('user_create=:user_create',[':user_create'=>$user_create])
                    ->max('forder');
            return $myproject;
             
       } catch (Exception $ex) {            
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
            return FALSE;
       }
    }
    /*gen forder 1-n*/
    public static function generateForder($user_create){
        try{
            $myproject=(new \yii\db\Query())
                    ->select('*')->from(self::zdata_create_project())
                    ->where('user_create=:user_create AND rstat not in(0,3)',[':user_create'=>$user_create])
                    ->all(); 
            if(!empty($myproject)){
                foreach($myproject as $key=>$value){
                    \Yii::$app->db->createCommand()
                            ->update(self::zdata_create_project(), [
                                'forder'=>$key+1
                            ], 'id=:id', 
                            [
                                ':id'=>$value['id']
                           ])->execute();
                }
            }
             
       } catch (Exception $ex) {            
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
            return FALSE;
       }
    }
    
    public static function autoUpdateProject($dbname, $id){
        //return $id;
        try{
            $table = self::zdata_create_project();
            if(CNDomain::isPortal()){
                 $sql="
                    REPLACE INTO `{$dbname}`.`{$table}` (select * from ncrc.`{$table}` where id = {$id});  
                  ";
            }else{
                 $sql="
                    REPLACE INTO `ncrc`.`{$table}` (select * from `{$dbname}`.`{$table}` where id = {$id});  
                  ";
            }
            $update = Yii::$app->db_main->createCommand($sql)->execute();
            if($update){
                return 'success';
            }
        } catch (Exception $ex) {
            \appxq\sdii\utils\VarDumper::dump($ex->getMessage());
        }
    }
    public static function setConfig(){
        //ini_set('max_execution_time', 300);
        set_time_limit(0);
        try{
            $db_portal = Yii::$app->db_portal;
            $max_project = 1000;
            $offset = 0;
            $dbname = [];
            $url_arr = [];
            for($i=$max_project; $i>=1; $i-=100){
                $data = (new \yii\db\Query())->select('*')
                    ->from('dynamic_db')
                    ->where("dbname <> 'ncrc' AND url not in('backend.ncrc.in.th', 'www.ncrc.in.th' ,'portal.ncrc.in.th','backend.ncrc.locals') AND rstat not in(0,3)")
                    ->offset($offset)
                    ->limit(100)
                    ->groupBy(['dbname'])
                    ->all($db_portal);
                foreach($data as $k=>$v){
                    array_push($dbname, $v['dbname']);
                    $url_arr[$k+1] = ['dbname'=>$v['dbname'],'url'=>$v['url']];
                }
                $offset += 100;
            }
           // \appxq\sdii\utils\VarDumper::dump($url_arr);
            foreach($dbname as $k=>$v){
                 try{
                     $sql="
                        REPLACE INTO {$v}.`core_options` VALUES (552, 'project_setup_islocal', '2', 'yes', 'Local', '', 'RadioList', '', '[1=>\'Yes\', 2=>\'No\']', 0, '', '', 5);
                        REPLACE INTO {$v}.`core_options` VALUES (553, 'project_setup_isupdate', '2', 'yes', 'Update Command', '', 'RadioList', '', '[1=>\'Yes\', 2=>\'No\']', 0, '', '', 6);
                        REPLACE INTO {$v}.`core_options` VALUES (554, 'project_setup_isportal', '2', 'yes', 'Portal', '', 'RadioList', '', '[1=>\'Yes\', 2=>\'No\']', 0, '', '', 4);
                        REPLACE INTO {$v}.`core_options` VALUES (555, 'project_setup_isheader', '1', 'yes', 'แสดง Header สีฟ้า', '', 'RadioList', '', '[1=>\'Yes\', 2=>\'No\']', 0, '', '', 7);
                        REPLACE INTO {$v}.`core_options` VALUES (556, 'project_setup_register_url', 'https://portal.ncrc.in.th/user/register', 'yes', 'url  ลงทะเบียน', '', 'TextInput', '', '', 0, '', '', 8);
                        REPLACE INTO {$v}.`core_options` VALUES (557, 'project_setup_portal_url', 'portal.ncrc.in.th', 'yes', 'Url Portal', '', 'TextInput', '', '', 0, '', '', 1);
                        REPLACE INTO {$v}.`core_options` VALUES (558, 'project_setup_frontend_url', 'www.ncrc.in.th', 'yes', 'Url Frontend', '', 'TextInput', '', '', 0, '', '', 2);
                        REPLACE INTO {$v}.`core_options` VALUES (559, 'project_setup_redirect_url', '/ezmodules/ezmodule/view?id=1521647584047559700&tab=1528945511006792400&addon=0', 'yes', 'Url สำหรับโปรเจค', '', 'TextInput', '', '', 0, '', '', 3);
                        REPLACE INTO {$v}.`core_options` VALUES (560, 'project_setup_portal_name', 'nCRC', 'yes', 'Portal Name', '', '', '', '', 0, '', '', 0);
                        REPLACE INTO {$v}.`core_options` VALUES (561, 'project_setup_ishttps', '1', 'yes', 'Https or Http', '', 'RadioList', '', '', 0, '', '', 0);
                        REPLACE INTO {$v}.`core_options` VALUES (562, 'project_setup_show_social_url', '2', 'yes', 'Show Social url', '', 'RadioList', '', '[1=>\'Yes\', 2=>\'No\']', 0, '', '', 5); 
                        REPLACE INTO {$v}.`core_options` VALUES (562, 'project_setup_site_navigator', '2', 'yes', 'Site Navigator', '', 'TextInput', '', '[1=>\'Yes\', 2=>\'No\']', 0, '', '', 5);
                        REPLACE INTO {$v}.`core_options` VALUES (563, 'project_setup_site_navigator_label', '<i class=\"fa fa-globe\"></i> Site Navigator', 'yes', 'Site Navigator', '', 'TextInput', '', '[1=>\'Yes\', 2=>\'No\']', 0, '', '', 5);     
                        REPLACE INTO {$v}.`core_options` VALUES (562, 'project_setup_site_navigator', '2', 'yes', 'Site Navigator', '', 'HTMLEditor', '', '[1=>\'Yes\', 2=>\'No\']', 0, '', '', 5);
                        REPLACE INTO {$v}.`core_options` VALUES (565, 'project_setup_footer', 'Developed by DAMASAC at Khon Kaen University E-Mail: ncrcthailand@gmail.com Copyright@DAMASAC 2018. All Rights Reserved.', 'yes', 'Footer', '', 'HTMLEditor', '', '', 0, '', '', 0);
                    ";
                    \Yii::$app->db_portal->createCommand($sql)->execute();
                 } catch (Exception $ex) {
                    \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
                 }
            }
        } catch (Exception $ex) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
        }
    }

    public static function CommandUpdate(){
            $status = []; 
            $is_update = \backend\modules\core\classes\CoreQuery::getOptions('project_setup_isupdate');
            
            if(isset($is_update['option_value']) && $is_update['option_value'] == '1'){
                //\appxq\sdii\utils\VarDumper::dump($is_update['option_value']);
                return;
            }else{
                $maxId = \backend\modules\manageproject\classes\CNUpdateCommandFunc::getMaxIdLogUpdate();
                $dataLogUpdate = \backend\modules\manageproject\classes\CNUpdateCommandFunc::getDataUpdateProject($maxId);
                //\appxq\sdii\utils\VarDumper::dump($dataLogUpdate);

                if (!empty($dataLogUpdate)) {
                    foreach ($dataLogUpdate as $d) {
                        $exect = \backend\modules\manageproject\classes\CNUpdateCommandFunc::updateCommand($d['sql_command'], $d['id']);
                    }
                }
            }
       
    }
    
    public static function getUserProject($userId){
        $userProject = (new \yii\db\Query())->select('*')->from('user_project')->where(['user_id'=>$userId])->all();
        if($userProject)
        {
            return $userProject;
        }
    }
    public static function getDisconByProjectId($projectId,$userId){
        $data = \backend\modules\manageproject\models\Discontinuatios::find()->where(['project_id'=>$projectId,'user_id'=>$userId])->one();
        if($data)
        {
            return $data;
        }
    }
    public static function getProject(){
       return \cpn\chanpan\classes\CNServerConfig::getServerModelDynamicDb();
    }
   public static function getProjectName(){ 
       $myproject = \cpn\chanpan\classes\utils\CNProject::getMyProject();
       if($myproject){
           return $myproject['data_create']['projectname'];
       }
    }
    public static function getProjectAcronym(){
        $myproject = \cpn\chanpan\classes\utils\CNProject::getMyProject();
       if($myproject){
           return $myproject['data_create']['projectacronym'];
       }
    }

    /**
     * Check current project is portal
     * @return bool
     */
    public static function isPortal(){
        return \cpn\chanpan\classes\CNServerConfig::isPortal();
    }
    
   
    public static $globalTree = [];
    public static function getSectionTree($sectionId){
        ini_set('memory_limit', '-1'); 
        //$section = \common\models\Sections::find()->where('parent_id=0 AND id <> 0 AND rstat <> 3')->asArray()->all();
          
//        foreach ($section as $section) {
//            self::buildTree($section['id'], 0);
//        }
        //\appxq\sdii\utils\VarDumper::dump($section);
        self::buildSectionTree($sectionId, 1);
        return self::$globalTree;

    }
    public static function buildSectionTree($section, $level)
    {
        $rootNode = \backend\modules\manageproject\models\Sitemaps::find()->where('id=:id', [':id'=>$section])->asArray()->one();
        //\appxq\sdii\utils\VarDumper::dump($rootNode);
        
        $childNodes = \backend\modules\manageproject\models\Sitemaps::find()->where('parent_id = :parent_id AND id <> :id AND rstat not in(0,3) ',[':parent_id'=>$rootNode['id'], ':id'=>$rootNode['id']])->asArray()->all();
        if(count($childNodes) < 1) {
            return 0;
        } else {
            $childLvl = $level + 1; 
            //self::$globalTree['parent'] = $rootNode;
            foreach ($childNodes as $childNode) {
                $id = $childNode['id'];
                $childLevel = isset(self::$globalTree[$id])? max(self::$globalTree[$id]['level'], $level): $level;
                $depth = ['level' => $childLevel];
                self::$globalTree[] = array_merge($childNode, $depth);
                self::buildSectionTree($id, $childLvl);
            }
            
        }
    }
    
    public static function saveProjectConfig(){
        $my_project = isset(Yii::$app->params['my_project'])?Yii::$app->params['my_project']:'';
        
        if(isset($my_project) && isset($my_project['data_create'])){
            try {
                if (!isset(\Yii::$app->session['set_config_project']) && \Yii::$app->session['set_config_project'] != '1') {
                    $data_id = isset($my_project['data_create']['id']) ? $my_project['data_create']['id'] : '';
                    $data_project = (new \yii\db\Query())->select('*')->from('zdata_create_project')->where('id=:id', [':id' => $data_id])->one();

                    if (!$data_project) {
                        $data_create_project = \Yii::$app->db->createCommand()->insert('zdata_create_project', $my_project['data_create'])->execute();
                    }

                    $data_id = isset($my_project['data_dynamic']['id']) ? $my_project['data_dynamic']['id'] : '';
                    $data_dynamic = (new \yii\db\Query())->select('*')->from('dynamic_db')->where('id=:id', [':id' => $data_id])->one();
                    if (!$data_dynamic) {
                        \Yii::$app->db->createCommand()->insert('dynamic_db', $my_project['data_dynamic'])->execute();
                    }
                    \Yii::$app->session['set_config_project'] = '1';
                }
            } catch (Exception $ex) {
                \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
                return false;
            }
        }
    }//save project config
    
    /**
     * 
     * @return boolean count project all
     */
    public static function getCountProject(){
         try {
            $sql = "SELECT count(*) as  count_project FROM dynamic_db WHERE rstat not in(0,3) AND id not in(1,4)";
            $count = \Yii::$app->db->createCommand($sql)->queryOne();
            return $count['count_project'];
        } catch (Exception $ex) {
            EzfFunc::addErrorLog($x);
            return false;
        }
    }
    public static function getCountProjectDelete(){
         try {
            $sql = "SELECT count(*) as  count_project FROM dynamic_db WHERE rstat in(0,3) AND id not in(1,4)";
            $count = \Yii::$app->db->createCommand($sql)->queryOne();
            return $count['count_project'];
        } catch (Exception $ex) {
            EzfFunc::addErrorLog($x);
            return false;
        }
    }
    
    public static function get_project_all(){
      try{
            $user_id = \cpn\chanpan\classes\CNUser::getUserId();
            $data_create = (new \yii\db\Query())
                            ->select('*')->from('zdata_create_project')
                            ->innerJoin('dynamic_db', 'zdata_create_project.id = dynamic_db.data_id')
                            ->where('zdata_create_project.user_create=:user_create AND zdata_create_project.rstat not in(0,3)', [
                                ':user_create' => $user_id
                            ])->groupBy(['dynamic_db.url'])->all(Yii::$app->db_main);

            $user_project = (new \yii\db\Query())
                            ->select('*')
                            ->from('user_project')
                            ->innerJoin('dynamic_db', 'user_project.url = dynamic_db.url')
                            ->where('user_project.user_id=:user_id', [':user_id' => $user_id])->groupBy(['user_project.url'])->all(Yii::$app->db_main);
            $data_project = [];
            $email = \cpn\chanpan\classes\CNUser::getEmail();
            if($email == 'chanpan.nuttaphon1993@gmail.com'){                                                                                                               
                //\appxq\sdii\utils\VarDumper::dump($user_project);                                                                                                          
            }
                    
            foreach ($data_create as $k => $v) {
                $data_project[\appxq\sdii\utils\SDUtility::getMillisecTime()] = 
                        [
                            'id' =>             isset( $v['id'])? $v['id']:'', 
                            'projectname' =>    isset($v['projectname'])?$v['projectname']:'', 
                            'projectacronym' => isset($v['projectacronym'])?$v['projectacronym']:'', 
                            'url' =>            isset($v['url'])?$v['url']:'', 
                            'user_update' =>    isset($v['user_update'])?$v['user_update']:''];
            }

            foreach ($user_project as $k => $v) {
                $data_project[\appxq\sdii\utils\SDUtility::getMillisecTime()] = [
                            'id' =>             isset($v['id'])?$v['id']:'',
                            'projectname' =>    isset($v['projectname']) ? $v['projectname'] : 'Portal',
                            'projectacronym' => isset($v['projectacronym']) ? $v['projectacronym'] : 'Portal',
                            'url' =>            isset($v['url'])?$v['url']:'',
                            'user_update' =>    isset($v['user_update'])?$v['user_update']:''
                ];
            }
            return $data_project;
        } catch (Exception $ex) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
            return false;

      }
      //crf เป็นของคนที่ create
    }
    
    public static function getProjectAll(){
        $status = isset($_GET['status']) ? $_GET['status'] : '1';
        $where = ['user_create'=> \cpn\chanpan\classes\CNUser::getUserId()];
        
        $data = \backend\modules\manageproject\classes\CNEzform::getDynamicTableAll("zdata_create_project",$where);
        
        $output = \backend\modules\manageproject\classes\CNEzform::getUserProject();
        foreach($data as $k1=>$v1){
            foreach($output as $k2=>$v2){
                if($v1['id'] == $v2['id']){
                    unset($output[$k2]);
                }
            }
        }
        
        if($status == '1'){
           $data = \yii\helpers\ArrayHelper::merge($data, $output);
        }else{
            if(empty($data)){
                return \cpn\chanpan\classes\CNResponse::notFoundAlert();
             }
        } 
        $user_id = \cpn\chanpan\classes\CNUser::getUserId();
        foreach($data as $key=>$value){
            if ($data[$key]['user_create']==$user_id){
                $data[$key]['mode']='all';
            } else {
                $data[$key]['mode']='assign';
            }
                
        }
        return $data;
    }
}
