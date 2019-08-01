<?php

namespace backend\modules\customer\classes;

use Yii;
use appxq\sdii\utils\SDdate;

/**
 * OvccaFunc class file UTF-8
 * @author SDII <iencoded@gmail.com>
 * @copyright Copyright &copy; 2015 AppXQ
 * @license http://www.appxq.com/license/
 * @version 1.0.0 Date: 9 ก.พ. 2559 12:38:14
 * @link http://www.appxq.com/
 * @example 
 */
class CusQuery {

    public static function getDetailItemCheckup($hn, $visit_date, $right_code, $project_id) {
        $paramsStr = " AND receipt_right_code=:right_code AND receipt_project_id=:project_id";
        $paramsArry[':right_code'] = $right_code;
        $paramsArry[':project_id'] = $project_id;

        $date = explode(",", $visit_date);
        if (count($date) > 1) {
            $dateStart = SDdate::phpThDate2mysqlDate($date[0], "-");
            $dateEnd = SDdate::phpThDate2mysqlDate($date[1], "-");

            $paramsStr .= " AND DATE(visit_date) BETWEEN '{$dateStart}' AND '{$dateEnd}'";
        } else {
            $date = SDdate::phpThDate2mysqlDate($date[0], "-");
            $paramsStr .= " AND DATE(visit_date) = '{$date}'";
        }

        if ($hn) {
            $paramsStr .= " AND pt_hn=:hn";
            $paramsArry[':hn'] = $hn;
        }

        $sql = "SELECT DATE(visit_date) AS visit_date,pt_hn,CONCAT(zpf.prefix_name,' ',vpp.pt_firstname,' ',vpp.pt_lastname) AS fullname,vpp.pt_bdate,zr.right_name
                ,zrt.tran_item_pay AS sumpay,zrt.tran_item_notpay AS sumnotpay
                ,nhso_code as sks_code,col.order_name,fin_item_code,fin_group_code as fin_item_group_code,zpj.project_name
                FROM zdata_receipt_mas zrm  
                INNER JOIN zdata_receipt_trn zrt ON(zrt.tran_receipt_id=zrm.id)  
                INNER JOIN zdata_order_lists col ON(zrt.tran_item_code = col.order_code)
                INNER JOIN zdata_order_fin_group cof ON(cof.id=col.fin_item_code)
                INNER JOIN zdata_order_fin_type cot ON(cot.id=cof.order_fin_type_id)
                INNER JOIN zdata_visit zv ON(zv.id = zrm.receipt_visit_id)  
                INNER JOIN zdata_patientprofile AS vpp ON(vpp.ptid=zrm.ptid) 
                INNER JOIN zdata_prefix zpf ON(zpf.prefix_id=vpp.pt_prefix_id)  
                INNER JOIN zdata_right zr ON(zr.right_code=zrm.receipt_right_code)
                INNER JOIN zdata_project zpj ON(zpj.id=receipt_project_id)
                WHERE receipt_status='A' $paramsStr
                /*UNION
                
                SELECT DATE(visit_date) AS visit_date,pt_hn,fullname,vpp.pt_bdate,zr.right_name  
                ,dkm4.drugtran_item_pay_amt AS sumpay,dkm4.drugtran_item_cr_amt AS sumnotpay,dkm4.drugtran_tmt_code AS sks_code
                ,dkm4.drugtran_itemdetail AS trad_name
                ,cof.fin_item_code,cof.fin_item_group_code,zpj.project_name
                FROM zdata_receipt_mas zrm  
		INNER JOIN zdata_visit zv ON(zv.id=zrm.receipt_visit_id)  
INNER JOIN zdata_drugtran_km4 dkm4 ON(dkm4.drugtran_visit_id=zv.id)  
INNER JOIN const_order_finname cof ON(cof.fin_item_code=dkm4.drugtran_item_code_op)
                INNER JOIN vpatient_profile AS vpp ON(vpp.pt_id=zrm.ptid)  
                INNER JOIN zdata_right zr ON(zr.right_code=zrm.receipt_right_code)
                LEFT JOIN zdata_project zpj ON(zpj.id=receipt_project_id)
                WHERE receipt_status='A' $paramsStr */
                ORDER BY pt_hn,visit_date,fin_item_group_code,sks_code";

        return Yii::$app->db->createCommand($sql, $paramsArry)->queryAll();
    }

