<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\modules\manageproject\classes;
use Yii;
use yii\db\Exception;
/**
 * Description of ChanpanConfigDatabase
 *
 * @author Sammy Guergachi <sguergachi at gmail.com>
 */
class CNSDatabaseFunc {
    
    public static function GetDatabaseName(){
       try{
//            $domain = CNDynamicDbConnector::getServerName();
            $sql="SELECT * FROM dynamic_db WHERE dbname <> 'ncrc3' AND dbname <> 'ncrc'";
            $data = \Yii::$app->db_main->createCommand($sql)->queryAll();
            return $data;
       } catch (\yii\db\Exception $ex) {

       }         
        
    } 
    /**
     * 
     * @return type array query
     */
    public static function GetDatabaseAllName(){
       try{
            $sql="SHOW DATABASES;";
            return \Yii::$app->db_main->createCommand($sql)->queryAll();
       } catch (\yii\db\Exception $ex) {

       }         
         
    }
    public static function CheckDatabaseByName($dbname,$template, $user_id){
        $dbNcrc  = CNSDatabaseFunc::GetDatabaseAllName();        
        $arr=[];
        $dbs=[];
        $n=0;
        foreach($dbNcrc as $k=>$db){
            //array_push($arr, $db['Database']);
            $arr[$k]=$db['Database']; 
        }
        if(!in_array($dbname,$arr)){          
            $dbnameClone = $dbname;
            //return $dbnameClone;
            $createDb = CNSDatabaseFunc::createDatabaseByName($dbnameClone);
//            if ($createDb) {                
                $createTableAll = CNSDatabaseFunc::createTableAll($dbnameClone, $template, $user_id);
                if ($createTableAll) {
                   CNSDatabaseFunc::addUserByUserNcrc($dbnameClone, $user_id);
                   CNSDatabaseFunc::GetUserNcrcById($dbnameClone, $user_id);
                }
//            }
        }else{
            return 0;
        }
        // return $dbs; 
    }

        /**
     * 
     * @param type string $dbname
     * @return type string database 
     */
    public static function renameDatabasenCRC($name){
        //return "ncrc_{$name}";
        return CNCloneDb::renameDatabasenCRC($name);
    }
    
    
    
    
     /**
     * 
     * @param type  string $dbName
     * @return type boolean true or false
     */
    public static function createDatabaseByName($dbClone){
        try{
            $sql="CREATE DATABASE IF NOT EXISTS `{$dbClone}` DEFAULT CHARACTER SET utf8   DEFAULT COLLATE utf8_unicode_ci";
            $dataQuery = \Yii::$app->db_main->createCommand($sql)->execute();
            return $dataQuery;
        } catch (Exception $ex) {

        }
    }
    public static function getTableAll($dbname=""){
        $dbname = isset($dbname) ? $dbname : "ncrc";
        try{
            $sql="SHOW TABLES IN {$dbname}";
        $dataTable = \Yii::$app->db_main->createCommand($sql)->queryAll(); 
        return $dataTable;
        } catch (Exception $ex) {

        }
    }
    
