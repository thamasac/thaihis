<?php

namespace backend\modules\patient\controllers;

use backend\modules\ezforms2\models\TbdataAll;
use Yii;
use yii\db\Expression;
use yii\web\Response;
use backend\modules\patient\classes\PatientQuery;
use backend\modules\patient\classes\PatientFunc;
use backend\modules\ezforms2\classes\EzfUiFunc;
use appxq\sdii\helpers\SDHtml;
use yii\web\NotFoundHttpException;

class PatientController extends \yii\web\Controller {

    public function actionIndex() {
        $tab = Yii::$app->request->get('tab', 1);
        $dataid = Yii::$app->request->get('dataid');
        Yii::$app->session['ezf_input'] = \backend\modules\ezforms2\classes\EzfQuery::getInputv2All();
        if (empty($dataid) && $tab <> 1) {
            $tab = 1;
            Yii::$app->session->setFlash('alert', [
                'body' => SDHtml::getMsgError() . Yii::t('patient', 'Please choose patient'),
                'options' => ['class' => 'alert-warning']
            ]);
        }

        return $this->render('index', ['dataid' => $dataid, 'tab' => $tab]);
    }

    public function actionView() {
        if (Yii::$app->getRequest()->isAjax) {
            $ezf_id = \backend\modules\patient\Module::$formID['profile'];
            $ezfVisit_id = \backend\modules\patient\Module::$formID['visit'];
            $ezfVisit_tbname = \backend\modules\patient\Module::$formTableName['visit'];
            $ezfPtRight_id = \backend\modules\patient\Module::$formID['patientright'];
            $ezfPtHt_id = \backend\modules\patient\Module::$formID['profilehistory'];
            $ezfPtHt_tbname = \backend\modules\patient\Module::$formTableName['profilehistory'];
            $reloadDiv = Yii::$app->request->get('reloadDiv');
            $dataid = Yii::$app->request->get('dataid');

            $dataRight = FALSE;
            $modelVisit = FALSE;
            //load patientprefile data
            $dataProfile = \backend\modules\thaihis\classes\ThaiHisQuery::getPtProfile($dataid);
            $dataPtHt = PatientFunc::loadTbDataByTarget($ezfPtHt_tbname, $dataid);
            //ป้องกันใส่ pt_id มั่ว
            if (empty($dataProfile['pt_id']) && isset($dataid)) {
                Yii::$app->session->setFlash('alert', [
                    'body' => SDHtml::getMsgError() . Yii::t('patient', 'Not found patient id'),
                    'options' => ['class' => 'alert-warning']
                ]);

                Yii::$app->controller->redirect(\yii\helpers\Url::to('/patient/patient'));
                return;
            }

            //load visit //check admit ???
            $dataCheckUp = '';
            $project_id = '';
            if (isset($dataid)) {
//                $modelVisit = PatientFunc::loadTbDataByTarget($ezfVisit_tbname, $dataid, date('Y-m-d'));
                $modelVisit = PatientFunc::loadTbDataByField($ezfVisit_tbname, ['ptid' => $dataid,
                            'DATE(visit_date)' => ['visit_date' => date('Y-m-d')]]);
                if (empty($modelVisit)) {
                    $modelVisit = EzfUiFunc::backgroundInsert($ezfVisit_id, '', $dataid);
                }

                if ($modelVisit['visit_type'] == '1') {
                    $ezfCheckup_table = \backend\modules\patient\Module::$formTableName['app_checkup'];
                    $dataCheckUp = PatientFunc::loadTbDataByTarget($ezfCheckup_table, $modelVisit['id']);
                }
                $dataRight = PatientFunc::getRightOnlineByNhso($dataProfile['pt_cid']);
                $project_id = \backend\modules\cpoe\classes\CpoeQuery::getProjectCheckVisit($dataProfile['pt_cid'])['target_project'];
            }

            return $this->renderAjax('_patient_profile', [
                        'ezf_id' => $ezf_id,
                        'ezfPtHt_id' => $ezfPtHt_id,
                        'dataPtHt' => $dataPtHt,
                        'dataid' => $dataid,
                        'dataProfile' => $dataProfile,
                        'modelVisit' => $modelVisit,
                        'reloadDiv' => $reloadDiv,
                        'ezfVisit_id' => $ezfVisit_id,
                        'ezfPtRight_id' => $ezfPtRight_id,
                        'dataRight' => $dataRight,
                        'dataCheckUp' => $dataCheckUp,
                        'project_id' => $project_id,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionUpdateHn() {
        if (Yii::$app->getRequest()->isAjax) {
            $target = Yii::$app->request->get('target');
            $pt_hn = Yii::$app->request->get('pt_hn');

            return $this->renderAjax('_view_update_hn', [
                        'target' => $target,
                        'pt_hn' => $pt_hn,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionUpdateHnSave() {
        if (Yii::$app->getRequest()->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $target = Yii::$app->request->post('target');
            $pt_hn_new = Yii::$app->request->post('pt_hn_new');
            $pt_hn_old = Yii::$app->request->post('pt_hn_old');
            $checkhn = \backend\modules\subjects\classes\SubjectManagementQuery::GetTableData('zdata_patientprofile', ['pt_hn' => $pt_hn_new], 'one');
            if ($checkhn)
                return [
                    'status' => 'error',
                    'message' => SDHtml::getMsgError() . Yii::t('app', 'HN duplicated!'),
                ];

            $update = Yii::$app->db->createCommand()
                    ->update('zdata_patientprofile', ['pt_hn' => $pt_hn_new], ['id' => $target]);

            $result = [];
            if ($update->execute()) {
                $result = [
                    'status' => 'success',
                    'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data complete.'),
                ];
            } else {
                $result = [
                    'status' => 'error',
                    'message' => SDHtml::getMsgError() . Yii::t('app', 'Error.'),
                ];
            }

            return $result;
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionSubmitVisit($ezf_id, $dataid, $target) {
        if (Yii::$app->request->post()) {
            $ezfVisit_tbname = \backend\modules\patient\Module::$formTableName['visit'];
            $cid = Yii::$app->request->get('cid');
            $visit_type = (isset(Yii::$app->request->post()['EZ' . $ezf_id]['visit_type']) ? Yii::$app->request->post()['EZ' . $ezf_id]['visit_type'] : Yii::$app->request->post('TbdataAll')['visit_type']);

            //delete order checkup
//            $dataOrder = PatientQuery::getOrderTranPt($dataid, '1');
//            if ($dataOrder) {
//                foreach ($dataOrder as $value) {
//                    OrderController::checkOrderCancelStatus($dataid, $value['order_tran_code']);
//                }
//            }

            $dept = $this->chkRightBypass($target, $cid, $visit_type);
            $result = $this->saveVisit($target, $dataid, $visit_type, $dept); //$target->ptid ,$dataid -> visitid

            Yii::$app->response->format = Response::FORMAT_JSON;
            return $result;
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionSubmitVisitCheckup() {
        if (Yii::$app->getRequest()->isAjax) {
            $ezf_id = \backend\modules\patient\Module::$formID['app_checkup'];
            $ezf_table = \backend\modules\patient\Module::$formTableName['app_checkup'];
            $ezfproject_patient_name_id = \backend\modules\patient\Module::$formID['project_patient_name'];
            $ezfproject_patient_name_tbname = \backend\modules\patient\Module::$formTableName['project_patient_name'];
            $ezfVisit_tbname = \backend\modules\patient\Module::$formTableName['visit'];
            $visit_id = Yii::$app->request->get('visit_id');
            $pt_id = Yii::$app->request->get('pt_id');
            $dataid = Yii::$app->request->get('dataid');
            $visit_type = '1'; //checkup

            $dataCheckup = EzfUiFunc::loadTbData($ezf_table, $dataid);
            $dataVisitChk = PatientFunc::loadTbDataByTarget($ezfVisit_tbname, $pt_id, date('Y-m-d'));
            if ($dataVisitChk) {
                if ($dataVisitChk['visit_type'] == '1' && $dataCheckup['appchk_status'] == '1') {
                    $result = [
                        'status' => 'error',
                        'message' => SDHtml::getMsgError() . 'พบข้อมูลส่งตรวจซ้ำกันหลายครั้ง',
                    ];

                    return $result;
                }
            }
            if ($dataCheckup) {
                $newData = '';
                if ($dataCheckup['appchk_right'] == '1') {
                    $zppn_id = PatientQuery::getprojectidByptid($pt_id);
                    if ($zppn_id) {
                        $zppndata['status_project'] = '1';
                        PatientFunc::saveDataNoSys($ezfproject_patient_name_id, $ezfproject_patient_name_tbname, $zppn_id['id'], $zppndata);
                    }

                    $newData = 'NEW';
                    $data['right_code'] = 'PRO';
                    $data['right_status'] = '2';
                    $data['right_project_id'] = $dataCheckup['appchk_project_id'];
                } elseif ($dataCheckup['appchk_right'] == '3') {
                    $dataHos = PatientQuery::getRightLast($pt_id);
                    if (empty($dataHos['right_prove_no'])) {
                        $dataProfile = \backend\modules\thaihis\classes\ThaiHisQuery::getPtProfile($pt_id);
                        $dataOnline = PatientFunc::getRightOnlineByNhso($dataProfile['pt_cid']); //ตรวจสอบจาก สปสช อีกครั้งหรือไมใม่
                        if ($dataOnline['subinscl'] == 'L1') {
                            $dataNhso = PatientQuery::getAuthnoLgo($dataProfile['pt_cid'], $dataProfile['pt_hn']); //ค้นหาในฐานข้อมูลว่าเคยสมัคร สปสช ยัง. ***** 
                            $newData = 'NEW';
                            $data['right_code'] = 'LGO';
                            $data['right_sub_code'] = 'L1';
                            if ($dataNhso) {
                                $data['right_prove_no'] = $dataNhso['authno']; //นำเลขที่ค้าหาเจอมาลง approve มาลง
                                $data['right_status'] = '2';
                            } else {
                                $data['right_status'] = '8';
                            }
                        } else {
                            $newData = 'NEW';
                            $data['right_status'] = '2';
                            $data['right_code'] = 'CASH';
                        }
                    } else {
                        $newData = '';
                        $data['right_code'] = 'LGO';
                    }
                } elseif ($dataCheckup['appchk_right'] == '4') {
                    $newData = 'NEW';
                    $data['right_code'] = 'ORI-G';
                    $data['right_status'] = '2';
                } elseif ($dataCheckup['appchk_right'] == '5') {
                    $newData = 'NEW';
                    $data['right_code'] = 'ORI';
                    $data['right_status'] = '2';
                } else {
                    $newData = 'NEW';
                    $data['right_status'] = '2';
                    $data['right_code'] = 'CASH';
                }
                if ($newData == 'NEW') {
                    $ezfPtRight_id = \backend\modules\patient\Module::$formID['patientright'];
                    EzfUiFunc::backgroundInsert($ezfPtRight_id, '', $pt_id, $data);
                }
                $dept = ''; //S047 opd checkup
                $resultVisit = $this->saveVisit($pt_id, $visit_id, $visit_type, $dept);

                if ($resultVisit) {
                    $ezf_table = \backend\modules\patient\Module::$formTableName['report_checkup'];
                    $dataReportCheckup = PatientFunc::loadTbDataByTarget($ezf_table, $resultVisit['data']['id']);
                    if (empty($dataReportCheckup)) {
                        $ezf_id = \backend\modules\patient\Module::$formID['report_checkup'];
                        EzfUiFunc::backgroundInsert($ezf_id, '', $resultVisit['data']['id'], ['ckr_status' => '1']);
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

                    $item['CG016^L'] = $dataCheckup['appchk_item_pep3']; //pep3
                    if ($dataCheckup['appchk_item_pep3'] || $dataCheckup['appchk_item_pep2']) {
                        $item['CG015^L'] = '1'; //pep3,pep2
                    } else {
                        $item['CG015^L'] = '0';
                    }

                    foreach ($item as $key => $value) {
                        $itemtype = explode('^', $key);

                        $checkOrder = OrderController::checkOrderCancelStatus($visit_id, $itemtype[0]);
                        if ($value == '1' && $checkOrder == TRUE) {
                            OrderController::saveOrderItem($visit_id, $itemtype[0], $itemtype[1], $order_status);
                        }
                    }

                    $result = $this->renderAjax('_submit_visit_checkup', [
                        'dataCheckup' => $dataCheckup,
                        'pt_id' => $pt_id,
                        'dept' => $dept,
                        'visit_id' => $visit_id
                    ]);
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

            return $result;
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    private function chkRightBypass($pt_id, $cid, $visit_type, $dept = null) {
        $ezfPtRight_id = \backend\modules\patient\Module::$formID['patientright'];
        $ezfPtRight_tbname = \backend\modules\patient\Module::$formTableName['patientright'];

        $dataHos = PatientQuery::getRightLast($pt_id);
        $dataOnline = PatientFunc::getRightOnlineByNhso($cid); //ตรวจสอบ web service สปสช

        $visit_dept = '';
        $newData = '';
        $data['right_flag'] = 'A';
        if ($dataOnline['maininscl'] == 'OFC') {
            if (in_array($dataOnline['subinscl'], ['E1', 'E2'])) {
                if ($dataHos['right_code'] !== 'ORI') { //รัฐวิสาหกิจ 
                    $newData = 'NEW';
                    $data['right_code'] = 'ORI'; //ต้นสังกัด
                    $data['right_sub_code'] = '';
                    $data['right_status'] = '2';
                }
            } elseif ($dataHos['right_code'] !== 'OFC') {
                $newData = 'NEW';
                $data['right_code'] = $dataOnline['maininscl'];
                $data['right_sub_code'] = '';
                $data['right_status'] = '2';
            }
        } elseif ($dataOnline['maininscl'] == 'LGO') {//L9 ไม่ระบุตำแหน่ง ต้องไปหาตรวจสอบสิทธิ
            if ($dataOnline['subinscl'] == 'L9') {
                $newData = 'NEW';
                $data['right_code'] = $dataOnline['maininscl'];
                $data['right_status'] = '8';
            } else {
                if (empty($dataHos['right_prove_no'])) {
                    $dataProfile = \backend\modules\thaihis\classes\ThaiHisQuery::getPtProfile($pt_id);
                    $dataNhso = PatientQuery::getAuthnoLgo($cid, $dataProfile['pt_hn']); //ค้นหาในฐานข้อมูลว่าเคยสมัคร สปสช ยัง. ***** 
                    $newData = 'NEW';
                    $data['right_code'] = $dataOnline['maininscl'];
                    $data['right_sub_code'] = $dataOnline['subinscl'];
                    if ($dataNhso) {
                        $data['right_prove_no'] = $dataNhso['authno']; //นำเลขที่ค้าหาเจอมาลง approve มาลง
                        $data['right_status'] = '2';
                    } else {
                        $data['right_status'] = '8';
                    }
                } else {
                    $newData = 'UPDATE';
                    $data['right_sub_code'] = $dataOnline['subinscl'];
                    $data['right_status'] = '2';
                }
            }
        } elseif ($dataOnline['maininscl'] == 'UCS' || $dataOnline['maininscl'] == 'SSS') {
            if ($dataHos && $visit_type == '2') {
                if (date($dataHos['right_refer_end']) < date('Y-m-d')) {
                    $newData = 'UPDATE';
                    $data['right_flag'] = 'C';
                    $data['right_status'] = '3';
                } elseif ($dataOnline['hmain'] !== $dataHos['right_hos_main']) {
                    $newData = 'UPDATE';
                    $data['right_flag'] = 'C';
                    $data['right_status'] = '4';
                }
            } elseif ($visit_type == '3') {
                $newData = 'NEW';
                $data['right_code'] = $dataOnline['maininscl'];
                //$data['right_sub_code'] = $dataOnline['subinscl'];
                $data['right_hos_main'] = $dataOnline['hmain'];

                if ($dataOnline['hmain'] == '12276') {
                    $data['right_status'] = '2';
                } else {
                    $data['right_status'] = '5';
                }
            } else {
                $newData = 'NEW';
                $data['right_status'] = '9';
                $data['right_code'] = 'CASH';
            }
        } else {
            $newData = 'NEW';
            $data['right_status'] = '2';
            $data['right_code'] = 'CASH';
        }

        switch ($visit_type) {
            case '1': //opd checkup
                $newData = 'NEW';
                $visit_dept = 'S047'; //047 opd checkup
                $data['right_code'] = 'PRO';
                $data['right_sub_code'] = '';
                $data['right_status'] = '2';
                $data['right_prove_no'] = '';
                $data['right_prove_end'] = '';

                break;
            case '2': //Appointment 
                $dataApp = PatientQuery::getAppointPt($pt_id, $dept, date('Y-m-d')); //check appoint
                if ($dataApp) {
                    $visit_dept = $dataApp['app_dept'];

                    $ezfApp_id = \backend\modules\patient\Module::$formID['appoint'];
                    $ezfApp_tbname = \backend\modules\patient\Module::$formTableName['appoint'];
                    PatientFunc::saveDataNoSys($ezfApp_id, $ezfApp_tbname, $dataApp['app_id'], ['app_status' => '2']);
                }
                break;
            case '3': //refer
                $visit_dept = 'S048';
                break;
            case '4': //ส่งตรวจสอบสิทธิก่อน                
                $visit_dept = $dept ? $dept : 'S074'; //ถ้า $dept ไม่มี่ค่าให้ส่ง opd ทั่วไป
                break;
            default:
                return FALSE;
        }

        if ($newData == 'NEW') {
            $rightID = EzfUiFunc::backgroundInsert($ezfPtRight_id, '', $pt_id, $data);
        } elseif ($newData == 'UPDATE' && $dataHos) {
            $rightID = PatientFunc::saveDataNoSys($ezfPtRight_id, $ezfPtRight_tbname, $dataHos['right_id'], $data);
        }

        return $visit_dept;
    }

    public function actionCounterVisit() {
        if (Yii::$app->getRequest()->isAjax) {
            $pt_id = Yii::$app->request->get('ptid');
            $cid = Yii::$app->request->get('cid');
            $dept = Yii::$app->request->get('dept');
            $appoint_id = Yii::$app->request->get('appointid');
            $visit_type = Yii::$app->request->get('visit_type');
            $visit_tran_new = Yii::$app->request->get('visit_tran_new');

            if ($appoint_id) {
                $visit_type = '2';
            } else {
                $dataApp = PatientQuery::getAppointPt($pt_id, $dept, date('Y-m-d')); //check appoint
                if ($dataApp) {
                    $visit_type = '2';
                }
            }

            if ($visit_tran_new == '1') {
                $ezfVisit_tbname = \backend\modules\patient\Module::$formTableName['visit'];
                $dataVisit = PatientFunc::loadTbDataByField($ezfVisit_tbname, ['ptid' => $pt_id,
                            'DATE(visit_date)' => ['visit_date' => date('Y-m-d')]]);
                $userProfile = Yii::$app->user->identity->profile->attributes;
                if ($userProfile['position'] == '2') {
                    $data['visit_tran_doctor'] = $userProfile['user_id'];
                    $data['visit_tran_doc_status'] = '1';
                }
                $data['visit_tran_dept'] = $dept;
                $data['visit_tran_status'] = '1';

                $result = self::saveVisitTran($pt_id, $dataVisit['id'], $data);
            } else {
                $this->chkRightBypass($pt_id, $cid, $visit_type, $dept);
                $result = $this->saveVisit($pt_id, '', $visit_type, $dept);
            }

            Yii::$app->response->format = Response::FORMAT_JSON;
            return $result;
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public static function saveVisit($pt_id, $visit_id, $visit_type, $dept, $ezf_id = '', $ezfVisitTran_id = '') {
        $dataEzf = \backend\modules\ezforms2\classes\EzfQuery::getEzformById($ezf_id);
        if ($dataEzf) {
            $ezf_id = $dataEzf['ezf_id'];
            $ezfVisit_tbname = $dataEzf['ezf_table'];
        } else {
            $ezf_id = \backend\modules\patient\Module::$formID['visit'];
            $ezfVisit_tbname = \backend\modules\patient\Module::$formTableName['visit'];
        }

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
                $result = EzfUiFunc::BackgroundInsert($ezf_id, '', $pt_id, $data);
            }

            $visit_id = $result['data']['id'];
        } else {
            $modelVisit = EzfUiFunc::loadTbData($ezfVisit_tbname, $visit_id);
            if ($modelVisit['visit_regis_type'] == '1') {
                $data['visit_regis_type'] = '2';
            }
            $result = PatientFunc::saveDataNoSys($ezf_id, $ezfVisit_tbname, $visit_id, $data);
        }
        $data = null;
        if ($dept) {
            if (Yii::$app->user->can('doctor')) {
                $data['visit_tran_doctor'] = Yii::$app->user->id;
                $data['visit_tran_doc_status'] = '1';
            }
            $data['visit_tran_dept'] = $dept;
            $data['visit_tran_status'] = '1';

            $result = self::saveVisitTran($pt_id, $visit_id, $data, $ezfVisitTran_id);
        }

        return $result;
    }

    public static function saveVisitTran($pt_id, $visit_id, $data, $ezfVisitTran_id = '') {

        $dataEzf = \backend\modules\ezforms2\classes\EzfQuery::getEzformById($ezfVisitTran_id);
        if ($dataEzf) {
            $ezfVisitTran_id = $dataEzf['ezf_id'];
            $ezfVisitTran_tbname = $dataEzf['ezf_table'];
        } else {
            $ezfVisitTran_id = \backend\modules\patient\Module::$formID['visit_tran'];
            $ezfVisitTran_tbname = \backend\modules\patient\Module::$formTableName['visit_tran'];
        }


        if ($data['visit_tran_dept']) {
            //chk visit_tran insert,update
            $dataVisitTran = PatientQuery::getVisitTran($pt_id, $data['visit_tran_dept'], date('Y-m-d'), '1');
            if ($dataVisitTran) {
                $result = PatientFunc::saveDataNoSys($ezfVisitTran_id, $ezfVisitTran_tbname, $dataVisitTran['visit_tran_id'], $data);
            } else {
                $result = EzfUiFunc::backgroundInsert($ezfVisitTran_id, '', $visit_id, $data);
            }
        } else {
            $result = [
                'status' => 'error',
                'message' => SDHtml::getMsgError() . Yii::t('app', 'Find not found.'),
            ];
        }
        return $result;
    }

    public function actionShow() {
        if (Yii::$app->getRequest()->isAjax) {
            $ezf_id = \backend\modules\patient\Module::$formID['profile'];
            $ezfPtRight_id = \backend\modules\patient\Module::$formID['patientright'];

            $dept = Yii::$app->user->identity->profile->attributes['department'];
            $dataid = Yii::$app->request->get('dataid'); //pt_id
            $target = Yii::$app->request->get('target'); //visit_id
            $reloadDiv = Yii::$app->request->get('reloadDiv');
            $IO = Yii::$app->request->get('IO');

            $dataProfile = \backend\modules\thaihis\classes\ThaiHisQuery::getPtProfile($dataid);
            $dataRight = PatientQuery::getRightLast($dataid);
            if ($IO == 'I') {
                $dataAdmit = PatientQuery::getAdmitCpoe($target);
            }

            return $this->renderAjax('_patient_profile_show', [
                        'ezf_id' => $ezf_id,
                        'target' => $target,
                        'IO' => $IO,
                        'dataProfile' => $dataProfile,
                        'reloadDiv' => $reloadDiv,
                        'ezfPtRight_id' => $ezfPtRight_id,
                        'dataRight' => $dataRight,
                        'dept' => $dept,
                        'dataAdmit' => $dataAdmit,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionCpoe() {
        if (Yii::$app->getRequest()->isAjax) {
            $ezf_id = \backend\modules\patient\Module::$formID['profile'];
            $ezfPtRight_id = \backend\modules\patient\Module::$formID['patientright'];
            $ezfPtHt_id = \backend\modules\patient\Module::$formID['profilehistory'];

            $pt_id = Yii::$app->request->get('ptid');
            $reloadDiv = Yii::$app->request->get('reloadDiv');
            $view = Yii::$app->request->get('view', '_patient_profile_cpoe');
            $btnDisabled = Yii::$app->request->get('btnDisabled');
            $user = Yii::$app->user->identity->profile->attributes;

            $dataProfile = \backend\modules\thaihis\classes\ThaiHisQuery::getPtProfile($pt_id);
            $dataRight = PatientQuery::getRightLast($pt_id);
            $dataVisit = PatientQuery::getVisitByDate($pt_id, date('Y-m-d'));
            $dataWarning = PatientQuery::getPtWarning($pt_id);

            return $this->renderAjax($view, [
                        'ezf_id' => $ezf_id,
                        'ezfPtRight_id' => $ezfPtRight_id,
                        'ezfPtHt_id' => $ezfPtHt_id,
                        'dataProfile' => $dataProfile,
                        'reloadDiv' => $reloadDiv,
                        'dataRight' => $dataRight,
                        'btnDisabled' => $btnDisabled,
                        'dataVisit' => $dataVisit,
                        'dataWarning' => $dataWarning,
                        'user' => $user,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionSearch($q = '') {
        if (Yii::$app->getRequest()->isAjax) {
            $sitecode = Yii::$app->user->identity->profile['sitecode'];
            $page = Yii::$app->request->get('page', 1);

            $dataProvider = PatientFunc::getPatientSearch($q, $sitecode);
            $dataProvider->pagination->page = $page - 1;
            $result['items'] = $dataProvider->getModels();
            $result['total_count'] = $dataProvider->getTotalCount();
            return \yii\helpers\Json::encode($result);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionUpdateappconf() {
        $dates = Yii::$app->request->get('dates');
        $datesold = Yii::$app->request->get('datesold');
        $targetid = Yii::$app->request->get('targetid');
        PatientQuery::updateZdata_app_conf($dates, $datesold);
        PatientQuery::updateVisitdateBytarget($targetid, $dates);
    }

    public function actionProfilePic() {
        if (Yii::$app->getRequest()->isAjax) {
            $pt_id = Yii::$app->request->get('ptid');
            $style = Yii::$app->request->get('style');
            $reloadDiv = Yii::$app->request->get('reloadDiv');

            $dataProfile = \backend\modules\thaihis\classes\ThaiHisQuery::getPtProfile($pt_id);
            $style = \backend\modules\ezforms2\classes\EzfFunc::stringDecode2Array($style);

            return $this->renderAjax('_patient_pic', [
                        'dataProfile' => $dataProfile,
                        'style' => $style,
                        'reloadDiv' => $reloadDiv,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionCheckVisit() {
//        $ezf_table = \backend\modules\patient\Module::$formTableName['visit'];
//
//        $model = new TbdataAll();
//        $model->setTableName($ezf_table);
        $pt_id = Yii::$app->request->get('pt_id', '');
        $date = Yii::$app->request->get('date', '0000-00-00');
//        $dataVisit = $model->find()->select('visit_app_id')->where(['visit_pt_id' => $pt_id,'DATE(visit_date)'=>new Expression("DATE('$date')")])->one();


        $ezf_table = \backend\modules\patient\Module::$formTableName['appoint'];
        $model = new TbdataAll();
        $model->setTableName($ezf_table);

        $model = $model->find()->select('id')->where(['app_pt_id' => $pt_id, 'app_date' => $date]);

        if (Yii::$app->user->can('doctor')) {
            $model->andWhere(['app_doctor' => Yii::$app->user->id]);
        } else {
//            $dataDept = EzfUiFunc::loadTbData('zdata_working_unit', Yii::$app->user->identity->profile->department);
//            $dataDept ? $model->andWhere(['app_dept' => $dataDept['unit_code']]) : null;
            $model->andWhere(['app_dept' => Yii::$app->user->identity->profile->department]);
        }
        $model->andWhere('app_status = 1 AND rstat NOT IN (0,3)');
        $result = $model->one();
        return isset($result['id']) ? $result['id'] : '';
    }

    public function actionAppointVisit() {
        if (Yii::$app->getRequest()->isAjax) {
            $ezf_id = \backend\modules\patient\Module::$formID['visit'];
            $ezf_table = \backend\modules\patient\Module::$formTableName['visit'];

            $ezf_id_app = \backend\modules\patient\Module::$formID['appoint'];
            $ezf_tbname_app = \backend\modules\patient\Module::$formTableName['appoint'];
            $status = Yii::$app->request->get('status');
            $pt_id = Yii::$app->request->get('pt_id');
            $date = Yii::$app->request->get('date');
            $app_id = Yii::$app->request->get('app_id');
            $options = Yii::$app->request->get('options', '');
            $visitid = Yii::$app->request->get('visitid', '');

            $date != '' ? $date = \appxq\sdii\utils\SDdate::phpThDate2mysqlDate($date, '-') : null;

            $model = new TbdataAll();
            $model->setTableName($ezf_table);
            $result = $model->find()->where(['visit_pt_id' => $pt_id, 'DATE(visit_date)' => new Expression("DATE('$date')")])->andWhere('rstat NOT IN (0,3)')->one();
//            VarDumper::dump($result);
            if ($date) {
                if (empty($result) && $status == 'save') {
                    $initdata['visit_date'] = $date;
                    $initdata['visit_status'] = '0';
                    $initdata['visit_type'] = '4';
                    $initdata['visit_app_id'] = $app_id;
                    if ($result) {
                        //update visit
                        $dataSave = PatientFunc::saveDataNoSys($ezf_id, $ezf_table, $result['id'], $initdata);
                        $result = isset($dataSave) ? $dataSave['data'] : '';
                        if ($dataSave['status'] == 'success') {
                            Yii::$app->db->createCommand()->update($ezf_tbname_app, ['app_visit_id' => $result['id']], ['id' => $app_id])->execute();
                        }
                    } else {
                        //insert visit
                        $dataSave = EzfUiFunc::BackgroundInsert($ezf_id, '', $pt_id, $initdata);
                        $result = isset($dataSave) ? $dataSave['data'] : '';
                        if ($dataSave['status'] == 'success') {
                            Yii::$app->db->createCommand()->update($ezf_tbname_app, ['app_visit_id' => $result['id']], ['id' => $app_id])->execute();
                        }
                    }
                } elseif ($result) {
                    if ($visitid == '') {
                        Yii::$app->db->createCommand()->update($ezf_tbname_app, ['app_visit_id' => $result['id']], ['id' => $app_id])->execute();
                    }
                }
            }

            return $this->renderAjax('_app_visit', [
                        'result' => $result,
                        'pt_id' => $pt_id,
                        'date' => $date,
                        'status' => $status,
                        'visitid' => $visitid,
                        'options' => $options
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionAppointSaveVisitDate() {
        if (Yii::$app->getRequest()->isAjax) {
            $dataid = Yii::$app->request->get('dataid');
            $ezf_table = \backend\modules\patient\Module::$formTableName['appoint'];
            $ezfVisit_id = \backend\modules\patient\Module::$formID['visit'];
            $ezfVisit_table = \backend\modules\patient\Module::$formTableName['visit'];

            $dataApp = EzfUiFunc::loadTbData($ezf_table, $dataid);
            if ($dataApp) {
                $dataVisit = PatientFunc::loadTbDataByField($ezfVisit_table, ['visit_app_id' => $dataid]);
                if ($dataVisit) {
                    $initdata['visit_date'] = $dataApp['app_date'];
                    $initdata['visit_status'] = '1';
                    $dataVisit = PatientFunc::saveDataNoSys($ezfVisit_id, $ezfVisit_table, $dataVisit['id'], $initdata)['data'];
                }
            }
            return $this->renderAjax('_app_print', [
                        'dataVisit' => $dataVisit,
                        'dataApp' => $dataApp,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

}
