<?php

namespace backend\modules\patient\classes;

use appxq\sdii\utils\VarDumper;
use Yii;
use backend\modules\ezforms2\classes\EzfQuery;
use yii\db\Query;

/**
 * OvccaFunc class file UTF-8
 * @author SDII <iencoded@gmail.com>
 * @copyright Copyright &copy; 2015 AppXQ
 * @license http://www.appxq.com/license/
 * @version 1.0.0 Date: 9 ก.พ. 2559 12:38:14
 * @link http://www.appxq.com/
 * @example 
 */
class PatientQuery extends EzfQuery {

    public static function getProviceByTumbon($tumbon_code) {
        $sql = "SELECT const_district.DISTRICT_CODE,const_district.DISTRICT_NAME,const_amphur.AMPHUR_CODE,
                const_amphur.AMPHUR_NAME,const_province.PROVINCE_CODE,const_province.PROVINCE_NAME
                FROM const_district
                INNER JOIN const_province ON const_district.PROVINCE_ID = const_province.PROVINCE_ID
                INNER JOIN const_amphur ON const_district.AMPHUR_ID = const_amphur.AMPHUR_ID
                WHERE const_district.DISTRICT_CODE=:tumbon_code";
        return Yii::$app->db->createCommand($sql, [':tumbon_code' => $tumbon_code])->queryOne();
    }

    public static function getMaxBmiByPtid($ptid) {
        $sql = "SELECT bmi_bw,bmi_ht FROM zdata_bmi WHERE ptid = :ptid ORDER BY id DESC LIMIT 1";
        return Yii::$app->db->createCommand($sql, [':ptid' => $ptid])->queryOne();
    }

    public static function getMaxVitalSignByPtid($ptid) {
        $sql = "SELECT vs_bp_squeeze,vs_bp_loosen,vs_pulse FROM zdata_vs WHERE ptid = :ptid ORDER BY id DESC LIMIT 1";
        return Yii::$app->db->createCommand($sql, [':ptid' => $ptid])->queryOne();
    }

    public static function gethistoryByPtid($ptid) {
        $sql = "SELECT pt_disease_status,pt_or_status FROM zdata_patienthistory WHERE ptid = :ptid ORDER BY id DESC LIMIT 1";
        return Yii::$app->db->createCommand($sql, [':ptid' => $ptid])->queryOne();
    }

    public static function getCerReportByCerid($cer_id) {
        $sql = "SELECT
`zdata_certificate_doctor`.`cer_disease`,
`zdata_certificate_doctor`.`cer_comment`,
`zdata_certificate_doctor`.`cer_date`,
`zdata_certificate_doctor`.`cer_pt_id`,
`zdata_certificate_doctor`.`var_11_other_2`,
`zdata_certificate_doctor`.`cer_diseases`,
`zdata_certificate_doctor`.`var_12_other_2`,
`zdata_certificate_doctor`.`cer_accident`,
`zdata_certificate_doctor`.`var_13_other_2`,
`zdata_certificate_doctor`.`cer_everbeentreated`,
`zdata_certificate_doctor`.`cer_history`,
`zdata_certificate_doctor`.`cer_weight`,
`zdata_certificate_doctor`.`cer_height`,
`zdata_certificate_doctor`.`cer_pressure`,
`zdata_certificate_doctor`.`cer_pulse`,
`zdata_certificate_doctor`.`var_19_other_2`,
`zdata_certificate_doctor`.`cer_physicalcondition`,
`zdata_certificate_doctor`.`ptid`,
zdata_prefix.prefix_name_cid AS prefix_name,
zdata_patientprofile.pt_firstname,
zdata_patientprofile.pt_lastname,
CONCAT('เลขที่ ',zdata_patientprofile.pt_address,' ตรอก/ ชอย - ถนน - หมู่ที่ ',zdata_patientprofile.pt_moi,' ต.'
,const_district.DISTRICT_NAME,'อ.',const_amphur.AMPHUR_NAME,'จ.',const_province.PROVINCE_NAME
,'รหัสไปรษณีย์ ',zdata_patientprofile.pt_addr_zipcode) AS FULL_ADDRESS,
zdata_patientprofile.pt_phone2,
zdata_patientprofile.pt_cid,CONCAT(title,pf.firstname,' ',pf.lastname) AS doc_fullname,
pf.certificate AS certificate_id,pf.certificate
FROM
`zdata_certificate_doctor`
INNER JOIN zdata_patientprofile ON(zdata_certificate_doctor.ptid = zdata_patientprofile.ptid)
LEFT JOIN zdata_prefix ON(zdata_patientprofile.pt_prefix_id = zdata_prefix.prefix_id)
LEFT JOIN const_province ON(zdata_patientprofile.pt_addr_province = const_province.PROVINCE_CODE)
LEFT JOIN const_amphur ON(zdata_patientprofile.pt_addr_amphur = const_amphur.AMPHUR_CODE)
LEFT JOIN const_district ON(zdata_patientprofile.pt_addr_tumbon = const_district.DISTRICT_CODE)
LEFT JOIN `profile` pf ON(pf.user_id= zdata_certificate_doctor.cer_doctor)
WHERE zdata_certificate_doctor.id = :cer_id ";
        return Yii::$app->db->createCommand($sql, [':cer_id' => $cer_id])->queryOne();
    }

    public static function updateZdata_app_conf($date, $datesold) {
        $sql = "UPDATE zdata_app_conf zac,(SELECT (app_conf_qty + 1) AS app_conf_qtys FROM zdata_app_conf WHERE app_conf_date=:datesold) AS pp  SET zac.app_conf_qty = pp.app_conf_qtys WHERE zac.app_conf_date=:datesold";
        Yii::$app->db->createCommand($sql, [':datesold' => $datesold])->execute();
        $sql = "UPDATE zdata_app_conf zac,(SELECT (app_conf_qty - 1) AS app_conf_qtys FROM zdata_app_conf WHERE app_conf_date=:dates) AS pp  SET zac.app_conf_qty = pp.app_conf_qtys WHERE zac.app_conf_date=:dates";
        return Yii::$app->db->createCommand($sql, [':dates' => $date])->execute();
    }

    public static function getreportlistnamepep($date, $group_type, $status) {
        $sql = "SELECT visit_id,pt_hn,pt_firstname,group_concat(order_name ORDER BY order_name) AS order_name FROM(
SELECT zot.order_tran_visit_id AS visit_id,pt_hn,pt_firstname,order_name
                FROM zdata_order_tran zot
                INNER JOIN zdata_visit zv ON(zv.id=zot.order_tran_visit_id)
                INNER JOIN zdata_order_lists co ON(co.order_code=zot.order_tran_code)
                INNER JOIN dept_sect ds ON(ds.sect_code=zot.order_tran_dept)
                INNER JOIN zdata_patientprofile AS vpp ON(vpp.pt_id=zot.ptid)
                INNER JOIN zdata_patientright AS zpr ON(zpr.id=(SELECT MAX(id) FROM zdata_patientright WHERE rstat='1' AND right_pt_id=zot.ptid))
                WHERE zot.rstat='1' AND group_type=:group_type AND order_tran_status=:status AND DATE(zv.visit_date) = :dates                
                ) AS PP
GROUP BY visit_id,pt_hn,fullname ORDER BY order_name";

