<?php

namespace backend\modules\patient\classes;

use Yii;
use backend\modules\ezforms2\classes\EzfQuery;

/**
 * OvccaFunc class file UTF-8
 * @author SDII <iencoded@gmail.com>
 * @copyright Copyright &copy; 2015 AppXQ
 * @license http://www.appxq.com/license/
 * @version 1.0.0 Date: 9 ก.พ. 2559 12:38:14
 * @link http://www.appxq.com/
 * @example 
 */
class CashierQuery {

    public static function getCashierGroupItem($visit_id, $cashier_status, $cashier_id, $unit_id) {
        $sql = "SELECT `zdata_order_fin_group`.`order_fin_code`,
				`zdata_order_fin_group`.`order_fin_name`,
				`zdata_order_header`.`order_visit_id`,
				`zdata_visit`.`visit_date`,
				`zdata_visit`.`visit_type`,
				`zdata_order_fin_type`.`fin_group_code`,
				`zdata_order_fin_type`.`fin_group_name`,
				`zdata_patientright`.`right_code`,
				`zdata_order_header`.`id`,
				`zdata_order_header`.`target`,
				`zdata_order_header`.`ptid`,
				`zdata_order_header`.`xsourcex`,
				`zdata_order_header`.`create_date`,
				`zdata_order_header`.`rstat`,
				`zdata_order_header`.`ezf_version`,
				`zdata_patientright`.`right_project_id`,
				SUM(zdata_order_tran.order_tran_pay) AS pay,
				SUM(zdata_order_tran.order_tran_notpay) AS notpay,`zdata_order_fin_group`.id AS fin_group_id,'ORDER' AS item_type
			FROM `zdata_order_header`
			INNER JOIN `zdata_order_tran` ON `zdata_order_header`.`id` = `zdata_order_tran`.`order_header_id`
			INNER JOIN `zdata_order_lists` ON `zdata_order_lists`.`order_code` = `zdata_order_tran`.`order_tran_code`
			LEFT JOIN `zdata_order_group` ON `zdata_order_group`.`id` = `zdata_order_lists`.`group_code`
			LEFT JOIN `zdata_order_type` ON `zdata_order_type`.`id` = `zdata_order_lists`.`group_type`
			LEFT JOIN `zdata_order_fin_group` ON `zdata_order_fin_group`.`id` = `zdata_order_lists`.`fin_item_code`
			LEFT JOIN `zdata_order_fin_type` ON `zdata_order_fin_type`.`id` = `zdata_order_fin_group`.`order_fin_type_id`
			INNER JOIN `zdata_visit` ON `zdata_visit`.`id` = `zdata_order_header`.`order_visit_id`
                        INNER JOIN `zdata_patientright` ON(`zdata_patientright`.id=(SELECT MAX(id) AS patientright_id FROM zdata_patientright subPtr WHERE subPtr.ptid=zdata_order_header.ptid))
			/*LEFT JOIN `zdata_patientright` ON `zdata_visit`.`id` = `zdata_patientright`.`right_visit_id`*/
WHERE `zdata_order_header`.`rstat` NOT IN(0,3) AND `zdata_order_tran`.`rstat` NOT IN(0,3)
AND (`zdata_visit`.`id`=:visit_id) AND (zdata_order_tran.order_tran_cashier_status=:cashier_status) ";
        if ($unit_id) {
            $sql .= "AND zdata_order_tran.order_tran_dept IN($unit_id) ";
        }
        if ($cashier_id) {
            $sql .= "AND zdata_order_tran.order_tran_cashier_id ='$cashier_id' ";
        }
        $sql .= "GROUP BY `zdata_order_fin_group`.`order_fin_code`, `zdata_order_fin_type`.`fin_group_code` 
UNION 
SELECT `zdata_order_fin_group`.`order_fin_code`, `zdata_order_fin_group`.`order_fin_name`,
 `zdata_pis_order`.`order_visit_id`, `zdata_visit`.`visit_date`, `zdata_visit`.`visit_type`,
 `zdata_order_fin_type`.`fin_group_code`, `zdata_order_fin_type`.`fin_group_name`,
 `zdata_patientright`.`right_code`, `zdata_pis_order`.`id`, `zdata_pis_order`.`target`,
 `zdata_pis_order`.`ptid`, `zdata_pis_order`.`xsourcex`, `zdata_pis_order`.`create_date`,
 `zdata_pis_order`.`rstat`, `zdata_pis_order`.`ezf_version`, 
`zdata_patientright`.`right_project_id`,
SUM(zdata_pis_order_tran.order_tran_pay) as pay,
 SUM(zdata_pis_order_tran.order_tran_notpay) as notpay,`zdata_order_fin_group`.id AS fin_group_id,'DRUG' AS item_type
FROM `zdata_pis_order` 
INNER JOIN `zdata_visit` ON `zdata_visit`.`id` = `zdata_pis_order`.`order_visit_id`
INNER JOIN `zdata_pis_order_tran` ON `zdata_pis_order`.`id` = `zdata_pis_order_tran`.`order_id`
LEFT JOIN `zdata_pism_item` ON `zdata_pism_item`.`id` = `zdata_pis_order_tran`.`order_trad_id`
LEFT JOIN `zdata_order_fin_group` ON `zdata_order_fin_group`.`id` = `zdata_pism_item`.`trad_fin_gruop_id`
INNER JOIN `zdata_patientright` ON `zdata_visit`.`id` = `zdata_patientright`.`right_visit_id`
LEFT JOIN `zdata_order_fin_type` ON `zdata_order_fin_type`.`id` = `zdata_order_fin_group`.`order_fin_type_id`
WHERE `zdata_pis_order_tran`.`rstat` NOT IN(0,3)";
        if ($unit_id) {
            $sql .= "AND zdata_pis_order.order_dept IN($unit_id)";
        }
        if ($cashier_id) {
            $sql .= "AND zdata_pis_order_tran.order_tran_cashier_id ='$cashier_id' ";
        }
        $sql .= " AND (`zdata_visit`.`id`=:visit_id) AND (zdata_pis_order_tran.order_tran_cashier_status=:cashier_status) 
AND (zdata_pis_order_tran.order_tran_status=2) 
GROUP BY `zdata_order_fin_group`.`order_fin_code`, `zdata_order_fin_type`.`fin_group_code`
ORDER BY `order_fin_code`";

        return Yii::$app->db->createCommand($sql, [':visit_id' => $visit_id,
                            ':cashier_status' => $cashier_status])
                        ->queryAll();
    }

