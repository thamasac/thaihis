<?php

namespace backend\modules\patient\classes;

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
class CashierFunc {

    public static function getCashierQue($model, $params) {
        $model->load($params);
        $paramsStr = " AND (zv.visit_date BETWEEN :dateST AND :dateEN OR visit_date=:date)";
        $paramsArry[':dateST'] = "{$model['create_date']} 00:00:00";
        $paramsArry[':dateEN'] = "{$model['create_date']} 23:59:59";
        $paramsArry[':date'] = $model['create_date'];
        if ($model['order_tran_code']) {
            $paramsStr .= " AND CONCAT(pt_hn,' ',CONCAT(zp.prefix_name,pt_firstname,' ',pt_lastname)) LIKE :search_order";
            $paramsArry[':search_order'] = "%{$model['order_tran_code']}%";
        }
        if ($model['order_tran_status'] == '1') {
            $paramsStr .= " AND IFNULL(order_tran_cashier_status,'') = ''";
        } elseif ($model['order_tran_status'] == '2') {
            $paramsStr .= " AND IFNULL(order_tran_cashier_status,'') <> ''";
        }

        $nonimg = Yii::getAlias('@storageUrl/images') . '/nouser.png';
        $img = Yii::getAlias('@storageUrl/ezform/fileinput') . '/';
        $sql = "SELECT DISTINCT * FROM (SELECT pt_hn,CONCAT(zp.prefix_name_cid,pt_firstname,' ',pt_lastname) AS fullname,'' as right_name,zv.id AS visit_id
,'' as right_code ,IFNULL(CONCAT('$img',zpp.pt_pic),'$nonimg')  AS pt_pic,order_tran_cashier_status,visit_type_name,zv.visit_date
FROM zdata_visit zv 
INNER JOIN zdata_visit_type zvt ON(zv.visit_type=zvt.visit_type_code)
INNER JOIN zdata_order_header zoh ON(zoh.order_visit_id=zv.id) 
INNER JOIN zdata_order_tran zot ON(zot.order_header_id=zoh.id)
INNER JOIN zdata_patientprofile zpp ON (zpp.id=zot.ptid)
LEFT JOIN zdata_prefix zp ON (zp.prefix_id=zpp.pt_prefix_id)
#INNER JOIN zdata_patientright AS zpr ON(zpr.id=(SELECT MAX(id) FROM zdata_patientright WHERE rstat='1' AND right_pt_id=zv.ptid))
#INNER JOIN zdata_right zr ON(zr.right_code=zpr.right_code)
WHERE zot.rstat= '1' AND order_tran_code <> 'BC114' $paramsStr
/*UNION ALL 
SELECT pt_hn,fullname,right_name,zv.id AS visit_id
,zr.right_code,IFNULL(CONCAT('$img',vpp.pt_pic),'$nonimg')  AS pt_pic,order_tran_cashier_status,visit_type_name,zv.visit_date
FROM zdata_pis_order2 zpo  
INNER JOIN zdata_pis_order_tran2 zpot ON(zpo.id=zpot.order_id) 
INNER JOIN zdata_visit zv ON(zv.id=zpo.order_visit_id)
INNER JOIN zdata_visit_type zvt ON(zv.visit_type=zvt.visit_type_code)
INNER JOIN vpatient_profile AS vpp ON(vpp.pt_id=zv.ptid)
INNER JOIN zdata_patientright AS zpr ON(zpr.id=(SELECT MAX(id) FROM zdata_patientright WHERE rstat= '1 ' AND right_pt_id=zv.ptid))
INNER JOIN zdata_right zr ON(zr.right_code=zpr.right_code)
WHERE zpo.rstat=  '1' AND zpot.rstat=  '1' AND zpot.order_tran_status='2' $paramsStr*/
) AS KK
ORDER BY visit_date DESC";

        $dataProvider = new \yii\data\SqlDataProvider([
            'sql' => $sql,
            'params' => $paramsArry,
            'pagination' => [
                'pageSize' => 15,
            ],
        ]);

        return $dataProvider;
    }

    public static function km4FingroupToDetail($array) {
        $result[0]['id'] = \appxq\sdii\utils\SDUtility::getMillisecTime();
        $result[0]['order_tran_status'] = $array['order_tran_status'];
        $result[0]['sks_code'] = $array['fin_item_code'];
        $result[0]['order_code'] = $array['fin_item_code'];
        $result[0]['order_name'] = $array['fin_item_code'];
        $result[0]['order_qty'] = '1';
        $result[0]['pay'] = $array['pay'];
        $result[0]['notpay'] = $array['notpay'];
        $result[0]['type'] = 'DRUG';

        return $result;
    }

}
