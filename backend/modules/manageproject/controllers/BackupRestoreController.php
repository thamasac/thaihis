<?php
 
namespace backend\modules\manageproject\controllers;
use yii\web\UploadedFile;
use yii\db\Exception;
use Yii;
class BackupRestoreController extends \yii\web\Controller{
    public function actionBackup(){
        if(!\Yii::$app->request->isAjax){
            return 'request not found';
        }
        try{
            $id = \Yii::$app->request->post('id', '');
            $myproject= \cpn\chanpan\classes\utils\CNProject::getMyProjectById($id);
            
            if(!empty($myproject)){
                
                
               /* dump sql */ 
               $dbname          = $myproject['data_dynamic']['dbname'];
               $newFile         = $myproject['data_dynamic']['dbname'].'.sql';
               \cpn\chanpan\classes\utils\CNProjectBackup::clearTables();//clear tables log
               
               $dump            = \cpn\chanpan\classes\utils\CNProjectBackup::backUpDataBase($dbname, $newFile);               
               if($dump){ 
                   /* save project_backup*/
                   $columns = [
                       'id'             => \appxq\sdii\utils\SDUtility::getMillisecTime(),
                       'data_id'        => $myproject['data_create']['id'],
                       'dbname'         => $dbname,
                       'create_date'    =>date('Y-m-d H:i:s'),
                       'user_create'    => \cpn\chanpan\classes\CNUser::getUserId(),
                       'details'        => \appxq\sdii\utils\SDUtility::array2String($myproject),
                       'rstat'          => 1
                   ];
                   
                   \cpn\chanpan\classes\utils\CNProjectBackup::saveProjectBackup($columns);
                   
                    /* zip files sql*/
                    $path       = \cpn\chanpan\classes\utils\CNDomain::getWebroot() . "/backup_files";
                    $oldFile    ="{$path}/{$newFile}";
                    $fileName   ="{$path}/{$dbname}.zip";
                    $fileName2  ="{$path}/data.txt";
                    $fileName3  ="{$path}/dataid.txt";
                    $data       = \appxq\sdii\utils\SDUtility::array2String($columns);                   // \appxq\sdii\utils\VarDumper::dump($data);
                    $fileDataId       = \cpn\chanpan\classes\utils\CNProjectBackup::createTxt("{$myproject['data_create']['id']}", $fileName3);
                    $file       = \cpn\chanpan\classes\utils\CNProjectBackup::createTxt($data, $fileName2);
                    
                    $zip       = \cpn\chanpan\classes\utils\CNProjectBackup::zipFile($newFile, "{$dbname}.zip", 'data.txt','dataid.txt', $path);
                    exec("rm -rf {$fileName2} {$fileName3}");
                    if($zip){
                        /* rename */
                        $rename = \cpn\chanpan\classes\utils\CNProjectBackup::renameFile($fileName, "{$path}/{$columns['id']}_{$dbname}.img");
                        $delete = \cpn\chanpan\classes\utils\CNProjectBackup::removeFile($oldFile);//delete file .sql
                        $fileDownload = [                             
                            'file_name'=>"{$columns['id']}_{$dbname}.img",
                            'url'=> \cpn\chanpan\classes\utils\CNDomain::getPortalFullUrl(),
                            'path'=>'backup_files',        
                        ];
                        return \backend\modules\manageproject\classes\CNMessage::getSuccessObj("success", $fileDownload);
                    }                   
               }else{
                   return \backend\modules\manageproject\classes\CNMessage::getError("Backup not success!"); 
               }                
            }
            
        } catch (Exception $ex) {
            
        }
    }
    public function actionDownload(){        
        if(!\Yii::$app->request->isAjax){
            return 'request not found';
        }
        $params = isset($_GET['params']) ? $_GET['params'] : '';
        if (!empty($params)) {
            $file_name = $params['file_name'];
            $path = \cpn\chanpan\classes\utils\CNDomain::getWebroot() . "/backup_files";
            $fileName = "{$path}/{$file_name}.img";
            $remove = \cpn\chanpan\classes\utils\CNProjectBackup::removeFile($fileName);
            if($remove){
                return \backend\modules\manageproject\classes\CNMessage::getSuccess("Remove Success");
            }else{
                   return \backend\modules\manageproject\classes\CNMessage::getError("Remove not success!"); 
            }   
        }
        return;
        
    }
    public function actionRestore(){
        

        ini_set('memory_limit','2560M');
        ini_set('max_execution_time', 30000);
        
//        $sql = "mysql --host=localhost --user=root --password=ncrcdamasac!@#$% abcd < ";
//        exec($sql);

    
        try{
              
            $files = UploadedFile::getInstancesByName('file-restore'); 
            $path = \Yii::getAlias('@backend').'/web/backup_files'; 
            $randomStr     = \appxq\sdii\utils\SDUtility::getMillisecTime();  
            $newFileName       = "{$randomStr}.zip";
            $filePath   = "{$path}/{$newFileName}";
            //\appxq\sdii\utils\VarDumper::dump($files[0]->extension);
            foreach ($files as $file) {
                if($file->extension != 'img'){
                   
                    exec("rm -rf {$path}/{$newFileName}");
                    return \cpn\chanpan\classes\CNResponse::getError('Restore fail : Invalid file format.');
                }
                if ($file->saveAs("{$filePath}")) {
                    $zipFolder = \cpn\chanpan\classes\utils\CNProjectBackup::unZipFile("{$path}", $newFileName);
                    
                    
                    exec("rm -rf {$path}/{$newFileName}");
                    if(isset($zipFolder)){
                         //\appxq\sdii\utils\VarDumper::dump($files[0]->extension);
                        /*restore*/
                        $generatorStr = date('YmdHis');
//                        $folderName = '1539082763033388200'; //Folder unzip file
                        $folderName = $zipFolder['folder']; 
                        $dataProjectString = file_get_contents("{$path}/{$folderName}/data.txt");//\cpn\chanpan\classes\utils\CNProjectBackup::readTextFile("{$path}/{$folderName}/data.txt");
                        $dataArr = \appxq\sdii\utils\SDUtility::string2Array($dataProjectString);
                        
                        if(!isset($dataArr['details'])){
                            //if($dataCreate['user_create'] != $user_id){
                                exec("rm -rf {$path}/{$folderName}");
                                return \cpn\chanpan\classes\CNResponse::getError('Restore fail : Invalid file format.');
                           // }
                        }
                        
                        $dataProjectArr = \appxq\sdii\utils\SDUtility::string2Array($dataArr['details']);
                        /* Set table zdata_create_project and dynamic_db */
                        $dataDynamic = $dataProjectArr['data_dynamic']; //dynamic_db
                        $dataCreate = $dataProjectArr['data_create']; //zdata_create_project
                        $user_id = \cpn\chanpan\classes\CNUser::getUserId();
                        
//                        if($dataCreate['user_create'] != $user_id){
//                            exec("rm -rf {$path}/{$folderName}");
//                            return \cpn\chanpan\classes\CNResponse::getError('Restore fail : Username does not match');
//                        }
                        
                        $sqlFile = "{$dataArr['dbname']}.sql";
                        
                        //$sqlCommand = file_get_contents("{$path}/{$folderName}/{$sqlFile}"); //\cpn\chanpan\classes\utils\CNProjectBackup::readTextFile("{$path}/{$folderName}/{$sqlFile}");
                        //\appxq\sdii\utils\VarDumper::dump("{$path}/{$folderName}/{$sqlFile}");
                        
                        /* Change name url and user_update. */
                        $dataCreate['projectacronym'] = strtolower($dataCreate['projectacronym']).rand(99, 999);
                        $acronym = str_replace(" ", "", $dataCreate['projectacronym']);
                        $acronym = strtolower($acronym);
                        $dataCreate['user_create'] = \cpn\chanpan\classes\CNUser::getUserId(); 
                        $dataDynamic['user_create'] = \cpn\chanpan\classes\CNUser::getUserId(); 
                        
                        $dataCreate['id'] = \appxq\sdii\utils\SDUtility::getMillisecTime();
                        $forder = 0;
                        try{
                            $sql="SELECT * FROM `zdata_create_project` WHERE user_create=:user_create ORDER BY forder ASC";
                            $data = Yii::$app->db->createCommand($sql, [':user_create'=>$dataCreate['user_create']])->queryOne();
                            if($data){
                                $forder = (int)$data['forder'] - 1;
                            }
                        } catch (Exception $ex) {
                            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
                        }
                        
                        $dataCreate['forder'] = $forder;
                        $dataDynamic['url'] = "{$acronym}{$generatorStr}.{$dataCreate['projdomain']}";
                        $dataCreate['projurl'] = "{$acronym}{$generatorStr}";
                        $dataCreate['update_date'] = date('Y-m-d H:i:s');
                        $dataDynamic['data_id'] = $dataCreate['id'];
                        $dataDynamic['id'] = $dataCreate['id'];
                        
                        $acronym = str_replace(" ", "", $dataCreate['projectacronym']);
                        $dbName = "rs{$acronym}{$generatorStr}".rand(99, 999);
                        $dataDynamic['config_db'] = $dbName;
                        $dataDynamic['dbname'] = $dbName;
                        
                        //\appxq\sdii\utils\VarDumper::dump($dbName);
                        
                        $saveDataCreate = \cpn\chanpan\classes\utils\CNProject::saveProject($dataCreate, '');
                        //    background: #fff13b47;
                        
                        if ($saveDataCreate) {
                            $saveDataDynamic = \cpn\chanpan\classes\utils\CNProject::saveProject($dataDynamic, 'dynamic_db');
                            
                            if ($saveDataDynamic) {
                                $create_database = \backend\modules\manageproject\classes\CNCloneDb::createDatabaseByName($dbName);
                               // \appxq\sdii\utils\VarDumper::dump($create_database);
                                try {
                                    $exectCommand = \common\modules\user\classes\CNDynamicDb::getDbConnectDynamic($dbName);
                                    $path = "{$path}/{$folderName}/{$sqlFile}";
                                    //\appxq\sdii\utils\VarDumper::dump($path);
                                    
                                    if($this->importDatabase($path, $dbName)){
                                       unset(\Yii::$app->session['highlight']);
                                       \Yii::$app->session['highlight'] = [
                                           'data_id'=>$dataCreate['id'],
                                           'bg_color'=>'#fff13b47',
                                           'num'=>0
                                       ];
                                       
                                       try{
                                           $table='zdata_create_project';
                                           $sql = "REPLACE INTO {$dbName}{$table} (SELECT * FROM {$table} WHERE id='{$dataDynamic['id']}')";  
                                           return \Yii::$app->db_main->createCommand($sql)->execute();
                                       } catch (Exception $ex) {
                                           \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
                                       }
                                       return \cpn\chanpan\classes\CNResponse::getSuccess('Restore success');
                                    }else{
                                        return \cpn\chanpan\classes\CNResponse::getError("Restore fail");
                                    } 
                                } catch (\yii\db\Exception $error) {
                                    \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($error);
                                    return \cpn\chanpan\classes\CNResponse::getError('Restore fail');
                                }
                            }
                        }
                        return \cpn\chanpan\classes\CNResponse::getError('Restore fail');
                        
                    } else {
                        
                        exec("rm -rf {$path}/{$newFileName}");
                        return \cpn\chanpan\classes\CNResponse::getError('Restore fail : Invalid file format.');
                    }
                }
           }
        } catch (Exception $ex) {
            return \cpn\chanpan\classes\CNResponse::getError("Restore fail {$ex->getMessage()}"); 
        }
        return;
    }//restore
    public function importDatabase($path, $dbname){
        ini_set('memory_limit','25600M');
        //$url = Yii::getAlias('@storage');
        //$path = "{$url}/web/demo.sql";
        
        $path = \yii\helpers\FileHelper::normalizePath(Yii::getAlias($path));
        if (file_exists($path)) {
            if (is_dir($path)) {
                $files = \yii\helpers\FileHelper::findFiles($path, ['only' => ['*.sql']]);
                if (!$files) {
                    return false;//\backend\modules\manageproject\classes\CNMessage::getError('Path does not contain any SQL files');
                }
                $select = \yii\helpers\Console::select('Select SQL file', $files);
                if (\yii\helpers\Console::confirm('Confirm selected file [' . $files[$select] . ']')) {
                    $path = $files[$select];
                } else {
                    exit;
                }
            }
            //\appxq\sdii\utils\VarDumper::dump($path);
            $db = Yii::$app->getDb();
            
            //$dbname= 'abc';
            //$cmd = 'mysql --host=' . $this->getDsnAttribute('host', $db->dsn) . ' --user=' . $db->username . ' --password=' . $db->password . ' ' . $dbname . ' < ' . $path;
            
            
            
            exec('mysql --host=' . $this->getDsnAttribute('host', $db->dsn) . ' --user=' . $db->username . ' --password=' . $db->password . ' ' . $dbname . ' < ' . $path, $output);
            
            return true;//\backend\modules\manageproject\classes\CNMessage::getSuccess('success');
        } else {
            return false; //\backend\modules\manageproject\classes\CNMessage::getError('Path does not exist');
        }
    }//import
    private function getDsnAttribute($name, $dsn)
    {
        if (preg_match('/' . $name . '=([^;]*)/', $dsn, $match)) {
            return $match[1];
        } else {
            return null;
        }
    }//private getDsnAttribute
    
     public function actionUnzip(){
        try{
            $file = isset($_GET['files']) ? $_GET['files'] : '';
            $path = \cpn\chanpan\classes\utils\CNDomain::getWebroot().'/backup_files/uploads/';
            $unzip = \cpn\chanpan\classes\utils\CNProjectBackup::unZipFile("{$path}1531458125025354100");
            print_r($unzip); return;
        } catch (Exception $ex) {
            print_r($ex->getMessage());
            return;
        }
        return;
    }//unzip
    
    
    
}