    public static function getDetailItem($hn, $visit_date, $right_code) {
        if ($right_code == 'OFC') {
           // $sqlAddon = "dkm4.drugtran_item_cr_amt + dkm4.drugtran_item_pay_amt";
        } else {
        //    $sqlAddon = "dkm4.drugtran_item_cr_amt";
        }
        $sql = "SELECT DISTINCT DATE(visit_date) AS visit_date,vpp.pt_hn,CONCAT(zpf.prefix_name,' ',vpp.pt_firstname,' ',vpp.pt_lastname) AS fullname,zr.right_name,pt_bdate
                ,zrt.tran_item_pay AS sumpay,zrt.tran_item_notpay AS sumnotpay
                ,col.nhso_code AS sks_code,col.order_name,col.fin_item_code,fin_group_code as fin_item_group_code,col.order_code,vpp.pt_cid
                ,visit_date AS visit_datetime,right_prove_no,visit_doctor_concat(zv.id) AS all_doc,di_txt AS diag_txt
,CONCAT_WS(',',di_icd10) AS diag,zot.order_qty
,'' AS certificate
                FROM zdata_receipt_mas zrm
		INNER JOIN zdata_order_header zoh ON(zoh.order_visit_id = zrm.receipt_visit_id)
                INNER JOIN zdata_order_tran zot ON(zot.order_header_id = zoh.id)
                INNER JOIN zdata_receipt_trn zrt ON(zrt.tran_receipt_id=zrm.id AND zrt.rstat='1')  	
                INNER JOIN zdata_order_lists col ON(col.order_code=zrt.tran_item_code)
                INNER JOIN zdata_order_fin_group cof ON(cof.id=col.fin_item_code)
                INNER JOIN zdata_order_fin_type cot ON(cot.id=cof.order_fin_type_id)  
                INNER JOIN zdata_visit zv ON(zv.id = zrm.receipt_visit_id)  
                INNER JOIN zdata_patientprofile AS vpp ON(vpp.ptid=zrm.ptid)
                INNER JOIN zdata_prefix zpf ON(zpf.prefix_id=vpp.pt_prefix_id) 
                INNER JOIN zdata_right zr ON(zr.right_code=zrm.receipt_right_code)
                LEFT JOIN zdata_patientright zpr ON(zpr.id=zrm.receipt_patientright_id  AND zpr.rstat = 1)
                LEFT JOIN zdata_dt zdt ON(zv.id=zdt.di_visit_id)
                WHERE  receipt_status='A' AND receipt_right_code=:right_code 
		AND DATE(visit_date) =:visit_date AND pt_hn=:hn
               UNION
                SELECT DATE(visit_date) AS visit_date,pt_hn,CONCAT(zpf.prefix_name,' ',vpp.pt_firstname,' ',vpp.pt_lastname) AS fullname,vpp.pt_bdate,zr.right_name
,zdata_pis_order_tran.order_tran_pay,zdata_pis_order_tran.order_tran_notpay,zdata_pism_item.trad_tmt AS sks_code,zdata_pism_item.trad_itemname AS order_name
,zdata_order_fin_group.order_fin_code AS fin_item_code, '' AS fin_item_group_code,zdata_pism_item.trad_tmt AS order_code,vpp.pt_cid,
visit_date AS visit_datetime,right_prove_no,visit_doctor_concat(zv.id) AS all_doc,di_txt AS diag_txt,CONCAT_WS(',',di_icd10) AS diag,zdata_pis_order_tran.order_tran_qty AS order_qty
,'' AS certificate
FROM zdata_receipt_mas zrm
INNER JOIN zdata_visit zv ON(zv.id = zrm.receipt_visit_id AND zv.rstat = 1)
INNER JOIN zdata_patientprofile AS vpp ON(vpp.ptid=zrm.ptid)
LEFT JOIN zdata_prefix zpf ON(zpf.prefix_id=vpp.pt_prefix_id)
INNER JOIN zdata_pis_order zpo ON(zv.id = zpo.order_visit_id)
INNER JOIN `zdata_pis_order_tran` ON `zpo`.`id` = `zdata_pis_order_tran`.`order_id`
LEFT JOIN `zdata_pism_item` ON `zdata_pism_item`.`id` = `zdata_pis_order_tran`.`order_trad_id`
LEFT JOIN `zdata_order_fin_group` ON `zdata_order_fin_group`.`id` = `zdata_pism_item`.`trad_fin_gruop_id`
INNER JOIN zdata_order_fin_type cot ON(cot.id=zdata_order_fin_group.order_fin_type_id)
INNER JOIN `zdata_patientright` ON (zv.`id` = `zdata_patientright`.`right_visit_id` AND zdata_patientright.rstat = 1)
LEFT JOIN `zdata_order_fin_type` ON `zdata_order_fin_type`.`id` = `zdata_order_fin_group`.`order_fin_type_id`
INNER JOIN zdata_right zr ON(zr.right_code=zrm.receipt_right_code)
LEFT JOIN zdata_project zpj ON(zpj.id=receipt_project_id)
LEFT JOIN zdata_dt zdt ON(zv.id=zdt.di_visit_id)
WHERE receipt_status='A' 
AND receipt_right_code = :right_code
AND DATE(visit_date) = :visit_date
AND order_tran_notpay  > 0
AND pt_hn= :hn ;
";
       /* $sql = "SELECT DISTINCT DATE(visit_date) AS visit_date,vpp.pt_hn,CONCAT(zpf.prefix_name,' ',vpp.pt_firstname,' ',vpp.pt_lastname) AS fullname,zr.right_name,pt_bdate
                ,zrt.tran_item_pay AS sumpay,zrt.tran_item_notpay AS sumnotpay
                ,col.nhso_code AS sks_code,col.order_name,col.fin_item_code,fin_group_code as fin_item_group_code,col.order_code,vpp.pt_cid
                ,visit_date AS visit_datetime,right_prove_no,visit_doctor_concat(zv.id) AS all_doc,di_txt AS diag_txt
,CONCAT_WS(',',di_icd10,di_icd10_2,di_icd10_3,di_icd10_4,di_icd10_5) AS diag,zot.order_qty
,'' AS certificate
                FROM zdata_receipt_mas zrm
                INNER JOIN zdata_order_tran zot ON(zot.order_tran_visit_id = zrm.receipt_visit_id)
                INNER JOIN zdata_receipt_trn zrt ON(zrt.tran_receipt_id=zrm.id AND zrt.rstat='1')  	
                INNER JOIN zdata_order_lists col ON(col.order_code=zrt.tran_item_code)
                INNER JOIN zdata_order_fin_group cof ON(cof.id=col.fin_item_code)
                INNER JOIN zdata_order_fin_type cot ON(cot.id=cof.order_fin_type_id)  
                INNER JOIN zdata_visit zv ON(zv.id = zrm.receipt_visit_id)  
                INNER JOIN zdata_patientprofile AS vpp ON(vpp.pt_id=zrm.ptid)
                INNER JOIN zdata_prefix zpf ON(zpf.prefix_id=zpp.pt_prefix_id) 
                INNER JOIN zdata_right zr ON(zr.right_code=zrm.receipt_right_code)
                LEFT JOIN zdata_patientright zpr ON(zpr.id=zrm.receipt_patientright_id)
                LEFT JOIN zdata_dt zdt ON(zv.id=zdt.di_visit_id)
                WHERE  receipt_status='A' AND receipt_right_code=:right_code 
		AND DATE(visit_date) =:visit_date AND pt_hn=:hn
                /*UNION
                SELECT DISTINCT DATE(visit_date) AS visit_date,vpp.pt_hn,fullname,zr.right_name,pt_bdate
                ,dkm4.drugtran_item_pay_amt AS sumpay,$sqlAddon AS sumnotpay,dkm4.drugtran_tmt_code AS sks_code
                ,dkm4.drugtran_itemdetail AS trad_name
                ,dkm4.drugtran_item_code_op AS fin_item_code,cof.fin_item_group_code,dkm4.drugtran_cpoe_id AS order_code,vpp.pt_cid,visit_date AS visit_datetime,right_prove_no
,visit_doctor_concat(zv.id) AS all_doc       
,di_txt AS diag_txt,CONCAT_WS(',',di_txt,di_icd10,di_icd10_2,di_icd10_3,di_icd10_4,di_icd10_5) AS diag,CAST(dkm4.drugtran_item_qty AS UNSIGNED) AS order_qty
,concat('ว',pf.certificate) AS certificate
FROM zdata_receipt_mas zrm  
                INNER JOIN zdata_visit zv ON(zv.id=zrm.receipt_visit_id)  
INNER JOIN zdata_drugtran_km4 dkm4 ON(dkm4.drugtran_visit_id=zv.id)
INNER JOIN const_order_finname cof ON(cof.fin_item_code=dkm4.drugtran_item_code_op)
                INNER JOIN vpatient_profile AS vpp ON(vpp.pt_id=zrm.ptid)  
                INNER JOIN zdata_right zr ON(zr.right_code=zrm.receipt_right_code)
                LEFT JOIN zdata_patientright zpr ON(zpr.id=zrm.receipt_patientright_id)
                LEFT JOIN zdata_dt zdt ON(zv.id=zdt.di_visit_id)
                LEFT JOIN profile pf ON(pf.certificate=dkm4.drugtran_user_license)
                WHERE receipt_status='A' AND receipt_right_code=:right_code 
		AND DATE(visit_date) =:visit_date AND pt_hn=:hn
                ORDER BY fin_item_group_code,sks_code";*/

        return Yii::$app->db->createCommand($sql, [':hn' => $hn, 'visit_date' => $visit_date, ':right_code' => $right_code])->queryAll();
    }

