<?php

namespace backend\modules\pis\classes;

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
class PisFunc {

    public static function getItemGeneric($model, $params) {
        $model->load($params);

        $paramsStr = "";
        $paramsArry = [];
        if ($model['generic_name']) {
            $paramsStr .= " AND generic_name LIKE :generic_name";
            $paramsArry[':generic_name'] = "%{$model['generic_name']}%";
        }
        if ($model['generic_type_id']) {
            $paramsStr .= " AND drug_type_name = :generic_type_id";
            $paramsArry[':generic_type_id'] = "{$model['generic_type_id']}";
        }
        if ($model['generic_status']) {
            $paramsStr .= " AND generic_status = :generic_status";
            $paramsArry[':generic_status'] = "{$model['generic_status']}";
        }
        if ($model['generic_type']) {
            $paramsStr .= " AND generic_type = :generic_type";
            $paramsArry[':generic_type'] = "{$model['generic_type']}";
        }

        $sql = "SELECT zpg.id AS generic_id,zpg.generic_name,CONCAT('Sig : ',zpg.generic_pertime,' ',zpu.drug_unit_lexicon,' ',zpue.drug_use_lexicon,' '
                ,zptf.drug_timeframe_lexicon,' ',CASE WHEN zptu.drug_usetime_lexicon <> 'none' THEN zptu.drug_usetime_lexicon ELSE '' END) AS drug_use
                ,zpt.drug_type_name,generic_type_id,generic_status,generic_type
                FROM zdata_pism_generic zpg
                LEFT JOIN zdata_pism_type zpt ON(zpg.generic_type_id=zpt.id)
                LEFT JOIN zdata_pism_unit zpu ON(zpg.generic_unit_id=zpu.id)
                LEFT JOIN zdata_pism_use zpue ON(zpg.generic_use_id=zpue.id)
                LEFT JOIN zdata_pism_timeframe zptf ON(zpg.generic_timeframe_id=zptf.id)
                LEFT JOIN zdata_pism_timeuse zptu ON(zpg.generic_usetime_id=zptu.id)
                WHERE zpg.rstat='1' $paramsStr";

        $dataProvider = new \yii\data\SqlDataProvider([
            'sql' => $sql,
            'params' => $paramsArry,
            'pagination' => [
                'pageSize' => 15,
            ],
        ]);

