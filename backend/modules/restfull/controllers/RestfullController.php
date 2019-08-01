<?php

namespace backend\modules\restfull\controllers;

use yii\filters\AccessControl;
use backend\behaviours\Verbcheck;
use backend\behaviours\Apiauth;
use common\modules\user\models\RegistrationForm;
use backend\modules\patient\classes\PatientFunc;
use backend\modules\patient\classes\PatientQuery;
use frontend\classes\FrontendQuery;
use Yii;
use appxq\sdii\utils\SDdate;
use backend\modules\patient\classes\CalendarFunc;
use frontend\classes\FrontendFunc;
use backend\modules\ezforms2\classes\EzfUiFunc;
use backend\modules\restfull\classes\EzfUiFuncrestful;
use yii\web\Response;
use appxq\sdii\helpers\SDHtml;
use common\models\AccessTokens;
use common\models\LoginForm;
use common\models\AuthorizationCodes;

class RestfullController extends RestController {

    public function behaviors() {

        $behaviors = parent::behaviors();

        return $behaviors + [
            'apiauth' => [
                'class' => Apiauth::className(),
                'exclude' => [ 'authorize', 'register', 'accesstoken', 'checkmregis', 'getlistcheckup', 'checkdate', 'login', 'getprofile', 'visits', 'reportnavi', 'getdateappoint'],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout', 'me'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => [ 'authorize', 'register', 'accesstoken', 'checkmregis', 'getlistcheckup', 'checkdate', 'login', 'getprofile', 'visits', 'reportnavi', 'getdateappoint'],
                        'allow' => true,
                        'roles' => ['*'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => Verbcheck::className(),
                'actions' => [
                    'logout' => ['GET'],
                    'authorize' => ['POST'],
                    'register' => ['POST'],
                    'accesstoken' => ['POST'],
                    'me' => ['GET'],
                ],
            ],
        ];
    }

    public function actionGetdateappoint() {
        $request = Yii::$app->request;
        if ($request->isPost) {
            $user_id = $request->post('user_id');
            $ezf_tbname = 'zdata_patientprofile';
            $model_profile = \backend\modules\patient\classes\PatientFunc::loadTbDataByField($ezf_tbname, ['pt_user_id' => $user_id]);
            $visit = FrontendFunc::getVisitDateFrontend($model_profile['id']);
            $visit_date = !empty($visit['visit_date']) ? 'คุณมีนัดตรวจสุขภาพวันที่ ' . \appxq\sdii\utils\SDdate::mysql2phpThDateSmallYear($visit['visit_date']) : ''; //;
            $data['fullname'] = $model_profile['pt_firstname'] . ' ' . $model_profile['pt_lastname'];
            $data['visit_date'] = $visit_date;
            $arrdata[] = $data;
            Yii::$app->api->sendSuccessResponse($arrdata);
        }
    }

    public function actionIndex() {
        Yii::$app->api->sendSuccessResponse(['Yii2 RESTful API with OAuth2']);
    }

    public function actionGetprofile() {
        $request = Yii::$app->request;
        if ($request->isPost) {
            $ezf_tbname = 'zdata_patientprofile';
            $user_id = $request->post('user_id');
            $model_profile = \backend\modules\patient\classes\PatientFunc::loadTbDataByField($ezf_tbname, ['pt_user_id' => $user_id]);
            $dataRight = PatientFunc::getRightOnlineByNhso($model_profile['pt_cid']);
            $project_id = \backend\modules\cpoe\classes\CpoeQuery::getProjectCheckVisit($model_profile['pt_cid'])['target_project'];
            $appchk_right = '2';
            if (!empty($project_id)) {
                $appchk_right = '1';
            } elseif ($dataRight['maininscl'] == 'LGO' || $dataRight['maininscl'] == 'OFC') {
                if ($dataRight['maininscl'] == 'LGO' && $dataRight['subinscl'] == 'L1') {
                    $appchk_right = '3';
                } elseif (in_array($dataRight['subinscl'], ['E1', 'E2'])) {
                    $appchk_right = '4';
                } elseif ($dataRight['maininscl'] == 'LGO') {
                    $appchk_right = '2';
                } elseif ($dataRight['maininscl'] == 'OFC' && $dataRight['subinscl'] == 'O4') {
                    $appchk_right = '2';
                } elseif ($dataRight['maininscl'] == 'OFC' && $dataRight['subinscl'] == 'O3') {
                    $appchk_right = '5';
                } elseif ($dataRight['maininscl'] == 'OFC') {
                    $appchk_right = '5';
                }
            } elseif ($dataRight['maininscl'] == 'PVT') {
                $appchk_right = '5';
            }
            $data['fullname'] = $model_profile['pt_firstname'] . ' ' . $model_profile['pt_lastname'];
            $data['pt_id'] = $model_profile['ptid'];
            $data['pt_age'] = SDdate::getAge(SDdate::dateTh2bod($model_profile['pt_bdate'])); //SDdate::mysql2phpThDate(SDdate::dateTh2bod($model_profile['pt_bdate']));
            $data['pt_sex'] = $model_profile['pt_sex'];
            $data['right'] = $appchk_right;
            $data['full_address'] = PatientFunc::getFulladdress($model_profile);
            $data['bdate'] = $model_profile['pt_bdate'];
            $arrdata[] = $data;
            Yii::$app->api->sendSuccessResponse($arrdata);
        }
    }

    public function actionVisits() {
        $ezfVisit_id = '1503589101005614900'; //Yii::$app->controller->module->patientFormID['visit'];
        $ezfVisit_tbname = 'zdata_visit'; //Yii::$app->controller->module->patientFormTableName['visit'];
        $ezfPtRight_id = '1505016314072939900'; // Yii::$app->controller->module->patientFormID['patientright'];
        $ezf_checkup_id = '1511490170071641200';
        $ezfcheckup_tbname = 'zdata_app_checkup';
        $request = Yii::$app->request;
        $dataid = $request->post('dataid');
        $getdate = $request->post('datadate');
        $data['visit_date'] = $getdate . ' ' . date('H:i:s');
        $data['visit_status'] = '1';
        $data['visit_type'] = '1';
        $data['rstat'] = '1';
        $visit = FrontendFunc::getVisitDateFrontend($dataid);
        $visitdatelod = CalendarFunc::dateTimetoDate(!empty($visit['visit_date']) ? $visit['visit_date'] : '');
        $modelVisit = '';
        if (isset($dataid)) {
            if (!empty($visitdatelod)) {
                $modelVisit['data']['id'] = $visit['id'];
                $data['visit_date'] = $data['visit_date'];
                $visitdatelod = $data['visit_date'];
            } else {
                $modelVisit = PatientFunc::loadTbDataByField($ezfVisit_tbname, ['ptid' => $dataid,
                            'DATE(visit_date)' => $getdate]);
            }
            if (empty($modelVisit)) {
                $modelVisit = PatientFunc::BackgroundInsert($ezfVisit_id, '', $dataid, $data);
                $dates = CalendarFunc::dateTimetoDate($data['visit_date']);
                \frontend\classes\FrontendQuery::update_zcon_f($dates);
            }
        }
        $data_checkup['ptid'] = $dataid;
        $data_checkup['target'] = $modelVisit['data']['id'];
        $data_checkup['appchk_item_cbc'] = $request->post('appchk_item_cbc');
        $data_checkup['appchk_item_ux'] = $request->post('appchk_item_ux');
        $data_checkup['appchk_item_seob'] = $request->post('appchk_item_seob');
        $data_checkup['appchk_item_chest'] = $request->post('appchk_item_chest');
        $data_checkup['appchk_item_aaa'] = $request->post('appchk_item_aaa');
        $data_checkup['appchk_item_fbs'] = $request->post('appchk_item_fbs');
        $data_checkup['appchk_item_buncr'] = $request->post('appchk_item_buncr');
        $data_checkup['appchk_item_choles'] = $request->post('appchk_item_choles');
        $data_checkup['appchk_item_ua'] = $request->post('appchk_item_ua');
        $data_checkup['appchk_item_tgcer'] = $request->post('appchk_item_tgcer');
        $data_checkup['appchk_item_lft'] = $request->post('appchk_item_lft');
        $data_checkup['appchk_item_vdrl'] = $request->post('appchk_item_vdrl');
        $data_checkup['appchk_item_afp'] = $request->post('appchk_item_afp');
        $data_checkup['appchk_item_cea'] = $request->post('appchk_item_cea');
        $data_checkup['appchk_item_hb'] = $request->post('appchk_item_hb');
        $data_checkup['appchk_item_ekg'] = $request->post('appchk_item_ekg');
        $data_checkup['pep_check'] = $request->post('pep_check');
        $data_checkup['appchk_item_pep1'] = $request->post('appchk_item_pep1');
        $data_checkup['appchk_item_pep2'] = $request->post('appchk_item_pep2');
        $data_checkup['appchk_item_pep3'] = $request->post('appchk_item_pep3');
        $data_checkup['appchk_item_pep3'] = $request->post('appchk_item_pep3');
        $data_checkup['appchk_right'] = $request->post('right');
        $data_checkup['appchk_visit_id'] = $modelVisit['data']['id'];
        $data_checkup['app_chk_pt_id'] = $dataid;
        $data_checkup['appchk_pk'] = $request->post('age');
        $data_checkup['appchk_status'] = $request->post('appchk_status');
        $data_checkup['appchk_project_id'] = '';
        $data_checkup['my_5a43a576ed6b5'] = $dataid;
        $app_checkup = PatientFunc::loadTbDataByField($ezfcheckup_tbname, ['appchk_visit_id' => $modelVisit['data']['id']]);
        $app_checkup_id = '';
        if ($app_checkup) {
            PatientFunc::saveDataNoSys($ezf_checkup_id, $ezfcheckup_tbname, $app_checkup['id'], $data_checkup);
            $app_checkup_id = $app_checkup['id'];
        } else {
            $app_checkup = PatientFunc::BackgroundInsert($ezf_checkup_id, '', $modelVisit['data']['id'], $data_checkup);
            $app_checkup_id = $app_checkup['data']['id'];
        }
        $this->SubmitVisitCheckup($modelVisit['data']['id'], $dataid, $app_checkup_id, $getdate, $visitdatelod);
        $data_response['pt_id'] = $dataid;
        $data_response['date'] = $getdate;
        $data_response['visit_type'] = '1';
        $arrdata[] = $data_response;
        Yii::$app->api->sendSuccessResponse($arrdata);
    }

    public function actionReportnavi() {
        $visit_type = Yii::$app->request->post('visit_type');
        $dept = Yii::$app->request->post('dept');
        $date = Yii::$app->request->post('date');
        $dataid = Yii::$app->request->post('dataid');
        $dataProfile = PatientQuery::getPtProfile($dataid);
        if (strpos($dataProfile['pt_bdate'], '/')) {
            $bdate = ' อายุ ' . SDdate::getAge(SDdate::dateTh2bod($dataProfile['pt_bdate'])) . ' ปี';
        } else {
            $bdate = \backend\modules\patient\classes\PatientFunc::integeter2date($dataProfile['pt_bdate']);
            $bdate = ' อายุ ' . SDdate::getAge(SDdate::dateTh2bod($bdate)) . ' ปี';
        }

        if ($visit_type == '1') {
            $dataVisitTran = PatientQuery::getVisit($dataid, $date);
            $dataVisitTran['sect_name'] = 'OPD ตรวจสุขภาพ,';

            $data = PatientQuery::getOrderGroupCounter($dataVisitTran['visit_id'], '1');
            $txtCounter = '';
            foreach ($data as $value) {
                $txtCounter .= $this->getCounterName($value['group_type']);
            }
        } else {
            $dataVisitTran = PatientQuery::getVisitTran($dataid, $dept, $date);
        }
        $data['visit_type'] = 'ประเภทการมา : ' . $this->getVisitTypeName($dataVisitTran['visit_type']);
        $data['age'] = $bdate;
        $data['hn_no'] = 'HN : ' . $dataProfile['pt_hn'] . ' ' . $bdate;
        $data['hn_no_qrcode'] = $dataProfile['pt_hn'];
        $data['sect_name'] = $dataVisitTran['sect_name'];
        $data['txtCounter'] = 'ตรวจแผนก : ' . $data['sect_name'] . ' ' . $txtCounter;
        $data['getvisittypename'] = $this->getVisitTypeName($dataVisitTran['visit_type']);
        $data['visit_date'] = 'วันที่นัดตรวจ : ' . SDdate::mysql2phpThDateTime($dataVisitTran['visit_date']);
        $data['fullname'] = ' ชื่อ - สกุล : ' . $dataProfile['fullname'];
        $dataRight = PatientQuery::getRightLast($dataProfile['pt_id']);
        if ($dataRight['right_project_id']) {
            $ezf_id = Yii::$app->controller->module->patientFormID['patientright'];
            $modelFields = \backend\modules\ezforms2\models\EzformFields::find()->where('ezf_id = :ezf_id AND ezf_field_name = :ezf_field_name', [':ezf_field_name' => 'right_project_id', ':ezf_id' => $ezf_id])->one();
            if (isset(Yii::$app->session['ezf_input'])) {
                $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($modelFields['ezf_field_type'], Yii::$app->session['ezf_input']);
            }
            $dataRight['right_project_id'] = \backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $modelFields, $dataRight);
        }
        $price = PatientQuery::getCashierCounterItemSum($dataVisitTran['visit_id']);
        $data['right_name'] = 'สิทธิ : ' . $dataRight['right_name'];
        $price2 = !empty($price['pay']) ? number_format($price['pay'], 2) : 0;
        $data['text_total'] = "ยอดที่ต้องชำระ : {$price2} บาท";
        $arrdata[] = $data;
        Yii::$app->api->sendSuccessResponse($arrdata);
    }