    public static function getProjectSummaryOfc($visit_date, $project_id) {
        $paramsStr = '';
        $date = explode(",", $visit_date);
        if (count($date) > 1) {
            $dateStart = SDdate::phpThDate2mysqlDate($date[0], "-");
            $dateEnd = SDdate::phpThDate2mysqlDate($date[1], "-");
            $paramsStr .= " AND DATE(visit_date) BETWEEN '{$dateStart}' AND '{$dateEnd}'";
        }

        $sql = "SELECT visit_date,pt_hn,fullname,pt_bdate,zpj.project_name,SUM(CG001) AS CG001,SUM(HM001) AS HM001,SUM(UR001) AS UR001,SUM(FE001) AS FE001
                ,SUM(FE002) AS FE002,SUM(BC001) AS BC001,SUM(BC002) AS BC002,SUM(BC003) AS BC003,SUM(BC005) AS BC005,SUM(BC006) AS BC006,SUM(BC009) AS BC009
                ,SUM(BC015) AS BC015,SUM(BC016) AS BC016,SUM(BC017) AS BC017,SUM(CH) AS CH
                FROM vcheckup_pro_ofc vpo
                INNER JOIN zdata_project zpj ON(zpj.id=vpo.receipt_project_id)
                WHERE vpo.receipt_project_id=:project_id $paramsStr
                GROUP BY visit_date,pt_hn,fullname";

        return Yii::$app->db->createCommand($sql, [':project_id' => $project_id,])->queryAll();
    }

