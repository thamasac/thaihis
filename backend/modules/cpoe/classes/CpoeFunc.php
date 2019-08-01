<?php

namespace backend\modules\cpoe\classes;

use Yii;
use appxq\sdii\utils\SDdate;
use backend\modules\ezforms2\classes\EzfFunc;
use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\thaihis\classes\ThaiHisQuery;
use backend\modules\thaihis\classes\ThaiHisFunc;
use backend\modules\ezforms2\models\TbdataAll;
use yii\data\ArrayDataProvider;

/**
 * OvccaFunc class file UTF-8
 * @author SDII <iencoded@gmail.com>
 * @copyright Copyright &copy; 2015 AppXQ
 * @license http://www.appxq.com/license/
 * @version 1.0.0 Date: 9 ก.พ. 2559 12:38:14
 * @link http://www.appxq.com/
 * @example 
 */
class CpoeFunc {

   public static function getVisitDept($dept, $date) {
        $nonimg = Yii::getAlias('@storageUrl/images') . '/nouser.png';
        $img = Yii::getAlias('@storageUrl/ezform/fileinput') . '/';
//        $field .= $field != '' ?  ',' : '';
        $field = '';
//        \appxq\sdii\utils\VarDumper::dump($field);
        $sql = "SELECT zv.target,zv.id,zpf.id AS pt_id,pt_prefix_id, CONCAT(pt_firstname,' ',pt_lastname) AS fullname
            ,zpf.pt_hn,zv.visit_type,pt_firstname,pt_lastname
            ,IFNULL(CONCAT('$img',zpf.pt_pic),'$nonimg') AS pt_pic,pt_bdate,zvt.id AS visit_tran_id,visit_type_name
                FROM zdata_visit zv
                INNER JOIN zdata_visit_type zvtt ON(zvtt.visit_type_code=zv.visit_type)
                INNER JOIN zdata_patientprofile zpf ON(zpf.id=zv.ptid)
                INNER JOIN zdata_order_header zoh ON zoh.order_visit_id=zv.id
                INNER JOIN zdata_visit_tran  zvt ON(zoh.id=zvt.order_header_id)
                WHERE visit_tran_status='1' AND (visit_tran_doc_status = '' OR visit_tran_doc_status IS NULL) AND visit_tran_dept=:dept
                AND zv.visit_date BETWEEN :dateST AND :dateEN
                ORDER BY zvt.update_date ASC";

        $dataProvider = new \yii\data\SqlDataProvider([
            'sql' => $sql,
            'params' => [':dept' => $dept, ':dateST' => "$date 00:00:00", ':dateEN' => "$date 23:59:59"],
        ]);

        return $dataProvider;
    }

    public static function getVisitOpdQue($dept, $date) {
        $nonimg = Yii::getAlias('@storageUrl/images') . '/nouser.png';
        $img = Yii::getAlias('@storageUrl/ezform/fileinput') . '/';
        $dept = ($dept == 'S047' || $dept == 'S074') ? "'S043','S074'" : "'$dept'";
        $sql = "SELECT DISTINCT zv.id,vp.pt_id,vp.fullname,vp.pt_hn,zv.visit_type
          ,IFNULL(CONCAT('$img',vp.pt_pic),'$nonimg') AS pt_pic,pt_bdate,visit_type_name,doc
          FROM zdata_visit zv
          INNER JOIN zdata_visit_type zvtt ON(zvtt.visit_type_code=zv.visit_type)
          INNER JOIN zdata_patientprofile vp ON(vp.pt_id=zv.visit_pt_id)
          INNER JOIN zdata_order_header zoh ON zoh.order_visit_id=zv.id
          INNER JOIN zdata_visit_tran  zvt ON(zoh.id=zvt.order_header_id)
          WHERE visit_tran_dept IN($dept) AND doc IS NULL
          AND zv.visit_date BETWEEN :dateST AND :dateEN
          ORDER BY zv.visit_date DESC";

        $dataProvider = new \yii\data\SqlDataProvider([
            'sql' => $sql,
            'params' => [':dateST' => "$date 00:00:00", ':dateEN' => "$date 23:59:59"],
            'pagination' => [
//                'page' => $page,
                'pageSize' => 10,
            ],
        ]);

        return $dataProvider;
    }

