<?php

namespace backend\modules\usfinding\controllers;

use backend\modules\usfinding\models\UserApplication;
use backend\modules\usfinding\models\LogApiModel;
use DateTime;
use yii\db\Exception;
use yii\db\Query;
use yii\web\Controller;
use Yii;
use frontend\controllers\classes\ModulesReport;
use backend\modules\usfinding\classes\QueryMonitor;
use yii\data\ActiveDataProvider;
use backend\modules\usfinding\models\LogApiSearch;

class MonitoringController extends Controller {

    public function actionIndex() {

        $qryProvince = $this->getProvince();
        $qryUsTour = $this->getUsTour();
        $qryZone = $this->getZone();
        $qryUsSite = $this->getUsSite();
        $worklistno = isset($_SESSION['worklistno']) ? $_SESSION['worklistno'] : null;

        $session = \Yii::$app->session;
        $table_us = $session['table_us'];
        $refresh_time = $session['refresh_time'];
        $auto_reload = $session['auto_reload'];

        if ($table_us == '') {
            $table_us = "tb_data_3";
            $_SESSION['table_us'];
        }

        if ($refresh_time == '') {
            $refresh_time = '5';
            $_SESSION['refresh_time'] = $refresh_time;
        }

        $startDate = NULL;
        $endDate = NULL;
        $sitecode = \Yii::$app->user->identity->userProfile->sitecode;
        if ($worklistno == '' || $worklistno == NULL) {
            $workilistid = " SELECT * FROM worklist WHERE sitecode=:sitecode ORDER BY id DESC LIMIT 1 ";
            $resWork = \Yii::$app->db->createCommand($workilistid, [':sitecode' => $sitecode])->queryOne();

            $worklistno = $sitecode;
            $_SESSION['worklistno'] = $sitecode;
            $startDate = date('Y-m-d');
            $endDate = date('Y-m-d');
        } else if ($worklistno != $sitecode) {
            $workilistid = " SELECT * FROM worklist WHERE id=:id ";
            $resWork = \Yii::$app->db->createCommand($workilistid, [':id' => $worklistno])->queryOne();

            $_SESSION['worklistno'] = $resWork['id'];
            $worklistno = $resWork['id'];
            $startDate = $resWork['start_date'];
            $endDate = $resWork['end_date'];
        } else if ($worklistno == $sitecode) {
            $checkDate = " SELECT update_date FROM tb_data_3 WHERE hsitecode='$sitecode' ORDER BY update_date DESC LIMIT 1  ";
            $resCheck = \Yii::$app->db->createCommand($checkDate)->queryOne();

            $date = strtotime($resCheck['update_date']);
            $startDate = date('Y-m-d', $date);
            $endDate = date('Y-m-d');
        }

//        $checkMon = " SELECT COUNT(id) FROM worklist WHERE  id=:id AND NOW() BETWEEN start_date AND end_date ";
//        $resChk = \Yii::$app->db->createCommand($checkMon, [':id' => $worklistno])->queryScalar();

        $isMonitor = 'false';
        if ($auto_reload == 'true') {
            $isMonitor = 'true';
        }
        $dfUSFinding = DefaultUsfindingSiteValueController::GetDefaultUSFinding();

        return $this->renderAjax('index', [
                    'dfUSFinding' => $dfUSFinding,
                    'zone' => $qryZone,
                    'province' => $qryProvince,
                    'usTour' => $qryUsTour,
                    'usSite' => $qryUsSite,
                    'worklist' => $worklistno,
                    'startDate' => $startDate,
                    'endDate' => $endDate,
                    'isMonitor' => $auto_reload
        ]);
    }

    private function getUsTour() {
        $sqlUsTour = "SELECT * FROM `history_us_tour` ORDER BY `times` DESC";

        $qryUsTour = Yii::$app->dbcascap->createCommand($sqlUsTour)->queryAll();

        return $qryUsTour;
    }

