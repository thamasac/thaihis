<?php

namespace backend\modules\patient\controllers;

use appxq\sdii\utils\VarDumper;
use Yii;
use backend\modules\patient\classes\PatientQuery;
use backend\modules\ezforms2\classes\EzfUiFunc;
use backend\modules\patient\classes\PatientFunc;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use appxq\sdii\utils\SDdate;

class OrderController extends \yii\web\Controller {

    public function actionIndex() {
        return $this->renderAjax('index');
    }

    public function actionOrderpopup() {
        if (Yii::$app->getRequest()->isAjax) {
            $ezf_id = \backend\modules\patient\Module::$formID['order_tran'];
            $pt_id = Yii::$app->request->get('pt_id');
            $target = Yii::$app->request->get('target');
            $visit_type = Yii::$app->request->get('visit_type');
            $reloadDiv = Yii::$app->request->get('reloadDiv');
            $searchModel = new \backend\modules\patient\models\ConstOrderSearch;

            return $this->renderAjax('_orderpopup', [
                        'ezf_id' => $ezf_id,
                        'pt_id' => $pt_id,
                        'target' => $target,
                        'reloadDiv' => $reloadDiv,
                        'visit_type' => $visit_type,
                        'searchModel' => $searchModel,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionOrderSearch() {
        if (Yii::$app->getRequest()->isAjax) {
            $target = Yii::$app->request->get('target');
            $reloadDiv = Yii::$app->request->get('reloadDiv');
            $userposition = Yii::$app->user->identity->profile->attributes['position'];

            $searchModel = new \backend\modules\patient\models\ConstOrderSearch;
            $dataProvider = $searchModel->searchOrderList(Yii::$app->request->post(), $userposition);

            $orderTranPt = PatientQuery::getOrderTranPt($target, '1');
            $orderTranPt = \yii\helpers\ArrayHelper::map($orderTranPt, 'order_tran_code', 'order_tran_code');

            return $this->renderAjax('_order_list', [
                        'target' => $target,
                        'reloadDiv' => $reloadDiv,
//                        'searchModel' => $searchModel,
                        'dataProvider' => $dataProvider,
                        'orderTranPt' => $orderTranPt
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionOrderSelect($item, $grouptype) {
        if (Yii::$app->getRequest()->isAjax) {
            $target = Yii::$app->request->get('target');
            $visit_type = Yii::$app->request->get('visit_type');
            $pt_id = Yii::$app->request->get('pt_id');
            $pt_right = PatientQuery::getRightLast($pt_id);
            $pt_right['pt_bdate'] = SDdate::getAge(SDdate::dateTh2bod($pt_right['pt_bdate']));
            $result = $this->saveOrderItem($target, $item, $grouptype, $visit_type . '^' . $pt_right['right_code'] . '^' . $pt_right['pt_bdate']);
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $result;
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionGridOrderChange($dataid, $qty) {
        if (Yii::$app->getRequest()->isAjax) {
            $ezf_id = \backend\modules\patient\Module::$formID['order_tran'];
            $ezf_table = \backend\modules\patient\Module::$formTableName['order_tran'];

            $model = EzfUiFunc::loadTbData($ezf_table, $dataid);
            if ($model) {
                $dataItem = \backend\modules\patient\models\ConstOrder::findOne(['order_code' => $model['order_tran_code']]);
                $initdata['order_qty'] = $qty;
                if ($model['order_tran_notpay'] == '0') {
                    $initdata['order_tran_pay'] = $qty * $dataItem['full_price'];
                } else {
                    $initdata['order_tran_notpay'] = $qty * $dataItem['full_price'];
                }

                $result = PatientFunc::saveDataNoSys($ezf_id, $ezf_table, $dataid, $initdata);
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

    public static function saveOrderItem($target, $item, $grouptype, $right_order) {
        $ezf_id = \backend\modules\patient\Module::$formID['order_tran'];

        if ($grouptype == 'P') {
            $arrItemCode = PatientQuery::getPackageItem($item);
            if ($arrItemCode) {
                foreach ($arrItemCode as $value) {
                    $dataItem = \backend\modules\patient\models\ConstOrder::findOne(['order_code' => $value]);
                    $initdata = self::flagRightPrice($right_order, $dataItem, $value['package_qty']);
                    $result = EzfUiFunc::backgroundInsert($ezf_id, '', $target, $initdata);
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
            $result = EzfUiFunc::backgroundInsert($ezf_id, '', $target, $initdata);
        }
        return $result;
    }

    private static function flagRightPrice($right_order, $dataItem, $qty) {
        $arrRight = explode("^", $right_order);
        $initdata = '';
        $userProfile = Yii::$app->user->identity->profile->attributes;
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
        $initdata['order_tran_dept'] = $userProfile['department'];
        $initdata['order_tran_code'] = $dataItem['order_code'];
        if ($userProfile['position'] == '2') {
            $initdata['order_tran_doctor'] = $userProfile['user_id'];
        }

        return $initdata;
    }

    public function actionOrderDelete($dataid) {
        if (Yii::$app->getRequest()->isAjax) {
            $ezf_id = \backend\modules\patient\Module::$formID['order_tran'];
            $ezf_table = \backend\modules\patient\Module::$formTableName['order_tran'];

            $model = EzfUiFunc::loadTbData($ezf_table, $dataid);
            if ($model['order_tran_status'] == '1') {
                $data['rstat'] = 3;
                $result = PatientFunc::saveDataNoSys($ezf_id, $ezf_table, $dataid, $data);
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
            $target = Yii::$app->request->get('target');
            $reloadDiv = Yii::$app->request->get('reloadDiv');
            $data_column = Yii::$app->request->get('data_column');
            $btnDisabled = Yii::$app->request->get('btnDisabled');
            
            $dataProvider = PatientFunc::getOrderTran($target);
            
            return $this->renderAjax('_gridorder', [
                        'ezf_id' => $ezf_id,
                        'target' => $target,
                        'reloadDiv' => $reloadDiv,
                        'dataProvider' => $dataProvider,
                        'btnDisabled' => $btnDisabled,
                        'data_column' => \backend\modules\ezforms2\classes\EzfFunc::stringDecode2Array($data_column),
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionOrderCounter() {
        $ezf_id = \backend\modules\patient\Module::$formID['order_tran'];
        $sect_code = (isset(Yii::$app->user->identity->profile->attributes['department']) ? Yii::$app->user->identity->profile->attributes['department'] : null);
        $dept = Yii::$app->request->get('dept');

        Yii::$app->session['ezf_input'] = \backend\modules\ezforms2\classes\EzfQuery::getInputv2All();
        if (empty($dept)) {
            $dept = PatientQuery::getDepartmentOne($sect_code)['sect_his_type'];
        }
        $searchModel = PatientFunc::getModel($ezf_id, '');
        $searchModel['order_tran_status'] = '1';
        $searchModel['create_date'] = date('d/m/Y');
        
        if ($dept <> 'C') {
            $dataProvider = PatientFunc::getOrderCounter($searchModel, Yii::$app->request->get(), $dept);
            $view = 'ordercounter';
        } else {
            $dataProvider = PatientFunc::getOrderCounterPap($searchModel, Yii::$app->request->get(), $sect_code);
            $view = 'ordercounter_pap';
        }

        return $this->render($view, [
                    'ezf_id' => $ezf_id,
                    'dept' => $dept,
                    'sect_code' => $sect_code,
                    'dataProvider' => $dataProvider,
                    'searchModel' => $searchModel,
        ]);
    }

    public function actionOrderCounterOutlab() {
        $ezf_id = \backend\modules\patient\Module::$formID['order_tran'];
        Yii::$app->session['ezf_input'] = \backend\modules\ezforms2\classes\EzfQuery::getInputv2All();
        $searchModel = PatientFunc::getModel($ezf_id, '');
        $searchModel['order_tran_status'] = '1';
        $dataProvider = PatientFunc::getOrderCounterOutlab($searchModel, Yii::$app->request->get());

        return $this->render('ordercounter_outlab', [
                    'ezf_id' => $ezf_id,
                    'dataProvider' => $dataProvider,
                    'searchModel' => $searchModel,
        ]);
    }

    public function actionOrderOutlabReceiveShow() {
        if (Yii::$app->getRequest()->isAjax) {
            $ezf_id = Yii::$app->request->get('ezf_id');
            $target = Yii::$app->request->get('target');
            $reloadDiv = Yii::$app->request->get('reloadDiv');
            $order_status = Yii::$app->request->get('order_status');

            $data = PatientQuery::getOrderOutlabCounterItem($order_status, $target);

            return $this->renderAjax('_gridorder_receive_outlab', [
                        'ezf_id' => $ezf_id,
                        'target' => $target,
                        'order_status' => $order_status,
                        'reloadDiv' => $reloadDiv,
                        'data' => $data,
                        'pt_id' => $data[0]['ptid'],
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionOrderReceiveShow() {
        if (Yii::$app->getRequest()->isAjax) {
            $ezf_id = Yii::$app->request->get('ezf_id');
            $target = Yii::$app->request->get('target');
            $reloadDiv = Yii::$app->request->get('reloadDiv');
            $dept = Yii::$app->request->get('dept');
            $sect_code = Yii::$app->request->get('sect_code');
            $order_status = Yii::$app->request->get('order_status');
            $dept_code = Yii::$app->request->get('dept_code');

            if ($dept <> 'C') {
                $data = PatientQuery::getOrderCounterItem($dept, $order_status, $target);
                $view = '_gridorder_receive';
            } else {
                if ($sect_code == 'S076') {
                    $data = PatientQuery::getOrderCounterItem($dept, $order_status, $target);
                } else {
                    $data = PatientQuery::getOrderCounterItemCyto($order_status, $target);
                }
                $view = '_gridorder_receive_pap';
            }

            return $this->renderAjax($view, [
                        'ezf_id' => $ezf_id,
                        'target' => $target,
                        'order_status' => $order_status,
                        'reloadDiv' => $reloadDiv,
                        'data' => $data,
                        'dept' => $dept,
                        'sect_code' => $sect_code,
                        'pt_id' => $data[0]['ptid'],
                        'dept_code' => $dept_code,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionOrderResultSubmit() {
        if (Yii::$app->getRequest()->isAjax) {
            $ezf_table = Yii::$app->request->get('ezf_table');
            $report_id = Yii::$app->request->get('dataid');
            $reloadDiv = Yii::$app->request->get('reloadDiv');
            $dept = Yii::$app->request->get('dept');

            if (!in_array($dept, ['C', 'out-lab'])) {
                $data = EzfUiFunc::loadTbData($ezf_table, $report_id);
                if ($data) {
                    $ezf_id = \backend\modules\patient\Module::$formID['order_tran'];
                    $ezf_table = \backend\modules\patient\Module::$formTableName['order_tran'];
                    $initdata = ['order_tran_status' => ($data['report_status'] == '1' ? '2' : '3')]; //สถานะของ result 1 ไปอัพเดท order_tran = ยังไม่ออกผล   
                    PatientFunc::saveDataNoSys($ezf_id, $ezf_table, $data['order_tran_id'], $initdata);
                }
            }

            return $this->renderAjax('_order_result_submit', [
                        'dept' => $dept,
                        'reloadDiv' => $reloadDiv
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionOrderReceiveSubmitPap() {
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
                    $data['order_vender_status'] = '1';
                    $lis = $this->fileRequestToLis($arrOrder[1], 'NEW', '1', $dept, $pt_id);
                    $data['order_vender_no'] = $lis['lis_ln'];
//                    $data['order_tran_status'] = '2';
                    $result = PatientFunc::saveDataNoSys($ezfOrder_id, $ezfOrder_table, $arrOrder[0], $data);
                    $value = "{$lis['lis_hn']}^{$lis['lis_fullname']}^{$lis['lis_ln']}^{$lis['lis_item']}^{$data['order_vender_status']}&dept=CG";
                    //สั่งพิมพ์ซ้ำ หน้ารับแล้วทั้ง Cyto,Lab
                    \backend\modules\patient\classes\PatientFunc::getPrintStickerCyto($value);
                }
            }
//            if (empty($result)) {
//                $result = [
//                    'status' => 'error',
//                    'message' => \appxq\sdii\helpers\SDHtml::getMsgError() . 'กรุณาทำรานการใหม่อีกครั้ง',
//                ];
//            }

            Yii::$app->response->format = Response::FORMAT_JSON;
            return $result;
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
            $pt_id = Yii::$app->request->get('pt_id');
            $order = Yii::$app->request->post('order_check', []);
            $order_id = [];
            foreach ($order as $value) {
                $arrOrder = explode("^", $value);
                $order_id[] = $arrOrder[0];
                $order_code[] = $arrOrder[1];
            }
            $dept = Yii::$app->request->post('order_dept');
            if ($order_status == '1') {
                if ($dept_counter == 'L') {
                    $order_code = implode(',', $order_code);
                    $vender_status = Yii::$app->request->post('order_vender_status', '2');
                    $vender_no = Yii::$app->request->post('order_vender_no', 'NEW');
                    $data['order_vender_status'] = $vender_status;
                    $data['order_vender_no'] = $this->fileRequestToLis($order_code, $vender_no, $vender_status, $dept, $pt_id)['lis_ln'];
                }

                foreach ($order_id as $dataid) {
                    if ($dept_counter == 'X') {
                        $data['order_vender_status'] = $this->fileRequestToRisHL7($dataid, $dept, $pt_id);

                        $ezfMap_id = \backend\modules\patient\Module::$formID['mapacc_pacs'];
                        EzfUiFunc::backgroundInsert($ezfMap_id, '', '', ['mapacc_orderid' => $dataid,
                            'mapacc_accid' => (int) substr($data['order_vender_status'], 8), 'mapacc_date' => date('Y-m-d')]);
                    }

                    $data['order_tran_status'] = '2';
                    $result = PatientFunc::saveDataNoSys($ezf_id, $ezf_table, $dataid, $data);
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

    private function fileRequestToLis($item_code, $ln, $status, $dept, $pt_id) {
        $result = PatientQuery::getHisMapLis($item_code, $ln);

        $profile = \backend\modules\thaihis\classes\ThaiHisQuery::getPtProfile($pt_id);
        $bdate = SDdate::dateTh2bod(RestfulController::checkBdate($profile['pt_bdate']));
        $age = SDdate::getAge($bdate);
        $bdate = str_replace("/", "", $bdate);
        $sex = ($profile['pt_sex'] == '1' ? 'M' : 'F');
        $dateTime = date('YmdHis');
        $fullname = $profile['pt_lastname'] . '^^' . $profile['prefix_name'] . $profile['pt_firstname'];
        $dept = substr_replace(PatientQuery::getDepartmentOne($dept)['sect_map_code'], '', 1, 1);
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
            $dirPath = Yii::getAlias('@storage/web/requestLab/request/' . $ln . $dateTime . ".pet");
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

    private function fileRequestToRis($order_id, $dept, $pt_id) {
        $dataHeader = \backend\modules\thaihis\classes\ThaiHisQuery::getPtProfile($pt_id);
        $bdate = SDdate::dateTh2bod($dataHeader['pt_bdate']);
        $age = SDdate::getAge($bdate);
        $bdate = str_replace("/", "", $bdate);
        $sex = ($dataHeader['pt_sex'] == '1' ? 'M' : 'F');

        $date = date('Ymd');
        $time = date('His');
        //เหรียญชัย|พรหมศิริ|นาย|45208931|||45208931|19450324|M|20141111|085035|CR|แพทย์รวินท์ อิงศิโรรัตน์|3243379|CHest:PA.(Digital)|CH Dig
//$o = "{$dataHeader['pt_firstname']}|{$dataHeader['pt_lastname']}|{$dataHeader['prefix_name']}|{$dataHeader['pt_hn']}|||{$dataHeader['pt_hn']}|{$bdate}|{$sex}|{$date}|{$time}||{$doctorName}|{$dataHeader['ln']}|CHest:PA.(Digital)|CH Dig\n";
        $o = '';
        $no = 1;
        foreach ($order_id as $value) {
            $item = PatientQuery::getOrderTranById($value, '1');
            $o = "{$dataHeader['pt_firstname']}|{$dataHeader['pt_lastname']}|{$dataHeader['prefix_name']}|{$dataHeader['pt_hn']}|||{$dataHeader['pt_hn']}|{$bdate}|{$sex}|{$date}|{$time}|{$item['order_modality_type']}||{$value}|{$item['order_name']}|{$item['order_tran_code']}\r\n";
            $no++;

            $dirPath = Yii::getAlias('@storage/web/requestPacs/request/' . $value . ".txt");
            $objFopen = fopen($dirPath, 'w')or die("Unable to open file!");
            fwrite($objFopen, iconv('UTF-8', 'TIS-620', $o));
            fclose($objFopen);
        }
    }

    private function fileRequestToRisHL7($order_id, $dept, $pt_id) {
        $dataHeader = \backend\modules\thaihis\classes\ThaiHisQuery::getPtProfile($pt_id);
        $bdate = SDdate::dateTh2bod($dataHeader['pt_bdate']);
        $age = SDdate::getAge($bdate);
        $bdate = str_replace("/", "", $bdate);
        $sex = ($dataHeader['pt_sex'] == '1' ? 'M' : 'F');

        $datetime = date('YmdHis');
        $no = 1;
        //foreach ($order_id as $value) {
        $item = PatientQuery::getOrderTranById($order_id, '1');
        //$o = "{$dataHeader['pt_firstname']}|{$dataHeader['pt_lastname']}|{$dataHeader['prefix_name']}|
        //{$dataHeader['pt_hn']}|||{$dataHeader['pt_hn']}|{$bdate}|{$sex}|{$date}|{$time}|
        //{$item['order_modality_type']}||{$value}|{$item['order_name']}|{$item['order_tran_code']}\r\n";
        $acc_id = date('Ymd') . $item['acc_id'];

        $content = "MSH|^~\&|TRAKCARE|001|||{$datetime}||ORM^O01|1364|P|2.4|||AL|NE|||EN|\r\n";
        $content .= "EVN|O01|{$datetime}|||puntipa^ ^^^^^^|{$datetime}|001^01MED\r\n";
        $content .= "PID|||{$dataHeader['pt_hn']}||{$dataHeader['pt_firstname']}^{$dataHeader['pt_lastname']}^^{$dataHeader['prefix_name']}||{$bdate}|{$sex}|{$dataHeader['pt_hn']}^^^||^^^^^^^^^^||||||||||||||||||||\r\n";
        $content .= "PV1|1|I|^^^^^^||||||||||||||||I|||||||||||||||||||||||||||||||||\r\n";
        //$content .= "ORC|NW||||||||{$item['datetime']}||||||||||{$item['doctor']}|\r\n";
        $content .= "ORC|NW||||||||{$datetime}||||||||||{$item['doctor']}|\r\n";
        //$value = ACCESSIONNUMBER
        $content .= "OBR|||{$acc_id}|{$item['order_tran_code']}^{$item['order_name']}|||||||||||||||||||||I|||||||||||||||||||\r\n";
        $no++;

        $dirPath = Yii::getAlias('@storage/web/requestPacs2/request_ris/' . $acc_id . ".HL7");
        $objFopen = fopen($dirPath, 'w')or die("Unable to open file!");
        fwrite($objFopen, iconv('UTF-8', 'TIS-620', $content));
        fclose($objFopen);

        return $acc_id;
        //}
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

//            $pt_hn = '46103286';
//            $date = '2018-11-07';
            
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

    public function actionRightCounter() {
        $ezfVisit_id = \backend\modules\patient\Module::$formID['visit'];
        $ezfRight_id = \backend\modules\patient\Module::$formID['patientright'];
        $ezfRight_tbname = \backend\modules\patient\Module::$formTableName['patientright'];
        $sect_code = (isset(Yii::$app->user->identity->profile->attributes['department']) ? Yii::$app->user->identity->profile->attributes['department'] : null);

        Yii::$app->session['ezf_input'] = \backend\modules\ezforms2\classes\EzfQuery::getInputv2All();

        $date = isset($_POST['date'])?$_POST['date']:date('Y-m-d');
        
        $searchModel = PatientFunc::loadDataByTarget($ezfRight_id, $ezfRight_tbname);

        $searchModel['sitecode'] = $sect_code;
        $dataProvider = PatientFunc::getRightCounter($searchModel, Yii::$app->request->post(), $date);
        
        if (Yii::$app->getRequest()->isAjax) {
            $reloadDiv = Yii::$app->request->get('reloadDiv');
            return $this->renderAjax('_gridright', [
                        'ezfVisit_id' => $ezfVisit_id,
                        'ezfRight_id' => $ezfRight_id,
                        'dataProvider' => $dataProvider,
                        'searchModel' => $searchModel,
                        'reloadDiv' => $reloadDiv,
                        'date'=>$date
            ]);
        } else {
            return $this->render('rightcounter', [
                        'ezfVisit_id' => $ezfVisit_id,
                        'ezfRight_id' => $ezfRight_id,
                        'dataProvider' => $dataProvider,
                        'searchModel' => $searchModel,
                        'date'=>$date
            ]);
        }
    }

    public function actionRightPopup() {
        if (Yii::$app->getRequest()->isAjax) {
            $ezfVisit_id = \backend\modules\patient\Module::$formID['visit'];
            $ezfVisitTran_id = \backend\modules\patient\Module::$formID['visit_tran'];
            $ezfVisitTran_tbname = \backend\modules\patient\Module::$formTableName['visit_tran'];

            $pt_id = Yii::$app->request->get('pt_id');
            $visit_id = Yii::$app->request->get('visit_id');

            return $this->renderAjax('_rightshow', [
                        'ezfVisit_id' => $ezfVisit_id,
                        'pt_id' => $pt_id,
                        'visit_id' => $visit_id,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionRightDetail() {
        if (Yii::$app->getRequest()->isAjax) {
            $ezf_id = \backend\modules\patient\Module::$formID['patientright'];
            $ezf_tbname = \backend\modules\patient\Module::$formTableName['patientright'];
            $dataid = Yii::$app->request->get('dataid');

            $model = EzfUiFunc::loadTbData($ezf_tbname, $dataid);
            if ($model['right_code'] == 'PRO') {
                $zppn_id = PatientQuery::getprojectidByptid($model['right_pt_id']);
                if ($zppn_id) {
                    $ezfproject_patient_name_id = \backend\modules\patient\Module::$formID['project_patient_name'];
                    $ezfproject_patient_name_tbname = \backend\modules\patient\Module::$formTableName['project_patient_name'];
                    $zppndata['status_project'] = '1';
                    PatientFunc::saveDataNoSys($ezfproject_patient_name_id, $ezfproject_patient_name_tbname, $zppn_id['id'], $zppndata);
                }
            }

            return $this->renderAjax('_right_detail', [
                        'ezf_id' => $ezf_id,
                        'model' => $model,
                        'dataid' => $dataid,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionOrderPtname($order_id, $order_status) {
        if (Yii::$app->getRequest()->isAjax) {
            $data = \backend\modules\thaihis\classes\OrderQuery::getOrderTranFullnameById($order_id);
            $data['pt_age'] = \backend\modules\thaihis\classes\ThaiHisQuery::calAge($data['pt_bdate']);//SDdate::getAge(SDdate::dateTh2bod($data['pt_bdate']));
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $data;
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public static function checkOrderCancelStatus($visit_id, $order_code) {
        $ezf_id = \backend\modules\patient\Module::$formID['order_tran'];
        $ezf_table = \backend\modules\patient\Module::$formTableName['order_tran'];
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

}
