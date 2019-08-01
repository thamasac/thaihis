<?php

namespace backend\modules\thaihis\classes;

use backend\modules\ezforms2\models\Ezform;
use backend\modules\ezforms2\models\EzformFields;
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
class OrderQuery {

    public static function getPtRightLast($pt_id) {
        $query = new \yii\db\Query();
        $data = $query->select('zpr.id AS right_id,zpr.right_code,right_sub_code,right_hos_main,right_refer_start,right_refer_end,right_status,right_prove_end
            ,zr.right_name,right_project_id,right_prove_no')
                        ->from('zdata_patientright zpr')
                        ->innerJoin('zdata_right zr', 'zr.right_code=zpr.right_code')
                        ->where('zpr.rstat=1 AND zpr.ptid=:pt_id', [':pt_id' => $pt_id])
                        ->orderBy('zpr.create_date DESC')->one();

        return $data;
    }

    //old
    /* public static function getOrderCheckStatus($visit_id, $order_code) {
      $query = new \yii\db\Query();
      $data = $query->select('zot.id,zot.order_tran_code,zot.order_tran_status,zot.order_tran_cashier_status')
      ->from('zdata_order_header zoh')
      ->innerJoin('zdata_order_tran zot', 'zot.order_header_id=zoh.id')
      ->where('zot.rstat=1 AND zoh.order_visit_id=:visit_id AND order_tran_code=:order_code'
      , [':visit_id' => $visit_id, ':order_code' => $order_code])->one();

      return $data;
      } */

    public static function getOrderCheckStatus($visit_id) {
        $sql = "SELECT `zot`.`id`,
                `zot`.`order_tran_code`,
                `zot`.`order_tran_status`,
                `zot`.`order_tran_cashier_status`
         FROM `zdata_order_header` `zoh`
         INNER JOIN `zdata_order_tran` `zot` ON zot.order_header_id=zoh.id
         INNER JOIN `zdata_order_lists` `zol` ON zol.order_code=zot.order_tran_code
         INNER JOIN
           (SELECT order_ref
            FROM zdata_health_type
            UNION SELECT order_ref_02
            FROM zdata_health_type
            UNION SELECT order_ref_03
            FROM zdata_health_type
            UNION SELECT order_ref_04
            FROM zdata_health_type
            UNION SELECT order_ref_05
            FROM zdata_health_type
            UNION SELECT order_ref_06
            FROM zdata_health_type
            UNION SELECT order_ref_07
            FROM zdata_health_type
            UNION SELECT order_ref_08
            FROM zdata_health_type) AS item_check_up ON(item_check_up.order_ref=zol.id)
         WHERE (zot.rstat=1
                AND zoh.order_visit_id=:visit_id)
           AND (zot.order_tran_status='1'
                AND zot.order_tran_cashier_status='1')";
        $result = Yii::$app->db->createCommand($sql, [':visit_id' => $visit_id])->queryAll();

        return $result;
    }

    public static function getOrderCounterItem($order_type, $order_status, $visit_id) {
        $data = (new \yii\db\Query())
                        ->select(["zot.id", "zol.order_code", "zol.order_name", "zog.order_group_name"
                            , "order_vender_status", "order_tran_dept", "zoh.ptid", "order_visit_id AS visit_id", "zol.order_ezf_id", "order_qty"
                            , "external_flag", "CONCAT(title,pf.firstname,' ',pf.lastname) AS doc_fullname", "order_tran_doctor"
                            , "zwu.unit_code AS unit_code_order", 'zwu.id AS unit_id_order',
                            "order_tran_oi_type"])
                        ->from("zdata_order_header zoh")
                        ->innerJoin('zdata_order_tran zot', 'zot.order_header_id=zoh.id')
                        ->innerJoin('zdata_order_lists zol', 'zol.order_code=zot.order_tran_code')
                        ->innerJoin('zdata_order_group zog', 'zog.id=zol.group_code')
                        ->innerJoin('zdata_order_type zoty', 'zoty.id=zol.group_type')
                        ->leftJoin('`profile` pf', 'pf.user_id=zot.order_tran_doctor')
                        ->innerJoin('zdata_working_unit zwu', 'zwu.id=zot.order_tran_dept')
                        ->where("zot.rstat =1 AND zoty.order_type_code=:order_type AND zoh.order_visit_id=:visit_id AND zot.order_tran_status=:order_status"
                                , [':order_type' => $order_type, ':order_status' => $order_status, ':visit_id' => $visit_id])->all();
//        \appxq\sdii\utils\VarDumper::dump($data->createCommand()->rawSql);
        return $data;
    }

    public static function getOrderLabNo($visit_id, $order_type_code) {
        $data = (new \yii\db\Query())
                        ->select("MAX(order_vender_no) AS order_vender_no")
                        ->from("zdata_order_header zoh")
                        ->innerJoin('zdata_order_tran zot', 'zot.order_header_id=zoh.id')
                        ->innerJoin('zdata_order_lists zol', 'zol.order_code=zot.order_tran_code')
                        ->innerJoin('zdata_order_type zoty', 'zoty.id=zol.group_type')
                        ->where("zot.rstat =1 AND zoh.order_visit_id=:visit_id AND zoty.order_type_code=:order_type_code", [':visit_id' => $visit_id,
                            ':order_type_code' => $order_type_code,
                        ])->one();

        return $data;
    }

    public static function genNumberVendor($type) {
        $data = (new \yii\db\Query())
                        ->select(["CONCAT(REPLACE(gen_date,'-',''),LPAD(gen_number,3,'0')) AS number"
                            , "gen_number", "gen_date", "id"])
                        ->from("zdata_gen_number")
                        ->where("rstat =1 AND gen_type=:type"
                                , [':type' => $type])->one();

        return $data;
    }

    public static function getHisMapLis($item_code, $ln) {
        $data = (new \yii\db\Query())
                ->select(['*', "CONCAT('$ln') AS ln"])
                ->from("zdata_map_his2lis")
                ->where(['in', 'hiscode', $item_code])
                ->all();

        return $data;
    }

    public static function getOrderTranById($order_id, $order_status) {
        $data = (new \yii\db\Query())
                        ->select(["order_tran_code", "cl.order_name", "order_tran_doctor", "order_modality_type"
                            , "DATE_FORMAT(zot.create_date, '%Y%m%d%H%i%s') AS datetime"
                            , "(SELECT CONCAT(certificate,'^',title,pf.firstname,' ',pf.lastname) AS doctor FROM `profile` pf WHERE pf.user_id=zot.order_tran_doctor) AS doctor"])
                        ->from("zdata_order_tran zot")
                        ->innerJoin("zdata_order_lists cl", "cl.order_code=zot.order_tran_code")
                        ->innerJoin("zdata_order_group cg", "cg.id=cl.group_code")
                        ->where("zot.rstat =1 AND zot.id=:order_id AND zot.order_tran_status=:order_status"
                                , [':order_id' => $order_id, ':order_status' => $order_status])->one();

        return $data;
    }

    public static function getOrderTranFullnameById($order_id) {
        $data = (new \yii\db\Query())
                        ->select(["order_tran_code", "zol.order_name", "CONCAT(zp.prefix_name,' ',pt_firstname,' ',pt_lastname) AS fullname", "pt_bdate"])
                        ->from("zdata_order_tran zot")
                        ->innerJoin("zdata_order_lists zol", "zol.order_code=zot.order_tran_code")
                        ->innerJoin("zdata_patientprofile zpp", "zpp.id=zot.ptid")
                        ->innerJoin("zdata_prefix zp", "zp.prefix_id=zpp.pt_prefix_id")
                        ->where("zot.rstat =1 AND zot.id=:order_id"
                                , [':order_id' => $order_id])->one();

        return $data;
    }

    public static function getOrderType() {
        $data = (new \yii\db\Query())
                        ->select(["order_type_code", "order_type_name"])
                        ->from("zdata_order_type")
                        ->where("rstat='1' AND order_type_status='1'")->all();

        return $data;
    }

    public static function getOrderCounterCyto($model, $params, $dept) {
        $model->load($params);
        $date = \appxq\sdii\utils\SDdate::phpThDate2mysqlDate($model['create_date']);
        $paramsStr = "AND zv.rstat=1 AND zoty.order_type_deptid LIKE '%{$dept}%'"
                . " AND order_tran_status=:order_status AND zv.visit_date BETWEEN :dateST AND :dateEN";

        $paramsArry[':dateST'] = "$date 00:00:00";
        $paramsArry[':dateEN'] = "$date 23:59:59";
        if ($model['order_tran_code']) {
            $paramsStr .= " AND CONCAT(pt_hn,' ',pt_firstname,' ',pt_lastname) LIKE :search_order";
            $paramsArry[':search_order'] = "%{$model['order_tran_code']}%";
        }

        $select = "";
        if ($model['order_tran_status'] == '1') {
            $joinReport = "INNER JOIN zdata_reportcyto zcr ON(zcr.order_tran_id=zot.id)";
            $paramsStr .= " AND zcr.report_status='H'";
            $paramsArry[':order_status'] = '2';
        } elseif ($model['order_tran_status'] == '2') {
            $joinReport = "INNER JOIN zdata_reportcyto zcr ON(zcr.order_tran_id=zot.id)";
            $paramsStr .= " AND zcr.report_status='1'";
            $paramsArry[':order_status'] = $model['order_tran_status'];
        } elseif ($model['order_tran_status'] == '3') {
            $joinReport = "INNER JOIN zdata_reportcyto zcr ON(zcr.order_tran_id=zot.id)";
            $paramsStr .= " AND zcr.report_status='2'";
            $paramsArry[':order_status'] = $model['order_tran_status'];
        }

        $sql = "SELECT DISTINCT zv.id AS order_visitid,pt_hn,CONCAT(IFNULL(prefix_name_cid,''),pt_firstname,' ',pt_lastname) AS fullname
                ,report_status AS order_tran_status,order_type_deptid,zwu.unit_name AS sect_name
                FROM  zdata_visit zv
                INNER JOIN zdata_order_header zoh ON(zoh.order_visit_id=zv.id)
		INNER JOIN zdata_order_tran zot ON(zot.order_header_id=zoh.id) 
                INNER JOIN zdata_working_unit zwu ON(zwu.id=zot.order_tran_dept)
                INNER JOIN zdata_order_lists zol ON(zol.order_code=zot.order_tran_code)
		INNER JOIN zdata_order_type zoty ON(zoty.id=zol.group_type)
                INNER JOIN zdata_patientprofile zpp ON(zpp.id=zv.ptid)
		LEFT JOIN zdata_prefix zpf ON(zpf.prefix_id=zpp.pt_prefix_id) 
                $joinReport
                WHERE zot.rstat='1' $paramsStr
                ORDER BY zot.update_date DESC";

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

    public static function getLabResultLastCr($hn) {
        $sql = "SELECT header_hn,header_ln,result
            FROM lab_result
            INNER JOIN lab_header_result ON(header_ln=ln)
            WHERE header_hn=:hn AND test_code='2003'
            ORDER BY ln DESC";

        return Yii::$app->db_chemo->createCommand($sql, [':hn' => $hn])->queryOne();
    }

}