    public static function getProjectSummaryOri($visit_date, $project_id) {
        $paramsStr = '';
        $date = explode(",", $visit_date);
        if (count($date) > 1) {
            $dateStart = SDdate::phpThDate2mysqlDate($date[0], "-");
            $dateEnd = SDdate::phpThDate2mysqlDate($date[1], "-");
            $paramsStr .= " AND DATE(visit_date) BETWEEN '{$dateStart}' AND '{$dateEnd}'";
        }

        $sql = "SELECT visit_date,pt_hn,fullname,pt_bdate,zpj.project_name,SUM(CG001) AS CG001,SUM(HM001) AS HM001,SUM(UR001) AS UR001,SUM(FE001) AS FE001
                ,SUM(FE002) AS FE002,SUM(BC001) AS BC001,SUM(BC002) AS BC002,SUM(BC003) AS BC003,SUM(BC005) AS BC005,SUM(BC006) AS BC006,SUM(BC009) AS BC009
                ,SUM(BC015) AS BC015,SUM(BC016) AS BC016,SUM(BC017) AS BC017,SUM(CH) AS CH,SUM(IM008) AS IM008
                ,SUM(BC011) AS BC011,SUM(BC012) AS BC012,SUM(BC013) AS BC013,SUM(BC014) AS BC014,
                SUM(IM001) AS IM001,SUM(IM002) AS IM002,SUM(IM006) AS IM006,SUM(IM047) AS IM047,
                SUM(PH001) AS PH001,SUM(CG015) AS CG015,SUM(CG016) AS CG016,SUM(CG020) AS CG020
                FROM vcheckup_pro_ori vpo
                INNER JOIN zdata_project zpj ON(zpj.id=vpo.receipt_project_id)
                WHERE vpo.receipt_project_id=:project_id $paramsStr
                GROUP BY visit_date,pt_hn,fullname";

        return Yii::$app->db->createCommand($sql, [':project_id' => $project_id,])->queryAll();
    }

