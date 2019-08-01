<?php

namespace backend\modules\thaihis\classes;

use appxq\sdii\utils\VarDumper;
use Yii;
use yii\data\SqlDataProvider;

/**
 * OvccaFunc class file UTF-8
 * @author SDII <iencoded@gmail.com>
 * @copyright Copyright &copy; 2015 AppXQ
 * @license http://www.appxq.com/license/
 * @version 1.0.0 Date: 9 ก.พ. 2559 12:38:14
 * @link http://www.appxq.com/
 * @example
 */
class OrderFunc {

    public static function getOrderSearch($colSelect, $colSearch, $params, $filter) {
        $select = ['zog.order_group_name', 'cot.order_type_name', 'zol.external_flag'];
        $addonWhere = '1=1';
        //$addonWhere = "order_group_code <> 'PA' AND order_type_code <> 'D'";
        //เปิดให้หมอเห็น service ไปก่อน
//        if (Yii::$app->user->can('doctor')) {
//            $addonWhere .= " AND cot.order_type_code <> 'S'";
//        }

        foreach ($colSelect as $value) {
            $select[] = $value;
        }

        if ($colSearch) {
//            $addonWhere .= " AND CONCAT(";
//            foreach ($colSearch as $value) {
//                $addonWhere .= $value . ",";
//            }
//            $addonWhere .= "'') LIKE '%{$params['order_name']}%'";
            $addonWhere .= " AND CONCAT(order_code,order_name,order_group_name) LIKE '%{$params['order_name']}%'";
        } else {
            $addonWhere .= " AND CONCAT(order_code,order_name,order_group_name) LIKE '%{$params['order_name']}%'";
        }

        if ($params['group_type']) {
            $addonWhere .= " AND cot.order_type_code='{$params['group_type']}'";
        }

        $query = new \yii\db\Query();
        $query->select($select)
                ->from('zdata_order_lists AS zol')
                ->innerJoin('zdata_order_group AS zog', 'zog.id=zol.group_code')
                ->innerJoin('zdata_order_type AS cot', 'cot.id=zol.group_type')
                ->andWhere(['zol.rstat' => 1])
                ->andWhere($addonWhere)
                ->andWhere(['cot.order_type_code' => $filter])
                ->orderBy('order_count_tran DESC');
//        ->orderBy('order_group_name,order_name');
//        \appxq\sdii\utils\VarDumper::dump($query->createCommand()->rawSql);
        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $dataProvider;
    }

