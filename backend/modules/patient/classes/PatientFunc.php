<?php

namespace backend\modules\patient\classes;

use backend\modules\api\v1\classes\Nhso;
use Yii;
use backend\modules\patient\classes\PatientQuery;
use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\ezforms2\classes\EzfFunc;
use backend\modules\ezforms2\models\EzformFields;
use backend\modules\ezforms2\classes\EzfUiFunc;
use backend\modules\ezforms2\models\TbdataAll;
use appxq\sdii\helpers\SDHtml;
use yii\web\HttpException;
use yii\web\Response;

/**
 * OvccaFunc class file UTF-8
 * @author SDII <iencoded@gmail.com>
 * @copyright Copyright &copy; 2015 AppXQ
 * @license http://www.appxq.com/license/
 * @version 1.0.0 Date: 9 ก.พ. 2559 12:38:14
 * @link http://www.appxq.com/
 * @example
 */
class PatientFunc {

    public static function getFulladdress($model) {
        $modelProvince = '';
        $address = '';
        if ($model['pt_addr_tumbon']) {
            $modelProvince = PatientQuery::getProviceByTumbon($model['pt_addr_tumbon']);
            $modelProvince = "ต.{$modelProvince['DISTRICT_NAME']} อ.{$modelProvince['AMPHUR_NAME']} จ.{$modelProvince['PROVINCE_NAME']} {$model['pt_addr_zipcode']}";
        }
        if ($model['pt_address'] || $model['pt_moi']) {
            $address = "{$model['pt_address']} หมู่ {$model['pt_moi']} ";
        }

        return $address . $modelProvince;
    }

    public static function getRefTableName($modelFields, $fieldNameRef, $dataid = null) {
        if ($dataid) {
            foreach ($modelFields as $value) {
                if ($value['attributes']['ezf_field_name'] == $fieldNameRef) {
                    $dataFields = EzfQuery::getRefFieldById($value['attributes']['ezf_field_id']);

                    return EzfQuery::builderSqlGetScalar([EzfFunc::array2ConcatStr($dataFields['ref_field_desc'])]
                                    , $dataFields['ezf_table']
                                    , "{$dataFields['ref_field_id']} = :dataid  AND rstat =:rstat", [':dataid' => $dataid, ':rstat' => 1]);
                }
            }
        } else {
            foreach ($modelFields as $value) {
                if ($value['attributes']['ezf_field_name'] == $fieldNameRef) {
                    return $value;
                }
            }
        }
    }

    public static function getModel($ezf_id, $dataid) {
        $modelEzf = PatientQuery::getEzformOne($ezf_id);
        $modelFields = EzformFields::find()
                ->where('ezf_id = :ezf_id', [':ezf_id' => $modelEzf->ezf_id])
                ->orderBy(['ezf_field_order' => SORT_ASC])
                ->all();

        $model = EzfFunc::setDynamicModel($modelFields, $modelEzf->ezf_table, Yii::$app->session['ezf_input'], Yii::$app->session['show_varname']);
        $model = EzfUiFunc::loadData($model, $modelEzf->ezf_table, $dataid);

        //return ['model' => $model, 'modelFields' => $modelFields, 'modelEzf' => $modelEzf];
        return $model;
    }

