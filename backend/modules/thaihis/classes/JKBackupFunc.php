<?php

namespace backend\modules\thaihis\classes;

use Yii;

class JKBackupFunc {

    //put your code here
    public static function getDiagnosis($s, $e) {
        $sql = "
            SELECT dt.id,dt.ptid,dt.sitecode,dt.ptcode,dt.ptcodefull,dt.target,dt.hptcode,dt.hsitecode
                ,dt.xdepartmentx,dt.xsourcex,dt.sys_lat,dt.sys_lng,dt.error,dt.rstat
                ,dt.user_create,dt.create_date,dt.user_update,dt.update_date
                ,dt.di_visit_id,dt.di_icd10,dt.di_txt,dt.my_59a77e293216b,dt.di_send_status
            FROM zdata_dt dt
            INNER JOIN backup_logs bl ON(bl.dataid=dt.di_visit_id)
            WHERE bl.tb_name='zdata_visit'
            LIMIT $s, $e ";
        $result = Yii::$app->db_nhis->createCommand($sql)->queryAll();

        return $result;
    }
    
    public static function getDiagnosisComo($s, $e) {
        $sql = "
            SELECT como.id,como.ptid,como.sitecode,como.ptcode,como.ptcodefull,como.target,como.hptcode,como.hsitecode
                ,como.xdepartmentx,como.xsourcex,como.sys_lat,como.sys_lng,como.error,como.rstat
                ,como.user_create,como.create_date,como.user_update,como.update_date
                ,como.di_como_visit_id,como.my_59de1f1c4f47a,como.di_como_icd10
            FROM zdata_diag_como como 
            INNER JOIN backup_logs bl ON(bl.dataid=como.di_como_visit_id)
            WHERE bl.tb_name='zdata_visit'
            LIMIT $s, $e ";
        $result = Yii::$app->db_nhis->createCommand($sql)->queryAll();

        return $result;
    }
    
    public static function getDiagnosisComp($s, $e) {
        $sql = "
            SELECT comp.id,comp.ptid,comp.sitecode,comp.ptcode,comp.ptcodefull,comp.target,comp.hptcode,comp.hsitecode
                ,comp.xdepartmentx,comp.xsourcex,comp.sys_lat,comp.sys_lng,comp.error,comp.rstat
                ,comp.user_create,comp.create_date,comp.user_update,comp.update_date
                ,comp.di_comp_visit_id,comp.my_59de1f1c4f47a,comp.di_comp_icd10
            FROM zdata_diag_comp comp
            INNER JOIN backup_logs bl ON(bl.dataid=comp.di_comp_visit_id)
            WHERE bl.tb_name='zdata_visit'
            LIMIT $s, $e ";
        $result = Yii::$app->db_nhis->createCommand($sql)->queryAll();

        return $result;
    }
    
    public static function getOperation($s, $e) {
        $sql = "
            SELECT ope.id,ope.ptid,ope.sitecode,ope.ptcode,ope.ptcodefull,ope.target,ope.hptcode,ope.hsitecode
                ,ope.xdepartmentx,ope.xsourcex,ope.sys_lat,ope.sys_lng,ope.error,ope.rstat
                ,ope.user_create,ope.create_date,ope.user_update,ope.update_date
                ,ope.di_operat_visit_id,ope.my_59de1f1c4f47a,ope.di_operat_icd9
            FROM zdata_operat ope
            INNER JOIN backup_logs bl ON(bl.dataid=ope.di_operat_visit_id)
            WHERE bl.tb_name='zdata_visit'
            LIMIT $s, $e ";
        $result = Yii::$app->db_nhis->createCommand($sql)->queryAll();

        return $result;
    }

}
