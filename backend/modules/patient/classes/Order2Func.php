<?php

namespace backend\modules\patient\classes;

use Yii;
use backend\modules\patient\classes\PatientQuery;
use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\ezforms2\classes\EzfFunc;
use backend\modules\ezforms2\models\EzformFields;
use backend\modules\ezforms2\classes\EzfUiFunc;
use backend\modules\ezforms2\models\TbdataAll;
use appxq\sdii\helpers\SDHtml;

/**
 * OvccaFunc class file UTF-8
 * @author SDII <iencoded@gmail.com>
 * @copyright Copyright &copy; 2015 AppXQ
 * @license http://www.appxq.com/license/
 * @version 1.0.0 Date: 9 ก.พ. 2559 12:38:14
 * @link http://www.appxq.com/
 * @example 
 */
class Order2Func {

    public static function getOrderCounter($model, $params, $dept) {
        $nonimg = Yii::getAlias('@storageUrl/images') . '/nouser.png';
        $img = Yii::getAlias('@storageUrl/ezform/fileinput') . '/';
        $model->load($params);
        if (Yii::$app->user->identity->profile->attributes['position'] == '2') {
            $model['order_tran_status'] = ($model['order_tran_status'] == '1' ? '2' : $model['order_tran_status']);
        }
        $date = \appxq\sdii\utils\SDdate::phpThDate2mysqlDate($model['create_date']);
        $paramsStr = 'AND group_type=:dept AND order_tran_status=:order_status AND DATE(zv.visit_date) = :date';

        $paramsArry[':dept'] = $dept;
        $paramsArry[':date'] = $date;
        $paramsArry[':order_status'] = $model['order_tran_status'];
        if ($model['order_tran_code']) {
            $paramsStr .= " AND CONCAT(pt_hn,' ',zp.prefix_name,pt_firstname,' ',pt_lastname) LIKE :search_order";
            $paramsArry[':search_order'] = "%{$model['order_tran_code']}%";
        }

        $sql = "SELECT DISTINCT zv.id,zpp.id AS pt_id,CONCAT(zp.prefix_name_cid,pt_firstname,' ',pt_lastname) AS fullname,zpp.pt_hn,zv.visit_type
            ,IFNULL(CONCAT('$img',zpp.pt_pic), '$nonimg') AS pt_pic
            ,pt_bdate,visit_type_name
                FROM zdata_order_tran zot 
                INNER JOIN const_order co ON(co.order_code=zot.order_tran_code) 
                INNER JOIN zdata_visit zv ON(zv.id=zot.order_tran_visit_id AND zv.rstat=1)
                INNER JOIN zdata_visit_type zvtt ON(zvtt.visit_type_code=zv.visit_type)
                INNER JOIN zdata_patientprofile zpp ON (zpp.id=zot.ptid)
                LEFT JOIN zdata_prefix zp ON (zp.prefix_id=zpp.pt_prefix_id)
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

    public static function getOrderHistoryVisit($model, $dept) {
        $paramsStr = "zot.rstat='1' AND group_type=:dept AND zv.target=:pt_id";
        $paramsArry[':dept'] = $dept;
        $paramsArry[':pt_id'] = $model['target'];

        if ($model['visit_date']) {
            $date = explode(",", $model['visit_date']);
            if (count($date) > 1) {
                $stDate = date('Y-m-d', strtotime($date[0]));
                $enDate = date('Y-m-d', strtotime($date[1]));

                $paramsStr .= " AND visit_date BETWEEN '{$stDate} 00:00:00' AND '{$enDate} 23:59:59'";
            }
        }

        $sql = "SELECT DISTINCT DATE(visit_date) AS visit_date,visit_type,zv.id,zv.visit_app_id
                FROM zdata_order_tran zot 
                INNER JOIN const_order co ON(co.order_code=zot.order_tran_code) 
                INNER JOIN zdata_visit zv ON(zv.id=zot.order_tran_visit_id AND zv.rstat=1)
                WHERE $paramsStr
                ORDER BY zv.visit_date DESC";

        $dataProvider = new \yii\data\SqlDataProvider([
            'sql' => $sql,
            'params' => $paramsArry,
        ]);

        return $dataProvider;
    }

}
