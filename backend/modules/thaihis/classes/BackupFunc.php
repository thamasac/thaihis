<?php

namespace backend\modules\thaihis\classes;

use Yii;

class BackupFunc {

    public static function getLog($s, $e, $tb) {
        $sql = "SELECT *
                FROM backup_logs
                WHERE tb_name = :tb 
                ORDER BY `dataid`
                LIMIT $s, $e";

        $result = Yii::$app->db_nhis->createCommand($sql, [':tb' => $tb])->queryAll();

        return $result;
    }

    public static function getDeptAllTest($s, $e) {
        $sql = "SELECT *
                FROM zdata_working_unit
                WHERE rstat not in(0,3) 
                ORDER BY `id`
                LIMIT $s, $e";

        $result = Yii::$app->db->createCommand($sql)->queryAll();

        return $result;
    }

    public static function getVisitAll($s, $e) {
        $sql = "SELECT *
                FROM zdata_visit zv
                WHERE rstat not in(0,3) 
                ORDER BY `id`
                LIMIT $s, $e";

        $result = Yii::$app->db_nhis->createCommand($sql)->queryAll();

        return $result;
    }

    public static function getVisitTran($s, $e) {
        $sql = "SELECT zvt.*
                FROM zdata_visit_tran zvt
                INNER JOIN backup_logs bl ON(bl.dataid=zvt.visit_tran_visit_id)
                WHERE zvt.rstat not in(0,3) AND bl.tb_name='zdata_visit'
                ORDER BY zvt.`id`
                LIMIT $s, $e";

        $result = Yii::$app->db_nhis->createCommand($sql)->queryAll();

        return $result;
    }

    public static function getOrderTran($s, $e) {
        $sql = "SELECT 
                zot.ptcode,zot.ptcodefull,zot.ptid,zot.rstat,zot.sitecode,zot.sys_lat,zot.sys_lng
                ,zot.target,zot.update_date,zot.user_create,zot.user_update,zot.xdepartmentx,zot.xsourcex
                ,zot.create_date,zot.error,zot.hptcode,zot.hsitecode,zot.id,zot.my_59ad6ccc47b10,zot.order_tran_code
                ,order_tran_status,get_59a8bcd032e66,order_tran_dept,order_vender_no,order_vender_status,order_tran_doctor,order_tran_notpay
                ,order_tran_pay,order_tran_cashier_status,order_tran_comment,order_qty,DATE(zv.visit_date) AS order_tran_date
                FROM zdata_order_tran zot
                INNER JOIN zdata_visit zv ON(zv.id=zot.order_tran_visit_id)
                WHERE zot.rstat not in(0,3) AND zv.rstat not in(0,3) 
                ORDER BY `zot`.target,`zot`.`id`
                LIMIT $s, $e";

        $result = Yii::$app->db_nhis->createCommand($sql)->queryAll();

        return $result;
    }

