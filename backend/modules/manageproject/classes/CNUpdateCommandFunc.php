<?php
 
namespace backend\modules\manageproject\classes;
use yii\db\Exception; 
use Yii;

class CNUpdateCommandFunc {
    
    public static function getDataUpdateProject($id=''){
        
        try{
            $data = (new \yii\db\Query())->select('*')->from('zdata_update_project')->where('rstat not in (0,3)');      
            if($id != ''){
              $data->andWhere('id > :id',[':id'=>$id]);
              $data->orderBy(['id'=>SORT_ASC]);  
            }else{
              $data->orderBy(['id'=>SORT_DESC]);  
            }
            
            $db = isset(\Yii::$app->db_main)?\Yii::$app->db_main:\Yii::$app->db;
            if(\cpn\chanpan\classes\CNServerConfig::isLocal() || \cpn\chanpan\classes\CNServerConfig::isPortal()){
                $db = isset(\Yii::$app->db) ? \Yii::$app->db : '';
            }else{
                $db = isset(\Yii::$app->db_main)?\Yii::$app->db_main:\Yii::$app->db;
            }
            return $data->all($db);
            
        } catch (Exception $ex) { 
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
            return false;
        }
    }
    
    public static function getDataUpdateProjectById($id=''){
        
        try{
            $data = (new \yii\db\Query())->select('*')->from('zdata_update_project')->where('id=:id AND rstat not in (0,3)',[':id'=>$id]);      
            return $data->one(\Yii::$app->db_main);
        } catch (Exception $ex) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
            return false;
        }
    }
    
    public static function getLogUpdate(){
        try{
            $checkData = (new \yii\db\Query())->select('*')->from('log_update_project')->all();
            if($checkData){
                return $checkData;
            }
        } catch (Exception $ex) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
            return false;
        }
    }
    /**
     * 
     * @return string max id table log_update_project
     */
    public static function getMaxIdLogUpdate(){
        try{
            $checkData = (new \yii\db\Query())->select('*')
                    ->from('log_update_project')
                    ->max('id');
            if($checkData){
                return $checkData;
            }
        } catch (Exception $ex) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
            return false;
        }
    }

    public static function updateCommand($sql_command, $id, $status=''){
        try{
          //  print_r($sql_command);return;
             
            $exe = \Yii::$app->db->createCommand($sql_command)->execute(); 
            if($exe == 0){                 
                if($status != ''){
                    return self::updateLogUpdateProject($id, 1);
                }else{
                    return self::saveLogUpdate($id, 1);
                }
                
            }else{
                return self::saveLogUpdate($id, 2);
            }
        } catch (Exception $ex) {  
            
            self::saveLogUpdate($id, 2, $ex->getMessage()); 
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
            return false;
        }   
        
    } 
    public static function saveLogUpdate($id, $rstat , $message=""){
        try{
            $checkData = (new \yii\db\Query())->select('*')->from('log_update_project')->where(['id'=>$id])->one();
            //\appxq\sdii\utils\VarDumper::dump($checkData);
            if(empty($checkData)){
                 
               return \Yii::$app->db->createCommand()->insert('log_update_project', [
                    'id'=> $id,
                    'update_id'=>$rstat,
                    'message'=>$message,
                    'created_at'=>date('Y-m-d H:i:s')
                ])->execute();
            }           
            
        } catch (Exception $ex) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
            return false;
        }
    }
    
    public static function updateLogUpdateProject($id, $rstat, $message=""){
        try{ 
            $update = \Yii::$app->db->createCommand()->update('log_update_project', [                    
                    'update_id'=>$rstat,
                    'message'=>$message,
                    'created_at'=>date('Y-m-d H:i:s')
                ],'id=:id ',[':id'=>$id])->execute();        
            if($update){
                $update = ""; 
                return true;
            }
            
        } catch (Exception $ex) { 
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
            return false;
        }
    }
    
    public static function getLogNotUpdateAll(){
        try{
            $dbName = Yii::$app->params['model_dynamic']['config_db'];
            $sitecode = \common\modules\user\classes\CNSitecode::getSiteCodeCurrent();

            $sql="
                SELECT zup.sql_command, lup.message, zup.id, zup.user_update as user_id,lup.update_id as status, lup.created_at as date FROM log_update_project as lup
                INNER JOIN zdata_update_project as zup on lup.id=zup.id 
                WHERE lup.update_id = 2 AND zup.rstat not in(0,3)
                ORDER BY lup.id ASC
            ";
            $data = \Yii::$app->db->createCommand($sql)->queryAll();
            return isset($data) ? $data : null;
        } catch (Exception $ex) { 
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
            return false;
        }
        
    }
    public static function deleteLogByid($id){
        try{
            $sql = "DELETE FROM log_update_project WHERE id=:id";
            $params = [':id'=>$id];
            $delete = \Yii::$app->db->createCommand($sql, $params)->execute();
            return $delete;
        } catch (Exception $ex) { 
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
            return false;
        }
    }
    
}
