<?php
 
namespace common\modules\user\classes;
use Yii; 
class CNDynamicDb {
    public static function getDbConnectDynamic($dbname=''){
        $dbname = ($dbname != '') ? $dbname : Yii::$app->params['obj_config']['dbname']; 
        $host = isset(Yii::$app->params['obj_config']['host']) ? Yii::$app->params['obj_config']['host'] : 'localhost';
        $connection = new \yii\db\Connection([ 
            'dsn' => "mysql:host={$host};dbname={$dbname}",
            'username' => isset(Yii::$app->params['obj_config']['user']) ? Yii::$app->params['obj_config']['user'] : 'root',
            'password' => isset(Yii::$app->params['obj_config']['pass']) ? Yii::$app->params['obj_config']['pass'] : '',
        ]); 
        $connection->open();
        return $connection;
    }

    //put your code here
    public static function Query($id='', $query=''){
        try{ 
            if($query != ''){
                return \cpn\chanpan\classes\CNServerConfig::getServerModelDynamicDb($id);
            }
            $data = ($id != '' && Yii::$app->params['model_dynamic']['data_id'] != $id) ? \cpn\chanpan\classes\CNServerConfig::getServerModelDynamicDb($id) : \Yii::$app->params['model_dynamic'];
            return $data;
        } catch (\yii\db\Exception $ex) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
            return FALSE;
        }
    }
    public static function getDataById($id){
       return CNDynamicDb::Query($id); 
    }  
    public static function getDataId(){        
        $data = CNDynamicDb::Query();
        return isset($data['data_id']) ? $data['data_id'] : '';
    }
    public static function getUrl(){        
        $data = CNDynamicDb::Query();
        return isset($data['url']) ? $data['url'] : '';
    }
    public static function getProjectName(){        
        $data = CNDynamicDb::Query();
        return isset($data['proj_name']) ? $data['proj_name'] : '';
    }
    public static function getProjectTemplate(){        
        $data = CNDynamicDb::Query();
        return isset($data['project_template']) ? $data['project_template'] : '';
    }
    public static function getProjectTctrId(){        
        $data = CNDynamicDb::Query();
        return isset($data['tctr_id']) ? $data['tctr_id'] : '';
    }
    public static function getPiName(){        
        $data = CNDynamicDb::Query();
        return isset($data['pi_name']) ? $data['pi_name'] : '';
    }
    public static function getAconym(){        
        $data = CNDynamicDb::Query();
        return isset($data['aconym']) ? $data['aconym'] : '';
    }
    
    public static function save($dataProject){
        try{
            $dbName = strtolower($dataProject['projurl']); //เอา url name มาสร้าง db
            $dbName = \backend\modules\manageproject\classes\CNCloneDb::renameDatabasenCRC($dbName) . '_' . time() * 1000;
            $useTemplate = 'ncrc';
            $columns = [
                'url' => "{$dataProject['projurl']}.{$dataProject['projdomain']}", 'data_id' => "{$dataProject['id']}",
                'config_db' => "{$dbName}", 'proj_name' => "{$dataProject['projectname']}",
                'dbname' => "{$dbName}", 'create_at' => date('Y-m-d H:i:s'),
                'project_template' => "{$useTemplate}", 'user_create' => isset(Yii::$app->user->id) ? Yii::$app->user->id : '',
                'tctr_id' => "{$dataProject['id_tctr']}", 'pi_name' => "{$dataProject['pi_name']}",
                'aconym' => "{$dataProject['projectacronym']}", 'rstat' => '1',
            ];
            if (\cpn\chanpan\classes\CNServerConfig::isPortal() || \cpn\chanpan\classes\CNServerConfig::isLocal()) {
              $data = \Yii::$app->db->createCommand()->insert('dynamic_db', $columns)->execute();
            }else{
              $data = \Yii::$app->db_main->createCommand()->insert('dynamic_db', $columns)->execute();  
            }
            
            return $data;
        } catch (\yii\db\Exception $ex) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
            return false;
        }
    }
    public static function saveByDefaultTable($columns){
        try{             
            $data = \Yii::$app->db_main->createCommand()->insert('dynamic_db', $columns)->execute();
        } catch (\yii\db\Exception $ex) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
            return false;
        }
    }
    
}