    public function actionCheckdate() {
        $dates_android = Yii::$app->request->post("dates");
        $dates_arr = explode('/', $dates_android);
        $y = $dates_arr[2];
        $dept = 'S074';
        $dates = $dates_arr[2] . '-' . sprintf("%02d", ($dates_arr[1])) . '-' . sprintf("%02d", ($dates_arr[0]));
        $status_toholriday = \backend\modules\restfull\classes\RestfullFunc::toholriday($dates, $dates);
        $stopEvents = PatientFunc::getCalendarStopEvent($y, $dates, $dates);
        $dataEvents = \backend\modules\restfull\classes\RestfullQuery::getAppointConfByDate($dept, $dates);
        $arr = [];
        $status_stopevent = 'false';
        foreach ($stopEvents as $row) {
            $arr[] = $row['tr_date'];
        }
        if (in_array($dates, $arr)) {
            $status_stopevent = 'true';
        }
        $status_dataEvents = 'false';
        if (!empty($dataEvents)) {
            $status_dataEvents = 'true';
        }
        $data = [
            'status_horiday' => $status_toholriday,
            'status_stopevent' => $status_stopevent,
            'status_dataEvents' => $status_dataEvents,
            'dates' => $dates
        ];
        $arrdata[] = $data;
        Yii::$app->api->sendSuccessResponse($arrdata);
    }

