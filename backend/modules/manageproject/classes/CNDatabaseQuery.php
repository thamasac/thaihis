<?php

 
namespace backend\modules\manageproject\classes;
use yii\db\Exception;
use Yii;
class CNDatabaseQuery {
   public static function getDatabaseQuery(){
       try{
           $sql="SHOW DATABASES";
           return \Yii::$app->db_main->createCommand($sql)->queryAll();
       } catch (Exception $error) {
           \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($error);
           return FALSE;
       }
   }
   public static function getDbDynamicById(){
       try{
           $sql="SHOW DATABASES";
           return \Yii::$app->db_main->createCommand($sql)->queryAll();
       } catch (Exception $error) {
           \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($error);
           return FALSE;
       }
   }
   
   /**
    * 
    * @param type $dbname database name or template 
    * @param type $tbname table name ex ezform 
    * @return query or false
    */
   public static function getTableByName($dbname, $tbname){
       try{
           $sql                 ="SHOW FIELDS FROM `{$dbname}`.`{$tbname}`"; 
           $create              =\Yii::$app->db_main->createCommand($sql)->queryAll();
           $output              =[];
           if(!$create){
              return false; 
           }
           $output['table']     =$tbname;
           $output['template']  =$dbname;
           $output['columns']   =$create;
           return $output;
       } catch (Exception $error) {
           \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($error);
           return FALSE;
       }
       
   }
   public static function createTable($dbname, $table, $db_template, $table_template){
       try{
            $sql="CREATE TABLE IF NOT EXISTS {$dbname}.{$table} LIKE {$db_template}.{$table_template}";
            $execute = Yii::$app->db->createCommand($sql)->execute();
            if($execute){return true;}
            return false;
       } catch (Exception $error) {
           \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($error);
           return false;
       }
   }
   /**
    * 
    * @param type $table ที่จะเพิ่มฟิล์
    * @param type $field ที่จะเพิ่ม
    * @param type $type ประเภทฟิล์
    * @param type $first อยู่หน้าสุด boolean true or false
    * @param type $nulls เป็นค่าว่าง ได้หรือไม่ type string 'NO' 
    * @param type $after_field
    * @return boolean
    */
   public static function addField($dbname, $table,$field, $type, $first=FALSE, $nulls=FALSE, $after_field=''){
       try{
           $nulls = ($nulls == FALSE) ? 'NOT NULL' : 'NULL';
           $first = ($first == FALSE) ? "AFTER `{$after_field}`" : 'FIRST';
           $sql= "
               ALTER TABLE `{$dbname}`.`{$table}`
               ADD COLUMN `{$field}` {$type} {$nulls} {$first} ;
           ";
           $add = \Yii::$app->db->createCommand($sql)->execute();
           if($add){
             return true;
           }
           return false;    
           
        
//           $sql="
//                ALTER TABLE `advance_report_config`
//                 ADD COLUMN `test`  varchar(255) NULL AFTER `status`;
//            ";
        
       
       } catch (Exception $error) {
           \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($error);
           return false;
       }
       
   }
}