    public static function getDetailItemSks($recivemas_id) {
        $sql = "SELECT DISTINCT vpp.pt_hn,fullname,zr.right_name,zs.visit_date AS visit_date,co.sks_code
            ,co.order_code,co.order_name,item_group AS fin_item_group_code,'' AS sumpay
            , item_price AS sumnotpay,item_qty AS order_qty,app_code,
            doctor_code AS certificate
               FROM zdata_sks zs
               INNER JOIN const_order co ON(co.order_code = zs.item_code)
               INNER JOIN zdata_receipt_mas zrm ON(zrm.receipt_visit_id = zs.ref_no)
               INNER JOIN vpatient_profile AS vpp ON(vpp.pt_id=zrm.ptid)  
               INNER JOIN zdata_right zr ON(zr.right_code=zrm.receipt_right_code)
               WHERE ref_no=:recivemas_id AND zrm.receipt_right_code='OFC'
               GROUP BY vpp.pt_hn,fullname,zr.right_name,zs.visit_date,co.sks_code
            ,co.order_code,co.order_name,item_group
            , item_price,item_qty
        UNION
        SELECT DISTINCT vpp.pt_hn,fullname
        ,zr.right_name
        ,zs.visit_date AS visit_date
        ,zs.sks_code
        ,zs.item_code AS order_code
        ,ItemName AS order_name
        ,zs.item_group AS fin_item_group_code
        ,'' AS sumpay
        ,zs.item_price AS sumnotpay
        ,zs.item_qty AS order_qty,app_code,doctor_code AS certificate
               FROM zdata_sks zs
	       INNER JOIN km4_item km4i ON(km4i.TMTID_TPU=zs.sks_code)
               INNER JOIN zdata_receipt_mas zrm ON(zrm.receipt_visit_id = zs.ref_no)
               INNER JOIN vpatient_profile AS vpp ON(vpp.pt_id=zrm.ptid)  
               INNER JOIN zdata_right zr ON(zr.right_code=zrm.receipt_right_code)
               WHERE ref_no=:recivemas_id AND zrm.receipt_right_code='OFC'
               ORDER BY fin_item_group_code,sks_code";

        return Yii::$app->db->createCommand($sql, [':recivemas_id' => $recivemas_id])->queryAll();
    }