    //กรณี Cashier เปิดดูใน Queue ที่ Status=2 จ่ายแล้ว
    public static function getCashierGroupItem2($cashier_id) {
        $sql = "SELECT cof.order_fin_code,cof.order_fin_name,zv.id AS order_visit_id,zv.visit_date,zv.visit_type
,cot.fin_group_code,cot.fin_group_name,zrm.receipt_right_code AS right_code
,SUM(zrt.tran_item_pay) AS pay,SUM(zrt.tran_item_notpay) AS notpay
,'ORDER' AS item_type
FROM zdata_receipt_mas zrm
INNER JOIN zdata_receipt_trn zrt ON(zrt.tran_receipt_id=zrm.id)
INNER JOIN zdata_order_lists col ON(col.order_code=zrt.tran_item_code)
INNER JOIN zdata_order_fin_group cof ON(cof.id=col.fin_item_code)
INNER JOIN zdata_order_fin_type cot ON(cot.id=cof.order_fin_type_id)
INNER JOIN zdata_visit zv ON(zv.id=zrm.receipt_visit_id)
INNER JOIN `zdata_patientright` ON(`zdata_patientright`.id=(SELECT MAX(id) AS patientright_id FROM zdata_patientright subPtr WHERE subPtr.ptid=zrm.ptid))
INNER JOIN zdata_patientprofile vpp ON(vpp.id=zrm.ptid)
LEFT JOIN zdata_prefix zp ON(vpp.pt_prefix_id=zp.prefix_id)
LEFT JOIN `profile` pf ON(pf.user_id= zrm.user_update)
INNER JOIN zdata_order_tran zot ON(zot.order_tran_cashier_id=zrm.id AND zot.order_tran_code=col.order_code)
WHERE zrm.id=:cashier_id AND receipt_status='A'
GROUP BY cof.id
UNION
SELECT cof.order_fin_code,cof.order_fin_name,zv.id AS order_visit_id,zv.visit_date,zv.visit_type
,cot.fin_group_code,cot.fin_group_name,zrm.receipt_right_code AS right_code
,SUM(zrt.tran_item_pay) AS pay,SUM(zrt.tran_item_notpay) AS notpay,'DRUG' AS item_type
                FROM zdata_receipt_mas zrm                
                INNER JOIN zdata_receipt_trn zrt ON(zrt.tran_receipt_id=zrm.id)
                INNER JOIN zdata_order_fin_group cof ON(cof.order_fin_code=zrt.tran_item_code)
                INNER JOIN zdata_order_fin_type cot ON(cot.id=cof.order_fin_type_id)
                INNER JOIN zdata_visit zv ON(zv.id = zrm.receipt_visit_id)
INNER JOIN `zdata_patientright` ON(`zdata_patientright`.id=(SELECT MAX(id) AS patientright_id FROM zdata_patientright subPtr WHERE subPtr.ptid=zrm.ptid))
                INNER JOIN zdata_patientprofile AS vpp ON(vpp.id=zrm.ptid)
                LEFT JOIN zdata_prefix zp ON(vpp.pt_prefix_id=zp.prefix_id)
                LEFT JOIN `profile` pf ON(pf.user_id= zrm.user_update)
                WHERE zrm.rstat=1 AND zrm.id=:cashier_id AND receipt_status='A'
                GROUP BY cof.id
                ORDER BY order_fin_code";
       
        return Yii::$app->db->createCommand($sql, [
                    ':cashier_id' => $cashier_id
                ])->queryAll();
    }

