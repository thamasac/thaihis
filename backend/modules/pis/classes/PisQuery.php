<?php

namespace backend\modules\pis\classes;

use Yii;
use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\thaihis\classes\ThaiHisQuery;
use backend\modules\thaihis\classes\ThaiHisFunc;
use backend\modules\ezforms2\models\TbdataAll;

/**
 * OvccaFunc class file UTF-8
 * @author SDII <iencoded@gmail.com>
 * @copyright Copyright &copy; 2015 AppXQ
 * @license http://www.appxq.com/license/
 * @version 1.0.0 Date: 9 ก.พ. 2559 12:38:14
 * @link http://www.appxq.com/
 * @example 
 */
class PisQuery {

    public static function getOrderTran($order_id, $order_status = NUll) {
        $paramsArry[':order_id'] = $order_id;
        $paramsStr = "AND zpo.id=:order_id";
        if ($order_status) {
            $paramsArry[':order_status'] = $order_status;
            $paramsStr .= " AND zpot.order_tran_status=:order_status";
        }

        $sql = "SELECT zpot.id
                ,CONCAT(zpue.drug_use_name,' ',zpot.order_tran_pertime,' ',zpu.drug_unit_name,' ',zptf.drug_timeframe_name) AS label_1
                ,CONCAT(CASE WHEN zptu.drug_usetime_lexicon <> 'none' THEN zptu.drug_usetime ELSE '' END,' '
                ,CASE WHEN zpot.order_tran_timezone_1='1' THEN 'เช้า,' ELSE '' END
                ,CASE WHEN zpot.order_tran_timezone_2='1' THEN 'กลางวัน,' ELSE '' END
                ,CASE WHEN zpot.order_tran_timezone_3='1' THEN 'เย็น,' ELSE '' END
                ,CASE WHEN zpot.order_tran_timezone_4='1' THEN 'ก่อนนอน,' ELSE '' END) AS label_2 
                ,zpot.order_tran_note
                ,zpi.trad_itemname AS item_name
                ,CONCAT(zpg.generic_name,' ',zpi.trad_strength) AS item_name_sticker
                ,unit_price,zpot.order_tran_label,zpot.order_tran_notpay,zpot.order_tran_pay,zpot.order_tran_qty,zpo.ptid AS pt_id
                ,DATE(zpot.update_date) AS receive_date,generic_info,zpi.trad_content
                ,CONCAT(title,pf.firstname,' ',pf.lastname) AS doctor_name,certificate,
                zwu.unit_name AS sect_name,zpo.order_visit_id,zpo.id AS order_id
                ,zpo.order_no,pt_hn,CONCAT(zpf.prefix_name_cid,pt_firstname,' ',pt_lastname) AS fullname,right_name,pt_bdate
                ,CASE WHEN generic_type='1' THEN 'ยา' ELSE 'เวชภัณฑ์' END AS generic_type,zpi.trad_tmt
                ,order_tran_ned,zpi.trad_itemname
                ,(SELECT di_txt FROM zdata_dt WHERE rstat not in(0,3) AND di_visit_id=zpo.order_visit_id 
ORDER BY update_date DESC LIMIT 1) AS di_txt,(SELECT di_icd10 FROM zdata_dt WHERE rstat not in(0,3) AND di_visit_id=zpo.order_visit_id 
ORDER BY update_date DESC LIMIT 1) AS di_icd10,
order_tran_chemo_cal,order_tran_chemo_amount,order_tran_chemo_result
                FROM zdata_pis_order zpo
                INNER JOIN zdata_pis_order_tran zpot ON(zpo.id=zpot.order_id)
                INNER JOIN zdata_pism_item zpi ON(zpi.id=zpot.order_trad_id)
                INNER JOIN zdata_pism_generic zpg ON(zpg.id=zpi.trad_generic_id)
		LEFT JOIN zdata_pism_unit zpu ON(zpu.id=zpot.order_tran_unit_id)
		LEFT JOIN zdata_pism_use zpue ON(zpot.order_tran_use_id=zpue.id)
                LEFT JOIN zdata_pism_timeframe zptf ON(zpot.order_tran_timeframe_id=zptf.id)
                LEFT JOIN zdata_pism_timeuse zptu ON(zpot.order_tran_usetime_id=zptu.id)
                LEFT JOIN `profile` pf ON(pf.user_id=zpo.order_doctor_id)
                INNER JOIN zdata_working_unit zwu ON(zwu.id=zpo.order_dept)
                INNER JOIN zdata_patientprofile zpp ON(zpp.id=zpo.ptid)
LEFT JOIN zdata_prefix zpf ON(zpf.prefix_id=zpp.pt_prefix_id)
INNER JOIN zdata_patientright AS zpr ON(zpr.right_visit_id=zpo.order_visit_id AND zpr.rstat not in(0,3))
LEFT JOIN zdata_right zr ON(zr.right_code=zpr.right_code) 
                WHERE zpo.rstat='1' AND zpot.rstat='1' AND zpo.order_status='2'
                 $paramsStr";

        return Yii::$app->db->createCommand($sql, $paramsArry)->queryAll();
    }

