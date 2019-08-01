<?php

namespace backend\modules\thaihis\controllers;

use Yii;
use backend\modules\thaihis\classes\OrderFunc;
use backend\modules\patient\classes\PatientQuery;
use backend\modules\thaihis\classes\OrderQuery;
use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\ezforms2\classes\EzfUiFunc;
use backend\modules\ezforms2\classes\EzfFunc;
use backend\modules\patient\classes\PatientFunc;
use backend\modules\thaihis\classes\ThaiHisQuery;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use appxq\sdii\utils\SDdate;

class OrderController extends \yii\web\Controller {

    public function actionIndex() {
        return $this->renderAjax('index');
    }

    public function actionOrderpopup() {
        if (Yii::$app->getRequest()->isAjax) {
            $options = EzfFunc::stringDecode2Array(Yii::$app->request->get('options'));
            $btnDisabled = 0; //fix enable เมื่อสามารถเปิด popup ได้
            $pt_id = Yii::$app->request->get('target');
            $visit_id = Yii::$app->request->get('visitid');
            $visit_type = Yii::$app->request->get('visit_type');
            $userProfile = Yii::$app->user->identity->profile->attributes;

            $ezf_order = EzfQuery::getEzformOne($options['order_ezf_id']);
            $order_header = PatientFunc::loadTbDataByField($ezf_order['ezf_table'], ['order_visit_id' => $visit_id, 'order_dept' => $userProfile['department']]);
            if (empty($order_header)) {
                $order_header = PatientFunc::backgroundInsert($ezf_order['ezf_id'], '', $visit_id, ['order_dept' => $userProfile['department']]);
                $order_header = $order_header['data'];
            }

            //$visit = \backend\modules\patient\Module::$formID['visit'];
            $visit_tb = \backend\modules\patient\Module::$formTableName['visit'];
            $modelVisit = EzfUiFunc::loadTbData($visit_tb, $visit_id);

            $vsType = isset($options['oipd_type']) ? $options['oipd_type'] : 'OPD';
            $visit_date = date('Y-m-d');
            if ($modelVisit && $vsType == 'OPD') {
                $visit_date = $modelVisit['visit_date'];
            }

            $searchModel = PatientFunc::getModel($options['orderlists_ezf_id'], '');

            return $this->renderAjax('_orderpopup', [
                        'ezf_id' => $options['orderlists_ezf_id'],
                        'pt_id' => $pt_id,
                        'visit_id' => $visit_id,
                        'visit_date' => $visit_date,
                        'visit_type' => $visit_type,
                        'searchModel' => $searchModel,
                        'options' => $options,
                        'btnDisabled' => $btnDisabled,
                        'order_header_id' => $order_header['id']
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionOrderSearch() {
        if (Yii::$app->getRequest()->isAjax) {
            $options = EzfFunc::stringDecode2Array(Yii::$app->request->get('options'));
            $visit_id = Yii::$app->request->get('visitid');
            $reloadDiv = Yii::$app->request->get('reloadDiv');
            //$userProfile = Yii::$app->user->identity->profile;
            $params = Yii::$app->request->post('EZ' . $options['orderlists_ezf_id'], [
                'group_type' => '',
                'order_name' => ''
            ]);

            $dataProvider = OrderFunc::getOrderSearch($options['orderlists_fields'], $options['filter_orderlists_fields'], $params, $options['filter_ordertype_default']);

            return $this->renderAjax('_order_list', [
                        'visit_id' => $visit_id,
                        'reloadDiv' => $reloadDiv,
                        'colShow' => $options['orderlists_fields'],
                        'dataProvider' => $dataProvider,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionOrderSelect($item, $grouptype) {
        if (Yii::$app->getRequest()->isAjax) {
            $options = EzfFunc::stringDecode2Array(Yii::$app->request->get('options'));
            $options['oipd_type'] = isset($options['oipd_type']) ? $options['oipd_type'] : 'OPD';
            $order_header_id = Yii::$app->request->get('order_header_id');
            $visit_type = Yii::$app->request->get('visit_type');
            $visit_date = Yii::$app->request->get('visit_date');
            $pt_id = Yii::$app->request->get('pt_id');

            $pt_right = ThaiHisQuery::getPtRightLast($pt_id);
            $pt_right['pt_bdate'] = 0; //SDdate::getAge(str_replace("-", "", "1989-01-05"));

            $result = $this->saveOrderItem($order_header_id, $item, $grouptype, $visit_type . '^' . $pt_right['right_code'] . '^' . $pt_right['pt_bdate'], $options['oipd_type'], $visit_date);
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $result;
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public static function saveOrderItem($orderheader_id, $item, $grouptype, $right_order, $oipd_type = 'OPD', $visit_date = '') {
        $ezf_ordertran_id = \backend\modules\patient\Module::$formID['order_tran'];
        $ezf_orderlists_table = \backend\modules\patient\Module::$formTableName['order_lists'];
        $ezf_orderpacker_table = \backend\modules\patient\Module::$formTableName['order_package'];

        if ($visit_date == '') {
            $visit_date = date('Y-m-d');
        }

        if ($grouptype == 'Package') {
            $arrItemCode = PatientFunc::loadTbDataByField($ezf_orderpacker_table, ['order_code' => $item]);
            if ($arrItemCode) {
                $arrItemCode = \appxq\sdii\utils\SDUtility::string2Array($arrItemCode['order_refs']);
                foreach ($arrItemCode as $value) {
                    $dataItem = PatientFunc::loadTbDataByField($ezf_orderlists_table, ['order_code' => $value]);
                    if ($dataItem) {
                        $initdata = self::flagRightPrice($right_order, $dataItem, '1', $oipd_type, $visit_date);
                        $result = PatientFunc::backgroundInsert($ezf_ordertran_id, '', $orderheader_id, $initdata);
                    } else {
                        throw new NotFoundHttpException("Find item not fold");
                    }
                }
            } else {
                throw new NotFoundHttpException("Package not set item");
            }
        } else {
            $dataItem = PatientFunc::loadTbDataByField($ezf_orderlists_table, ['order_code' => $item]);
            if ($dataItem) {
                $initdata = self::flagRightPrice($right_order, $dataItem, '1', $oipd_type, $visit_date);
                $result = PatientFunc::backgroundInsert($ezf_ordertran_id, '', $orderheader_id, $initdata);
            } else {
                throw new NotFoundHttpException("Find item not fold");
            }
        }

        return $result;
    }

    public function actionGridOrderChange($dataid, $qty) {
        if (Yii::$app->getRequest()->isAjax) {
            $options = EzfFunc::stringDecode2Array(Yii::$app->request->get('options'));
            $ezf_ordertran_table = EzfQuery::getEzformOne($options['ordertran_ezf_id'])['ezf_table'];
            $ezf_orderlists_table = EzfQuery::getEzformOne($options['orderlists_ezf_id'])['ezf_table'];
            $model = EzfUiFunc::loadTbData($ezf_ordertran_table, $dataid);
            if ($model) {
                $dataItem = PatientFunc::loadTbDataByField($ezf_orderlists_table, ['order_code' => $model['order_tran_code']]);
                $initdata['order_qty'] = $qty;
                if ($model['order_tran_notpay'] == '0') {
                    $initdata['order_tran_pay'] = $qty * $dataItem['unit_price'];
                } else {
                    $initdata['order_tran_notpay'] = $qty * $dataItem['unit_price'];
                }

                $result = PatientFunc::saveDataNoSys($options['ordertran_ezf_id'], $ezf_ordertran_table, $dataid, $initdata);
            } else {
                $result = [
                    'status' => 'error',
                    'message' => \appxq\sdii\helpers\SDHtml::getMsgError() . Yii::t('patient', 'The transaction has been completed. Can not cancel'),
                ];
            }
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $result;
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    private static function flagRightPrice($right_order, $dataItem, $qty, $oipd_type, $visit_date) {
        $arrRight = explode("^", $right_order);
        $initdata = [];
        $userProfile = Yii::$app->user->identity->profile->attributes;
        $initdata['order_qty'] = $qty;
        if ($arrRight[0] == '1') { //viittype CheckUp
            $dataItem['unit_price'] = $qty * $dataItem['unit_price_checkup'];
            if ($dataItem['flag_pay'] == '2') {
                $initdata['order_tran_notpay'] = 0;
                $initdata['order_tran_pay'] = $dataItem['unit_price'];
            } elseif ($dataItem['flag_pay'] == '3' && $arrRight[2] < 35) {
                $initdata['order_tran_notpay'] = 0;
                $initdata['order_tran_pay'] = $dataItem['unit_price'];
            } else {
                if ($dataItem['flag_pay'] == '1') {
                    $initdata['order_tran_notpay'] = 0;
                    $initdata['order_tran_pay'] = $dataItem['unit_price'];
                } else {
                    $initdata['order_tran_notpay'] = $dataItem['unit_price'];
                    $initdata['order_tran_pay'] = 0;
                }
            }
        } else {
            $dataItem['unit_price'] = $qty * $dataItem['unit_price'];
            if ($dataItem['flag_pay'] == '1') {
                $initdata['order_tran_notpay'] = 0;
                $initdata['order_tran_pay'] = $dataItem['unit_price'];
            } else {
                $initdata['order_tran_notpay'] = $dataItem['unit_price'];
                $initdata['order_tran_pay'] = 0;
            }
        }

        $initdata['order_tran_status'] = '1';
        $initdata['order_tran_cashier_status'] = '1';
        $initdata['order_tran_dept'] = $userProfile['department'];
        $initdata['order_tran_code'] = $dataItem['order_code'];
        $initdata['order_tran_oi_type'] = $oipd_type;
        $initdata['order_tran_date'] = \appxq\sdii\utils\SDdate::mysql2phpDate($visit_date, '-');
        if (Yii::$app->user->can('doctor')) {
            $initdata['order_tran_doctor'] = $userProfile['user_id'];
        }

        return $initdata;
    }

    public function actionOrderDelete($dataid) {
        if (Yii::$app->getRequest()->isAjax) {
            $ezf_ordertran_id = \backend\modules\patient\Module::$formID['order_tran'];
            $ezf_ordertran_table = \backend\modules\patient\Module::$formTableName['order_tran'];

            $model = EzfUiFunc::loadTbData($ezf_ordertran_table, $dataid);
            if ($model['order_tran_status'] == '1') {
                $initdata['rstat'] = 3;
                $result = PatientFunc::saveDataNoSys($ezf_ordertran_id, $ezf_ordertran_table, $dataid, $initdata);
            } else {
                $result = [
                    'status' => 'error',
                    'message' => \appxq\sdii\helpers\SDHtml::getMsgError() . Yii::t('patient', 'The transaction has been completed. Can not cancel'),
                ];
            }
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $result;
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionGridOrder() {
        if (Yii::$app->getRequest()->isAjax) {
            $ezf_id = Yii::$app->request->get('ezf_id');
            $visit_id = Yii::$app->request->get('visitid');
            $reloadDiv = Yii::$app->request->get('reloadDiv');
            $btnDisabled = Yii::$app->request->get('btnDisabled');
            $options = EzfFunc::stringDecode2Array(Yii::$app->request->get('options'));
            $options['oipd_type'] = isset($options['oipd_type']) ? $options['oipd_type'] : 'OPD';

            if ($options['oipd_type'] == 'IPD') {
                if (isset($options['fix_grid_ipd']) && $options['fix_grid_ipd']) {
                    $dataProvider = OrderFunc::getOrderTran($visit_id, $options['oipd_type'], $options['fix_grid_ipd_date']);
                    $view = '_gridorder';
                } else {
                    $dataProvider = OrderFunc::getOrderGroupDate($visit_id);
                    $view = '_gridorder_groupdate';
                }
            } else {
                $dataProvider = OrderFunc::getOrderTran($visit_id, $options['oipd_type']);
                $view = '_gridorder';
            }

            return $this->renderAjax($view, [
                        'ezf_id' => $ezf_id,
                        'visit_id' => $visit_id,
                        'reloadDiv' => $reloadDiv,
                        'dataProvider' => $dataProvider,
                        'btnDisabled' => $btnDisabled,
                        'options' => $options,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionCounterOrderLists() {
        if (Yii::$app->getRequest()->isAjax) {
            $ezf_id = \backend\modules\patient\Module::$formID['order_header'];
            $ezfAppoint_id = \backend\modules\patient\Module::$formID['appoint'];
            $visit_id = Yii::$app->request->get('visitid');
            $reloadDiv = Yii::$app->request->get('reloadDiv');
            $order_status = Yii::$app->request->get('order_status', '1');
            $user = Yii::$app->user->identity->profile->attributes;
            $dept = ThaiHisQuery::getDepartmentFull($user['user_id']);
            $options = EzfFunc::stringDecode2Array(Yii::$app->request->get('options'));
            if ($dept) {
                $lab_no = '';
                $data = OrderQuery::getOrderCounterItem($dept['order_type_code'], $order_status, $visit_id);
                if ($dept['order_type_code'] == 'L') {
                    $lab_no = OrderQuery::getOrderLabNo($visit_id, $dept['order_type_code']);
                }

                return $this->renderAjax('_counter_order_lists', [
                            'visit_id' => $visit_id,
                            'order_status' => $order_status,
                            'reloadDiv' => $reloadDiv,
                            'data' => $data,
                            'dept' => $dept,
                            'ezf_id' => $ezf_id, 'user' => $user, 'ezm_id' => $options['ezm_id'],
                            'ezfAppoint_id' => $ezfAppoint_id, 'lab_no' => $lab_no
                ]);
            } else {
                throw new NotFoundHttpException('Profile user no department');
            }
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionOrderReceiveSubmit() {
        $ezf_id = Yii::$app->request->get('ezf_id');
        $ezf_table = \backend\modules\patient\Module::$formTableName['order_tran'];
        if (Yii::$app->request->post()) {
            $order_status = Yii::$app->request->get('order_status');
            $dept_counter = Yii::$app->request->get('dept');
            $order_bydept = Yii::$app->request->post('order_bydept');
            $pt_id = Yii::$app->request->get('pt_id');
            $oipd_type = Yii::$app->request->get('oipd_type');
            $order = Yii::$app->request->post('order_check', []);

            $order_id = [];
            foreach ($order as $value) {
                $arrOrder = EzfFunc::stringDecode2Array($value);
                $order_id[] = $arrOrder['order_tran_id'];
                $order_code[] = $arrOrder['order_code'];
            }

            if ($order_status == '1') {
                if ($dept_counter == 'L') {
                    $order_code = implode(',', $order_code); //ถ้าใช้ function ใหม่ต้องปิด 4/12/61
                    $vender_status = Yii::$app->request->post('order_vender_status', '2');
                    $vender_no = Yii::$app->request->post('order_vender_no', 'NEW');

                    $lis = $this->fileRequestToLis($order_code, $vender_no, $vender_status, $order_bydept, $pt_id);
                    $data['order_vender_status'] = $vender_status;
                    $data['order_vender_no'] = $lis['lis_ln'];
                    //ยกเลิกเพราะ lab พิมพ์เอง 
//                    $value = "{$lis['lis_hn']}^{$lis['lis_fullname']}^{$lis['lis_ln']}^{$lis['lis_item']}^{$vender_status}&dept=";
                    //สั่งพิมพ์ซ้ำ หน้ารับแล้วทั้ง Cyto,Lab
//                    \backend\modules\patient\classes\PatientFunc::getPrintStickerCyto($value);
                }
                $i = 0; //i for gen accnumber to pacs
                foreach ($order_id as $dataid) {
                    if ($dept_counter == 'X') {
                        $data['order_vender_no'] = $this->fileRequestToRisHL7($dataid, $order_bydept, $pt_id, $i);
                    }

                    //check oipd update receive tran
                    if ($oipd_type == "IPD") {
                        $data['order_tran_cashier_id'] = \backend\modules\patient\controllers\AdmitController::updateIpdReceiveTran($dataid, "ADD");
                    }

                    //save receive order
                    $data['order_tran_status'] = '2';
                    $result = PatientFunc::saveDataNoSys($ezf_id, $ezf_table, $dataid, $data);
                    $i++;
                }
            } else {
                $chkorder_item = Yii::$app->request->post('chkorder_tran_id');
                $order_item = array_diff($chkorder_item, $order_id);
                if (empty($order_item)) {
                    //$order_item = $chkorder_item;
                    $result = [
                        'status' => 'error',
                        'message' => \appxq\sdii\helpers\SDHtml::getMsgError() . 'คุณไม่ได้เลือกรายการ',
                    ];
                }
                foreach ($order_item as $dataid) {
                    //check oipd update receive tran
                    if ($oipd_type == "IPD") {
                        \backend\modules\patient\controllers\AdmitController::updateIpdReceiveTran($dataid, "CANCEL");
                        $data['order_tran_cashier_id'] = '';
                    }

                    $data['order_tran_status'] = '1';
                    $result = PatientFunc::saveDataNoSys($ezf_id, $ezf_table, $dataid, $data);
                }
            }
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $result;
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionOrderResultSubmit() {
        if (Yii::$app->getRequest()->isAjax) {
            $ezf_table = Yii::$app->request->get('ezf_table');
            $report_id = Yii::$app->request->get('dataid');
            $reloadDiv = Yii::$app->request->get('reloadDiv');
            $ezm_id = Yii::$app->request->get('ezm_id');
            $dept = Yii::$app->request->get('dept');

            //if (!in_array($dept, ['C', 'out-lab'])) {
            if ($dept !== 'out-lab') {
                $data = EzfUiFunc::loadTbData($ezf_table, $report_id);
                if ($data) {
                    $ezf_id = \backend\modules\patient\Module::$formID['order_tran'];
                    $ezf_table = \backend\modules\patient\Module::$formTableName['order_tran'];
                    $initdata = ['order_tran_status' => ($data['report_status'] == '1' ? '2' : '3')]; //สถานะของ result 1 ไปอัพเดท order_tran = ยังไม่ออกผล   
                    PatientFunc::saveDataNoSys($ezf_id, $ezf_table, $data['order_tran_id'], $initdata);
                }
            }

            return $this->renderAjax('_order_result_submit', [
                        'dept' => $dept, 'ezm_id' => $ezm_id,
                        'reloadDiv' => $reloadDiv
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    private function genNumberVendor($number, $type) {
        if ($number == "NEW") {
            $result = OrderQuery::genNumberVendor($type);
            if ($result) {
                $date = date('Y-m-d');
                $number = $result['number'];
                $ezf_id = \backend\modules\patient\Module::$formID['generate_number'];
                $ezf_table = \backend\modules\patient\Module::$formTableName['generate_number'];
                if ($result['gen_date'] == $date) {
                    $initdata = ['gen_number' => $result['gen_number'] + 1];
                } else {
                    $initdata = ['gen_number' => 1, 'gen_date' => $date];
                }

                PatientFunc::saveDataNoSys($ezf_id, $ezf_table, $result['id'], $initdata);
            } else {
                throw new NotFoundHttpException('genNumberVendor find not found');
            }
        }

        return sprintf("%03d", $number);
    }

    private function fileRequestToLis($item_code, $ln, $status, $dept, $pt_id) {
        $result = PatientQuery::getHisMapLis($item_code, $ln);

        $profile = \backend\modules\thaihis\classes\ThaiHisQuery::getPtProfile($pt_id);
        $bdate = str_replace("-", "/", \backend\modules\thaihis\classes\ThaiHisFunc::checkBdate($profile['pt_bdate'], "-"));
        $age = ThaiHisQuery::calAge($bdate);
        $bdate = str_replace("/", "", $bdate);
        $sex = ($profile['pt_sex'] == '1' ? 'M' : 'F');
        $dateTime = date('YmdHis');
        $fullname = $profile['pt_lastname'] . '^^' . $profile['prefix_name'] . $profile['pt_firstname'];
        $dept = substr_replace(PatientQuery::getWorkingUnit($dept)['unit_code_old'], '', 1, 1);
        $hn = (substr($profile['pt_hn'], 0, 1) == 'H' ? $profile['pt_hn'] : substr($profile['pt_hn'], 1, 7));
        $reqno = "";
        $lastDiag = "";
        $ln = $result[0]['ln'];
        $userIP = Yii::$app->getRequest()->getUserIP();
        if (isset($ln)) {
            $h = "H|\^&|||HOST|||||OMEGA|P||$dateTime\r\n";
            $p = "P|1|{$result[0]['ln']}|{$hn}|^|{$fullname}||{$bdate}|{$sex}||^^^|||{$reqno}|^||||{$lastDiag}||lab|||{$dateTime}^{$dateTime}|R|||^{$status}||||||{$dept}|{$profile['pt_sex']}^{$age}|{$userIP}\r\n";
            $o = '';
            $oBB = '';
            $no = 1;

            foreach ($result as $value) {
                if ($value['seccode'] == '6') {
                    /* foreach ($item_qty as $keyBB => $valueBB) {
                      if ($value['hiscode'] == $keyBB) {
                      $oBB .= "O|$no|{$ln}||^^^{$value['testcode']}^{$value['flag']}^$valueBB^|||||||A|||{$_POST['create_datetime']}|{$value['speccode']}|||{$value['seccode']}|||||||O\r\n";
                      }
                      } */
                } else {
                    $o .= "O|$no|{$ln}||^^^{$value['testcode']}^{$value['flag']}^^|||||||A|||{$dateTime}|{$value['speccode']}|||{$value['seccode']}|||||||O\r\n";
                }
                $no++;
            }

            $l = "L|1|N";

            /* if (!empty($oBB)) {
              $content = $h . $p . $oBB . $l;
              $dirPath = "/home/www/chemo/module/lab/request_lis/bb/" . $ln . $dateTime . ".pet";

              $objFopen = fopen($dirPath, 'w')or die("Unable to open file!");
              fwrite($objFopen, iconv('UTF-8', 'TIS-620', $content));
              fclose($objFopen);
              } */

            $content = $h . $p . $o . $oBB . $l;
            $dirPath = Yii::getAlias('@storage/web/request/lab/request/' . $ln . $dateTime . ".pet");
            $objFopen = fopen($dirPath, 'w')or die("Unable to open file!");
            fwrite($objFopen, iconv('UTF-8', 'TIS-620', $content));
            fclose($objFopen);

//            $dirPathSource = Yii::getAlias('@storage/web/ezform/fileinput/' . $profile['pt_pic']);
//            if (file_exists($dirPathSource) && $profile['pt_pic']) {
//                try {
//                    $dirPathDest = Yii::getAlias('@storage/web/requestLab/request/Image/' . $hn . ".jpg");
//
//                    \Intervention\Image\ImageManagerStatic::make($dirPathSource)->fit(148, 178)->save($dirPathDest);
//                    //copy($dirPathSource, $dirPathDest);
//                } catch (Exception $ex) {
//                    
//                }
//            }

            return ['lis_ln' => $ln, 'lis_hn' => $hn, 'lis_fullname' => $profile['prefix_name'] . $profile['pt_firstname'] . '_' . $profile['pt_lastname'], 'lis_item' => $item_code];
        }
    }

    private function fileRequestToLisNew($item_code, $ln, $status, $dept, $pt_id) {
        $ln = $this->genNumberVendor($ln, "LAB");
        $result = OrderQuery::getHisMapLis($item_code, $ln);

        $profile = ThaiHisQuery::getPtProfile($pt_id);
        $bdate = str_replace("-", "/", \backend\modules\thaihis\classes\ThaiHisFunc::checkBdate($profile['pt_bdate'], "-"));
        $age = ThaiHisQuery::calAge($bdate);
        $bdate = str_replace("/", "", $bdate);
        $sex = ($profile['pt_sex'] == '1' ? 'M' : 'F');
        $dateTime = date('YmdHis');
        $fullname = $profile['pt_lastname'] . '^^' . $profile['prefix_name'] . $profile['pt_firstname'];
        $dept = substr_replace(PatientQuery::getWorkingUnit($dept)['unit_code_old'], '', 1, 1);
        $hn = (substr($profile['pt_hn'], 0, 1) == 'H' ? $profile['pt_hn'] : substr($profile['pt_hn'], 1, 7));
        $reqno = "";
        $lastDiag = "";
        $ln = $result[0]['ln'];
        $userIP = Yii::$app->getRequest()->getUserIP();
        if (isset($ln)) {
            $h = "H|\^&|||HOST|||||OMEGA|P||$dateTime\r\n";
            $p = "P|1|{$result[0]['ln']}|{$hn}|^|{$fullname}||{$bdate}|{$sex}||^^^|||{$reqno}|^||||{$lastDiag}||lab|||{$dateTime}^{$dateTime}|R|||^{$status}||||||{$dept}|{$profile['pt_sex']}^{$age}|{$userIP}\r\n";
            $o = '';
            $oBB = '';
            $no = 1;

            foreach ($result as $value) {
                if ($value['seccode'] == '6') {
                    /* foreach ($item_qty as $keyBB => $valueBB) {
                      if ($value['hiscode'] == $keyBB) {
                      $oBB .= "O|$no|{$ln}||^^^{$value['testcode']}^{$value['flag']}^$valueBB^|||||||A|||{$_POST['create_datetime']}|{$value['speccode']}|||{$value['seccode']}|||||||O\r\n";
                      }
                      } */
                } else {
                    $o .= "O|$no|{$ln}||^^^{$value['testcode']}^{$value['flag']}^^|||||||A|||{$dateTime}|{$value['speccode']}|||{$value['seccode']}|||||||O\r\n";
                }
                $no++;
            }

            $l = "L|1|N";

            /* if (!empty($oBB)) {
              $content = $h . $p . $oBB . $l;
              $dirPath = "/home/www/chemo/module/lab/request_lis/bb/" . $ln . $dateTime . ".pet";

              $objFopen = fopen($dirPath, 'w')or die("Unable to open file!");
              fwrite($objFopen, iconv('UTF-8', 'TIS-620', $content));
              fclose($objFopen);
              } */

            $content = $h . $p . $o . $oBB . $l;
            $dirPath = Yii::getAlias('@storage/web/request/lab/request/' . $ln . $dateTime . ".pet");
            $objFopen = fopen($dirPath, 'w')or die("Unable to open file!");
            fwrite($objFopen, iconv('UTF-8', 'TIS-620', $content));
            fclose($objFopen);

//            $dirPathSource = Yii::getAlias('@storage/web/ezform/fileinput/' . $profile['pt_pic']);
//            if (file_exists($dirPathSource) && $profile['pt_pic']) {
//                try {
//                    $dirPathDest = Yii::getAlias('@storage/web/requestLab/request/Image/' . $hn . ".jpg");
//
//                    \Intervention\Image\ImageManagerStatic::make($dirPathSource)->fit(148, 178)->save($dirPathDest);
//                    //copy($dirPathSource, $dirPathDest);
//                } catch (Exception $ex) {
//                    
//                }
//            }

            return ['lis_ln' => $ln, 'lis_hn' => $hn, 'lis_fullname' => $profile['prefix_name'] . $profile['pt_firstname'] . '_' . $profile['pt_lastname'], 'lis_item' => $item_code];
        }
    }

    private function fileRequestToRisHL7($order_id, $dept, $pt_id, $i) {
        $profile = ThaiHisQuery::getPtProfile($pt_id);
        $bdate = str_replace("-", "/", \backend\modules\thaihis\classes\ThaiHisFunc::checkBdate($profile['pt_bdate'], "-"));
        $bdate = str_replace("/", "", $bdate);
        $sex = ($profile['pt_sex'] == '1' ? 'M' : 'F');
        $datetime = date('YmdHis') + $i;
        //$acc_id = $this->genNumberVendor("NEW", "XRAY");
        $item = OrderQuery::getOrderTranById($order_id, '1');

        $content = "MSH|^~\&|TRAKCARE|001|||{$datetime}||ORM^O01|1364|P|2.4|||AL|NE|||EN|\r\n";
        $content .= "EVN|O01|{$datetime}|||puntipa^ ^^^^^^|{$datetime}|001^01MED\r\n";
        $content .= "PID|||{$profile['pt_hn']}||{$profile['pt_firstname']}^{$profile['pt_lastname']}^^{$profile['prefix_name']}||{$bdate}|{$sex}|{$profile['pt_hn']}^^^||^^^^^^^^^^||||||||||||||||||||\r\n";
        $content .= "PV1|1|I|^^^^^^||||||||||||||||I|||||||||||||||||||||||||||||||||\r\n";
        //$content .= "ORC|NW||||||||{$item['datetime']}||||||||||{$item['doctor']}|\r\n";
        $content .= "ORC|NW||||||||{$datetime}||||||||||{$item['doctor']}|\r\n";
        //$value = ACCESSIONNUMBER
        //$content .= "OBR|||{$acc_id}|{$item['order_tran_code']}^{$item['order_name']}|||||||||||||||||||||I|||||||||||||||||||\r\n";
        $content .= "OBR|||{$datetime}|{$item['order_tran_code']}^{$item['order_name']}|||||||||||||||||||||I|||||||||||||||||||\r\n";

        $dirPath = Yii::getAlias('@storage/web/request/xray/request/' . $datetime . ".HL7");
        $objFopen = fopen($dirPath, 'w')or die("Unable to open file!");
        fwrite($objFopen, iconv('UTF-8', 'TIS-620', $content));
        fclose($objFopen);

        return $datetime;
    }

    public function actionPtprofileByorder($order_id, $order_status) {
        if (Yii::$app->getRequest()->isAjax) {
            $data = OrderQuery::getOrderTranFullnameById($order_id);
            $bdate = str_replace("-", "/", \backend\modules\thaihis\classes\ThaiHisFunc::checkBdate($data['pt_bdate'], "-"));
            $data['pt_age'] = ThaiHisQuery::calAge($bdate);

            Yii::$app->response->format = Response::FORMAT_JSON;
            return $data;
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionResultLabShow() {
        if (Yii::$app->getRequest()->isAjax) {
            //$ezf_table = \backend\modules\patient\Module::$formTableName['order_tran'];
            $pt_id = Yii::$app->request->get('pt_id');
            $pt_hn = Yii::$app->request->get('pt_hn');
            $visit_id = Yii::$app->request->get('visit_id');
            $reloadDiv = Yii::$app->request->get('reloadDiv');
            $view = Yii::$app->request->get('view');
            $secname = Yii::$app->request->get('secname', null);
            $test_code = Yii::$app->request->get('test_code', null);
            $ln = Yii::$app->request->get('ln', null);
            $date = Yii::$app->request->get('date');

//            $pt_hn = 'H6100042';
//            $date = '2018-04-04';

            $view_file = '';
            if ($view == 'modal_chart' || $view == 'cpoe') {
                $arrResultLab = $this->labResultChart($pt_hn, $date, $secname);
                $view_file = '_result_lab_chart';
            } else {
                if (empty($ln)) {
                    $arrResultLab = PatientQuery::getLabResultOneRecord($pt_hn, $date, $secname, $test_code);
                } else {
                    $arrResultLab = PatientQuery::getLabResultByLn($ln);
                }
                $view_file = '_result_lab_table';
            }

            return $this->renderAjax($view_file, [
                        'pt_id' => $pt_id,
                        'pt_hn' => $pt_hn,
                        'visit_id' => $visit_id,
                        'reloadDiv' => $reloadDiv,
                        'view' => $view,
                        'arrResultLab' => $arrResultLab,
                        'secname' => $secname,
                        'date' => $date,
                            //'chkAppDate' => $chkAppDate,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    private function labResultChart($pt_hn, $date, $secname) {
        $dataResultLab = [];
        $chkAppDate = [];
        $chkTestName = '';
        $chkSecName = '';
        $arrResultLab = [];

        $dataResultLab = PatientQuery::getLabResultByHn($pt_hn, $date, $secname);

        if ($dataResultLab) {
            foreach ($dataResultLab as $value) { //date ใน section ทั้งหมดที่มี
                if ($chkSecName !== $value['secname']) {
                    $chkSecName = $value['secname'];
                }
                $chkAppDate[$chkSecName][$value['app_date']] = 0;
            }

            $chkSecName = '';
            foreach ($dataResultLab as $value) {
                if ($chkSecName !== $value['secname']) {
                    $chkSecName = $value['secname'];
                }
                if ($chkTestName !== $value['test_name']) {
                    $chkTestName = $value['test_name'];
                }
                $valueResult = str_replace(',', '', $value['result']);
                //if (is_numeric($valueResult)) {
                $arrResultLab[$chkSecName][$chkTestName]['show'] = $value['flagshow'];
                $arrResultLab[$chkSecName][$chkTestName][$value['app_date']] = (double) $valueResult;
                //}
            }
        }

        return $arrResultLab;
    }

    public function actionResultXrayShow() {
        if (Yii::$app->getRequest()->isAjax) {
            $pt_id = Yii::$app->request->get('pt_id');
            $pt_hn = Yii::$app->request->get('pt_hn');
            $visit_id = Yii::$app->request->get('visit_id');
            $reloadDiv = Yii::$app->request->get('reloadDiv');
            $date = Yii::$app->request->get('date');

            $data['result'] = PatientQuery::getOrderCounterItemReport($visit_id);
            return $this->renderAjax('_result_xray', [
                        'pt_id' => $pt_id,
                        'pt_hn' => $pt_hn,
                        'visit_id' => $visit_id,
                        'reloadDiv' => $reloadDiv,
                        'data' => $data,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionResultCytoShow() {
        if (Yii::$app->getRequest()->isAjax) {
            $ezfCyto_id = \backend\modules\patient\Module::$formID['cytoreport'];
            $ezfEkg_id = \backend\modules\patient\Module::$formID['report_ekg'];
            $pt_id = Yii::$app->request->get('pt_id');
            $pt_hn = Yii::$app->request->get('pt_hn');
            $visit_id = Yii::$app->request->get('visit_id');
            $reloadDiv = Yii::$app->request->get('reloadDiv');

            $dataPap = PatientQuery::getOrderCounterItemCyto('2', $visit_id);
            $dataEkg = PatientQuery::getOrderReportEkg('2', $visit_id);

            return $this->renderAjax('_result_cyto', [
                        'pt_id' => $pt_id,
                        'pt_hn' => $pt_hn,
                        'visit_id' => $visit_id,
                        'reloadDiv' => $reloadDiv,
                        'dataPap' => $dataPap,
                        'dataEkg' => $dataEkg,
                        'ezfCyto_id' => $ezfCyto_id, 'ezfEkg_id' => $ezfEkg_id
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionResultPathoShow() {
        if (Yii::$app->getRequest()->isAjax) {
            $pt_id = Yii::$app->request->get('pt_id');
            $pt_hn = Yii::$app->request->get('pt_hn');
            $visit_id = Yii::$app->request->get('visit_id');
            $reloadDiv = Yii::$app->request->get('reloadDiv');

            $data = null;

            return $this->renderAjax('_result_cyto', [
                        'pt_id' => $pt_id,
                        'pt_hn' => $pt_hn,
                        'visit_id' => $visit_id,
                        'reloadDiv' => $reloadDiv,
                        'data' => $data
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public static function checkOrderCancelStatus($visit_id) {
        $ezf_id = \backend\modules\patient\Module::$formID['order_tran'];
        $ezf_table = \backend\modules\patient\Module::$formTableName['order_tran'];
        $result = FALSE;

        $data = OrderQuery::getOrderCheckStatus($visit_id);
        if ($data) {
            foreach ($data as $value) {
                PatientFunc::saveDataNoSys($ezf_id, $ezf_table, $value['id'], ['rstat' => 3]);
            }
            $result = true;
        } else {
            $result = true;
        }

        return $result;
    }

    public function actionPrintSticker($visit_id) {

        \backend\modules\patient\classes\PatientFunc::getPrintStickerCyto($value);
    }

    public function actionOrderAdmitGroup($admit_id, $visit_id) {
        $reloadDiv = Yii::$app->request->get('reloadDiv');

        $dataOrderGroup = PatientQuery::getOrderFinGroup($visit_id);

        return $this->renderAjax('_order_fin_group', [
                    'dataOrderGroup' => $dataOrderGroup,
                    'visit_id' => $visit_id,
                    'reloadDiv' => $reloadDiv,
                    'visit_id' => $visit_id,
                    'admit_id' => $admit_id,
        ]);
    }

    public function actionOrderAdmitGroupDetail($visit_id, $group_code) {
        $reloadDiv = Yii::$app->request->get('reloadDiv');
        $admit_id = Yii::$app->request->get('admit_id');

        $dataOrderGroupDetail = PatientQuery::getOrderFinGroupDetail($visit_id, $group_code);

        return $this->renderAjax('_order_fin_groupdetail', [
                    'dataOrderGroupDetail' => $dataOrderGroupDetail,
                    'visit_id' => $visit_id,
                    'reloadDiv' => $reloadDiv,
                    'visit_id' => $visit_id,
                    'admit_id' => $admit_id,
        ]);
    }

    public function actionOrderFoodAdmit() {
        if (Yii::$app->getRequest()->isAjax) {
            $ezf_id = \backend\modules\patient\Module::$formID['food_order'];
            $ezf_tbname = \backend\modules\patient\Module::$formTableName['food_order'];
            $dataid = Yii::$app->request->get('dataid');
            $admit_id = Yii::$app->request->get('admit_id');
            $visit_id = Yii::$app->request->get('visit_id');
            $reloadDiv = Yii::$app->request->get('reloadDiv');

            if ($dataid) {
                $model = EzfUiFunc::loadTbData($ezf_tbname, $dataid);
            } else {
                $model = PatientFunc::loadTbDataByTarget($ezf_tbname, $admit_id);
            }

            return $this->renderAjax('_food_order', [
                        'ezf_id' => $ezf_id,
                        'model' => $model,
                        'dataid' => $dataid,
                        'admit_id' => $admit_id,
                        'visit_id' => $visit_id,
                        'reloadDiv' => $reloadDiv,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionXrayDoc() {
        if (Yii::$app->getRequest()->isAjax) {
            $ezf_id = \backend\modules\patient\Module::$formID['order_header'];
            $ezfAppoint_id = \backend\modules\patient\Module::$formID['appoint'];
            $visit_id = Yii::$app->request->get('visitid');
            $reloadDiv = Yii::$app->request->get('reloadDiv');
            $order_status = Yii::$app->request->get('order_status', '1');
            $user = Yii::$app->user->identity->profile->attributes;
            $dept = ThaiHisQuery::getDepartmentFull($user['user_id']);
            if ($dept) {
                $data = OrderQuery::getOrderCounterItem($dept['order_type_code'], $order_status, $visit_id);

                return $this->renderAjax('_counter_order_lists', [
                            'visit_id' => $visit_id,
                            'order_status' => $order_status,
                            'reloadDiv' => $reloadDiv,
                            'data' => $data,
                            'dept' => $dept,
                            'ezf_id' => $ezf_id,
                            'user' => $user,
                            'ezfAppoint_id' => $ezfAppoint_id
                ]);
            } else {
                throw new NotFoundHttpException('Profile user no department');
            }
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionXrayDocCounter() {
        $ezf_id = \backend\modules\patient\Module::$formID['order_tran'];
        $user = Yii::$app->user->identity->profile->attributes;
        $dept = ThaiHisQuery::getDepartmentFull($user['user_id']);

        $searchModel = PatientFunc::getModel($ezf_id, '');
        $searchModel['order_tran_status'] = '1';
        $searchModel['create_date'] = date('d/m/Y');

        $dataProvider = PatientFunc::getOrderCounter($searchModel, Yii::$app->request->get(), $dept);
        \appxq\sdii\utils\VarDumper::dump($dataProvider);

        return $this->render('_ordercounter', [
                    'ezf_id' => $ezf_id,
                    'dept' => $dept,
                    'dataProvider' => $dataProvider,
                    'searchModel' => $searchModel,
        ]);
    }

    public function actionOrderCounterCyto() {
        $ezf_id = \backend\modules\patient\Module::$formID['order_tran'];
        $params = Yii::$app->request->get();
        if (!isset($params['unit_id'])) {
            $user = Yii::$app->user->identity->profile->attributes;
            $dept = ThaiHisQuery::getDepartmentFull($user['user_id']);
        } else {
            $dept['unit_id'] = $params['unit_id'];
        }

        $searchModel = PatientFunc::getModel($ezf_id, '');
        $searchModel['order_tran_status'] = '1';
        $searchModel['create_date'] = date('d/m/Y'); //default date now()

        $dataProvider = OrderQuery::getOrderCounterCyto($searchModel, $params, $dept['unit_id']);

        return $this->renderAjax('ordercounter_cyto', [
                    'ezf_id' => $ezf_id,
                    'dataProvider' => $dataProvider,
                    'searchModel' => $searchModel,
                    'unit_id' => $dept['unit_id'],
                    'reloadDiv' => $params['reloadDiv'],
        ]);
    }

    public function actionOrderReceiveCyto() {
        if (Yii::$app->getRequest()->isAjax) {
            $reloadDiv = Yii::$app->request->get('reloadDiv');
            $order_status = Yii::$app->request->get('order_status');
            $visit_id = Yii::$app->request->get('visitid');

            $data = PatientQuery::getOrderCounterItemCyto($order_status, $visit_id);
            $view = '_gridorder_receive_cyto';

            return $this->renderAjax($view, [
                        'visit_id' => $visit_id,
                        'order_status' => $order_status,
                        'reloadDiv' => $reloadDiv,
                        'data' => $data,
                        'pt_id' => $data[0]['ptid'],
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionOrderReceiveSubmitCyto() {
        if (Yii::$app->request->post()) {
            $ezf_id = \backend\modules\patient\Module::$formID['cytoreport'];
            $ezf_table = \backend\modules\patient\Module::$formTableName['cytoreport'];
            $order = Yii::$app->request->post('order_check', []);
            $order_status = Yii::$app->request->get('order_status');
            $order_id = [];
            $result = '';
            foreach ($order as $value) {
                $arrOrder = explode("^", $value);
                $order_id[] = $arrOrder[0];
                if ($arrOrder[1] == 'CG001' && $order_status == 'H') {
                    $data['report_status'] = '1';
                    $result = PatientFunc::saveDataNoSys($ezf_id, $ezf_table, $arrOrder[0], $data);
                }

                if ($arrOrder[1] == 'CG016' && $order_status == 'H') {
                    $dept = Yii::$app->request->post('order_dept');
                    $pt_id = Yii::$app->request->get('pt_id');
                    $ezfOrder_id = \backend\modules\patient\Module::$formID['order_tran'];
                    $ezfOrder_table = \backend\modules\patient\Module::$formTableName['order_tran'];

                    $lis = $this->fileRequestToLis($arrOrder[1], 'NEW', '1', $dept, $pt_id);
                    $data['order_vender_no'] = $lis['lis_ln'];
                    $data['order_vender_status'] = '1';

                    $result = PatientFunc::saveDataNoSys($ezfOrder_id, $ezfOrder_table, $arrOrder[0], $data);
                    $value = "{$lis['lis_hn']}^{$lis['lis_fullname']}^{$lis['lis_ln']}^{$lis['lis_item']}^{$data['order_vender_status']}&dept=CG";
                    //สั่งพิมพ์ซ้ำ หน้ารับแล้วทั้ง Cyto,Lab
                    \backend\modules\patient\classes\PatientFunc::getPrintStickerCyto($value);
                }
            }

            Yii::$app->response->format = Response::FORMAT_JSON;
            return $result;
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionOrderResultSubmitCyto() {
        if (Yii::$app->getRequest()->isAjax) {
            $ezf_table = Yii::$app->request->get('ezf_table');
            $report_id = Yii::$app->request->get('dataid');
            $order_status = Yii::$app->request->get('order_status');
            $reloadDiv = Yii::$app->request->get('reloadDiv');

            $data = EzfUiFunc::loadTbData($ezf_table, $report_id);
            if ($data) {
                $ezf_id = \backend\modules\patient\Module::$formID['order_tran'];
                $ezf_table = \backend\modules\patient\Module::$formTableName['order_tran'];
                $initdata = ['order_tran_status' => ($data['report_status'] == '1' ? '2' : '3')]; //สถานะของ result 1 ไปอัพเดท order_tran = ยังไม่ออกผล   
                PatientFunc::saveDataNoSys($ezf_id, $ezf_table, $data['order_tran_id'], $initdata);
            }

//            return $this->renderAjax('_order_result_submit_cyto', [
//                        'reloadDiv' => $reloadDiv,
//                        'order_status' => $order_status,
//            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

}