    public static function getCashierOrderDept($visit_id, $cashier_status) {
        $sql = "SELECT DATE(visit_date) AS visit_date,SUM(zot.order_tran_pay) AS pay,SUM(zot.order_tran_notpay) AS notpay
,unit_code,unit_name,zwu.id AS unit_id,zot.ptid,visit_type,visit_date AS visit_datetime
                FROM zdata_order_tran zot
                INNER JOIN zdata_order_header zoh ON(zoh.id=zot.order_header_id) 
                INNER JOIN zdata_visit zv ON(zv.id=zoh.order_visit_id)
                INNER JOIN zdata_working_unit zwu ON(zwu.id=zot.order_tran_dept)
                WHERE zot.rstat =1 AND zoh.order_visit_id= :visit_id
                AND IFNULL(order_tran_cashier_status,'') = :cashier_status 
                GROUP BY zwu.id,visit_date
                UNION ALL 
SELECT DATE(visit_date) AS visit_date ,SUM(zpot.order_tran_pay) AS pay
,SUM(zpot.order_tran_notpay) AS notpay,unit_code,unit_name,zwu.id AS unit_id
                ,zpo.ptid,visit_type,visit_date AS visit_datetime 
                FROM zdata_pis_order zpo 
		INNER JOIN zdata_pis_order_tran zpot ON(zpo.id=zpot.order_id) 
		INNER JOIN zdata_visit zv ON(zv.id=zpo.order_visit_id) 								
                INNER JOIN zdata_working_unit zwu ON(zwu.id=zpo.order_dept)
                WHERE zpo.rstat=1 AND zpot.rstat=1 AND zpot.order_tran_status='2' AND zpo.order_visit_id=:visit_id 
                AND order_tran_cashier_status = :cashier_status   
                GROUP BY zwu.id,visit_date
                ";

        return Yii::$app->db->createCommand($sql, [':visit_id' => $visit_id,
                    ':cashier_status' => $cashier_status])->queryAll();
    }