    public static function getVisitQue($options, $dept, $modelFields) {

        $ezf_id = $options['ezf_id'];
        $type_ezf_id = isset($options['type_ezf_id']) ? $options['type_ezf_id'] : null;
        $fields = $options['fields'];
        $target = $options['target'];

        $ezform = EzfQuery::getEzformOne($ezf_id);
        if ($type_ezf_id)
            $ezform_type = EzfQuery::getEzformOne($type_ezf_id);

//$modelFields = EzfQuery::getFieldAll($ezform->ezf_id, 'v1');
        if (Yii::$app->session['ezf_input']) {
            Yii::$app->session['show_varname'] = 0;
            Yii::$app->session['ezf_input'] = EzfQuery::getInputv2All();
        }
//$modelFields เพิ่ม Field ตารางอื่นๆเพื่อน ปั่น Model Joine Table   
        $model = ThaiHisFunc::setDynamicModel($modelFields, $ezform->ezf_table, Yii::$app->session['ezf_input'], Yii::$app->session['show_varname']);

        $searchModel = new TbdataAll();
        $searchModel->setTableName($ezform->ezf_table);

        $ezformParent = Null;
        $targetField = EzfQuery::getTargetOne($ezform->ezf_id);
        if (isset($targetField)) {
            $ezformParent = EzfQuery::getEzformById($targetField->ref_ezf_id);
        }
        
        $where = "DATE({$ezform['ezf_table']}.visit_date)= CURDATE() AND {$ezform['ezf_table']}.visit_status =1 ";
        $data = ThaiHisFunc::modelSearchAll($searchModel, $ezform, $targetField, $ezformParent, $modelFields, $where, 0, Yii::$app->request->queryParams);
        
        $provider = new ArrayDataProvider([
            'allModels' => $data,
//            'pagination' => [
//                'pageSize' => 10,
//            ],
            'sort' => [
                'attributes' => ['id'],
            ],
        ]);
        
        return $provider;
    }

    public static function getVisitOpdAll($dept, $date) {
        $nonimg = Yii::getAlias('@storageUrl/images') . '/nouser.png';
        $img = Yii::getAlias('@storageUrl/ezform/fileinput') . '/';
        $dept = ($dept == 'S047' || $dept == 'S074') ? "'S043','S074'" : "'$dept'";
        $sql = "SELECT DISTINCT zv.id,vp.pt_id,vp.fullname,vp.pt_hn,zv.visit_type,
            pt_firstname,pt_lastname,zv.target
            ,IFNULL(CONCAT('$img',vp.pt_pic),'$nonimg') AS pt_pic,pt_bdate,visit_type_name
                FROM zdata_visit zv
                INNER JOIN zdata_visit_type zvtt ON(zvtt.visit_type_code=zv.visit_type)
                INNER JOIN zdata_patientprofile vp ON(vp.pt_id=zv.visit_pt_id)                
                AND zv.visit_date BETWEEN :datestart AND :dateend
                ORDER BY zv.visit_date DESC";

        $dataProvider = new \yii\data\SqlDataProvider([
            'sql' => $sql,
            'params' => [':datestart' => $date . ' 00:00:00', ':dateend' => $date . ' 23:59:59'],
            'pagination' => [
//                'page' => $page,
                'pageSize' => 10,
            ],
        ]);

        return $dataProvider;
    }