    public static function getItem($item_id) {
        $sql = "SELECT zpi.trad_itemname AS trad_name 
                FROM zdata_pism_item zpi
                WHERE zpi.rstat= '1' AND zpi.id=:item_id";

        return Yii::$app->db->createCommand($sql, [':item_id' => $item_id])->queryOne();
    }

    public static function getUseSet($item_id, $generic_id) {
        $sql = "SELECT zpus.id AS useset_id,CONCAT('Sig : ',zpus.use_pertime, ' ',zpu.drug_unit_lexicon, ' ',zpue.drug_use_lexicon, ' ' 
,zptf.drug_timeframe_lexicon, ' ',CASE WHEN zptu.drug_usetime_lexicon <>  'none' THEN zptu.drug_usetime_lexicon ELSE  ' ' END) AS drug_use,use_active
                FROM zdata_pis_use_set zpus
                LEFT JOIN zdata_pism_unit zpu ON(zpus.use_unit_id=zpu.id) 
                LEFT JOIN zdata_pism_use zpue ON(zpus.use_use_id=zpue.id) 
                LEFT JOIN zdata_pism_timeframe zptf ON(zpus.use_timeframe_id=zptf.id) 
                LEFT JOIN zdata_pism_timeuse zptu ON(zpus.use_usetime_id=zptu.id)
                 WHERE zpus.rstat='1' AND zpus.use_item_id=:item_id AND zpus.use_generic_id=:generic_id ";

