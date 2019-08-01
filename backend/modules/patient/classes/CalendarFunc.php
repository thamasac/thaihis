<?php

namespace backend\modules\patient\classes;

use Yii;
use yii\helpers\Url;
use appxq\sdii\utils\SDdate;

Class CalendarFunc {

    public static function getEventCalendarFront($catMessage, $date) {
        $y = substr($date, 0, 4);
        $m = substr($date, 5, 2);
        $dept = 'S074'; //fix ไปก่อน
        $events=[];
        $dataEvents = PatientQuery::getAppointConf($dept, $date);
        $stopEvents = PatientFunc::getCalendarStopEvent($y);
        if ($dataEvents) {
            foreach ($dataEvents as $key => $value) {
                $event = new \yii2fullcalendar\models\Event();
                $event->id = $value['id'];
                $event->title = $value['sum_qty'];
                $event->start = $value['app_conf_date'];
                $event->color = $value['sum_qty'] == '0' ? '#f0ad4e' : '#5cb85c';
                $event->url = '#';
                $events[] = $event;
            }
        }

        if ($stopEvents) {
            foreach ($stopEvents as $key => $value) {
                $event = new \yii2fullcalendar\models\Event();
                $event->id = "stop-event";
                $event->title = $value['tr_stop_desc'];
                $event->start = $value['tr_date'];
                $event->color = '#d9534f';
                $event->url = '#';
                $event->allDay = true;
                $events[] = $event;
            }
        }

        return $events;
    }

    public static function getVisitDate($date) {
        $visit_Date = PatientQuery::getVisitDate($date);
        if($visit_Date != null){
            return  $visit_Date['visit_date'];
        }else{
            return '';
        }
    }
    public static function dateTimetoDate($strDate) {
        if ($strDate != '' && $strDate != '1900-01-01') {
            $strDate = explode('-', $strDate);
            $strYear = $strDate[0];
            $strMonth = $strDate[1];
            $strDay = substr($strDate[2], 0, 2);
            $strTime = substr($strDate[2], 2);
            return "$strYear-$strMonth-$strDay";
        } else {
            return '';
        }
    }
    
    public static function getReportTypeDoctor($model, $params) {
        $model->load($params);
        $paramsStr = '';
        
            $date = explode(",", date('d-m-Y'));
        if($model['create_date']!=''){
            $date = explode(",", $model['create_date']);
        }
        
        if (count($date) > 1) {
            $dateStart = SDdate::phpThDate2mysqlDate($date[0], "-");
            $dateEnd = SDdate::phpThDate2mysqlDate($date[1], "-");

            $paramsStr .= " AND date(app_date) BETWEEN '{$dateStart}' AND '{$dateEnd}'";
        } else {
            $date = SDdate::phpThDate2mysqlDate($date[0], "-");
            $paramsStr .= " AND date(app_date) = '{$date}'";
        }
        
        //$position = \Yii::$app->user->identity->profile->position;
        $iddoctor = \Yii::$app->user->identity->profile->user_id;
        $app_dept = \Yii::$app->user->identity->profile->department;
        $userRoles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());
        
        if (isset($userRoles['appoint']) || isset($userRoles['administrator']) || isset($userRoles['adminsite'])) {
            
        }else if(isset($userRoles['doctor'])) {
            $paramsStr.= " AND zap.app_doctor = '{$iddoctor}'";
        } else {
            
            $paramsStr.= " AND zap.app_dept = '{$app_dept}'";
        }

        $sql = "SELECT pt_hn,
concat(zp.prefix_name,zpp.pt_firstname,' ',zpp.pt_lastname) AS patientfullname,zap.app_date,               
zin.ins_name AS InspectName,ds.sect_name,CONCAT(pf.title,' ',pf.firstname,' ',pf.lastname) AS DoctorFullname,zap.id
                FROM zdata_appoint zap
                LEFT JOIN `profile` pf ON(pf.user_id=zap.app_doctor)
                LEFT JOIN zdata_patientprofile zpp ON(zpp.ptid =zap.ptid)
                LEFT JOIN zdata_inspect zin ON(zin.id=zap.app_insp_id)
  LEFT JOIN zdata_prefix zp ON(zp.prefix_id = zpp.pt_prefix_id)
  LEFT JOIN dept_sect ds ON(ds.sect_code = zap.app_dept)
                WHERE zap.rstat='1' $paramsStr
                ORDER BY pf.firstname";

        $dataProvider = new \yii\data\SqlDataProvider([
            'sql' => $sql,
            'db' => 'db_nhis',
//            'params' => $paramsArry,
//            'sort' => ['attributes' => ['pt_hn', 'fullname', 'sect_name', 'doctor_name', 'right_name']],
            'pagination' => false,
        ]);
//          \appxq\sdii\utils\VarDumper::dump($dataProvider);
        return $dataProvider;
    }
    
    public static function getAppointitem($app_id){
          $sql = "SELECT zv.id,zv.visit_date,zot.order_tran_code,co.order_name,co.full_price
FROM zdata_visit zv
LEFT JOIN zdata_order_tran  zot ON(zv.ptid = zot.ptid AND zv.id = zot.order_tran_visit_id)
LEFT JOIN const_order co ON(co.order_code = zot.order_tran_code)
 WHERE zv.visit_app_id = :visit_app_id";
          return Yii::$app->db_nhis->createCommand($sql, [':visit_app_id' =>$app_id])->queryAll();
      
    }
    public static function getPatientByAppid($app_id){
        $sql ="SELECT pt_hn,
concat(zp.prefix_name,zpp.pt_firstname,' ',zpp.pt_lastname) AS patientfullname,zap.app_date,               
zin.ins_name AS InspectName,ds.sect_name,CONCAT(pf.title,' ',pf.firstname,' ',pf.lastname) AS DoctorFullname,zap.id
                FROM zdata_appoint zap
                LEFT JOIN `profile` pf ON(pf.user_id=zap.app_doctor)
                LEFT JOIN zdata_patientprofile zpp ON(zpp.ptid =zap.ptid)
                LEFT JOIN zdata_inspect zin ON(zin.id=zap.app_insp_id)
  LEFT JOIN zdata_prefix zp ON(zp.prefix_id = zpp.pt_prefix_id)
  LEFT JOIN dept_sect ds ON(ds.sect_code = zap.app_dept)
                WHERE zap.rstat='1' AND zap.id = :app_id
                ORDER BY pf.firstname";
        return Yii::$app->db_nhis->createCommand($sql, [':app_id' =>$app_id])->queryOne();
    }

}