    private function getUsSite() {
        $sqlUsTour = "SELECT ussite.No, ussite.dateatsite, ussite.hcode, ussite.hospitalname ";
        $sqlUsTour .= ",hospital.zone_code as zonecode ";
        $sqlUsTour .= ",hospital.provincecode as provcode ";
        $sqlUsTour .= ",concat(hospital.provincecode,hospital.amphurcode) as ampcode ";
        $sqlUsTour .= ",NOW() as edate ";
        $sqlUsTour .= "from history_us_site ussite ";
        $sqlUsTour .= "left join all_hospital_thai hospital ";
        $sqlUsTour .= "on hospital.hcode=ussite.hcode ";
        $sqlUsTour .= "order by ussite.dateatsite ";

        $qryUsTour = Yii::$app->db->createCommand($sqlUsTour)->queryAll();

        return $qryUsTour;
    }

    private function getZone() {
        $sqlZone = "SELECT zone_code, zone_name FROM `cascapcloud`.`all_hospital_thai` WHERE zone_code IS NOT NULL " .
                "GROUP BY zone_code ORDER BY zone_code";

        $qryZone = Yii::$app->db->createCommand($sqlZone)->queryAll();

        return $qryZone;
    }

    private function getProvince() {
        $sqlProvince = "SELECT province_code as PROVINCE_CODE, province_name as PROVINCE_NAME " .
                "FROM `zdata_province` " .
                "WHERE province_code IS NOT NULL " .
                "GROUP BY province_code " .
                "ORDER BY province_code";

        $qryProvince = Yii::$app->db->createCommand($sqlProvince)->queryAll();

        return $qryProvince;
    }

    private function getAmphur($provinceId) {
        $sqlAmphur = "SELECT city_code as CITY_CODE, city_name as CITY_NAME " .
                "FROM zdata_city " .
                "WHERE province = '$provinceId' " .
                "AND city_name NOT LIKE '' " .
                "GROUP BY city_code " .
                "ORDER BY city_name";

        $qryAmphur = Yii::$app->db->createCommand($sqlAmphur)->queryAll();

        return $qryAmphur;
    }

    private function getHospital($cityId) {
        $sqlAllHospitalThai = "SELECT site_code,site_name,city, province " .
                "FROM `zdata_sitecode` " .
                "WHERE city = '$cityId' " .
                "GROUP BY hcode ORDER BY name";

        $qryAllHospitalThai = Yii::$app->db->createCommand($sqlAllHospitalThai)->queryAll();

        return $qryAllHospitalThai;
    }

    public function actionOpenForm() {
        $ezf_id = \Yii::$app->request->get('ezf_id');
        $dataid = \Yii::$app->request->get('dataid');
        $ptid = \Yii::$app->request->get('ptid');
        $url = "/inputdata/redirect-page?dataid=$dataid&ezf_id=$ezf_id";
        return $this->redirect([$url]);
    }

    public function actionAddUsroom() {
        $sitecode = \Yii::$app->user->identity->userProfile->sitecode;
        $user_id = \Yii::$app->user->identity->userProfile->user_id;
        $worklist_id = \Yii::$app->request->post('worklist_id');
        $room = " SELECT MAX(room_name) as room_name FROM us_room WHERE room_type='us-room' AND  worklist_id=:worklist_id ";
        $resRoom = \Yii::$app->db->createCommand($room, [':worklist_id' => $worklist_id])->queryOne();
        $new_room = $resRoom['room_name'] + 1;

        $insert = " INSERT INTO us_room(room_name, room_type, worklist_id, user_create, create_date)
            VALUES('$new_room', 'us-room', '$worklist_id', '$user_id',NOW())";
        $resInsert = \Yii::$app->db->createCommand($insert)->execute();

        return true;
    }