    public static function getCashierItem($fin_code, $params, $unit_id, $cashier_id) {

        $sql = "SELECT zot.id AS item_id,co.nhso_code,co.order_name,
             zot.order_tran_pay AS pay,zot.order_tran_notpay AS notpay 
                ,zot.ptid,order_visit_id AS visit_id,co.order_code,order_tran_status,'ORDER' AS type,order_qty
                FROM zdata_order_tran zot
                INNER JOIN zdata_order_header zoh ON(zoh.id=zot.order_header_id)
                INNER JOIN zdata_order_lists co ON(co.order_code=zot.order_tran_code)
		/*INNER JOIN zdata_patientright AS zpr ON(zpr.right_visit_id=zoh.order_visit_id)*/
		/*INNER JOIN `zdata_patientright` ON(`zdata_patientright`.id=(SELECT MAX(id) AS patientright_id 
                FROM zdata_patientright subPtr WHERE subPtr.ptid=zoh.ptid))*/
                /*LEFT JOIN zdata_right zr ON(zr.right_code=zpr.right_code)*/
                INNER JOIN zdata_order_fin_group cof ON(cof.id=co.fin_item_code)
                INNER JOIN zdata_order_fin_type cot ON(cot.id=cof.order_fin_type_id)
                WHERE zot.rstat =1 AND zoh.order_visit_id=:visit_id AND order_fin_code=:fin_code
                AND order_tran_cashier_status = :cashier_status ";
        if ($unit_id) {
            $sql .= " AND zot.order_tran_dept IN($unit_id)";
        }
        if ($cashier_id) {
            $sql .= "AND zot.order_tran_cashier_id ='$cashier_id' ";
        }

        return Yii::$app->db->createCommand($sql, [':visit_id' => $params['visitid'],
                    ':fin_code' => $fin_code,
                    ':cashier_status' => $params['cashier_status']])->queryAll();
    }

    public static function getCashierItemStatus2($fin_code, $cashier_id) {

        $sql = "SELECT zrt.id AS item_id,co.nhso_code,co.order_name,zrt.tran_item_pay AS pay,zrt.tran_item_notpay AS notpay
,zrt.ptid,zrm.receipt_visit_id AS visit_id,co.order_code,'2' AS order_tran_status,'ORDER' AS type,order_qty
                FROM zdata_receipt_mas zrm   								
                INNER JOIN zdata_receipt_trn zrt ON(zrt.tran_receipt_id=zrm.id)
		INNER JOIN zdata_order_tran zot ON(zot.order_tran_cashier_id=zrm.id AND zot.order_tran_code=zrt.tran_item_code)
                INNER JOIN zdata_order_lists co ON(co.order_code=zrt.tran_item_code)		
                INNER JOIN zdata_order_fin_group cof ON(cof.id=co.fin_item_code)
                INNER JOIN zdata_order_fin_type cot ON(cot.id=cof.order_fin_type_id)
                WHERE zrt.rstat =1 AND zrm.id=:cashier_id
                AND order_fin_code=:fin_code";
        
        return Yii::$app->db->createCommand($sql, [
                    ':fin_code' => $fin_code,
                    ':cashier_id' => $cashier_id
                ])->queryAll();
    }

    public static function getCashierItem2($visit_id, $fin_code, $params, $itemConfig) {

        $fields = isset($itemConfig[0]['fields']) ? $itemConfig[0]['fields'] : '';
        $forms = isset($itemConfig[0]['refform']) ? $itemConfig[0]['refform'] : '';
        $begin_form = isset($itemConfig[0]['ezf_id']) ? $itemConfig[0]['ezf_id'] : '';
        $cashierStatus = isset($itemConfig[0]['cashier_status']) ? $itemConfig[0]['cashier_status'] : '';
        $receipt_id = isset($itemConfig[0]['receipt_id']) ? $itemConfig[0]['receipt_id'] : '';
        $visit_field = isset($itemConfig[0]['visit_field']) ? $itemConfig[0]['visit_field'] : '';
        $order_fin_code = isset($itemConfig[0]['order_fin_code']) ? $itemConfig[0]['order_fin_code'] : '';
        $summarys = isset($itemConfig[0]['summarys']) ? $itemConfig[0]['summarys'] : '';
        $selects = isset($itemConfig[0]['selects']) ? $itemConfig[0]['selects'] : [];
        $ezform = EzfQuery::getEzformOne($begin_form);
        $modelFilter[] = $visit_field . "='" . $visit_id . "'";
        $modelFilter[] = $order_fin_code . "='" . $fin_code . "'";
        $modelFilter[] = $cashierStatus . "='" . $params['cashier_status'] . "'";
        $modelFilter[] = $receipt_id . "='" . $params['receipt_id'] . "'";
        $customSelect = [];
        foreach ($selects as $val) {
            if (isset($val['field']) && $val['field'] != '')
                $customSelect[] = $val['field'] . " as " . $val['alias_name'];
            else if (isset($val['custom_val']) && $val['custom_val'] != '')
                $customSelect[] = "CONCAT('" . $val['custom_val'] . "') as " . $val['alias_name'];
        }

        $reponseQuery = \backend\modules\pis\classes\PisQuery::getDynamicQuery($fields, $forms, $ezform, null, $summarys, null, $customSelect, $modelFilter);

        return $reponseQuery['data'];
    }

