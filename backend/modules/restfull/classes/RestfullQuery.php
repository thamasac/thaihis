<?php

namespace backend\modules\restfull\classes;

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
class RestfullQuery {

    public static function getAppointConfByDate($dept, $date) {
        $sql = "SELECT id,app_conf_qty - IFNULL(app_conf_qty_pt,0) AS sum_qty,app_conf_date
                FROM zdata_app_conf
                WHERE app_conf_dept=:dept AND app_conf_date = :date";

        return Yii::$app->db->createCommand($sql, [':dept' => $dept, ':date' => $date])->queryAll();
    }

    public static function getPatientData($page, $q, $limit) {

        $sqllike = '';
        if (!empty($q)) {
            $sqllike = " AND CONCAT(pt_hn,pt_firstname,pt_lastname,pt_cid) like '%" . $q . "%'  ";
        }
        $sqlpage = '';
        if ($page == 1) {
            $sqlpage = " limit $limit";
        } else {
            $sqlpage = " limit $page,$limit";
        }
        $sql = "SELECT pt_hn,zpf.prefix_name,pt_firstname,pt_lastname,pt_cid,pt_bdate,CONCAT(pt_address,' หมู่ ',pt_moi,' ต.',cd.DISTRICT_NAME,' อ.',cm.AMPHUR_NAME,'จ.',cp.PROVINCE_NAME ,' ',zp.pt_addr_zipcode) AS Fulladdress
       ,pt_phone,pt_phone2,znn.national_name,znn2.national_name AS origin_name ,zrg.religion_name,
CASE WHEN pt_mstatus =1 THEN 'โสด' WHEN pt_mstatus='2' THEN 'คู่' WHEN pt_mstatus='3' THEN 'หม้าย' WHEN pt_mstatus='4' THEN 'อย่า' ELSE 'ไม่ทราบ' END AS pt_mstatus
,zopt.occ_name,pt_email,pt_line_id,(SELECT visit_type  from zdata_visit zv WHERE  zv.ptid = zp.ptid  ORDER BY  id  DESC LIMIT 1) AS visit_type,pt_contact_name,pt_contact_status,pt_contact_phone
FROM zdata_patientprofile zp
LEFT JOIN const_district cd ON(zp.pt_addr_tumbon = cd.DISTRICT_CODE)
LEFT JOIN const_amphur cm ON(cd.AMPHUR_ID = cm.AMPHUR_ID)
LEFT JOIN const_province cp ON(cd.PROVINCE_ID = cp.PROVINCE_ID)
LEFT JOIN zdata_prefix zpf ON(zp.pt_prefix_id = zpf.prefix_id)
LEFT JOIN zdata_national znn ON(zp.pt_national_id = znn.national_id)
LEFT JOIN zdata_national znn2 ON(zp.pt_origin_id = znn2.national_id)
LEFT JOIN zdata_religion zrg ON(zp.pt_religion_id = zrg.religion_id )
LEFT JOIN zdata_occupation zopt ON(zp.pt_occ = zopt.occ_id) 
              WHERE zp.rstat = 1  $sqllike $sqlpage";

        return Yii::$app->db->createCommand($sql)->queryAll();
    }

    public static function getCountPatientData($q) {
        $sqllike = '';
        if (!empty($q)) {
            $sqllike = "AND CONCAT(pt_hn,pt_firstname,pt_lastname,pt_cid) like '%" . $q . "%'  ";
        }
        $sql = "SELECT COUNT(zp.pt_hn) AS COUNTS
                    FROM zdata_patientprofile zp
             WHERE zp.rstat = 1 $sqllike  ";

        return Yii::$app->db->createCommand($sql)->queryOne();
    }

