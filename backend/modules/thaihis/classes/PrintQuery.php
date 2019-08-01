<?php

namespace backend\modules\thaihis\classes;

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
class PrintQuery {

    public static function getXrayReportByid($report_id) {
        $data = (new \yii\db\Query())
                        ->select(["zx.id", "zx.report_xray_result AS result", "zol.order_name AS xray_item_des"
                            , "CONCAT(pf.title,pf.firstname,' ',pf.lastname) AS doc_result", "CONCAT(pf_order.title,pf_order.firstname,' ',pf_order.lastname) AS doc_order"
                            , "pt_sex", "zx.report_xray_date AS result_date", "zot.create_date AS order_date"
                            , "pt_hn", "CONCAT(zp.prefix_name,' ',pt_firstname,' ',pt_lastname) AS fullname", "pt_bdate", "order_group_name", "zwu.unit_name"])
                        ->from("zdata_order_tran zot")
                ->innerJoin("zdata_reportxray zx", "zx.target = zot.id")
                        ->innerJoin("zdata_order_lists zol", "zol.order_code=zot.order_tran_code")
                        ->innerJoin("zdata_order_group zog", "zog.id=zol.group_code")
                        ->innerJoin("zdata_patientprofile zpp", "zpp.id=zot.ptid")
                        ->innerJoin("zdata_prefix zp", "zp.prefix_id=zpp.pt_prefix_id")
                        ->leftJoin("zdata_working_unit zwu", "zwu.id = zot.order_tran_dept")
                        ->leftJoin("`profile` pf", "pf.user_id= zx.report_xray_docid")
                        ->leftJoin("`profile` pf_order", "pf_order.user_id= zot.order_tran_doctor")
                        ->where("zot.rstat =1 AND zx.report_status = '2' AND zx.id = :report_id"
                                , [':report_id' => $report_id])->one();
        
        return $data;
    }

}