    public function actionGetlistcheckup() {
        Yii::$app->session['ezf_input'] = \backend\modules\ezforms2\classes\EzfQuery::getInputv2All();
        $request = Yii::$app->request;
        if ($request->isPost) {
            $ezf_tbname = 'zdata_patientprofile';
            $user_id = $request->post('user_id');
            $model_profile = FrontendQuery::getPt_idbyuserid($user_id);
            $reportCheckup = FrontendQuery::getCheckupResult("", $model_profile['ptid']);
            $arr = [];
            $arrs = [];
            foreach ($reportCheckup as $row) {
                $arr['visit_date'] = SDdate::mysql2phpThDateSm($row['visit_date']);
                $arr['ckr_summary_check'] = $row['ckr_summary'];
                $arr['ckr_summary'] = $this->getValue($row, 'ckr_summary');
                $arr['ckr_summary_detail'] = $row['ckr_summary_detail'];
                $arr['visit_id'] = $row['visit_id'];
                $arr['pt_hn'] = $row['pt_hn'];
                $arr['pt_id'] = $row['pt_id'];
                $arrs[] = $arr;
                $arr = [];
            }
            Yii::$app->api->sendSuccessResponse($arrs);
        }
    }

    public function actionCheckmregis() {
        $request = Yii::$app->request;
        if ($request->isPost) {
            $data = '';
            $ezf_tbname = 'zdata_mobileregister';
            $model_mobile = PatientFunc::loadTbDataByField($ezf_tbname, ['mac_address' => $request->post('mac_address')]);
            if ($model_mobile != FALSE) {
                $data['status'] = '1';
                $data['text'] = 'เข้าสู่ระบบสำเร็จ';
                $data['user_id'] = $model_mobile['user_id'];
            } else {
                $data['status'] = '0';
                $data['text'] = 'กรุณาสมัครสมาชิก';
                $data['user_id'] = '0';
            }
            $arr[] = $data;
            Yii::$app->api->sendSuccessResponse($arr);
        }
    }