    public static function getPe($s, $e) {
        $sql = "SELECT pe.* FROM
                (SELECT 
                create_date,error,hptcode,hsitecode,id,my_59a77e0d9e8a1,
                ptcode,ptcodefull,ptid,rstat,sitecode
                sys_lat,sys_lng,target,update_date,user_create
                ,user_update,xdepartmentx,xsourcex,
                pe_abdomen,pe_abdomen_abn,pe_abdomen_note,pe_breast,pe_breast_note,
                pe_ga_1,pe_ga_2,pe_ga_3,pe_ga_5,pe_ga_6,pe_ga_7,pe_ga_8,
                pe_ga_abn,pe_head,pe_head_abn,pe_head_note,pe_heart,
                pe_heart_abn,pe_lung,pe_lung_abn,pe_mpc_1,pe_mpc_2,
                pe_mpc_3,pe_n_all,pe_neck,pe_neck_abn,pe_note,
                '' AS pe_pic_f,'' AS pe_pic_m,'' AS pe_sex,pe_visit_id
                FROM zdata_pe_f
                WHERE rstat not in(0,3)
                UNION
                SELECT 
                create_date,error,hptcode,hsitecode,id,my_59a77e0d9e8a1,
                ptcode,ptcodefull,ptid,rstat,sitecode
                sys_lat,sys_lng,target,update_date,user_create
                ,user_update,xdepartmentx,xsourcex,
                pe_abdomen,pe_abdomen_abn,pe_abdomen_note,pe_breast,pe_breast_note,
                pe_ga_1,pe_ga_2,pe_ga_3,pe_ga_5,pe_ga_6,pe_ga_7,pe_ga_8,
                pe_ga_abn,pe_head,pe_head_abn,pe_head_note,pe_heart,
                pe_heart_abn,pe_lung,pe_lung_abn,pe_mpc_1,pe_mpc_2,
                pe_mpc_3,pe_n_all,pe_neck,pe_neck_abn,pe_note,
                '' AS pe_pic_f,'' AS pe_pic_m,'' AS pe_sex,pe_visit_id
                FROM zdata_pe_m
                WHERE rstat not in(0,3)) AS pe
                INNER JOIN backup_logs bl ON(bl.dataid=pe.pe_visit_id)
                WHERE bl.tb_name='zdata_visit'
                LIMIT $s, $e";

        $result = Yii::$app->db_nhis->createCommand($sql)->queryAll();

        return $result;
    }

    public static function getReportCyto($s, $e) {
        $sql = "SELECT *
                FROM zdata_reportcyto
                WHERE rstat not in(0,3) 
                ORDER BY `id`
                LIMIT $s, $e";

        $result = Yii::$app->db_nhis->createCommand($sql)->queryAll();

        return $result;
    }

    public static function getReportEkg($s, $e) {
        $sql = "SELECT *
                FROM zdata_reportekg
                WHERE rstat not in(0,3) 
                ORDER BY `id`
                LIMIT $s, $e";

        $result = Yii::$app->db_nhis->createCommand($sql)->queryAll();

        return $result;
    }

    public static function getReportXray($s, $e) {
        $sql = "SELECT
                `zdata_reportxray`.`id`,
                `zdata_reportxray`.`ptid`,
                `zdata_reportxray`.`sitecode`,
                `zdata_reportxray`.`ptcode`,
                `zdata_reportxray`.`ptcodefull`,
                `zdata_reportxray`.`target`,
                `zdata_reportxray`.`hptcode`,
                `zdata_reportxray`.`hsitecode`,
                `zdata_reportxray`.`xsourcex`,
                `zdata_reportxray`.`xdepartmentx`,
                `zdata_reportxray`.`sys_lat`,
                `zdata_reportxray`.`sys_lng`,
                `zdata_reportxray`.`error`,
                `zdata_reportxray`.`rstat`,
                `zdata_reportxray`.`user_create`,
                `zdata_reportxray`.`create_date`,
                `zdata_reportxray`.`user_update`,
                `zdata_reportxray`.`update_date`,
                `zdata_reportxray`.`order_tran_id`,
                `zdata_reportxray`.`report_x_result` AS report_xray_result,
                `zdata_reportxray`.`report_status`,
                `zdata_reportxray`.`get_59ad6ccc47b10`,
                `zdata_reportxray`.`my_5a407dec76748`,
                `zdata_reportxray`.`report_xr_date` AS report_xray_date,
                `zdata_reportxray`.`report_x_resut_docid` AS report_xray_docid
                FROM
                `zdata_reportxray`
                WHERE rstat not in(0,3) 
                ORDER BY `id`
                LIMIT $s, $e";

        $result = Yii::$app->db_nhis->createCommand($sql)->queryAll();

        return $result;
    }

    public static function getReportCheckup($s, $e) {
        $sql = "SELECT *
                FROM zdata_reportcheckup
                WHERE rstat not in(0,3) 
                ORDER BY `id`
                LIMIT $s, $e";

        $result = Yii::$app->db_nhis->createCommand($sql)->queryAll();

        return $result;
    }