    public static function getPatientInProject($model) {
        $paramsStr = "";
        $paramsArry = [];
        if ($model['target_project']) {
            $paramsStr .= "AND target_project = :target_project";
            $paramsArry[':target_project'] = $model['target_project'];
        }
        if ($model['fullname_project']) {
            $paramsStr .= "AND (CONCAT(zp.prefix_name,pt_firstname,' ',pt_lastname) LIKE :fullname_project OR fullname_project LIKE :fullname_project )";
            $paramsArry[':fullname_project'] = "%{$model['fullname_project']}%";
        }
        if ($model['cid_project']) {
            $paramsStr .= "AND cid_project LIKE :cid_project";
            $paramsArry[':cid_project'] = "{$model['cid_project']}%";
        }
        if ($model['date_start_project'] && $model['date_end_project']) {
            $date_start_project = SDdate::phpThDate2mysqlDate($model['date_start_project'], '-');
            $date_end_project = SDdate::phpThDate2mysqlDate($model['date_end_project'], '-');
            $paramsArry[':date_start_project'] = $date_start_project;
            $paramsArry[':date_end_project'] = $date_end_project;
            $paramsStr .= " AND date_start_project BETWEEN :date_start_project AND :date_end_project";
        }
        $sql = "SELECT DISTINCT zppn.cid_project,zppn.id,pt_cid, CASE WHEN zppn.fullname_project <> '' THEN zppn.fullname_project ELSE CONCAT(zp.prefix_name_cid,pt_firstname,' ',pt_lastname) END AS fullname_project 
                ,project_name,zrm.id AS receipt_id
                FROM zdata_project_patient_name zppn
                LEFT JOIN zdata_patientprofile pf ON(pf.pt_cid = zppn.cid_project)
                LEFT JOIN zdata_prefix zp ON(zp.prefix_id = pf.pt_prefix_id)
                LEFT JOIN zdata_project zpj ON(zpj.id=target_project)
                LEFT JOIN zdata_receipt_mas zrm ON(zrm.receipt_project_id=zppn.target_project AND zrm.ptid=pf.id)
                WHERE zppn.rstat = '1' $paramsStr";

        $dataProvider = new \yii\data\SqlDataProvider([
            'sql' => $sql,
            'params' => $paramsArry,
            'pagination' => [
                'pageSize' => 15,
            ],
        ]);
        return $dataProvider;
    }

    public static function getReportCashierCounter($user_id, $receipt_status, $qname, $date_now) {
        $nonimg = Yii::getAlias('@storageUrl/images') . '/nouser.png';
        $img = Yii::getAlias('@storageUrl/ezform/fileinput') . '/';
        $paramsStr = " AND DATE(zv.visit_date) = :date";
        $prefixStr = "";
        $subfix = "";
        $paramsArry[':date'] = !empty($date_now) ? $date_now : date('Y-m-d');
        if ($qname) {
            $paramsStr .= " AND CONCAT(pt_hn,' ',fullname) LIKE :fullname";
            $paramsArry[':fullname'] = "%$qname%";
        }
        if ($receipt_status == '1') {
            $prefixStr = "SELECT * FROM (";
            $subfix = ") AS KK WHERE sum_pay > 0";
            $paramsStr .= " AND order_tran_cashier_status = ''";
        } else if ($receipt_status == '2') {
            $paramsStr .= " AND order_tran_cashier_status <> ''";
        }
        $sql = "SELECT * FROM (SELECT pt_hn,fullname,sect_name,right_name,pt_bdate
,CASE WHEN (zpr.right_code = 'CASH') THEN SUM(zot.order_tran_pay)+SUM(zot.order_tran_notpay) WHEN (zpr.right_code = 'ORI') THEN SUM(zot.order_tran_pay) ELSE SUM(zot.order_tran_pay) END  AS sum_pay
,CASE WHEN (zpr.right_code = 'CASH') THEN '0' WHEN (zpr.right_code = 'ORI') THEN SUM(zot.order_tran_pay)+SUM(zot.order_tran_notpay)  ELSE SUM(zot.order_tran_notpay) END AS sum_notpay
,order_tran_cashier_status,zv.id AS visit_id,zr.right_code,IFNULL(CONCAT('$img',vpp.pt_pic),'$nonimg')  AS pt_pic
                FROM zdata_order_tran zot
                INNER JOIN const_order co ON(co.order_code=zot.order_tran_code)
                INNER JOIN dept_sect ds ON(ds.sect_code=zot.order_tran_dept)
                INNER JOIN zdata_patientprofile AS vpp ON(vpp.pt_id=zot.my_59ad6ccc47b10)
                INNER JOIN zdata_patientright AS zpr ON(zpr.id=(SELECT MAX(id) FROM zdata_patientright WHERE rstat='1' AND right_pt_id=zot.ptid))
                INNER JOIN zdata_right zr ON(zr.right_code=zpr.right_code)
                #INNER JOIN zdata_visit_tran zvt ON(zvt.visit_tran_visit_id=zot.order_tran_visit_id)
                INNER JOIN zdata_visit zv ON(zv.id=zot.order_tran_visit_id)
                WHERE zot.rstat= '1' $paramsStr
                GROUP BY zot.order_tran_visit_id,pt_hn,fullname,sect_name,right_name,order_tran_cashier_status   
                /*ORDER BY zot.update_date DESC*/) AS KK ORDER BY sum_pay DESC ";