    public function actionAddExitnurse() {
        $sitecode = \Yii::$app->user->identity->userProfile->sitecode;
        $user_id = \Yii::$app->user->identity->userProfile->user_id;
        $worklist_id = \Yii::$app->request->post('worklist_id');
        $room = " SELECT MAX(room_name) as room_name FROM us_room WHERE room_type='exit-nurse' AND worklist_id=:worklist_id ";
        $resRoom = \Yii::$app->db->createCommand($room, [':worklist_id' => $worklist_id])->queryOne();
        $new_room = $resRoom['room_name'] + 1;

        $insert = " INSERT INTO us_room(room_name, room_type, worklist_id, user_create, create_date)
            VALUES('$new_room', 'exit-nurse', '$worklist_id', '$user_id',NOW())";
        $resInsert = \Yii::$app->db->createCommand($insert)->execute();

        return true;
    }

    public function actionAddRefer() {
        $sitecode = \Yii::$app->user->identity->userProfile->sitecode;
        $user_id = \Yii::$app->user->identity->userProfile->user_id;
        $worklist_id = \Yii::$app->request->post('worklist_id');
        $room = " SELECT MAX(room_name) as room_name FROM us_room WHERE worklist_id=:worklist_id AND  room_type='refer' ";
        $resRoom = \Yii::$app->db->createCommand($room, [':worklist_id' => $worklist_id])->queryOne();
        $new_room = $resRoom['room_name'] + 1;

        $insert = " INSERT INTO us_room(room_name, room_type, worklist_id, user_create, create_date)
            VALUES('$new_room', 'refer', '$worklist_id', '$user_id',NOW())";
        $resInsert = \Yii::$app->db->createCommand($insert)->execute();

        return true;
    }

    public function actionAddOther() {
        $sitecode = \Yii::$app->user->identity->userProfile->sitecode;
        $user_id = \Yii::$app->user->identity->userProfile->user_id;
        $worklist_id = \Yii::$app->request->post('worklist_id');
        $room = " SELECT MAX(room_name) as room_name FROM us_room WHERE worklist_id=:worklist_id AND  room_type='other' ";
        $resRoom = \Yii::$app->db->createCommand($room, [':worklist_id' => $worklist_id])->queryOne();
        $new_room = $resRoom['room_name'] + 1;

        $insert = " INSERT INTO us_room(room_name, room_type, worklist_id, user_create, create_date)
            VALUES('$new_room', 'other', '$worklist_id', '$user_id',NOW())";
        $resInsert = \Yii::$app->db->createCommand($insert)->execute();

        return true;
    }

