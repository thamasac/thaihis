<?php
 
namespace backend\modules\manageproject\classes;
use backend\modules\manageproject\classes\CNCloneDb;
use yii\db\Exception; 
class CNDatabaseFunc2 {
    public static function getDatabase(){
        $data = CNDatabaseQuery::getDatabaseQuery();
        $database=[];
        if(!empty($data)){
            foreach($data as $d){
                array_push($database, $d['Database']);
            }
            return $database;
        }else{
            return null;
        }
    }
    
    /**
     * 
     * @param type $dataProject type array table zdata_create_project
     * @param type $dbClone database name 
     * @param type $template template select 
     * @param type $myProject  
     */
    public static function cloneRepair($dataProject ,$myProject){
        try{
             
            //\appxq\sdii\utils\VarDumper::dump($myProject);
            $createDatabaseStatus   = CNCloneDb::createDatabaseByName($myProject['config_db']);
            if($createDatabaseStatus){
                $createTableAll     = CNCloneDb::createTableAll($myProject['config_db'], $myProject['project_template'], $dataProject['projectacronym']);
                if($createTableAll){
                    $deleteUser     = CNCloneDb::deleteUserNotAdmin($myProject['config_db']);
                    $deleteMatching = CNCloneDb::deleteMatching($myProject['config_db']);
                    $deleteSitecode = CNCloneDb::deleteSitecode($myProject['config_db']);
                    
                    $uID            = \cpn\chanpan\classes\CNUser::getUserId();
                    $checkUser      = \cpn\chanpan\classes\CNUser::checkUserDynamicDb($uID, $myProject['config_db']);
                    if (empty($checkUser)) {
                        $dataUser   = \cpn\chanpan\classes\CNUser::getUserNcrcById($uID);
                        $addUser    = \cpn\chanpan\classes\CNUser::AddUserDyNamicDb($myProject['config_db'], $dataUser);
                    }

                    $dataOption     = ['option_value' => 3];
                    $dataOption2    = ['option_value' => $dataProject['projectacronym']];
                    $dataOption3    = ['option_value' => ''];
                    
                    $d1=\backend\modules\core\classes\CoreFunc::updateCoreOptionValueByName("step", $dataOption, $myProject['config_db']);
                    $d2=\backend\modules\core\classes\CoreFunc::updateCoreOptionValueByName("company_name", $dataOption2, $myProject['config_db']);
                    $d3=\backend\modules\core\classes\CoreFunc::updateCoreOptionValueByName('site_text', $dataOption3, $myProject['config_db']);
                if($d1 || $d2 ||$d3){return true;}
              }                
            }return false;
        } catch (Exception $error) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($error);
            return false;
        }
    }
    
    /**
     * 
     * @param type $dbname string database name or template  ex $dbname = 'ncrc' 
     * @return type Query all table in database name or template
     */
    public static function getTableAll($dbname){
        return CNCloneDb::getTableAll($dbname);
    }
    
    /**
    * 
    * @param type $dbname database name or template   ex ncrc 
    * @param type $tbname table name ex ezform 
    * @return query or false
    */
    public static function getTableByName($dbname, $tbname){
        return CNDatabaseQuery::getTableByName($dbname, $tbname);
    }
    
    /**
     * 
     * @param type $dbname database name ที่จะสร้าง 
     * @param type $table ตารางที่จะสร้าง 
     * @param type $db_template  template ที่ต้องการสร้าง 
     * @param type $table_template ตารางใน template
     */
    public static function createTable($dbname, $table, $db_template, $table_template){
        return CNDatabaseQuery::createTable($dbname, $table, $db_template, $table_template);
    }
    /**
     * 
     * @param type $dbClone ฐานข้อมูลที่จะ update
     * @param type $table_struc ข้อมูล table ที่ get มาจาก template
     * @param type $tbname ตารางที่เราจะเช็ค  
     */
    public static function updateFields($dbname, $table_struc, $tbname){
         $tables = self::getTableByName($dbname, $tbname);  
         
         if(empty($tables)){             
            CNDatabaseQuery::createTable($dbname, $table_struc['table'], $table_struc['template'], $table_struc['table']);
         }
         $tableArr = [];
         if(!empty($tables)){
            foreach($tables['columns'] as $k1=>$v1){
                array_push($tableArr, $v1['Field']);
            }//end foreach
            
            foreach($table_struc['columns'] as $k=>$v){
                if(!in_array($v['Field'], $tableArr)){                      
                      $first = FALSE;
                      $nulls = FALSE;                 
                      $after_field = '';
                      if($k == 0 ){
                         $first = TRUE;
                      }else{
                          $after_field=$table_struc['columns'][($k !=0 ) ? $k-1 : $k]['Field'];
                      }
                      if($v['Null'] == 'YES'){
                          $nulls=TRUE;
                      } 
                      return CNDatabaseQuery::addField($dbname,$tables['table'], $v['Field'], $v['Type'], $first, $nulls, $after_field);    
                } 
            }//end foreach
         }//end if
         return true;
          
    }

    public static function getLengthText($name){
        $gname = \yii\helpers\Html::encode($name);
        $checkthai = \backend\modules\ezmodules\classes\ModuleFunc::checkthai($gname);
        $len = 12;
        if ($checkthai != '') {
            $len = $len * 3;
        }
        if (strlen($gname) > $len) {
            $gname = substr($gname, 0, $len) . '...';
        }
        return $gname;
    }
     
}