    public static function deletesks($ref_no) {
        $sql = "DELETE FROM zdata_sks WHERE ref_no = :ref_no";
        Yii::$app->db->createCommand($sql, [':ref_no' => $ref_no])->execute();
    }

    public static function getConstOrderName($const_name) {
        $sql = "SELECT co.order_code AS id,concat(co.order_name,'|',co.full_price,'|',co.fin_item_code,'|',co.sks_code,'|',cof.fin_item_group_code) AS text,co.fin_item_code,co.sks_code,co.full_price,cof.fin_item_group_code FROM const_order co
INNER JOIN const_order_finname cof ON(cof.fin_item_code = co.fin_item_code)
WHERE CONCAT(co.order_name,' ',co.order_code) like :const_name
UNION
SELECT DISTINCT km4i.TMTID_TPU AS id
,CONCAT(km4i.ItemName,'|',km4ip.cr_price_op,'|',cof.fin_item_code,'|',km4i.TMTID_TPU,'|',cof.fin_item_group_code) AS txt
,cof.fin_item_group_code AS fin_item_code,km4i.TMTID_TPU AS sks_code
,km4ip.cr_price_op AS full_price,fin_item_group_code
FROM km4_item km4i
INNER JOIN const_order_finname cof ON(cof.fin_item_code=km4i.ITEM_CODE_OP)
INNER JOIN km4_item_price km4ip ON(km4ip.ItemID=km4i.ItemID)
WHERE ItemName like :const_name
";

        return Yii::$app->db->createCommand($sql, [':const_name' => "%$const_name%"])->queryAll();
    }

    public static function getDoctorName($q) {
        $sql = "SELECT CONCAT('ว',certificate) AS id,user_id,CONCAT('ว',certificate,' ',title,firstname,' ',lastname) AS text  FROM profile  
where position = '2' AND CONCAT(firstname,' ',lastname) LIKE :q";

        return Yii::$app->db->createCommand($sql, [':q' => "%$q%"])->queryAll();
    }

    public static function getReciveidByVisitid($receipt_visit_id) {
        $sql = "SELECT id FROM zdata_receipt_mas where receipt_visit_id = :receipt_visit_id";
        return Yii::$app->db->createCommand($sql, [':receipt_visit_id' => $receipt_visit_id])->queryAll();
    }

    public static function genRefNum($visit_date) {
        $sql = "SELECT IFNULL(MAX(CAST(ref_num AS UNSIGNED))+1,1) AS ref_num
                FROM zdata_sks
                WHERE visit_date=:visit_date";
        return Yii::$app->db->createCommand($sql, [':visit_date' => $visit_date])->queryOne();
    }

    public static function getskstoxmlHeader($date_st, $date_en) {
        $sql = "SELECT SATAION,DATES,HCODE,YMD,BOOK_NO,HN,MBNO,DATES2,ref_no,REPLACE(FORMAT(SUM(NOTPAY),2),',','') AS NOTPAY
,PAY,VERCODE,TFLAG,DATES2,PT_CID FROM (SELECT '01' AS SATAION,concat(zs.visit_date,'T','00:00:00') AS DATES,'12276' AS HCODE,DATE_FORMAT(zs.visit_date,'%Y%m%d') AS YMD,'' AS BOOK_NO,
CASE WHEN (zpp.pt_hn_sks='' Or zpp.pt_hn_sks IS NULL) THEN zpp.pt_hn ELSE zpp.pt_hn_sks END AS HN,'' AS MBNO,DATE_FORMAT(zv.visit_date,'%Y-%m-%d %H:%m:%s') AS DATES2
,CONCAT(DATE_FORMAT(zv.visit_date,'%Y%m%d'),LPAD(ref_num,3,'0')) AS ref_no
,item_price AS NOTPAY,'' AS PAY
,'' AS VERCODE,'' AS TFLAG,zpp.pt_cid AS PT_CID
 FROM zdata_sks zs
INNER JOIN zdata_visit zv ON(zv.id=zs.ref_no)
INNER JOIN zdata_patientprofile zpp ON(zpp.id=zv.ptid)
INNER JOIN zdata_receipt_mas zrm ON(zrm.receipt_visit_id=zs.ref_no AND zrm.receipt_right_code='OFC')
WHERE zs.visit_date BETWEEN :date_st AND :date_en AND status_bill='2'
GROUP BY zs.visit_date,zv.visit_date,zs.hn_no,item_price,sks_code) AS KK
GROUP BY DATES2,HN ";

        return Yii::$app->db->createCommand($sql, [':date_st' => $date_st, ':date_en' => $date_en])->queryAll();
    }