    public function actionUltrasoundData() {
        $startDate = \Yii::$app->request->post('startDate');
        $endDate = \Yii::$app->request->post('endDate');
        $zone = \Yii::$app->request->post('zone');
        $province = \Yii::$app->request->post('province');
        $amphur = \Yii::$app->request->post('amphur');
        $hospital = \Yii::$app->request->post('hospital');
        $xsourcex = \Yii::$app->user->identity->userProfile->sitecode;

        $worklistno = $_SESSION['worklistno'];

        $table_us = $_SESSION['table_us'];
        $refresh_time = $_SESSION['refresh_time'];
        $filter_count = $_SESSION['filter_count'];
        if ($table_us == '') {
            $table_us = "tb_data_3";
            $_SESSION['table_us'] = $table_us;
        }

        if ($refresh_time == '') {
            $refresh_time = '5';
            $_SESSION['refresh_time'] = $refresh_time;
        }

        if ($hospital != null) {
            $sitename = ModulesReport::getSiteName($hospital);
            $worklistno = null;
        } else {
            $sitename = ModulesReport::getSiteName($xsourcex);
        }

        $drilldownState = true;
        $manageRoom = false;

        if ($worklistno == null || $worklistno==$xsourcex) {
            $drilldownState = false;
            $resWork['id'] = $hospital;
            $resWork['title'] = ': เลือกทั้งหมด';
            $resWork['sitecode'] = ModulesReport::getSiteName($hospital);
            $worklistno=$xsourcex;
        } else if ($worklistno != $xsourcex) {
            $manageRoom = true;
            $worklistQry = " SELECT * FROM worklist WHERE id=:id ";
            $resWork = \Yii::$app->db->createCommand($worklistQry, [':id' => $worklistno])->queryOne();
            if ($resWork) {
                $_SESSION['worklistno'] = $resWork['id'];
                $worklistno = $resWork['id'];
                $startDate = date('d/m/Y', strtotime($resWork['start_date']));
                $endDate = date('d/m/Y', strtotime($resWork['end_date']));
            }
        }


        if ($zone != null || $province != null || $amphur != null || $hospital != null) {
            $drilldownState = false;
            if ($zone != null) {
                $sitename = "เขตบริการสุขภาพที่ " . $zone;
                $worklistno = $zone;
            }
            if ($province != null) {
                $sitename = "จังหวัด";
                $sitename .= ModulesReport::getProvinceName($province);
                $worklistno = $province;
            }
            if ($amphur != null) {
                $sitename = "อำเภอ";
                $sitename .= ModulesReport::getAmphurName($province . $amphur);
                $worklistno = $province . $amphur;
            }
        }

        if ($hospital == $xsourcex || Yii::$app->user->can('administrator')) {
            $drilldownState = true;
        }


        $sqlEz = " SELECT ezf_id FROM ezform WHERE ezf_table=:ezf_table";
        $resEz = \Yii::$app->db->createCommand($sqlEz, [':ezf_table' => $table_us])->queryOne();

        $doctorList = QueryMonitor::UltrasoundRoomData($startDate, $endDate, $zone, $province, $amphur, $hospital, $worklistno, $filter_count);
        $nurseList = QueryMonitor::ExitNurseData($worklistno);
        $referList = QueryMonitor::ReferData($worklistno);
        $otherList = QueryMonitor::OtherData($worklistno);

        $session = \Yii::$app->session;
        $auto_reload = $session['auto_reload'];

        $isMonitor = 'false';
        if ($auto_reload == 'true' || $auto_reload == true) {
            $isMonitor = 'true';
        }
        try{
            $duplicatedRecord = QueryMonitor::GetDuplicated($hospital, $startDate, $endDate);
        }catch (\Exception $e){
            $duplicatedRecord = $e->getMessage();
        }
        return $this->renderAjax('monitoring', [
                    'duplicatedRecord' => $duplicatedRecord,
                    'doctorList' => $doctorList,
                    'nurseList' => $nurseList,
                    'referList' => $referList,
                    'otherList' => $otherList,
                    'drilldownState' => $drilldownState,
                    'sitename' => $sitename,
                    'worklistno' => $worklistno,
                    'startDate' => $startDate,
                    'endDate' => $endDate,
                    'ezf_id' => $resEz['ezf_id'],
                    'isMonitor' => $isMonitor,
                    'hospital' => $hospital,
                    'resWorkDefault' => $resWork,
                    'manageRoom'=>$manageRoom,
        ]);
    }

    public function actionDoctorUltrasound() {
        $xsourcex = \Yii::$app->user->identity->userProfile->sitecode;
        $sqlDoctor = " SELECT doctorcode, doctorfullname FROM doctor_all WHERE doctorcode=:doctorcode LIMIT 0,50";
        $doctor_code = \Yii::$app->request->post('doctor_code');
        $room = \Yii::$app->request->post('room_name');
        $room_type = \Yii::$app->request->post('room_type');
        $resDoctor = \Yii::$app->db->createCommand($sqlDoctor, [':doctorcode' => $doctor_code])->queryOne();
        return $this->renderAjax('doctor-ultrasound', [
                    'doctor' => $resDoctor,
                    'room_name' => $room,
                    'room_type' => $room_type
        ]);
    }

