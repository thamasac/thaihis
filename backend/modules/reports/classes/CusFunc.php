<?php

namespace backend\modules\reports\classes;

use Yii;
use appxq\sdii\utils\SDdate;
use DateTime;

/**
 * OvccaFunc class file UTF-8
 * @author SDII <iencoded@gmail.com>
 * @copyright Copyright &copy; 2015 AppXQ
 * @license http://www.appxq.com/license/
 * @version 1.0.0 Date: 9 ก.พ. 2559 12:38:14
 * @link http://www.appxq.com/
 * @example 
 */
class CusFunc {

    public static function getReportadmin($model, $params) {
        $model->load($params);
        $date = explode(",", $model['create_date']);
        $paramsStr = "";
        if (count($date) > 1) {
            $dateStart = SDdate::phpThDate2mysqlDate($date[0], "-");
            $dateEnd = SDdate::phpThDate2mysqlDate($date[1], "-");
            $paramsStr .= " AND DATE(zv.visit_date) BETWEEN '{$dateStart}' AND '{$dateEnd}'";
        } else {
            $date = SDdate::phpThDate2mysqlDate($date[0], "-");
            $paramsStr .= " AND DATE(zv.visit_date) = '{$date}'";
        }
        $sql = "select zv.visit_date,zv.ptid,pt_cid,pt_hn,CONCAT(zp.prefix_name,pt_firstname,' ',pt_lastname) AS fullname,sect_name,sect_code,visit_regis_type,pt_phone
from zdata_visit zv
INNER JOIN zdata_patientprofile zpp ON (zpp.id=zv.ptid)
LEFT JOIN zdata_prefix zp ON (zp.prefix_id=zpp.pt_prefix_id)
INNER JOIN zdata_order_tran zot ON(zot.ptid = zv.ptid)
INNER JOIN dept_sect ds ON(ds.sect_code=zot.order_tran_dept)
where zv.rstat = 1
AND visit_regis_type IN(1,2)
AND ds.sect_code ='S999' $paramsStr
GROUP BY zv.visit_date,zv.ptid,pt_hn,prefix_name,pt_firstname,pt_lastname,sect_name,sect_code,pt_phone";
        $dataProvider = new \yii\data\SqlDataProvider([
            'sql' => $sql,
//            'params' => $paramsArry,
            'pagination' => [
                'pageSize' => 15,
            ],
        ]);

        return $dataProvider;
    }

