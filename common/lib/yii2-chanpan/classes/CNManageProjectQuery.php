<?php

namespace cpn\chanpan\classes;
use yii\db\Exception;
use Yii;
class CNManageProjectQuery {
   /**
    * 
    * @param type $tbName array $tbName=['ezform','ezform_assign'];
    */
   public static function getCoreTable(){
       try{
            $sql = "SELECT * FROM core_table";
            $data = \Yii::$app->db_main->createCommand($sql)->queryAll();
            return $data;
       } catch (Exception $ex) {
           \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
       }
   }   
   /**
    * 
    * @param type $tbName
    */
   public static function dropTableOld($tbName){
       $dataStatus=[];
       try{
           foreach($tbName as $key=>$tb){
                $sql="DROP TABLE IF EXISTS `{$tb['tbname']}`";
                $ex = \Yii::$app->db->createCommand($sql)->execute();
                if($ex){
                   $dataStatus[$key] = ['tbname'=>$tb['tbname'], 'status'=>'success']; 
                }else{
                    $dataStatus[$key] = ['tbname'=>$tb['tbname'], 'status'=>'error']; 
                }
           }
           return $dataStatus;
       } catch (Exception $ex) {
           \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
       }
      
   }
   /**
    * 
    * @param type $tbName array
    */
   public static function updateCoreTable($tbName){
       try{
           foreach($tbName as $key=>$tb){
                $sql="CREATE TABLE IF NOT EXISTS {$tb['tbname']} LIKE `ncrc`.{$tb['tbname']}";
                $execute = Yii::$app->db->createCommand($sql)->execute();
                $sql2="REPLACE INTO {$tb['tbname']} (SELECT * FROM `ncrc`.{$tb['tbname']})";
                $execute2 = Yii::$app->db->createCommand($sql2)->execute(); 
           }
           return true;
       } catch (Exception $ex) {
           \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
           return $ex->getMessage();
       }
   }
   
   public static function updateAconym($aconym){
      $data=['option_value'=>$aconym] ;
      
      return \backend\modules\core\classes\CoreFunc::updateCoreOptionValueByNameInDb("company_name", $data);
   }
   
   
   
}