        return Yii::$app->db->createCommand($sql, [':item_id' => $item_id, ':generic_id' => $generic_id])->queryAll();
    }

    public static function updateItemActive($generic_id, $item_id) {
        $sql = "UPDATE zdata_pis_use_set SET use_active=NULL "
                . " WHERE use_generic_id=:generic_id AND use_item_id=:item_id";

        Yii::$app->db->createCommand($sql, [':generic_id' => $generic_id, ':item_id' => $item_id])->execute();
    }

    public static function getDrugGroup() {
        $sql = "SELECT DISTINCT zpc.id AS drug_id,CONCAT(drug_group_name
                ,CASE WHEN drug_group_parent <> '' THEN CONCAT(' (',drug_group_parent,')') ELSE '' END) AS drug_group_name
                FROM zdata_pism_class zpc
                INNER JOIN zdata_pism_generic zpg ON(zpg.generic_class_id=zpc.id)
                WHERE zpc.rstat='1'
                ORDER BY zpc.drug_group_parent,drug_group_name ASC";

        return Yii::$app->db->createCommand($sql)->queryAll();
    }

    public static function getDrugOrderCount($date) {
        $paramsStr = " AND zv.visit_date BETWEEN '{$date} 00:00:00' AND '{$date} 23:59:59' ";
        $sql = "SELECT order_trad_id,zpot.user_create,COUNT(order_trad_id) AS c_item
                FROM zdata_visit zv
                INNER JOIN zdata_pis_order zpo ON(zpo.order_visit_id=zv.id)
                INNER JOIN zdata_pis_order_tran zpot ON(zpot.order_id=zpo.id)
                WHERE zpot.order_tran_status='2' $paramsStr
                GROUP BY order_trad_id,user_create";

        return Yii::$app->db->createCommand($sql)->queryAll();
    }

    public static function getDrugAllergyShow($pt_id) {
        $sql = "SELECT all_drug_type,ec.ezf_choicelabel,GROUP_CONCAT(CONCAT(drug_allergy,' ',IFNULL(all_note,'')) SEPARATOR ' ,') AS drug_allergy,all_id FROM
                (SELECT zpa.all_drug_type
                ,CASE zpa.all_drug_type WHEN '1' THEN zpi.trad_itemname
                WHEN '2' THEN zpg.generic_name
                WHEN '3' THEN zpc.drug_group_name END AS drug_allergy,zpa.id AS all_id,all_note
                FROM zdata_pis_allergy zpa
                LEFT JOIN zdata_pism_item zpi ON(zpi.id=zpa.all_drug_itemid)
                LEFT JOIN zdata_pism_generic zpg ON(zpg.id=zpa.all_drug_genericid)
                LEFT JOIN zdata_pism_class zpc ON(zpc.id=zpa.all_drug_groupid)
                WHERE zpa.rstat not in(0,3) AND zpa.ptid=:ptid) AS KK
                LEFT JOIN ezform_choice ec ON(ec.ezf_id='1533111256009157800' AND ec.ezf_field_id='1533112638095666900' AND ec.ezf_choicevalue=all_drug_type)
                GROUP BY all_drug_type
                ORDER BY all_drug_type";

        return Yii::$app->db->createCommand($sql, [':ptid' => $pt_id])->queryAll();
    }

    public static function getDrugAllergy($params) {
        $sql = "CALL pt_drug_allergy($params)";

        return Yii::$app->db->createCommand($sql)->queryOne();
    }

    /**
     * 
     * @param type $fields
     * @param type $forms
     * @param type $ezform
     * @param type $conditions
     * @param type $summarys
     * @param type $image_field
     * @param string $customSelect
     * @param string $modelFilter
     * @param type $group_field
     * @param type $left_forms
     * @return type $responseQuery = [
      'modelDynamic' => $modelDyn,
      'data' => $data,
      'dataProvider' => $dataProvider,
      'searchModel' => $searchModel,
      'modelFields'=>$modelFields,
      ];
     */
    public static function getDynamicQuery($fields, $forms, $ezform, $conditions = null, $summarys = null, $image_field = null, $customSelect = null, $modelFilter = null, $group_field = null, $left_forms = null, $sort_order = null) {

        $ezf_table = isset($ezform->ezf_table) ? $ezform->ezf_table : 'zdata_pism_item';
        $ezf_id = isset($ezform->ezf_id) ? $ezform->ezf_id : '1515588745039739100';
        if ($image_field)
            $fields[] = $image_field;
        //$dataProvider = \backend\modules\pis\classes\PisFunc::getOrderTran($visit_id, ''); // ค้นหา Order จากทุก Doctor_Id
        $modelFields = ThaiHisQuery::getEzformFields3($fields, $ezform['ezf_id'], $forms, $left_forms);
        $modelDyn = ThaiHisFunc::setDynamicModel($modelFields, $ezf_table, Yii::$app->session['ezf_input'], Yii::$app->session['show_varname']);

        $searchModel = new TbdataAll();
        $searchModel->setTableName($ezf_table);

        $ezformParent = Null;
        $targetField = EzfQuery::getTargetOne($ezf_id);
        if (isset($targetField)) {
            $ezformParent = EzfQuery::getEzformById($targetField->ref_ezf_id);
        }

        if (is_array($conditions)) {
            foreach ($conditions as $val) {
                $modelFilter[] = $val['field'] . $val['operator'] . $val['compare'];
            }
        }

        if (is_array($summarys)) {
            foreach ($summarys as $val) {
                $customSelect[] = 'SUM(' . $val['field'] . ') as ' . $val['alias_name'];
            }
        }
        
        $data = ThaiHisFunc::modelSearchAll2($searchModel, $ezform, $targetField, $ezformParent, $modelFields, $modelFilter, null, $customSelect, $group_field, false, $sort_order);

        if ($data)
            $modelDyn->attributes = $data[0];

        $dataProvider = new \yii\data\ArrayDataProvider([
            'allModels' => $data,
            'pagination' => [
                'pageSize' => 15,
            ],
        ]);

        $responseQuery = [
            'modelDynamic' => $modelDyn,
            'data' => $data,
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'modelFields' => $modelFields,
        ];

        return $responseQuery;
    }

    public static function getDynamicUnoinQuery($fields, $forms, $ezform, $conditions = null, $summarys = null, $image_field = null, $modelFilter = null, $group_field = null, $orderby = null) {
        $query = [];
        if (is_array($fields)) {
            foreach ($fields as $key => $value) {
                $customSelect = [];
                $ezf_table = isset($ezform[$key]->ezf_table) ? $ezform[$key]->ezf_table : 'zdata_pism_item';
                $ezf_id = isset($ezform[$key]->ezf_id) ?: '1515588745039739100';
                $value[] = $image_field[$key];
                //$dataProvider = \backend\modules\pis\classes\PisFunc::getOrderTran($visit_id, ''); // ค้นหา Order จากทุก Doctor_Id
                $modelFields = ThaiHisQuery::getEzformFields3($value, $ezform[$key]['ezf_id'], $forms[$key]);

                $fieldSort = [];
                foreach ($value as $keyF => $valF) {
                    foreach ($modelFields as $valField) {
                        if ($valF == $valField['ezf_field_id'] && $valField['field_to_join'] == 'no') {
                            $fieldSort[] = $valField;
                        }
                    }
                }

                foreach ($modelFields as $valF) {
                    if ($valF['field_to_join'] == 'yes') {
                        $fieldSort[] = $valF;
                    }
                }

                $searchModel = new TbdataAll();
                $searchModel->setTableName($ezf_table);

                $ezformParent = Null;
                $targetField = EzfQuery::getTargetOne($ezf_id);
                if (isset($targetField)) {
                    $ezformParent = EzfQuery::getEzformById($targetField->ref_ezf_id);
                }

                if (isset($conditions[$key]) && is_array($conditions[$key])) {
                    foreach ($conditions[$key] as $val) {
                        $modelFilter[$key][] = $val['field'] . $val['operator'] . $val['compare'];
                    }
                }

                if (isset($summarys[$key]) && is_array($summarys[$key])) {
                    foreach ($summarys[$key] as $val) {
                        $customSelect[] = 'SUM(' . $val['field'] . ') as ' . $val['alias_name'];
                    }
                }

                $query[] = ThaiHisFunc::modelSearchAll2($searchModel, $ezform[$key], $targetField, $ezformParent, $fieldSort, $modelFilter[$key], null, $customSelect, $group_field[$key], true);
            }
            $modelDyn = ThaiHisFunc::setDynamicModel($modelFields, $ezf_table, Yii::$app->session['ezf_input'], Yii::$app->session['show_varname']);
        }

        $unionQuery = null;
        if (count($query) > 1) {
            foreach ($query as $key => $val) {
                if ($key == 0)
                    $unionQuery = $val;
                else
//                    $unionQuery = $unionQuery->union($val->createCommand()->rawSql);
                    $unionQuery = (new \yii\db\Query())
                            ->from(['dummy_query' => $unionQuery->union($val)])
                            ->orderBy($orderby);
            }
        }else {
            $unionQuery = $query[0];
        }
//        \appxq\sdii\utils\VarDumper::dump($unionQuery->createCommand()->rawSql);        
        $data = $unionQuery->createCommand()->queryAll();
        $modelDyn->attributes = $data;

        $dataProvider = new \yii\data\ArrayDataProvider([
            'allModels' => $data,
            'pagination' => [
                'pageSize' => 15,
            ],
        ]);

        $responseQuery = [
            'modelDynamic' => $modelDyn,
            'data' => $data,
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ];

        return $responseQuery;
    }

    /**
     * @param type $user_id
     * @param type $visit_id
     * @return type $data
     */
    public static function getOrderByDocId($user_id, $visit_id) {
        $query = new \yii\db\Query();
        $data = $query->select('zpot.order_trad_id')
                ->from('zdata_pis_order zpo')
                ->innerJoin('zdata_pis_order_tran  zpot', 'zpot.order_id=zpo.id')
                ->where(['zpot.rstat' => '1', 'zpo.order_doctor_id' => $user_id
                    , 'zpo.order_visit_id' => $visit_id])
                ->all();

        return $data;
    }

    public static function getOrderByvisitId($visit_id) {
        $sql = "SELECT  zpot.*
                FROM zdata_pis_order zpo 
                INNER JOIN zdata_pis_order_tran zpot ON(zpo.id=zpot.order_id) 					
                WHERE zpo.rstat =1 AND zpo.order_visit_id=:visitid 
		AND zpot.order_tran_status='2' AND order_tran_cashier_status = '1'";
        
        return Yii::$app->db->createCommand($sql, [':visitid' => $visit_id])->queryAll();
    }
    
    public static function getBmiCal($visitid) {
        $sql = "SELECT bmi_visit_id,bmi_bw,bmi_ht,bmi_bsa,bmi_bmi,zpp.pt_hn,
                CONCAT(pt_firstname,' ',pt_lastname) AS fullname,pt_bdate,pt_sex,
                CASE pt_sex WHEN '1' THEN 'ชาย' WHEN '2' THEN 'หญิง' ELSE 'ไม่ระบุ' END AS pt_sex_name
                FROM zdata_bmi zb
                INNER JOIN zdata_patientprofile zpp ON(zpp.id=zb.ptid)
                WHERE zb.rstat not in(0,3) AND zb.bmi_visit_id=:visitid
                ORDER BY zb.update_date DESC";
        
        return Yii::$app->db->createCommand($sql, [':visitid' => $visitid])->queryOne();
    }

}
