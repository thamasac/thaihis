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
class Order2Query {

    public static function getOrderByVisit($visit_id) {
        $sql = "SELECT zot.*,DATE(zv.visit_date) AS visit_date,zv.visit_type
                FROM zdata_order_tran zot
                INNER JOIN zdata_visit zv ON(zv.id = zot.order_tran_visit_id)
                WHERE zot.rstat =1 AND zot.target=:visit_id AND zot.order_tran_status <> '1' ORDER BY order_tran_code";

        return Yii::$app->db->createCommand($sql, [':visit_id' => $visit_id])->queryAll();
    }

    public static function getDrugGeneric() {
        $sql = "SELECT DISTINCT generic_name,'1' AS drug_status
,zpc.id AS drug_group_id,`กลุ่มยา`
FROM main_item
LEFT JOIN nhis.zdata_pism_class zpc ON(zpc.drug_group_name=TRIM(`กลุ่มยา`))
ORDER BY generic_name";

        return Yii::$app->db_cscd->createCommand($sql)->queryAll();
    }

    public static function getDrugTrade() {
        $sql = "SELECT DISTINCT zpg.id AS generic_id,TradeName_TMT
FROM main_item mi
LEFT JOIN nhis.zdata_pism_generic zpg ON(zpg.generic_name=TRIM(mi.generic_name))";

        return Yii::$app->db_cscd->createCommand($sql)->queryAll();
    }

    public static function getDrugItem() {
        $sql = "SELECT zpg.id AS generic_id,zpg.generic_name,zpt.id AS trad_id,stdtrad_name,mi.ItemName,TMTID_TPU,workingcode,STRENGTH
,SUBSTR(CONTENT,3) AS CONTENT,zpu.id AS content_id,CASE WHEN ISED ='ED' THEN '1' ELSE '2' END AS drug_type,`ราคาขาย/หน่วย` AS price
FROM main_item mi
LEFT JOIN nhis.zdata_pism_generic zpg ON(zpg.generic_name=TRIM(mi.generic_name))
LEFT JOIN nhis.zdata_pism_tradname zpt ON(zpt.stdtrad_name=TRIM(mi.TradeName_TMT))
LEFT JOIN nhis.zdata_pism_unit zpu ON(TRIM(zpu.drug_unit_lexicon)=TRIM(SUBSTR(CONTENT,3)) AND zpu.rstat='1')
GROUP BY zpg.id,zpg.generic_name,zpt.id,stdtrad_name,mi.ItemName,TMTID_TPU,workingcode,STRENGTH
,SUBSTR(CONTENT,3),zpu.id
ORDER BY ItemName,zpg.id";

        return Yii::$app->db_cscd->createCommand($sql)->queryAll();
    }

    public static function getSks() {
        $sql = "SELECT zs.id AS sks_id,ref_no,hn_no,zs.visit_date
FROM zdata_sks zs
INNER JOIN zdata_visit zv ON(zv.id=zs.ref_no)
WHERE zs.visit_date BETWEEN '2018-03-01' AND '2018-05-31'
ORDER BY zv.visit_date";

        return Yii::$app->db->createCommand($sql)->queryAll();
    }

    public static function getOrder() {
        $sql = "SELECT sect_code,sect_name,sect_his_type,zot.id AS zot_id
FROM dept_sect
LEFT JOIN zdata_order_type zot ON(zot.order_type_code=sect_his_type)
WHERE sect_his='1'";

        return Yii::$app->db->createCommand($sql)->queryAll();
    }

}