    public static function getCashierItemDrug($fin_code, $params, $unit_id) {

        $sql = "SELECT zpot.id AS item_id,order_tran_status,cof.order_fin_name AS order_name
            ,cof.order_fin_code AS nhso_code,cof.order_fin_code AS order_code,1 AS order_qty
            ,SUM(zpot.order_tran_pay) AS pay,SUM(zpot.order_tran_notpay) AS notpay,'DURG' AS type
            ,0 AS countdatagroup
                FROM zdata_pis_order zpo 
                INNER JOIN zdata_pis_order_tran zpot ON(zpo.id=zpot.order_id) 
                INNER JOIN zdata_pism_item zpi ON(zpi.id=zpot.order_trad_id)  
                INNER JOIN zdata_order_fin_group cof ON(cof.id=zpi.trad_fin_gruop_id)
                WHERE zpot.rstat= '1' AND zpot.rstat= '1' AND zpot.order_tran_status=:order_tran_status 
                AND zpo.order_visit_id=:visit_id AND cof.order_fin_code=:fin_code
                AND zpot.order_tran_cashier_status = :cashier_status 
                AND zpot.order_tran_cashier_id = :receipt_id ";
        if ($unit_id) {
            $sql .= " AND zpo.order_dept IN($unit_id)";
        }
        $sql .= "GROUP BY cof.order_fin_code,cof.order_fin_name";

        return Yii::$app->db->createCommand($sql, [':visit_id' => $params['visitid'], ':fin_code' => $fin_code
                    , ':cashier_status' => $params['cashier_status']
                    , ':receipt_id' => $params['receipt_id']
                    , ':order_tran_status' => '2'])->queryAll(); //order_tran_status=2 status 2 คือห้องยาจ่ายยาแล้ว
    }

    //กรณี Cashier เปิดดูใน Queue ที่ Status จ่ายแล้ว
    public static function getCashierItemDrug2($fin_code, $cashier_id) {

        $sql = "SELECT zrt.id AS item_id,'2' AS order_tran_status,cof.order_fin_name AS order_name
            ,cof.order_fin_code AS nhso_code,cof.order_fin_code AS order_code,1 AS order_qty
            ,zrt.tran_item_pay AS pay,zrt.tran_item_notpay AS notpay,'DURG' AS type
            ,0 AS countdatagroup
                FROM zdata_receipt_mas zrm
		INNER JOIN zdata_receipt_trn zrt ON(zrt.tran_receipt_id=zrm.id) 
                LEFT JOIN zdata_order_fin_group cof ON(cof.order_fin_code=zrt.tran_item_code)
                WHERE zrm.rstat not in(0,3) AND zrt.rstat not in(0,3) AND zrm.id = :receipt_id
                AND cof.order_fin_code=:fin_code";

        return Yii::$app->db->createCommand($sql, [
                    ':fin_code' => $fin_code,
                    ':receipt_id' => $cashier_id
                ])->queryAll();
    }