        return $dataProvider;
    }

    public static function getItemTrad($model, $params, $generic_id) {
        $model->load($params);

        $paramsStr = "";
        $paramsArry = [];
        if ($generic_id) {
            $paramsStr .= " AND zpi.trad_generic_id='{$generic_id}'";
        }

        if ($model['trad_stdtrad_id']) {
            $paramsStr .= " AND zptn.stdtrad_name LIKE :trad_stdtrad_id";
            $paramsArry[':trad_stdtrad_id'] = "%{$model['trad_stdtrad_id']}%";
        }

        if ($model['trad_nickname']) {
            $paramsStr .= " AND trad_nickname LIKE :trad_nickname";
            $paramsArry[':trad_nickname'] = "%{$model['trad_nickname']}%";
        }

        if ($model['trad_status']) {
            $paramsStr .= " AND trad_status = :trad_status";
            $paramsArry[':trad_status'] = $model['trad_status'];
        }

        $sql = "SELECT zpi.id AS item_id,zpi.trad_itemname AS trad_name,trad_price,trad_nickname,zpi.trad_item_pic,kk.drug_use
                ,trad_status
                FROM zdata_pism_item zpi
		LEFT JOIN (SELECT zpus.use_item_id,CONCAT('Sig : ',zpus.use_pertime, ' ',zpu.drug_unit_lexicon, ' ',zpue.drug_use_lexicon, ' ' ,zptf.drug_timeframe_lexicon, ' ',CASE WHEN zptu.drug_usetime_lexicon <>  'none' THEN zptu.drug_usetime_lexicon ELSE  ' ' END) AS drug_use
                FROM zdata_pis_use_set zpus                
                LEFT JOIN zdata_pism_unit zpu ON(zpus.use_unit_id=zpu.id) 
                LEFT JOIN zdata_pism_use zpue ON(zpus.use_use_id=zpue.id) 
                LEFT JOIN zdata_pism_timeframe zptf ON(zpus.use_timeframe_id=zptf.id) 
                LEFT JOIN zdata_pism_timeuse zptu ON(zpus.use_usetime_id=zptu.id) 
                WHERE zpus.rstat='1' ORDER BY zpus.update_date DESC LIMIT 1) AS kk ON(kk.use_item_id=zpi.id) 
                WHERE zpi.rstat='1' $paramsStr";

        $dataProvider = new \yii\data\SqlDataProvider([
            'sql' => $sql,
            'params' => $paramsArry,
            'pagination' => [
                'pageSize' => 15,
            ],
        ]);

        return $dataProvider;
    }

    public static function getItemSearch($model, $params, $ptid) {
        $model->load($params);

        $paramsStr = " zpi.trad_status='1'";
        $paramsArry = [];
        if ($model['trad_stdtrad_id']) {
            $paramsStr .= "AND (trad_itemname LIKE :txtSearch OR trad_nickname LIKE :txtSearch)";
            $paramsArry[':txtSearch'] = "%{$model['trad_stdtrad_id']}%";
        }
        if ($model['trad_content']) {
            $paramsStr .= " AND generic_class_id = :class_id";
            $paramsArry[':class_id'] = $model['trad_content'];
        }

        $query = new \yii\db\Query();
        $query->select(["CONCAT_WS(',','$ptid',zpi.id,zpg.id,zpg.generic_class_id ) AS item_allergy_id", "zpi.id AS item_id", "zpi.trad_generic_id AS generic_id", "zpi.trad_itemname AS trad_name", "trad_price", "trad_nickname", "zpi.trad_item_pic"
                    , "zpu.use_pertime", "use_unit_id", "use_use_id", "use_timeframe_id", "use_timezone_1", "use_timezone_2", "use_timezone_3", "use_timezone_4"
                    , "use_usetime_id", "use_note", "zpi.trad_drug_type", "use_label", "zpi.trad_tmt"])
                ->from("zdata_pism_item zpi ")
                ->leftJoin("zdata_pis_use_set zpu", "(zpu.use_item_id=zpi.id AND zpu.use_generic_id=zpi.trad_generic_id AND zpu.rstat='1' AND zpu.use_active='1')")
                ->innerJoin("zdata_pism_generic zpg", "(zpg.id=zpi.trad_generic_id)")
                ->where("zpi.rstat= '1'")
                ->andWhere($paramsStr, $paramsArry);

        $result = $query->all();

        $dataProvider = new \yii\data\ArrayDataProvider([
            'allModels' => $result,
            'pagination' => [
                'pageSize' => 15,
            ],
        ]);

        return $dataProvider;
    }

    public static function getOrderTran($visit_id, $doctor_id) {
        $paramsStr = " AND zpo.order_visit_id=:visit_id";
        $paramsArry[':visit_id'] = $visit_id;

        if ($doctor_id) {
            $paramsStr .= " AND zpo.order_doctor_id=:doctor_id";
            $paramsArry[':doctor_id'] = $doctor_id;
        }

        $sql = "SELECT zpi.trad_tmt,zpot.id AS order_tran_id,zpi.trad_itemname AS item_name,order_status
                ,trad_price,zpot.order_tran_label,zpot.order_tran_notpay,zpot.order_tran_pay,zpot.order_tran_qty,zpot.order_tran_status
                ,zpo.order_doctor_id,CONCAT(title,pf.firstname,' ',pf.lastname) AS fullname_doctor,order_tran_ned
                FROM zdata_pis_order zpo
                INNER JOIN zdata_pis_order_tran zpot ON(zpo.id=zpot.order_id)
                INNER JOIN zdata_pism_item zpi ON(zpi.id=zpot.order_trad_id)
                INNER JOIN zdata_pism_tradname zptn ON(zptn.id=zpi.trad_stdtrad_id) 
                INNER JOIN zdata_pism_generic zpg ON(zpg.id=zptn.stdtrad_generic_id)
                LEFT JOIN zdata_pism_unit zpu ON(zpu.id=zpi.trad_content)
                LEFT JOIN `profile` pf ON(pf.user_id=zpo.order_doctor_id)
                WHERE zpo.rstat='1' AND zpot.rstat='1' $paramsStr";

        $dataProvider = new \yii\data\SqlDataProvider([
            'sql' => $sql,
            'params' => $paramsArry,
//            'sort' => ['attributes' => ['order_tran_code', 'order_name', 'full_price', 'create_date']],
            'pagination' => [
                'pageSize' => 15,
            ],
        ]);

        return $dataProvider;
    }

    public static function getOrderTranReport($visit_id) {
        $paramsStr = " AND zpo.order_visit_id=:visit_id";
        $paramsArry[':visit_id'] = $visit_id;

        $sql = "SELECT zpi.trad_tmt,zpot.id AS order_tran_id,zpi.trad_itemname AS item_name,order_status
                ,trad_price,zpot.order_tran_label,zpot.order_tran_notpay,zpot.order_tran_pay,zpot.order_tran_qty,zpot.order_tran_status
                ,zpo.order_doctor_id,CONCAT(title,pf.firstname,' ',pf.lastname) AS fullname_doctor,order_tran_ned
                FROM zdata_pis_order zpo
                INNER JOIN zdata_pis_order_tran zpot ON(zpo.id=zpot.order_id)
                INNER JOIN zdata_pism_item zpi ON(zpi.id=zpot.order_trad_id)
                INNER JOIN zdata_pism_tradname zptn ON(zptn.id=zpi.trad_stdtrad_id) 
                INNER JOIN zdata_pism_generic zpg ON(zpg.id=zptn.stdtrad_generic_id)
                LEFT JOIN zdata_pism_unit zpu ON(zpu.id=zpi.trad_content)
                LEFT JOIN `profile` pf ON(pf.user_id=zpo.order_doctor_id)
                WHERE zpo.rstat='1' AND zpot.rstat='1' $paramsStr";

        $query = Yii::$app->db->createCommand($sql, $paramsArry)->queryAll();

        return $query;
    }

    public static function getOrderTranCounter($order_id, $order_status) {
        $sql = "SELECT zpot.id AS order_tran_id,CONCAT(zpg.generic_name,' (',zptn.stdtrad_name, ' ',trad_strength,') ',zpu.drug_unit_lexicon) AS trad_name
                ,trad_price,zpot.order_tran_label,zpot.order_tran_notpay,zpot.order_tran_pay,zpot.order_tran_qty
                FROM zdata_pis_order zpo
                INNER JOIN zdata_pis_order_tran zpot ON(zpo.id=zpot.order_id)
                INNER JOIN zdata_pism_item zpi ON(zpi.id=zpot.order_trad_id)
                INNER JOIN zdata_pism_tradname zptn ON(zptn.id=zpi.trad_stdtrad_id) 
                INNER JOIN zdata_pism_generic zpg ON(zpg.id=zptn.stdtrad_generic_id)
                LEFT JOIN zdata_pism_unit zpu ON(zpu.id=zpi.trad_unit_id)
                WHERE zpo.rstat='1' AND zpot.rstat='1' AND zpo.id=:order_id AND zpot.order_tran_status=:order_status";

        $dataProvider = new \yii\data\SqlDataProvider([
            'sql' => $sql,
            'params' => [':order_id' => $order_id, ':order_status' => $order_status],
//            'sort' => ['attributes' => ['order_tran_code', 'order_name', 'full_price', 'create_date']],
            'pagination' => [
                'pageSize' => 15,
            ],
        ]);

        return $dataProvider;
    }

    public static function getCounterQue($model, $params) {
        $model->load($params);

        $paramsStr = " AND zv.visit_date BETWEEN '{$model['create_date']} 00:00:00' AND '{$model['create_date']} 23:59:59' "
                . "AND zpo.order_status='2' AND zpot.order_tran_status=:status";
        $paramsArry = [':status' => $model['order_status']];

        if ($model['order_no']) {
            $paramsStr .= " AND CONCAT(vp.pt_hn,' ',vp.pt_firstname,' ',vp.pt_lastname) LIKE :fullname";
            $paramsArry[':fullname'] = "%{$model['order_no']}%";
        }

        $nonimg = Yii::getAlias('@storageUrl/images') . '/nouser.png';
        $img = Yii::getAlias('@storageUrl/ezform/fileinput') . '/';
        $sql = "SELECT DISTINCT CONCAT(prefix_name_cid,pt_firstname,' ',pt_lastname) AS fullname
            ,vp.pt_hn,IFNULL(CONCAT('$img',vp.pt_pic),'$nonimg') AS pt_pic,pt_bdate,zpo.order_no
                ,right_name,zpo.id AS order_id
                FROM zdata_pis_order zpo
                INNER JOIN zdata_pis_order_tran zpot ON(zpot.order_id=zpo.id)
                INNER JOIN zdata_patientprofile AS vp ON(vp.id=zpo.ptid)
		LEFT JOIN zdata_prefix zp ON(vp.pt_prefix_id=zp.prefix_id)
                INNER JOIN zdata_patientright AS zpr ON(zpr.id=(SELECT MAX(id) FROM zdata_patientright WHERE rstat='1' AND right_pt_id=zpo.ptid))
                INNER JOIN zdata_right zr ON(zr.right_code=zpr.right_code)
                INNER JOIN zdata_visit zv ON(zv.id=zpo.order_visit_id)
                WHERE zpo.rstat='1' AND zpot.rstat='1' $paramsStr
                ORDER BY zpo.update_date ASC";
        $dataProvider = new \yii\data\SqlDataProvider([
            'sql' => $sql,
            'params' => $paramsArry,
//            'sort' => ['attributes' => ['order_tran_code', 'order_name', 'full_price', 'create_date']],
            'pagination' => [
                'pageSize' => 15,
            ],
        ]);

        return $dataProvider;
    }

    /**
     * 
     * @param type $user_id
     * @param type $q
     * @return \yii\data\SqlDataProvider
     */
    public static function getPisPackage($user_id, $q) {
        $query = new \yii\db\Query();
        $query->select('*')
                ->from('zdata_pis_package')
                ->where(['rstat' => '1', 'package_status' => '1'])
                ->andwhere("(user_create=$user_id OR package_shared_status='2')")
                ->andWhere(['LIKE', 'package_name', "$q"]);

        $dataProvider = new \yii\data\SqlDataProvider([
            'sql' => $query->createCommand()->rawSql,
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $dataProvider;
    }

    public static function getItemPackage($package_id) {
        $query = new \yii\db\Query();
        $query->select(["CONCAT(zpg.generic_name,' (',zptn.stdtrad_name, ' ',trad_strength,') ',zpu.drug_unit_lexicon) AS trad_itemname",
                    'trad_price', 'zppi.order_tran_label', 'zppi.order_tran_qty', 'zppi.id AS item_id', 'zpi.id AS drug_item_id',
                    'trad_tmt', 'trad_generic_id', 'order_tran_pertime', 'order_tran_unit_id', 'order_tran_use_id',
                    'order_tran_use_id', 'order_tran_timeframe_id', 'order_tran_usetime_id', 'order_tran_note',
                    'order_tran_note', 'order_tran_type_status', 'order_tran_day', 'order_tran_drugtype',
                    'order_tran_zeropay_status', 'order_tran_zeropay_type', 'order_tran_chemo_cal',
                    'order_tran_chemo_amount', 'order_tran_chemo_result'])
                ->from("zdata_pis_package zpp")
                ->innerJoin("zdata_pis_package_item zppi", "zppi.order_id=zpp.id")
                ->innerJoin("zdata_pism_item zpi", "zpi.id=zppi.order_trad_id")
                ->innerJoin("zdata_pism_tradname zptn", "zptn.id=zpi.trad_stdtrad_id")
                ->innerJoin("zdata_pism_generic zpg", "zpg.id=zptn.stdtrad_generic_id")
                ->leftJoin("zdata_pism_unit zpu", "zpu.id=zpi.trad_content")
                ->where(["zpp.id" => $package_id, 'zppi.rstat' => 1]);

        $dataProvider = new \yii\data\SqlDataProvider([
            'sql' => $query->createCommand()->rawSql,
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

        return $dataProvider;
    }

    public static function getItemVisit($visit_id) {
        $query = new \yii\db\Query();
        $query->select(["CONCAT(zpg.generic_name,' (',zptn.stdtrad_name, ' ',trad_strength,') ',zpu.drug_unit_lexicon) AS trad_itemname"
                    , 'trad_price', 'zpot.order_tran_label', 'zpot.order_tran_qty', 'zpot.id AS item_id', 'zpi.id AS drug_item_id'
                    , 'trad_tmt', 'trad_generic_id', 'order_tran_pertime', 'order_tran_unit_id', 'order_tran_use_id'
                    , 'order_tran_use_id', 'order_tran_timeframe_id', 'order_tran_usetime_id', 'order_tran_note'
                    , 'order_tran_note', 'order_tran_type_status', 'order_tran_day', 'order_tran_drugtype',
                    'order_tran_zeropay_status', 'order_tran_zeropay_type'])
                ->from("zdata_pis_order zpo")
                ->innerJoin("zdata_pis_order_tran zpot", "zpot.order_id=zpo.id")
                ->innerJoin("zdata_pism_item zpi", "zpi.id=zpot.order_trad_id")
                ->innerJoin("zdata_pism_tradname zptn", "zptn.id=zpi.trad_stdtrad_id")
                ->innerJoin("zdata_pism_generic zpg", "zpg.id=zptn.stdtrad_generic_id")
                ->leftJoin("zdata_pism_unit zpu", "zpu.id=zpi.trad_content")
                ->where(["zpo.order_visit_id" => $visit_id, 'zpot.rstat' => 1]);

        $dataProvider = new \yii\data\SqlDataProvider([
            'sql' => $query->createCommand()->rawSql,
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

        return $dataProvider;
    }

    /**
     * 
     * @param type $ptid
     * @return \yii\data\SqlDataProvider
     */
    public static function getOrderHistoryByPtid($ptid, $q) {
        $subSql = '';
        if ($q) {
            $date = explode(",", $q);
            if (count($date) > 1) {
                $stDate = date('Y-m-d', strtotime($date[0]));
                $enDate = date('Y-m-d', strtotime($date[1]));

                $subSql .= " AND visit_date BETWEEN '{$stDate} 00:00:00' AND '{$enDate} 23:59:59'";
            }
        }

        $query = new \yii\db\Query();
        $query->select('zv.id,zv.visit_date,icd10.`name` AS diag_name')
                ->distinct(true)
                ->from('zdata_visit zv')
                ->innerJoin('zdata_pis_order zpo', 'zpo.order_visit_id=zv.id')
                ->innerJoin('zdata_pis_order_tran  zpot', 'zpot.order_id=zpo.id')
                ->leftJoin('zdata_dt zdt', 'zdt.di_visit_id=zv.id')
                ->leftJoin('const_icd10 icd10', 'icd10.`code`=zdt.di_icd10')
                ->where(['zpot.rstat' => '1', 'zpot.order_tran_status' => '2'
                    , 'zv.ptid' => $ptid])
                ->andWhere($subSql);

        $dataProvider = new \yii\data\SqlDataProvider([
            'sql' => $query->createCommand()->rawSql,
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

        return $dataProvider;
    }

}
