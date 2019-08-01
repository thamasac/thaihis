<?php

namespace cpn\chanpan\classes;

use Yii;
use yii\httpclient\Exception;

class CNServerConfig {

    /**
     * 
     * @return object dynamic db config
     */
    public static function getServerModelDynamicDb($id = "", $url_change = false) {
        try {            
            if (self::isPortal() || self::isLocal()) {
                $db = \Yii::$app->db;
            }else{
                $db = \Yii::$app->db_main;
                //$db = (self::checkInternetConnection() === true) ? \Yii::$app->db_main : \Yii::$app->db;
            }
            ;
            $data = (new \yii\db\Query())->select('*')->from('dynamic_db');
            if ($id == "") {
                if ($url_change == true) {
                    $data->where(['url_change' => self::getDomainName()]);
                } else {
                    $data->where(['url' => self::getDomainName()]);
                    
                }
            } else {
                $data->where(['data_id' => $id]);
            }
            //$data = (new \yii\db\Query())->select('*')->from('dynamic_db')->where(['url_change' => self::getDomainName()])->one();
            //if(\Yii::$app->user->id == '1545286790068226300'){ \appxq\sdii\utils\VarDumper::dump(Yii::$app->params['model_dynamic']); }
           // \appxq\sdii\utils\VarDumper::dump($data->one($db));
            
            return $data->one($db);

            return self::getDomainName();
        } catch (Exception $ex) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
            return FALSE;
        }
    }

    /**
     *
     * @return boolean true or false
     */
    public static function checkInternetConnection() {
        //$connected = @fsockopen("https://www.google.co.th", 80);//website, port  (try 80 or 443)
        $connected = @fsockopen("www.google.com", 443); //website, port  (try 80 or 443)
        if ($connected) {
            $is_conn = true; //action when connected
            fclose($connected);
        } else {
            $is_conn = false; //action in connection failure
        }
        //\appxq\sdii\utils\VarDumper::dump($is_conn);
        return $is_conn;
    }

    /**
     * 
     * @return type string server name example localhost or backend.ncrc.local
     */
    public static function getDomainName() {
//        return 'cn01.work.thaihis.org';
        return isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : '';
    }

    /**
     * 
     * @return type string http or https
     */
    public static function getProtocol() {
        return stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true ? 'https://' : 'http://';
    }

    /**
     * 
     * @return type string url 'https://backend.xxx.com' or 'http://backend.xx.com'
     */
    public static function getBackendUrl() {
        return self::getProtocol() . \backend\modules\core\classes\CoreFunc::getParams('main_url', 'url');
    }

    /**
     * 
     * @return type string url 'https://xxx.com' or 'http://xx.com'
     */
    public static function getFrontendUrl() {
        return self::getProtocol() . \backend\modules\core\classes\CoreFunc::getParams('frontend_url', 'url');
    }

    /**
     * Check current project is portal
     * @return bool
     */
    public static function isPortal() {
        $portal = \backend\modules\core\classes\CoreFunc::getParams('project_setup_isportal', 'portal');
        if($portal == '1'){
            return true;
        }else{
            return false;
        }
        
        
        $domain = self::getDomainName();
        $main_url = \backend\modules\core\classes\CoreFunc::getParams('main_url', 'url');
        if ($domain == "backend.ncrc.local" || $domain == $main_url) {
            return true;
        }
    }

    public static function isLocal() {
        $isLocal = isset(Yii::$app->params['islocal']) ? Yii::$app->params['islocal'] : '';
        
        if ($isLocal == '1') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * check server test
     * @return boolean
     */
    public static function isTest() {
        $domain = self::getDomainName();
        if ($domain == 'nhbackend.damasacdev.tk') {
            return true;
        }
    }

    /**
     * @param bool $status
     * @throws \yii\db\Exception
     */
    public static function getDynamicConnect($status = false) {
        try {
            //check is local
            $data = \backend\modules\core\classes\CoreQuery::getOptions('islocal');
            if(isset($data) && !empty($data) && $data['option_value'] == '1'){
                return [
                    'status' => 'warning',
                    'message' => 'Offline',
                    'data' => $data
                ];
            }
            
            
        } catch (\yii\db\Exception $ex) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
            return [
                'status' => 'error',
                'message' => $ex->getMessage(),
            ];
        }//check options_config islocal
        $session_url = isset(\Yii::$app->session['url']) ? \Yii::$app->session['url'] : ''; //get session url
        $session_dynamic_dsn = isset(\Yii::$app->session['dynamic_dsn']) ? \Yii::$app->session['dynamic_dsn'] : ''; //get session dynamic_dsn
        $current_dsn = isset(Yii::$app->db->dsn) ? Yii::$app->db->dsn : ''; //get current dsn 
        //unset session เดิมก่อน
        unset(\Yii::$app->session['url']);
        unset(\Yii::$app->session['dynamic_dsn']);
        
        if ($status === true) {
            $current_url = self::getDomainName();
            /**
             * 1 session_url != ''
             * 2 session_url == current url
             * 3 dsn != ''
             * 4 dsn == current dsn
             */
            if ($session_url != '' && ($session_url == $current_url) && $session_dynamic_dsn != '' && $session_dynamic_dsn == $current_dsn) {
                return [
                    'status'=>'warning',
                    'message' => '1 url != \'\' && url == current url && dsn != \'\' && dsn == current dsn',
                ];
            } else {
                $dsn = self::getDSN();
            }

            /* connection database */
            \Yii::$app->db->close();
            \Yii::$app->db->dsn = $dsn;
            \Yii::$app->session['url'] = $current_url;
            \Yii::$app->session['dynamic_dsn'] = $dsn;
            \Yii::$app->db->open();
            return [
                'status'=>'success',
                'message' => 'Get dynamic dsn success',
                
            ];
        }
    }

    public static function getDSN() {
        $cDsn = isset(Yii::$app->db->dsn) ? Yii::$app->db->dsn : '';
        if ($cDsn != '') {
            $db = explode('dbname=', $cDsn); //get dbname
            $db = end($db); //current dbname
            $myProject = self::getServerModelDynamicDb(''); //get myproject by url
            if ($myProject) {//found myProject by url
                $rstat = isset($data['rstat']) ? $data['rstat'] : '';
                if ($rstat == '3') {//Deleted Projects
                    return self::showMessageDeleteProject($myProject);
                }
                $host = isset($myProject['host']) && $myProject['host'] != '' ? $myProject['host'] : 'localhost';
                $dbname = isset($myProject['dbname']) ? $myProject['dbname'] : 'thaihis';
                $dsn = "mysql:host=$host;dbname=$dbname";
                return $dsn;
            } else {
                $myProject = self::getServerModelDynamicDb('', true); //get myproject by url_change
                if ($myProject) {//found myProject by change_url
                    return self::showMessageDeleteProject($myProject);
                } else {
                    return $cDsn; //not found myProject by url_change return current dsn
                }
            }
            return $myProject;
        }
    }

    public static function showMessageDeleteProject($data) {
        $frontendUrl = Yii::$app->params['frontendUrl'];
        $backendUrl = Yii::$app->params['backendUrl'];
        //\appxq\sdii\utils\VarDumper::dump($backendUrl);
        $html = "
                       <style>body{    background: #f2dede;}</style>
                        <div style='
                                    padding:20px;
                                    margin-top:10%;text-align:center;color: #31708f;
                                    color: #a94442;
                                    background-color: #f2dede;
                                    border-color: #ebccd1;'>
                            <h1>This project has been deleted. Please contact the project owner.</h1>
                            <a href='{$frontendUrl}'>
                                <i class='fa fa-home'></i> 
                                <span class='title'>nCRC Central Site</span>
                            </a> | 
                            <a href='{$backendUrl}'>
                                <i class='fa fa-home'></i> 
                                <span class='title'>All My Projects</span>
                            </a>
                        </div>
                   ";
        echo $html;
        exit();
    }

    public static function configParamsServerProject() {
        
        Yii::$app->params['current_url'] = \cpn\chanpan\classes\CNServerConfig::getDomainName();
        Yii::$app->params['dynamic_potocal'] = \cpn\chanpan\classes\CNServerConfig::getProtocol();
        
        
        
        if (isset(Yii::$app->params['model_dynamic'])) {
            if (Yii::$app->params['model_dynamic']['url'] != Yii::$app->params['current_url']) {
                Yii::$app->params['model_dynamic'] = \cpn\chanpan\classes\CNServerConfig::getServerModelDynamicDb();
            }
            
        } else {
            
            Yii::$app->params['model_dynamic'] = \cpn\chanpan\classes\CNServerConfig::getServerModelDynamicDb();
            //
            //\appxq\sdii\utils\VarDumper::dump(Yii::$app->params['model_dynamic']);
        }
        
        if (!isset(Yii::$app->params['main_url'])) { //Option config  name = main_url
            Yii::$app->params['main_url'] = Yii::$app->params['current_url'];
        }
        //\appxq\sdii\utils\VarDumper::dump(Yii::$app->params['main_url']); 
        Yii::$app->params['redirect2portal'] = Yii::$app->params['dynamic_potocal'] . Yii::$app->params['main_url'];
        Yii::$app->params['project_menu'] = \appxq\sdii\utils\SDUtility::string2Array(isset(Yii::$app->params['project_menu']) ? Yii::$app->params['project_menu'] : '');
        Yii::$app->params['frontend_url'] = isset(Yii::$app->params['frontend_url']) ? Yii::$app->params['frontend_url'] : '';
        Yii::$app->params['frontend_full_url'] = Yii::$app->params['dynamic_potocal'] . Yii::$app->params['frontend_url'];
       
        //end dynamic url config
        //config project
        //
        
        
        //\appxq\sdii\utils\VarDumper::dump(self::isLocal()); local
        Yii::$app->params['my_project'] = (!self::isLocal()) ? \cpn\chanpan\classes\utils\CNProject::getMyProjectById(Yii::$app->params['model_dynamic']['data_id'], 'db_main') : '';
       // \appxq\sdii\utils\VarDumper::dump(Yii::$app->params['model_dynamic']);
        
        Yii::$app->params['project_name'] = (!self::isLocal()) ? Yii::$app->params['my_project']['data_dynamic']['proj_name'] : '';
        Yii::$app->params['aconym'] = (!self::isLocal())?Yii::$app->params['my_project']['data_dynamic']['aconym']: '';
                 
        Yii::$app->params['site_name'] = (!self::isLocal())?\common\modules\user\classes\CNSitecode::getSiteValue():'';
        Yii::$app->params['role_name'] = (!self::isLocal())?\backend\modules\manageproject\classes\CNRole::getRoleNames():'';
        //end config project
        //Yii::$app->params['my_project']     = Yii::$app->params['model_dynamic'];//  
        //themes
        Yii::$app->params['themes'] = \backend\modules\manageproject\classes\CNSettingProjectFunc::getThemes();
        //end themes
        //\appxq\sdii\utils\VarDumper::dump(Yii::$app->params['model_dynamic']);
        
        if(!CNServerConfig::isLocal()){
           // utils\CNProject::saveProjectConfig();
        }
    }
     
}