        return Yii::$app->db->createCommand($sql, [':dates' => $date, ':group_type' => $group_type, ':status' => $status])->queryAll();
    }

    public static function updateVisitdateBytarget($target, $dates) {
        $dates = $dates . ' ' . date('H:i:s');
        $sql = "UPDATE zdata_visit SET visit_date = :dates WHERE id=:target";
        return Yii::$app->db->createCommand($sql, [':dates' => $dates, ':target' => $target])->execute();
    }

    public static function getVisitDate($ptid) {
        $sql = "SELECT ptid,target,visit_date FROM zdata_visit
                WHERE visit_date > NOW() AND ptid = :ptid";
        return Yii::$app->db->createCommand($sql, [':ptid' => $ptid])->queryOne();
    }

    public static function getVisitByDate($ptid, $visit_date) {
        $sql = "SELECT * FROM zdata_visit
                WHERE target=:ptid AND DATE(visit_date) = :visit_date";
        return Yii::$app->db->createCommand($sql, [':ptid' => $ptid, ':visit_date' => $visit_date])->queryOne();
    }

    public static function getPtWarning($ptid) {
        $sql = "SELECT * FROM zdata_warning
                WHERE rstat='1' AND target=:ptid ORDER BY update_date DESC LIMIT 1";
        return Yii::$app->db->createCommand($sql, [':ptid' => $ptid])->queryOne();
    }

    public static function getCashierCounterItemSum($visit_id) {
        $sql = "SELECT CASE WHEN (zpr.right_code = 'CASH') THEN SUM(zot.order_tran_pay)+SUM(zot.order_tran_notpay) WHEN (zpr.right_code = 'ORI') THEN SUM(zot.order_tran_pay)+SUM(zot.order_tran_notpay) WHEN (zpr.right_code = 'ORI-G') THEN SUM(zot.order_tran_pay)+SUM(zot.order_tran_notpay) ELSE SUM(zot.order_tran_pay) END  AS pay
,CASE WHEN (zpr.right_code = 'CASH') THEN '0' WHEN (zpr.right_code = 'ORI') THEN '0' WHEN (zpr.right_code = 'ORI-G') THEN '0'  ELSE SUM(zot.order_tran_notpay) END AS notpay
                FROM zdata_order_tran zot
                INNER JOIN vpatient_profile AS vpp ON(vpp.pt_id=zot.my_59ad6ccc47b10)
                INNER JOIN zdata_patientright AS zpr ON(zpr.id=(SELECT MAX(id) FROM zdata_patientright WHERE rstat='1' AND right_pt_id=zot.ptid))
                INNER JOIN zdata_right zr ON(zr.right_code=zpr.right_code)
                WHERE zot.rstat =1 AND zot.order_tran_status='1' AND order_tran_cashier_status='' AND zot.order_tran_visit_id=:visit_id";
        return Yii::$app->db->createCommand($sql, [':visit_id' => $visit_id])->queryOne();
    }

    public static function getPatientByCid($cid) {
        $nonimg = Yii::getAlias('@storageUrl/images') . '/nouser.png';
        // TODO Check query and ADD Test
        $sql = "SELECT id,pt_firstname,pt_lastname,pt_cid,pt_hn,IFNULL(pt_pic,'$nonimg') AS pt_pic,pt_bdate
                ,pt_address,pt_moi,cd.DISTRICT_NAME,ca.AMPHUR_NAME,cp.PROVINCE_NAME,pt_addr_zipcode
                FROM zdata_patientprofile zp
                LEFT JOIN const_district cd ON(cd.DISTRICT_CODE=zp.pt_addr_tumbon)
                LEFT JOIN const_amphur ca ON(ca.AMPHUR_ID = cd.AMPHUR_ID)
                LEFT JOIN const_province cp ON(cp.PROVINCE_ID = ca.PROVINCE_ID)
                WHERE rstat='1' AND pt_cid = :cid";
        return Yii::$app->db->createCommand($sql, [':cid' => $cid])->queryAll();
    }

    public static function getPatientSearch($q, $sitecode, $concatImg = true) {
        $nonimg = Yii::getAlias('@storageUrl/images') . '/nouser.png';
        $img = '';
        if ($concatImg == true) {
            $img = Yii::getAlias('@storageUrl/ezform/fileinput') . '/';
        }

        // TODO Check query and ADD Test
        $sql = "SELECT id,pt_firstname,pt_lastname,pt_cid,pt_hn,IFNULL(CONCAT('$img',pt_pic),'$nonimg') AS pt_pic,pt_bdate
                ,pt_address,pt_moi,cd.DISTRICT_NAME,ca.AMPHUR_NAME,cp.PROVINCE_NAME,pt_addr_zipcode
                FROM zdata_patientprofile zp
                LEFT JOIN const_district cd ON(cd.DISTRICT_CODE=zp.pt_addr_tumbon)
                INNER JOIN const_amphur ca ON(ca.AMPHUR_ID = cd.AMPHUR_ID)
                INNER JOIN const_province cp ON(cp.PROVINCE_ID = ca.PROVINCE_ID)
                WHERE rstat='1' AND (CONCAT(pt_firstname,' ',pt_firstname,ISNULL(pt_hn),pt_cid) LIKE :q)";
        // AND zp.xsourcex=:xsourcex
        return Yii::$app->db->createCommand($sql, [':q' => "%$q%"])->queryAll();
    }

    public static function getRefFieldByEzfid($ezf_id) {
        $sql = "SELECT `ezform`.`ezf_table`,`ezform_fields`.`ezf_field_name`,`ezform_fields`.`ref_form`
                FROM `ezform`
                LEFT JOIN `ezform_fields` ON `ezform`.`ezf_id` = `ezform_fields`.`ezf_id`
                WHERE `ezform_fields`.`ezf_id` = :ezf_id AND `ezform_fields`.`ref_form`<>''";

        return Yii::$app->db->createCommand($sql, [':ezf_id' => $ezf_id])->queryOne();
    }

    public static function getHospitalGroupItem() {
        $sql = "SELECT order_type_code,order_type_name
                FROM const_order_type
                WHERE order_type_status='1'";
        return Yii::$app->db->createCommand($sql)->queryAll();
    }

    public static function getPtAdmit($dept, $bed_status, $admit_status, $bed_type) {
        $joinStr = '';
        $whereStr = '';
        $param = [':dept' => "$dept", ':bed_status' => $bed_status];
        if($bed_type!=''){
            $joinStr = 'INNER JOIN zdata_ward_bed zwb ON (zwb.id=zbt.bed_tran_bed_id)';
            $whereStr = '  AND zwb.bed_type=:bed_type ';
           $param[':bed_type'] = $bed_type;
        }
        
        $sql = "SELECT 
            za.ptid,
            za.id AS admit_id,
            zbt.bed_tran_bed_id AS bed_id,
            zpp.pt_hn,
            za.admit_visit_id AS visit_id,
            zbt.id AS bed_tran_id,
            zwu.unit_name AS admit_from_dept
, CONCAT(pf.title,pf.firstname,' ',pf.lastname) AS admit_doctor_name
,za.admit_visit_id AS visit_id,pt_hn,za.admit_an
,CONCAT(prefix_name_cid,pt_firstname,' ',pt_lastname) AS fullname,right_name 
FROM zdata_admit za
        INNER JOIN zdata_bed_tran zbt ON(zbt.bed_tran_admit_id=za.id)
        INNER JOIN zdata_working_unit zwu ON(zwu.id=zbt.bed_tran_dept)
        INNER JOIN `profile` pf ON (pf.user_id=za.admit_doctor_user)
	INNER JOIN zdata_patientprofile zpp ON (zpp.id=za.ptid)
        INNER JOIN zdata_prefix zp ON (zp.prefix_id=zpp.pt_prefix_id )
	left JOIN zdata_patientright zpr ON (zpr.right_visit_id=za.admit_visit_id)
        left JOIN zdata_right zr ON (zr.right_code=zpr.right_code)        
        $joinStr
        WHERE zbt.rstat='1' AND za.rstat='1'
	AND zbt.bed_tran_dept=:dept AND zbt.bed_tran_status=:bed_status AND za.admit_status IN($admit_status) $whereStr";
        
        return Yii::$app->db->createCommand($sql, $param)->queryAll();
    }

    public static function getOrderCounterItemReport($visit_id, $order_code = null) {
        $paramsStr = "AND order_visit_id = :visit_id";
        $paramsArry [':visit_id'] = $visit_id;
        if ($order_code) {
            $paramsStr .= " AND zot.order_tran_code=:order_code";
            $paramsArry[':order_code'] = $order_code;
        }

        $sql = "SELECT zx.id,zx.report_xray_result AS result,zx.report_xray_date,col.order_name AS xray_item_des
        ,CONCAT(title,pf.firstname,' ',pf.lastname) AS doc_fullname
        FROM zdata_order_header oh
	INNER JOIN zdata_order_tran zot ON(zot.order_header_id=oh.id)
        INNER JOIN zdata_reportxray zx ON(zx.target = zot.id) 
        INNER JOIN zdata_order_lists col ON(col.order_code=zot.order_tran_code) 
        LEFT JOIN `profile` pf ON(pf.user_id= zx.report_xray_docid) 
        WHERE zot.rstat =1 AND zx.report_status = '2' $paramsStr";
        return Yii::$app->db->createCommand($sql, $paramsArry)->queryAll();
    }
    
        public static function getPatient2($ptid) {
        $sql = "SELECT vp.*,CONCAT(zp.prefix_name,vp.pt_firstname,' ',vp.pt_lastname) AS fullname,YEAR(DATE(CURDATE()))-YEAR(DATE(pt_bdate)) AS ageInYears,vf.PROVINCE_NAME,vf.AMPHUR_NAME
             ,vf.DISTRICT_NAME,zron.religion_name,zocp.occ_name,zrr.right_name,znl.national_name,znol.national_name AS origin_name
             FROM zdata_patientprofile vp
             LEFT JOIN vaddress_full vf ON(vp.pt_addr_province = vf.PROVINCE_CODE 
             AND vp.pt_addr_amphur = vf.AMPHUR_CODE AND vp.pt_addr_tumbon = vf.DISTRICT_CODE)
             LEFT JOIN zdata_prefix zp ON(vp.pt_prefix_id = zp.prefix_id)
             LEFT JOIN zdata_religion zron ON(vp.pt_religion_id = zron.religion_id)
             LEFT JOIN zdata_occupation zocp ON(vp.pt_occ = zocp.occ_id)
             LEFT JOIN zdata_patientright AS zpr ON(zpr.id=(SELECT MAX(id) FROM zdata_patientright WHERE rstat='1' AND right_visit_id=vp.ptid))
             LEFT JOIN zdata_right zrr ON(zpr.right_code = zrr.right_code)
             LEFT JOIN zdata_national znl ON(vp.pt_national_id = znl.national_id)
             LEFT JOIN zdata_national znol ON(vp.pt_origin_id =  znol.national_id)
             where vp.ptid = :ptid";

        return Yii::$app->db->createCommand($sql, [':ptid' => $ptid])->queryOne();
    }

    public static function getLabReportByHeaderHn($hn, $date) {
        $sql = "SELECT lhr.header_ln,lhr.header_hn,lhr.header_fullname,CASE lhr.header_sex WHEN 'M' THEN 'ชาย' ELSE 'หญิง' END AS sex"
                . ",truncate(datediff(curdate(),lhr.header_birthdate)/365.25,0) AS age"
                . ",dept_name,DATE_FORMAT(header_approved_date,'%d/%m/%Y %H:%i') AS app_date"
                . ",DATE_FORMAT(header_receive_date,'%d/%m/%Y %H:%i') AS receive_date,lr.test_code,ls.secname"
                . ",lr.test_name,lr.result,lr.unit,lr.comment,lr.normal_range,lhr.header_comment,lr.commt_all"
                . ",CONCAT(lth1.tech_fullname,' ( ท.น.',lth1.tech_no,' )') AS reported_by"
                . ",CONCAT(lth2.tech_fullname,' ( ท.น.',lth2.tech_no,' )') AS approved_by"
                . " FROM lab_header_result lhr"
                . " INNER JOIN lab_result lr ON(lr.ln=lhr.header_ln)"
                . " LEFT OUTER JOIN lab_test lt ON(lt.testcode=lr.test_code)"
                . " LEFT OUTER JOIN lab_setsection ls ON(ls.seccode=lt.seccode)"
                . " LEFT OUTER JOIN lab_tech lth1 ON(lth1.tech_user=lr.reported_by)"
                . " LEFT OUTER JOIN lab_tech lth2 ON(lth2.tech_user=lr.approved_by)"
                . " LEFT OUTER JOIN login_dept ld ON(ld.dept_id=header_dept)"
                . " WHERE header_hn = :hn /*AND test_code <> '3060'*/ AND DATE_FORMAT(header_receive_date,'%Y-%m-%d') = :date";
        $sql .= " GROUP BY lhr.header_ln,lhr.header_hn,lhr.header_fullname,lhr.header_sex"
                . ",lhr.header_birthdate,dept_name,header_approved_date,header_receive_date,lr.test_code,ls.secname,lr.test_name,lr.result,lr.unit"
                . ",lr.comment,lr.normal_range,lhr.header_comment,lth1.tech_fullname,lth1.tech_no,lth2.tech_fullname,lth2.tech_no,lr.commt_all"
                . " ORDER BY ls.secname";
        
        return Yii::$app->db_chemo->createCommand($sql, [':hn' => $hn, ':date' => $date])->queryAll();
    }

    public static function getUserByAssignment($item_name, $q) {
        $sql = "SELECT pro.user_id,pro.certificate
            ,IFNULL(CONCAT(title,firstname, ' ', lastname),pro.`name`) AS fullname
            FROM profile pro 
            INNER JOIN auth_assignment aa ON pro.user_id=aa.user_id 
            WHERE aa.item_name=:item_name AND CONCAT(certificate,firstname, ' ', lastname,pro.user_id) LIKE :q";


        return Yii::$app->db->createCommand($sql, [':item_name' => $item_name, ':q' => "%$q%"])->queryAll();
    }


    public static function getWorkingUnitById($id) {
        $sql = "SELECT id AS unit_id,unit_code,unit_name,unit_code_old
                FROM zdata_working_unit
                WHERE rstat='1' AND id=:id";
        return Yii::$app->db->createCommand($sql, [':id' => $id])->queryOne();
    }

    public static function getWorkingUnit($unit_id) {
        $sql = "SELECT id AS unit_id,unit_code,unit_name,unit_code_old
                FROM zdata_working_unit
                WHERE rstat='1' AND id=:unit_id";
        return Yii::$app->db->createCommand($sql, [':unit_id' => $unit_id])->queryOne();
    }

    public static function getOrderCounterItem($group_type, $order_status, $visit_id) {
        $sql = "SELECT zot.id,co.order_code,co.order_name,cog.order_group_name,(SELECT MAX(order_vender_no) FROM zdata_order_tran WHERE order_tran_visit_id=zot.order_tran_visit_id) AS order_vender_no,
                order_vender_status,order_tran_dept,ptid,order_tran_visit_id AS visit_id,co.ezf_id,order_qty,external_flag
                ,CONCAT(title,pf.firstname,' ',pf.lastname) AS doc_fullname,order_tran_doctor
                FROM zdata_order_tran zot
                INNER JOIN const_order co ON(co.order_code=zot.order_tran_code)
                INNER JOIN const_order_group cog ON(cog.order_group_code=co.group_code)
                LEFT JOIN `profile` pf ON(pf.user_id=zot.order_tran_doctor) 
                WHERE zot.rstat =1 AND co.group_type=:group_type AND zot.order_tran_visit_id=:visit_id
                AND zot.order_tran_status=:order_status
                ORDER BY cog.order_group_orderby ASC,zot.create_date DESC";
        return Yii::$app->db->createCommand($sql, [':group_type' => $group_type, ':order_status' => $order_status
                    , ':visit_id' => $visit_id])->queryAll();
    }

    public static function getOrderOutlabCounterItem($order_status, $visit_id) {
        if ($order_status == '1') {
            $paramsStr = " AND zle.external_result_status IS NULL";
        } else {
            $paramsStr = " AND zle.external_result_status IS NOT NULL";
        }

        $sql = "SELECT zot.id,co.order_code,co.order_name,cog.order_group_name,(SELECT MAX(order_vender_no) FROM zdata_order_tran WHERE order_tran_visit_id=zot.order_tran_visit_id) AS order_vender_no,
                order_vender_status,order_tran_dept,zot.ptid,order_tran_visit_id AS visit_id,co.ezf_id,order_qty,external_flag
                FROM zdata_order_tran zot
                INNER JOIN const_order co ON(co.order_code=zot.order_tran_code)
                INNER JOIN const_order_group cog ON(cog.order_group_code=co.group_code)
                LEFT JOIN zdata_lab_external zle ON(zle.external_order_id=zot.id AND zle.rstat='1')
                WHERE zot.rstat =1 AND co.external_flag=:external_flag AND zot.order_tran_visit_id=:visit_id
                $paramsStr";
        return Yii::$app->db->createCommand($sql, [':external_flag' => 'Y', ':visit_id' => $visit_id])->queryAll();
    }

    public static function getOrderCounterItemCyto($order_status, $visit_id) {
        $sql = "SELECT CASE WHEN zcr.id IS NOT NULL THEN zcr.id ELSE zot.id END AS id,order_qty
                ,col.order_code,col.order_name,zog.order_group_name,order_tran_dept
                ,zot.ptid,order_visit_id AS visit_id,col.order_ezf_id AS ezf_id,zcr.report_status
                FROM zdata_order_header oh
		INNER JOIN zdata_order_tran zot ON(zot.order_header_id=oh.id)
                INNER JOIN zdata_order_lists col ON(col.order_code=zot.order_tran_code) 
                INNER JOIN zdata_order_group zog ON(zog.id=col.group_code)
                INNER JOIN zdata_order_type zoty ON(zoty.id=col.group_type)
                LEFT JOIN zdata_reportcyto zcr ON(zcr.order_tran_id=zot.id AND report_status=:report_status)
                WHERE zot.rstat =1 AND zoty.order_type_code='C' AND oh.order_visit_id=:visit_id
                ";        
        return Yii::$app->db->createCommand($sql, [':report_status' => $order_status
                    , ':visit_id' => $visit_id])->queryAll();
    }

    public static function getOrderReportEkg($order_status, $visit_id) {
        $sql = "SELECT zrk.id,order_name
                FROM zdata_order_header oh
		INNER JOIN zdata_order_tran zot ON(zot.order_header_id=oh.id)
                INNER JOIN zdata_order_lists col ON(col.order_code=zot.order_tran_code) 
		INNER JOIN zdata_reportekg zrk ON(zrk.order_tran_id=zot.id)
                WHERE zot.rstat =1 AND zrk.report_status=:report_status AND oh.order_visit_id=:visit_id
                ";
        return Yii::$app->db->createCommand($sql, [':report_status' => $order_status
                    , ':visit_id' => $visit_id])->queryAll();
    }

    public static function getCashierItem($visit_id, $fin_code) {

        $sql = "SELECT zot.id,co.sks_code,co.order_name,
            CASE WHEN (zpr.right_code = 'CASH') THEN zot.order_tran_pay + zot.order_tran_notpay ELSE zot.order_tran_pay END AS pay,
CASE WHEN (zpr.right_code = 'CASH') THEN 0 ELSE zot.order_tran_notpay END AS notpay 
                ,zot.ptid,order_tran_visit_id AS visit_id,co.order_code,order_tran_status,'ORDER' AS type
                FROM zdata_order_tran zot
                INNER JOIN const_order co ON(co.order_code=zot.order_tran_code)
                INNER JOIN vpatient_profile AS vpp ON(vpp.pt_id=zot.my_59ad6ccc47b10)
		INNER JOIN zdata_patientright AS zpr ON(zpr.id=(SELECT MAX(id) FROM zdata_patientright WHERE rstat='1' AND right_pt_id=zot.ptid))
		INNER JOIN zdata_right zr ON(zr.right_code=zpr.right_code)
                INNER JOIN const_order_finname cof ON(cof.fin_item_code=co.fin_item_code)
                WHERE zot.rstat =1 AND zot.order_tran_visit_id=:visit_id AND cof.fin_item_code=:fin_code
                AND order_tran_cashier_status = ''";

        return Yii::$app->db->createCommand($sql, [':visit_id' => $visit_id, ':fin_code' => $fin_code])->queryAll();
    }

    public static function getCashierItemDrug($visit_id, $fin_code) {
        $sql = "SELECT zpot.id,'' AS sks_code,fin_item_name AS order_name
                ,CASE WHEN (zpr.right_code =  'CASH') THEN SUM(zpot.order_tran_pay + zpot.order_tran_notpay) ELSE zpot.order_tran_pay END AS pay
                ,CASE WHEN (zpr.right_code =  'CASH') THEN 0 ELSE SUM(zpot.order_tran_notpay) END AS notpay 
                ,zpo.order_visit_id AS visit_id,fin_item_code AS order_code,'DURG' AS type,zpo.id AS order_tran_id,order_tran_status
                FROM zdata_pis_order2 zpo 
                INNER JOIN zdata_pis_order_tran2 zpot ON(zpo.id=zpot.order_id) 
                INNER JOIN zdata_pism_item2 zpi ON(zpi.id=zpot.order_item_id)  
                INNER JOIN const_order_finname zof ON(zof.fin_item_code=zpi.item_fin_code)
                INNER JOIN vpatient_profile AS vpp ON(vpp.pt_id=zpo.ptid) 
                INNER JOIN zdata_patientright AS zpr ON(zpr.id=(SELECT MAX(id) FROM zdata_patientright WHERE rstat='1' AND right_pt_id=zpo.ptid)) 
                INNER JOIN zdata_right zr ON(zr.right_code=zpr.right_code) 
                WHERE zpo.rstat= '1 ' AND zpot.rstat= '1 ' AND zpot.order_tran_status= '2' 
                AND zpo.order_visit_id=:visit_id AND zpi.item_fin_code=:fin_code
                AND zpot.order_tran_cashier_status = ''";

        return Yii::$app->db->createCommand($sql, [':visit_id' => $visit_id, ':fin_code' => $fin_code])->queryAll();
    }

    public static function getCashierItemDrugToReceipt($visit_id, $fin_code, $cashier_status) {
        $sql = "SELECT zpot.id AS order_tran_id,order_tran_status,zpi.trad_itemname,cof.order_fin_name
                FROM zdata_pis_order zpo 
                INNER JOIN zdata_pis_order_tran zpot ON(zpo.id=zpot.order_id) 
                INNER JOIN zdata_pism_item zpi ON(zpi.id=zpot.order_trad_id)  
                INNER JOIN zdata_order_fin_group cof ON(cof.id=zpi.trad_fin_gruop_id)
                WHERE zpo.rstat= '1' AND zpot.rstat= '1' AND zpot.order_tran_status=:order_tran_status
                AND zpo.order_visit_id=:visit_id AND cof.order_fin_code=:fin_code
                AND zpot.order_tran_cashier_status = :cashier_status";

        return Yii::$app->db->createCommand($sql, [':visit_id' => $visit_id, ':fin_code' => $fin_code
                    , ':order_tran_status' => '2',':cashier_status'=>$cashier_status])->queryAll(); //order_tran_status fix เป็น 1 ไปก่อนรอ counter pis
    }

    public static function getCashierCancel($visit_id) {
        $sql = "SELECT fullname,right_name
                FROM zdata_order_tran zot
                INNER JOIN vpatient_profile AS vpp ON(vpp.pt_id=zot.my_59ad6ccc47b10)
                INNER JOIN zdata_patientright AS zpr ON(zpr.id=(SELECT MAX(id) FROM zdata_patientright WHERE rstat='1' AND right_pt_id=zot.ptid))
                INNER JOIN zdata_right zr ON(zr.right_code=zpr.right_code)
                WHERE zot.rstat =1 AND zot.order_tran_visit_id=:visit_id
                LIMIT 1";
        return Yii::$app->db->createCommand($sql, [':visit_id' => $visit_id])->queryAll();
    }

    public static function getAdmitDashboard($dept) {
        $sql = "SELECT IFNULL(SUM(IF(admit_status IN('3','2') AND zbt.bed_tran_status='2',1,0)),0) AS Cadmit,
                    IFNULL(SUM(IF(admit_status IN('1','2') AND zbt.bed_tran_status ='1',1,0)),0) AS Cpadmit,
                    IFNULL(SUM(IF(admit_status='3' AND zbt.bed_tran_status='2',1,0)),0) AS Cpdis,
                    IFNULL(SUM(IF(admit_status IN('3','2') AND zbt.bed_tran_status='2' AND zwb.bed_type='1',1,0)),0) AS cp_alert,
                    IFNULL(SUM(IF(admit_status IN('3','2') AND zbt.bed_tran_status='2' AND zwb.bed_type='2',1,0)),0) AS cp_addons
                FROM zdata_admit za
		INNER JOIN zdata_bed_tran zbt ON(zbt.bed_tran_admit_id=za.id)
                INNER JOIN zdata_patientprofile zpp ON (zpp.id=za.ptid)
                INNER JOIN zdata_prefix zp ON (zp.prefix_id=zpp.pt_prefix_id )
                left JOIN zdata_ward_bed zwb ON(zwb.id=zbt.bed_tran_bed_id)
                WHERE zbt.rstat='1' AND za.rstat='1' AND zbt.bed_tran_status <> '3'
		AND zbt.bed_tran_dept=:dept AND admit_status IN('1','2','3') ";
        return Yii::$app->db->createCommand($sql, [':dept' => $dept])->queryOne();
    }

    public static function getBedTran($target) {
        $sql = "SELECT zbt.id,CONCAT(zwb.bed_code,' ',zwb.bed_name) AS bed_name,zwu.unit_name AS sect_name
            ,zbt.my_59b8a3b35fc7f AS visit_id
                ,zbt.bed_tran_status,zbt.create_date
                FROM zdata_bed_tran zbt
                LEFT JOIN zdata_ward_bed zwb ON(zwb.id=zbt.bed_tran_bed_id)
                INNER JOIN  zdata_working_unit zwu ON(zwu.id=zbt.bed_tran_dept)
                WHERE zbt.rstat=1 AND zbt.target=:target
                ORDER BY create_date DESC";
        return Yii::$app->db->createCommand($sql, [':target' => $target])->queryAll();
    }