    public static function getOrderTran($target) {
        $sql = "SELECT  zot.id,zot.order_tran_code,zot.order_tran_status,co.order_name,co.group_type,co.full_price,order_tran_doctor
                ,ds.sect_name,zot.create_date,co.ezf_id,order_qty,cog.order_group_name,order_type_name,
               # zot.order_tran_pay,zot.order_tran_notpay
                CASE WHEN (zpr.right_code = 'CASH') THEN zot.order_tran_pay + zot.order_tran_notpay ELSE zot.order_tran_pay END AS order_tran_pay,
                CASE WHEN (zpr.right_code = 'CASH') THEN 0 ELSE zot.order_tran_notpay END AS order_tran_notpay,order_tran_cashier_status,
                zot.ptid,zpp.pt_hn,external_flag,CONCAT(title,pf.firstname,' ',pf.lastname) AS doc_fullname
                FROM zdata_order_tran zot
                INNER JOIN const_order co ON(co.order_code=zot.order_tran_code)
                INNER JOIN zdata_patientprofile zpp ON (zpp.id=zot.ptid)
		INNER JOIN zdata_patientright AS zpr ON(zpr.id=(SELECT MAX(id) FROM zdata_patientright WHERE rstat='1' AND right_pt_id=zot.ptid))
		INNER JOIN zdata_right zr ON(zr.right_code=zpr.right_code) 
                INNER JOIN dept_sect ds ON(ds.sect_code=zot.order_tran_dept)
                LEFT JOIN const_order_group cog ON(cog.order_group_code=co.group_code)
                INNER JOIN const_order_type cot ON(cot.order_type_code=co.group_type)
                LEFT JOIN `profile` pf ON(pf.user_id=zot.order_tran_doctor) 
                WHERE zot.rstat='1' AND zot.order_tran_visit_id=:target 
                ORDER BY cog.order_group_orderby ASC,zot.create_date DESC";
//        \appxq\sdii\utils\VarDumper::dump(Yii::$app->db->createCommand($sql, [':target' => $target])->rawSql);
        $dataProvider = new \yii\data\SqlDataProvider([
            'sql' => $sql,
            'params' => [':target' => $target],
            'sort' => ['attributes' => ['order_tran_code', 'order_name', 'full_price', 'create_date']],
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

        return $dataProvider;
    }

    public static function getOrderCounter($model, $params, $dept) {
        $model->load($params);
        if (Yii::$app->user->identity->profile->attributes['position'] == '2') {
            $model['order_tran_status'] = ($model['order_tran_status'] == '1' ? '2' : $model['order_tran_status']);
        }
        $date = \appxq\sdii\utils\SDdate::phpThDate2mysqlDate($model['create_date']);
        $paramsStr = "AND group_type=:dept AND order_tran_status=:order_status AND (zv.visit_date BETWEEN :dateST AND :dateEN OR visit_date=:date)";
        $paramsArry[':dateST'] = "$date 00:00:00";
        $paramsArry[':dateEN'] = "$date 23:59:59";
        $paramsArry[':date'] = $date;
        $paramsArry[':dept'] = $dept;
        $paramsArry[':order_status'] = $model['order_tran_status'];
        if ($model['order_tran_code']) {
            $paramsStr .= " AND CONCAT(pt_hn,' ',zp.prefix_name,pt_firstname,' ',pt_lastname) LIKE :search_order";
            $paramsArry[':search_order'] = "%{$model['order_tran_code']}%";
        }

        if ($dept == 'X') {
            $sql = "SELECT order_tran_id,pt_hn,fullname,sect_name,sect_code,right_name,order_tran_status,GROUP_CONCAT(order_name ORDER BY order_name) AS order_name FROM( 
                SELECT zot.order_tran_visit_id AS order_tran_id,pt_hn,CONCAT(zp.prefix_name_cid,pt_firstname,' ',pt_lastname) AS fullname
                ,sect_name,ds.sect_code,right_name,order_tran_status,co.order_name 
                FROM zdata_order_tran zot 
                INNER JOIN zdata_visit zv ON(zv.id=zot.order_tran_visit_id) 
                INNER JOIN const_order co ON(co.order_code=zot.order_tran_code) 
                INNER JOIN dept_sect ds ON(ds.sect_code=zot.order_tran_dept) 
                /*INNER JOIN vpatient_profile AS vpp ON(vpp.pt_id=zot.ptid) */
                INNER JOIN zdata_patientprofile zpp ON (zpp.id=zot.ptid)
                LEFT JOIN zdata_prefix zp ON (zp.prefix_id=zpp.pt_prefix_id)
                INNER JOIN zdata_patientright AS zpr ON(zpr.id=(SELECT MAX(id) FROM zdata_patientright WHERE rstat='1' AND right_pt_id=zot.ptid)) 
                INNER JOIN zdata_right zr ON(zr.right_code=zpr.right_code) 
                WHERE zot.rstat='1' $paramsStr 
                ORDER BY zot.update_date DESC ) AS PP 
                GROUP BY order_tran_id,pt_hn,fullname,sect_name,sect_code,right_name,order_tran_status";
        } else {
            $sql = "SELECT DISTINCT zot.order_tran_visit_id AS order_tran_id,pt_hn,CONCAT(zp.prefix_name_cid,pt_firstname,' ',pt_lastname) AS fullname
                ,sect_name,ds.sect_code,right_name,order_tran_status
                FROM zdata_order_tran zot
                INNER JOIN zdata_visit zv ON(zv.id=zot.order_tran_visit_id)
                INNER JOIN const_order co ON(co.order_code=zot.order_tran_code)
                INNER JOIN dept_sect ds ON(ds.sect_code=zot.order_tran_dept)
                INNER JOIN zdata_patientprofile zpp ON (zpp.id=zot.ptid)
                LEFT JOIN zdata_prefix zp ON (zp.prefix_id=zpp.pt_prefix_id )
                INNER JOIN zdata_patientright AS zpr ON(zpr.id=(SELECT MAX(id) FROM zdata_patientright WHERE rstat='1' AND right_pt_id=zot.ptid))
                INNER JOIN zdata_right zr ON(zr.right_code=zpr.right_code)                
                WHERE zot.rstat='1' $paramsStr
                ORDER BY zot.update_date DESC";
        }


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

    public static function getOrderCounterPap($model, $params, $sect_code) {
        $model->load($params);
        $date = \appxq\sdii\utils\SDdate::phpThDate2mysqlDate($model['create_date']);
        $paramsStr = 'AND zv.rstat=1 AND group_type=:dept AND order_tran_status=:order_status AND zv.visit_date BETWEEN :dateST AND :dateEN';
        $paramsArry[':dept'] = 'C';
        $paramsArry[':dateST'] = "$date 00:00:00";
        $paramsArry[':dateEN'] = "$date 23:59:59";
        if ($model['order_tran_code']) {
            $paramsStr .= " AND CONCAT(pt_hn,' ',fullname) LIKE :search_order";
            $paramsArry[':search_order'] = "%{$model['order_tran_code']}%";
        }
        if ($sect_code == 'S076') {
            $select = ",order_tran_status";
            if ($model['order_tran_status'] == '1') {
                $joinReport = "";
                $paramsArry[':order_status'] = $model['order_tran_status'];
            } elseif ($model['order_tran_status'] == '2') {
                $joinReport = "INNER JOIN zdata_reportcyto zcr ON(zcr.order_tran_id=zot.id)";
                $paramsStr .= " AND zcr.report_status='H'";
                $paramsArry[':order_status'] = $model['order_tran_status'];
            }
        } else {
            $select = ",report_status AS order_tran_status";
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
        }

        $sql = "SELECT DISTINCT zot.order_tran_visit_id AS order_tran_id,pt_hn,fullname,sect_name,right_name
                $select
                FROM zdata_order_tran zot
                INNER JOIN zdata_visit zv ON(zv.id=zot.order_tran_visit_id)
                INNER JOIN const_order co ON(co.order_code=zot.order_tran_code)
                INNER JOIN dept_sect ds ON(ds.sect_code=zot.order_tran_dept)
                INNER JOIN vpatient_profile AS vpp ON(vpp.pt_id=zot.ptid)
                INNER JOIN zdata_patientright AS zpr ON(zpr.id=(SELECT MAX(id) FROM zdata_patientright WHERE rstat='1' AND right_pt_id=zot.ptid))
                INNER JOIN zdata_right zr ON(zr.right_code=zpr.right_code)  
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

    public static function getOrderCounterOutlab($model, $params) {
        $model->load($params);
        $paramsArry = [];
        if ($model['create_date']) {
            $date = \appxq\sdii\utils\SDdate::phpThDate2mysqlDate($model['create_date']);
            $paramsStr = ' AND DATE(zot.update_date) = :date';
            $paramsArry[':date'] = $date;
        }
        if ($model['order_tran_status'] == '1') {
            $paramsStr = ' AND zle.external_result_status IS NULL';
        } else {
            $paramsStr = ' AND zle.external_result_status IS NOT NULL';
        }

        if ($model['order_tran_code']) {
            $paramsStr .= " AND CONCAT(pt_hn,' ',zp.prefix_name,pt_firstname,' ',pt_lastname) LIKE :search_order";
            $paramsArry[':search_order'] = "%{$model['order_tran_code']}%";
        }

        $sql = "SELECT DISTINCT zot.order_tran_visit_id AS order_tran_id,pt_hn,CONCAT(zp.prefix_name_cid,pt_firstname,' ',pt_lastname) AS fullname
                ,sect_name,ds.sect_code,right_name,order_tran_status,zle.external_result_status
                FROM zdata_order_tran zot
                INNER JOIN zdata_visit zv ON(zv.id=zot.order_tran_visit_id)
                INNER JOIN const_order co ON(co.order_code=zot.order_tran_code)
                INNER JOIN dept_sect ds ON(ds.sect_code=zot.order_tran_dept)
                INNER JOIN zdata_patientprofile zpp ON (zpp.id=zot.ptid)
                LEFT JOIN zdata_prefix zp ON (zp.prefix_id=zpp.pt_prefix_id )
                INNER JOIN zdata_patientright AS zpr ON(zpr.id=(SELECT MAX(id) FROM zdata_patientright WHERE rstat='1' AND right_pt_id=zot.ptid))
                INNER JOIN zdata_right zr ON(zr.right_code=zpr.right_code)      
                LEFT JOIN zdata_lab_external zle ON(zle.external_order_id=zot.id AND zle.rstat='1')
                WHERE zot.rstat='1' AND order_tran_status<>'1' AND co.external_flag='Y' $paramsStr
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

    public static function getCashierCounter($model, $params, $receipt_status) {
        $model->load($params);
        $date = \appxq\sdii\utils\SDdate::phpThDate2mysqlDate($model['create_date']);
        $paramsStr = " AND DATE(zv.visit_date) = :date";
        $paramsArry[':date'] = $date;
        if ($model['order_tran_code']) {
            $paramsStr .= " AND CONCAT(pt_hn,' ',fullname) LIKE :search_order";
            $paramsArry[':search_order'] = "%{$model['order_tran_code']}%";
        }
        if ($receipt_status == '1') {
            $paramsStr .= " AND order_tran_cashier_status = ''";
        } else if ($receipt_status == '2') {
            $paramsStr .= " AND order_tran_cashier_status <> ''";
        }

        $sql = "SELECT pt_hn,fullname,right_name,sum_pay,sum_notpay,order_tran_cashier_status,visit_id,right_code
FROM (SELECT pt_hn,fullname,right_name
,CASE WHEN (zpr.right_code = 'CASH') THEN SUM(zot.order_tran_pay)+SUM(zot.order_tran_notpay) WHEN (zpr.right_code = 'ORI') THEN '0' ELSE SUM(zot.order_tran_pay) END  AS sum_pay
,CASE WHEN (zpr.right_code = 'CASH') THEN '0' WHEN (zpr.right_code = 'ORI') THEN SUM(zot.order_tran_pay)+SUM(zot.order_tran_notpay)  ELSE SUM(zot.order_tran_notpay) END AS sum_notpay
,order_tran_cashier_status,zv.id AS visit_id,zr.right_code
                FROM zdata_order_tran zot
                INNER JOIN vpatient_profile AS vpp ON(vpp.pt_id=zot.my_59ad6ccc47b10)
                INNER JOIN zdata_patientright AS zpr ON(zpr.id=(SELECT MAX(id) FROM zdata_patientright WHERE rstat='1' AND right_pt_id=zot.ptid))
                INNER JOIN zdata_right zr ON(zr.right_code=zpr.right_code)
                INNER JOIN zdata_visit zv ON(zv.id=zot.order_tran_visit_id)
                WHERE zot.rstat= '1' $paramsStr
                GROUP BY zot.order_tran_visit_id,pt_hn,fullname,right_name,order_tran_cashier_status   
                ORDER BY zot.order_tran_cashier_status ASC,zot.update_date DESC
                ) AS PP WHERE sum_pay > 0";

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

    public static function getWardBed($model, $params, $dept) {
        /* $model->load($params); กลับมาใช่นี้เมื่อว่าง
          $paramsStr = '';
          $paramsArry[':dept'] = $dept;
          if ($model['order_tran_code']) {
          $paramsStr .= " AND CONCAT(pt_hn,' ',fullname) LIKE :search_order";
          $search_order = $model['order_tran_code'];
          $paramsArry[':search_order'] = "%$search_order%";
          } */
        $model->load($params);

        $where_str = '';
        if (isset($model->bed_name) && $model->bed_name != '') {
            $where_str = 'AND admit_status in (2,3)';
        }

        $sql = "SELECT zwb.id AS bed_id,visit_id,admit_id,pt_id,zwb.bed_code,zwb.bed_name,admit_an,pt_hn,pt_pic,fullname, admit_status, zwb.bed_type, bed_tran_status
                FROM zdata_ward_bed zwb
                LEFT JOIN (SELECT zbt.bed_tran_bed_id,admit_an,zpp.pt_hn,pt_pic,CONCAT(pt_firstname,' ',pt_lastname) AS fullname,
                za.admit_visit_id AS visit_id,za.id AS admit_id,zpp.id AS pt_id, za.admit_status, zbt.bed_tran_status
                FROM zdata_admit za
                INNER JOIN zdata_bed_tran zbt ON(za.id=zbt.bed_tran_admit_id)
                INNER JOIN zdata_patientprofile zpp ON(zpp.id=za.ptid)
                WHERE za.rstat not in(0,3) AND zbt.bed_tran_status = 2 ) AS pt_admit ON(pt_admit.bed_tran_bed_id=zwb.id)
                WHERE zwb.rstat='1' AND bed_dept = :dept $where_str
                AND CONCAT(IFNULL(pt_hn,''), ' ',IFNULL(fullname,'')) LIKE :pt_hn";


        $dataProvider = new \yii\data\SqlDataProvider([
            'sql' => $sql,
            'params' => [':dept' => "$dept", ':pt_hn' => "%$model->bed_name%"],
            'pagination' => false,
        ]);

        return $dataProvider;
    }

    public static function getRightCounter($model, $params, $date) {
        $model->load($params);

        $paramsStr = 'AND DATE(zv.visit_date)=:date';
        $paramsArry[':date'] = $date;
        if ($model['right_code']) {
            $paramsStr .= " AND CONCAT(vp.pt_hn,' ',vp.pt_firstname,' ',vp.pt_lastname) LIKE :search_right";
            $paramsArry[':search_right'] = "%{$model['right_code']}%";
        }

        $sql = "SELECT zv.id AS visit_id,
                    zpr.id AS right_id,
                    vp.pt_hn,
                    vp.pt_cid,
                    zv.visit_type,
                    zpr.right_status,
                    zv.visit_pt_id,
                    CONCAT(IFNULL(prefix.prefix_name,''),vp.pt_firstname,' ',vp.pt_lastname) AS fullname,
                    zr.right_name
                FROM zdata_visit zv
                INNER JOIN zdata_patientprofile vp ON(vp.id=zv.ptid)
                LEFT JOIN zdata_prefix prefix ON(prefix.prefix_id=vp.pt_prefix_id)
		INNER JOIN zdata_patientright AS zpr ON(right_visit_id=zv.id)
                INNER JOIN zdata_right zr ON(zr.right_code=zpr.right_code)
                WHERE zv.rstat not in(0, 3) $paramsStr
                ORDER BY zv.create_date DESC,zpr.right_status ASC";

        $dataProvider = new \yii\data\SqlDataProvider([
            'sql' => $sql,
            'params' => $paramsArry,
            //'sort' => ['attributes' => ['pt_hn', 'fullname', 'visit_type', 'visit_right_status']],
            'pagination' => [
                'pageSize' => 15,
            ],
        ]);

        return $dataProvider;
    }

    /**
     * function saveDataNoSys
     *
     * @param string @ezf_id ezf_id
     * @param string $ezf_table table name
     * @param string $dataid dataid
     * @param array() $initdata $data['visit_no']
     */
    public static function saveDataNoSys($ezf_id, $ezf_table, $dataid, $data) {
        try {
            $userProfile = isset(Yii::$app->user->identity->profile) ? Yii::$app->user->identity->profile : \common\modules\user\models\User::findOne(['id' => '1'])->profile;
            $model = EzfUiFunc::loadTbData($ezf_table, $dataid);

            if ($model) {
                $model->attributes = $data;
                if (isset($data['rstat'])) {
                    $model->rstat = $data['rstat'];
                } else {
                    $model->rstat = 1;
                }
                $model->user_update = $userProfile->user_id;
                $model->update_date = new \yii\db\Expression('NOW()');

                $result = $model->save();
            } else {
                $result = [
                    'status' => 'error',
                    'message' => SDHtml::getMsgError() . Yii::t('app', 'No results found.'),
                    'data' => $dataid,
                ];
                return $result;
            }

            if ($result) {
                EzfUiFunc::saveTarget($model, $ezf_id);
                EzfUiFunc::saveLog($model, $ezf_id);

                $result = [
                    'status' => 'success',
                    'action' => 'update',
                    'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Save completed.'),
                    'data' => $model->attributes,
                ];
                return $result;
            } else {
                $result = [
                    'status' => 'error',
                    'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not save the data.'),
                    'data' => $model->attributes,
                ];
                return $result;
            }
        } catch (\yii\db\Exception $e) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
            $result = [
                'status' => 'error',
                'message' => SDHtml::getMsgError() . Yii::t('app', 'Database error') . ' ' . $e->getMessage(),
            ];
            return $result;
        }
    }

    public static function saveDataNoSysByTarget($ezf_id, $ezf_table, $target, $data) {
        try {
            $userProfile = isset(Yii::$app->user->identity->profile) ? Yii::$app->user->identity->profile : \common\modules\user\models\User::findOne(['id' => '1'])->profile;
            $models = PatientFunc::loadTbDataByTargetAll($ezf_table, $target);
            if ($models) {
                foreach ($models as $model) {
                    $model->attributes = $data;
                    if (isset($data['rstat'])) {
                        $model->rstat = $data['rstat'];
                    } else {
                        $model->rstat = 1;
                    }
                    $model->user_update = $userProfile->user_id;
                    $model->update_date = new \yii\db\Expression('NOW()');

                    $result = $model->save();

                    if ($result) {
                        EzfUiFunc::saveTarget($model, $ezf_id);
                        EzfUiFunc::saveLog($model, $ezf_id);
                    }
                }
                $result = [
                    'status' => 'success',
                    'action' => 'update',
                    'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Save completed.'),
                    'data' => $model->attributes,
                ];
            } else {
                $result = [
                    'status' => 'error',
                    'message' => SDHtml::getMsgError() . Yii::t('app', 'No results found.'),
                ];
            }
            return $result;
        } catch (\yii\db\Exception $e) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
            $result = [
                'status' => 'error',
                'message' => SDHtml::getMsgError() . Yii::t('app', 'Database error') . ' ' . $e->getMessage(),
            ];
            return $result;
        }
    }