    public function actionPatientInspected() {
        $user_id = \Yii::$app->request->post('user_id');
        $worklist_id = \Yii::$app->request->post('worklist_id');
        $room_name = \Yii::$app->request->post('room_name');
        $inspect_type = \Yii::$app->request->post('inspect_type');
        $startDate = \Yii::$app->request->post('startDate');
        $endDate = \Yii::$app->request->post('endDate');
        $doctorcode = \Yii::$app->request->post('doctorcode');
        $amt_number = \Yii::$app->request->post('amt_number');
        $hospital = \Yii::$app->request->post('hospital');
        $filter_number = $_SESSION['filter_count'];
        if($inspect_type == 'inspected-all' || $inspect_type == 'inspecting-all' || $inspect_type == 'startinspect-all' || $inspect_type == 'patient-total'){
            //\appxq\sdii\utils\VarDumper::dump($inspect_type);
            $filter_number = '0';
        }elseif ($filter_number == null){
            $filter_number = '1';
        }

        $patientList = QueryMonitor::PatientUltrasound(
                        $startDate
                        , $endDate
                        , $user_id
                        , $inspect_type
                        , $room_name
                        , $worklist_id
                        , $filter_number
                        , $doctorcode
                        , $amt_number
                        ,$hospital
        );

        return $this->renderAjax('patient-inspected', [
                    'patientList' => $patientList
        ]);
    }

    public function actionDoctorList($q = null) {

        $sqlDoctor = " SELECT doctorcode, doctorfullname FROM doctor_all WHERE CONCAT(IFNULL(doctorcode,''), '', IFNULL(doctorfullname,'')) LIKE '%" . $q . "%' LIMIT 50";
        $resDoctor = \Yii::$app->db->createCommand($sqlDoctor)->queryAll();
        //\appxq\sdii\utils\VarDumper::dump($resSite);
        $out = [];
        $i = 0;
        foreach ($resDoctor as $value) {
            $out["results"][$i] = ['id' => $value['doctorcode'], 'text' => $value["doctorfullname"]];
            $i++;
        }

        return json_encode($out);
    }

    public function actionDoctorChange() {
        $doctor_code = \Yii::$app->request->post('doctor_code');
        $room_name = \Yii::$app->request->post('room_name');
        $worklist_id = \Yii::$app->request->post('worklist_id');

        $update = " UPDATE us_room SET doctor_code=:doctor_code WHERE room_name=:room_name AND worklist_id=:worklist_id ";
        $result = \Yii::$app->db->createCommand($update, [':doctor_code' => $doctor_code
                    , ':room_name' => $room_name, ':worklist_id' => $worklist_id])->execute();
        if ($result) {
            $txt = "เปลี่ยนแพทย์ประจำห้องตรวจเรียบร้อยแล้ว";
        } else {
            $txt = "มีข้อผิดพลาด! ไม่สามารถเปลี่ยนแพทย์ประจำห้องตรวจได้";
        }
        echo $txt;
    }

    public function actionUserUltrasound() {

        $sqlUser = " SELECT user_id, firstname, lastname, avatar_path, avatar_base_url FROM user_profile WHERE user_id=:user_id LIMIT 0,50";
        $user_id = \Yii::$app->request->post('user_id');
        $room = \Yii::$app->request->post('room_name');
        $room_type = \Yii::$app->request->post('room_type');
        $resUser = \Yii::$app->db->createCommand($sqlUser, [':user_id' => $user_id])->queryOne();
        return $this->renderAjax('user-ultrasound', [
                    'user_us' => $resUser,
                    'room_name' => $room,
                    'room_type' => $room_type
        ]);
    }

    public function actionUserList($q = null) {

        $xsourcex = \Yii::$app->user->identity->userProfile->sitecode;
        $params = [':sitecode' => $xsourcex];
        $sqlUser = " SELECT user_id, firstname, lastname FROM user_profile WHERE CONCAT(`user_id`, ' ', `firstname`,' ',`lastname`) LIKE '%" . $q . "%' AND sitecode=:sitecode  LIMIT 0,50";
        $resUser = \Yii::$app->db->createCommand($sqlUser, $params)->queryAll();
        //\appxq\sdii\utils\VarDumper::dump($xsourcex);
        $out = [];
        $i = 0;
        foreach ($resUser as $value) {
            $out["results"][$i] = ['id' => $value['user_id'], 'text' => $value["firstname"] . ' ' . $value["lastname"]];
            $i++;
        }

        return json_encode($out);
    }