    public static function getAppointHistory($cid) {
      
        $sql = "SELECT zpp.pt_hn,zpp.pt_cid,za.app_pt_detail,za.ptid,za.app_date,za.app_time,zd.sect_code,za.app_doctor FROM zdata_appoint za 
INNER JOIN zdata_patientprofile zpp ON(za.app_pt_id = zpp.ptid)
LEFT JOIN dept_sect zd ON(za.app_dept = zd.sect_code)
WHERE zpp.pt_cid = :cid";

        return Yii::$app->db->createCommand($sql,[':cid'=>$cid])->queryAll();
    }
    public static function getPatientDisease($cid){
         $sql = "SELECT zpp.pt_cid,zph.pt_pt_id,CASE WHEN pt_disease_status = 1 THEN 'ไม่มี' ELSE 'มี' END AS pt_disease_status,
zdb1.diag_name AS diag_name1,pt_disease_num AS pt_disease_num1,pt_disease_num2,pt_disease_num3,zdb2.diag_name AS diag_name2,zdb3.diag_name AS diag_name3,ch1.name AS hname,ch2.name AS h2name,ch3.name AS h3name,pt_disease_detail4,
CASE WHEN pt_drug_status = 1 THEN 'ไม่เคยแพ้ยา' WHEN pt_drug_status = 2 THEN 'แพ้ยา' END AS pt_drug_status,pt_drug_list,pt_drug_action
FROM zdata_patienthistory zph
INNER JOIN zdata_patientprofile zpp ON(zph.pt_pt_id = zpp.ptid)
LEFT JOIN zdata_diag_basic zdb1 ON(zph.pt_disease_detail = zdb1.id)
LEFT JOIN zdata_diag_basic zdb2 ON(zph.pt_disease_detail2 = zdb2.id)
LEFT JOIN zdata_diag_basic zdb3 ON(zph.pt_disease_detail3 = zdb3.id)
LEFT JOIN const_hospital ch1 ON(zph.pt_disease_hos = ch1.`code`)
LEFT JOIN const_hospital ch2 ON(zph.pt_disease_hos2 = ch2.`code`)
LEFT JOIN const_hospital ch3 ON(zph.pt_disease_hos3 = ch3.`code`)
WHERE zpp.pt_cid = :cid";

        return Yii::$app->db->createCommand($sql,[':cid'=>$cid])->queryOne();
    }
    public static function getDept(){
        $sql ="SELECT sect_code,sect_name,sect_his_type,sect_map_code FROM dept_sect";
        return Yii::$app->db->createCommand($sql)->queryAll();
    }
    public static function getDoctor($typeuser){
        $sql="SELECT user_id,title,firstname,lastname,department,
CASE WHEN position =0 THEN 'พยาบาลวิชาชีพ' WHEN position =1 THEN 'พนักงานช่วยการพยาบาล' WHEN position =2 THEN 'แพทย์' 
WHEN position =3 THEN 'ทันตแพทย์'
WHEN position =4 THEN 'นักเทคนิคการแพทย์' WHEN position =5 THEN 'นักรังสีการแพทย์'WHEN position =6 THEN 'ผู้ดูแลระบบ' 
WHEN position =7 THEN 'เจ้าหน้าที่' 
ELSE  'บุคคลทั่วไป' END AS position,ds.sect_name,certificate
 FROM `profile` pf
 LEFT JOIN dept_sect ds ON(pf.department = ds.sect_code)
 where position = :typeuser";
        return Yii::$app->db->createCommand($sql,['typeuser'=>$typeuser])->queryAll();
    }
    public static function getHistoryPatient($cid){
        $sql ="SELECT zv.id,zv.visit_date,zb.bmi_bw,zb.bmi_ht,zb.bmi_bmi,zb.bmi_bsa,zb.bmi_waistline,
zvs.vs_bp_squeeze,zvs.vs_bp_loosen,zvs.vs_pulse,zvs.vs_respiratory,zvs.vs_temperature,zv.xdepartmentx,
(SELECT visit_tran_doctor FROM zdata_visit_tran zvt WHERE zvt.visit_tran_visit_id = zv.id AND zvt.visit_tran_doctor IS NOT NULL LIMIT 1 ) AS doctor,
zi.ins_name
FROM zdata_visit zv 
INNER JOIN zdata_patientprofile zpp ON(zv.ptid = zpp.ptid)
LEFT JOIN zdata_bmi zb ON(zv.id = zb.bmi_visit_id)
LEFT JOIN zdata_vs zvs ON(zv.id = zvs.vs_visit_id)
LEFT JOIN zdata_tk ztk ON(zv.id = ztk.tk_visit_id)
LEFT JOIN zdata_inspect zi ON(ztk.tk_inspect  = zi.id)
WHERE zpp.pt_cid = :cid AND zv.rstat =1 AND zpp.rstat =1";
         return Yii::$app->db->createCommand($sql,[':cid'=>$cid])->queryAll();
    }
}