    public static function getReportPt($model, $params) {
        $model->load($params);
        $paramsStr = " receipt_status='A' AND receipt_right_code=:receipt_right_code";
        $paramsArry[':receipt_right_code'] = $model['order_tran_code'];
        if ($model['order_tran_comment'] <> 1) {
            $paramsStr .= " AND status_bill=:status_bill ";
            $paramsArry[':status_bill'] = $model['order_tran_comment'];
        } else {
            $paramsStr .= " AND (status_bill = '' OR status_bill IS NULL)";
        }
        $date = explode(",", $model['create_date']);
        if (count($date) > 1) {
            $dateStart = SDdate::phpThDate2mysqlDate($date[0], "-");
            $dateEnd = SDdate::phpThDate2mysqlDate($date[1], "-");

            $paramsStr .= " AND DATE(visit_date) BETWEEN '{$dateStart}' AND '{$dateEnd}'";
        } else {
            $date = SDdate::phpThDate2mysqlDate($date[0], "-");
            $paramsStr .= " AND DATE(visit_date) = '{$date}'";
        }

        if ($model['order_tran_dept']) {
            $paramsStr .= " AND receipt_project_id=:project_id";
            $paramsArry[':project_id'] = $model['order_tran_dept'];
        }

        $sql = "SELECT visit_date,receipt_ids,pt_hn,fullname,pt_cid,right_code,SUM(sumpay) AS sumpay,SUM(sumnotpay) AS sumnotpay,SUM(drug_sumpay) AS drug_sumpay
                ,SUM(drug_sumnotpay) AS drug_sumnotpay,status_bill,receipt_visit_id FROM
                (SELECT DATE(visit_date) AS visit_date,zrm.id AS receipt_ids,zpp.pt_hn,CONCAT(zpf.prefix_name,' ',zpp.pt_firstname,' ',zpp.pt_lastname) AS fullname,zpp.pt_cid,zrm.receipt_right_code AS right_code  
                ,SUM(zrt.tran_item_pay)AS sumpay,SUM(zrt.tran_item_notpay) AS sumnotpay,0 AS drug_sumpay,0 AS drug_sumnotpay,
                status_bill,zrm.receipt_visit_id
                FROM zdata_receipt_mas zrm  
                INNER JOIN zdata_receipt_trn zrt ON(zrt.tran_receipt_id=zrm.id AND zrt.rstat='1')  
		INNER JOIN const_order co ON(co.order_code=zrt.tran_item_code) 
                INNER JOIN const_order_finname cof ON(cof.fin_item_code=co.fin_item_code) 
                INNER JOIN zdata_visit zv ON(zv.id = zrm.receipt_visit_id)  
                INNER JOIN zdata_patientprofile zpp ON(zpp.id=zrm.ptid)
		INNER JOIN zdata_prefix zpf ON(zpf.prefix_id=zpp.pt_prefix_id)  
                WHERE $paramsStr
                GROUP BY zrm.receipt_right_code,fullname,DATE(visit_date)
                UNION ALL
                SELECT DATE(visit_date) AS visit_date,zrm.id AS receipt_ids,zpp.pt_hn,CONCAT(zpf.prefix_name,' ',zpp.pt_firstname,' ',zpp.pt_lastname) AS fullname,zpp.pt_cid,zrm.receipt_right_code AS right_code  
                ,0 AS sumpay,0 AS sumnotpay,SUM(zrt.tran_item_pay)AS drug_sumpay,SUM(zrt.tran_item_notpay) AS drug_sumnotpay,
                status_bill,zrm.receipt_visit_id
                FROM zdata_receipt_mas zrm  
                INNER JOIN zdata_receipt_trn zrt ON(zrt.tran_receipt_id=zrm.id AND zrt.rstat='1')  
		INNER JOIN const_order_finname cof ON(cof.fin_item_code=zrt.tran_item_code) 
                INNER JOIN zdata_visit zv ON(zv.id = zrm.receipt_visit_id)  
                INNER JOIN zdata_patientprofile zpp ON(zpp.id=zrm.ptid)
		INNER JOIN zdata_prefix zpf ON(zpf.prefix_id=zpp.pt_prefix_id) 
                WHERE $paramsStr
                GROUP BY zrm.receipt_right_code,fullname,DATE(visit_date)) AS KK
                GROUP BY right_code,fullname,visit_date ORDER BY visit_date,pt_hn";
        
        $dataProvider = new \yii\data\SqlDataProvider([
            'sql' => $sql,
            'params' => $paramsArry,
            //'sort' => ['attributes' => ['pt_hn', 'fullname', 'sect_name', 'doctor_name', 'right_name']],
            'pagination' => [
                'pageSize' => 15,
            ],
        ]);

        return $dataProvider;
    }

