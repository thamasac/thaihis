<?php

namespace backend\modules\cpoe\classes;

use Yii;

/**
 * OvccaFunc class file UTF-8
 * @author SDII <iencoded@gmail.com>
 * @copyright Copyright &copy; 2015 AppXQ
 * @license http://www.appxq.com/license/
 * @version 1.0.0 Date: 9 ก.พ. 2559 12:38:14
 * @link http://www.appxq.com/
 * @example 
 */
class CpoeQuery {

    public static function getVisitDept($dept, $date) {
        $sql = "SELECT zv.id,vp.pt_id,CONCAT(prefix_name,pt_firstname,' ',pt_lastname) as fullname
                FROM zdata_visit zv
                INNER JOIN (zdata_patientprofile vp LEFT JOIN zdata_prefix zpf ON vp.pt_prefix_id=zpf.prefix_id) ON(vp.pt_id=zv.visit_pt_id)
                INNER JOIN zdata_visit_tran  zvt ON(zv.id=zvt.visit_tran_visit_id)
                WHERE visit_tran_dept=:dept AND pt_status='VISIT' 
                AND DATE(zv.visit_date)=:date";
        return Yii::$app->db->createCommand($sql, [':dept' => $dept, ':date' => $date])->queryAll();
    }

    public static function getProjectNo() {
        $sql = "SELECT target,project_id,project_name
                FROM zdata_project";
        return Yii::$app->db->createCommand($sql)->queryAll();
    }

    public static function getCheckupResult($visit_id, $pt_id = null) {
        $paramsStr = '';
        if ($visit_id) {
            $paramsStr = "AND zv.id=:visit_id";
            $paramsArry[':visit_id'] = "$visit_id";
        }

        if ($pt_id) {
            $paramsStr = "AND zv.ptid=:ptid";
            $paramsArry[':ptid'] = "$pt_id";
        }

        $sql = "SELECT zrc.id,vp.pt_hn,CONCAT(prefix_name,pt_firstname,' ',pt_lastname) as fullname,CONCAT(title,pf.firstname,' ',pf.lastname) AS doctor_name,pt_bdate,DATE(zv.visit_date) AS visit_date 
                ,pt_sex,visit_type
                ,zvs.vs_pulse,zvs.vs_respiratory,CONCAT(vs_bp_squeeze,'/',vs_bp_loosen) AS BP,zbmi.bmi_bw,zbmi.bmi_ht,zbmi.bmi_bmi 
                ,zbmi.bmi_waistline,ckr_pe,ckr_pe_abn,ckr_cbc,ckr_cbc_abn,ckr_ua,ckr_ua_abn,ckr_se,ckr_se_abn,ckr_hba,ckr_hba_abn 
                ,ckr_rpr,ckr_rpr_abn,ckr_ekg,ckr_ekg_abn,ckr_summary,ckr_summary_detail,ckr_about,ckr_seedoctor,ckr_resultcheck,ckr_resultcheck_detail
                ,ckr_cxr,ckr_ab,ckr_cxr_abn,ckr_chem,ckr_chem_abn,zrc.ckr_doctorverify,ckr_summary_abn,ckr_hpv_1,ckr_hpv_2,ckr_hpv_3,ckr_hpv_4
                ,ckr_breast_abn,ckr_breast,ckr_sgt_5,ckr_sgt_other_5,ckr_ekg_comment,ckr_pap_comment,ckr_attach_file
                ,ckr_seedoctor2,ckr_order_drug2,ckr_resultcheck2,ckr_resultcheck_detail2
                #prep 
                ,ckr_epeptp,ckr_cyto,ckr_cyto_abn,ckr_tibvc_1,ckr_tibvc_2,ckr_tibvc_3,ckr_tibvc_4,ckr_tibvc_5,ckr_tibvc_6 
                ,ckr_caypa_1,ckr_caypa_2,ckr_caypa_3,ckr_caypa_4,ckr_awscc,ckr_haalh,ckr_awcc,ckr_awcc_scc,ckr_sgt_1,ckr_sgt_2,ckr_sgt_3,ckr_sgt_4,ckr_sgt_other_1
                ,ckr_ab_other,DATE(zrc.report_date) AS report_date,ckr_sum_bmi,ckr_order_drug,ckr_sero,ckr_sero_abn
                FROM zdata_visit zv 
                INNER JOIN zdata_reportcheckup zrc ON(zrc.ckr_visit_id=zv.id) 
                INNER JOIN (zdata_patientprofile vp LEFT JOIN zdata_prefix zpf ON vp.pt_prefix_id=zpf.prefix_id)  ON(vp.id=zv.target) 
                
                LEFT JOIN zdata_vs zvs ON(zvs.id=(SELECT MAX(id) FROM zdata_vs WHERE vs_visit_id=:visit_id AND rstat='1') AND zvs.rstat='1') 
                LEFT JOIN zdata_bmi zbmi ON(zbmi.id=(SELECT MAX(id) FROM zdata_bmi WHERE bmi_visit_id=:visit_id AND rstat='1') AND zbmi.rstat='1') 
                LEFT JOIN `profile` pf ON(pf.user_id=zrc.ckr_doctorverify) 
                WHERE zrc.rstat='1' /*AND zrc.ckr_status='2'*/ $paramsStr";

        return Yii::$app->db->createCommand($sql, $paramsArry)->queryOne();
    }