    public function actionUserChange() {
        $user_id = \Yii::$app->request->post('user_id');
        $room_name = \Yii::$app->request->post('room_name');
        $worklist_id = \Yii::$app->request->post('worklist_id');
        $room_type = \Yii::$app->request->post('room_type');

        $update = " UPDATE us_room SET user_id=:user_id WHERE room_name=:room_name AND worklist_id=:worklist_id AND room_type=:room_type ";
        $result = \Yii::$app->db->createCommand($update, [':user_id' => $user_id
                    , ':room_name' => $room_name, ':worklist_id' => $worklist_id, ':room_type' => $room_type])->execute();
        if ($result) {
            $txt = "เปลี่ยนเจ้าหน้าที่ประจำห้องตรวจเรียบร้อยแล้ว";
        } else {
            $txt = "มีข้อผิดพลาด! ไม่สามารถเปลี่ยนเจ้าหน้าที่ประจำห้องตรวจได้";
        }
        echo $txt;
    }

    public function actionRemoveUsroom() {
        $room_name = \Yii::$app->request->post('room_name');
        $worklist_id = \Yii::$app->request->post('worklist_id');
        $room_type = \Yii::$app->request->post('room_type');
        $delete = " DELETE FROM us_room WHERE room_name=:room_name AND worklist_id=:worklist_id AND room_type=:room_type ";
        $result = \Yii::$app->db->createCommand($delete, ['room_name' => $room_name, 'worklist_id' => $worklist_id, 'room_type' => $room_type])->execute();

        return true;
    }

    public function actionSetting() {
        $worklistno = $_SESSION['worklistno'];
        if (!$worklistno == '' || !$worklistno == NULL) {
            $where = " AND id = '$worklistno' ";
        }
        $sitecode = \Yii::$app->user->identity->userProfile->sitecode;
        $sqlWork = " SELECT id,title,sitecode FROM worklist WHERE CONCAT(id, ' ',`title`, `sitecode`) LIKE '%" . $q . "%' $where AND sitecode=:sitecode ORDER BY id DESC LIMIT 1";
        $resWork = \Yii::$app->db->createCommand($sqlWork, [':sitecode' => $sitecode])->queryOne();
        //\appxq\sdii\utils\VarDumper::dump($worklistno);
        if (!$resWork) {
            $resWork['id'] = $sitecode;
            $resWork['title'] = ': เลือกทั้งหมด';
            $resWork['sitecode'] = '';
        }
        return $this->renderAjax('monitor-setting', ['resWorkDefault' => $resWork]);
    }

    public function actionSetSetting() {
        $request = \Yii::$app->request;
        $table_us = $request->post('table_us');
        $refresh_time = $request->post('refresh_time');
        $worklistno = $request->post('worklistno');
        $auto_reload = $request->post('auto_reload');
        $filter_count = $request->post('filter_count');

        if (isset($worklistno) || $worklistno != NULL)
            $_SESSION['worklistno'] = $worklistno;
        if (isset($table_us) || $table_us != NULL)
            $_SESSION['table_us'] = $table_us;
        if (isset($refresh_time) || $refresh_time != NULL)
            $_SESSION['refresh_time'] = $refresh_time;
        if (isset($auto_reload) || $auto_reload != NULL)
            $_SESSION['auto_reload'] = $auto_reload;
        if (isset($filter_count) || $filter_count != NULL)
            $_SESSION['filter_count'] = $filter_count;
    }

    public function actionPatientView() {
        $provider = QueryMonitor::getPatients();
        return $this->renderAjax('patient-view.php', ['provider' => $provider]);
    }

    public function actionReferList() {
        $provider = QueryMonitor::getListSuspected();
        //\appxq\sdii\utils\VarDumper::dump($provider);
        return $this->renderAjax('refer-list.php', ['provider' => $provider]);
    }