    public static function getOrderTran($visit_id, $oipd_type = "OPD", $order_date = "") {
        $sql = "SELECT  zot.id,zot.order_tran_code,zot.order_tran_status,zol.order_name,zol.group_type,zol.unit_price,order_tran_doctor,
                zwu.unit_name,zot.create_date,zol.order_ezf_id,order_qty,zog.order_group_name,order_type_name,
                #zot.order_tran_pay,zot.order_tran_notpay
                CASE WHEN (zpr.right_code = 'CASH') THEN zot.order_tran_pay + zot.order_tran_notpay ELSE zot.order_tran_pay END AS order_tran_pay,
                CASE WHEN (zpr.right_code = 'CASH') THEN 0 ELSE zot.order_tran_notpay END AS order_tran_notpay,order_tran_cashier_status,
                zot.ptid,zpp.pt_hn,external_flag,CONCAT(title,pf.firstname,' ',pf.lastname) AS doc_fullname
                FROM zdata_order_tran zot
                INNER JOIN zdata_order_header zoh ON(zoh.id=zot.order_header_id)
                INNER JOIN zdata_order_lists zol ON(zol.order_code=zot.order_tran_code) 
                INNER JOIN zdata_patientprofile zpp ON (zpp.id=zot.ptid) 
		LEFT JOIN zdata_patientright AS zpr ON(zpr.right_visit_id=zoh.order_visit_id AND zpr.rstat not in(0,3))
		LEFT JOIN zdata_right zr ON(zr.right_code=zpr.right_code)
                INNER JOIN zdata_working_unit zwu ON(zwu.id=zot.order_tran_dept) 
                INNER JOIN zdata_order_group zog ON(zog.id=zol.group_code)
                INNER JOIN zdata_order_type cot ON(cot.id=zol.group_type)
                LEFT JOIN `profile` pf ON(pf.user_id=zot.order_tran_doctor)                
                WHERE zot.rstat='1' AND zoh.order_visit_id=:visitid 
                AND order_tran_oi_type='$oipd_type' ";
        if ($order_date) {
            $sql .= "AND zot.create_date BETWEEN '$order_date 00:00:00' AND '$order_date 23:59:59'";
        }
        $sql .= "ORDER BY zog.order_group_orderby ASC,zot.create_date DESC";

        $dataProvider = new SqlDataProvider([
            'sql' => $sql,
            'params' => [':visitid' => $visit_id],
            'sort' => ['attributes' => ['order_tran_code', 'order_name', 'full_price', 'create_date']],
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

        return $dataProvider;
    }

    public static function getOrderTranReport($visit_id, $oipd_type = "OPD", $order_date = "", $dept_type = null, $order_tran_status = null) {
        $sql = "SELECT  zot.id,zot.order_tran_code,zot.order_tran_status,zol.order_name,zol.group_type,zol.unit_price,order_tran_doctor,
                zwu.unit_name,zot.create_date,zol.order_ezf_id,order_qty,zog.order_group_name,order_type_name,
                #zot.order_tran_pay,zot.order_tran_notpay
                CASE WHEN (zpr.right_code = 'CASH') THEN zot.order_tran_pay + zot.order_tran_notpay ELSE zot.order_tran_pay END AS order_tran_pay,
                CASE WHEN (zpr.right_code = 'CASH') THEN 0 ELSE zot.order_tran_notpay END AS order_tran_notpay,order_tran_cashier_status,
                zot.ptid,zpp.pt_hn,external_flag,CONCAT(title,pf.firstname,' ',pf.lastname) AS doc_fullname
                FROM zdata_order_tran zot
                INNER JOIN zdata_order_header zoh ON(zoh.id=zot.order_header_id)
                INNER JOIN zdata_order_lists zol ON(zol.order_code=zot.order_tran_code) 
                INNER JOIN zdata_patientprofile zpp ON (zpp.id=zot.ptid) 
		LEFT JOIN zdata_patientright AS zpr ON(zpr.right_visit_id=zoh.order_visit_id)
		LEFT JOIN zdata_right zr ON(zr.right_code=zpr.right_code)
                INNER JOIN zdata_working_unit zwu ON(zwu.id=zot.order_tran_dept) 
                INNER JOIN zdata_order_group zog ON(zog.id=zol.group_code)
                INNER JOIN zdata_order_type cot ON(cot.id=zol.group_type)
                LEFT JOIN `profile` pf ON(pf.user_id=zot.order_tran_doctor)                
                WHERE zot.rstat='1' AND zoh.order_visit_id=:visitid 
                AND order_tran_oi_type='$oipd_type' ";
        if ($order_date) {
            $sql .= " AND zot.create_date BETWEEN '$order_date 00:00:00' AND '$order_date 23:59:59'";
        }
        if ($dept_type) {
            $sql .= " AND cot.order_type_code = '$dept_type' ";
        }
        if ($order_tran_status) {
            $sql .= " AND order_tran_status ='$order_tran_status'";
        }
        $sql .= "ORDER BY zog.order_group_orderby ASC,zot.create_date DESC";
//        VarDumper::dump(Yii::$app->db->createCommand($sql,['visitid'=>$visit_id])->rawSql);
        $query = Yii::$app->db->createCommand($sql, ['visitid' => $visit_id])->queryAll();

        return $query;
    }

    public static function getOrderGroupDate($visit_id) {
        $sql = "SELECT DATE(zot.create_date) AS order_date,order_visit_id
                FROM zdata_order_tran zot
                INNER JOIN zdata_order_header zoh ON(zoh.id=zot.order_header_id) 
                WHERE zot.rstat =1 AND zoh.order_visit_id= :visitid
                AND order_tran_oi_type='IPD'
                GROUP BY DATE(zot.create_date)";

        $dataProvider = new SqlDataProvider([
            'sql' => $sql,
            'params' => [':visitid' => $visit_id],
//            'sort' => ['attributes' => ['order_tran_code', 'order_name', 'full_price', 'create_date']],
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        return $dataProvider;
    }

}