//รอลบ
    public static function getAdmitBtnAdt($target) {
        $sql = "SELECT za.id AS admit_id,za.admit_an,zbt.id AS bed_tran_id,za.admit_status,zbt.bed_tran_status,zbt.update_date
                FROM zdata_admit za
                INNER JOIN zdata_bed_tran zbt ON(zbt.bed_tran_admit_id=za.id)
                WHERE za.rstat = '1' AND zbt.rstat = '1' AND za.target=:target
                ORDER BY zbt.update_date DESC";
        return Yii::$app->db->createCommand($sql, [':target' => $target])->queryOne();
    }

    public static function getAdmitCpoe($visit_id) {
        $sql = "SELECT zbt.bed_tran_bed_id,za.admit_visit_id AS visit_id,za.id AS admit_id,zpp.id AS pt_id,pt_bdate,admit_date,admit_an
                ,pt_hn,pt_pic
                ,CONCAT(prefix_name_cid,pt_firstname,' ',pt_lastname) AS fullname
                ,CONCAT(zwb.bed_code,' : ',zwb.bed_name) AS bed,zwu.unit_name AS sect_name
                ,za.admit_status,zbt.bed_tran_status,DATEDIFF(NOW(),admit_date) AS admit_amount
                FROM zdata_admit za
                INNER JOIN zdata_bed_tran zbt ON(za.id=zbt.bed_tran_admit_id)
                LEFT JOIN zdata_ward_bed zwb ON(zwb.id=zbt.bed_tran_bed_id) 
                LEFT JOIN zdata_working_unit zwu ON(zwu.id=zwb.bed_dept)
                
                INNER JOIN zdata_patientprofile zpp ON(zpp.id=za.ptid)
                INNER JOIN zdata_prefix zpf ON(zpf.prefix_id=zpp.pt_prefix_id)
                
                WHERE za.rstat='1' AND zbt.rstat='1'
                AND za.admit_visit_id = :visit_id";
        $result = Yii::$app->db->createCommand($sql, [':visit_id' => $visit_id])->queryOne();

        return $result;
    }

    public static function getBedTranID($target, $bed_status) {
        $sql = "SELECT zbt.id AS bed_tran_id
                FROM zdata_bed_tran zbt
                WHERE zbt.rstat='1' AND zbt.bed_tran_status=:bed_status AND zbt.bed_tran_admit_id=:target";
        return Yii::$app->db->createCommand($sql, [':target' => $target, ':bed_status' => $bed_status])->queryOne();
    }

    public static function getOrderTranPt($target, $order_tran_status) {
        $sql = "SELECT order_tran_code,order_tran_status,order_vender_no FROM zdata_order_tran zot
            WHERE zot.rstat =1 AND zot.order_tran_visit_id=:target AND zot.order_tran_status=:order_tran_status";
        return Yii::$app->db->createCommand($sql, [':target' => $target
                    , ':order_tran_status' => $order_tran_status])->queryAll();
    }

    public static function getOrderGroupCounter($target, $order_tran_status) {
        $sql = "SELECT DISTINCT ds.order_type_code
                FROM zdata_order_header zoh
                INNER JOIN  zdata_order_tran zot ON(zoh.id = zot.order_header_id)
                INNER JOIN zdata_order_lists co ON(co.order_code=zot.order_tran_code)
                INNER JOIN zdata_order_type ds ON(ds.id = co.group_type)
                WHERE zot.rstat =1 AND zoh.order_visit_id = :target AND zot.order_tran_status=:order_tran_status
                ORDER BY ds.order_type_code DESC
                ";
        return Yii::$app->db->createCommand($sql, [':target' => $target
                    , ':order_tran_status' => $order_tran_status])->queryAll();
    }

    public static function getOrderFinGroup($visit_id) {
        $sql = "SELECT zoft.fin_group_code,zoft.fin_group_name,SUM(order_tran_notpay) AS group_notpay
            ,SUM(order_tran_pay) AS group_pay
                FROM zdata_order_header zoh
		INNER JOIN zdata_order_tran zot ON(zot.order_header_id=zoh.id)
		INNER JOIN zdata_order_lists zol ON(zol.order_code=zot.order_tran_code)
		INNER JOIN zdata_order_fin_group zfg ON(zfg.id=zol.fin_item_code)
		INNER JOIN zdata_order_fin_type zoft ON(zoft.id=zfg.order_fin_type_id)
                WHERE zot.rstat =1 AND zoh.order_visit_id=:visit_id 
                GROUP BY fin_group_code
                ORDER BY fin_group_code;";
        return Yii::$app->db->createCommand($sql, [':visit_id' => $visit_id])->queryAll();
    }

    public static function getOrderFinGroupDetail($visit_id, $group_code) {
        $sql = "SELECT co.order_code,co.order_name,order_tran_notpay AS item_notpay,order_tran_pay AS item_pay
                ,DATE(create_date) AS order_date
                FROM zdata_order_tran zot
                INNER JOIN const_order co ON(co.order_code=zot.order_tran_code)
                INNER JOIN const_order_finname cof ON(cof.fin_item_code=co.fin_item_code)
                WHERE zot.rstat =1 AND zot.order_tran_visit_id=:visit_id AND cof.fin_item_group_code=:group_code
                ORDER BY create_date";
        return Yii::$app->db->createCommand($sql, [':visit_id' => $visit_id
                    , ':group_code' => $group_code])->queryAll();
    }

    public static function getProfileuserByid($user_id) {
        $sql = "SELECT title,firstname,lastname FROM profile
                WHERE user_id=:user_id";
        return Yii::$app->db->createCommand($sql, [':user_id' => $user_id])->queryOne();
    }
    
    public static function getProfilePatientByid($ptid) {
        $sql = "SELECT * FROM zdata_patientprofile
                WHERE ptid=:ptid";
        return Yii::$app->db->createCommand($sql, [':ptid' => $ptid])->queryOne();
    }

    public static function getAdviceDetail($advice_id) {
        $sql = "SELECT advice_detail 
            FROM zdata_advice
            WHERE rstat='1' AND id=:advice_id";
        return Yii::$app->db->createCommand($sql, [':advice_id' => $advice_id])->queryOne();
    }

    public static function getIcd10Fulltxt($code) {
        $sql = "SELECT CONCAT(code,' : ',name) AS icd10fulltxt
            FROM const_icd10
            WHERE code=:code";
        $result = Yii::$app->db->createCommand($sql, [':code' => $code])->queryOne();

        return $result['icd10fulltxt'];
    }

    public static function getDischargeCpoe($visit_id) {
        $sql = "SELECT za.id AS admit_id,za.admit_status,discharge_date,discharge_code,discharge_status,zdc.id AS discharge_id
                ,admit_date,admit_an,vp.pt_hn,vp.fullname,pt_pic,discharge_date,DATEDIFF(discharge_date, admit_date) AS LOS
		,ds.sect_name,vp.pt_bdate,zdc.di_txt,CONCAT(zwb.bed_code,' : ',zwb.bed_name) AS bed
                FROM zdata_admit za
		INNER JOIN zdata_bed_tran zbt ON(za.id=zbt.bed_tran_admit_id AND zbt.bed_tran_status='2')
                LEFT JOIN zdata_ward_bed zwb ON(zwb.id=zbt.bed_tran_bed_id) 
                INNER JOIN zdata_discharge zdc ON(zdc.discharge_visit_id=za.admit_visit_id)
                INNER JOIN vpatient_profile vp ON(vp.pt_id=za.ptid)
		INNER JOIN dept_sect ds ON(ds.sect_code=zdc.discharge_from_dept)
                WHERE za.rstat='1' AND zdc.rstat='1'
                AND za.admit_visit_id = :visit_id";
        $result = Yii::$app->db->createCommand($sql, [':visit_id' => $visit_id])->queryOne();

        return $result;
    }

    public static function getTranfer($tranfer_id) {
        $sql = "SELECT vp.pt_hn,vp.fullname,vp.pt_bdate,vp.pt_sex,pt_moi,pt_address
                ,cd.DISTRICT_NAME,ca.AMPHUR_NAME,cp.PROVINCE_NAME,pt_addr_zipcode
                ,ztf.*,ci.code AS code,ci2.code AS code1,ci.name AS diag1name,ci2.name AS diag2name
                FROM zdata_tranfer ztf
                INNER JOIN vpatient_profile vp ON(vp.pt_id=ztf.ptid)
                LEFT JOIN const_icd10 ci ON(ci.code = ztf.refer_basic_diag1)
                LEFT JOIN const_icd10 ci2 ON(ci2.code = ztf.refer_basic_diag2)
                LEFT JOIN const_district cd ON(cd.DISTRICT_CODE=vp.pt_addr_tumbon)
                LEFT JOIN const_province cp ON (cd.PROVINCE_ID = cp.PROVINCE_ID)
                LEFT JOIN const_amphur ca ON (cd.AMPHUR_ID = ca.AMPHUR_ID)
                WHERE ztf.rstat='1' AND ztf.id = :tranfer_id";

        $result = Yii::$app->db->createCommand($sql, [':tranfer_id' => $tranfer_id])->queryOne();

        return $result;
    }

    public static function getDiagComo($visit_id) {
        $sql = "SELECT di_como_icd10 AS di_icd10_code,ci10.name AS di_icd10_name
                FROM zdata_diag_como
                INNER JOIN const_icd10 ci10 ON(di_como_icd10=ci10.`code`)
                WHERE rstat='1' AND di_como_visit_id=:visit_id";
        $result = Yii::$app->db->createCommand($sql, [':visit_id' => $visit_id])->queryAll();

        return $result;
    }

    public static function getDiagComp($visit_id) {
        $sql = "SELECT di_comp_icd10 AS di_icd10_code,ci10.name AS di_icd10_name
                FROM zdata_diag_comp
                INNER JOIN const_icd10 ci10 ON(di_comp_icd10=ci10.`code`)
                WHERE rstat='1' AND di_comp_visit_id=:visit_id";
        $result = Yii::$app->db->createCommand($sql, [':visit_id' => $visit_id])->queryAll();

        return $result;
    }

    public static function getOperat($visit_id) {
        $sql = "SELECT di_operat_icd9 AS di_icd9_code,ci9.`name` AS di_icd9_name
                FROM zdata_operat 
                INNER JOIN const_icd9 ci9 ON(di_operat_icd9=ci9.`code`)
                WHERE rstat='1' AND di_operat_visit_id=:visit_id";
        $result = Yii::$app->db->createCommand($sql, [':visit_id' => $visit_id])->queryAll();

        return $result;
    }

    public static function getAppointmentByPtid($pt_id){
        $query = Yii::$app->db->createCommand('SELECT * FROM zdata_appoint WHERE ptid = :ptid AND app_date = CURRENT_DATE() and rstat not in (0,3) ORDER BY id ASC',[':ptid' => $pt_id]);
        return $query->queryOne();
    }

    /**
     * @param $pt_id
     * @param $dept
     * @param null $date
     * @return array|bool
     */
    public static function getAppointPt($pt_id, $dept, $date = null) {
        $query = (new Query())->select('zap.id AS app_id,app_dept,app_date,app_time,app_doctor,app_status')
            ->from('zdata_appoint AS zap')
            ->where("zap.rstat='1' AND app_status='1'")
            ->andWhere(['ptid'=>$pt_id]);

        if ($dept) {
            $query->andWhere(['zap.app_dept'=>$dept]);
        }
        if (isset($date)) {
            $query->andWhere(['zap.app_date'=>$date]);
        }
        return $query->one();
    }


    /**
     * @param $visit_id
     * @return array|false
     * @throws \yii\db\Exception
     */
    public static function getRightByVisitId($visit_id) {
        $sql = "SELECT zpr.id AS right_id,zpr.right_code,right_hos_main,right_refer_start,right_refer_end,right_status,right_prove_end
            ,zr.right_name,right_project_id,pt_bdate,right_prove_no
                FROM zdata_patientright zpr
                INNER JOIN zdata_right zr ON(zr.right_code=zpr.right_code)
                INNER JOIN zdata_patientprofile zpp ON(zpp.id=zpr.ptid)
                WHERE right_visit_id=:visit_id";
        return Yii::$app->db->createCommand($sql, [':visit_id' => $visit_id])->queryOne();
    }

    /**
     * @deprecated use getRightByVisitId() instead
     * @param $visit_id
     * @return array|false
     * @throws \yii\db\Exception
     */
    public static function getRightLast($visit_id) {
        $sql = "SELECT zpr.id AS right_id,zpr.right_code,right_hos_main,right_refer_start,right_refer_end,right_status,right_prove_end
            ,zr.right_name,right_project_id,pt_bdate,right_prove_no
                FROM zdata_patientright zpr
                INNER JOIN zdata_right zr ON(zr.right_code=zpr.right_code)
                INNER JOIN zdata_patientprofile zpp ON(zpp.id=zpr.ptid)
                WHERE right_visit_id=:visit_id";

        return Yii::$app->db->createCommand($sql, [':visit_id' => $visit_id])->queryOne();
    }

    public static function getProjectName($project_name) {
        $sql = "SELECT id, project_name AS text FROM zdata_project
                WHERE rstat='1' AND project_name LIKE :project_name";

        return Yii::$app->db->createCommand($sql, [':project_name' => "%$project_name%"])->queryAll();
    }

    public static function getPrefixId($prefix_name) {
        $sql = "SELECT prefix_id,prefix_name,prefix_sex
                FROM zdata_prefix
                WHERE prefix_name_cid=:prefix_name";
        $result = Yii::$app->db->createCommand($sql, [':prefix_name' => $prefix_name])->queryOne();

        return $result;
    }

    public static function getProviceByName($tumbon, $amphur, $province) {
        $sql = "SELECT cd.DISTRICT_CODE,DISTRICT_NAME,AMPHUR_CODE,AMPHUR_NAME,PROVINCE_CODE,PROVINCE_NAME
                ,zipcode
                FROM const_district cd
                INNER JOIN const_amphur ca ON (ca.AMPHUR_ID = cd.AMPHUR_ID)
                INNER JOIN const_province cp ON (cp.PROVINCE_ID = ca.PROVINCE_ID)
                INNER JOIN const_zipcodes cz ON (cz.district_code = cd.DISTRICT_CODE)
                WHERE DISTRICT_NAME = :tumbon AND AMPHUR_NAME = :amphur AND PROVINCE_NAME = :province";

        return Yii::$app->db->createCommand($sql, [':tumbon' => "$tumbon", ':amphur' => "$amphur",
                    ':province' => "$province"])->queryOne();
    }

    public static function getVisit($pt_id, $date) {
        $sql = "SELECT zv.id,zv.id AS visit_id,zv.visit_type,visit_date
                FROM zdata_visit zv
                WHERE zv.visit_pt_id=:pt_id AND DATE(zv.visit_date)=:date
                ";

        return Yii::$app->db->createCommand($sql, [':pt_id' => $pt_id, ':date' => $date])->queryOne();
    }


    /**
     * use Visit_ID form zdata_visit for find destination(location) for patient today
     * @return string
     * @throws \yii\db\Exception
     */
    public static function getDestinationDepartment($visit_id){
        $visitData = PatientQuery::getVisitById($visit_id);
        $ptid = $visitData['ptid'];
        if ($visitData['visit_type'] == '1') {
            $dataVisitTran = PatientQuery::getVisit($ptid, date("Y-m-d"));
            $dataVisitTran['sect_name'] = 'OPD ตรวจสุขภาพ,';

            $data = PatientQuery::getOrderGroupCounter($dataVisitTran['visit_id'], '1');

            $txtCounter = '';
            foreach ($data as $value) {
                $txtCounter .= self::getCounterName($value['order_type_code']);
            }
            //return $txtCounter;
            $sect_name = isset($dataVisitTran['sect_name'])?$dataVisitTran['sect_name']:'';
            return $sect_name.$txtCounter; 
        } else {

            //งานบริการผู้ป่วยนอก ตรวจรักษาโรค 1536740859027234200   $visitData['visit_type'] == 2 1538031598039175900 งานพยาบาลรับ-ส่งต่อผู้ป่วย
            if($visitData['visit_type'] == 3){
                $dept = '1538031598039175900';
                $dataVisitTran = PatientQuery::getVisitTran($ptid, $dept, date("Y-m-d"));
                return $dataVisitTran['sect_name'];
            }else if($visitData['visit_type'] == 2){
                $appointmentArr = PatientQuery::getAppointmentByPtid($ptid);
                if($appointmentArr){
                    $workingUnit = PatientQuery::getWorkingUnitById($appointmentArr['app_dept']);
                    return $workingUnit['unit_name'];
                }
            }
        }
        return '-';
    }

    /**
     * Counter name from order_type_code of table zdata_order_type
     * @param $counter_code
     * @return bool|string
     */
    public static function getCounterName($counter_code)
    {
        $result = "";
        switch ($counter_code) {
            case 'X': //opd checkup
                $result = ' XRAY,';
                break;
            case 'L': //Appointment
                $result = ' LAB,';
                break;
            case 'E': //refer
                $result = ' ตรวจคลื่นหัวใจ(EKG),';
                break;
            case 'C': //ส่งตรวจสอบสิทธิก่อน
                $result = ' ตรวจภายใน,';
                break;
            default:
                return FALSE;
        }

        return $result;
    }


    /**
     * @param $visit_id
     * @return array|false
     * @throws \yii\db\Exception
     */
    public static function getVisitById($visit_id) {
        $sql = "SELECT *
                FROM zdata_visit zv
                WHERE zv.id=:visit_id
                ";

        return Yii::$app->db->createCommand($sql, [':visit_id' =>$visit_id])->queryOne();
    }

    public static function getVisitTran($pt_id, $dept, $date, $tran_status = NUll) {
        $paramsStr = "zv.rstat='1' AND ( zvt.visit_tran_doc_status = '' OR ISNULL(zvt.visit_tran_doc_status) )  AND zv.visit_pt_id=:pt_id AND DATE(zv.visit_date)=:date AND zvt.visit_tran_dept=:dept";
        $paramsArry = [':pt_id' => $pt_id, ':dept' => $dept, ':date' => $date,];
        if ($tran_status) {
            $paramsStr .= ' AND zvt.visit_tran_status=:tran_status';
            $paramsArry[':tran_status'] = "$tran_status";
        }
        if (Yii::$app->user->can('doctor')) {
            $user_id = Yii::$app->user->identity->profile->user_id;
            $paramsStr .= " AND visit_tran_doctor='{$user_id}'";
        } else {
            $paramsStr .= " AND IFNULL(visit_tran_doctor,'')=''";
        }

        $sql = "SELECT zv.id AS visit_id,zv.visit_type,zvt.visit_tran_dept,zvt.visit_tran_doctor,zv.visit_date
            ,zvt.id AS visit_tran_id,wu.unit_name AS sect_name,visit_tran_status,visit_tran_doc_status,visit_tran_close_type
                FROM zdata_visit zv
                INNER JOIN zdata_visit_tran zvt ON(zvt.visit_tran_visit_id=zv.id)
                INNER JOIN zdata_working_unit wu ON(wu.id=zvt.visit_tran_dept) 
                WHERE $paramsStr ORDER BY zvt.update_date DESC";
        return Yii::$app->db->createCommand($sql, $paramsArry)->queryOne();
    }

    public static function getVisitTranDoctor($pt_id, $doctor_id, $date) {
        $sql = "SELECT zv.id AS visit_id,zv.visit_type,zvt.visit_tran_dept,zvt.visit_tran_doctor
            ,zvt.id AS visit_tran_id,ds.sect_name,visit_tran_status,visit_tran_doc_status,visit_tran_close_type
                FROM zdata_visit zv
                INNER JOIN zdata_visit_tran zvt ON(zvt.visit_tran_visit_id=zv.id)
                INNER JOIN dept_sect ds ON(ds.sect_code=zvt.visit_tran_dept)
                WHERE zv.rstat='1' AND zv.visit_pt_id=:pt_id AND DATE(zv.visit_date)=:date
                AND visit_tran_doctor=:doctor_id AND zvt.visit_tran_status='1'
                ORDER BY zvt.update_date DESC";

        return Yii::$app->db->createCommand($sql, [':pt_id' => $pt_id, ':date' => $date, ':doctor_id' => $doctor_id])->queryOne();
    }

    public static function getPackageItem($package_code) {
        $sql = "SELECT package_order_code AS order_code,checkup_flag_pay,full_price,package_qty
                FROM const_package_item cpi
                INNER JOIN const_order co ON(co.order_code=cpi.package_code)
                WHERE cpi.package_code=:package_code ORDER BY package_order_by ASC";

        return Yii::$app->db->createCommand($sql, [':package_code' => $package_code])->queryAll();
    }

    public static function getHisMapLis($item_code, $ln) {
        $sql = 'CALL lab_genLN_test("' . $item_code . '","' . $ln . '");';

        return Yii::$app->db_chemo->createCommand($sql)->queryAll();
    }

    public static function getLabResultOneRecord($hn, $date, $secname, $test_code) {
        $paramsStr = "header_hn=:hn AND DATE_FORMAT(header_receive_date,'%Y-%m-%d') = :date";
        $paramsArry[':hn'] = $hn;
        $paramsArry[':date'] = $date;
        if ($test_code) {
            if ($test_code <> 'Serology') {
                $paramsStr .= " AND test_code=:test_code";
                $paramsArry[':test_code'] = $test_code;
            } else {
                $paramsStr .= " AND test_code IN ('3033','3031','3032','3051','3052','3054')";
            }
        }
        if ($secname) {
            $paramsStr .= " AND secname LIKE :secname";
            $paramsArry[':secname'] = "$secname%";
        }

        $sql = "SELECT DISTINCT lhr.header_ln,DATE_FORMAT(header_approved_date,'%d/%m/%Y') AS app_date
                ,ls.secname,lr.test_name,lr.result,lr.unit,lr.comment,lr.normal_range,lhr.header_comment,lr.commt_all,PrintSticker AS flagshow
                ,lt.testcode,hiscode
                FROM lab_header_result lhr  
                INNER JOIN lab_result lr ON(lr.ln=lhr.header_ln) 
                INNER JOIN lab_test lt ON(lt.testcode=lr.test_code) 
                INNER JOIN lab_setsection ls ON(ls.seccode=lt.seccode)
                LEFT JOIN hismaptest hmt ON(hmt.testcode=lt.testcode)
                WHERE $paramsStr
                ORDER BY ls.secname";

        return Yii::$app->db_chemo->createCommand($sql, $paramsArry)->queryAll();
    }

    public static function getLabResultByLn($ln) {
        $sql = "SELECT DISTINCT lhr.header_ln,DATE_FORMAT(header_approved_date,'%d/%m/%Y') AS app_date
                ,ls.secname,lr.test_name,lr.result,lr.unit,lr.comment,lr.normal_range,lhr.header_comment,lr.commt_all,PrintSticker AS flagshow
                ,lt.testcode
                FROM lab_header_result lhr  
                INNER JOIN lab_result lr ON(lr.ln=lhr.header_ln) 
                INNER JOIN lab_test lt ON(lt.testcode=lr.test_code) 
                INNER JOIN lab_setsection ls ON(ls.seccode=lt.seccode)
                WHERE header_ln=:ln
                ORDER BY ls.secname";

        return Yii::$app->db_chemo->createCommand($sql, [':ln' => $ln])->queryAll();
    }

    public static function getLabResultByHn($hn, $date, $secname) {

        $sql = "CALL lab_result_pro('{$hn}','{$date}','{$secname}%');";

        return Yii::$app->db_chemo->createCommand($sql)->queryAll();
    }

    public static function getAppointConf($dept, $date) {
        $sql = "SELECT id,app_conf_qty - IFNULL(app_conf_qty_pt,0) AS sum_qty,app_conf_date
                FROM zdata_app_conf
                WHERE app_conf_dept=:dept AND app_conf_date BETWEEN :date
                AND DATE_ADD( LAST_DAY(:date), INTERVAL 1 MONTH)";

        return Yii::$app->db->createCommand($sql, [':dept' => $dept, ':date' => $date])->queryAll();
    }

    public static function getOrderByVisit($visit_id) {
        $sql = "SELECT zot.*,zv.id as 'order_visit_id', DATE(zv.visit_date) AS visit_date,zv.visit_type
                FROM zdata_order_tran zot
                INNER JOIN zdata_order_header zoh ON zoh.id=zot.order_header_id
                INNER JOIN zdata_visit zv ON(zv.id = zoh.order_visit_id)
                WHERE zot.rstat NOT IN (0,3) AND zv.id=:visit_id AND zot.order_tran_status <> '1' ORDER BY order_tran_code";

        return Yii::$app->db->createCommand($sql, [':visit_id' => $visit_id])->queryAll();
    }

    public static function getReceiptBookNo() {
        $sql = "SELECT CASE WHEN MAX(CAST(book_num AS UNSIGNED))=100 THEN MAX(CAST(book_no AS UNSIGNED))+1 ELSE MAX(CAST(book_no AS UNSIGNED)) END AS book_no 
                ,CASE WHEN MAX(CAST(book_num AS UNSIGNED))=100 THEN 1 ELSE MAX(CAST(book_num AS UNSIGNED))+1 END AS book_num 
                FROM zdata_receipt_mas 
                WHERE book_no=(SELECT MAX(CAST(book_no AS UNSIGNED)) FROM zdata_receipt_mas WHERE SUBSTR(receipt_no,1,2) = '61')";

        return Yii::$app->db->createCommand($sql)->queryOne();
    }

    public static function getprojectidByptid($pt_id) {
        $date = date('Y-m-d', strtotime('-7 days'));
        $sql = "SELECT zppn.id,zpj.project_id,zpj.project_name,zpj.id AS ms_project_id
                FROM zdata_patientprofile zp
                INNER JOIN zdata_project_patient_name zppn ON(zppn.cid_project = zp.pt_cid)
                INNER JOIN zdata_project zpj ON(zpj.id = zppn.target_project)
                WHERE zp.ptid=:pt_id AND date_end_project > :date AND zppn.status_project='0'";

        return Yii::$app->db->createCommand($sql, [':pt_id' => $pt_id, ':date' => $date])->queryOne();
    }

    public static function getOrderCheckStatus($visit_id, $order_code) {
        $sql = "SELECT zot.id,zot.order_tran_code,zot.order_tran_status,zot.order_tran_cashier_status
                FROM zdata_order_tran zot
                WHERE zot.rstat =1 AND zot.order_tran_visit_id=:visit_id AND order_tran_code=:order_code
                AND zot.order_tran_status='1' AND zot.order_tran_cashier_status = ''";

        return Yii::$app->db->createCommand($sql, [':visit_id' => $visit_id, ':order_code' => $order_code])->queryOne();
    }

    public static function getTkLast($target) {
        $sql = "SELECT ztk.tk_ph,ztk.tk_fh
                FROM zdata_tk ztk
                ##INNER JOIN zdata_tk ztk ON(ztk.ptid=zv.target)
                WHERE ztk.id=:target AND ztk.rstat='1'
                ORDER BY ztk.update_date DESC
                LIMIT 1;";

        return Yii::$app->db->createCommand($sql, [':target' => $target])->queryOne();
    }

    public static function getTkLastNew($ptid, $ezf_table) {        
         $sql = "SELECT ztk.tk_ph,ztk.tk_fh,ztk.ptid
                FROM $ezf_table ztk
                WHERE ztk.ptid=:ptid AND ztk.rstat not in(0,3)
                ORDER BY ztk.update_date DESC";

        return Yii::$app->db->createCommand($sql, [':ptid' => $ptid])->queryOne();
    }
    
    public static function getPatientHistoryNew($ptid) {        
         $sql = "SELECT CASE WHEN pth_disease_status=2 THEN
	CONCAT(CASE WHEN pth_disease_dm=1 THEN CONCAT('เบาหวาน เป็นมานาน ',pth_disease_dm_year,'ปี ,') ELSE '' END,
	CASE WHEN pth_disease_ht=1 THEN CONCAT('ความดันโลหิตสูง เป็นมานาน ',pth_disease_ht_year,'ปี ,') ELSE '' END,
	CASE WHEN pth_disease_kd=1 THEN CONCAT('ไตเรื้อรัง เป็นมานาน ',pth_disease_kd_year,'ปี ,') ELSE '' END,
        CASE WHEN pth_disease_other=1 THEN CONCAT(pth_disease_other_txt,'  เป็นมานาน ',pth_disease_kd_year,'ปี') ELSE '' END) END pth_disease,
        CASE WHEN pth_or_status=2 THEN CONCAT(pth_or_position,' เป็นมานาน ',pth_or_year,' ปี') ELSE '' END pth_or,
        pth_allergy_drug,CONCAT(IFNULL(pth_allergy_food,''),' ,',IFNULL(pth_allergy_other,'')) AS allergy_other,
        CASE WHEN pth_fh_cancer=2 THEN CONCAT('โรคมะเร็งในครอบครัว ',pth_fh_cancer_txt) END AS pth_fh,
        CONCAT(CASE WHEN pth_disease_smoke=2 THEN 'สูบบุหรี่ ,' ELSE '' END,
        CASE WHEN pth_disease_alcohol=2 THEN 'ดื่มแอลกอฮอล์ ,' ELSE '' END,
        CASE WHEN pth_disease_raw=2 THEN 'ทานปลาดิบ' ELSE '' END
        ) AS pth_cancer
        FROM zdata_patienthistory_new
        WHERE rstat not in(0,3) AND ptid=:ptid
        ";

        return Yii::$app->db->createCommand($sql, [':ptid' => $ptid])->queryOne();
    }    

    public static function getFmTk($ptid) {
        $sql = "SELECT CONCAT(pt_cancer_relative,' ',pt_cancer_organ) AS fm_history
                ,CONCAT(IFNULL(CONCAT(db.diag_name,' เป็นมา ',zp.pt_disease_num,' ปี',' รักษาที่ ',IFNULL(zho.`name`,zp.pt_disease_hos)),'')
,IFNULL(CONCAT(', ',db2.diag_name,' เป็นมา ',zp.pt_disease_num2,' ปี',' รักษาที่ ',IFNULL(zho2.`name`,zp.pt_disease_hos2)),'')
,IFNULL(CONCAT(', ',db3.diag_name,' เป็นมา ',zp.pt_disease_num3,' ปี',' รักษาที่ ',IFNULL(zho3.`name`,zp.pt_disease_hos3)),'')) AS disease
                FROM zdata_patienthistory zp
                LEFT JOIN zdata_diag_basic db ON(db.id=zp.pt_disease_detail)
                LEFT JOIN const_hospital zho ON(zho.`code`=zp.pt_disease_hos)
                LEFT JOIN zdata_diag_basic db2 ON(db2.id=zp.pt_disease_detail2)
                LEFT JOIN const_hospital zho2 ON(zho2.`code`=zp.pt_disease_hos2)
                LEFT JOIN zdata_diag_basic db3 ON(db3.id=zp.pt_disease_detail3)
                LEFT JOIN const_hospital zho3 ON(zho3.`code`=zp.pt_disease_hos3)
                WHERE zp.rstat='1' AND zp.ptid=:ptid";

        return Yii::$app->db->createCommand($sql, [':ptid' => $ptid])->queryOne();
    }

    public static function getPrintAppoint($app_id) {
//        $sql = "SELECT pt_hn,CONCAT(prefix.prefix_name,vp.pt_firstname,' ',vp.pt_lastname) AS fullname,pt_bdate,app_date,app_time,zi.ins_name
//                ,cot.order_type_name,GROUP_CONCAT(order_name ORDER BY order_name SEPARATOR '   ,') AS order_name,app_pt_detail
//                ,CONCAT(pf.title,pf.firstname,' ',pf.lastname) AS doc_fullname,ds.sect_name
//                FROM zdata_appoint za
//                LEFT JOIN zdata_visit zv ON(zv.visit_app_id=za.id)
//                LEFT JOIN zdata_order_header od ON (od.order_visit_id = zv.id)
//                LEFT JOIN zdata_order_tran zot ON(zot.order_header_id=od.id AND zot.rstat=1)
//                LEFT JOIN const_order co ON(co.order_code=zot.order_tran_code)
//                LEFT JOIN const_order_type cot ON(cot.order_type_code=co.group_type)
//                INNER JOIN zdata_patientprofile vp ON(vp.id=za.ptid)
//                INNER JOIN zdata_prefix prefix ON(prefix.prefix_id=vp.pt_prefix_id)
//                LEFT JOIN zdata_inspect zi ON(zi.id=app_insp_id)
//                LEFT JOIN `profile` pf ON(pf.user_id= app_doctor)
//                INNER JOIN dept_sect ds ON(ds.sect_code=za.app_dept)
//                WHERE za.rstat='1' AND za.id=:app_id
//                GROUP BY order_type_name";
        $dept = Yii::$app->user->identity->profile->department;
        $where_order_dept = '';
        $where_order_type = '';
        $where_order_list = '';
        $where_param = [':app_id' => $app_id];
        if($dept == '1536740852042206000'){
            $where_order_type = ' AND zoty.order_type_code = :type';
            $where_order_list = ' AND zol.group_type = :g_type';
            $where_param[':type'] = 'X';
            $where_param[':g_type'] = '1536202095096115800';
        }else{
            $where_order_dept = ' AND zot.order_tran_dept = :dept';
            $where_param[':dept'] = $dept;
        }
//        VarDumper::dump($where_param);
        $sql = "SELECT pt_hn,CONCAT(prefix.prefix_name,vp.pt_firstname,' ',vp.pt_lastname) AS fullname,pt_bdate,app_date,app_time,zi.ins_name
                ,zoty.order_type_name,GROUP_CONCAT(order_name ORDER BY order_name SEPARATOR ' , ') AS order_name,app_pt_detail
                ,CONCAT(pf.title,pf.firstname,' ',pf.lastname) AS doc_fullname,ds.sect_name
                FROM zdata_appoint za
                LEFT JOIN zdata_visit zv ON(zv.id=za.app_visit_id AND zv.ptid=za.ptid)
                LEFT JOIN zdata_order_header od ON (od.order_visit_id = zv.id)
                LEFT JOIN zdata_order_tran zot ON(zot.order_header_id=od.id AND zot.rstat=1 {$where_order_dept})

								LEFT JOIN zdata_order_lists zol ON(zol.order_code=zot.order_tran_code {$where_order_list})
								LEFT JOIN zdata_order_type zoty ON(zoty.id=zol.group_type {$where_order_type})
                
-- 								LEFT JOIN const_order co ON(co.order_code=zot.order_tran_code)
--                 LEFT JOIN const_order_type cot ON(cot.order_type_code=co.group_type)

                LEFT JOIN zdata_patientprofile vp ON(vp.id=za.ptid)
                LEFT JOIN zdata_prefix prefix ON(prefix.prefix_id=vp.pt_prefix_id)
                LEFT JOIN zdata_inspect zi ON(zi.id=app_insp_id)
                LEFT JOIN `profile` pf ON(pf.user_id= app_doctor) 
                LEFT JOIN dept_sect ds ON(ds.sect_code=za.app_dept)
                 WHERE za.rstat='1' AND za.id=:app_id
                GROUP BY order_type_name";

        return Yii::$app->db->createCommand($sql, $where_param)->queryAll();
    }


    public static function getSereneDeptToNhisDept($dept_ocde) {
        $sql = "SELECT sect_code,sect_name
                FROM dept_sect ds
                WHERE sect_map_code=:dept_code";
        return Yii::$app->db->createCommand($sql, [':dept_code' => $dept_ocde])->queryOne();
    }

    public static function getDataAutocom($q, $table, $field) {
        $sql = "SELECT DISTINCT $field
                FROM $table
                WHERE $field <> '' AND $field LIKE :q";
        return Yii::$app->db->createCommand($sql, [':q' => "%$q%"])->queryAll();
    }

    public static function getReportOutlab($visit_id, $order_code) {
        $sql = "SELECT zle.id
                FROM zdata_lab_external zle
                INNER JOIN zdata_order_tran zot ON(zot.id=zle.external_order_id)
                WHERE zle.rstat='1' AND zot.order_tran_visit_id=:visit_id 
                AND zot.order_tran_code=:order_code";

        return Yii::$app->db->createCommand($sql, [':visit_id' => $visit_id, ':order_code' => $order_code])->queryOne();
    }

    public static function getPrintStickLis($dept, $date) {
        $sql = "";

        return Yii::$app->db->createCommand($sql, [':dept' => $dept, ':date' => $date])->queryAll();
    }

    public static function getVisitDetail($visit_id) {
        $sql = "SELECT zv.visit_date AS visit_date_time,DATE(zv.visit_date) AS visit_date
                ,zpp.pt_hn,visit_type
                FROM zdata_visit zv
                INNER JOIN zdata_patientprofile zpp ON(zv.visit_pt_id=zpp.id)
                WHERE zv.rstat='1'
                AND zv.id = :visit_id";
        $result = Yii::$app->db->createCommand($sql, [':visit_id' => $visit_id])->queryOne();

        return $result;
    }

    public static function deleteZdata($target, $table) {
        $sql = "DELETE FROM $table WHERE rstat='1' AND target='{$target}'";

        Yii::$app->db->createCommand($sql)->execute();
    }

    public static function getDoctorTreat($visit_id) {
        $sql = "SELECT visit_doctor_concat(:visit_id) AS doctor_treat";
        return Yii::$app->db->createCommand($sql, [':visit_id' => $visit_id])->queryOne();
    }

    public static function getXrayReportByid($report_id) {
        $sql = "SELECT zx.id,zx.report_xray_result AS result,col.order_name AS xray_item_des
        ,CONCAT(pf.title,pf.firstname,' ',pf.lastname) AS doc_result,CONCAT(pf_order.title,pf_order.firstname,' ',pf_order.lastname) AS doc_order
	,CASE WHEN pt_sex = '1' THEN 'ชาย' ELSE 'หญิง' END AS pt_sex,zx.report_xray_date AS result_date,zot.create_date AS order_date
	,pt_hn,CONCAT(prefix_name_cid,pt_firstname,' ',pt_lastname) AS fullname,pt_bdate,order_group_name,zwu.unit_name
        FROM zdata_order_header oh
	INNER JOIN zdata_order_tran zot ON(zot.order_header_id=oh.id)
        INNER JOIN zdata_reportxray zx ON(zx.target = zot.id) 
        INNER JOIN zdata_order_lists col ON(col.order_code=zot.order_tran_code) 
	INNER JOIN zdata_order_group zog ON(zog.id=col.group_code)
        LEFT JOIN zdata_working_unit zwu ON(zwu.id = zot.order_tran_dept)
        LEFT JOIN `profile` pf ON(pf.user_id= zx.report_xray_docid) 
	LEFT JOIN `profile` pf_order ON(pf_order.user_id= zot.order_tran_doctor) 
        INNER JOIN zdata_patientprofile zpp ON(zpp.id=oh.ptid)
        INNER JOIN zdata_prefix zpf ON(zpf.prefix_id=zpp.pt_prefix_id)
        WHERE zot.rstat =1 AND zx.report_status = '2' AND zx.id = :report_id";

        return Yii::$app->db->createCommand($sql, [':report_id' => $report_id])->queryAll();
    }
    
    public static function getAdmitFinGroup($order_tran_id) {
        $sql = "SELECT zoh.order_visit_id AS visit_id,za.id AS admit_id,cof.id AS fin_group_id,cof.order_fin_name
                ,zot.order_tran_pay,zot.order_tran_notpay
                FROM zdata_order_tran zot
                INNER JOIN zdata_order_header zoh ON(zoh.id=zot.order_header_id)
                INNER JOIN zdata_order_lists col ON(col.order_code=zot.order_tran_code)
                INNER JOIN zdata_order_fin_group cof ON(cof.id=col.fin_item_code)
                INNER JOIN zdata_admit za ON(za.admit_visit_id=zoh.order_visit_id)
                WHERE zot.id=:order_tran_id;";
        
        return Yii::$app->db->createCommand($sql, [':order_tran_id' => $order_tran_id])->queryOne();
    }

    public static function  checkAdmit($ptid)
    {
        $sql = "SELECT ptid,admit_status FROM zdata_admit WHERE ptid =:ptid AND admit_status = 1 AND rstat NOT IN(0,3);";

        return Yii::$app->db->createCommand($sql, [':ptid' => $ptid])->queryOne();
    }

}
