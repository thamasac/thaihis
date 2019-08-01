<?php

namespace cpn\chanpan\classes\utils;
use yii\db\Exception;
use Yii;
class CNDomain {
    /**
     * 
     * @return type url potal portal.ncrc.in.th
     */
    public static function getPortal(){
        return Yii::$app->params['main_url'];//\backend\modules\core\classes\CoreFunc::getParams('main_url', 'url');
    }
    /**
     * 
     * @return boolean
     */
    public static function isPortal(){
       try{
           $potalUrl    = self::getPortal();
           $projectUrl  = \Yii::$app->params['current_url']; //\cpn\chanpan\classes\CNServer::getDemain(); 
           return ($potalUrl == $projectUrl) ? TRUE : FALSE;
       } catch (Exception $ex) {
           \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
           return FALSE;
       }
    }
    
    /**
     * 
     * @return boolean
     */
    public static function isLocal(){
       try{
           $projectUrl  = \Yii::$app->params['current_url']; //\cpn\chanpan\classes\CNServer::getDemain(); 
           return ('backend.ncrc.local' == $projectUrl) ? TRUE : FALSE;
       } catch (Exception $ex) {
           \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
           return FALSE;
       }
    }
    
    /**
     * @return type url https://potal portal.ncrc.in.th
     */
    public static function getPortalFullUrl(){
        $portalUrl = self::getPortal();
        $http = 'http://';
        if (\cpn\chanpan\classes\utils\CNDomain::isHttps()) {
            $http = 'https://';
        }
        return $http.$portalUrl;
    }
    
    /**
     * 
     * @return type xxx.ncrc.in.th
     */
    public static function getCurrentProjectUrl(){
        $url= \Yii::$app->params['current_url']; //\cpn\chanpan\classes\CNServer::getDemain();
        //$url = 'poll_1530695825001594900.work.ncrc.in.th';
        return $url;
    }
    
    /* return frontend*/
    public static function isFrontend(){
       $f = \backend\modules\core\classes\CoreFunc::getParams('frontend_url', 'url');
       if($f != ''){
           $http = 'http://';
            if (\cpn\chanpan\classes\utils\CNDomain::isHttps()) {
                $http = 'https://';
            }
           return $http.$f;
       }
    }
    
    /* check https */
    public static function isHttps(){
       if( (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443 ){
           return true;
       }else{
           return FALSE;
       }
       
    }
    
    /* get storage url */
    public static function getStorage(){
       return \Yii::getAlias('@storageUrl');
    }
    /* get backend storage url */
    public static function getBackendStorage(){
       return \Yii::getAlias('@backendUrl');
    }
    /* get webroot url */
    public static function getWebroot(){
       return \Yii::getAlias('@webroot');
    }
     
}