        $dataProvider = new \yii\data\SqlDataProvider([
            'sql' => $sql,
            'params' => $paramsArry,
            //'sort' => ['attributes' => ['pt_hn', 'fullname', 'sect_name', 'doctor_name', 'right_name']],
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);
        return $dataProvider;
        //  return $dataProvider;
    }

    public static function getAppDept($dept, $date, $doc_id) {
        $nonimg = Yii::getAlias('@storageUrl/images') . '/nouser.png';
        $img = Yii::getAlias('@storageUrl/ezform/fileinput') . '/';
        $paramsStr = "zap.app_dept=:dept AND zap.app_date=:date";
        $paramsArry = [':dept' => $dept, ':date' => $date];
        if ($doc_id) {
            $paramsStr .= " AND zap.app_doctor=:app_doc";
            $paramsArry[':app_doc'] = $doc_id;
        }
        $sql = "SELECT zap.id,zap.app_status,zap.app_time,zis.ins_name,CONCAT(title,pf.firstname,' ',pf.lastname) AS doctor_name
                ,vp.pt_id,vp.fullname,vp.pt_hn,IFNULL(CONCAT('$img',vp.pt_pic),'$nonimg') AS pt_pic,pt_bdate
                FROM zdata_appoint zap
                INNER JOIN zdata_patientprofile vp ON(vp.pt_id=zap.app_pt_id)
                INNER JOIN zdata_inspect zis ON(zis.id=zap.app_insp_id)
                INNER JOIN  `profile` pf ON(pf.user_id=zap.app_doctor)
                WHERE $paramsStr
                ORDER BY zap.app_time ASC";

        $dataProvider = new \yii\data\SqlDataProvider([
            'sql' => $sql,
            'params' => $paramsArry,
        ]);

        return $dataProvider;
    }

    public static function getVisitDoctor($doc_id, $date) {
        $nonimg = Yii::getAlias('@storageUrl/images') . '/nouser.png';
        $img = Yii::getAlias('@storageUrl/ezform/fileinput') . '/';

        $sql = "SELECT zv.id,vp.ptid,vp.fullname,vp.pt_hn,zv.visit_type
            ,IFNULL(CONCAT('$img',vp.pt_pic),'$nonimg') AS pt_pic,pt_bdate,zvt.id AS visit_tran_id,visit_type_name
                FROM zdata_visit zv
                INNER JOIN zdata_visit_type zvtt ON(zvtt.visit_type_code=zv.visit_type)
                INNER JOIN zdata_patientprofile vp ON(vp.id=zv.target)
                INNER JOIN zdata_visit_tran  zvt ON(zv.id=zvt.visit_tran_visit_id)
                WHERE visit_tran_doc_status='1' AND visit_tran_doctor=:doc_id
                AND zv.visit_date BETWEEN :dateST AND :dateEN
                ORDER BY zvt.update_date ASC";

        $dataProvider = new \yii\data\SqlDataProvider([
            'sql' => $sql,
            'params' => [':doc_id' => $doc_id, ':dateST' => "$date 00:00:00", ':dateEN' => "$date 23:59:59"],
        ]);

        return $dataProvider;
    }

    public static function getReportCheckupDoctor($doc_id, $report_status, $page) {
        $nonimg = Yii::getAlias('@storageUrl/images') . '/nouser.png';
        $img = Yii::getAlias('@storageUrl/ezform/fileinput') . '/';

        $sql = "SELECT DISTINCT zv.id,vp.ptid, CONCAT(pt_firstname,' ',pt_lastname) as fullname,vp.pt_hn,zv.visit_type
            ,IFNULL(CONCAT('$img',vp.pt_pic),'$nonimg') AS pt_pic,pt_bdate
                FROM zdata_visit zv
                INNER JOIN zdata_patientprofile vp ON(vp.id=zv.target)
                INNER JOIN zdata_visit_tran zvt ON(zv.id=zvt.visit_tran_visit_id)
		INNER JOIN zdata_reportcheckup zrc ON(zrc.ckr_visit_id=zv.id)
                LEFT JOIN (SELECT DISTINCT order_visit_id,order_tran_status
                FROM zdata_order_tran zot
                INNER JOIN zdata_order_header zoh ON zoh.id= zot.order_header_id
                INNER JOIN zdata_visit zv ON(zv.id = zoh.order_visit_id)
                WHERE zot.rstat=1
                AND order_tran_code IN(/*'CH',*/'FE001','FE002','HM001','UR001','BC001','BC015','BC016','BC017','BC002','IM047',
                'BC003','BC005','BC006','BC009','CG001','BC011','BC012','BC013','BC014','IM006','IM001','IM002','PH001'
                ,'IM008','CG016','BC010')
                AND order_tran_status='2' AND visit_type= '1') AS chko ON(chko.order_visit_id=zv.id)
                WHERE zrc.rstat='1' AND visit_tran_doctor=:doc_id AND zrc.ckr_status = :ckr_status 
                AND visit_type='1' AND chko.order_visit_id IS NULL
                ORDER BY zv.visit_date ASC";
        
        $dataProvider = new \yii\data\SqlDataProvider([
            'sql' => $sql,
            'params' => [':doc_id' => $doc_id, ':ckr_status' => $report_status],
            'pagination' => [
                'page' => $page,
                'pageSize' => 9,
            ],
        ]);

        return $dataProvider;
    }