    public function actionLogin() {
        $ezf_tbname = 'zdata_mobileregister';
        $ezf_id = '1523958554000417700';
        $request = Yii::$app->request;
        if ($request->isPost) {
            $model = \Yii::createObject(\dektrium\user\models\LoginForm::className());
            if ($model->load(\Yii::$app->request->post()) && $model->login()) {
                $user_find = \common\modules\user\models\User::findOne(['username' => $request->post('login-form')['login']]);
                $datas['user_id'] = $user_find->id;
                $datas['mac_address'] = $request->post('mac_address');
                $model_mobile = PatientFunc::loadTbDataByField($ezf_tbname, ['user_id' => $datas['user_id'], 'mac_address' => $datas['mac_address']]);
                if ($model_mobile == false) {
                    PatientFunc::BackgroundInsert($ezf_id, '', '', $datas);
                }
                $data['user_id'] = $user_find->id;
                $data['statuss'] = 1;
                $arr[] = $data;
                Yii::$app->api->sendSuccessResponse($arr);
            } else {
                $data['user_id'] = 0;
                $data['statuss'] = 0;
                $arr[] = $data;
                Yii::$app->api->sendSuccessResponse($arr);
            }
        }
    }

    public function actionRegister() {
        $request = Yii::$app->request;
        if ($request->isPost) {
            $ezf_tbname = 'zdata_mobileregister';
            $ezf_id = '1523958554000417700';
            $user = '';
            $data = '';
            $user_find = \common\modules\user\models\User::findOne(['username' => $request->post('register-form')['username']]);
            $email_find = \common\modules\user\models\User::findOne(['email' => $request->post('register-form')['email']]);
            $pt_date = $request->post('register-form')['bdate'];
            $pt_cid = $request->post('register-form')['cid'];
            $arrs = $this->Checkptidbycid($pt_date, $pt_cid);
            if (!empty($user_find)) {
                $data['user_find_status'] = 1;
                $data['email_find_status'] = 0;
                $data['pt_cid'] = 0;
                $data['pt_bdate'] = 0;
            } else if (!empty($email_find)) {
                $data['email_find_status'] = 1;
                $data['user_find_status'] = 0;
                $data['pt_cid'] = 0;
                $data['pt_bdate'] = 0;
            } else if ($arrs['pt_cid'] == FALSE && $arrs['pt_bdate'] == FALSE) {
                $data['email_find_status'] = 1;
                $data['user_find_status'] = 0;
                $data['pt_cid'] = 1;
                $data['pt_bdate'] = 1;
            } else {
                $model = \Yii::createObject(RegistrationForm::className());
                $model->load(\Yii::$app->request->post());
                $model->register();
                $user = \common\models\User::findOne(['email' => $request->post('register-form')['email']]);
                $profile = \common\modules\user\models\Profile::findOne(['user_id' => $user['id']]);
                $datas['user_id'] = $user['id'];
                $datas['mac_address'] = Yii::$app->request->post('mac_address');
                $this->SaveProfile($pt_cid, $user['id']);
                $model_mobile = PatientFunc::loadTbDataByField($ezf_tbname, ['user_id' => $datas['user_id'], 'mac_address' => $datas['mac_address']]);
                if ($model_mobile == false) {
                    PatientFunc::BackgroundInsert($ezf_id, '', '', $datas);
                }
                $data['name'] = $profile['name'];
                $data['user_id'] = $user['id'];
                $data['user_find_status'] = 0;
                $data['email_find_status'] = 0;
                $data['pt_cid'] = 0;
                $data['pt_bdate'] = 0;
            }
            $arr[] = $data;
            Yii::$app->api->sendSuccessResponse($arr);
        }
    }