    public static function getPatientRight($s, $e) {
        $sql = "SELECT MAX(id) AS id,ptid,sitecode,ptcode,ptcodefull,target,hptcode,hsitecode,xsourcex,xdepartmentx,sys_lat
                ,sys_lng,error,rstat,user_create,create_date,user_update,update_date,right_hos_main,right_refer_no
                ,right_refer_start,right_refer_end,right_prove_no,right_code,right_sub_code
                ,right_status,right_flag,right_prove_end,right_hos_refer,right_project_id
                FROM zdata_patientright
                WHERE rstat not in(0,3)
                GROUP BY ptid
                ORDER BY id
                LIMIT $s, $e";

        $result = Yii::$app->db_nhis->createCommand($sql)->queryAll();

        return $result;
    }

    public static function getReceiptMas($s, $e) {
        $sql = "SELECT zrm.*
                FROM zdata_receipt_mas zrm
                INNER JOIN backup_logs bl ON(bl.dataid=zrm.receipt_visit_id AND bl.tb_name='zdata_visit')
                WHERE zrm.rstat not in(0,3) 
                ORDER BY zrm.`id`
                LIMIT $s, $e";

        $result = Yii::$app->db_nhis->createCommand($sql)->queryAll();

        return $result;
    }

    public static function getReceiptTrn($s, $e) {
        $sql = "SELECT zrt.*
                FROM zdata_receipt_trn zrt
                INNER JOIN backup_logs bl ON(bl.dataid=zrm.receipt_visit_id AND bl.tb_name='zdata_receipt_mas')
                WHERE zrt.rstat not in(0,3) 
                ORDER BY zrt.`id`
                LIMIT $s, $e";

        $result = Yii::$app->db_nhis->createCommand($sql)->queryAll();

        return $result;
    }

    public static function getDeptAll() {
        $sql = "SELECT id,
                    unit_code,
                    unit_name,
                    unit_desc
                FROM zdata_working_unit
                WHERE rstat not in(0,3)";

        $result = Yii::$app->db->createCommand($sql)->queryAll();

        return $result;
    }

    public static function getVersionByTb($tbname) {
        $sql = "SELECT ezf_id,
                    ezf_version,
                    ezf_name,
                    ezf_table
                FROM ezform
                WHERE ezf_table = :ezf_table";

        $result = Yii::$app->db->createCommand($sql, [':ezf_table' => $tbname])->queryOne();

        return $result;
    }

    public static function insertData($tb_name, $data) {
        $result = Yii::$app->db->createCommand()->insert($tb_name, $data)->execute();

        return $result;
    }

    public static function insertLogs($tb_name, $dataid, $data) {

        $result = Yii::$app->db_nhis->createCommand()->insert('backup_logs', [
                    'id' => \appxq\sdii\utils\SDUtility::getMillisecTime(),
                    'tb_name' => $tb_name,
                    'dataid' => $dataid,
                    'data_json' => \appxq\sdii\utils\SDUtility::array2String($data),
                ])->execute();

        return $result;
    }

    public static function getAppoint($s, $e) {
        $sql = "SELECT zdata_appoint.* 
                FROM zdata_appoint 
                WHERE rstat NOT IN (0,3)
                ORDER BY  id  LIMIT $s, $e";
        $result = Yii::$app->db_nhis->createCommand($sql)->queryAll();

        return $result;
    }

    public static function getTreatMent($s, $e) {
        $sql = "SELECT tm.*
            FROM zdata_treatment AS tm
            INNER JOIN backup_logs AS bl ON bl.dataid = tm.treat_visit_id
            WHERE bl.tb_name = 'zdata_visit'
            ORDER BY `tm`.`id`
                LIMIT $s, $e";
        $result = Yii::$app->db_nhis->createCommand($sql)->queryAll();

        return $result;
    }

    public static function getPatientHistory($s, $e) {
        $sql = "SELECT *
                FROM zdata_patienthistory
                 ORDER BY  id  LIMIT $s, $e";
        $result = Yii::$app->db_nhis->createCommand($sql)->queryAll();
        return $result;
    }

}