    public static function getReportAppDate($model, $params) {
        $model->load($params);
        $paramsStr = '';
        $date = explode(",", $model['create_date']);
        if (count($date) > 1) {
            $dateStart = SDdate::phpThDate2mysqlDate($date[0], "-");
            $dateEnd = SDdate::phpThDate2mysqlDate($date[1], "-");

            $paramsStr .= " AND app_date BETWEEN '{$dateStart}' AND '{$dateEnd}'";
        } else {
            $date = SDdate::phpThDate2mysqlDate($date[0], "-");
            $paramsStr .= " AND app_date = '{$date}'";
        }

        $sql = "SELECT /*zap.app_pt_id,zin.ins_name,CONCAT(pf.title,pf.firstname,' ',pf.lastname) AS doc_result*/
                zin.ins_name AS InspectName,count(zin.ins_name) AS Amount,CONCAT(pf.title,pf.firstname,' ',pf.lastname) AS DoctorFullname
                FROM zdata_appoint zap
                LEFT JOIN `profile` pf ON(pf.user_id=zap.app_doctor)
                INNER JOIN zdata_inspect zin ON(zin.id=zap.app_insp_id)
                WHERE zap.rstat='1' $paramsStr
                GROUP BY zin.ins_name,pf.firstname
                ORDER BY pf.firstname";

        $dataProvider = new \yii\data\SqlDataProvider([
            'sql' => $sql,
//            'params' => $paramsArry,
//            'sort' => ['attributes' => ['pt_hn', 'fullname', 'sect_name', 'doctor_name', 'right_name']],
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

        return $dataProvider;
    }
    
    public static function getReportProject($model, $params) {
        $model->load($params);
        $paramsStr = "";
        if ($model['create_date']) {
            $date = explode(",", $model['create_date']);
            if (count($date) > 1) {
                $dateStart = SDdate::phpThDate2mysqlDate($date[0], "-");
                $dateEnd = SDdate::phpThDate2mysqlDate($date[1], "-");
                $paramsStr .= " AND DATE(visit_date) BETWEEN '{$dateStart}' AND '{$dateEnd}' ";
            } else {
                $date = SDdate::phpThDate2mysqlDate($date[0], "-");
                $paramsStr .= " AND DATE(visit_date) = '{$date}'";
            }
        }
        $sql = "SELECT zppn.project_name,COUNT(zv.ptid) AS countpatient FROM zdata_visit zv
INNER JOIN zdata_receipt_mas zrm ON(zv.ptid = zrm.ptid AND zv.id = zrm.receipt_visit_id)
INNER JOIN zdata_project zppn ON(zrm.receipt_project_id = zppn.id)
WHERE zv.rstat = '1' AND zrm.rstat = '1' $paramsStr
GROUP BY zppn.project_name";

        $dataProvider = new \yii\data\SqlDataProvider([
            'sql' => $sql,
            // 'params' => $paramsArry,
            //'sort' => ['attributes' => ['pt_hn', 'fullname', 'sect_name', 'doctor_name', 'right_name']],
            'pagination' => [
                'pageSize' => 200,
            ],
        ]);
        //  \appxq\sdii\utils\VarDumper::dump($dataProvider);
        return $dataProvider;
    }
 public static function getReportTypeDoctor($model, $params) {
        $model->load($params);
        $paramsStr = '';
        $date = explode(",", $model['create_date']);
        if (count($date) > 1) {
            $dateStart = SDdate::phpThDate2mysqlDate($date[0], "-");
            $dateEnd = SDdate::phpThDate2mysqlDate($date[1], "-");

            $paramsStr .= " AND app_date BETWEEN '{$dateStart}' AND '{$dateEnd}'";
        } else {
            $date = SDdate::phpThDate2mysqlDate($date[0], "-");
            $paramsStr .= " AND app_date = '{$date}'";
        }
        $position = \Yii::$app->user->identity->profile->position;
        $iddoctor = \Yii::$app->user->identity->profile->user_id;
        $app_dept = \Yii::$app->user->identity->profile->department;
        if ($position == 2) {
            $paramsStr.= " AND zap.app_doctor = '{$iddoctor}'";
        }else if($position == 6){
            
        } else {
            $paramsStr.= " AND zap.app_dept = '{$app_dept}'";
        }

        $sql = "SELECT pt_hn,
concat(zp.prefix_name,zpp.pt_firstname,' ',zpp.pt_lastname) AS patientfullname,zap.app_date,               
zin.ins_name AS InspectName,ds.sect_name,CONCAT(pf.title,' ',pf.firstname,' ',pf.lastname) AS DoctorFullname
                FROM zdata_appoint zap
                LEFT JOIN `profile` pf ON(pf.user_id=zap.app_doctor)
                LEFT JOIN zdata_patientprofile zpp ON(zpp.ptid =zap.ptid)
                LEFT JOIN zdata_inspect zin ON(zin.id=zap.app_insp_id)
		LEFT JOIN zdata_prefix zp ON(zp.prefix_id = zpp.pt_prefix_id)
		LEFT JOIN dept_sect ds ON(ds.sect_code = zap.app_dept)
                WHERE zap.rstat='1' $paramsStr
                ORDER BY pf.firstname";

        $dataProvider = new \yii\data\SqlDataProvider([
            'sql' => $sql,
//            'params' => $paramsArry,
//            'sort' => ['attributes' => ['pt_hn', 'fullname', 'sect_name', 'doctor_name', 'right_name']],
            'pagination' => [
                'pageSize' => 200,
            ],
        ]);
        //  \appxq\sdii\utils\VarDumper::dump($dataProvider);
        return $dataProvider;
    }


    public static function getReportIC($model, $params) {
        $model->load($params);
        $paramsStr = '';
        $date = explode(",", $model['create_date']);
        if (count($date) > 1) {
            $dateStart = SDdate::phpThDate2mysqlDate($date[0], "-");
            $dateEnd = SDdate::phpThDate2mysqlDate($date[1], "-");

            $paramsStr .= " AND DATE(zv.visit_date) BETWEEN '{$dateStart}' AND '{$dateEnd}'";
        } else {
            $date = SDdate::phpThDate2mysqlDate($date[0], "-");
            $paramsStr .= " AND DATE(zv.visit_date) = '{$date}'";
        }

        $sql = "SELECT 
                CASE tk_ic WHEN '1' THEN 'No' 
                WHEN '2' THEN 'มีภาวะเม็ดเลือดขาว ต่ำกว่า 3,000 Cells/MM'
                WHEN '3' THEN CONCAT('มีสายสวนคาเส้นเลือด ',tk_ic_other_3)
                WHEN '4' THEN CONCAT('มีท่อต่อ',tk_ic_other_4)
                WHEN '5' THEN 'ไข้ ไอ มีเสมหะ' 
                WHEN '6' THEN 'ปัสสาวะแสบขัด' 
                WHEN '7' THEN 'ถ่ายอุจจาระเหลว ,เป็นมูก ,มูกเลือด มากกว่า 3 ครั้ง/วัน' 
                WHEN '8' THEN 'มีแผลตุ่ม ,ฝี ,หนอง' END as 'ประเมินความเสี่ยงด้าน IC'
                ,COUNT(tk_ic) AS count
                FROM zdata_visit zv
                INNER JOIN zdata_tk tk ON(tk.target=zv.id)
                WHERE tk.rstat='1' $paramsStr
                GROUP BY tk_ic";

        $dataProvider = new \yii\data\SqlDataProvider([
            'sql' => $sql,
//            'params' => $paramsArry,
//            'sort' => ['attributes' => ['pt_hn', 'fullname', 'sect_name', 'doctor_name', 'right_name']],
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

        return $dataProvider;
    }

    public static function getReportEmer($model, $params) {
        $model->load($params);
        $paramsStr = '';
        $date = explode(",", $model['create_date']);
        if (count($date) > 1) {
            $dateStart = SDdate::phpThDate2mysqlDate($date[0], "-");
            $dateEnd = SDdate::phpThDate2mysqlDate($date[1], "-");

            $paramsStr .= " AND DATE(zv.visit_date) BETWEEN '{$dateStart}' AND '{$dateEnd}'";
        } else {
            $date = SDdate::phpThDate2mysqlDate($date[0], "-");
            $paramsStr .= " AND DATE(zv.visit_date) = '{$date}'";
        }

        $sql = "SELECT 
                CASE tk_emc WHEN '1' THEN 'Emergency' 
                WHEN '2' THEN 'Urgent'
                WHEN '3' THEN 'Non Urgent' END as 'ประเภทผู้ป่วย'
                ,COUNT(tk_emc) AS count
                FROM zdata_visit zv
                INNER JOIN zdata_tk tk ON(tk.target=zv.id)
                WHERE zv.rstat='1' AND tk.rstat='1' $paramsStr 
                GROUP BY tk_emc";

        $dataProvider = new \yii\data\SqlDataProvider([
            'sql' => $sql,
//            'params' => $paramsArry,
//            'sort' => ['attributes' => ['pt_hn', 'fullname', 'sect_name', 'doctor_name', 'right_name']],
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

        return $dataProvider;
    }

    public static function getReportTrea($model, $params) {
        $model->load($params);
        $paramsStr = '';
        $date = explode(",", $model['create_date']);
        if (count($date) > 1) {
            $dateStart = SDdate::phpThDate2mysqlDate($date[0], "-");
            $dateEnd = SDdate::phpThDate2mysqlDate($date[1], "-");

            $paramsStr .= " AND DATE(zv.visit_date) BETWEEN '{$dateStart}' AND '{$dateEnd}'";
        } else {
            $date = SDdate::phpThDate2mysqlDate($date[0], "-");
            $paramsStr .= " AND DATE(zv.visit_date) = '{$date}'";
        }

        $sql = "SELECT 
                zi.ins_name AS 'ประเภทการมาตรวจ',COUNT(zi.ins_name) AS count
                FROM zdata_visit zv
                INNER JOIN zdata_tk tk ON(tk.target=zv.id)
                INNER JOIN zdata_inspect zi ON(zi.id=tk_inspect)
                WHERE zv.rstat='1' AND tk.rstat='1' $paramsStr
                GROUP BY zi.ins_name";

        $dataProvider = new \yii\data\SqlDataProvider([
            'sql' => $sql,
//            'params' => $paramsArry,
//            'sort' => ['attributes' => ['pt_hn', 'fullname', 'sect_name', 'doctor_name', 'right_name']],
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

        return $dataProvider;
    }

    public static function getReportType($model, $params) {
        $model->load($params);
        $paramsStr = '';
        $date = explode(",", $model['create_date']);
        if (count($date) > 1) {
            $dateStart = SDdate::phpThDate2mysqlDate($date[0], "-");
            $dateEnd = SDdate::phpThDate2mysqlDate($date[1], "-");

            $paramsStr .= " AND DATE(zv.visit_date) BETWEEN '{$dateStart}' AND '{$dateEnd}'";
        } else {
            $date = SDdate::phpThDate2mysqlDate($date[0], "-");
            $paramsStr .= " AND DATE(zv.visit_date) = '{$date}'";
        }

        $sql = "SELECT DISTINCT visit_type_name,CONCAT(pf.title,pf.firstname,' ',pf.lastname) AS DoctorFullname,DATE(zv.visit_date) AS visit_date
            , CONCAT(prefix_name,pt_firstname,' ',pt_lastname) AS PtFullname
                FROM zdata_visit_tran zvt
                INNER JOIN zdata_visit zv ON(zv.id=zvt.visit_tran_visit_id)
                INNER JOIN `profile` pf ON(pf.user_id= zvt.visit_tran_doctor) 
                INNER JOIN zdata_patientprofile zpp ON(zpp.id=zv.visit_pt_id )
                LEFT JOIN zdata_prefix zpf ON(zpf.prefix_id=zpp.pt_prefix_id )
                INNER JOIN zdata_visit_type zvtt ON(zvtt.visit_type_code=zv.visit_type)
                WHERE zv.rstat='1' $paramsStr
                ORDER BY visit_date,firstname,visit_type_name";

        $dataProvider = new \yii\data\SqlDataProvider([
            'sql' => $sql,
//            'params' => $paramsArry,
//            'sort' => ['attributes' => ['pt_hn', 'fullname', 'sect_name', 'doctor_name', 'right_name']],
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

        return $dataProvider;
    }

    public static function getReportTypeCount($model, $params) {
        $model->load($params);
        $paramsStr = '';
        $date = explode(",", $model['create_date']);
        if (count($date) > 1) {
            $dateStart = SDdate::phpThDate2mysqlDate($date[0], "-");
            $dateEnd = SDdate::phpThDate2mysqlDate($date[1], "-");

            $paramsStr .= " AND DATE(zv.visit_date) BETWEEN '{$dateStart}' AND '{$dateEnd}'";
        } else {
            $date = SDdate::phpThDate2mysqlDate($date[0], "-");
            $paramsStr .= " AND DATE(zv.visit_date) = '{$date}'";
        }

        $sql = "SELECT visit_type_name,CONCAT(pf.title,pf.firstname,' ',pf.lastname) AS DoctorFullname,COUNT(visit_type_name) AS Amount
                FROM zdata_visit_tran zvt
                INNER JOIN zdata_visit zv ON(zv.id=zvt.visit_tran_visit_id)
                INNER JOIN `profile` pf ON(pf.user_id= zvt.visit_tran_doctor) 
                INNER JOIN zdata_visit_type zvtt ON(zvtt.visit_type_code=zv.visit_type)
                WHERE zv.rstat='1' $paramsStr
                GROUP BY visit_type_name,pf.firstname
                ORDER BY visit_date,firstname,visit_type_name";

        $dataProvider = new \yii\data\SqlDataProvider([
            'sql' => $sql,
//            'params' => $paramsArry,
//            'sort' => ['attributes' => ['pt_hn', 'fullname', 'sect_name', 'doctor_name', 'right_name']],
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

        return $dataProvider;
    }

    public static function getXrayReportCountDoc($model, $params) {
        $model->load($params);
        $paramsStr = '';
        $date = explode(",", $model['create_date']);
        if (count($date) > 1) {
            $dateStart = SDdate::phpThDate2mysqlDate($date[0], "-");
            $dateEnd = SDdate::phpThDate2mysqlDate($date[1], "-");

            $paramsStr .= " AND zx.report_xr_date BETWEEN '{$dateStart} 00:00:00' AND '{$dateEnd} 23:59:59'";
        } else {
            $date = SDdate::phpThDate2mysqlDate($date[0], "-");
            $paramsStr .= " AND zx.report_xr_date = '{$date}'";
        }

        $sql = "SELECT co.order_name AS item_desc,COUNT(co.order_name) AS report_count
                ,CONCAT(pf.title,pf.firstname,' ',pf.lastname) AS doc_fullname
                FROM zdata_order_tran zot 
                INNER JOIN zdata_reportxray zx ON(zx.target = zot.id) 
                INNER JOIN const_order co ON(co.order_code=zot.order_tran_code)
                LEFT JOIN `profile` pf ON(pf.user_id= zx.report_x_resut_docid) 
                WHERE zot.rstat =1 AND zx.report_status = '2' AND zx.rstat='1' $paramsStr
                GROUP BY co.order_name
                ORDER BY pf.firstname,co.order_name";

        $dataProvider = new \yii\data\SqlDataProvider([
            'sql' => $sql,
//            'params' => $paramsArry,
//            'sort' => ['attributes' => ['pt_hn', 'fullname', 'sect_name', 'doctor_name', 'right_name']],
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

        return $dataProvider;
    }

    public static function phpThDate2mysqlDate($phpDate, $delimiter = '/') {
        $arr = explode($delimiter, $phpDate);
        //  print_r(date_parse($phpDate));
        $date = ($arr[2] - 543) . '-' . $arr[1] . '-' . str_replace(' ', '', $arr[0]);
        return $date;
    }

}
