<?php

namespace backend\modules\patient\controllers;

use Yii;
use backend\modules\patient\classes\PatientFunc;
use backend\modules\patient\classes\PatientQuery;
use backend\modules\ezforms2\classes\EzfUiFunc;
use yii\web\Response;

class AdmitController extends \yii\web\Controller {

    public function actionIndex() {
        return $this->render('index');
    }

    public function actionWard() {
        $ezfAdmit_id = \backend\modules\patient\Module::$formID['admit'];
        $ezfBed_id = \backend\modules\patient\Module::$formID['ward_bed'];
        $unit_id = Yii::$app->user->identity->profile->attributes['department'];
        Yii::$app->session['ezf_input'] = \backend\modules\ezforms2\classes\EzfQuery::getInputv2All();
        //$dept = PatientQuery::getDepartmentOne($sect_code);
        $dept = PatientQuery::getWorkingUnit($unit_id);

        return $this->render('ward', [
                    'ezfAdmit_id' => $ezfAdmit_id,
                    'ezfBed_id' => $ezfBed_id,
                    'dept' => $dept,
        ]);
    }

    public function actionWardDash() {
        $ezf_id = Yii::$app->request->get('ezf_id');
        $reloadDiv = Yii::$app->request->get('reloadDiv');
        $dept = Yii::$app->request->get('dept');
        $tab = Yii::$app->request->get('tab');
        $module = Yii::$app->request->get('module');

        $count_admit = PatientQuery::getAdmitDashboard($dept);

        return $this->renderAjax('_warddash', [
                    'ezf_id' => $ezf_id,
                    'reloadDiv' => $reloadDiv,
                    'dept' => $dept,
                    'count_admit' => $count_admit,
                    'tab' => $tab,
                    'module' => $module,
        ]);
    }