    public static function getksktoxmlDetail($date_st, $date_en) {
        $sql = "SELECT HN_NO,DRG_FIELD,RECEIPT_DATE,RECEIPT_DATE2,REPLACE(FORMAT(SUM(NOTPAY),2),',','') AS NOTPAY,PAY FROM
(SELECT CASE WHEN (zpp.pt_hn_sks='' Or zpp.pt_hn_sks IS NULL) THEN zpp.pt_hn ELSE zpp.pt_hn_sks END AS HN_NO,
            zig.drg_field AS DRG_FIELD,concat(zs.visit_date,'T','00:00:00') AS RECEIPT_DATE
            ,DATE_FORMAT(zv.visit_date,'%Y-%m-%d %H:%m:%s') AS RECEIPT_DATE2
,item_price AS NOTPAY,'0.00' AS PAY
 FROM zdata_sks zs
INNER JOIN zdata_item_group zig ON(zig.DRG_ITEM_CODE = zs.item_group)
INNER JOIN zdata_visit zv ON(zv.id=zs.ref_no)
INNER JOIN zdata_patientprofile zpp ON(zpp.id=zv.ptid)
INNER JOIN zdata_receipt_mas zrm ON(zrm.receipt_visit_id=zs.ref_no AND zrm.receipt_right_code='OFC')
WHERE zs.visit_date BETWEEN :date_st AND :date_en AND status_bill='2' 
GROUP BY zs.visit_date,hn_no,drg_field,item_price,sks_code) AS KK
GROUP BY HN_NO,RECEIPT_DATE2,drg_field
ORDER BY HN_NO,drg_field";
        return Yii::$app->db->createCommand($sql, [':date_st' => $date_st, ':date_en' => $date_en])->queryAll();
    }

    public static function getDrugreportheader($date_st, $date_en) {
        $sql = "SELECT SATAION,DISPENSE_ID,HN_NO,PID,PRESCRIPTION_DATE,DISPENSED_DATE,DISPENSED_DATE2,PERSCRIBER,REPLACE(SUM(FULL_PRICE),',','') AS FULL_PRICE
,REPLACE(SUM(NOTPAY),',','') AS NOTPAY,PAY,OT,USERCLAIM,BENEFIT,DISPENSE_STATUS,COUNT(ITEM_COUNT) AS ITEM_COUNT FROM
(SELECT '12276' AS SATAION,concat(date_format(zs.visit_date,'%d%m%Y'),ref_no_drug) AS DISPENSE_ID
            ,CASE WHEN (zpp.pt_hn_sks='' Or zpp.pt_hn_sks IS NULL) THEN zpp.pt_hn ELSE zpp.pt_hn_sks END AS HN_NO,zpp.pt_cid AS PID,
zs.visit_Date AS PRESCRIPTION_DATE
,DATE_FORMAT(zv.visit_date,'%Y-%m-%dT%H:%m:%s') AS DISPENSED_DATE
,concat(DATE(zv.visit_date),'T','00:00:00') AS DISPENSED_DATE2
,zs.doctor_code AS PERSCRIBER,item_price AS FULL_PRICE
,item_price AS NOTPAY,'0.00' AS PAY,'0.00' AS OT
,'HP' AS USERCLAIM,'CS' AS BENEFIT,'1' AS DISPENSE_STATUS,ref_no_drug AS ITEM_COUNT
  FROM zdata_sks zs
INNER JOIN zdata_visit zv ON(zv.id=zs.ref_no) 
INNER JOIN zdata_patientprofile zpp ON(zpp.id=zv.ptid)
INNER JOIN zdata_receipt_mas zrm ON(zrm.receipt_visit_id=zs.ref_no AND zrm.receipt_right_code='OFC')
WHERE ITEM_GROUP IN('03','05')
AND zs.visit_date BETWEEN :date_st AND :date_en
AND status_bill='2'
GROUP BY REF_NO,HN_NO,pt_cid,zs.visit_date,doctor_code,item_price,sks_code,ref_no_drug) AS KK
GROUP BY HN_NO,DISPENSED_DATE2,ITEM_COUNT";
        return Yii::$app->db->createCommand($sql, [':date_st' => $date_st, ':date_en' => $date_en])->queryAll();
    }