    public static function getReceiptGroupDetail($receipt_id, $order_fin_code, $right_code = null) {
        $subsql = "";
        if (!in_array($right_code, ['ORI', 'ORI-G', 'CASH', null])) {
            $subsql = "AND zrt.tran_item_pay > 0";
        }
        $sql = "SELECT cof.order_fin_name,SUM(zrt.tran_item_pay) AS pay,SUM(zrt.tran_item_notpay) AS notpay,
                count(tran_receipt_id) AS countdatagroup
                FROM zdata_receipt_mas zrm
                INNER JOIN zdata_receipt_trn zrt ON(zrt.tran_receipt_id=zrm.id)
                INNER JOIN zdata_order_lists col ON(col.order_code=zrt.tran_item_code)
INNER JOIN zdata_order_fin_group cof ON(cof.id=col.fin_item_code)
INNER JOIN zdata_order_fin_type cot ON(cot.id=cof.order_fin_type_id)
                WHERE zrm.id=:receipt_id AND receipt_status='A' 
                AND cof.order_fin_code = :order_fin_code $subsql
                GROUP BY cof.order_fin_name
                ORDER BY cof.order_fin_name";
        return Yii::$app->db->createCommand($sql, [':receipt_id' => $receipt_id, ':order_fin_code' => $order_fin_code])->queryOne();
    }

    public static function getReceiptDetail($receipt_id, $right_code = null) {
        $subsql = "";
        if (!in_array($right_code, ['ORI', 'ORI-G', 'CASH', null])) {
            $subsql = "AND zrt.tran_item_pay > 0";
        }

        $sql = "SELECT LPAD(zrm.book_no, 3, '0') AS book_no,LPAD(zrm.book_num, 3, '0') AS book_num,DATE(zrm.create_date) AS receipt_date
,CONCAT(prefix_name_cid,pt_firstname,' ',pt_lastname) AS fullname,col.nhso_code,col.order_name,zrt.tran_item_pay,zrt.tran_item_notpay
,zrm.id AS receipt_id,cof.order_fin_name,cof.order_fin_code,vpp.pt_hn,receipt_type_mony,receipt_reciveMony,receipt_tronmony,cot.fin_group_code
,visit_date,visit_type,zrm.receipt_right_code AS right_code,zrm.create_date,CONCAT(title,pf.firstname,' ',pf.lastname) AS cashier_fullname
,DATE(zv.visit_date) AS visit_date,zot.id AS order_tran_id
FROM zdata_receipt_mas zrm
INNER JOIN zdata_receipt_trn zrt ON(zrt.tran_receipt_id=zrm.id)
INNER JOIN zdata_order_lists col ON(col.order_code=zrt.tran_item_code)
INNER JOIN zdata_order_fin_group cof ON(cof.id=col.fin_item_code)
INNER JOIN zdata_order_fin_type cot ON(cot.id=cof.order_fin_type_id)
INNER JOIN zdata_visit zv ON(zv.id=zrm.receipt_visit_id)
/*INNER JOIN zdata_patientright zpr ON(zpr.right_visit_id=zv.id)*/
INNER JOIN zdata_patientprofile vpp ON(vpp.id=zrm.ptid)
LEFT JOIN zdata_prefix zp ON(vpp.pt_prefix_id=zp.prefix_id)
LEFT JOIN `profile` pf ON(pf.user_id= zrm.user_update)
INNER JOIN zdata_order_tran zot ON(zot.order_tran_cashier_id=zrm.id AND zot.order_tran_code=col.order_code)
WHERE zrm.id=:receipt_id AND receipt_status='A' $subsql
                UNION
                SELECT LPAD(zrm.book_no, 3, '0') AS book_no,LPAD(zrm.book_num, 3, '0') AS book_num,DATE(zrm.create_date) AS receipt_date
,CONCAT(prefix_name_cid,pt_firstname,' ',pt_lastname) AS fullname,'' AS nhso_code,cof.order_fin_name AS order_name
,zrt.tran_item_pay,zrt.tran_item_notpay,zrm.id AS receipt_id,cof.order_fin_name,cof.order_fin_code
,pt_hn,receipt_type_mony,receipt_reciveMony,receipt_tronmony,cot.fin_group_code,visit_date,visit_type
                ,zrm.receipt_right_code AS right_code,zrm.create_date,CONCAT(title,pf.firstname,' ',pf.lastname) AS cashier_fullname
                ,DATE(zv.visit_date) AS visit_date,'3' AS order_tran_id
                FROM zdata_receipt_mas zrm                
                INNER JOIN zdata_receipt_trn zrt ON(zrt.tran_receipt_id=zrm.id)
                INNER JOIN zdata_order_fin_group cof ON(cof.order_fin_code=zrt.tran_item_code)
INNER JOIN zdata_order_fin_type cot ON(cot.id=cof.order_fin_type_id)
INNER JOIN zdata_visit zv ON(zv.id = zrm.receipt_visit_id)
                /*INNER JOIN zdata_patientright AS zpr ON(zpr.right_visit_id=zv.id)*/
                INNER JOIN zdata_patientprofile AS vpp ON(vpp.id=zrm.ptid)
                LEFT JOIN zdata_prefix zp ON(vpp.pt_prefix_id=zp.prefix_id)
                LEFT JOIN `profile` pf ON(pf.user_id= zrm.user_update)
                WHERE zrm.rstat=1 AND zrm.id=:receipt_id AND receipt_status='A' $subsql
                ORDER BY order_fin_code,nhso_code";
        return Yii::$app->db->createCommand($sql, [':receipt_id' => $receipt_id])->queryAll();
    }