    private function SaveProfile($pt_cid, $pt_user_id) {
        if (Yii::$app->request->post()) {
            $ezf_id = '1503378440057007100';
            $data['pt_cid'] = str_replace('-', '', $pt_cid);
            $profileData = PatientQuery::getPatientSearch($pt_cid, '12276');
            if (empty($profileData)) {
                $data['pt_user_id'] = $pt_user_id;
                $data['pt_pic'] = '';
                $dataRight = PatientFunc::getRightOnlineByNhso($data['pt_cid']);
                $sqlDate = $dataRight['birthdate'];
                $sqlDate = substr($sqlDate, 6, 2) . '/' . substr($sqlDate, 4, 2) . '/' . substr($sqlDate, 0, 4);
                $data['pt_bdate'] = $sqlDate;
                $dataSex = PatientQuery::getPrefixId($dataRight['title_name']);
                $data['pt_sex'] = $dataSex['prefix_sex'];
                $data['pt_prefix_id'] = $dataSex['prefix_id'];
                $data['pt_firstname'] = $dataRight['fname'];
                $data['pt_lastname'] = $dataRight['lname'];
                $data['pt_address'] = '';
                $data['pt_moi'] = $dataRight['primary_moo'];
                $data['pt_addr_tumbon'] = $dataRight['primary_tumbon_name'];
                $data['pt_addr_amphur'] = $dataRight['primary_amphur_name'];
                $data['pt_addr_province'] = $dataRight['primary_province_name'];
                $dataTAC = PatientQuery::getProviceByName($data['pt_addr_tumbon'], $data['pt_addr_amphur'], $data['pt_addr_province']);
                $data['pt_addr_tumbon'] = $dataTAC['DISTRICT_CODE'];
                $data['pt_addr_amphur'] = $dataTAC['AMPHUR_CODE'];
                $data['pt_addr_province'] = $dataTAC['PROVINCE_CODE'];
                $data['pt_addr_zipcode'] = $dataTAC['zipcode'];

                $dataSerene = \backend\modules\patient\classes\PatientFunc::checkPtProfileOld($data['pt_cid']);
                if ($dataSerene['value']['status'] == 'OLD') {
                    $dataSerene = $dataSerene['value'];
                    $data['pt_hn'] = $dataSerene['pt_hn'];
                    $data['pt_national_id'] = $dataSerene['pt_national_id'];
                    $data['pt_origin_id'] = $dataSerene['pt_national_id'];
                    $data['pt_religion_id'] = $dataSerene['pt_religion_id'];
                    $data['pt_mstatus'] = $dataSerene['pt_mstatus'];
                    $data['pt_occ'] = $dataSerene['pt_occ'];
                    $data['pt_phone2'] = $dataSerene['pt_phone2'];
                    $data['pt_contact_name'] = $dataSerene['pt_contact_name'];
                    $data['pt_contact_status'] = $dataSerene['pt_contact_status'];
                    $data['pt_contact_phone'] = $dataSerene['pt_contact_phone'];
                }
                $dataid = \backend\modules\patient\classes\PatientFunc::backgroundInsert($ezf_id, '', '', $data)['data']['id'];
                $data['pt_id'] = $dataid;
                $dataid = $data;
            } else {
                $dataid = $profileData[0]['id'];
                $data['pt_user_id'] = $pt_user_id;
                PatientFunc::saveDataNoSys($ezf_id, 'zdata_patientprofile', $dataid, $data);
                $profileData[0]['pt_user_id'] = $pt_user_id;
                $dataid = $profileData[0];
            }

            return $dataid;
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    private function Checkptidbycid($pt_date, $pt_cid) {
        $pt_date = $pt_date; //\appxq\sdii\utils\SDdate::dateTh2bod($request->post('pt_date'));
        $pt_cid = $pt_cid;
        $profileData = PatientQuery::getPatientSearch($pt_cid, '12276');
        $dataSerene = \backend\modules\patient\classes\PatientFunc::checkPtProfileOld($pt_cid);
        $arrs['pt_cid'] = FALSE;
        $arrs['pt_date'] = FALSE;
        if ($profileData) {
            if ($profileData[0]['pt_cid'] == $pt_cid && $profileData[0]['pt_bdate'] == $pt_date) {
                $arrs['pt_cid'] = TRUE;
                $arrs['pt_date'] = TRUE;
                $arrs['pt_status'] = 1;
            } else {
                $arrs['pt_cid'] = FALSE;
                $arrs['pt_date'] = FALSE;
            }
        } else if ($dataSerene) {
            if ($dataSerene['value']['pt_bdate'] == $pt_date) {
                $arrs['pt_cid'] = TRUE;
                $arrs['pt_date'] = TRUE;
                $arrs['pt_status'] = 2;
            } else {
                $arrs['pt_cid'] = FALSE;
                $arrs['pt_date'] = FALSE;
            }
        } else {
            $dataRight = PatientFunc::getRightOnlineByNhso($pt_cid);
            $sqlDate = $dataRight['birthdate'];
            $sqlDate = substr($sqlDate, 6, 2) . '/' . substr($sqlDate, 4, 2) . '/' . substr($sqlDate, 0, 4);
            $data['pt_bdate'] = $sqlDate;
            if ($data['pt_bdate'] == $pt_date && $pt_cid == $dataRight['person_id']) {
                $arrs['pt_cid'] = TRUE;
                $arrs['pt_date'] = TRUE;
                $arrs['pt_status'] = 3;
            } else {
                $arrs['pt_cid'] = FALSE;
                $arrs['pt_date'] = FALSE;
            }
        }
        return $arrs;
    }

    private function getValue($data, $fieldName) {
        $ezf_id = Yii::$app->controller->module->patientFormID['report_checkup'];
        $modelFields = \backend\modules\ezforms2\models\EzformFields::find()->where('ezf_id = :ezf_id AND ezf_field_name = :ezf_field_name', [':ezf_field_name' => $fieldName, ':ezf_id' => $ezf_id])->one();
        if (isset(Yii::$app->session['ezf_input'])) {
            $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($modelFields['ezf_field_type'], Yii::$app->session['ezf_input']);
        }
        return \backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $modelFields, $data);
    }

    public function SubmitVisitCheckup($visit_id, $pt_id, $dataid, $dates, $datesold) {
//        if (Yii::$app->getRequest()->isAjax) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $ezf_id = '1511490170071641200'; //Yii::$app->controller->module->patientFormID['app_checkup'];
        $ezf_table = 'zdata_app_checkup'; //Yii::$app->controller->module->patientFormTableName['app_checkup'];
        $ezfVisit_tbname = 'zdata_visit'; // Yii::$app->controller->module->patientFormTableName['visit'];
//            $visit_id = Yii::$app->request->get('visit_id');
//            $pt_id = Yii::$app->request->get('pt_id');
//            $dataid = Yii::$app->request->get('dataid');
//            $dates = Yii::$app->request->get('dates');
//            $datesold = Yii::$app->request->get('datesold');

        $visit_type = '1'; //checkup

        $dataCheckup = EzfUiFunc::loadTbData($ezf_table, $dataid);
        if ($dataCheckup) {
            if ($dataCheckup['appchk_right'] == '1') {
                $data['right_code'] = 'PRO';
                $data['right_status'] = '2';
                $data['right_project_id'] = $dataCheckup['appchk_project_id'];
            } elseif ($dataCheckup['appchk_right'] == '3') {
                $data['right_code'] = 'LGO';
                $data['right_sub_code'] = 'L1';
                $data['right_status'] = '8';
            } elseif ($dataCheckup['appchk_right'] == '4') {
                $data['right_code'] = 'ORI-G';
                $data['right_status'] = '2';
            } elseif ($dataCheckup['appchk_right'] == '5') {
                $data['right_code'] = 'ORI';
                $data['right_status'] = '2';
            } else {
                $data['right_status'] = '2';
                $data['right_code'] = 'CASH';
            }
            $ezfPtRight_id = '1505016314072939900'; //Yii::$app->controller->module->patientFormID['patientright'];
            PatientFunc::backgroundInsert($ezfPtRight_id, '', $pt_id, $data);

            $dept = ''; //S047 opd checkup
            $resultVisit = $this->saveVisit($pt_id, $visit_id, $visit_type, $dept);

            if ($resultVisit) {
                if ($dataCheckup['appchk_status'] == '1') {
                    $ezf_id = '1514016599071774100'; //Yii::$app->controller->module->patientFormID['report_checkup'];
                    PatientFunc::backgroundInsert($ezf_id, '', $resultVisit['data']['id'], ['ckr_status' => '1']);
                }
                $order_status = $visit_type . '^' . $data['right_code'] . '^' . $dataCheckup['appchk_pk'];

                $item['CH^X'] = $dataCheckup['appchk_item_chest']; //Chest
                $item['FE001^L'] = $dataCheckup['appchk_item_seob']; //Stool
                $item['FE002^L'] = $dataCheckup['appchk_item_seob']; //Occult
                $item['HM001^L'] = $dataCheckup['appchk_item_cbc']; //CBC
                $item['UR001^L'] = $dataCheckup['appchk_item_ux']; //Urine

                $item['BC001^L'] = $dataCheckup['appchk_item_fbs']; //FBS
                $item['BC015^L'] = $dataCheckup['appchk_item_aaa'];  //AST
                $item['BC016^L'] = $dataCheckup['appchk_item_aaa'];  //ALT
                $item['BC017^L'] = $dataCheckup['appchk_item_aaa'];  //ALP

                $item['BC002^L'] = $dataCheckup['appchk_item_buncr']; //bun
                $item['BC003^L'] = $dataCheckup['appchk_item_buncr']; //cr
                $item['BC005^L'] = $dataCheckup['appchk_item_ua']; //Uric Acid
                $item['BC006^L'] = $dataCheckup['appchk_item_choles']; //Cholesterol
                $item['BC009^L'] = $dataCheckup['appchk_item_tgcer']; //Triglyceride
                $item['CG001^L'] = $dataCheckup['appchk_item_pep1']; //pep1

                if ($dataCheckup['appchk_item_lft'] == '1') {
                    $item['BC011^L'] = $dataCheckup['appchk_item_lft']; //LFT1
                    $item['BC012^L'] = $dataCheckup['appchk_item_lft']; //LFT2
                    $item['BC013^L'] = $dataCheckup['appchk_item_lft']; //LFT3
                    $item['BC014^L'] = $dataCheckup['appchk_item_lft']; //LFT4
                    $item['BC015^L'] = $dataCheckup['appchk_item_lft']; //LFT5
                    $item['BC016^L'] = $dataCheckup['appchk_item_lft']; //LFT6
                    $item['BC017^L'] = $dataCheckup['appchk_item_lft']; //LFT7
                }

                $item['IM006^L'] = $dataCheckup['appchk_item_vdrl']; //vdrl
                $item['IM001^L'] = $dataCheckup['appchk_item_afp']; //afp
                $item['IM002^L'] = $dataCheckup['appchk_item_hb']; //hb
                $item['PH001^L'] = $dataCheckup['appchk_item_ekg']; //ekg
                $item['IM047^L'] = $dataCheckup['appchk_item_psa']; //psa
                $item['IM008^L'] = $dataCheckup['appchk_item_cea']; //cea

                $item['CG020^L'] = $dataCheckup['appchk_item_pep2']; //Liquid base
                if ($dataCheckup['appchk_item_pep3']) {
                    $item['CG015^L'] = '1'; //pep2
                    $item['CG016^L'] = $dataCheckup['appchk_item_pep3']; //pep3
                } else {
                    $item['CG015^L'] = '0';
                    $item['CG016^L'] = '0';
                }
                foreach ($item as $key => $value) {
                    $itemtype = explode('^', $key);

                    $checkOrder = self::checkOrderCancelStatus($visit_id, $itemtype[0]);
                    if ($value == '1' && $checkOrder == TRUE) {
                        self::saveOrderItem($visit_id, $itemtype[0], $itemtype[1], $order_status);
                    }
                }
                PatientQuery::updateVisitdateBytarget($visit_id, $dates);
            } else {
                $result = [
                    'status' => 'error',
                    'message' => SDHtml::getMsgError() . 'ไม่สามารถส่งตรวจได้ กรุณาลองใหม่',
                ];
            }
        } else {
            $result = [
                'status' => 'error',
                'message' => SDHtml::getMsgError() . 'ไม่สามารถส่งตรวจได้ กรุณาลองใหม่',
            ];
        }

        return true;
    }

    public static function saveVisit($pt_id, $visit_id, $visit_type, $dept) {
        $ezf_id = '1503589101005614900'; //Yii::$app->controller->module->patientFormID['visit'];
        $ezfVisit_tbname = 'zdata_visit'; // Yii::$app->controller->module->patientFormTableName['visit'];
        //init data visit
        $data['visit_date'] = date('Y-m-d H:i:s');
        $data['visit_status'] = '1';
        $data['visit_type'] = $visit_type;
        if (empty($visit_id)) {
//            $modelVisit = PatientFunc::loadTbDataByTarget($ezfVisit_tbname, $pt_id, date('Y-m-d'));
            $modelVisit = PatientFunc::loadTbDataByField($ezfVisit_tbname, ['ptid' => $pt_id,
                        'DATE(visit_date)' => ['visit_date' => date('Y-m-d')]]);
            if ($modelVisit) {
                //update visit
                $result = PatientFunc::saveDataNoSys($ezf_id, $ezfVisit_tbname, $modelVisit['id'], $data);
            } else {
                //insert visit
                $result = EzfUiFuncrestful::BackgroundInsert($ezf_id, '', $pt_id, $data);
            }

            $visit_id = $result['data']['id'];
        } else {
            $result = PatientFunc::saveDataNoSys($ezf_id, $ezfVisit_tbname, $visit_id, $data);
        }
        $data = null;
        if ($dept) {
            $userProfile = Yii::$app->user->identity->profile->attributes;
            if ($userProfile['position'] == '2') {
                $data['visit_tran_doctor'] = $userProfile['user_id'];
                $data['visit_tran_doc_status'] = '1';
            }
            $data['visit_tran_dept'] = $dept;
            $data['visit_tran_status'] = '1';

            $result = self::saveVisitTran($pt_id, $visit_id, $data);
        }

        return $result;
    }

    public static function saveVisitTran($pt_id, $visit_id, $data) {
        $ezfVisitTran_id = '1506694193013273800'; //Yii::$app->controller->module->patientFormID['visit_tran'];
        $ezfVisitTran_tbname = 'zdata_visit_tran'; // Yii::$app->controller->module->patientFormTableName['visit_tran'];
        if ($data['visit_tran_dept']) {
            //chk visit_tran insert,update
            $dataVisitTran = PatientQuery::getVisitTran($pt_id, $data['visit_tran_dept'], date('Y-m-d'), '1');
            if ($dataVisitTran) {
                $result = PatientFunc::saveDataNoSys($ezfVisitTran_id, $ezfVisitTran_tbname, $dataVisitTran['visit_tran_id'], $data);
            } else {
                $result = EzfUiFuncrestful::backgroundInsert($ezfVisitTran_id, '', $visit_id, $data);
            }
        } else {
            $result = [
                'status' => 'error',
                'message' => SDHtml::getMsgError() . Yii::t('app', 'Find not found.'),
            ];
        }
        return $result;
    }

    public static function checkOrderCancelStatus($visit_id, $order_code) {
        $ezf_id = '1504537671028647300'; // Yii::$app->controller->module->patientFormID['order_tran'];
        $ezf_table = 'zdata_order_tran'; //Yii::$app->controller->module->patientFormTableName['order_tran'];
        $result = FALSE;
        $data = PatientQuery::getOrderCheckStatus($visit_id, $order_code);
        if ($data) {
            if ($data['order_tran_status'] == '1' && $data['order_tran_cashier_status'] == '') {
                $initdata = ['rstat' => 3];
                PatientFunc::saveDataNoSys($ezf_id, $ezf_table, $data['id'], $initdata);
                $result = true;
            }
        } else {
            $result = true;
        }

        return $result;
    }

    public static function saveOrderItem($target, $item, $grouptype, $right_order) {
        $ezf_id = '1504537671028647300'; //Yii::$app->controller->module->patientFormID['order_tran'];

        if ($grouptype == 'P') {
            $arrItemCode = PatientQuery::getPackageItem($item);
            if ($arrItemCode) {
                foreach ($arrItemCode as $value) {
                    $dataItem = \backend\modules\patient\models\ConstOrder::findOne(['order_code' => $value]);
                    $initdata = self::flagRightPrice($right_order, $dataItem, $value['package_qty']);
                    $result = PatientFunc::backgroundInsert($ezf_id, '', $target, $initdata);
                }
            } else {
                $result = [
                    'status' => 'error',
                    'message' => \appxq\sdii\helpers\SDHtml::getMsgError() . Yii::t('app', 'No item.'),
                ];
            }
        } else {
            $dataItem = \backend\modules\patient\models\ConstOrder::findOne(['order_code' => $item]);
            $initdata = self::flagRightPrice($right_order, $dataItem, '1');
            $result = PatientFunc::backgroundInsert($ezf_id, '', $target, $initdata);
        }
        return $result;
    }

    private static function flagRightPrice($right_order, $dataItem, $qty) {
        $arrRight = explode("^", $right_order);
        $initdata = '';
//        $userProfile = Yii::$app->user->identity->profile->attributes;
        $initdata['order_qty'] = $qty;
        if ($arrRight[0] == '1') { //viittype CheckUp
            $dataItem['full_price'] = $qty * $dataItem['full_price_checkup'];
            if ($dataItem['checkup_flag_pay'] == '1') {
                $initdata['order_tran_notpay'] = 0;
                $initdata['order_tran_pay'] = $dataItem['full_price'];
            } elseif ($dataItem['checkup_flag_pay'] == '2' && $arrRight[2] < 35) {
                $initdata['order_tran_notpay'] = 0;
                $initdata['order_tran_pay'] = $dataItem['full_price'];
            } else {
                if ($dataItem['flag_pay'] == '1') {
                    $initdata['order_tran_notpay'] = 0;
                    $initdata['order_tran_pay'] = $dataItem['full_price'];
                } else {
                    $initdata['order_tran_notpay'] = $dataItem['full_price'];
                    $initdata['order_tran_pay'] = 0;
                }
            }
        } else {
            $dataItem['full_price'] = $qty * $dataItem['full_price'];
            if ($dataItem['flag_pay'] == '1') {
                $initdata['order_tran_notpay'] = 0;
                $initdata['order_tran_pay'] = $dataItem['full_price'];
            } else {
                $initdata['order_tran_notpay'] = $dataItem['full_price'];
                $initdata['order_tran_pay'] = 0;
            }
        }
        $initdata['order_tran_status'] = '1';
        $initdata['order_tran_dept'] = 'S999'; //$userProfile['department'];
        $initdata['order_tran_code'] = $dataItem['order_code'];
//        if ($userProfile['position'] == '2') {
//            $initdata['order_tran_doctor'] = $userProfile['user_id'];
//        }

        return $initdata;
    }

    private function getVisitTypeName($visit_type) {
        $typeTxt = "";
        switch ($visit_type) {
            case '1': //opd checkup
                $typeTxt = 'ตรวจสุขภาพ';
                break;
            case '2': //Appointment 
                $typeTxt = 'ตรวจตามนัด';
                break;
            case '3': //refer
                $typeTxt = 'ส่งต่อการรักษา';
                break;
            case '4': //ส่งตรวจสอบสิทธิก่อน
                $typeTxt = 'ตรวจรักษาโรค';
                break;
            default:
                return FALSE;
        }

        return $typeTxt;
    }

    private function getCounterName($counter_code) {
        $result = "";
        switch ($counter_code) {
            case 'X': //opd checkup
                $result = ' XRAY,';
                break;
            case 'L': //Appointment 
                $result = ' LAB,';
                break;
            case 'E': //refer
                $result = ' ตรวจคลื่นหัวใจ(EKG),';
                break;
            case 'C': //ส่งตรวจสอบสิทธิก่อน
                $result = ' ตรวจภายใน,';
                break;
            default:
                return FALSE;
        }

        return $result;
    }

    public function actionMe() {
        $data = Yii::$app->user->identity;
        $data = $data->attributes;
        unset($data['auth_key']);
        unset($data['password_hash']);
        unset($data['password_reset_token']);

        Yii::$app->api->sendSuccessResponse($data);
    }

    public function actionAccesstoken() {

        if (!isset($this->request["authorization_code"])) {
            Yii::$app->api->sendFailedResponse("Authorization code missing");
        }

        $authorization_code = $this->request["authorization_code"];

        $auth_code = AuthorizationCodes::isValid($authorization_code);
        if (!$auth_code) {
            Yii::$app->api->sendFailedResponse("Invalid Authorization Code");
        }

        $accesstoken = Yii::$app->api->createAccesstoken($authorization_code);

        $data = [];
        $data['access_token'] = $accesstoken->token;
        $data['expires_at'] = $accesstoken->expires_at;
        Yii::$app->api->sendSuccessResponse($data);
    }

    public function actionAuthorize() {
        $model = new LoginForm();

        $model->attributes = $this->request;
        if ($model->validate() && $model->login()) {
            $auth_code = Yii::$app->api->createAuthorizationCode(Yii::$app->user->identity['id']);

            $data = [];
            $data['authorization_code'] = $auth_code->code;
            $data['expires_at'] = $auth_code->expires_at;

            Yii::$app->api->sendSuccessResponse($data);
        } else {
            Yii::$app->api->sendFailedResponse($model->errors);
        }
    }

    public function actionLogout() {
        $headers = Yii::$app->getRequest()->getHeaders();
        $access_token = $headers->get('x-access-token');

        if (!$access_token) {
            $access_token = Yii::$app->getRequest()->getQueryParam('access-token');
        }

        $model = AccessTokens::findOne(['token' => $access_token]);

        if ($model->delete()) {

            Yii::$app->api->sendSuccessResponse(["Logged Out Successfully"]);
        } else {
            Yii::$app->api->sendFailedResponse("Invalid Request");
        }
    }

}