    public static function getReportPapDoctor($doc_id, $report_status, $page) {
        $nonimg = Yii::getAlias('@storageUrl/images') . '/nouser.png';
        $img = Yii::getAlias('@storageUrl/ezform/fileinput') . '/';

        $sql = "SELECT DISTINCT zv.id,vp.ptid,CONCAT(pt_firstname,' ',pt_lastname) as fullname,vp.pt_hn,zv.visit_type
            ,IFNULL(CONCAT('$img',vp.pt_pic),'$nonimg') AS pt_pic,pt_bdate
                FROM zdata_visit zv
                INNER JOIN zdata_patientprofile vp ON(vp.id=zv.target)
                INNER JOIN zdata_order_header zoh ON zoh.order_visit_id= zv.id
                INNER JOIN zdata_visit_tran zvt ON(zv.id=zvt.visit_tran_visit_id)
		INNER JOIN zdata_reportcheckup zrc ON(zrc.ckr_visit_id=zv.id)
                INNER JOIN zdata_order_tran zot ON(zot.order_header_id=zoh.id)
                LEFT JOIN (SELECT DISTINCT order_visit_id,order_tran_status
                FROM zdata_order_tran zot
                INNER JOIN zdata_order_header zoh ON zoh.id= zot.order_header_id
                INNER JOIN zdata_visit zv ON(zv.id = zoh.order_visit_id)
                WHERE zot.rstat=1
                AND order_tran_code IN('CG001','CG016')
                AND order_tran_status='2' AND visit_type= '4') AS chko ON(chko.order_visit_id=zv.id)
                WHERE zrc.rstat='1' AND visit_tran_doctor=:doc_id AND zrc.ckr_status = :ckr_status 
                AND zvt.visit_tran_doc_status='2' AND visit_type='4' AND chko.order_visit_id IS NULL
                AND zot.order_tran_code ='CG001' 
                ORDER BY zv.visit_date ASC";

        $dataProvider = new \yii\data\SqlDataProvider([
            'sql' => $sql,
            'params' => [':doc_id' => $doc_id, ':ckr_status' => $report_status],
            'pagination' => [
                'page' => $page,
                'pageSize' => 9,
            ],
        ]);

        return $dataProvider;
    }