    public function actionWardBed() {
        if (Yii::$app->getRequest()->isAjax) {
            $ezf_id = Yii::$app->request->get('ezf_id');
            $reloadDiv = Yii::$app->request->get('reloadDiv');
            $dept = Yii::$app->request->get('dept');
            $tab = Yii::$app->request->get('tab');
            $module = Yii::$app->request->get('module');
//            \appxq\sdii\utils\VarDumper::dump(Yii::$app->user->identity->profile->attributes);

            $searchModel = PatientFunc::getModel($ezf_id, '');
            $dataProvider = PatientFunc::getWardBed($searchModel, Yii::$app->request->post(), $dept);

            return $this->renderAjax('_wardbed', [
                        'ezf_id' => $ezf_id,
                        'reloadDiv' => $reloadDiv,
                        'searchModel' => $searchModel,
                        'dataProvider' => $dataProvider,
                        'dept' => $dept,
                        'tab' => $tab,
                        'module' => $module,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionAdmitPt() {
        $reloadDiv = Yii::$app->request->get('reloadDiv');
        $pt_id = Yii::$app->request->get('pt_id');
        $visit_id = Yii::$app->request->get('visit_id');
        $admit_id = Yii::$app->request->get('admit_id');
        $bed_id = Yii::$app->request->get('bed_id');
        $pt_hn = Yii::$app->request->get('pt_hn');

        return $this->renderAjax('admit', [
                    'reloadDiv' => $reloadDiv,
                    'pt_id' => $pt_id,
                    'visit_id' => $visit_id,
                    'admit_id' => $admit_id,
                    'bed_id' => $bed_id,
                    'pt_hn' => $pt_hn,
        ]);
    }

    public function actionWardPtadmit() {
        if (Yii::$app->getRequest()->isAjax) {
            $reloadDiv = Yii::$app->request->get('reloadDiv');
            $admit_status = Yii::$app->request->get('admit_status');
            $bed_status = Yii::$app->request->get('bed_status');
            $dept = Yii::$app->request->get('dept');
            $bed_id = Yii::$app->request->get('bed_id');
            $bed_type = Yii::$app->request->get('bed_type', '');
            $mode = Yii::$app->request->get('mode', 0);
            $tab = Yii::$app->request->get('tab');
            $module = Yii::$app->request->get('module');

            $admit_status = \backend\modules\ezforms2\classes\EzfFunc::stringDecode2Array($admit_status);
            $admit_status = implode(',', $admit_status);
            $data = PatientQuery::getPtAdmit($dept, $bed_status, $admit_status, $bed_type);

            return $this->renderAjax('_ptadmit', [
                        'reloadDiv' => $reloadDiv,
                        'admit_status' => $admit_status,
                        'data' => $data,
                        'dept' => $dept,
                        'bed_id' => $bed_id,
                        'bed_type' => $bed_type,
                        'mode' => $mode,
                        'tab' => $tab,
                        'module' => $module,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionWardPtadmitSelect() {
        if (Yii::$app->getRequest()->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $ezfBed_id = \backend\modules\patient\Module::$formID['bed_tran'];
            $ezfAdmit_id = \backend\modules\patient\Module::$formID['admit'];
            $dataid = Yii::$app->request->get('dataid');
            $target = Yii::$app->request->get('target'); //admit_id
            $bed_id = Yii::$app->request->get('bed_id');
            //update status old room --> out
            $bed_tran_id = PatientQuery::getBedTranID($target, '2');
            if ($bed_tran_id) {
                $data['bed_tran_status'] = '3';
                PatientFunc::saveDataNoSys($ezfBed_id, 'zdata_bed_tran', $bed_tran_id['bed_tran_id'], $data);
            } else {
                //update status admit in admit
                $data['admit_status'] = '2';
                $dataAdmit = PatientFunc::saveDataNoSys($ezfAdmit_id, 'zdata_admit', $target, $data)['data'];

                //clone ค่าใช้จ่ายจาก OPD ลง IPD receive tran     
                $this->orderToIpdReceive($dataAdmit['admit_visit_id'], $dataAdmit['id']);
            }

            $data = null;
            //update room in bed_tran
            $data['bed_tran_bed_id'] = $bed_id;
            $data['bed_tran_status'] = '2';
            return PatientFunc::saveDataNoSys($ezfBed_id, 'zdata_bed_tran', $dataid, $data);
            $data = null;
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    private function orderToIpdReceive($visit_id, $admit_id) {
        $dataOrderOpd = \backend\modules\patient\classes\CashierQuery::getCashierGroupItem($visit_id, '1', '', '');
        if ($dataOrderOpd) {
            $ezfIpdReceive_id = \backend\modules\patient\Module::$formID['ipd_receive_tran'];
            $ezfOrder_id = \backend\modules\patient\Module::$formID['order_tran'];
            $ezfOrder_table = \backend\modules\patient\Module::$formTableName['order_tran'];
            $ezfPisTran_id = \backend\modules\patient\Module::$formID['pis_order_tran'];
            $ezfPisTran_table = \backend\modules\patient\Module::$formTableName['pis_order_tran'];

            $params = ['visitid' => $visit_id, 'cashier_status' => '1', 'receipt_id' => ''];
            foreach ($dataOrderOpd as $value) {
                //แปลงสิทธิ เงินสดให้เข้า Pay
//                if ($dataOrderOpd['right_code'] == 'CASH') {
//                    $value['pay'] = $value['pay'];
//                    $value['notpay'] = $value['notpay'];
//                }
                $initdata = [
                    'ipd_receive_fin_code' => $value['fin_group_id'],
                    'ipd_receive_pay' => $value['pay'],
                    'ipd_receive_not_pay' => $value['notpay'],
                ];

                $dataIpdReceive = PatientFunc::backgroundInsert($ezfIpdReceive_id, '', $admit_id, $initdata)['data'];
                if ($dataIpdReceive) {
                    if (in_array($value['fin_group_code'], ['3', '5'])) {
                        $dataPis = \backend\modules\pis\classes\PisQuery::getOrderByvisitId($visit_id);
                        foreach ($dataPis as $pisValue) {
                            PatientFunc::saveDataNoSys($ezfPisTran_id, $ezfPisTran_table, $pisValue['id'], [
                                'order_tran_cashier_status' => '3',
                                'order_tran_cashier_id' => $dataIpdReceive['id'],
                                'order_tran_oi_type' => 'IPD'
                            ]);
                        }
                    } else {
                        $data_item = \backend\modules\patient\classes\CashierQuery::getCashierItem($value['order_fin_code'], $params, '', '');
                        foreach ($data_item as $value) {
                            PatientFunc::saveDataNoSys($ezfOrder_id, $ezfOrder_table, $value['item_id'], [
                                'order_tran_cashier_status' => '3',
                                'order_tran_cashier_id' => $dataIpdReceive['id'],
                                'order_tran_oi_type' => 'IPD'
                            ]);
                        }
                    }
                }
            }
        }
    }

    public static function updateIpdReceiveTran($order_tran_id, $mode) {
        $ezf_id = \backend\modules\patient\Module::$formID['ipd_receive_tran'];
        $ezf_table = \backend\modules\patient\Module::$formTableName['ipd_receive_tran'];
        //query fin_group_code
        $dataOrder = PatientQuery::getAdmitFinGroup($order_tran_id);
        //find data fin_group_code 
        $dataReceive = PatientFunc::loadTbDataByField($ezf_table, [
                    'ipd_receive_admit_id' => $dataOrder['admit_id'],
                    'ipd_receive_fin_code' => $dataOrder['fin_group_id'],
        ]);
        $pay = 0;
        $notpay = 0;
        if ($dataReceive) {
            if ($mode == "ADD") {
                $pay = $dataReceive['ipd_receive_pay'] + $dataOrder['order_tran_pay'];
                $notpay = $dataReceive['ipd_receive_not_pay'] + $dataOrder['order_tran_notpay'];
            } else {
                //$mode cancel ยกเลิกรับ order
                $pay = $dataReceive['ipd_receive_pay'] - $dataOrder['order_tran_pay'];
                $notpay = $dataReceive['ipd_receive_not_pay'] - $dataOrder['order_tran_notpay'];
            }

            $initdata = [
                'ipd_receive_pay' => $pay,
                'ipd_receive_not_pay' => $notpay,
            ];

            $dataIpdReceive = $dataReceive['id'];
            PatientFunc::saveDataNoSys($ezf_id, $ezf_table, $dataReceive['id'], $initdata);
        } elseif ($mode == 'ADD') {
            $initdata = [
                'ipd_receive_fin_code' => $dataOrder['fin_group_id'],
                'ipd_receive_pay' => $dataOrder['order_tran_pay'],
                'ipd_receive_not_pay' => $dataOrder['order_tran_notpay'],
            ];

            $dataIpdReceive = PatientFunc::backgroundInsert($ezf_id, '', $dataOrder['admit_id'], $initdata)['data'];
        }

        return $dataIpdReceive['id'];
    }

    public function actionBedTran() {
        if (Yii::$app->getRequest()->isAjax) {
            $ezf_id = \backend\modules\patient\Module::$formID['bed_tran'];
            $target = Yii::$app->request->get('target');
            $reloadDiv = Yii::$app->request->get('reloadDiv');
            $visit_id = Yii::$app->request->get('visit_id');

            $modelBedtran = PatientQuery::getBedTran($target);

            if (isset($visit_id) && $modelBedtran) {
                if ($modelBedtran[0]['visit_id'] <> $visit_id) {
                    $data['rstat'] = '3';
                    PatientFunc::saveDataNoSys($ezf_id, 'zdata_bed_tran', $modelBedtran[0]['id'], $data);
                    $modelBedtran = [];
                }
            }

            return $this->renderAjax('_bedtran', [
                        'ezf_id' => $ezf_id,
                        'modelBedtran' => $modelBedtran,
                        'target' => $target,
                        'visit_id' => $visit_id,
                        'reloadDiv' => $reloadDiv,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionAdmitBtnAdt() {
        if (Yii::$app->getRequest()->isAjax) {
            $ezfVisit_id = \backend\modules\patient\Module::$formID['visit'];
            $ezfVisit_tbname = \backend\modules\patient\Module::$formTableName['visit'];
            $ezfAdmit_id = \backend\modules\patient\Module::$formID['admit'];
            $ezfDisch_id = \backend\modules\patient\Module::$formID['discharge'];
            $ezfBedTran_id = \backend\modules\patient\Module::$formID['bed_tran'];
            $ezfProfile_id = \backend\modules\patient\Module::$formID['profile'];

            $admit_id = Yii::$app->request->get('admit_id');
            $visit_id = Yii::$app->request->get('visit_id');
            $reloadDiv = Yii::$app->request->get('reloadDiv');
            $IO = Yii::$app->request->get('IO');
            $bedtran_id = Yii::$app->request->get('bedtran_id');

            $modelVisit = EzfUiFunc::loadTbData($ezfVisit_tbname, $visit_id);
            //$dataAdmit = PatientQuery::getAdmitBtnAdt($visit_id);
            $dataAdmit = PatientQuery::getAdmitCpoe($visit_id);

            if (isset($dataAdmit['admit_id'])) {
                if (empty($modelVisit['visit_admit_an'])) {
                    $data['visit_admit_an'] = $dataAdmit['admit_an'];
                    PatientFunc::saveDataNoSys($ezfVisit_id, 'zdata_visit', $modelVisit['id'], $data);
                    $data = null;

                    $data['pt_status'] = 'ADMIT';
                    PatientFunc::saveDataNoSys($ezfProfile_id, 'zdata_patientprofile', $modelVisit['visit_pt_id'], $data);
                    $data = null;
                } elseif ($dataAdmit['admit_status'] == '3') {
                    //Update admit stataus 3=pre-dis => 4=discharge                   
                    $data['admit_status'] = '4';
//                    PatientFunc::saveDataNoSys($ezfAdmit_id, 'zdata_admit', $dataAdmit['admit_id'], $data);
                }
            }

            return $this->renderAjax('_btnADT', [
                        'ezfAdmit_id' => $ezfAdmit_id,
                        'ezfBedTran_id' => $ezfBedTran_id,
                        'ezfDisch_id' => $ezfDisch_id,
                        'modelVisit' => $modelVisit,
                        'dataAdmit' => $dataAdmit,
                        'admit_id' => $admit_id,
                        'visit_id' => $visit_id,
                        'reloadDiv' => $reloadDiv,
                        'IO' => $IO,
                        'bedtran_id' => $bedtran_id
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionAdmitBtnadtSubmit() {
        if (Yii::$app->getRequest()->isAjax) {
            $ezfVisit_id = \backend\modules\patient\Module::$formID['visit'];
            $ezfVisit_tbname = \backend\modules\patient\Module::$formTableName['visit'];
            $ezfAdmit_id = \backend\modules\patient\Module::$formID['admit'];
            $ezfAdmit_tbname = \backend\modules\patient\Module::$formTableName['admit'];
            $dataid = Yii::$app->request->get('dataid');
            $target = Yii::$app->request->get('target');
            $action = Yii::$app->request->get('action');

            $data = [];
            if ($action == 'cancel') {
                $data['visit_admit_an'] = null;
                PatientFunc::saveDataNoSys($ezfVisit_id, $ezfVisit_tbname, $target, $data);

                $data = null;
                $data['admit_status'] = '5';
                $data['rstat'] = '3';
            } elseif ($action == 'predis') {
                $data['admit_status'] = '3';
            } elseif ($action == 'cancelpredis') {
                $data['admit_status'] = '2';
            }

            Yii::$app->response->format = Response::FORMAT_JSON;
            return PatientFunc::saveDataNoSys($ezfAdmit_id, $ezfAdmit_tbname, $dataid, $data);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionAdmitCpoe() {
        if (Yii::$app->getRequest()->isAjax) {
            $ezf_id = \backend\modules\patient\Module::$formID['admit'];
            $ezfRight_id = \backend\modules\patient\Module::$formID['patientright'];

            $pt_id = Yii::$app->request->get('pt_id');
            $visit_id = Yii::$app->request->get('visit_id');
            $reloadDiv = Yii::$app->request->get('reloadDiv');
            $userProfile = Yii::$app->user->identity->profile->attributes;
            $dataAdmit = PatientQuery::getAdmitCpoe($visit_id);
            $dataRight = PatientQuery::getRightLast($pt_id);

            return $this->renderAjax('_admit_cpoe', [
                        'ezf_id' => $ezf_id,
                        'ezfRight_id' => $ezfRight_id,
                        'pt_id' => $pt_id,
                        'visit_id' => $visit_id,
                        'dataAdmit' => $dataAdmit,
                        'dataRight' => $dataRight,
                        'reloadDiv' => $reloadDiv,
                        'userProfile' => $userProfile,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionDischargeCpoe() {
        if (Yii::$app->getRequest()->isAjax) {
            $ezf_id = \backend\modules\patient\Module::$formID['discharge'];
            $ezfComo_id = \backend\modules\patient\Module::$formID['diag_como'];
            $ezfComp_id = \backend\modules\patient\Module::$formID['diag_comp'];
            $ezfOperat_id = \backend\modules\patient\Module::$formID['operat'];
            $ezf_tbname = \backend\modules\patient\Module::$formTableName['discharge'];

            $pt_id = Yii::$app->request->get('pt_id');
            $visit_id = Yii::$app->request->get('visit_id');
            $view = Yii::$app->request->get('view');
            $reloadDiv = Yii::$app->request->get('reloadDiv');

            $model = PatientQuery::getDischargeCpoe($visit_id);
            $dataDiagComo = '';
            $dataDiagComp = '';
            $dataOperat = '';
            if ($model['discharge_id']) {
                $dataDiagComo = PatientQuery::getDiagComo($visit_id);
                $dataDiagComp = PatientQuery::getDiagComp($visit_id);
                $dataOperat = PatientQuery::getOperat($visit_id);
            }

            if ($model['admit_status'] == '2') {
                $model['admit_status'] = '3';
                $data['admit_status'] = '3';
                PatientFunc::saveDataNoSys($ezf_id, $ezf_tbname, $model['admit_id'], $data);
            }

            return $this->renderAjax('_discharge_' . $view, [
                        'ezf_id' => $ezf_id,
                        'ezfComp_id' => $ezfComp_id,
                        'ezfComo_id' => $ezfComo_id,
                        'ezfOperat_id' => $ezfOperat_id,
                        'pt_id' => $pt_id,
                        'visit_id' => $visit_id,
                        'model' => $model,
                        'dataDiagComo' => $dataDiagComo,
                        'dataDiagComp' => $dataDiagComp,
                        'dataOperat' => $dataOperat,
                        'reloadDiv' => $reloadDiv,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionTransfer() {
        if (Yii::$app->getRequest()->isAjax) {
            $ezf_id = \backend\modules\patient\Module::$formID['tranfer'];
            $ezf_table = \backend\modules\patient\Module::$formTableName['tranfer'];

            $pt_id = Yii::$app->request->get('pt_id');
            $visit_id = Yii::$app->request->get('visit_id');
            $view = Yii::$app->request->get('view');
            $reloadDiv = Yii::$app->request->get('reloadDiv');

            $model = PatientFunc::loadTbDataByTarget($ezf_table, $visit_id);

            return $this->renderAjax('_transfer_' . $view, [
                        'ezf_id' => $ezf_id,
                        'pt_id' => $pt_id,
                        'visit_id' => $visit_id,
                        'reloadDiv' => $reloadDiv,
                        'model' => $model,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionDashboard() {
        Yii::$app->session['ezf_input'] = \backend\modules\ezforms2\classes\EzfQuery::getInputv2All();
        $ezf_id = \backend\modules\patient\Module::$formID['admit'];

        $searchModel = PatientFunc::getModel($ezf_id, '');
        $searchModel['admit_status'] = '2';
        $dataProvider = PatientFunc::getAdmitDashboard($searchModel, Yii::$app->request->get());

        return $this->render('dashboard', [
                    'ezf_id' => $ezf_id,
                    'dataProvider' => $dataProvider,
                    'searchModel' => $searchModel
        ]);
    }

    public function actionPrintDischarge() {
        $visit_id = Yii::$app->request->get('visit_id');

        return $this->renderAjax('_discharge_print', [
                    'visit_id' => $visit_id,
        ]);
    }

    public function actionNurseNote() {
        if (Yii::$app->getRequest()->isAjax) {
            $ezf_id = \backend\modules\patient\Module::$formID['tranfer'];
            $ezf_table = \backend\modules\patient\Module::$formTableName['tranfer'];

            $admit_id = Yii::$app->request->get('admit_id');
            $visit_id = Yii::$app->request->get('visit_id');
            $reloadDiv = Yii::$app->request->get('reloadDiv');

            $model = PatientFunc::loadTbDataByTarget($ezf_table, $visit_id);

            return $this->renderAjax('_nurse_notemain', [
                        'ezf_id' => $ezf_id,
                        'admit_id' => $admit_id,
                        'visit_id' => $visit_id,
                        'reloadDiv' => $reloadDiv,
                        'model' => $model,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionDrugShow($visit_id, $admit_id, $reloadDiv) {

        $url = Yii::$app->params['km4Url'] . "/api/core-api/cpoe-detail?pt_hn=45101688&visit_date=2018-05-08";

        $body = \backend\modules\patient\classes\PatientFunc::callWebService($url);
        $body = json_decode($body, TRUE);

        $date_start = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . "-4 days"));
        if (empty($body['status'])) {
            foreach ($body as $valueBody) {
                foreach ($valueBody as $key => $value) {
                    $value['use_time'] = $this->randomDate(date('Y-m-d H:i:s'), $date_start);
                    $arrData[] = $value;
                }
            }
        } else {
            $arrData = [];
        }

        $dataProvider = \backend\modules\patient\classes\PatientFunc::ArrayToDataProvider($arrData);
//        \appxq\sdii\utils\VarDumper::dump($arrData);
        return $this->renderAjax('@backend/modules/patient/views/restful/_km4_order_show_ipd', [
                    'dataProvider' => $dataProvider,
                    'reloadDiv' => $reloadDiv,
        ]);
    }

    private function randomDate($start_date, $end_date) {
        // Convert to timetamps
        $min = strtotime($start_date);
        $max = strtotime($end_date);

        // Generate random number using above bounds
        $val = rand($min, $max);

        // Convert back to desired date format
        return date('Y-m-d H:i:s', $val);
    }

    public function actionCheckAdmit() {
        if (Yii::$app->getRequest()->isAjax) {
            $ptid = Yii::$app->request->get('ptid');
            $data = PatientQuery::checkAdmit($ptid);

            Yii::$app->response->format = Response::FORMAT_JSON;
            if (!empty($data)) {
                return [
                    'warning' => TRUE,
                ];
            } else {
                return [
                    'warning' => FALSE
                ];
            }
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

}