    public static function createTableAll($dbnameClone, $dbName, $user_id){
        try{
            $dbFullName = "Tables_in_".$dbName;
            $table = CNSDatabaseFunc::getTableAll($dbName);  
            foreach($table as $key=>$tb){
                $sql="CREATE TABLE IF NOT EXISTS {$dbnameClone}.{$tb[$dbFullName]} LIKE {$dbName}.{$tb[$dbFullName]}";
                $execute = Yii::$app->db_main->createCommand($sql)->execute();

                $sql2="REPLACE INTO {$dbnameClone}.{$tb[$dbFullName]} (SELECT * FROM {$dbName}.{$tb[$dbFullName]})";
                $execute2 = Yii::$app->db_main->createCommand($sql2)->execute();
            }
             $dataOption=['option_value'=>3];
             $dataUpdate = \Yii::$app->db_main->createCommand()
                    ->update("{$dbnameClone}.core_options", $dataOption, "option_name=:option_name", [
                        ":option_name"=>"step"
                    ])
                    ->execute();            
            CNSDatabaseFunc::deleteUserNotAdmin($dbnameClone,$user_id);        
            return $execute2;
        } catch (\yii\db\Exception $ex) {
    }
    
        
    }
    public static function deleteUserNotAdmin($dbnameClone,$user_id){
        try{
            $sql = "DELETE FROM {$dbnameClone}.user WHERE username <> 'admin'";
            $delete = Yii::$app->db_main->createCommand($sql)->execute();
            $sql2 = "DELETE FROM {$dbnameClone}.profile WHERE user_id <> {$user_id}";
            $delete2 = Yii::$app->db_main->createCommand($sql2)->execute();
            $sql3 = "DELETE FROM {$dbnameClone}.auth_assignment WHERE user_id <> {$user_id}";
            $delete3 = Yii::$app->db_main->createCommand($sql3)->execute();
           
            CNSDatabaseFunc::deleteMatching($dbnameClone,$user_id);
        } catch (\yii\db\Exception $ex) {

        }
    }
    public static function deleteMatching($dbClone,$user_id){   
        try{
            $sql="update {$dbClone}.zdata_matching  SET user_id=''";
            $delete = Yii::$app->db_main->createCommand($sql)->execute();
            return $delete;   
        } catch (Exception $ex) {

        }
    }
    
    
    public static function addUserByUserNcrc($dbClone,$user_id){   
        try{
            $sql="update {$dbClone}.zdata_matching  SET user_id=''";//ลบ zdata_matching
            $delete = Yii::$app->db_main->createCommand($sql)->execute();
            return $delete;  
        } catch (Exception $ex) {

        } 
    }
    public static function GetUserNcrcById($dbClone,$id){
        //user ncrc
        $dataUser = \Yii::$app->db_main->createCommand("SELECT * FROM user where id=:id",[':id'=>$id])->queryOne();
        $dataProfile = \Yii::$app->db_main->createCommand("SELECT * FROM profile where user_id=:id",[':id'=>$id])->queryOne();
        
         $data=[
            'user'=>$dataUser,
            'profile'=>$dataProfile
         ];
         
         try{
             $dataUserAttribute = [
                'id' => $data["user"]["id"],
                'username'=>$data["user"]['username'],
                'email'=>isset($data["user"]['email']) ? $data["user"]['email'] : " ",
                'password_hash'=>$data["user"]['password_hash'],
                'auth_key'=>$data["user"]['auth_key'],
                'confirmed_at'=>time(),
                'created_at'=>time(),
                'updated_at'=>time(),
                'flags'=>0
            ];
            $dataProfileAttribuite=[
                        'user_id'=> $data['profile']['user_id'],
                        'public_email'=> isset($data['profile']['email']) ? $data['profile']['email'] : " ",
                        'tel'=>isset($data['profile']['telephone']) ? $data['profile']['telephone'] : " ",
                        'cid'=>isset($data['profile']['cid']) ? $data['profile']["cid"] : "",
                        'sitecode'=> isset($data['profile']['sitecode']) ? $data['profile']['sitecode'] : '00',
                        'firstname'=>isset($data['profile']['firstname']) ? $data['profile']['firstname'] : '',
                        'lastname' => isset($data['profile']['lastname']) ? $data['profile']['lastname'] : '',
                        'department' => isset($data['profile']['department']) ? $data['profile']['department'] : '00',
                        'certificate' => ' ',
                        'position'=>0
                     ];
            $saveUser = \Yii::$app->db_main->createCommand()->insert("{$dbClone}.user", $dataUserAttribute)->execute();
            if($saveUser){
                $saveProfile = \Yii::$app->db_main->createCommand()->insert("{$dbClone}.profile", $dataProfileAttribuite)->execute();
                $dataRole=['item_name'=>"administrator", 'user_id'=>$data["user"]["id"], 'created_at'=>time()];
                \Yii::$app->db_main->createCommand()->insert("{$dbClone}.auth_assignment", $dataRole)->execute();

                return $saveProfile;
            }
        } catch (\yii\db\Exception $ex) {

        }
         
    }
}