    public static function getReportCheckup2Doctor($report_status, $page) {
        $nonimg = Yii::getAlias('@storageUrl/images') . '/nouser.png';
        $img = Yii::getAlias('@storageUrl/ezform/fileinput') . '/';

        /* $sql = "SELECT DISTINCT zv.id,vp.pt_id,vp.fullname,vp.pt_hn,zv.visit_type
          ,IFNULL(CONCAT('$img',vp.pt_pic),'$nonimg') AS pt_pic,pt_bdate,kk.visit_tran_doctor,zv.visit_date
          FROM zdata_visit zv
          INNER JOIN zdata_patientprofile vp ON(vp.pt_id=zv.visit_pt_id)
          INNER JOIN zdata_visit_tran zvt ON(zv.id=zvt.visit_tran_visit_id)
          INNER JOIN zdata_reportcheckup zrc ON(zrc.ckr_visit_id=zv.id)
          LEFT JOIN (SELECT DISTINCT zv.id AS visit_id,zv.ptid,visit_tran_doctor
          FROM zdata_visit zv
          INNER JOIN zdata_visit_tran zvt ON(zv.id=zvt.visit_tran_visit_id)
          INNER JOIN zdata_reportcheckup zrc ON(zrc.ckr_visit_id=zv.id)
          WHERE zrc.rstat= '1' AND visit_tran_doctor<>:doc_id AND visit_tran_doctor<>''
          AND zrc.ckr_status = :ckr_status AND zvt.visit_tran_doc_status= '2' AND visit_type= '1') AS kk ON(kk.visit_id=zv.id)
          WHERE zrc.rstat= '1' AND zvt.visit_tran_doctor=:doc_id AND zrc.ckr_status = :ckr_status
          AND zvt.visit_tran_doc_status= '2' AND visit_type= '1' AND kk.visit_tran_doctor IS NULL
          ORDER BY zv.visit_date ASC"; */

        $sql = "SELECT DISTINCT zv.id,vp.ptid,CONCAT(pt_firstname,' ',pt_lastname) as fullname,vp.pt_hn,zv.visit_type
                ,IFNULL(CONCAT('$img',vp.pt_pic),'$nonimg') AS pt_pic,pt_bdate,kk.visit_tran_doctor,zv.visit_date
                FROM zdata_visit zv
                INNER JOIN zdata_patientprofile vp ON(vp.id=zv.target)
                INNER JOIN zdata_visit_tran zvt ON(zv.id=zvt.visit_tran_visit_id)
		INNER JOIN zdata_reportcheckup zrc ON(zrc.ckr_visit_id=zv.id)     
		LEFT JOIN (SELECT DISTINCT zv.id AS visit_id,zv.ptid,visit_tran_doctor
                FROM zdata_visit zv
                INNER JOIN zdata_visit_tran zvt ON(zv.id=zvt.visit_tran_visit_id)
                INNER JOIN zdata_reportcheckup zrc ON(zrc.ckr_visit_id=zv.id)               
                WHERE zrc.rstat=  '1' AND visit_tran_doctor IN('1514136149083155900','1514136171016664500')
                AND zrc.ckr_status = '1' /*AND zvt.visit_tran_doc_status= '2'*/ AND visit_type= '1') AS kk ON(kk.visit_id=zv.id)
                INNER JOIN zdata_order_header zoh ON zoh.order_visit_id=zv.id
                INNER JOIN zdata_order_tran zot ON(zot.order_header_id=zoh.id AND zot.rstat='1' AND order_tran_code='CH') 
                WHERE zrc.rstat='1' AND zrc.ckr_status ='1' AND visit_type='1' AND kk.visit_tran_doctor IS NULL
                AND zot.order_tran_status='3'
                ORDER BY zv.visit_date ASC";

        $dataProvider = new \yii\data\SqlDataProvider([
            'sql' => $sql,
            'params' => [':ckr_status' => $report_status, ':doc_id' => '1514136075046873900'],
            'pagination' => [
                'page' => $page,
                'pageSize' => 50,
            ],
        ]);

        return $dataProvider;
    }

    public static function getReportPap2Doctor($page) {
        $nonimg = Yii::getAlias('@storageUrl/images') . '/nouser.png';
        $img = Yii::getAlias('@storageUrl/ezform/fileinput') . '/';

        $sql = "SELECT DISTINCT zv.id,vp.ptid,CONCAT(pt_firstname,' ',pt_lastname)as fullname,vp.pt_hn,zv.visit_type
                ,IFNULL(CONCAT('$img',vp.pt_pic),'$nonimg') AS pt_pic,pt_bdate
                ,zv.visit_date,zrc.id AS report_id,kk.visit_tran_doctor
                FROM zdata_visit zv
                INNER JOIN zdata_patientprofile vp ON(vp.id=zv.target)
                INNER JOIN zdata_order_header zoh ON zoh.order_visit_id=zv.id
                INNER JOIN zdata_order_tran zot ON(zot.order_header_id=zoh.id)
		LEFT JOIN zdata_reportcheckup zrc ON(zrc.ckr_visit_id=zv.id)  
		LEFT JOIN (SELECT DISTINCT zv.ptid,zv.id AS visit_id,visit_tran_doctor
		FROM zdata_visit zv
		INNER JOIN zdata_visit_tran zvt ON(zvt.visit_tran_visit_id=zv.id)
		WHERE zv.rstat=1 AND zv.visit_type=4 AND visit_tran_doctor<>'' AND visit_tran_dept='S074') AS kk ON(kk.visit_id=zv.id)
                WHERE zv.rstat=1 AND zot.rstat=1 AND zv.visit_type=4 AND zot.order_tran_code ='CG001' AND zot.order_tran_code <>'CH' 
                AND zot.order_tran_status<>'1' AND zrc.id IS NULL
                ORDER BY zv.visit_date ASC";

        $dataProvider = new \yii\data\SqlDataProvider([
            'sql' => $sql,
//            'params' => [':ckr_status' => $report_status, ':doc_id' => '1514136075046873900'],
            'pagination' => [
                'page' => $page,
                'pageSize' => 10,
            ],
        ]);

        return $dataProvider;
    }

