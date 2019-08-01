<?php

namespace backend\modules\patient\controllers;

use Yii;
use backend\modules\patient\classes\PatientQuery;
use backend\modules\ezforms2\classes\EzfUiFunc;
use backend\modules\patient\classes\PatientFunc;
use backend\modules\patient\classes\CashierQuery;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use backend\modules\pis\classes\PisQuery;
use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\ezforms2\classes\EzfFunc;

class Cashier2Controller extends \yii\web\Controller {

    public function actionIndex() {
        $reloadDiv = Yii::$app->request->get('reloadDiv');
        $visit_id = Yii::$app->request->get('visit_id');
        $target = Yii::$app->request->get('target');
        $options = Yii::$app->request->get('options');
        $options = \backend\modules\ezforms2\classes\EzfFunc::stringDecode2Array($options);

        $configs = $options['configs'];
        $items = $options['items'];
        $params = $options['params'];

        return $this->renderAjax('index', [
                    'reloadDiv' => $reloadDiv,
                    'visit_id' => $visit_id,
                    'target' => $target,
                    'configs' => $configs,
                    'items' => $items
                    , 'params' => $params,
        ]);
    }

    public function actionReceiveShowDetail() {
        if (Yii::$app->getRequest()->isAjax) {
            $configs = EzfFunc::stringDecode2Array(Yii::$app->request->get('configs'));
            $items = EzfFunc::stringDecode2Array(Yii::$app->request->get('items'));
            $params = EzfFunc::stringDecode2Array(Yii::$app->request->get('params'));
            $unit_id = Yii::$app->request->get('unit_id');
//            $modelFilter = [];
//
//            if (isset($configs) && is_array($configs)) {
//                foreach ($configs as $key => $val) {
//                    $fields[$key] = isset($val['fields']) ? $val['fields'] : null;
//                    $forms[$key] = isset($val['refform']) ? $val['refform'] : null;
//                    $main_form[$key] = isset($val['ezf_id']) ? $val['ezf_id'] : null;
//                    $conditions[$key] = isset($val['conditions']) ? $val['conditions'] : null;
//                    $summarys[$key] = isset($val['summarys']) ? $val['summarys'] : null;
//                    $select = isset($val['selects']) ? $val['selects'] : null;
//                    $selects[$key] = $select;
//                    $group_field[$key] = isset($val['group_field']) ? $val['group_field'] : null;
//                    $cashier_status_field[$key] = isset($val['cashier_status']) ? $val['cashier_status'] : null;
//                    $visit_field[$key] = isset($val['visit_field']) ? $val['visit_field'] : null;
//                    $image_field[$key] = isset($val['image_field']) ? $val['image_field'] : null;
//                    $fields_search[$key] = isset($val['fields_search']) ? $val['fields_search'] : null;
//
//                    $ezform[$key] = EzfQuery::getEzformOne($val['ezf_id']);
//
//                    $modelFilter[$key][] = [$ezform[$key]['ezf_table'] . '.target' => $visit_id];
//                    if ($visit_field != null) {
//                        $modelFilter[$key][] = [$val['visit_field'] => $visit_id];
//                    }
//
//                    if ($cashier_status_field != null) {
//                        if ($params['cashier_status'] == '1') {
//                            $modelFilter[$key][] = $val['cashier_status'] . "='" . $params['cashier_status'] . "'";
//                        } else {
//                            $modelFilter[$key][] = $val['cashier_status'] . "='" . $params['cashier_status'] . "'";
//                            $modelFilter[$key][] = $val['receipt_id'] . "='" . $params['receipt_id'] . "'";
//                        }
//                    }
//                    if (is_array($select)) {
//                        foreach ($select as $field) {
//                            if (isset($field['field']))
//                                $fields[$key][] = $field['field'];
//                        }
//                    }
//                    if (isset($val['order_by'])) {
//                        foreach ($val['order_by']['field'] as $field) {
//                            $field = substr($field, strpos($field, ".") + 1);
//                            $orderby[] = $field . ' ' . $val['order_by']['sort'];
//                        }
//                    }
//                }
//            }
//            $orderby = implode(",", $orderby);
            //$reponseQuery = PisQuery::getDynamicUnoinQuery($fields, $forms, $ezform, $conditions, $summarys, $image_field, $modelFilter, $group_field, $orderby);
            $reponseQuery = [];
            if (isset($params['visitid']) && isset($params['cashier_status']) && $params['visitid'] && $params['cashier_status']) {
                $cashier_id = isset($params['receipt_id']) ? $params['receipt_id'] : null;
                if ($params['cashier_status'] == '1') {
                    $reponseQuery = CashierQuery::getCashierGroupItem($params['visitid'], $params['cashier_status'], $cashier_id, $unit_id);
                } elseif ($params['cashier_status'] == '2') {
                    $reponseQuery = CashierQuery::getCashierGroupItem2($params['receipt_id']);
                }
            } else {
                return \yii\helpers\Html::tag('div', 'เกิดข้อผิดพลาด ไม่พบ Visit_id และ Cashier_status', ['class' => 'alert alert-danger']);
            }

            return $this->renderAjax('_gridcashier', [
                        'visit_id' => $params['visitid'],
//                        'data' => $reponseQuery['data'],
                        'data' => $reponseQuery,
                        'params' => $params,
                        'items' => $items,
                        'unit_id' => $unit_id,
                        'cashier_id' => $cashier_id
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionReceiveShowDept() {
        $configs = EzfFunc::stringDecode2Array(Yii::$app->request->get('configs'));
        $items = EzfFunc::stringDecode2Array(Yii::$app->request->get('items'));
        $params = EzfFunc::stringDecode2Array(Yii::$app->request->get('params'));

        if (isset($params['visitid']) && isset($params['cashier_status']) && $params['visitid'] && $params['cashier_status']) {
            $data = CashierQuery::getCashierOrderDept($params['visitid'], $params['cashier_status']);
        } else {
            return \yii\helpers\Html::tag('div', 'เกิดข้อผิดพลาด ไม่พบ Visit_id และ Cashier_status', ['class' => 'alert alert-danger']);
        }

        return $this->renderAjax('_gridcashier_dept', [
                    'visit_id' => $params['visitid'],
                    'data' => $data,
                    'cashier_status' => $params['visitid'],
                    'params' => EzfFunc::arrayEncode2String($params),
        ]);
    }

    public function actionReceiptNo() {
        if (Yii::$app->getRequest()->isAjax) {
            $ezf_id = \backend\modules\patient\Module::$formID['receipt_no'];
            $reloadDiv = Yii::$app->request->get('reloadDiv');
            $user_id = Yii::$app->request->get('user_id');

            $data = CashierQuery::getReceiptNo($user_id);

            return $this->renderAjax('_receipt_no', [
                        'data' => $data,
                        'reloadDiv' => $reloadDiv,
                        'ezf_id' => $ezf_id, 'user_id' => $user_id
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionCashierReceiveItemGroup() {
        if (Yii::$app->getRequest()->isAjax) {
            $visit_id = Yii::$app->request->get('visit_id');
            $fin_code = Yii::$app->request->get('fin_code');

            $data = PatientQuery::getCashierItem($visit_id, $fin_code);

            return $this->renderAjax('_item_group', [
                        'visit_id' => $visit_id,
                        'fin_code' => $fin_code,
                        'data' => $data,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionSaveReceipt() {
        if (Yii::$app->request->post()) {
            $ezfMas_id = \backend\modules\patient\Module::$formID['receipt_mas'];
            $ezfTrn_id = \backend\modules\patient\Module::$formID['receipt_trn'];
            $ezfOrder_id = \backend\modules\patient\Module::$formID['order_tran'];
            $ezfPisTran_id = \backend\modules\patient\Module::$formID['pis_order_tran'];
            $order = Yii::$app->request->post('order_check', []);
            $visit_id = Yii::$app->request->get('visit_id');
            $sum = ['notpay' => 0, 'pay' => 0, 'item_code' => '', 'item_id' => '', 'type' => ''];
            $result_mas = '';

            foreach ($order as $key => $arrValue) {
                if (isset($sum[$key]))
                    $sum[$key] = 0;
                if ($key == 'pay' || $key == 'notpay') {
                    foreach ($arrValue as $value) {
                        $sum[$key] += $value;
                    }
                }
            }
            $totalPay = (float) str_replace(",", "", Yii::$app->request->post('total_pay'));
            if (is_numeric($totalPay) && $totalPay > 0) { //ถ้ายอด pay>0 ต้องใส่เลขที่ใบเสร็จ update book number
                $initdata['book_no'] = Yii::$app->request->post('receipt_book_no');
                $initdata['book_num'] = Yii::$app->request->post('receipt_tr_no');
                $receipt_dataid = Yii::$app->request->post('receipt_no_id');
                $ezfReceiptNo_id = \backend\modules\patient\Module::$formID['receipt_no'];
                PatientFunc::saveDataNoSys($ezfReceiptNo_id, 'zdata_receipt_no', $receipt_dataid, ['receipt_book_no' => $initdata['book_no'], 'receipt_tr_no' => $initdata['book_num'] + 1]);
            }
            $initdata['receipt_status'] = 'A';
            $initdata['receipt_pay'] = $sum['pay'];
            $initdata['receipt_notpay'] = $sum['notpay'];
            $initdata['receipt_reciveMony'] = Yii::$app->request->post('ReciveMony');
            $initdata['receipt_tronmony'] = Yii::$app->request->post('Tronmony');
            $initdata['receipt_type_mony'] = Yii::$app->request->post('type_mony');
            $initdata['receipt_credit_id'] = Yii::$app->request->post('credit_id');
            $initdata['receipt_patientright_id'] = Yii::$app->request->post('right_id');
            $initdata['receipt_right_code'] = Yii::$app->request->post('receipt_right_code');
            $initdata['receipt_right_sub_code'] = Yii::$app->request->post('receipt_right_sub_code');
            $initdata['receipt_project_id'] = Yii::$app->request->post('receipt_project_id');
            $initdata['receipt_send_status'] = '1'; //send to serene status
            $result_mas = EzfUiFunc::backgroundInsert($ezfMas_id, '', $visit_id, $initdata)['data'];
            $initdata = null;

            $i = 0;
            $importDrug = FALSE;
            foreach ($order['item_code'] as $arrValue) {
                if ($order['type'][$i] == 'ORDER') {
                    $initdata['tran_item_code'] = $arrValue;
                    $initdata['tran_item_pay'] = $order['pay'][$i];
                    $initdata['tran_item_notpay'] = $order['notpay'][$i];
                    PatientFunc::backgroundInsert($ezfTrn_id, '', $result_mas['id'], $initdata);
                    PatientFunc::saveDataNoSys($ezfOrder_id, 'zdata_order_tran', $order['item_id'][$i], ['order_tran_cashier_status' => '2', 'order_tran_cashier_id' => $result_mas['id']]);
                } else {
                    $data = PatientQuery::getCashierItemDrugToReceipt($visit_id, $arrValue, '1');
                    if ($data) {
                        $initdata['tran_item_code'] = $arrValue;
                        $initdata['tran_item_pay'] = $order['pay'][$i];
                        $initdata['tran_item_notpay'] = $order['notpay'][$i];
                        PatientFunc::backgroundInsert($ezfTrn_id, '', $result_mas['id'], $initdata);
                        foreach ($data as $value) {
                            PatientFunc::saveDataNoSys($ezfPisTran_id, 'zdata_pis_order_tran', $value['order_tran_id'], ['order_tran_cashier_status' => '2', 'order_tran_cashier_id' => $result_mas['id']]);
                        }
                    }
                }
                $i++;
            }
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $result_mas;
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionCancelReceipt($receipt_id) {
        if (Yii::$app->request->post()) {
            $ezfOrder_id = \backend\modules\patient\Module::$formID['order_tran'];
            $ezfOrder_table = \backend\modules\patient\Module::$formTableName['order_tran'];
            $ezfPisTran_id = \backend\modules\patient\Module::$formID['pis_order_tran'];
            $ezfPisTran_table = \backend\modules\patient\Module::$formTableName['pis_order_tran'];
            $ezfMas_id = \backend\modules\patient\Module::$formID['receipt_mas'];
            $ezfMas_table = \backend\modules\patient\Module::$formTableName['receipt_mas'];

            $params = EzfFunc::stringDecode2Array(Yii::$app->request->get('params'));
            $comment_cancel = Yii::$app->request->post('comment', 'No Comment');
            $order = Yii::$app->request->post('order_check', []);

            $data = CashierQuery::getReceiptDetail($receipt_id);
            if ($data) {
                $result = PatientFunc::saveDataNoSys($ezfMas_id, $ezfMas_table, $data[0]['receipt_id'], ['receipt_status' => 'C',
                            'receipt_cancel_comment' => $comment_cancel, 'receipt_send_status' => (date('Y-m-d') !== $data[0]['visit_date'] ? '3' : '1')]);
                foreach ($data as $value) {
                    if (!in_array($value['fin_group_code'], ['3', '5', '2'])) {
                        //if ($value['fin_group_code'] !== '3') { //ยกเลิก order ถ้าไม่ใช้หมวด 3,5
                        PatientFunc::saveDataNoSys($ezfOrder_id, $ezfOrder_table, $value['order_tran_id'], ['order_tran_cashier_status' => '1', 'order_tran_cashier_id' => '']);
                    } else {
                        //ยกเลิก 3,5 ยา ค้นหาจากกลุ่มมา loop ยกเลิก
                        $dataPis = PatientFunc::loadTbDataByField($ezfPisTran_table, ['order_tran_cashier_id' => $receipt_id], 'all');
                        if ($dataPis) {
                            foreach ($dataPis as $pisValue) {
                                PatientFunc::saveDataNoSys($ezfPisTran_id, $ezfPisTran_table, $pisValue['id'], ['order_tran_cashier_status' => '1', 'order_tran_cashier_id' => '']);
                            }
                        }
                    }
                }
            }

            return $this->redirect(['/ezmodules/ezmodule/view',
                        //fix เลข module เพื่อให้ใช้ได้ไปก่อน
                        'id' => '1537418246057501400', //$params['ezm_id'],
                        //'target' => $params['target'], 'visitid' => $params['visitid'],
                        'cashier_status' => '1', 'search_field[order_tran_cashier_status]' => '1'
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionPrintReceipt2($receipt_id, $right_code) {
        $data = CashierQuery::getReceiptDetail($receipt_id, $right_code);
        if (empty($data[0]['book_no']) || $data[0]['book_no'] == '000') {
            return false;
        }                                   //('P', PDF_UNIT, 'A4', true, 'UTF-8', false);
        $pdf = new \common\lib\tcpdf\SDPDF('P', PDF_UNIT, [205, 300], true, 'UTF-8', false);
        $pdf->SetCreator('AppXQ');
        $pdf->SetAuthor('iencoded@gmail.com');
        $pdf->SetTitle('ใบเสร็จรับเงิน ' . $data[0]['fullname']);
        $pdf->SetSubject('Original');
        $pdf->SetKeywords('AppXQ, SDII, PDF, report, medical,');
        $pdf->data = $data;

        // remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // set margins
        $pdf->SetMargins(10, 37, 0, TRUE);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, 15);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set font
        $pdf->SetFont($pdf->fontName, '', $pdf->fontSize);
        $pdf->fontSize = 18;

        // add a page
        $pdf->AddPage();
        $this->receiptContent2($pdf, $data, $right_code);

        $pdf->Output('receipt.pdf', 'I');
        Yii::$app->end();
    }

    private function receiptContent2($pdf, $data, $right_code) {
        //content
        $pdf->SetFont('thsarabunpsk', 'B', 16);
        $pdf->Cell(155, 0, '', 0, 0, 'R', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(50, 0, $data[0]['pt_hn'], 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont('thsarabunpsk', 'B', 14);
        $pdf->ln(4);
        $pdf->Cell(100, 0, '', 0, 0, 'R', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(100, 0, \backend\modules\thaihis\classes\ThaiHisFunc::mysql2phpThDateMonthTime($data[0]['create_date']), 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->ln(3);
        $pdf->Cell(150, 0, '', 0, 0, 'R', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(50, 0, $data[0]['fullname'], 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->ln(23);
        $w = array(15, 134, 40, 23, 74);
        $sum = 0;
        $i = 1;
        $chkGroup = '';
        $countdatagroup = '';
        $k = 1;
        $l = 1;
        $notpayall = 0;
        $sumnotpaycolum = 0;
        $sumpaycolum = 0;
        if ($data[0]['visit_type'] == 1) {
            $yearTH = date('Y') + 543;
            $pdf->Cell(133, 5, ' ค่าตรวจสุขภาพปี ' . $yearTH, 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        }
        foreach ($data as $value) {
            //เปลี่ยนค่าเงินต้องจ่ายในสิทธิต่างๆ ยกเว้น ORI
            if (in_array($data[0]['right_code'], ['ORI', 'ORI-G', 'CASH', null])) {
                $value['tran_item_notpay'] = $value['tran_item_notpay'];
            } else {
                $value['tran_item_notpay'] = '';
            }

            if ($chkGroup !== $value['order_fin_name']) {
                $countdatagroup = '';
                $k = 1;
                $l = 1;
                $pdf->SetFont('thsarabunpsk', 'B', 14);
                if (in_array($value['fin_group_code'], ['3', '5', '2'])) {
                    $resultgroup = ['pay' => $value['tran_item_pay'], 'notpay' => $value['tran_item_notpay'], 'countdatagroup' => '1'];
                } else {
                    $resultgroup = CashierQuery::getReceiptGroupDetail($value['receipt_id'], $value['order_fin_code'], $right_code);
                }

                $pdf->Cell(133, 5, ' ' . $value['order_fin_name'], 0, 0, 'L', 0, '', 0, false, 'T', 'M');
                $pdf->SetFont('thsarabunpsk', 'B', 12);
                $pdf->Cell($w[3] + 5, 5, $this->Checknotequezero($resultgroup['pay']), 0, 0, 'R', 0, '', 0, false, 'T', 'M');
                $pdf->Cell($w[3] + 3, 5, $this->Checknotequezero($resultgroup['notpay']), 0, 1, 'R', 0, '', 0, false, 'T', 'M');
                $sumnotpaycolum += $resultgroup['notpay'];
                $sumpaycolum += $resultgroup['pay'];

                $chkGroup = $value['order_fin_name'];
                $countdatagroup = $resultgroup['countdatagroup'];
                $i++;
            }
            if ($countdatagroup > 1) {
                if ($k == 2) {
                    $pdf->SetFont('thsarabunpsk', 'B', 11);
                    $pdf->Cell($w[4], 5, '    ' . $value['nhso_code'] . $countdatagroup . ' ' . '  ' . $value['order_name'] . ' ' . $this->Checknotequezero($value['tran_item_pay']) . ' ' . $this->Checknotequezero($value['tran_item_notpay']), '', 0, 'L', 0, '', 0, false, 'T', 'M');
                    $pdf->Cell($w[3], 5, '', '', 0, 'R', 0, '', 0, false, 'T', 'M');
                    $pdf->Cell($w[3], 5, '', '', 1, 'R', 0, '', 0, false, 'T', 'M');
                    $sum += $value['tran_item_pay'];
                    $k = 1;
                    $l++;
                } else if ($countdatagroup == $l) {
                    $pdf->SetFont('thsarabunpsk', 'B', 11);
                    $pdf->Cell($w[4], 5, '     ' . $value['nhso_code'] . ' ' . $value['order_name'] . ' ' . $this->Checknotequezero($value['tran_item_pay']) . ' ' . $this->Checknotequezero($value['tran_item_notpay']), '', 0, 'L', 0, '', 0, false, 'T', 'M');
                    if (!$countdatagroup % 2 == 0) {
                        $pdf->Cell(74, 5, '', '', 0, 'R', 0, '', 0, false, 'T', 'M');
                        $pdf->Cell($w[3], 5, '', '', 0, 'R', 0, '', 0, false, 'T', 'M');
                        $pdf->Cell($w[3], 5, '', '', 1, 'R', 0, '', 0, false, 'T', 'M');
                    }
                    $sum += $value['tran_item_pay'];
                    $l = 1;
                    $k++;
                } else {
                    $pdf->SetFont('thsarabunpsk', 'B', 11);
                    $pdf->Cell($w[4], 5, '     ' . $value['nhso_code'] . ' ' . $value['order_name'] . ' ' . $this->Checknotequezero($value['tran_item_pay']) . ' ' . $this->Checknotequezero($value['tran_item_notpay']), '', 0, 'L', 0, '', 0, false, 'T', 'M');
                    $sum += $value['tran_item_pay'];
                    $l++;
                    $k++;
                }
            } else {
                $pdf->SetFont('thsarabunpsk', 'B', 11);
                $pdf->Cell(148, 5, '     ' . $value['nhso_code'] . ' ' . $value['order_name'] . ' ' . $this->Checknotequezero($value['tran_item_pay']) . ' ' . $this->Checknotequezero($value['tran_item_notpay']), '', 0, 'L', 0, '', 0, false, 'T', 'M');
                $pdf->Cell($w[3], 5, '', '', 0, 'R', 0, '', 0, false, 'T', 'M');
                $pdf->Cell($w[3], 5, '', '', 1, 'R', 0, '', 0, false, 'T', 'M');
                $sum += $value['tran_item_pay'];
            }
        }

        $pdf->SetY(-60);
        $pdf->SetFont('thsarabunpsk', 'B', 14);
        $number_text = new \backend\modules\patient\classes\NumberThai();
        $pdf->Cell(138, 7, '    ' . 'ชำระเงินด้วย' . $data[0]['receipt_type_mony'] . ' ' . $this->Checknotequezero($data[0]['receipt_reciveMony']) . ' บาท ทอน ' . ($data[0]['receipt_tronmony'] == 0 ? '0' : $this->Checknotequezero($data[0]['receipt_tronmony'])) . ' บาท', 0, 0, 'L', 0, '', 0, false, 'T', 'M');

        //sum foot pay,notpay
        $pdf->Cell($w[3] + 6, 7, $this->Checknotequezero($sumpaycolum), 0, 0, 'R', 0, '', 0, false, 'T', 'M');
        $pdf->Cell($w[3] + 2, 7, $this->Checknotequezero($sumnotpaycolum), 0, 1, 'R', 0, '', 0, false, 'T', 'M');
        if (in_array($data[0]['right_code'], ['ORI', 'ORI-G', 'CASH', null])) {
            $sum = $sumpaycolum + $sumnotpaycolum;
        }
        $pdf->Cell(30, 10, '', 0, 0, 'C', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(118, 10, $number_text->convertBaht(number_format($sum, 2, ".", "")), 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(46, 10, number_format($sum, 2, ".", ","), 0, 1, 'C', 0, '', 0, false, 'T', 'M');
        $pdf->ln(20);
        $pdf->Cell(185, 0, $data[0]['cashier_fullname'], 0, 1, 'R', 0, '', 0, false, 'T', 'M');
    }

    public function actionPrintReceipt($receipt_id, $right_code) {
        $data = CashierQuery::getReceiptDetail($receipt_id, $right_code);
        if (empty($data[0]['book_no']) || $data[0]['book_no'] == '000') {
            return false;
        }
        $pdf = new \common\lib\tcpdf\SDPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);
        $pdf->SetCreator('AppXQ');
        $pdf->SetAuthor('iencoded@gmail.com');
        $pdf->SetTitle('ใบเสร็จรับเงิน ' . $data[0]['fullname']);
        $pdf->SetSubject('Original');
        $pdf->SetKeywords('AppXQ, SDII, PDF, report, medical,');

        // remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // set margins
        $pdf->SetMargins(10, 5, 0, TRUE);
        $pdf->SetHeaderMargin(5);
        $pdf->SetFooterMargin(5);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, 15);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set font
        $pdf->SetFont($pdf->fontName, '', $pdf->fontSize);
        $pdf->fontSize = 18;

        // add a page
        $pdf->AddPage();
        $this->receiptHeader('ต้นฉบับ', $pdf, $data);
        $this->receiptContent($pdf, $data, $right_code);
        $pdf->AddPage();
        $this->receiptHeader('สำเนา', $pdf, $data);
        $this->receiptContent($pdf, $data, $right_code);

        $pdf->Output('receipt.pdf', 'I');
        Yii::$app->end();
    }

    private function receiptHeader($Htext, $pdf, $data) {
        //ezform Hospital Config
        $dataid = \backend\modules\patient\Module::$dataidForm['hos_config'];
        $ezf_table = \backend\modules\patient\Module::$formTableName['hos_config'];
        $dataHosConfig = EzfUiFunc::loadTbData($ezf_table, $dataid);
        if ($dataHosConfig['logo_05']) {
            $path = Yii::getAlias('@storageUrl/ezform/fileinput') . '/' . $dataHosConfig['logo_06'];
            $TypeName = "jpeg";
        } else {
            $path = Yii::getAlias('@storageUrl/images') . '/nouser.png';
            $TypeName = "png";
        }

        $pdf->Image($path, 92.5, 8, 25, 25, '');
        $pdf->Ln(4);
        $pdf->SetFont('thsarabunpsk', '', 16);
        $pdf->Cell(170, 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(15, 0, '', 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont('thsarabunpsk', 'B', 16);
        $pdf->Cell(160, 0, 'เล่มที่ : ' . $data[0]['book_no'], 0, 1, 'R', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(160, 0, 'เลขที่ : ' . $data[0]['book_num'], 0, 1, 'R', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(160, 0, 'วันที่ ' . \appxq\sdii\utils\SDdate::mysql2phpThDateTime($data[0]['create_date']), 0, 1, 'R', 0, '', 0, false, 'T', 'M');

        // $pdf->Ln(1);
        $pdf->Cell(115, 0, "ใบเสร็จรับเงิน ({$Htext})", 0, 0, 'R', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(115, 0, "   เลขที่ผู้เสียภาษี 0994000363613", 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont('thsarabunpsk', 'B', 16);
        //$pdf->Cell(20, 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(185, 0, 'โรงพยาบาลมะเร็งอุดรธานี', 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(185, 0, '36 หมู่ที่ 1 ถนนอุดร-ขอนแก่น ต.หนองไผ่ อ.เมืองอุดรธานี จ.อุดรธานี 41330 โทร 0-4220-7375-80', 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        //$pdf->Ln(3);
        $pdf->Cell(185, 0, 'HN : ' . $data[0]['pt_hn'] . 'ชื่อ - นามสกุล    ' . $data[0]['fullname'], 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(185, 0, 'วันที่เข้ารับบริการ ' . \appxq\sdii\utils\SDdate::mysql2phpThDate($data[0]['visit_date']) . '  ' . PatientFunc::visit_type($data[0]['visit_type']), 0, 1, 'L', 0, '', 0, false, 'T', 'M');
    }

    private function receiptContent($pdf, $data, $right_code) {
//        $user_id = Yii::$app->user->identity->id;
//        $dataProfile = PatientQuery::getProfileuserByid($user_id);
        //content
        $w = array(15, 134, 40, 23, 74);
        // 'TBR'
        $pdf->SetFont('thsarabunpsk', 'B', 16);
        //  $pdf->Cell($w[0], 0, '', 'TL', 0, 'C', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(148, 0, 'รายการ / DESCRIPTION', 'TRL', 0, 'C', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(46, 0, 'จำนวนเงิน (บาท / BAHT)', 'TBR', 1, 'C', 0, '', 0, false, 'T', 'M');

        // $pdf->Cell($w[0], 0, '', 'BL', 0, 'C', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(148, 0, '', 'BL', 0, 'C', 0, '', 0, false, 'T', 'M');
        $pdf->Cell($w[3], 0, 'เบิกไม่ได้', 'BLR', 0, 'C', 0, '', 0, false, 'T', 'M');
        $pdf->Cell($w[3], 0, 'เบิกได้', 'BLR', 1, 'C', 0, '', 0, false, 'T', 'M');
        $sum = 0;
        $i = 1;
        $chkGroup = '';
        $countdatagroup = '';
        $k = 1;
        $l = 1;
        $notpayall = 0;
        $sumnotpaycolum = 0;
        $sumpaycolum = 0;
        foreach ($data as $value) {
            //เปลี่ยนค่าเงินต้องจ่ายในสิทธิต่างๆ ยกเว้น ORI
            if (in_array($data[0]['right_code'], ['ORI', 'ORI-G', 'CASH', null])) {
                $value['tran_item_notpay'] = $value['tran_item_notpay'];
            } else {
                $value['tran_item_notpay'] = '';
            }
            
            if ($chkGroup !== $value['order_fin_name']) {
                $countdatagroup = '';
                $k = 1;
                $l = 1;
                $pdf->SetFont('thsarabunpsk', 'B', 15);

                if (in_array($value['fin_group_code'], ['3', '5'])) {
                    $resultgroup = ['pay' => $value['tran_item_pay'], 'notpay' => $value['tran_item_notpay'], 'countdatagroup' => '1'];
                } else {
                    $resultgroup = CashierQuery::getReceiptGroupDetail($value['receipt_id'], $value['order_fin_code'], $right_code);
                }

                $pdf->Cell(148, 5, ' ' . $value['order_fin_name'], 'LR', 0, 'L', 0, '', 0, false, 'T', 'M');

                $pdf->Cell($w[3], 5, $this->Checknotequezero($resultgroup['pay']), 'R', 0, 'R', 0, '', 0, false, 'T', 'M');
                $pdf->Cell($w[3], 5, $this->Checknotequezero($resultgroup['notpay']), 'R', 1, 'R', 0, '', 0, false, 'T', 'M');
                $sumnotpaycolum += $resultgroup['notpay'];
                $sumpaycolum += $resultgroup['pay'];

                $chkGroup = $value['order_fin_name'];
                $countdatagroup = $resultgroup['countdatagroup'];
                $i++;
            }
            if ($countdatagroup > 1) {
                if ($k == 2) {
                    $pdf->SetFont('thsarabunpsk', 'B', 12);
                    $pdf->Cell($w[4], 5, '    ' . $value['nhso_code'] . $countdatagroup . ' ' . '  ' . $value['order_name'] . ' ' . $this->Checknotequezero($value['tran_item_pay']) . ' ' . $this->Checknotequezero($value['tran_item_notpay']), '', 0, 'L', 0, '', 0, false, 'T', 'M');
                    $pdf->Cell($w[3], 5, '', 'LR', 0, 'R', 0, '', 0, false, 'T', 'M');
                    $pdf->Cell($w[3], 5, '', 'R', 1, 'R', 0, '', 0, false, 'T', 'M');
                    $sum += $value['tran_item_pay'];
                    $k = 1;
                    $l++;
                } else if ($countdatagroup == $l) {
                    $pdf->SetFont('thsarabunpsk', 'B', 12);
                    $pdf->Cell($w[4], 5, '     ' . $value['nhso_code'] . ' ' . $value['order_name'] . ' ' . $this->Checknotequezero($value['tran_item_pay']) . ' ' . $this->Checknotequezero($value['tran_item_notpay']), 'L', 0, 'L', 0, '', 0, false, 'T', 'M');
                    if (!$countdatagroup % 2 == 0) {
                        $pdf->Cell(74, 5, '', 'R', 0, 'R', 0, '', 0, false, 'T', 'M');
                        $pdf->Cell($w[3], 5, '', 'R', 0, 'R', 0, '', 0, false, 'T', 'M');
                        $pdf->Cell($w[3], 5, '', 'R', 1, 'R', 0, '', 0, false, 'T', 'M');
                    }
                    $sum += $value['tran_item_pay'];
                    $l = 1;
                    $k++;
                } else {
                    $pdf->SetFont('thsarabunpsk', 'B', 12);
                    $pdf->Cell($w[4], 5, '     ' . $value['nhso_code'] . ' ' . $value['order_name'] . ' ' . $this->Checknotequezero($value['tran_item_pay']) . ' ' . $this->Checknotequezero($value['tran_item_notpay']), 'L', 0, 'L', 0, '', 0, false, 'T', 'M');
                    $sum += $value['tran_item_pay'];
                    $l++;
                    $k++;
                }
            } else {
                $pdf->SetFont('thsarabunpsk', 'B', 12);
                $pdf->Cell(148, 5, '     ' . $value['nhso_code'] . ' ' . $value['order_name'] . ' ' . $this->Checknotequezero($value['tran_item_pay']) . ' ' . $this->Checknotequezero($value['tran_item_notpay']), 'RL', 0, 'L', 0, '', 0, false, 'T', 'M');
                $pdf->Cell($w[3], 5, '', 'R', 0, 'R', 0, '', 0, false, 'T', 'M');
                $pdf->Cell($w[3], 5, '', 'R', 1, 'R', 0, '', 0, false, 'T', 'M');
                $sum += $value['tran_item_pay'];
            }
        }

        $pdf->SetFont('thsarabunpsk', 'B', 16);
        $number_text = new \backend\modules\patient\classes\NumberThai();
        $pdf->Cell(148, 5, '    ' . 'ชำระเงินด้วย' . $data[0]['receipt_type_mony'] . ' ' . $this->Checknotequezero($data[0]['receipt_reciveMony']) . ' บาท ทอน ' . ($data[0]['receipt_tronmony'] == 0 ? '0' : $this->Checknotequezero($data[0]['receipt_tronmony'])) . ' บาท', 'RL', 0, 'L', 0, '', 0, false, 'T', 'M');

        //sum foot pay,notpay
        $pdf->Cell($w[3], 5, $this->Checknotequezero($sumpaycolum), 'R', 0, 'R', 0, '', 0, false, 'T', 'M');
        $pdf->Cell($w[3], 5, $this->Checknotequezero($sumnotpaycolum), 'R', 1, 'R', 0, '', 0, false, 'T', 'M');
        if (in_array($value['right_code'], ['ORI', 'ORI-G', 'CASH', null])) {
            $sum = $sumpaycolum + $sumnotpaycolum;
        }
        $pdf->Cell(148, 0, 'ตัวอักษร   (' . $number_text->convertBaht(number_format($sum, 2, ".", "")) . ')   รวมทั้งสิ้น ', 'LTBR', 0, 'R', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(46, 0, number_format($sum, 2, ".", ","), 'TBR', 1, 'C', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(90, 0, 'ได้รับเงินไว้เป็นการถูกต้องแล้ว', 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(120, 0, 'ลงชื่อ', 0, 0, 'R', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(45, 0, '.............................................', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(10, 0, ' ผู้รับเงิน ', 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(120, 0, '', 0, 0, 'R', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(45, 0, '(' . $data[0]['cashier_fullname'] . ')', 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(135, 0, 'ตำแหน่ง', 0, 0, 'R', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(55, 0, 'เจ้าหน้าที่การเงิน', 0, 1, 'L', 0, '', 0, false, 'T', 'M');
    }

    public function actionPrintReceiptDetail($receipt_id) {
        $data = CashierQuery::getReceiptDetail($receipt_id);
        // print_r($data);
        $pdf = new \common\lib\tcpdf\SDPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);
        $pdf->SetCreator('AppXQ');
        $pdf->SetAuthor('iencoded@gmail.com');
        $pdf->SetTitle('รายละเอียดค่าใช้จ่าย' . $data[0]['fullname']);
        $pdf->SetSubject('Original');
        $pdf->SetKeywords('AppXQ, SDII, PDF, report, medical,');

        // remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(TRUE);

        // set margins
        $pdf->SetMargins(10, 5, 0, TRUE);
        $pdf->SetHeaderMargin(5);
        $pdf->SetFooterMargin(5);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, 15);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set font
        $pdf->SetFont($pdf->fontName, '', $pdf->fontSize);
        $pdf->fontSize = 18;

        // add a page
        $pdf->AddPage();
        $this->receiptHeaderDetail($pdf);
        $this->receiptContentDetail($pdf, $data);

        $pdf->Output('report.pdf', 'I');
        Yii::$app->end();
    }

    private function receiptHeaderDetail($pdf) {
        $path = Yii::getAlias('@storageUrl/images') . '/logo4.jpg';
        $pdf->Image($path, 0, 8, 45, 30, 'JPG');
        $pdf->Ln(5);
        $pdf->SetFont('thsarabunpsk', 'B', 20);
        $pdf->Cell(30, 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(150, 0, 'UDONTHANI CANCER HOSPITAL', 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(30, 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(150, 0, 'โรงพยาบาลมะเร็งอุดรธานี กรมการแพทย์ กระทรวงสาธารณสุข', 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont('thsarabunpsk', 'B', 16);
        $pdf->Cell(30, 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(150, 0, '36 หมู่ 1 หลัก ถนนอุดร - ขอนแก่น ตำบลหนองไผ่ อำเภอเมือง จังหวัดอุดรธานี 41330', 0, 1, 'L', 0, '', 0, false, 'T', 'M');
    }

    private function receiptContentDetail($pdf, $data) {
        //content
        $w = array(15, 125, 50);
        $pdf->ln(5);
        $pdf->Cell(170, 0, "รายละเอียดค่าใช้จ่าย " . $data[0]['fullname'], 0, 1, 'C', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont('thsarabunpsk', 'B', 16);
        $pdf->Cell($w[0], 0, '  ลำดับ', 1, 0, 'C', 0, '', 0, false, 'T', 'M');
        $pdf->Cell($w[1] + $w[2], 0, 'รายการ', 'TBR', 1, 'C', 0, '', 0, false, 'T', 'M');
        $sum = 0;
        $i = 1;
        $chkGroup = '';
        foreach ($data as $value) {
            if ($chkGroup !== $value['fin_item_name']) {
                $pdf->Cell($w[0], 8, $i . '  ', 'LR', 0, 'R', 0, '', 0, false, 'T', 'M');
                $pdf->Cell($w[1] + $w[2], 8, ' ' . $value['fin_item_name'], 'R', 1, 'L', 0, '', 0, false, 'T', 'M');
                $chkGroup = $value['fin_item_name'];
                $i++;
            }
            $pdf->Cell($w[0], 8, '', 'LR', 0, 'R', 0, '', 0, false, 'T', 'M');
            $pdf->Cell($w[1] + $w[2], 8, '      ' . $value['sks_code'] . ' ' . $value['order_name'], 'R', 1, 'L', 0, '', 0, false, 'T', 'M');
            //$pdf->Cell($w[2], 8, number_format($value['tran_item_pay'], 2, ".", ","), '', 1, 'R', 0, '', 0, false, 'T', 'M');
            //$sum += $value['tran_item_pay'];
        }
        //$number_text = new \backend\modules\patient\classes\NumberThai();
        //$pdf->Cell(140, 0, 'จำนวนเงิน   ' . $number_text->convertBaht($sum) . '   ', 'TBR', 0, 'R', 0, '', 0, false, 'T', 'M');
        //$pdf->Cell($w[2], 0, number_format($sum, 2, ".", ","), 'TB', 1, 'R', 0, '', 0, false, 'T', 'M');
        $pdf->Cell($w[0], 8, '', 'LRB', 0, 'R', 0, '', 0, false, 'T', 'M');
        $pdf->Cell($w[1] + $w[2], 8, '', 'LRB', 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->ln();
        //$pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0));
//        $pdf->Cell(120, 0, 'ลงชื่อ', 0, 0, 'R', 0, '', 0, false, 'T', 'M');
//        $pdf->Cell(45, 0, '.............................................', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
//        $pdf->Cell(10, 0, ' ผู้รับเงิน ', 0, 1, 'L', 0, '', 0, false, 'T', 'M');
//
//        $pdf->Cell(65, 0, '', 0, 0, 'R', 0, '', 0, false, 'T', 'M');
//        $pdf->Cell(90, 0, 'ไว้เป็นการถูกต้องแล้ว', 0, 1, 'L', 0, '', 0, false, 'T', 'M');
//
//        $pdf->Cell(120, 0, 'ตำแหน่ง', 0, 0, 'R', 0, '', 0, false, 'T', 'M');
//        $pdf->Cell(55, 0, '.............................................................', 0, 1, 'L', 0, '', 0, false, 'T', 'M');
    }

    private function Checknotequezero($val) {
        if ($val != 0) {
            return number_format($val, 2, ".", ",");
        }
    }

    public function actionAddMoreOrder() {
        $ezf_id = Yii::$app->request->get('ezf_id');
        $ezm_id = Yii::$app->request->get('ezm_id');
        $configs = Yii::$app->request->get('configs');
        $key_index = Yii::$app->request->get('key_index');

        return $this->renderAjax('../../../ezmodules/views/ezmodule-widget/assets/his_cashier/config_1', [
                    'ezf_id' => $ezf_id,
                    'ezm_id' => $ezm_id,
                    'configs' => $configs,
                    'key_index' => $key_index,
        ]);
    }

    public function actionAddMoreItemOrder() {
        $ezf_id = Yii::$app->request->get('ezf_id');
        $ezm_id = Yii::$app->request->get('ezm_id');
        $items = Yii::$app->request->get('items');
        $key_index = Yii::$app->request->get('key_index');

        return $this->renderAjax('../../../ezmodules/views/ezmodule-widget/assets/his_cashier/item_config', [
                    'ezf_id' => $ezf_id,
                    'ezm_id' => $ezm_id,
                    'items' => $items,
                    'key_index' => $key_index,
        ]);
    }

}
