<?php

namespace backend\modules\customer\controllers;

use Yii;
use backend\modules\patient\classes\PatientFunc;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use appxq\sdii\utils\SDdate;

class MainReportController extends \yii\web\Controller {

    public function actionIndex() {
        $ezf_id = \backend\modules\patient\Module::$formID['order_tran'];
        Yii::$app->session['ezf_input'] = \backend\modules\ezforms2\classes\EzfQuery::getInputv2All();
        $searchModel = PatientFunc::getModel($ezf_id, '');
        $searchModel['create_date'] = date('d-m-Y');

        return $this->renderAjax('index', [
                    'ezf_id' => $ezf_id,
                    'searchModel' => $searchModel,
        ]);
    }

    public function actionGrid() {
        if (Yii::$app->getRequest()->isAjax) {
            $ezf_id = \backend\modules\patient\Module::$formID['order_tran'];
            $searchModel = PatientFunc::getModel($ezf_id, '');
            $dataProvider = \backend\modules\customer\classes\CusFunc::getReportPt($searchModel, Yii::$app->request->post());

            return $this->renderAjax('_grid', [
                        'ezf_id' => $ezf_id,
                        'searchModel' => $searchModel,
                        'dataProvider' => $dataProvider,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionDetailItem() {
        if (Yii::$app->getRequest()->isAjax) {
            $hn = Yii::$app->request->get('hn');
            $visit_date = Yii::$app->request->get('visit_date');
            $right_code = Yii::$app->request->get('right_code');
            $receipt_mas_id = Yii::$app->request->get('receipt_mas_id');
            $status_bill = Yii::$app->request->get('status_bill');
            $receipt_visit_id = Yii::$app->request->get('receipt_visit_id');
            if ($right_code == 'OFC') {
                if ($status_bill == 2) {
                    $data = \backend\modules\customer\classes\CusQuery::getDetailItemSks($receipt_visit_id);
                } else {
                    $data = \backend\modules\customer\classes\CusQuery::getDetailItem($hn, $visit_date, $right_code);
                }

                return $this->renderAjax('_datail_item_add', [
                            'data' => $data,
                            'receipt_mas_id' => $receipt_mas_id,
                            'receipt_visit_id' => $receipt_visit_id
                ]);
            } else {
                $data = \backend\modules\customer\classes\CusQuery::getDetailItem($hn, $visit_date, $right_code);

                return $this->renderAjax('_detail_item', [
                            'data' => $data,
                ]);
            }
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionSaveData() {
        $ezf_id = \backend\modules\patient\Module::$formID['sks'];
        $ezfsks_tbname = \backend\modules\patient\Module::$formTableName['sks'];
        $ezf_recivemas_id = \backend\modules\patient\Module::$formID['receipt_mas'];
        $ezfrecivemas_tbname = \backend\modules\patient\Module::$formTableName['receipt_mas'];
        $ordercode = Yii::$app->request->post('order_code');
        $qty = Yii::$app->request->post('qty');
        $sumnotpay = Yii::$app->request->post('sumnotpay');
        $pt_hn = Yii::$app->request->post('pt_hn');
        $sks_code = Yii::$app->request->post('sks_code');
        $item_group = Yii::$app->request->post('item_group');
        $ref_no = Yii::$app->request->post('receipt_visit_id');
        $visit_date = Yii::$app->request->post('visit_date');
        $doctorcode = Yii::$app->request->post('doctorcode');
        $app_code = Yii::$app->request->post('app_code');
        $item_reqno = Yii::$app->request->post('item_reqno');

        $ref_num = \backend\modules\customer\classes\CusQuery::genRefNum($visit_date)['ref_num'];
        $model = \backend\modules\patient\classes\PatientFunc::loadTbDataByField($ezfsks_tbname, ['ref_no' => $ref_no]);
        if ($model !== false) {
            \backend\modules\customer\classes\CusQuery::deletesks($ref_no);
        }
        $i = 0;
        foreach ($ordercode AS $row) {
            $data['item_code'] = $row;
            $data['item_qty'] = $qty[$i];
            $data['item_price'] = str_replace(",", "", $sumnotpay[$i]);
            $data['item_status'] = 1;
            $data['hn_no'] = $pt_hn;
            $data['sks_code'] = $sks_code[$i];
            if ($item_group[$i] == '03') {
                $data['ref_no_drug'] = $item_reqno[$i];
            }
            $data['item_group'] = $item_group[$i];
            $data['ref_no'] = $ref_no;
            $data['visit_date'] = $visit_date;
            $data['doctor_code'] = $doctorcode[$i];
            $data['app_code'] = $app_code;
            $data['ref_num'] = $ref_num;
            PatientFunc::BackgroundInsert($ezf_id, '', '', $data);
            $i++;
            $data['ref_no_drug'] = '';
        }
        $datamas['status_bill'] = '2';
        $model = \backend\modules\customer\classes\CusQuery::getReciveidByVisitid($ref_no);
        foreach ($model AS $row) {
            PatientFunc::saveDataNoSys($ezf_recivemas_id, $ezfrecivemas_tbname, $row['id'], $datamas);
        }

        $out['status'] = 'success';
        $out['message'] = 'success';
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $out;
    }

    public function actionItem() {
        return $this->renderAjax('_item');
    }

    public function actionGetListOrder($q = null) {
        $data = \backend\modules\customer\classes\CusQuery::getConstOrderName($q);
        $out = ['results' => ['id' => '', 'text' => '']];
        $out['results'] = array_values($data);

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $out;
    }

    public function actionExportDrug() {
        $sessno = Yii::$app->request->get('sessno');
        $create_date = Yii::$app->request->get('create_date');
        $create_date = explode(',', $create_date);
        $date_st = SDdate::phpThDate2mysqlDate($create_date[0], '-');
        $date_en = SDdate::phpThDate2mysqlDate($create_date[1], '-');
        $dateNow = date('Y-m-d H:m:s'); // . 'T' . date('');

        $path1 = Yii::getAlias('@storage/web/Export_XML/') . "BillTran" . date('Ymd') . ".txt";
        $myfile = fopen($path1, "w") or die("Unable to open file!");

        $result = \backend\modules\customer\classes\CusQuery::getskstoxmlHeader($date_st, $date_en);
        $txtValue = '';
        $txtHeader = '<ClaimRec System="OP" PayPlan="CS" Version="0.9"></ClaimRec>';
        $txtHeader .= "\r\n<HCODE>12276</HCODE>";
        $txtHeader .= "\r\n<HNAME>มะเร็งอุดรธานี</HNAME>";
        $txtHeader .= "\r\n<DATETIME>" . $dateNow . "</DATETIME>";
        $txtHeader .= "\r\n<SESSNO>" . $sessno . "</SESSNO>";
        $chkHN = [];
        $txtHeader2 = '';
        $chkDateReceipt = '';
        $i = 1;
        foreach ($result AS $output) {
            $chkHN[$output['HN']][$output['DATES']] = $output['ref_no'];
            $txtHeader2 .= "\r\n" . '01|' . '|' . $output['DATES2'] . '|12276|' . $output['ref_no'] . '||' . $output['HN'] . '||'
                    . $output['NOTPAY'] . '|' . $output['PAY'] . '|||' . $output['PT_CID'];
        }
        $txtHeader2 .= "\r\n</BILLTRAN>";
        $numRows = count($result);
        $txtHeader .= "\r\n<RECCOUNT>{$numRows}</RECCOUNT>"
                . "\r\n<BILLTRAN>";

        $result = \backend\modules\customer\classes\CusQuery::getksktoxmlDetail($date_st, $date_en);
        $txtValue2 = '';
        foreach ($result AS $output) {
            $txtValue .= "\r\n" . $chkHN["{$output['HN_NO']}"]["{$output['RECEIPT_DATE']}"] . "|{$output['DRG_FIELD']}|{$output['NOTPAY']}|{$output['PAY']}";
        }
        $numRows2 = count($result);
        $txtValue2 = "\r\n" . '<OPBills invcount="' . $numRows . '" lines="' . $numRows2 . '">';
        $txtValue .= "\r\n</OPBills>"
                . "\r\n";
        fwrite($myfile, $txtHeader . $txtHeader2 . $txtValue2 . $txtValue);
        fclose($myfile);
        $md5 = md5_file($path1);
        $txtValue .= "<END>" . $md5 . "</END>";
        $myfile = fopen($path1, "w") or die("Unable to open file!");
        fwrite($myfile, $txtHeader . $txtHeader2 . $txtValue2 . $txtValue);
        fclose($myfile);
        /* ---  report_drugdips_xml start */

        $path = Yii::getAlias('@storage/web/Export_XML/') . "BillDisp" . date('Ymd') . ".txt";
        $myfile = fopen($path, "w") or die("Unable to open file!");
        $result = \backend\modules\customer\classes\CusQuery::getDrugreportheader($date_st, $date_en);
        $txtValue = '';
        $txtHeader = '<?xml version="1.0" encoding ="windows-874"?>';
        $txtHeader .= "\r\n" . '<ClaimRec System="OP" PayPlan="CS" Version="0.91">';
        $txtHeader .= "\r\n<Header>";
        $txtHeader .= "\r\n<HCODE>12276</HCODE>";
        $txtHeader .= "\r\n<HNAME>" . iconv('UTF-8', 'cp874', 'มะเร็งอุดรธานี') . "</HNAME>";
        $txtHeader .= "\r\n<DATETIME>" . date('Y-m-d') . 'T' . date('H:i:s') . "</DATETIME>";
        $txtHeader .= "\r\n<SESSNO>" . $sessno . "</SESSNO>";
        foreach ($result AS $output) {
            $disID = trim($output['DISPENSE_ID']);
            $perscEncode = iconv('UTF-8', 'cp874', $output['PERSCRIBER']);
            $txtValue .= "\r\n{$output['SATAION']}|{$disID}|"
                    . $chkHN[$output['HN_NO']][$output['DISPENSED_DATE2']]
                    . "|{$output['HN_NO']}|{$output['PID']}"
                    . "|{$output['PRESCRIPTION_DATE']}|{$output['DISPENSED_DATE']}|{$perscEncode}|{$output['ITEM_COUNT']}|{$output['FULL_PRICE']}"
                    . "|{$output['NOTPAY']}|{$output['PAY']}|{$output['OT']}|{$output['USERCLAIM']}|{$output['BENEFIT']}|{$output['DISPENSE_STATUS']}";
        }
        $txtValue .= "\r\n</Dispensing>"
                . "\r\n<DispensedItems>";
        $numRows = count($result);
        $txtHeader .= "\r\n<RECCOUNT>{$numRows}</RECCOUNT>"
                . "\r\n</Header>"
                . "\r\n<Dispensing>";

        $result = \backend\modules\customer\classes\CusQuery::getDrugreportDetail($date_st, $date_en);
        foreach ($result AS $output) {
            $DFS_NAME = iconv('UTF-8', 'cp874', TRIM($output['DFS_NAME']));

            $txtValue .= "\r\n{$output['DISPENSE_ID']}|{$output['DIS_ID']}|{$output['HOS_DRUG_CODE']}|{$output['DRUG_ID']}|{$output['DFS_DOSE']
                    }|{$DFS_NAME}|{$output['PACK_SIZE']}|{$output['SIG_CODE']}|{$output['SIG_TEXT']}|{$output['DRUG_QTY']}|{$output['UNIT_PRICE']}|{$output['NOYPAY']
                    }|{$output['UNIT_PRICE2']}|{$output['NOYPAY2']}|{$output['A1']}|{$output['A2']}|{$output['A3']}|{$output['A4']}";
        }
        $txtValue .= "\r\n</DispensedItems>";
        $txtValue .= "\r\n</ClaimRec>"
                . "\r\n";

        fwrite($myfile, $txtHeader . $txtValue);
        fclose($myfile);
        $md5 = md5_file($path);
        $txtValue .= '<?EndNote Checksum="' . $md5 . '"?>';

        $myfile = fopen($path, "w") or die("Unable to open file!");
        fwrite($myfile, $txtHeader . $txtValue);
        fclose($myfile);
        /* ---  report_drugdips_xml end */

        $txtHeader = '<?xml version="1.0" encoding ="windows-874"?>';
        $txtHeader .= "\r\n" . '<ClaimRec System="OP" PayPlan="CS" Version="0.91">';
        $txtHeader .= "\r\n<Header>";
        $txtHeader .= "\r\n<HCODE>12276</HCODE>";
        $txtHeader .= "\r\n<HNAME>" . iconv('UTF-8', 'cp874', 'มะเร็งอุดรธานี') . "</HNAME>";
        $txtHeader .= "\r\n<DATETIME>" . date('Y-m-d') . 'T' . date('H:i:s') . "</DATETIME>";
        $txtHeader .= "\r\n<SESSNO>" . $sessno . "</SESSNO>";
        $txtValue = '';
        $txtValue .= "\r\n</OPServices>"
                . "\r\n<OPDx>";
        $numRows = '0';
        $txtHeader .= "\r\n<RECCOUNT>{$numRows}</RECCOUNT>"
                . "\r\n</Header>"
                . "\r\n<OPServices>";

        $txtValue .= "\r\n</OPDx>";
        $txtValue .= "\r\n</ClaimRec>"
                . "\r\n";
        $path2 = Yii::getAlias('@storage/web/Export_XML/') . "OPServices" . date('Ymd') . ".txt";

        $myfile = fopen($path2, "w") or die("Unable to open file!");
        fwrite($myfile, $txtHeader . $txtValue);
        fclose($myfile);

        $md5 = md5_file($path2);
        $txtValue .= '<?EndNote Checksum="' . $md5 . '"?>';

        $myfile = fopen($path2, "w") or die("Unable to open file!");
        fwrite($myfile, $txtHeader . $txtValue);
        fclose($myfile);
        $time = date('Ymd-Hms');
        $pathZip = Yii::getAlias('@storage/web/Export_XML/') . '12276_COCDBIL_' . $sessno . '_01_' . $time . '.zip ';
        if (exec('zip -j ' . $pathZip . Yii::getAlias('@storage/web/Export_XML/') . '*.txt')) {
            unlink($path1);
            unlink($path);
            unlink($path2);
            $out['status'] = 'success';
            $out['message'] = 'success';
            $out['pathZip'] = '12276_COCDBIL_' . $sessno . '_01_' . $time . '.zip ';
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return $out;
        } else {
            echo 'FU';
        }
    }

    public function actionImportBill() {
        $uploadPath = Yii::getAlias('@storage/web/ezform/fileinput/');
        $filename = preg_grep('~\.(bil|BIL)$~', array_diff(scandir($uploadPath), array('..', '.')));
        if ($filename) {
            $rows = '';
            foreach ($filename AS $row) {
                $rows = $row;
            }
            $uploadPath = $uploadPath . $rows;
            $sess_no = substr($rows, strpos($rows, '.') - 4, 4);
            $myfile = fopen($uploadPath, "r") or die("Unable to open file!");
            $contents = iconv('cp874', 'UTF-8', fread($myfile, filesize($uploadPath)));
            preg_match_all('|\ A (.*) |', $contents, $match);
            if (is_array($match[1])) {
                foreach ($match[1] AS $value) {
                    $arr = explode(',', $value);
                    $hn = substr(str_replace('_', '', $arr[6]), 1);
                    $receipt_no = substr(str_replace('_', '', $arr[4]), 1);
                    $receipt_date = \backend\modules\customer\classes\CusFunc::phpThDate2mysqlDate($arr[3]);
                    \backend\modules\customer\classes\CusQuery::UpdateSksRecivemas($sess_no, $receipt_no, $receipt_date, $hn);
                }
                unlink($uploadPath);
            }
        } else {
            echo '<h2>ไม่พบไฟล์ที่จะทำการประมวลผล กรุณาอัพโหลดไฟล์อีกครั้ง</h2>';
        }
    }

    public function actionGetListDoctor($q = null) {
        $data = \backend\modules\customer\classes\CusQuery::getDoctorName($q);
        $out = ['results' => ['id' => '', 'text' => '']];
        $out['results'] = array_values($data);

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $out;
    }

    public function actionPrintDetail() {
        $hn = Yii::$app->request->get('hn');
        $visit_date = Yii::$app->request->get('visit_date');
        $right_code = Yii::$app->request->get('right_code');

        if ($right_code == 'PRO') {
            $project_id = Yii::$app->request->get('project_id');
            $data = \backend\modules\customer\classes\CusQuery::getDetailItemCheckup($hn, $visit_date, $right_code, $project_id);
        } else {
            $data = \backend\modules\customer\classes\CusQuery::getDetailItem($hn, $visit_date, $right_code);
        }

        $this->reportHeader($data, $right_code);
    }

    private function reportHeader($data, $right_code) {
        $pdf = new CUSTOMERREPORT('P', PDF_UNIT, 'A4', true, 'UTF-8', false);
        $pdf->SetCreator('AppXQ');
        $pdf->SetAuthor('iencoded@gmail.com');
        $pdf->SetTitle('รายการค่าใช่จ่าย ');
        $pdf->SetSubject('Original');
        $pdf->SetKeywords('AppXQ, SDII, PDF, report, medical,');

        // remove default header/footer
        $pdf->setPrintHeader(TRUE);
        $pdf->setPrintFooter(false);

        // set margins
        $pdf->SetMargins(10, 25, 0, TRUE);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, 5);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set font
        $pdf->SetFont($pdf->fontName, '', $pdf->fontSize);
        $pdf->fontSize = 14;
        $pdf->data['report_type'] = $right_code;
        //Content
        if ($right_code == 'PRO') {
            $this->reportContenCheckup($pdf, $data);
        } else {
            $this->reportConten($pdf, $data);
        }

        $pdf->Output('ReportCheckup.pdf', 'I');
        Yii::$app->end();
    }

    private function reportContenCheckup($pdf, $data) {
        $chkHN = '';
        $chkVisitDate = '';
        $k = 99;
        $i = 1;
        $sumTotalPay = 0;
        $sumTotalNotPay = 0;
        $sumNOTPAY = 0;
        $sumPAY = 0;

        foreach ($data as $value) {
            if ($chkHN != $value['pt_hn'] or $chkVisitDate != $value['visit_date']) {
                if ($i != 1) {
                    $pdf->Cell(135, 0, 'รวม', 1, 0, 'R', 0, '', 0, false, 'T', 'M');
                    $pdf->Cell(30, 0, number_format($sumPAY, 2, ".", ","), 1, 0, 'L', 0, '', 0, false, 'T', 'M');
                    $pdf->Cell(30, 0, number_format($sumNOTPAY, 2, ".", ","), 1, 1, 'L', 0, '', 0, false, 'T', 'M');
                }

                $pdf->AddPage();
                $pdf->SetFont($pdf->fontName, 'B', 16);
                $w = [0 => 98, 1 => 98];
                $pdf->Cell(98, 0, 'HN : ' . $value['pt_hn'] . ' ' . $value['fullname'], 0, 0, 'L', 0, '', 0, false, 'T', 'M');
                $pdf->Cell(98, 0, 'อายุ : ' . SDdate::getAge(SDdate::dateTh2bod($value['pt_bdate'])) . ' ปี', 0, 1, 'L', 0, '', 0, false, 'T', 'M');

                $pdf->Cell(98, 0, 'หน่วยงาน : ' . $value['project_name'], 0, 0, 'L', 0, '', 0, false, 'T', 'M');
                $pdf->Cell(98, 0, $value['visit_date'], 0, 1, 'L', 0, '', 0, false, 'T', 'M');
                $pdf->ln(3);
                $pdf->Cell(20, 0, 'ลำดับ', 1, 0, 'C', 0, '', 0, false, 'T', 'M');
                $pdf->Cell(95, 0, 'รายการ', 1, 0, 'C', 0, '', 0, false, 'T', 'M');
                $pdf->Cell(20, 0, 'รหัส', 1, 0, 'C', 0, '', 0, false, 'T', 'M');
                $pdf->Cell(30, 0, 'ชำระเอง ', 1, 0, 'C', 0, '', 0, false, 'T', 'M');
                $pdf->Cell(30, 0, 'จำนวนเรียกเก็บ ', 1, 1, 'C', 0, '', 0, false, 'T', 'M');

                $sumTotalPay = 0;
                $sumTotalNotPay = 0;
                $sumNOTPAY = 0;
                $sumPAY = 0;
                $i = 1;
                $chkHN = $value['pt_hn'];
                $chkVisitDate = $value['visit_date'];
            }
            $pdf->Cell(20, 0, $i, 1, 0, 'C', 0, '', 0, false, 'T', 'M');
            $pdf->Cell(95, 0, $value['order_name'], 1, 0, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->Cell(20, 0, $value['sks_code'], 1, 0, 'C', 0, '', 0, false, 'T', 'M');
            $pdf->Cell(30, 0, number_format($value['sumpay'], 2, ".", ","), 1, 0, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->Cell(30, 0, number_format($value['sumnotpay'], 2, ".", ","), 1, 1, 'L', 0, '', 0, false, 'T', 'M');
            $sumPAY += $value['sumpay'];
            $sumNOTPAY += $value['sumnotpay'];
            $i++;
        }
        $pdf->Cell(135, 0, 'รวม', 1, 0, 'R', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(30, 0, number_format($sumPAY, 2, ".", ","), 1, 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(30, 0, number_format($sumNOTPAY, 2, ".", ","), 1, 1, 'L', 0, '', 0, false, 'T', 'M');
    }

    private function reportConten($pdf, $data) {
        $chkHN = '';
        $chkVisitDate = '';
        $k = 99;
        $i = 1;
        $sumTotalPay = 0;
        $sumTotalNotPay = 0;
        $sumNOTPAY = 0;
        $sumPAY = 0;

        foreach ($data as $value) {
            if ($chkHN != $value['pt_hn'] or $chkVisitDate != $value['visit_date']) {
                if ($i != 1) {
                    $pdf->Cell(135, 0, 'รวม', 1, 0, 'R', 0, '', 0, false, 'T', 'M');
                    $pdf->Cell(30, 0, number_format($sumPAY, 2, ".", ","), 1, 0, 'L', 0, '', 0, false, 'T', 'M');
                    $pdf->Cell(30, 0, number_format($sumNOTPAY, 2, ".", ","), 1, 1, 'L', 0, '', 0, false, 'T', 'M');
                }

                $pdf->AddPage();
                $pdf->SetFont($pdf->fontName, 'B', 16);
                $w = [0 => 66, 1 => 98];
                $pdf->Cell(98, 0, 'HN : ' . $value['pt_hn'] . ' ' . $value['fullname'], 0, 0, 'L', 0, '', 0, false, 'T', 'M');
              //  $pdf->Cell(98, 0, 'รหัส ปชช. : ' . $value['pt_cid'] . ' วันเกิด : ' . SDdate::mysql2phpThDateSmall(SDdate::dateTh2bod($value['pt_bdate'])) . ' อายุ : ' . SDdate::getAge(SDdate::dateTh2bod($value['pt_bdate'])) . ' ปี', 0, 1, 'L', 0, '', 0, false, 'T', 'M');
                //$pdf->Cell($w[0], 0, 'วันเกิด : '.SDdate::mysql2phpThDateSmall(SDdate::dateTh2bod($value['pt_bdate'])).' อายุ : ' . SDdate::getAge(SDdate::dateTh2bod($value['pt_bdate'])) . ' ปี', 0, 1, 'L', 0, '', 0, false, 'T', 'M');

                $pdf->Cell(0, 0, 'เลขที่อนุมัติ : ' . $value['right_prove_no'], 0, 1, 'L', 0, '', 0, false, 'T', 'M');
               // $pdf->Cell(98, 0, 'วันที่ Visit : ' . SDdate::mysql2phpThDateTime($value['visit_datetime']), 0, 0, 'L', 0, '', 0, false, 'T', 'M');
                $pdf->Cell(98, 0, 'แพทย์ : ' . $value['all_doc'], 0, 1, 'L', 0, '', 0, false, 'T', 'M');
                $pdf->SetFont($pdf->fontName, 'B', 14);
                $pdf->Cell(98, 0, 'Diag : ' . strip_tags($value['diag_txt']) . ' ' . $value['diag'], 0, 0, 'L', 0, '', 0, false, 'T', 'M');
                $pdf->Cell(98, 0, '', 0, 1, 'L', 0, '', 0, false, 'T', 'M');
                $pdf->SetFont($pdf->fontName, 'B', 14);
                $pdf->ln(3);
                $pdf->Cell(20, 0, 'ลำดับ', 1, 0, 'C', 0, '', 0, false, 'T', 'M');
                $pdf->Cell(95, 0, 'รายการ', 1, 0, 'C', 0, '', 0, false, 'T', 'M');
                $pdf->Cell(20, 0, 'รหัส', 1, 0, 'C', 0, '', 0, false, 'T', 'M');
                $pdf->Cell(30, 0, 'ชำระเอง ', 1, 0, 'C', 0, '', 0, false, 'T', 'M');
                $pdf->Cell(30, 0, 'จำนวนเรียกเก็บ ', 1, 1, 'C', 0, '', 0, false, 'T', 'M');

                $sumTotalPay = 0;
                $sumTotalNotPay = 0;
                $sumNOTPAY = 0;
                $sumPAY = 0;
                $i = 1;
                $chkHN = $value['pt_hn'];
                $chkVisitDate = $value['visit_date'];
            }
            $pdf->Cell(20, 0, $i, 1, 0, 'C', 0, '', 0, false, 'T', 'M');
            $pdf->Cell(95, 0, $value['order_name'], 1, 0, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->Cell(20, 0, $value['sks_code'], 1, 0, 'C', 0, '', 0, false, 'T', 'M');
            $pdf->Cell(30, 0, number_format($value['sumpay'], 2, ".", ","), 1, 0, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->Cell(30, 0, number_format($value['sumnotpay'], 2, ".", ","), 1, 1, 'L', 0, '', 0, false, 'T', 'M');
            $sumPAY += $value['sumpay'];
            $sumNOTPAY += $value['sumnotpay'];
            $i++;
        }
        $pdf->Cell(135, 0, 'รวม', 1, 0, 'R', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(30, 0, number_format($sumPAY, 2, ".", ","), 1, 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(30, 0, number_format($sumNOTPAY, 2, ".", ","), 1, 1, 'L', 0, '', 0, false, 'T', 'M');
    }

    public function actionCheckupProjectSummaryExcel() {
        $visit_date = Yii::$app->request->get('visit_date');
        $project_id = Yii::$app->request->get('project_id');
        $project_type = Yii::$app->request->get('project_type');
        if ($project_type == '2') {
            $data = \backend\modules\customer\classes\CusQuery::getProjectSummaryOri($visit_date, $project_id);
            $this->exportExcelOri($data);
        } else {
            $data = \backend\modules\customer\classes\CusQuery::getProjectSummaryOfc($visit_date, $project_id);
            $this->exportExcelOfc($data);
        }
    }

    public function actionCheckupProjectSummary() {
        $visit_date = Yii::$app->request->get('visit_date');
        $project_id = Yii::$app->request->get('project_id');
        $project_type = Yii::$app->request->get('project_type');
        if ($project_type == '2') {
            $data = \backend\modules\customer\classes\CusQuery::getProjectSummaryOri($visit_date, $project_id);
            $report_type = 'CHECKUP-ORI';
        } else {
            $data = \backend\modules\customer\classes\CusQuery::getProjectSummaryOfc($visit_date, $project_id);
            $report_type = 'CHECKUP-OFC';
        }

        $this->reportProjectSummary($data, $report_type);
    }

    private function reportProjectSummary($data, $report_type) {
        $pdf = new CUSTOMERREPORT('L', PDF_UNIT, 'A4', true, 'UTF-8', false);
        $pdf->SetCreator('AppXQ');
        $pdf->SetAuthor('iencoded@gmail.com');
        $pdf->SetTitle('สรุปค่าตรวจสอขภาพหน่วยงาน ' . $data[0]['project_name']);
        $pdf->SetSubject('Original');
        $pdf->SetKeywords('AppXQ, SDII, PDF, report, medical,');

        // remove default header/footer
        $pdf->setPrintHeader(TRUE);
        $pdf->setPrintFooter(TRUE);

        // set margins
        $pdf->SetMargins(5, 55, 0, TRUE);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, 10);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set font
        $pdf->SetFont($pdf->fontName, '', $pdf->fontSize);
        $pdf->fontSize = 14;
        $pdf->data['report_type'] = $report_type;
        $pdf->data['project_name'] = $data[0]['project_name'];
        //Content
        if ($report_type == 'CHECKUP-OFC') {
            $this->reportContenCheckupOfc($pdf, $data);
        } elseif ($report_type == 'CHECKUP-ORI') {
            $this->reportContenCheckupOri($pdf, $data);
        }


        $pdf->Output('ReportCheckup.pdf', 'I');
        Yii::$app->end();
    }

    private function reportContenCheckupOfc($pdf, $data) {
        $sizeH = Yii::$app->request->get('size', 0);

        $w = array(6, 15, 39, 8
            , 11, 14, 16, 18
            , 25, 13, 126); //SUM
        $h = array($sizeH, 18);

        $sumHN = 0;
        $sumProject = 0;
        $sumStool = '';
        $i = 1;
        $pdf->AddPage();
        foreach ($data as $value) {
            if ($value['FE001'] || $value['FE001']) {
                $sumStool = $value['FE001'] + $value['FE002'];
            }
            $sumHN = $value['CG001'] + $value['HM001'] + $value['UR001'] + $sumStool + $value['BC001'] + $value['BC002'] + $value['BC003'];
            $sumHN += $value['BC005'] + $value['BC006'] + $value['BC009'] + $value['BC015'] + $value['BC016'] + $value['BC017'] + $value['CH'];
            $pdf->Cell($w[0], $h[0], $i, 1, 0, 'C', 0, '', 0, false, 'T', 'M');
            $pdf->Cell($w[1], $h[0], SDdate::mysql2phpThDateSmallYear($value['visit_date']), 1, 0, 'C', 0, '', 0, false, 'T', 'M');
            $pdf->Cell($w[2], $h[0], $value['fullname'], 1, 0, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->Cell($w[3], $h[0], SDdate::getAge(SDdate::dateTh2bod($value['pt_bdate'])), 1, 0, 'C', 0, '', 0, false, 'T', 'M');
            $pdf->Cell($w[4], $h[0], '', 1, 0, 'C', 0, '', 0, false, 'T', 'M');
            $pdf->Cell($w[5], $h[0], $value['CG001'], 1, 0, 'C', 0, '', 0, false, 'T', 'M');
            $pdf->Cell($w[5], $h[0], $value['HM001'], 1, 0, 'C', 0, '', 0, false, 'T', 'M');
            $pdf->Cell($w[5] - 1, $h[0], $value['UR001'], 1, 0, 'C', 0, '', 0, false, 'T', 'M');
            $pdf->Cell($w[5] - 1, $h[0], $sumStool, 1, 0, 'C', 0, '', 0, false, 'T', 'M');
            $pdf->Cell($w[4], $h[0], $value['BC001'], 1, 0, 'C', 0, '', 0, false, 'T', 'M');
            $pdf->Cell($w[4], $h[0], $value['BC002'], 1, 0, 'C', 0, '', 0, false, 'T', 'M');
            $pdf->Cell($w[6], $h[0], $value['BC003'], 1, 0, 'C', 0, '', 0, false, 'T', 'M');
            $pdf->Cell($w[6], $h[0], $value['BC005'], 1, 0, 'C', 0, '', 0, false, 'T', 'M');
            $pdf->Cell($w[7], $h[0], $value['BC006'], 1, 0, 'C', 0, '', 0, false, 'T', 'M');
            $pdf->Cell($w[7], $h[0], $value['BC009'], 1, 0, 'C', 0, '', 0, false, 'T', 'M');
            $pdf->Cell($w[4], $h[0], $value['BC015'], 1, 0, 'C', 0, '', 0, false, 'T', 'M');
            $pdf->Cell($w[4], $h[0], $value['BC016'], 1, 0, 'C', 0, '', 0, false, 'T', 'M');
            $pdf->Cell($w[5], $h[0], $value['BC017'], 1, 0, 'C', 0, '', 0, false, 'T', 'M');
            $pdf->Cell($w[5], $h[0], $value['CH'], 1, 0, 'C', 0, '', 0, false, 'T', 'M');
            $pdf->Cell($w[6], $h[0], number_format($sumHN, 2, ".", ","), 1, 1, 'R', 0, '', 0, false, 'T', 'M');

            $sumProject += $sumHN;
            $sumHN = 0;
            $sumStool = '';
            $i++;
        }


        $number_text = new \backend\modules\patient\classes\NumberThai();
        $pdf->SetFont('thsarabunpsk', 'B', 13);
        $pdf->Cell(259, 0, 'รวมทั้งสิ้น (ตัวอักษร)  ' . $number_text->convertBaht($sumProject), '', 0, 'R', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(30, 0, number_format($sumProject, 2, ".", ","), 1, 1, 'R', 0, '', 0, false, 'T', 'M');
        $pdf->ln(10);
        $pdf->MultiCell(72.5 / 2, 0, '', '', 'C', 0, 0, '', '', true);
        $pdf->MultiCell(72.5 / 2, 0, '', '', 'C', 0, 0, '', '', true);
        $pdf->MultiCell(72.5, 0, 'ลงชื่อ................................................................................( นาง วิไลวรรณ  นามพิลา )   หัวหน้าฝ่ายสวัสดิการสังคมและประกันสุขภาพ  วันที่................./................./.................', '', 'C', 0, 0, '', '', true);
        $pdf->MultiCell(72.5 / 2, 0, '', '', 'C', 0, 0, '', '', true);
        $pdf->MultiCell(72.5, 0, 'ลงชื่อ................................................................................( นาง วิไลพร สุขเกษม )      เจ้าพนักงานการเงินและบัญชี  วันที่................./................./.................', '', 'C', 0, 1, '', '', true);
    }

    private function reportContenCheckupOri($pdf, $data) {
        $sizeH = Yii::$app->request->get('size', 0);

        $w = array(7, 14, 39, 8
            , 11, 12, 13//6
            , 14, 15, 16//9
            , 17, 18, 208); //11
//        $h = array($sizeH, 18);

        $sumHN = 0;
        $sumProject = 0;
        $sumStool = '';
        $sumLFT = 0;
        $i = 1;
        $pdf->SetMargins(5, 43, 0, TRUE);
        $pdf->AddPage();
        foreach ($data as $value) {
            $sumSero = $value['IM001'] + $value['IM002'] + $value['IM006'] + $value['IM008'];
            $sumStool = $value['FE001'] + $value['FE002'];
            $sumLFT = $value['BC011'] + $value['BC012'] + $value['BC013'] + $value['BC014'];
            $sumLFT += $value['BC015'] + $value['BC016'] + $value['BC017'];

            $sumHN = $value['CG001'] + $value['CG020'] + $value['HM001'] + $value['UR001'] + $value['BC001'] + $value['BC002'] + $value['BC003'];
            $sumHN += $value['BC005'] + $value['BC006'] + $value['BC009'] + $value['IM047'] + $value['PH001'] + $value['CH'];
            $sumHN += $value['CG015'] + $value['CG016'] + $sumSero + $sumLFT + $sumStool;
            $pdf->Cell($w[0], $sizeH, $i, 1, 0, 'C', 0, '', 0, false, 'T', 'M');
            $pdf->Cell($w[1], $sizeH, SDdate::mysql2phpThDateSmallYear($value['visit_date']), 1, 0, 'C', 0, '', 0, false, 'T', 'M');
            $pdf->Cell($w[2], $sizeH, $value['fullname'], 1, 0, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->Cell($w[3], $sizeH, SDdate::getAge(SDdate::dateTh2bod($value['pt_bdate'])), 1, 0, 'C', 0, '', 0, false, 'T', 'M');
            $pdf->Cell($w[6], $sizeH, $value['CG001'] + $value['CG020'], 1, 0, 'C', 0, '', 0, false, 'T', 'M');
            $pdf->Cell($w[3], $sizeH, $value['HM001'], 1, 0, 'C', 0, '', 0, false, 'T', 'M');
            $pdf->Cell($w[4], $sizeH, $value['UR001'], 1, 0, 'C', 0, '', 0, false, 'T', 'M');
            $pdf->Cell($w[4], $sizeH, $sumStool, 1, 0, 'C', 0, '', 0, false, 'T', 'M');
            $pdf->Cell($w[7], $sizeH, $value['BC001'], 1, 0, 'C', 0, '', 0, false, 'T', 'M');
            $pdf->Cell($w[3], $sizeH, $value['BC002'], 1, 0, 'C', 0, '', 0, false, 'T', 'M');
            $pdf->Cell($w[9], $sizeH, $value['BC003'], 1, 0, 'C', 0, '', 0, false, 'T', 'M');
            $pdf->Cell($w[4], $sizeH, $value['BC005'], 1, 0, 'C', 0, '', 0, false, 'T', 'M');
            $pdf->Cell($w[11], $sizeH, $value['BC006'], 1, 0, 'C', 0, '', 0, false, 'T', 'M');
            $pdf->Cell($w[11], $sizeH, $value['BC009'], 1, 0, 'C', 0, '', 0, false, 'T', 'M');
            $pdf->Cell($w[4], $sizeH, $sumLFT, 1, 0, 'C', 0, '', 0, false, 'T', 'M');
            $pdf->Cell($w[7], $sizeH, $sumSero, 1, 0, 'C', 0, '', 0, false, 'T', 'M');
            $pdf->Cell($w[4], $sizeH, $value['CH'], 1, 0, 'C', 0, '', 0, false, 'T', 'M');
            $pdf->Cell($w[4], $sizeH, $value['IM047'], 1, 0, 'C', 0, '', 0, false, 'T', 'M');
            $pdf->Cell($w[4], $sizeH, $value['PH001'], 1, 0, 'C', 0, '', 0, false, 'T', 'M');
            $pdf->Cell($w[4], $sizeH, $value['CG015'], 1, 0, 'C', 0, '', 0, false, 'T', 'M');
            $pdf->Cell($w[4], $sizeH, $value['CG016'], 1, 0, 'C', 0, '', 0, false, 'T', 'M');
            $pdf->Cell($w[6], $sizeH, number_format($sumHN, 1, ".", ","), 1, 1, 'R', 0, '', 0, false, 'T', 'M');

            $sumProject += $sumHN;
            $sumHN = 0;
            $sumStool = 0;
            $sumSero = 0;
            $sumLFT = 0;
            $i++;
        }

        $number_text = new \backend\modules\patient\classes\NumberThai();
        $pdf->SetFont('thsarabunpsk', 'B', 13);
        $pdf->Cell(259, 0, 'รวมทั้งสิ้น (ตัวอักษร)  ' . $number_text->convertBaht($sumProject), '', 0, 'R', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(30, 0, number_format($sumProject, 2, ".", ","), 1, 1, 'R', 0, '', 0, false, 'T', 'M');
        $pdf->ln(10);
        $pdf->MultiCell(72.5 / 2, 0, '', '', 'C', 0, 0, '', '', true);
        $pdf->MultiCell(72.5 / 2, 0, '', '', 'C', 0, 0, '', '', true);
        $pdf->MultiCell(72.5, 0, 'ลงชื่อ................................................................................( นาง วิไลวรรณ  นามพิลา )   หัวหน้าฝ่ายสวัสดิการสังคมและประกันสุขภาพ  วันที่................./................./.................', '', 'C', 0, 0, '', '', true);
        $pdf->MultiCell(72.5 / 2, 0, '', '', 'C', 0, 0, '', '', true);
        $pdf->MultiCell(72.5, 0, 'ลงชื่อ................................................................................( นาง วิไลพร สุขเกษม )      เจ้าพนักงานการเงินและบัญชี  วันที่................./................./.................', '', 'C', 0, 1, '', '', true);
    }

    public function actionExportReport() {
        if (Yii::$app->getRequest()->isAjax) {
            $dataForm = Yii::$app->request->post()['EZ1504537671028647300'];

            if ($dataForm['report_type'] == 'report_checkup') {
                $view = 'export_checkup';
            } elseif ($dataForm['report_type'] == 'report_checkup_excel') {
                $view = 'export_checkup';
            }

            return $this->renderAjax($view, [
                        'dataForm' => $dataForm,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    private function exportExcelOfc($data) {
        ini_set('max_execution_time', 0);
        set_time_limit(0);
        ini_set('memory_limit', '256M');

        $url = 'checkup_' . \appxq\sdii\utils\SDUtility::getMillisecTime();

        header("Content-Type: application/vnd.ms-excel");
        header('Content-Disposition: attachment; filename="' . $url . '.xls"');
        ob_start();
        ?>
        <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
          <HTML>
            <HEAD>
              <meta http-equiv="Content-type" content="text/html;charset=utf-8" />
            </HEAD>
            <BODY>
              <TABLE x:str BORDER="1">
                <tr class="header success">
                  <td>วันที่ตรวจ </td>
                  <td>HN</td>
                  <td>ชื่อ-สกุล </td>
                  <td>อายุ </td>
                  <td>PV </td>
                  <td>PAP smear </td>
                  <td>CBC </td>
                  <td>UA </td>
                  <td>Stool </td>
                  <td>Sugar </td>
                  <td>BUN </td>
                  <td>Creatinine </td>
                  <td>Uric Acid </td>
                  <td>Cholesterol </td>
                  <td>Triglyceride </td>
                  <td>SGOT </td>
                  <td>SGPT </td>
                  <td>ALK.Phos </td>
                  <td>CXR</td>
                  <td>รวมเป็นเงิน ทั้งสิ้น </td>
                </tr>
                <?php
                $sumHN = 0;
                $sumProject = 0;
                $sumStool = '';
                $i = 0;
                foreach ($data as $value) {
                    $i++;
                    $sumStool = $value['FE001'] + $value['FE002'];
                    $sumHN = $value['CG001'] + $value['HM001'] + $value['UR001'] + $sumStool + $value['BC001'] + $value['BC002'] + $value['BC003'];
                    $sumHN += $value['BC005'] + $value['BC006'] + $value['BC009'] + $value['BC015'] + $value['BC016'] + $value['BC017'] + $value['CH'];
                    ?>
                    <tr>
                      <td><?= SDdate::mysql2phpThDateSmallYear($value['visit_date']) ?></td>
                      <td><?= $value['pt_hn'] ?></td>
                      <td><?= $value['fullname']; ?></td>
                      <td><?= SDdate::getAge(SDdate::dateTh2bod($value['pt_bdate'])) ?></td>
                      <td></td>
                      <td><?= $value['CG001']; ?></td>
                      <td><?= $value['HM001']; ?></td>
                      <td><?= $value['UR001']; ?></td>
                      <td><?= $sumStool; ?></td>
                      <td><?= $value['BC001']; ?></td>
                      <td><?= $value['BC002']; ?></td>
                      <td><?= $value['BC003']; ?></td>
                      <td><?= $value['BC005']; ?></td>
                      <td><?= $value['BC006']; ?></td>
                      <td><?= $value['BC009']; ?></td>
                      <td><?= $value['BC015']; ?></td>
                      <td><?= $value['BC016']; ?></td>
                      <td><?= $value['BC017']; ?></td>
                      <td><?= $value['CH']; ?></td>
                      <td><?= number_format($sumHN, 2, ".", ",") ?></td>
                    </tr>
                    <?php
                    $sumProject += $sumHN;
                    $sumHN = 0;
                    $sumStool = '';
                }
                ?>
              </TABLE>
            </BODY>
          </HTML>
          <?php
//          ob_end_flush();
      }

      private function exportExcelOri($data) {
          ini_set('max_execution_time', 0);
          set_time_limit(0);
          ini_set('memory_limit', '256M');

          $url = 'checkup_' . \appxq\sdii\utils\SDUtility::getMillisecTime();

          header("Content-Type: application/vnd.ms-excel");
          header('Content-Disposition: attachment; filename="' . $url . '.xls"');
          ?>
          <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
            <HTML>
              <HEAD>
                <meta http-equiv="Content-type" content="text/html;charset=utf-8" />
              </HEAD>
              <BODY>
                <TABLE  x:str BORDER="1">
                  <tr class="header success">
                    <td>วันที่ตรวจ </td>
                    <td>HN</td>
                    <td>ชื่อ-สกุล </td>
                    <td>อายุ </td>
                    <td>PAP smear </td>
                    <td>CBC </td>
                    <td>Urine Exam </td>
                    <td>Stool Exam </td>
                    <td>Glucose </td>
                    <td>BUN </td>
                    <td>Creatinine </td>
                    <td>Uric Acid </td>
                    <td>Cholesterol </td>
                    <td>Triglyceride </td>
                    <td>LFT </td>
                    <td>Serology </td>
                    <td>CXR</td>
                    <td>PSA</td>
                    <td>EKG</td>
                    <td>Thin Prep</td>
                    <td>HPV DNA</td>
                    <td>รวมเป็นเงิน ทั้งสิ้น </td>
                  </tr>
                  <?php
                  $sumHN = 0;
                  $sumProject = 0;
                  $sumStool = '';
                  $sumLFT = 0;
                  $i = 1;
                  foreach ($data as $value) {
                      $sumSero = $value['IM001'] + $value['IM002'] + $value['IM006'] + $value['IM008'];
                      $sumStool = $value['FE001'] + $value['FE002'];
                      $sumLFT = $value['BC011'] + $value['BC012'] + $value['BC013'] + $value['BC014'];
                      $sumLFT += $value['BC015'] + $value['BC016'] + $value['BC017'];

                      $sumHN = $value['CG001'] + $value['CG020'] + $value['HM001'] + $value['UR001'] + $value['BC001'] + $value['BC002'] + $value['BC003'];
                      $sumHN += $value['BC005'] + $value['BC006'] + $value['BC009'] + $value['IM047'] + $value['PH001'] + $value['CH'];
                      $sumHN += $value['CG015'] + $value['CG016'] + $sumSero + $sumLFT + $sumStool;
                      ?>
                      <tr>
                        <td><?= SDdate::mysql2phpThDateSmallYear($value['visit_date']) ?></td>
                        <td><?= $value['pt_hn'] ?></td>
                        <td><?= $value['fullname']; ?></td>
                        <td><?= SDdate::getAge(SDdate::dateTh2bod($value['pt_bdate'])) ?></td>
                        <td><?= $value['CG001'] + $value['CG020']; ?></td>
                        <td><?= $value['HM001']; ?></td>
                        <td><?= $value['UR001']; ?></td>
                        <td><?= $sumStool; ?></td>
                        <td><?= $value['BC001']; ?></td>
                        <td><?= $value['BC002']; ?></td>
                        <td><?= $value['BC003']; ?></td>
                        <td><?= $value['BC005']; ?></td>
                        <td><?= $value['BC006']; ?></td>
                        <td><?= $value['BC009']; ?></td>
                        <td><?= $sumLFT; ?></td>
                        <td><?= $sumSero; ?></td>
                        <td><?= $value['CH']; ?></td>

                        <td><?= $value['IM047']; ?></td>
                        <td><?= $value['PH001']; ?></td>
                        <td><?= $value['CG015']; ?></td>
                        <td><?= $value['CG016']; ?></td>
                        <td><?= number_format($sumHN, 2, ".", ",") ?></td>
                      </tr>
                      <?php
                      $sumProject += $sumHN;
                      $sumHN = 0;
                      $sumStool = '';
                  }
                  ?>
                </TABLE>
              </BODY>
            </HTML>
            <?php
        }

    }

    class CUSTOMERREPORT extends \common\lib\tcpdf\SDPDF {

        public $data;

        function Header() {
            $path = Yii::getAlias('@storageUrl/images') . '/logo2.jpg';
            if ($this->data['report_type'] == 'PRO') {
                $this->Image($path, 63, 3, 18, 18, 'JPG');
                $this->ln(5);
                $this->SetFont($this->fontName, 'B', 18);
                $this->Cell(0, 0, 'โรงพยาบาลมะเร็งอุดรธานี', 0, 1, 'C', 0, '', 0, false, 'T', 'M');
                $this->Cell(0, 0, 'รายละเอียดค่าตรวจสุขภาพ ', 0, 1, 'C', 0, '', 0, false, 'T', 'M');
            } elseif ($this->data['report_type'] == 'CHECKUP-OFC') {
                $this->ln(5);
                $this->SetFont($this->fontName, 'B', 16);
                $this->Cell(0, 0, 'โรงพยาบาลมะเร็งอุดรธานี', 0, 1, 'C', 0, '', 0, false, 'T', 'M');
                $year = date('Y') + 543;
                $this->Cell(0, 0, 'ใบแจ้งหนี้ ค่าตรวจสุขภาพประจำปี  หน่วยงาน ' . $this->data['project_name'], 0, 1, 'C', 0, '', 0, false, 'T', 'M');
                $y = $this->GetY();
                $this->Image($path, $y, 3, 15, 15, 'JPG');
                $w = array(6, 15, 39, 8
                    , 11, 14, 16, 18
                    , 25, 13, 126); //SUM
                $h = array(18, 18);

                $this->SetFont($this->fontName, '', 13);
                $this->MultiCell($w[0], $h[0], 'ลำ ดับ', 'LRT', 'C', 0, 0, '', '', true);
                $this->MultiCell($w[1], $h[0], 'วันที่ตรวจ', 'LRT', 'C', 0, 0, '', '', true);
                $this->MultiCell($w[2], $h[0], 'ชื่อ-สกุล', 'LRT', 'C', 0, 0, '', '', true);
                $this->MultiCell($w[3], $h[0], 'อายุ', 'LRT', 'C', 0, 0, '', '', true);
                $this->MultiCell($w[8], $h[0], 'ตรวจมะเร็ง ปากมดลูก', 1, 'C', 0, 0, '', '', true); //*2
                $this->MultiCell($w[1] - 1, $h[0], 'ความ เข้มข้น ของเลือด', 1, 'C', 0, 0, '', '', true);
                $this->MultiCell($w[9], $h[0], 'ตรวจ  ปัสสาวะ', 1, 'C', 0, 0, '', '', true);
                $this->MultiCell($w[9], $h[0], 'ตรวจ อุจจาระ ', 1, 'C', 0, 0, '', '', true);
                $this->MultiCell($w[10], $h[0], 'รายการตรวจอื่นๆ', 1, 'C', 0, 0, '', '', true);
                $this->MultiCell($w[1] - 1, $h[0], 'เอ็กเรย์ ทรวงอก', 1, 'C', 0, 0, '', '', true);
                $this->MultiCell($w[6], $h[0], 'รวมเป็นเงิน ทั้งสิ้น', 'LRT', 'C', 0, 1, '', '', true);

                $this->MultiCell($w[0], $h[1], '', 'LRB', 'C', 0, 0, '', '', true);
                $this->MultiCell($w[1], $h[1], '', 'LRB', 'C', 0, 0, '', '', true);
                $this->MultiCell($w[2], $h[1], '', 'LRB', 'C', 0, 0, '', '', true);
                $this->MultiCell($w[3], $h[1], '', 'LRB', 'C', 0, 0, '', '', true);
                $this->MultiCell($w[4], $h[1], 'PV 55620', 1, 'C', 0, 0, '', '', true);
                $this->MultiCell($w[5], $h[1], 'Pap smear 38302', 1, 'C', 0, 0, '', '', true);
                $this->MultiCell($w[5], $h[1], 'CBC 30101', 1, 'C', 0, 0, '', '', true);
                $this->MultiCell($w[5] - 1, $h[1], 'UA 31001', 1, 'C', 0, 0, '', '', true);
                $this->MultiCell($w[5] - 1, $h[1], 'Stool 31201/ 31203', 1, 'C', 0, 0, '', '', true);
                $this->MultiCell($w[4], $h[1], 'Sugar 32203', 1, 'C', 0, 0, '', '', true);
                $this->MultiCell($w[4], $h[1], 'BUN 32201', 1, 'C', 0, 0, '', '', true);
                $this->MultiCell($w[6], $h[1], 'Creatinine 32202', 1, 'C', 0, 0, '', '', true);
                $this->MultiCell($w[6], $h[1], 'Uric Acid 32205', 1, 'C', 0, 0, '', '', true);
                $this->MultiCell($w[7], $h[1], 'Cholesterol 32501', 1, 'C', 0, 0, '', '', true);
                $this->MultiCell($w[7], $h[1], 'Triglyceride 32502', 1, 'C', 0, 0, '', '', true);
                $this->MultiCell($w[4], $h[1], 'SGOT 32310', 1, 'C', 0, 0, '', '', true);
                $this->MultiCell($w[4], $h[1], 'SGPT 32311', 1, 'C', 0, 0, '', '', true);
                $this->MultiCell($w[5], $h[1], 'ALK.Phoshatuse 32309', 1, 'C', 0, 0, '', '', true);
                $this->MultiCell($w[5], $h[1], 'CXR 41001', 1, 'C', 0, 0, '', '', true);
                $this->MultiCell($w[6], $h[1], '', 'LRB', 'C', 0, 1, '', '', true);
            } elseif ($this->data['report_type'] == 'CHECKUP-ORI') {
                $this->ln(5);
                $this->SetFont($this->fontName, 'B', 16);
                $this->Cell(0, 0, 'โรงพยาบาลมะเร็งอุดรธานี', 0, 1, 'C', 0, '', 0, false, 'T', 'M');
                $year = date('Y') + 543;
                $this->Cell(0, 0, 'ใบแจ้งหนี้ ค่าตรวจสุขภาพประจำปี   หน่วยงาน ' . $this->data['project_name'], 0, 1, 'C', 0, '', 0, false, 'T', 'M');
                $y = $this->GetY();
                $this->Image($path, $y, 3, 15, 15, 'JPG');
                $w = array(7, 14, 39, 8
                    , 11, 12, 13//6
                    , 14, 15, 16//9
                    , 17, 18, 208); //11
                $h = array(0, 18);
//
                $this->SetFont('thsarabunpsk', '', 13);
                $this->MultiCell($w[0], $h[0], 'ลำ', 'LRT', 'C', 0, 0, '', '', true);
                $this->MultiCell($w[1], $h[0], 'วันที่ตรวจ', 'LRT', 'C', 0, 0, '', '', true);
                $this->MultiCell($w[2], $h[0], 'ชื่อ-สกุล', 'LRT', 'C', 0, 0, '', '', true);
                $this->MultiCell($w[3], $h[0], 'อายุ', 'LRT', 'C', 0, 0, '', '', true);
                $this->MultiCell($w[12], $h[0], 'รายการตรวจอื่นๆ', 1, 'C', 0, 0, '', '', true);
                $this->MultiCell($w[6], $h[0], 'รวมเป็น', 'LRT', 'C', 0, 1, '', '', true);

                $this->MultiCell($w[0], $h[1], 'ดับ', 'LRB', 'C', 0, 0, '', '', true);
                $this->MultiCell($w[1], $h[1], '', 'LRB', 'C', 0, 0, '', '', true);
                $this->MultiCell($w[2], $h[1], '', 'LRB', 'C', 0, 0, '', '', true);
                $this->MultiCell($w[3], $h[1], '', 'LRB', 'C', 0, 0, '', '', true);
                $this->MultiCell($w[6], $h[1], 'PV/Pap smear', 1, 'C', 0, 0, '', '', true);
                $this->MultiCell($w[3], $h[1], 'CBC', 1, 'C', 0, 0, '', '', true);
                $this->MultiCell($w[4], $h[1], 'Urine Exam', 1, 'C', 0, 0, '', '', true);
                $this->MultiCell($w[4], $h[1], 'Stool Exam', 1, 'C', 0, 0, '', '', true);
                $this->MultiCell($w[7], $h[1], 'Glucose', 1, 'C', 0, 0, '', '', true);
                $this->MultiCell($w[3], $h[1], 'BUN', 1, 'C', 0, 0, '', '', true);
                $this->MultiCell($w[9], $h[1], 'Creatinine', 1, 'C', 0, 0, '', '', true);
                $this->MultiCell($w[4], $h[1], 'Uric Acid', 1, 'C', 0, 0, '', '', true);
                $this->MultiCell($w[11], $h[1], 'Cholesterol', 1, 'C', 0, 0, '', '', true);
                $this->MultiCell($w[11], $h[1], 'Triglyceride', 1, 'C', 0, 0, '', '', true);
                $this->MultiCell($w[4], $h[1], 'LFT', 1, 'C', 0, 0, '', '', true);
                $this->MultiCell($w[7], $h[1], 'Serology', 1, 'C', 0, 0, '', '', true);
                $this->MultiCell($w[4], $h[1], 'CXR', 1, 'C', 0, 0, '', '', true);
                $this->MultiCell($w[4], $h[1], 'PSA', 1, 'C', 0, 0, '', '', true);
                $this->MultiCell($w[4], $h[1], 'EKG', 1, 'C', 0, 0, '', '', true);
                $this->MultiCell($w[4], $h[1], 'Thin Prep', 1, 'C', 0, 0, '', '', true);
                $this->MultiCell($w[4], $h[1], 'HPV DNA', 1, 'C', 0, 0, '', '', true);
                $this->MultiCell($w[6], $h[1], 'เงิน ทั้งสิ้น', 'LRB', 'C', 0, 1, '', '', true);
            } elseif ($this->data['report_type'] == 'LGO') {
                $this->Image($path, 10, 3, 18, 18, 'JPG');
                $this->ln(5);
                $this->SetFont($this->fontName, 'B', 18);
                $this->Cell(0, 0, 'โรงพยาบาลมะเร็งอุดรธานี', 0, 1, 'C', 0, '', 0, false, 'T', 'M');
                $this->Cell(0, 0, 'รายงานแจ้งค่ารักษาผู้ป่วยนอก โครงการรักษาต่อเนื่อง(อปท.) ', 0, 1, 'C', 0, '', 0, false, 'T', 'M');
            }
        }

        function Footer() {
            $this->SetY(-13);
            // Set font
            $this->SetFont($this->fontName, '', 13);
            $this->Cell(0, $this->spaceHigh, 'หน้าที่ ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, 1, 'R', 0, '', 0, false, 'T', 'M');
        }

    }
    