<?php
 

namespace backend\modules\manageproject\classes;
use Yii; 
class CNDynamicDbConnector {
    public static function getDbName(){
        $dbname="ncrc";
          
        $domain = isset(Yii::$app->params['current_url']) ? Yii::$app->params['current_url'] : \cpn\chanpan\classes\CNServerConfig::getDomainName();
        $main_url = \Yii::$app->params['main_url']; //\backend\modules\core\classes\CoreFunc::getParams('main_url', 'url');
 
        if($domain == $main_url){
            $dbname = "ncrc";
        }else if($domain == "backend.ncrc.local"){
            $dbname = "ncrc3";
        }else if($domain == "http://backend.nhis.test"){
            $dbname = "ncrc3";
        } 
        else{
            $config = explode(".", $domain);
            $dbname = CNCloneDb::renameDatabasenCRC($config[0]);
        } 
        return $dbname;
    }
    
    public static function getServerName(){        
        return $_SERVER['SERVER_NAME'];
    }

    public static function getDbConfig(){
        return \cpn\chanpan\classes\CNServerConfig::getDynamicDb();
        
        
//        $dbname = CNDynamicDbConnector::getDbName();
//        //$serName = CNDynamicDbConnector::getServerName();
//        
//        $serName = isset(Yii::$app->params['current_url']) ? Yii::$app->params['current_url'] : \cpn\chanpan\classes\CNServerConfig::getDomainName();
//        $main_url = \backend\modules\core\classes\CoreFunc::getParams('main_url', 'url');
//        
//        if($serName == "backend.ncrc.local" || $serName=="backend.nhis.test"){
//            $db=[
//                'class' => 'yii\db\Connection',
//                'dsn' => 'mysql:host=localhost;dbname=ncrc3',
//                'username' => 'root',
//                'password' => '',
//                'charset' => 'utf8',
//            ];     
//        }else if($serName == "udon.work.thaihis.org"){
//            $db=[
//                'class' => 'yii\db\Connection',
//                'dsn' => 'mysql:host=localhost;dbname=ncrc_udon',
//                'username' => 'ncrc',
//                'password' => 'ncrcdamasac!@#$%',
//                'charset' => 'utf8',
//            ]; 
//        }else{
//           $db=[
//                'class' => 'yii\db\Connection',
//                'dsn' => 'mysql:host=localhost;dbname='.$dbname,
//                'username' => 'ncrc',
//                'password' => 'ncrcdamasac!@#$%',
//                'charset' => 'utf8',
//            ];
//        }       
//        return $db;
    }
}