    public static function getVisitCheckupReport($action_id, $action, $report_status, $que_type = 1) {
        $paramsStr = " AND zrc.ckr_status = :ckr_status";
        $paramsArry = [':ckr_status' => $report_status,];
        if ($action == 'visit') {
            $paramsStr .= ' AND visit_tran_visit_id=:visit_id AND visit_tran_doctor=:doctor_id';
            $paramsArry[':visit_id'] = "$action_id";
            $paramsArry[':doctor_id'] = Yii::$app->user->identity->profile->attributes['user_id'];
        } elseif ($action == 'doctor') {
            $paramsStr .= " AND visit_tran_doctor=:doctor_id ORDER BY zvt.update_date ASC";
            $paramsArry[':doctor_id'] = "$action_id";
        } elseif ($action == 'report') {
            $paramsStr .= ' AND zrc.id=:report_id';
            $paramsArry[':report_id'] = "$action_id";
        }
        if ($que_type == '1') {
            $sqlVisitType = " AND order_tran_code IN(/*'CH',*/'FE001','FE002','HM001','UR001','BC001','BC015','BC016','BC017','BC002','IM047',
                'BC003','BC005','BC006','BC009','CG001','BC011','BC012','BC013','BC014','IM006','IM001','IM002','PH001'
                ,'IM008','CG016')
                AND order_tran_status='2' AND visit_type='1'";
        } else {
            $sqlVisitType = " AND order_tran_code IN('CG001','CG016')
                AND order_tran_status='2' AND visit_type= '4'";
        }

        $sql = "SELECT DISTINCT zvt.visit_tran_doctor,zvt.visit_tran_visit_id,zvt.ptid,zrc.id AS report_id,vp.pt_hn
                ,zvt.visit_tran_visit_id AS visit_id,CONCAT(prefix_name,pt_firstname,' ',pt_lastname) as fullname,zbmi.bmi_bmi
                FROM zdata_visit zv
		INNER JOIN zdata_visit_tran zvt ON(zv.id=zvt.visit_tran_visit_id)
                INNER JOIN zdata_reportcheckup zrc ON(zrc.ckr_visit_id=zvt.visit_tran_visit_id)
                INNER JOIN (zdata_patientprofile vp LEFT JOIN zdata_prefix zpf ON vp.pt_prefix_id=zpf.prefix_id)  ON(vp.id=zv.target)
                
                LEFT JOIN (SELECT DISTINCT order_visit_id,order_tran_status
                FROM zdata_order_tran zot
                INNER JOIN zdata_order_header zoh ON zot.order_header_id=zoh.id
                INNER JOIN zdata_visit zv ON(zv.id = zoh.order_visit_id)
                WHERE zot.rstat=1 $sqlVisitType) AS chko ON(chko.order_visit_id=zv.id)
                LEFT JOIN zdata_bmi zbmi ON(zbmi.id=(SELECT MAX(id) FROM zdata_bmi WHERE bmi_visit_id=zv.id AND rstat='1')) 
                WHERE zrc.rstat not in(0,3) /*AND zvt.visit_tran_doc_status='2'*/ AND chko.order_visit_id IS NULL 
                $paramsStr  
                ";

        return Yii::$app->db->createCommand($sql, $paramsArry)->queryOne();
    }

    public static function DeleteDataFromTable($id) {
        $sql = "DELETE FROM zdata_project_patient_name WHERE id = :id";
        Yii::$app->db->createCommand($sql, [':id' => $id])->execute();
    }

    public static function getVisitTranDoctorByVisit($visit_id) {
        $sql = "SELECT DISTINCT CONCAT(title,pf.firstname,' ',pf.lastname) AS doctor_name,visit_tran_doctor,zv.ptid
            ,zrc.id AS report_id,zv.visit_date
                FROM zdata_visit zv
                LEFT JOIN zdata_visit_tran zvt ON(zvt.visit_tran_visit_id=zv.id AND visit_tran_doctor<>'')
                LEFT JOIN zdata_reportcheckup zrc ON(zrc.ckr_visit_id=zv.id)
                LEFT JOIN `profile` pf ON(pf.user_id=zvt.visit_tran_doctor)
                WHERE zv.rstat='1' AND zv.id=:visit_id";

        return Yii::$app->db->createCommand($sql, [':visit_id' => $visit_id])->queryAll();
    }

    public static function getProjectCheckVisit($cid) {
        $date = date('Y-m-d', strtotime('-7 days'));
        $sql = "SELECT DISTINCT zppn.id,zppn.target_project,zppn.cid_project
                FROM zdata_project_patient_name zppn
                LEFT JOIN zdata_patientprofile pf ON(pf.pt_cid = zppn.cid_project)
                WHERE zppn.rstat = '1' AND cid_project=:cid AND status_project=0
                AND date_end_project > :date
                ORDER BY zppn.update_date DESC
                ";

        return Yii::$app->db->createCommand($sql, [':cid' => $cid, ':date' => $date])->queryOne();
    }

}