    public static function getDrugreportDetail($date_st, $date_en) {
        $sql = "SELECT DISTINCT visit_Date
            ,concat(date_format(visit_Date,'%d%m%Y'),ref_no_drug) AS DISPENSE_ID
,'1' DIS_ID
,km4i.Item_workingcode AS HOS_DRUG_CODE
,zs.SKS_CODE AS DRUG_ID
,'' AS DFS_DOSE,km4i.ItemName AS DFS_NAME,
'NA' AS PACK_SIZE
,'' AS SIG_CODE
,'NA' AS SIG_TEXT,ITEM_QTY AS DRUG_QTY
,REPLACE(FORMAT((zs.ITEM_PRICE/IFNULL(ITEM_QTY,1)),2),',','') AS UNIT_PRICE
,REPLACE(FORMAT(zs.ITEM_PRICE,2),',','') AS NOYPAY 
,REPLACE(FORMAT((zs.ITEM_PRICE/IFNULL(ITEM_QTY,1)),2),',','') AS UNIT_PRICE2
,REPLACE(FORMAT(zs.ITEM_PRICE,2),',','') AS NOYPAY2 
,'' AS A1,'' AS A2
,CASE WHEN SUBSTR(km4i.ITEM_CODE_OP,1,4) = '0306' THEN 'EB' END AS A3
,'' AS A4 
FROM zdata_sks zs
INNER JOIN km4_item km4i ON(km4i.TMTID_TPU=zs.sks_code)
INNER JOIN zdata_receipt_mas zrm ON(zrm.receipt_visit_id=zs.ref_no AND zrm.receipt_right_code='OFC')
WHERE zs.ITEM_GROUP IN('03','05')
AND zs.visit_date BETWEEN :date_st AND :date_en AND status_bill='2'";
        return Yii::$app->db->createCommand($sql, [':date_st' => $date_st, ':date_en' => $date_en])->queryAll();
    }

    public static function UpdateSksRecivemas($sess_no, $receipt_no, $receipt_date, $hn) {
        $sql = "UPDATE zdata_receipt_mas zrm 
INNER JOIN zdata_patientprofile zp ON(zp.ptid = zrm.ptid)
INNER JOIN zdata_visit zv ON(zv.id = zrm.receipt_visit_id)
SET zrm.status_bill= 3 ,zrm.claim_tr = :sess_no,zrm.claim_tr_no=:receipt_no
WHERE (zp.pt_hn=:hn or zp.pt_hn_sks=:hn) AND DATE_FORMAT(zv.visit_date,'%Y-%m-%d') = :receipt_date  AND zrm.receipt_status ='A' AND receipt_right_code = 'OFC'";
        return Yii::$app->db->createCommand($sql, [':sess_no' => $sess_no, ':receipt_no' => $receipt_no, ':hn' => $hn, ':receipt_date' => $receipt_date])->execute();
    }

}