    public function actionUsersApplicationList(){
        // Find on command and param
        // User Profile
        // User Application
        // Set Title of page Online USER , Active USER
        $command = Yii::$app->request->get('command' , null);
        $application = Yii::$app->request->get('application' , null);
        $param1 = Yii::$app->request->get('param1' , null);
        $param2 = Yii::$app->request->get('param2' , null);
        $heading = "";


        switch ($command){
            case "online":
                $heading = "Online User";
                $dataQuery = (new Query());
                $dataQuery->select( ['log_api.user_id' , 'user_application.device_id' , 'user_application.platform'
                    ,'user_profile.firstname','user_profile.lastname','user_profile.telephone','user_profile.sitecode','user_profile.email'])
                    ->from("log_api")
                    ->innerJoin("user_profile","user_profile.user_id = log_api.user_id")
                    ->innerJoin("user_application","user_application.device_id = log_api.device_id")
                    ->where(["log_api.application" => $application])
                    ->andWhere("log_api.create_date > (NOW() -  INTERVAL 5 MINUTE)")->groupBy("log_api.user_id");
                break;
             case "userActive":
                 $heading = "DeviceActive ".$param1;
                 $limitDate = date('Y-m-d',strtotime($param1 . "+1 days"));
                 $dataQuery = (new Query());
                 $dataQuery->select( ['log_api.user_id'  , 'user_application.device_id' , 'user_application.platform'
                     ,'user_profile.firstname','user_profile.lastname','user_profile.telephone','user_profile.sitecode','user_profile.email'])
                     ->from("log_api")
                     ->innerJoin("user_profile","user_profile.user_id = log_api.user_id")
                     ->innerJoin("user_application","user_application.device_id = log_api.device_id")
                     ->where(["=","log_api.application" , $application])
                     ->andWhere( ['between', 'log_api.create_date', "$param1","$limitDate"])->groupBy("log_api.user_id");
                 break;
            case "platform":
                $heading = "Platform ".$param1;

                $dataQuery = new Query();
                $dataQuery->select( ['user_application.*' ,'user_profile.firstname','user_profile.lastname','user_profile.telephone'
                    ,'user_profile.sitecode','user_profile.email'])
                    ->from("user_application")->innerJoin("user_profile","user_profile.user_id = user_application.user_id")
                    ->where(["user_application.application" => $application , "user_application.platform" => $param1]);
                break;
            case "all":
                $heading = "All ";
                $dataQuery = new Query();
                $dataQuery->select( ['user_application.*' ,'user_profile.firstname','user_profile.lastname','user_profile.telephone'
                    ,'user_profile.sitecode','user_profile.email'])
                    ->from("user_application")->innerJoin("user_profile","user_profile.user_id = user_application.user_id")
                    ->where(["user_application.application" => $application])->groupBy("user_id");
                break;

        }

        $dataProvider = new ActiveDataProvider([
            'query' => $dataQuery,
            'pagination' => [
                'pageSize' => 20,
            ]
        ]);
        return $this->render('users-application',[ 'dataProvider' => $dataProvider,'dataQuery' => $dataQuery,'heading'=>$heading]);

    }

