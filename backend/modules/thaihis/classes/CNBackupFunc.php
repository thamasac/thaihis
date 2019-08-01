<?php
namespace backend\modules\thaihis\classes;
use Yii;
class CNBackupFunc {
    
    /**
     * 
     * @param type $s
     * @param type $e
     * @return boolean
     */
    public static function backupZdata_Tk($s, $e){
       try{
           //zdata_tk
            $sql="
                SELECT tk.* FROM zdata_tk AS tk 
                INNER JOIN backup_logs bl ON(bl.dataid=tk.tk_visit_id)
                WHERE tk.rstat not in(0,3) AND bl.tb_name='zdata_visit'
                ORDER BY `tk`.`id`
                LIMIT $s, $e 
            ";

            $result = Yii::$app->db_nhis->createCommand($sql)->queryAll();
            return $result;
       } catch (\yii\db\Exception $ex) {
           \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
           return false;
       }
       
               
               
    }
    /**
     * 
     * @param type $s
     * @param type $e
     * @return boolean
     */
    public static function backupZdata_Vs($s, $e){
        //zdata_vs
        try{
            $sql="
                SELECT vs.* FROM zdata_vs AS vs 
                INNER JOIN backup_logs bl ON(bl.dataid=vs.vs_visit_id)
                WHERE vs.rstat not in(0,3) AND bl.tb_name='zdata_visit'
                ORDER BY `vs`.`id`
                LIMIT $s, $e 
            ";

            $result = Yii::$app->db_nhis->createCommand($sql)->queryAll();
            return $result;
       } catch (\yii\db\Exception $ex) {
           \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
           return false;
       }
    }
    
    /**
     * 
     * @param type $s
     * @param type $e
     * @return boolean
     */
    public static function backupZdata_Bmi($s, $e){
        //zdata_bmi
        try{
            $sql="
                SELECT bmi.* FROM zdata_bmi AS bmi 
                INNER JOIN backup_logs bl ON(bl.dataid=bmi.bmi_visit_id)
                WHERE bmi.rstat not in(0,3) AND bl.tb_name='zdata_visit'
                ORDER BY `bmi`.`id`
                LIMIT $s, $e 
            ";

            $result = Yii::$app->db_nhis->createCommand($sql)->queryAll();
            return $result;
       } catch (\yii\db\Exception $ex) {
           \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($ex);
           return false;
       }
        
    }
}