    public static function getAppDoctor($doc_id, $date) {
        $nonimg = Yii::getAlias('@storageUrl/images') . '/nouser.png';
        $img = Yii::getAlias('@storageUrl/ezform/fileinput') . '/';

        $sql = "SELECT zap.id,zap.app_status,zap.app_time,zis.insp_name,CONCAT(title,pf.firstname,' ',pf.lastname) AS doctor_name
                ,vp.pt_id,vp.fullname,vp.pt_hn,IFNULL(CONCAT('$img',vp.pt_pic),'$nonimg') AS pt_pic,pt_bdate
                FROM zdata_appoint zap
                INNER JOIN zdata_patientprofile vp ON(vp.pt_id=zap.app_pt_id)
                INNER JOIN zdata_inspect zis ON(zis.id=zap.app_insp_id)
                INNER JOIN  `profile` pf ON(pf.user_id=zap.app_doctor)
                WHERE zap.app_doctor=:doc_id
                AND zap.app_date=:date
                ORDER BY zap.app_time ASC";

        $dataProvider = new \yii\data\SqlDataProvider([
            'sql' => $sql,
            'params' => [':doc_id' => $doc_id, ':date' => $date],
        ]);

        return $dataProvider;
    }

    public static function getReportCheckupSend($model, $params) {
        $model->load($params);
//        $date = \appxq\sdii\utils\SDdate::phpThDate2mysqlDate($model['create_date']);
        $paramsArry = [];
        if ($model['ckr_status'] == '3') {
            $paramsStr = " AND zrc.ckr_status IN('3','4')";
        } else {
            $paramsStr = " AND zrc.ckr_status=:report_status";
            $paramsArry[':report_status'] = $model['ckr_status'];
        }

//        if ($model['ckr_status'] !== '1') {
//            $paramsStr .= " AND DATE(zrc.report_date) = :date";
//            $paramsArry[':date'] = $date;
//        }

        if ($model['ckr_summary_detail']) {
            $paramsStr .= " AND CONCAT(pt_hn,' ',zp.prefix_name,pt_firstname,' ',pt_lastname) LIKE :ckr_summary_detail";
            $paramsArry[':ckr_summary_detail'] = "%{$model['ckr_summary_detail']}%";
        }

        $sql = "SELECT DISTINCT zrc.id AS report_id,zpf.pt_hn,CONCAT(zp.prefix_name_cid,pt_firstname,' ',pt_lastname) AS fullname,CONCAT(title,pf.firstname,' ',pf.lastname) AS doctor_name
                ,zv.id AS visit_id,DATE(zv.visit_date) AS visit_date,zpf.pt_email,ckr_status,zrc.update_date
                FROM zdata_visit zv
                INNER JOIN zdata_reportcheckup zrc ON(zrc.ckr_visit_id=zv.id)
                INNER JOIN zdata_patientprofile zpf ON(zpf.id=zv.visit_pt_id)
                LEFT JOIN zdata_prefix zp ON(zp.prefix_id = zpf.pt_prefix_id)
                LEFT JOIN `profile` pf ON(pf.user_id=zrc.ckr_doctorverify)
                
                INNER JOIN zdata_visit_tran zvt ON(zv.id=zvt.visit_tran_visit_id)
                WHERE zrc.rstat='1' $paramsStr /*AND zvt.visit_tran_doc_status='2'*/ ORDER BY zv.visit_date ASC";

        $dataProvider = new \yii\data\SqlDataProvider([
            'sql' => $sql,
            'params' => $paramsArry,
            'pagination' => [
                'pageSize' => 15,
            ],
        ]);

        return $dataProvider;
    }

}
