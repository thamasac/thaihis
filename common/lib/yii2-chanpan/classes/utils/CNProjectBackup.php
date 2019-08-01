<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace cpn\chanpan\classes\utils;

use yii\db\Exception;

class CNProjectBackup {

    /**
     * 
     * @param type $dbname string database name 
     * @param type $newFile file name  'test.sql'
     * @return boolean true or false
     */
    public static function backUpDataBase($dbname, $newFile) {
        try {
            set_time_limit(120);
            $getDb = \Yii::$app->db_backup;
            $user = $getDb->username;
            $pass = $getDb->password;
            $path = CNDomain::getWebroot() . "/backup_files/{$newFile}";
            //echo $path;return;
            $sql = "mysqldump -h127.0.0.1 -u{$user} -p{$pass} {$dbname} > {$path}";
            exec($sql, $out, $retval);
            if ($retval == '0') {
                return true;
            } else {
                return false;
            }
            return;
        } catch (Exception $ex) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
            return false;
        }
    }
    
    public static function createTxt($data, $pathName){        
        try {
            $sql="echo '".$data."' >> {$pathName}";
            exec($sql, $out, $retval);
            if ($retval == '0') {
                return true;
            } else {
                return false;
            }
            return;
        } catch (Exception $ex) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
            return false;
        }
    }

    /**
     * 
     * @param type $oldFile 'backend/web/xxx/tet.sql'  $fileName '/backend/web/xxx.zip'
     * @return boolean
     */
    public static function zipFile($oldFile, $fileName, $fileName2, $fileName3, $path='') {
        try {
            $sql = "zip -5 -Pncrcdamasac  {$fileName} {$oldFile} {$fileName2} {$fileName3}";
            if(isset($path)){
                $sql = "cd {$path}; zip -5 -Pncrcdamasac  {$fileName} {$oldFile} {$fileName2} {$fileName3}";
            }
            
            
            exec($sql, $out, $retval);
            if ($retval == '0') {                 
                return true;
            } else {
                return false;
            }
            return;
        } catch (Exception $ex) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
            return false;
        }
    }
       /**
     * 
     * @param type $oldFile 'backend/web/xxx/tet.sql'  $fileName '/backend/web/xxx.zip'
     * @return boolean
     */
    public static function unZipFile($path, $fileName) {
        try {
            $zip = new \ZipArchive();
            $zip_status = $zip->open("{$path}/{$fileName}");
            $pwd = 'ncrcdamasac';
            $folder = \appxq\sdii\utils\SDUtility::getMillisecTime();
            if($zip_status === true) { 
                if ($zip->setPassword($pwd)) {
                    if (!$zip->extractTo("{$path}/{$folder}")){
                        return FALSE;
                    }
                    return ['folder'=>$folder]; 
                }
            }
            
//            set_time_limit(120);
//            $result = array();             
//            exec("unzip -Pncrcdamasac '{$fileName}'", $result, $returnval);
//            print_r($result);
//            print_r($returnval);
//            return;
//            if ($retval == '0') {
//                return true;
//            } else {
//                return false;
//            }
//            return;
        } catch (Exception $ex) {            
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
            return false; 
        }
    }
    
    
    /**
     * clear tables
     * @return boolean
     */
    public static function clearTables(){
        try {
            $tables = ['ezform_log', 'system_error'];
            foreach($tables as $t){
                $sql="TRUNCATE {$t}";
                \Yii::$app->db->createCommand($sql)->execute();
            }            
            return;
        } catch (Exception $ex) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
            return false;
        }
        
    }

    /**
     * 
     * @param type $filepath 'backend/web/xxx/tet.sql'
     * @return boolean
     */
    public static function renameFile($filesName, $to) {
        try {
            $sql = "mv {$filesName} {$to}";
            exec($sql, $out, $retval);
            if ($retval == '0') {                 
                return true;
            } else {
                return false;
            }
            return;
        } catch (Exception $ex) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
            return false;
        }
    }
     /**
     * 
     * @param type $filepath 'backend/web/xxx/tet.sql'
     * @return boolean
     */
    public static function removeFile($filesName) {
         try {
            $sql = "rm -rf {$filesName}";
            exec($sql, $out, $retval);
            if ($retval == '0') {                
                return true;
            } else {
                return false;
            }
            return;
        } catch (Exception $ex) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
            return false;
        }
    }
 
    public static function project_backup(){
        return 'project_backup';
    }
    /* save project backup*/
    public static function saveProjectBackup($columns) {
         try {
             $save = \Yii::$app->db->createCommand()->insert(self::project_backup(), $columns)->execute();
             if($save){
                 return true;
             }
            return false;
        } catch (Exception $ex) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
            return false;
        }
   }
   
   public static function readTextFile($strFileName){
        ini_set('memory_limit', '-1');
        $data = '';
        $myfile = fopen($strFileName, "r") or die("Unable to open file!");
        $data = fread($myfile,filesize($strFileName));
        fclose($myfile);
        return $data;
   }
}