    public static function getReceiptRightShow($receipt_id) {
        $sql = "SELECT zrm.book_no,zrm.book_num,zr.right_name,zp.project_name,zr.right_code
                FROM zdata_receipt_mas zrm
                INNER JOIN zdata_right zr ON(zr.right_code=zrm.receipt_right_code)
                LEFT JOIN zdata_project zp ON(zp.id=zrm.receipt_project_id)
                WHERE zrm.id =:receipt_id";
        return Yii::$app->db->createCommand($sql, [':receipt_id' => $receipt_id])->queryOne();
    }

    public static function getCashierQue2($model) {
        $paramsStr = " AND DATE(zv.visit_date) = :date";
        $paramsArry[':date'] = $model['create_date'];
        if ($model['order_tran_code']) {
            $paramsStr .= " AND CONCAT(pt_hn,' ',fullname) LIKE :search_order";
            $paramsArry[':search_order'] = "%{$model['order_tran_code']}%";
        }
        if ($model['order_tran_status'] == '1') {
            $paramsStr .= " AND order_tran_cashier_status = ''";
        } elseif ($model['order_tran_status'] == '2') {
            $paramsStr .= " AND order_tran_cashier_status <> ''";
        }

        $nonimg = Yii::getAlias('@storageUrl/images') . '/nouser.png';
        $img = Yii::getAlias('@storageUrl/ezform/fileinput') . '/';
        $sql = "SELECT DISTINCT * FROM (SELECT pt_hn,fullname,right_name,zv.id AS visit_id
                ,zr.right_code,IFNULL(CONCAT('$img',vpp.pt_pic),'$nonimg')  AS pt_pic,order_tran_cashier_status,visit_type_name,zv.visit_date
                FROM zdata_order_tran zot
                INNER JOIN zdata_order_header zoh ON(zoh.id=zot.order_header_id) 
                INNER JOIN zdata_visit zv ON(zv.id=zoh.order_visit_id)
                INNER JOIN zdata_visit_type zvt ON(zv.visit_type=zvt.visit_type_code)
                INNER JOIN vpatient_profile AS vpp ON(vpp.pt_id=zv.ptid)
                INNER JOIN zdata_patientright AS zpr ON(zpr.id=(SELECT MAX(id) FROM zdata_patientright WHERE rstat='1' AND right_pt_id=zv.ptid))
                INNER JOIN zdata_right zr ON(zr.right_code=zpr.right_code)
                WHERE zot.rstat= '1' $paramsStr) AS KK
                ORDER BY visit_date DESC";

        return Yii::$app->db->createCommand($sql, $paramsArry)->queryAll();
    }

    public static function getReceiptNo($user_id) {
        $sql = "SELECT id AS receipt_no_id,receipt_book_no,receipt_tr_no AS receipt_tr_no
                FROM zdata_receipt_no
                WHERE rstat=1 AND receipt_user_id=:user_id";
        return Yii::$app->db->createCommand($sql, [':user_id' => $user_id])->queryOne();
    }

}
