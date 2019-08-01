<?php

namespace backend\modules\manageproject\classes;
use Yii;
use yii\db\Exception;

class CNCloneDb {
    public static function renameDatabasenCRC($name){
        $defaultDb = isset(\Yii::$app->params['default_db']) ? \Yii::$app->params['default_db'] : 'ncrc_';
        return "{$defaultDb}{$name}";
    }

    /**
     * 
     * @param type  string $dbName
     * @return type boolean true or false
     */
    public static function createDatabaseByName($dbName){
        try{
            
            $sql="CREATE DATABASE IF NOT EXISTS `{$dbName}` DEFAULT CHARACTER SET utf8   DEFAULT COLLATE utf8_unicode_ci";
            $dataQuery = \Yii::$app->db_main->createCommand($sql)->execute();
            if($dataQuery){
                return true;
            }else{
                return false;
            }
        } catch (Exception $ex) {
            //return $ex;
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
            return false;
        }
    }
    public static function getTableAll($dbname){
        $sql="SHOW TABLES IN `{$dbname}`";        
        $dataTable = \Yii::$app->db_main->createCommand($sql)->queryAll(); 
        return $dataTable;
    }
    
    /**
     * 
     * @param type string $dbnameClone ฐานข้อมูลที่จะโคลน
     * @return type boolean true or false
     */
    public static function createTableAll($dbnameClone, $templateDB, $projectaconym="", $type=""){
        try{
            //\appxq\sdii\utils\VarDumper::dump($dbnameClone);
            
            $projectaconym = isset($projectaconym) ? $projectaconym : '';
            $templateDB = isset($templateDB) ? $templateDB : 'ncrc';
            $dbname = $templateDB;// CNDynamicDbConnector::getDbName();
            $dbFullName = "Tables_in_".$dbname;             
         
            
            $table = CNCloneDb::getTableAll($dbname);        
            foreach($table as $key=>$tb){                
                $sql="CREATE TABLE IF NOT EXISTS {$dbnameClone}.{$tb[$dbFullName]} LIKE {$dbname}.{$tb[$dbFullName]}";
                $execute = Yii::$app->db_main->createCommand($sql)->execute();                
                if($type == "clone"){
                    $sql2="REPLACE INTO {$dbnameClone}.{$tb[$dbFullName]} (SELECT * FROM {$dbname}.{$tb[$dbFullName]})";
                    $execute2 = Yii::$app->db_main->createCommand($sql2)->execute(); 
                }else{
                    $table_arr = [
                        'zdata_tctr_main','zdata_tctr_part_intervention','zdata_tctr_part_investigator','zdata_tctr_part_links',
                        'zdata_tctr_part_primary','zdata_tctr_part_secids','zdata_tctr_part_secondary','zdata_tctr_part_sectionb',
                        'zdata_tctr_part_sectionc','zdata_tctr_part_sectiond','ezform_target',
                         'ezform_log','log_api','ezform_change_log','zdata_sae_log','queue_log'
                      ];
                    if(!in_array($tb[$dbFullName], $table_arr)){
                        $sql2="REPLACE INTO {$dbnameClone}.{$tb[$dbFullName]} (SELECT * FROM {$dbname}.{$tb[$dbFullName]})";
                        $execute2 = Yii::$app->db_main->createCommand($sql2)->execute(); 
                    }
                }
                 
                
            }
            
            $sql="DELETE FROM {$dbnameClone}.ezform WHERE ezf_crf=1;";
            $execute = Yii::$app->db_main->createCommand($sql)->execute();
              
            return $execute2;
        } catch (\yii\db\Exception $ex) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
        }
    }
    /**
     * 
     * @param type string $dbClone database name ที่จะ ลบ
     * @return type boolean true or false
     */
    public static function deleteUserNotAdmin($dbClone){
        $id = Yii::$app->user->id;
        $checkUser = Yii::$app->db->createCommand("SELECT auth_key FROM user WHERE id=:id", [':id'=>$id])->queryOne();
        if(!empty($checkUser)){
            $dataUser = (new \yii\db\Query())
                    ->select('id')
                    ->from('user')
                    ->where(['username'=>'admin'])->one();
            
            $sql="DELETE FROM {$dbClone}.user WHERE username <> 'admin'";
            $delete = Yii::$app->db->createCommand($sql)->execute();
            
            $sql2="DELETE FROM {$dbClone}.profile WHERE user_id <> {$dataUser['id']}";
            $delete2 = Yii::$app->db->createCommand($sql2)->execute();
            
            
            $sql3="DELETE FROM {$dbClone}.auth_assignment WHERE user_id <> {$dataUser['id']}";
            $delete3 = Yii::$app->db->createCommand($sql3)->execute();
            
            
           
            return $delete2;
        }        
    }
    /**
     * 
     * @param type string $dbClone database name ที่จะ ลบ
     * @return type boolean true or false 
     */
    public static function deleteMatching($dbClone){        
        $id = Yii::$app->user->id;
        $checkUser = Yii::$app->db->createCommand("SELECT auth_key FROM user WHERE id=:id", [':id'=>$id])->queryOne();
        if(!empty($checkUser)){
             
            $sql="update {$dbClone}.zdata_matching  SET user_id=''";
            $delete = Yii::$app->db->createCommand($sql)->execute();
            return $delete;
        }     
    }
    
    /**
     * 
     * @param type string $dbClone database name ที่จะ ลบ
     * @return type boolean true or false 
     */
    public static function deleteSitecode($dbClone){        
        $sql = "DELETE FROM {$dbClone}.zdata_sitecode WHERE site_name <> 00";
        $delete = Yii::$app->db->createCommand($sql)->execute();
        return $delete;
    }
    
    /**
     * 
     * @param type string $ezfId ezform id
     * @param type string $dataId data_id
     * @return type array data zdata_....
     */
    public static function checkDataInEzfoem($ezfId, $where){
        $table = CNEzform::getEzfTableName($ezfId);
        $data = CNEzform::getDynamicTableAll($table,$where);
        
        return $data;
    }
    
    public static function checkDynamicDb($domain){
        try{
            return \cpn\chanpan\classes\CNServerConfig::getServerModelDynamicDb();
//            $sql="SELECT * FROM dynamic_db WHERE url=:url";
//            $params=[":url"=>$domain];
//            if($domain == "backend.ncrc.local"){
//                \cpn\chanpan\classes\CNServerConfig::getServerModelDynamicDb();
//                $checkData = Yii::$app->db->createCommand($sql,$params)->queryOne();
//            }else{
//                $checkData = Yii::$app->db_main->createCommand($sql,$params)->queryOne();
//            }
            
//            return $checkData;
        } catch (\yii\db\Exception $ex) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
        }
    }

    /**
     * 
     * @param type array $data [url=>"", "config_db"=>"","proj_name"=>"","dbname"=>"", "create_at"=>Date('Y-m-d H:i:s')];
     * @return type boolean true or false
     */
    public static function saveDynamicDb($data){
       try{
                \Yii::$app->db->createCommand()
                ->insert("dynamic_db", $data)
                ->execute();
           
//                \Yii::$app->db->createCommand()->insert("user_project", [
//                    'url'=>$data['url'], 
//                    'user_id'=>1, 
//                    'create_by'=>::getUserId(),
//                    'create_at'=>Date('Y-m-d'),
//                    'data_id'=>$data['data_id']
//                ])->execute();
             return TRUE;
       }catch(\yii\db\Exception $ex){
           \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
       }
        
    }

    public static function setDefaultSiteCode($dbClone)
    {
        $id = isset(Yii::$app->user->id) ? Yii::$app->user->id : '';
        try{
            $sql="UPDATE profile SET sitecode='00' FROM `{$dbClone}`.`profile`";
            return Yii::$app->db->createCommand($sql)->execute();
        }catch (Exception $ex){
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
            return false;
        }
    }




    }