    public static function saveDataNoSysByField($ezf_id, $ezf_table, $field, $data) {
        try {
            $userProfile = isset(Yii::$app->user->identity->profile) ? Yii::$app->user->identity->profile : \common\modules\user\models\User::findOne(['id' => '1'])->profile;
            $models = PatientFunc::loadTbDataByField($ezf_table, $field);
            if ($models) {
                foreach ($models as $model) {
                    $model->attributes = $data;
                    if (isset($data['rstat'])) {
                        $model->rstat = $data['rstat'];
                    } else {
                        $model->rstat = 1;
                    }
                    $model->user_update = $userProfile->user_id;
                    $model->update_date = new \yii\db\Expression('NOW()');

                    $result = $model->save();

                    if ($result) {
                        EzfUiFunc::saveTarget($model, $ezf_id);
                        EzfUiFunc::saveLog($model, $ezf_id);
                    }
                }
                $result = [
                    'status' => 'success',
                    'action' => 'update',
                    'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Save completed.'),
                    'data' => $model->attributes,
                ];
            } else {
                $result = [
                    'status' => 'error',
                    'message' => SDHtml::getMsgError() . Yii::t('app', 'No results found.'),
                ];
            }
            return $result;
        } catch (\yii\db\Exception $e) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
            $result = [
                'status' => 'error',
                'message' => SDHtml::getMsgError() . Yii::t('app', 'Database error') . ' ' . $e->getMessage(),
            ];
            return $result;
        }
    }

    /**
     * 
     * @param type $ezf_id
     * @param type $ezf_table
     * @param type $target
     * @return $model
     */
    public static function loadDataByTarget($ezf_id, $ezf_table, $target = '') {
        $modelFields = EzformFields::find()
                ->where('ezf_id = :ezf_id', [':ezf_id' => $ezf_id])
                ->orderBy(['ezf_field_order' => SORT_ASC])
                ->all();

        $model = EzfFunc::setDynamicModel($modelFields, $ezf_table, Yii::$app->session['ezf_input'], Yii::$app->session['show_varname']);

        if ($target != '') {

            $modelSave = self::loadTbDataByTarget($ezf_table, $target);
            if (!$modelSave) {
                return FALSE;
            }

            $model->attributes = $modelSave->attributes;

            $model->afterFind();
        } else {
            $model->init();
        }

        return $model;
    }

    public static function loadTbDataByTarget($ezf_table, $target, $date = null) {
        try {
            $paramsStr = 'rstat = 1 AND target=:target';
            $paramsArry[':target'] = $target;
            if (isset($date)) {
                $paramsStr .= " AND DATE(update_date) = :date";
                $paramsArry[':date'] = $date;
            }

            $model = new TbdataAll();
            $model->setTableName($ezf_table);

            $model = $model->find()->where($paramsStr, $paramsArry)->orderBy(['create_date' => SORT_DESC])->one();

            if (!$model) {
                return FALSE;
            }
            return $model;
        } catch (\yii\db\Exception $e) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
            return FALSE;
        }
    }

    public static function loadTbDataByTargetAll($ezf_table, $target, $date = null) {
        try {
            $paramsStr = 'rstat = 1 AND target=:target';
            $paramsArry[':target'] = $target;
            if (isset($date)) {
                $paramsStr .= " AND DATE(create_date) = :date";
                $paramsArry[':date'] = $date;
            }

            $model = new TbdataAll();
            $model->setTableName($ezf_table);

            $model = $model->find()->where($paramsStr, $paramsArry)->all();

            if (!$model) {
                return FALSE;
            }
            return $model;
        } catch (\yii\db\Exception $e) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
            return FALSE;
        }
    }

    /**
     * 
     * @param type $ezf_table
     * @param type $dataFiled
     * @param type $limit
     * @return boolean
     */
    public static function loadTbDataByField($ezf_table, $dataFiled, $limit = "one") {
        try {
            $paramsStr = 'rstat = 1';

            foreach ($dataFiled as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $keyFiled => $valueFiled) {
                        $paramsArry[':' . $keyFiled] = $valueFiled;
                        $paramsStr .= ' AND ' . $key . '=:' . $keyFiled;
                    }
                } else {
                    $paramsArry[':' . $key] = $value;
                    $paramsStr .= ' AND ' . $key . '=:' . $key;
                }
            }

            $model = new TbdataAll();
            $model->setTableName($ezf_table);
            if ($limit == "one") {
                $model = $model->find()->where($paramsStr, $paramsArry)->one();
            } else {
                $model = $model->find()->where($paramsStr, $paramsArry)->all();
            }

            if (!$model) {
                return FALSE;
            }
            return $model;
        } catch (\yii\db\Exception $e) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
            return FALSE;
        }
    }

    public static function loadTbDataAll($ezf_table) {
        try {
            $paramsStr = 'rstat = 1';
            $paramsArry = [];

            $model = new TbdataAll();
            $model->setTableName($ezf_table);

            $model = $model->find()->where($paramsStr, $paramsArry)->all();

            if (!$model) {
                return FALSE;
            }
            return $model;
        } catch (\yii\db\Exception $e) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
            return FALSE;
        }
    }

    public static function getVisitHospital($pt_id) {
        $sql = "SELECT DISTINCT ch.`code`,REPLACE(ch.`name`,'โรงพยาบาล','รพ.') AS name
            FROM zdata_visit zv
            INNER JOIN const_hospital ch ON(ch.`code`=zv.xsourcex)
            WHERE zv.rstat='1' AND visit_pt_id=:pt_id";

        $dataProvider = new \yii\data\SqlDataProvider([
            'sql' => $sql,
            'params' => [':pt_id' => $pt_id],
        ]);

        return $dataProvider;
    }

    public static function getVisit($model) {
        if (is_array($model['sitecode'])) {
            $subSql = "AND zv.xsourcex IN('" . implode("','", $model['sitecode']) . "')";
        } else {
            $subSql = "AND zv.xsourcex='{$model['sitecode']}'";
        }

        if ($model['visit_date']) {
            $date = explode(",", $model['visit_date']);
            if (count($date) > 1) {
                $stDate = date('Y-m-d', strtotime($date[0]));
                $enDate = date('Y-m-d', strtotime($date[1]));

                $subSql .= " AND visit_date BETWEEN '{$stDate} 00:00:00' AND '{$enDate} 23:59:59'";
            }
        }

        $sql = "SELECT DATE(visit_date) AS visit_date,visit_type,zv.id,visit_admit_an
            FROM zdata_visit zv
            INNER JOIN zdata_tk ztk ON(ztk.tk_visit_id=zv.id)
            INNER JOIN const_hospital ch ON(ch.`code`=zv.xsourcex)
            WHERE zv.rstat='1' AND visit_pt_id=:pt_id {$subSql} ORDER BY visit_date DESC";

        $dataProvider = new \yii\data\SqlDataProvider([
            'sql' => $sql,
            'params' => [':pt_id' => $model['target']],
        ]);

        return $dataProvider;
    }

    /**
     * function backgroundInsert
     *
     * @param $ezf_id
     * @param string $dataid dataid
     * @param string $target target
     * @param array $initdata $data['visit_no']
     * @param null $post
     * @param string $version
     * @param int $db2 default 0
     * @param null $user_id
     * @return \appxq\sdii\models\SDDynamicModel|array|bool
     * @throws HttpException
     */
    public static function backgroundInsert($ezf_id, $dataid, $target, $initdata = [], $post = null, $version = 'v1', $db2 = 0, $user_id = NULL) {
        //$dataid = '';

        $modelEzf = EzfQuery::getEzformOne($ezf_id);
        if ($modelEzf) {
            $version = $modelEzf->ezf_version;
            if (!$modelEzf) {
                $modelEzf = new \backend\modules\ezforms2\models\Ezform();
                $modelEzf->ezf_name = 'Unnamed Form';
                throw new HttpException('No results found.');
            }

            if ($db2 == 1) {
                $checkDb2 = EzfFunc::updateDoubleData($modelEzf, $dataid);
                if ($checkDb2) {
                    $modelEzf->ezf_table = $checkDb2;
                } else {
                    throw new HttpException('Can not update `double data`.');
                }
            }

            //fix version by dataid
            if ($dataid != '') {
                $modelZdata = EzfUiFunc::loadTbData($modelEzf->ezf_table, $dataid);
                if ($modelZdata) {
                    if ($modelZdata->rstat != 0 && !empty($modelZdata->ezf_version)) {
                        $version = ($version && in_array($modelZdata->rstat, [0, 1])) ? $version : $modelZdata->ezf_version;
                    }
                    if ($db2 == 1 && !empty($modelZdata->ezf_version)) {
                        $version = ($version && in_array($modelZdata->rstat, [0, 1])) ? $version : $modelZdata->ezf_version;
                    }
                    if (!empty($modelZdata->ezf_version)) {
                        $modelEzf->ezf_version = ($version && in_array($modelZdata->rstat, [0, 1])) ? $version : $modelZdata->ezf_version;
                    }
                } else {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    throw new HttpException('No results found.');
                }
            }

            if ($modelEzf->enable_version) {
                $modelVersion = EzfQuery::getEzformConfigApprov($modelEzf->ezf_id, $version);
            } else {
                $modelVersion = EzfQuery::getEzformConfig($modelEzf->ezf_id, $version);
            }

            if ($modelVersion) {
                $modelEzf->field_detail = $modelVersion->field_detail;
                $modelEzf->ezf_sql = $modelVersion->ezf_sql;
                $modelEzf->ezf_js = $modelVersion->ezf_js;
                $modelEzf->ezf_error = $modelVersion->ezf_error;
                $modelEzf->ezf_options = $modelVersion->ezf_options;
            } else {
                Yii::$app->response->format = Response::FORMAT_JSON;
                throw new HttpException('No version found');
            }

            $modelFields = EzfQuery::getFieldAll($modelEzf->ezf_id, $version);

            Yii::$app->session['show_varname'] = 0;
            Yii::$app->session['ezf_input'] = EzfQuery::getInputv2All();

            if ($user_id) {
                $userProfile = \common\modules\user\models\User::findOne(['id' => $user_id])->profile;
            } else {
                $userProfile = Yii::$app->user->identity->profile;
            }

            $model = EzfFunc::setDynamicModel($modelFields, $modelEzf->ezf_table, Yii::$app->session['ezf_input'], Yii::$app->session['show_varname']);
            $model = EzfUiFunc::loadData($model, $modelEzf->ezf_table, $dataid);

            if (!$model) {// dataid ส่งมาผิดหาไม่เจอ / ไมคิดรวมถ้าส่ง '' มา
                Yii::$app->response->format = Response::FORMAT_JSON;
                throw new HttpException('No results found. #2');
            }

            $targetReset = false;
            if (!isset($model->id)) {// ถ้ามี new record ที่คนนั้นสร้างไว้ ให้ใช้อันนั้น
                $modelNewRecord = EzfUiFunc::loadNewRecord($model, $modelEzf->ezf_table, $userProfile->user_id);

                if ($modelNewRecord) {
                    $targetReset = true;
                    $model = $modelNewRecord;
                }
            }

            //ขั้นตอนกรอกข้อมูลสำคัญ
            $evenFields = EzfFunc::getEvenField($modelFields);
            $special = isset($evenFields['special']) && !empty($evenFields['special']);

            if (isset($evenFields['target']) && !empty($evenFields['target'])) { //มีเป้าหมาย
                if ($targetReset) {
                    $model[$evenFields['target']['ezf_field_name']] = '';
                }

                $modelEzfTarget = EzfQuery::getEzformOne($evenFields['target']['ref_ezf_id']);
                $target = ($target == '') ? $model[$evenFields['target']['ezf_field_name']] : $target;
                $dataTarget = EzfQuery::getTargetNotRstat($modelEzfTarget->ezf_table, $target);

                $disable[$evenFields['target']['ezf_field_name']] = 1;

                if ($dataTarget) {//เลือกเป้าหมายแล้ว
                    if (isset($modelEzf['unique_record']) && $modelEzf['unique_record'] == 2) {
                        $unique = EzfUiFunc::loadUniqueRecord($model, $modelEzf->ezf_table, $target);
                        Yii::$app->response->format = Response::FORMAT_JSON;
                        throw new HttpException('This form only records 1 record.');
                    }


                    //เพิ่มและแก้ไขข้อมูล system
                    $model->attributes = EzfUiFunc::setSystemProperty($model, $target, $dataTarget, $modelEzf->ezf_table, $evenFields['target']['ezf_field_name'], '', $special, $userProfile, $evenFields['target'], 0);
                    EzfFunc::inProcess($model, $modelEzfTarget->ezf_id, $modelEzf->ezf_table);
                    $model->afterFind();
                } else {
                    //ฟอร์มค้นหาเป้าหมาย
//                    $modelTargetFields = [$evenFields['target']];
                    throw new HttpException('No results found. No dataTarget');
                }
            } else {// ไม่มีเป้าหมาย
                if ($model->id) {
                    $dataTarget = EzfQuery::getTarget($modelEzf->ezf_table, $model->id);
                } else {
                    $dataTarget = [];
                }

                if (isset($evenFields['special']['ezf_field_name'])) {
                    $disable[$evenFields['special']['ezf_field_name']] = 1;
                }

                //เพิ่มและแก้ไขข้อมูล system
                $fieldSpecial = null;
                $model->attributes = EzfUiFunc::setSystemProperty($model, $target, $dataTarget, $modelEzf->ezf_table, '', $fieldSpecial, $special, $userProfile, NULL, 0);
                $model->afterFind();
            }

            if (!empty($initdata)) {//กำหนดค่าเริ่มต้น
                if ($post) {
                    $model->load($post);
                }
                $rstat = Yii::$app->request->post('submit') ? Yii::$app->request->post('submit') : $model->rstat;
                $initdata['report_date'] = date('Y-m-d H:i:s');
                $model->attributes = $initdata;

                if (isset($initdata['rstat'])) {
                    $model->rstat = $initdata['rstat'];
                } else {
                    $model->rstat = 1;
                }
                $model->create_date = $model['rstat'] == 0 ? date('Y-m-d H:i:s') : $model['create_date']; //new \yii\db\Expression('NOW()')

                $model->ezf_version = $version;
                $model->user_update = $userProfile->user_id;
                $model->update_date = date('Y-m-d H:i:s');

                // $model->update_date = new \yii\db\Expression('NOW()');
                //\appxq\sdii\utils\VarDumper::dump($model);
                $validate = [];
                foreach ($modelFields as $keyf => $valuef) {
                    $pos = strpos($valuef['ezf_field_validate'], 'UniqueValidator');
                    if ($pos === false) {
                    } else {
                        $validate[] = $valuef['ezf_field_name'];
                    }
                }
                
                if(!$model->validate($validate) && $rstat!=3) {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    $emsg = '';
                    if (isset($model->errors)) {
                        foreach ($model->errors as $ekey => $evalue) {
                            $evalue = implode(', ', $evalue);
                            $emsg .= "<strong>[$ekey]</strong> : $evalue<br>";
                        }
                    }

                    $result = [
                        'status' => 'error',
                        'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not update the data.<br>' . $emsg),
                        'data' => $dataid,
                    ];
                    return $result;
                }

                $result = EzfUiFunc::saveData($model, $modelEzf->ezf_table, $modelEzf->ezf_id, $model->id);

                return $result;
            }

            return $model;
        }
        return false;
    }

    public static function getTotal($provider, $fieldName) {
        $total = 0;
        foreach ($provider as $item) {
            $total += $item[$fieldName];
        }

        return $total;
    }

    public static function getPatientSearch($q, $sitecode) {
        $nonimg = Yii::getAlias('@storageUrl/images') . '/nouser.png';
        $img = Yii::getAlias('@storageUrl/ezform/fileinput') . '/';
        $sql = "SELECT id,pt_firstname,pt_lastname,pt_cid,pt_hn,IFNULL(CONCAT('$img',pt_pic),'$nonimg') AS pt_pic,pt_bdate
                ,pt_address,pt_moi,pt_addr_zipcode
                FROM zdata_patientprofile zp
                WHERE rstat='1' AND zp.xsourcex=:xsourcex AND (CONCAT(pt_cid,pt_firstname,' ',pt_firstname,pt_hn)) LIKE :q";

        $dataProvider = new \yii\data\SqlDataProvider([
            'sql' => $sql,
            'params' => [':xsourcex' => $sitecode, ':q' => "%$q%"],
            'sort' => ['attributes' => ['order_tran_code', 'order_name', 'full_price', 'create_date']],
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        return $dataProvider;
    }

    public static function callWebService($url) {
        //$data = array('token' => '4214698aabbccpa', 'heaader_ln' => '171109054');
        //$data_json = json_encode($data);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Content-Type: application/json'));
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, TRUE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, TRUE);
        //curl_setopt($curl, CURLOPT_POSTFIELDS, $data_json);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET'); //POST,GET
        $response = curl_exec($curl);
        $info = curl_getinfo($curl);
        curl_close($curl);
        $header = trim(substr($response, 0, $info['header_size']));
        $body = substr($response, $info['header_size']);

        return $body;
    }

    public static function getRightOnlineByNhso($cid) {
        $nhsoJsonData = Nhso::getNhso($cid);
        $body = json_decode($nhsoJsonData,true);
        if (empty($body['status-system'])) {
            if (isset($body['maininscl'])) {
                if ($body['maininscl'] == 'WEL') {
                    $body['maininscl'] = 'UCS';
                }
            } else {
                $body['maininscl'] = '';
                $body['hmain_name'] = '';
                $body['maininscl_name'] = '';
                $body['subinscl'] = '';
                $body['hmain_name'] = '';
            }
            $body['connect'] = 'ok';
        } else {
            $body['connect'] = 'error';
            $body['maininscl'] = '';
            $body['hmain_name'] = '';
        }

        return $body;
    }

    public static function getRightSks($cid, $hn) {
        $url = Yii::$app->params['udchWebService'] . "module/customer/show_subpatent.php"
                . "?action=sks_approve&cid={$cid}&hn={$hn}";
        $body = PatientFunc::callWebService($url);
        $body = json_decode($body, TRUE);

        return $body;
    }

    public static function checkPtProfileOld($cid, $userid = '') {
        $url = Yii::$app->params['udchWebService'] . "module/customer/show_subpatent.php"
                . "?action=getPtSereneThaiHis&cid={$cid}&userid={$userid}";
        $body = PatientFunc::callWebService($url);
        $body = json_decode($body, TRUE);

        return $body;
    }

    public static function getCalendarStopEvent($y, $dateST = '', $dateEN = '') {
        $url = Yii::$app->params['udchWebService'] . "module/customer/show_subpatent.php"
                . "?action=calendarStopEvent&dateST={$dateST}&dateEN={$dateEN}&year={$y}";
        $body = PatientFunc::callWebService($url);
        $body = json_decode($body, TRUE);

        return $body;
    }

    public static function getXrayResult($hn, $date) {
        $url = Yii::$app->params['udchWebService'] . "module/customer/show_subpatent.php"
                . "?action=getResultXray&hn={$hn}&date={$date}";
        $body = PatientFunc::callWebService($url);
        $body = json_decode($body, TRUE);

        if ($body['status'] == FALSE) {
            $body = FALSE;
        }
        return $body;
    }

    public static function getPrintStickerCyto($value) {
        $url = Yii::$app->params['udchWebService'] . "module/lab/report_label_spec_ex.php"
                . "?pt={$value}";
        $body = PatientFunc::callWebService($url);

        return $body;
    }

    public static function getImportSereneToZdata() {
        $url = Yii::$app->params['udchWebService'] . "module/customer/show_subpatent.php"
                . "?action=getImportData";
        $body = PatientFunc::callWebService($url);
        $body = json_decode($body, TRUE);

        return $body;
    }

    public static function integeter2date($intDate) {
        if ($intDate == '') {
            return '';
        }
        //ddmmyyyy To dd/mm/yyyy
        return substr($intDate, 0, 2) . '/' . substr($intDate, 2, 2) . '/' . substr($intDate, 4, 4);
    }

    public static function visit_type($visit_id) {
        if ($visit_id == '1') {
            return Yii::t('patient', 'Checkup');
        } else if ($visit_id == '2') {
            return Yii::t('patient', 'Follow up');
        } else if ($visit_id == '3') {
            return Yii::t('patient', 'Refer');
        } else {
            return Yii::t('patient', 'Treatment');
        }
    }

    public static function ArrayToDataProvider($arrData) {
        $dataProvider = new \yii\data\ArrayDataProvider([
            'allModels' => $arrData,
            'pagination' => [
                'pageSize' => 15,
            ],
            'sort' => [
                'attributes' => ['cpoe_id', 'cpoe_ids'],
            ],
        ]);

        return $dataProvider;
    }

    public static function getAdmitDashboard($model, $params) {
        $model->load($params);
        $paramsStr = ' AND admit_status=:admit_status';
        $paramsArry[':admit_status'] = $model['admit_status'];
        if ($model['update_date']) {
            $date = \appxq\sdii\utils\SDdate::phpThDate2mysqlDate($model['update_date']);
            $paramsArry[':update_date'] = $date;
            $paramsStr .= ' AND DATE(za.update_date)=:update_date';
        }

        $sql = "SELECT admit_date,vp.pt_hn,admit_an,vp.fullname,CONCAT(zwb.bed_code,' : ',zwb.bed_name) AS bed,ds.sect_name
                ,za.admit_status,DATEDIFF(NOW(),admit_date) AS admit_amount
                FROM zdata_admit za
                INNER JOIN zdata_bed_tran zbt ON(za.id=zbt.bed_tran_admit_id)
                LEFT JOIN zdata_ward_bed zwb ON(zwb.id=zbt.bed_tran_bed_id) 
                LEFT JOIN dept_sect ds ON(ds.sect_code=zwb.bed_dept)
                INNER JOIN vpatient_profile vp ON(vp.pt_id=za.ptid)
                WHERE za.rstat='1' AND zbt.rstat='1' AND zbt.bed_tran_status='2' $paramsStr";

        $dataProvider = new \yii\data\SqlDataProvider([
            'sql' => $sql,
            'params' => $paramsArry,
            //'sort' => ['attributes' => ['pt_hn', 'fullname', 'visit_type', 'visit_right_status']],
            'pagination' => [
                'pageSize' => 15,
            ],
        ]);

        return $dataProvider;
    }

    public static function getDrgCounter($model, $params) {
        $model->load($params);

        $date = \appxq\sdii\utils\SDdate::phpThDate2mysqlDate($model['create_date'], '-');
        $paramsArry[':create_date'] = $date;
        $paramsStr = "AND DATE(zv.visit_date)=:create_date";

        if ($model['ptid']) {
            $paramsStr .= " AND CONCAT(pt_hn,' ',zp.prefix_name,pt_firstname,' ',pt_lastname) LIKE :fullname";
            $paramsArry[':fullname'] = "%{$model['ptid']}%";
        }

        $sql = "SELECT DATE(zv.visit_date) AS visit_date,zvt.visit_type_name
,zpp.pt_hn,CONCAT(zp.prefix_name,pt_firstname,' ',pt_lastname) AS fullname
,zdt.di_txt,CONCAT_WS(', ',zdt.di_icd10,zdt.di_icd10_2,zdt.di_icd10_3,di_icd10_4,di_icd10_5) AS diag_icd10
,zv.id AS visit_id,zpp.id AS ptid,zv.visit_type,GROUP_CONCAT(DISTINCT zor.di_operat_icd9 SEPARATOR ', ') AS icd9
FROM zdata_visit zv
INNER JOIN zdata_patientprofile zpp ON(zpp.id=zv.visit_pt_id)
INNER JOIN zdata_visit_type zvt ON(zvt.visit_type_code=zv.visit_type)
LEFT JOIN zdata_prefix zp ON (zp.prefix_id=zpp.pt_prefix_id )
LEFT JOIN zdata_dt zdt ON(zdt.di_visit_id=zv.id)
LEFT JOIN zdata_operat zor ON(zor.di_operat_visit_id=zv.id)
INNER JOIN zdata_receipt_mas zrm ON(zrm.receipt_visit_id=zv.id)
WHERE zv.rstat='1' $paramsStr
GROUP BY zv.id";

        $dataProvider = new \yii\data\SqlDataProvider([
            'sql' => $sql,
            'params' => $paramsArry,
            'sort' => ['attributes' => ['pt_hn', 'fullname', 'visit_type_name', 'di_txt', 'diag_icd10']],
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

        return $dataProvider;
    }

    public static function getInputValue($ezf_id, $data, $fieldName) {
        $modelFields = \backend\modules\ezforms2\models\EzformFields::find()->where('ezf_id = :ezf_id AND ezf_field_name = :ezf_field_name', [':ezf_field_name' => $fieldName, ':ezf_id' => $ezf_id])->one();
        if (isset(Yii::$app->session['ezf_input'])) {
            $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($modelFields['ezf_field_type'], Yii::$app->session['ezf_input']);
        }
        return \backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $modelFields, $data);
    }

    public static function getLabelValue($ezf_id, $fieldName) {
        $modelFields = \backend\modules\ezforms2\models\EzformFields::find()->where('ezf_id = :ezf_id AND ezf_field_name = :ezf_field_name', [':ezf_field_name' => $fieldName, ':ezf_id' => $ezf_id])->one();

        return $modelFields;
    }

}