    public function actionLogStash(){
        $subTime = strtotime('-5 minutes');
        $currentDate = date('Y-m-d H:i:s', $subTime);
        $start_date = Yii::$app->request->get('start_date' , null);
        $end_date = Yii::$app->request->get('end_date' , null);
        $userId = Yii::$app->request->get('user_id' , null);
        $tab = Yii::$app->request->get('tab' , 1);
        $page = Yii::$app->request->get('page' , null);
        if($page != null) $tab = 2 ;
        $type['log']= Yii::$app->request->get('log' , '0');
        $type['error'] = Yii::$app->request->get('error' , '1');
        $type['info'] = Yii::$app->request->get('info' , '0');
        $type['track'] = Yii::$app->request->get('track' , '1');
        $type['api'] = Yii::$app->request->get('api' , '0');
        $dataQuery = \backend\modules\usfinding\models\LogApiModel::find();
        


        if($userId) $dataQuery->where(["user_id" => $userId]);
        if($start_date == null || $end_date == null){
            $subTime = strtotime('-7 days');
            $start_date = date('Y-m-d H:i', $subTime);
            $end_date = date('Y-m-d H:i');
        }
        $dataQuery->andWhere(['between', 'create_date', $start_date, $end_date]);
        $graphSql = (new Query())->select(["count(distinct if(type='log',id,null)) as `log`
        ,count(distinct if(type='error',id,null)) as `error`
        ,count(distinct if(type='info',id,null)) as `info`
        ,count(distinct if(type='track',id,null)) as `track`
        ,count(distinct if(type='api',id,null)) as `api`"])
            ->from("log_api")
            ->andWhere(['between', 'create_date', $start_date, $end_date]);
        $datagraph = $graphSql->one();
        $num = 0;
        foreach ($datagraph as $key => $value) {
            $dataName[$num][] = $key;
            $dataChart[$num]['name'] = $key;
            $dataChart[$num]['y'] = intval($value);
            $num++;
        }

        $conditions = ['or'];
        if($type['log']=='1')
                array_push($conditions,"type='log'");
        if($type['error']=='1')
                array_push($conditions,"type='error'");
        if($type['info']=='1')
                array_push($conditions,"type='info'");
        if($type['track']=='1')
            array_push($conditions,"type='track'");
        if($type['api']=='1')
            array_push($conditions,"type='api'");
        if(count($conditions) > 1 )
            $dataQuery->andWhere($conditions);

        $totalOnline = 0;
        $totalOnlineQuery = new Query();
        $totalOnlineQuery->select( "COUNT(DISTINCT user_id) as totalOnline" )
            ->from("log_api")
            ->where(["application" => "usmobile"])
            ->andWhere(['>', 'create_date', $currentDate]);
        $totalOnline = $totalOnlineQuery->one()["totalOnline"];

        $totalUsers = 0;
        $totalUsersQuery = new Query();
        $totalUsersQuery->select( "COUNT(DISTINCT user_id) as totalUsers" )
            ->from("user_application")
            ->where(["application" => "usmobile"]);
        $totalUsers = $totalUsersQuery->one()["totalUsers"];

        $dataUser = [];
        if(isset($userId) && $userId != null){
            $lastSyncQuery = new Query();

            $lastSyncQuery->select( "*" )
                ->from("log_api")
                ->where(["name" => "sync-all-by-ref-site" , "user_id"=>$userId])
                ->orderBy("create_date DESC")->limit(1);
            $lastSync = $lastSyncQuery->one();
            if($lastSync)
                $lastSync = 'ซิงค์ข้อมูลล่าสุดเมื่อ'.(new DateTime($lastSync["create_date"]))->format('Y-m-d H:i:s');
            else
                $lastSync = 'ยังไม่มีการ Sync ข้อมูล';
            $dataUser = \Yii::$app->db->createCommand("SELECT user_id as `id` ,concat(firstname, ' ' ,lastname) as `name` FROM user_profile");
            try {
                $dataUser = $dataUser->queryAll();
            } catch (Exception $e) {
                $dataUser = [];
            }
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $dataQuery,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'defaultOrder' => [
                    'create_date' => SORT_DESC,
                ]
            ],
        ]);

        $userPerDay = LogApiModel::getTotalActive("usmobile");
        $platformUsage = UserApplication::getPlatform("usmobile");
        $versionUsage = UserApplication::getVersion("usmobile");
        $totalRequest = LogApiModel::getTotalRequestEachDay("usmobile");
        return $this->render('logstash',[
            'userId'=>$userId,
            'totalOnline'=>$totalOnline,
            'totalUsers'=>$totalUsers,
            'lastSync'=>$lastSync,
            'startDate'=>$start_date,
            'userPerDay'=>$userPerDay,
            'platformUsage'=>$platformUsage,
            'versionUsage'=>$versionUsage,
            'totalRequest'=>$totalRequest,
            'endDate'=>$end_date,
            'dataProvider'=>$dataProvider,
            'dataUser'=>$dataUser,
            'dataChart'=> $dataChart,
            'dataName' => $dataName,
            'tab' => $tab,
            'type'=>$type,
        ]);
    }

}
