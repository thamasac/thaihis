<?php

namespace backend\modules\cpoe\controllers;

use Yii;
use backend\modules\cpoe\classes\CpoeFunc;
use backend\modules\patient\classes\PatientFunc;
use backend\modules\patient\classes\PatientQuery;
use appxq\sdii\utils\SDdate;

class ReportCheckupSendController extends \yii\web\Controller {

    public function actionIndex() {
        Yii::$app->session['ezf_input'] = \backend\modules\ezforms2\classes\EzfQuery::getInputv2All();
        $ezf_id = \backend\modules\patient\Module::$formID['report_checkup'];
        $ezfOrder_id = \backend\modules\patient\Module::$formID['order_tran'];

        $searchModel = PatientFunc::getModel($ezf_id, '');
        $searchModel['ckr_status'] = '2';
//        $searchModel['create_date'] = date('d/m/Y');
        $dataProvider = CpoeFunc::getReportCheckupSend($searchModel, Yii::$app->request->get());

        return $this->render('reportcounter', [
                    'ezf_id' => $ezf_id,
                    'ezfOrder_id' => $ezfOrder_id,
                    'dataProvider' => $dataProvider,
                    'searchModel' => $searchModel
        ]);
    }

    public function actionPrintReport() {
        $dataid = Yii::$app->request->get('dataid');
        $pt_hn = Yii::$app->request->get('pt_hn');
        $visit_id = Yii::$app->request->get('visit_id');

//        $pdf = new \common\lib\tcpdf\SDPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);
        $pdf = new REPORTCHK('P', PDF_UNIT, 'A4', true, 'UTF-8', false);
        $pdf->SetCreator('AppXQ');
        $pdf->SetAuthor('iencoded@gmail.com');
        $pdf->SetTitle('ผลการตรวจสุขภาพ ');
        $pdf->SetSubject('Original');
        $pdf->SetKeywords('AppXQ, SDII, PDF, report, medical,');

        // remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(TRUE);

        // set margins
        $pdf->SetMargins(10, 5, 0, TRUE);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, 30);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set font
        $pdf->SetFont($pdf->fontName, '', $pdf->fontSize);
        $pdf->fontSize = 14;

        //Content      
        $this->reportContent($pdf, $visit_id, $pt_hn);

        $pdf->Output('ReportCheckup' . $pt_hn . '.pdf', 'I');
        Yii::$app->end();
    }

    private function reportContent($pdf, $visit_id, $pt_hn) {
        $ezf_table = \backend\modules\patient\Module::$formTableName['order_tran'];
        $dataOrder = PatientQuery::getOrderByVisit($visit_id);
        $data = \backend\modules\cpoe\classes\CpoeQuery::getCheckupResult($visit_id);
        $pdf->data = $data;
        $pagePap = FALSE;
        $pageEkg = FALSE;
        if (/* $data['visit_type'] == '4' */empty($data['ckr_pe']) && !empty($data['ckr_epeptp'])) {
            $pagePap = TRUE;
        } else {
            $pdf->AddPage();
            $date = substr($data['visit_date'], 0, 10); //'2018-01-03';
            $resultLab = PatientQuery::getLabResultOneRecord($pt_hn, $date, '', '');

            //content        
//            $path = Yii::getAlias('@storageUrl/images') . '/logo2.jpg';
            $dataid = \backend\modules\patient\Module::$dataidForm['hos_config'];
            $ezf_table = \backend\modules\patient\Module::$formTableName['hos_config'];
            $dataHosConfig = \backend\modules\ezforms2\classes\EzfUiFunc::loadTbData($ezf_table, $dataid);
            if ($dataHosConfig['logo_02']) {
                try {
                    $path = Yii::getAlias('@storageUrl/ezform/fileinput') . '/' . $dataHosConfig['logo_02'];
                } catch (Exception $ex) {
                    $path = null;
                }
            } else {
                $path = null;
            }

            $pdf->Image($path, 55, 2, 18, 18, 'JPG');
            $pdf->SetFont($pdf->fontName, 'B', 18);
            $pdf->Cell(195, 0, 'รายงานตรวจสุขภาพประจำปี ' . (substr($data['visit_date'], 0, 4) + 543), 0, 1, 'C', 0, '', 0, false, 'T', 'M');
            $pdf->SetFont($pdf->fontName, 'B', $pdf->fontSize);
            $pdf->Cell(195, 0, SDdate::mysql2phpThDate($data['visit_date']), 0, 1, 'C', 0, '', 0, false, 'T', 'M');
            $pdf->ln(2);
//            $pdf->SetFont($pdf->fontName, 'B', $pdf->fontSize);
            $pdf->Cell(5, 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->Cell(195, 0, $data['fullname'] . ' อายุ ' . SDdate::getAge(SDdate::dateTh2bod($data['pt_bdate'])) . ' ปี  HN ' . $data['pt_hn'], 0, 1, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->SetFont($pdf->fontName, 'BU', $pdf->fontSize);
            $pdf->Cell(195, 0, 'ข้อมูลการตรวจ', 0, 1, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->SetFont($pdf->fontName, 'B', $pdf->fontSize);
            $pdf->Cell(5, 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->Cell(195, 0, (!empty($data['BP']) ? 'ความดันโลหิต ' . $data['BP'] . ' มม.ปรอท' : '') . (!empty($data['vs_pulse']) ? ' ชีพจร ' . $data['vs_pulse'] . ' ครั้ง/นาที ' : '') . (!empty($data['vs_respiratory']) ? 'หายใจ ' . $data['vs_respiratory'] . ' ครั้ง/นาที ' : ''), 0, 1, 'L', 0, '', 0, false, 'T', 'M');
            if (!empty($data['bmi_bw'])) {
                $pdf->Cell(5, 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
                $pdf->Cell(195, 0, 'น้ำหนัก ' . $data['bmi_bw'] . ' กก. ส่วนสูง ' . $data['bmi_ht'] . ' ชม. รอบเอว ' . $data['bmi_waistline'] . ' ซม. ดัชนีมวลกาย(BMI) ' . (!empty($data['bmi_bmi']) ? number_format($data['bmi_bmi'], 2) : '') . ' ' . $this->getValue($data, 'ckr_sum_bmi'), 0, 1, 'L', 0, '', 0, false, 'T', 'M');
            }
            $pdf->ln(2);
            $w = [0 => 70, 1 => 125];
            $wLab = [0 => 50, 1 => 28, 2 => 31, 3 => 16];
            $pdf->Cell($w[0], 0, 'รายการตรวจสุขภาพ', 1, 0, 'C', 0, '', 0, false, 'T', 'M');
            $pdf->Cell($w[1], 0, 'ผลการตรวจ', 1, 1, 'C', 0, '', 0, false, 'T', 'M');
            $pdf->SetFont($pdf->fontName, '', $pdf->fontSize);
            $pdf->Cell($w[0], 0, 'ตรวจร่างกายทั่วไป (Physical Exam)', 1, 0, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->MultiCell($w[1], 0, $this->getValue($data, 'ckr_pe'), 1, 'C');
            if ($data['ckr_breast']) {
                $pdf->Cell($w[0], 0, 'ตรวจเต้านม (Breast)', 1, 0, 'L', 0, '', 0, false, 'T', 'M');
                $pdf->MultiCell($w[1], 0, $this->getValue($data, 'ckr_breast'), 1, 'C');
            }
            foreach ($dataOrder as $value) {
                switch ($value['order_tran_code']) {
                    case 'CH':
//                        $pdf->Cell($w[0], 0, 'ตรวจเอ็กซเรย์ทรวงอก (Chest X-ray)', 1, 0, 'L', 0, '', 0, false, 'T', 'M');
                        $pdf->Cell($w[0], 0, 'ตรวจเอ็กซเรย์ทรวงอก (Chest X-ray)', 1, 0, 'L', 0, '', 0, false, 'T', 'M');
                        if ($data['ckr_cxr'] == 2) {
                            $dataXray = PatientQuery::getOrderCounterItemReport($visit_id, 'CH');
                            $text = str_replace("</p>", "", str_replace("<p>", "<br/>", $dataXray[0]['result']));
                            $text = str_replace("</div>", "", str_replace("<div>", "<br/>", $text));
                            $text = str_replace('"=', '=', $text);
                            $text = str_replace('<p ="">', '<br/>', $text);
                            $text = str_replace('<br>', '', $text);
                            $pdf->writeHTMLCell($w[1], '', '', '', $text, 1, 1);

                            $pdf->Cell($w[0], 0, 'ผลการตรวจ ', 1, 0, 'R', 0, '', 0, false, 'T', 'M');
                            $pdf->SetTextColor(255, 0, 0);
                        }
                        $pdf->MultiCell($w[1], 0, $this->getValue($data, 'ckr_cxr'), 1, 'C');
                        $pdf->SetTextColor(0, 0, 0);
                        break;
                    case 'FE001':
                        if ($data['ckr_se']) {
                            $pdf->Cell($w[0], 0, 'ตรวจอุจจาระ (Stool Exam)', 1, 0, 'L', 0, '', 0, false, 'T', 'M');
                            if ($data['ckr_se'] == 2) {
                                $pdf->Cell($wLab[0], 0, 'การตรวจ', 1, 0, 'L', 0, '', 0, false, 'T', 'M');
                                $pdf->Cell($wLab[1], 0, 'ผล', 1, 0, 'C', 0, '', 0, false, 'T', 'M');
                                $pdf->Cell($wLab[2], 0, 'ค่าปกติ', 1, 0, 'C', 0, '', 0, false, 'T', 'M');
                                $pdf->Cell($wLab[3], 0, 'หมายเหตุ', 1, 1, 'C', 0, '', 0, false, 'T', 'M');
                                foreach ($resultLab as $value) {
                                    if ($value['secname'] == 'Stool Exam') {
                                        $pdf->Cell($w[0], 0, '', 'LR', 0, 'L', 0, '', 0, false, 'T', 'M');
                                        $unit = empty($value['unit']) ? ' ' : ' ( ' . $value['unit'] . ' ) ';
                                        $pdf->Cell($wLab[0], 0, $value['test_name'] . $unit, 1, 0, 'L', 0, '', 0, false, 'T', 'M');
                                        if ($value['commt_all']) {
                                            $pdf->SetTextColor(255, 0, 0);
                                        }
                                        $pdf->Cell($wLab[1], 0, $value['result'], 1, 0, 'C', 0, '', 0, false, 'T', 'M');
                                        $pdf->SetTextColor(0, 0, 0);
                                        $pdf->Cell($wLab[2], 0, $value['normal_range'], 1, 0, 'C', 0, '', 0, false, 'T', 'M');
                                        if (empty($value['commt_all'])) {
                                            $txtValue = 'ปกติ';
                                        } else {
                                            $pdf->SetTextColor(255, 0, 0);
                                            $txtValue = 'ผิดปกติ';
                                        }
                                        $pdf->Cell($wLab[3], 0, $txtValue, 1, 1, 'C', 0, '', 0, false, 'T', 'M');
                                        $pdf->SetTextColor(0, 0, 0);
                                    }
                                }
                                $pdf->Cell($w[0], 0, 'ผลการตรวจ ', 1, 0, 'R', 0, '', 0, false, 'T', 'M');
                                $pdf->SetTextColor(255, 0, 0);
//                            $pdf->Cell($w[1], 0, $this->getValue($data, 'ckr_se'), 1, 1, 'C', 0, '', 0, false, 'T', 'M');
                                $pdf->MultiCell($w[1], 0, $this->getValue($data, 'ckr_se'), 1, 'C');
                                $pdf->SetTextColor(0, 0, 0);
                            } else {
                                $pdf->Cell($w[1], 0, $this->getValue($data, 'ckr_se'), 1, 1, 'C', 0, '', 0, false, 'T', 'M');
                            }
                        }
                        break;
                    case 'HM001':
                        $pdf->Cell($w[0], 0, 'ตรวจเม็ดเลือด,ความเข้มข้นของเลือด (CBC)', 1, 0, 'L', 0, '', 0, false, 'T', 'M');
                        if ($data['ckr_cbc'] == 2) {
                            $pdf->Cell($wLab[0], 0, 'การตรวจ', 1, 0, 'L', 0, '', 0, false, 'T', 'M');
                            $pdf->Cell($wLab[1], 0, 'ผล', 1, 0, 'C', 0, '', 0, false, 'T', 'M');
                            $pdf->Cell($wLab[2], 0, 'ค่าปกติ', 1, 0, 'C', 0, '', 0, false, 'T', 'M');
                            $pdf->Cell($wLab[3], 0, 'หมายเหตุ', 1, 1, 'C', 0, '', 0, false, 'T', 'M');
                            foreach ($resultLab as $value) {
                                if ($value['secname'] == 'CBC') {
                                    $pdf->Cell($w[0], 0, '', 'LR', 0, 'L', 0, '', 0, false, 'T', 'M');
                                    $unit = empty($value['unit']) ? ' ' : ' ( ' . $value['unit'] . ' ) ';
                                    $pdf->Cell($wLab[0], 0, $value['test_name'] . $unit, 1, 0, 'L', 0, '', 0, false, 'T', 'M');
                                    if ($value['commt_all']) {
                                        $pdf->SetTextColor(255, 0, 0);
                                    }
                                    $pdf->Cell($wLab[1], 0, $value['result'], 1, 0, 'C', 0, '', 0, false, 'T', 'M');
                                    $pdf->SetTextColor(0, 0, 0);
                                    $pdf->Cell($wLab[2], 0, $value['normal_range'], 1, 0, 'C', 0, '', 0, false, 'T', 'M');
                                    if (empty($value['commt_all'])) {
                                        $txtValue = 'ปกติ';
                                    } else {
                                        $pdf->SetTextColor(255, 0, 0);
                                        $txtValue = 'ผิดปกติ';
                                    }
                                    $pdf->Cell($wLab[3], 0, $txtValue, 1, 1, 'C', 0, '', 0, false, 'T', 'M');
                                    $pdf->SetTextColor(0, 0, 0);
                                }
                            }
                            $pdf->Cell($w[0], 0, 'ผลการตรวจ ', 1, 0, 'R', 0, '', 0, false, 'T', 'M');
                            $pdf->SetTextColor(255, 0, 0);
//                            $pdf->Cell($w[1], 0, $this->getValue($data, 'ckr_cbc'), 1, 1, 'C', 0, '', 0, false, 'T', 'M');
                            $pdf->MultiCell($w[1], 0, $this->getValue($data, 'ckr_cbc'), 1, 'C');
                            $pdf->SetTextColor(0, 0, 0);
                        } else {
                            $pdf->Cell($w[1], 0, $this->getValue($data, 'ckr_cbc'), 1, 1, 'C', 0, '', 0, false, 'T', 'M');
                        }
                        break;
                    case 'UR001':
                        if ($data['ckr_ua']) {
                            $pdf->Cell($w[0], 0, 'ตรวจปัสสาวะ (U/A)', 1, 0, 'L', 0, '', 0, false, 'T', 'M');
                            if ($data['ckr_ua'] == 2) {
                                $pdf->Cell($wLab[0], 0, 'การตรวจ', 1, 0, 'L', 0, '', 0, false, 'T', 'M');
                                $pdf->Cell($wLab[1], 0, 'ผล', 1, 0, 'C', 0, '', 0, false, 'T', 'M');
                                $pdf->Cell($wLab[2], 0, 'ค่าปกติ', 1, 0, 'C', 0, '', 0, false, 'T', 'M');
                                $pdf->Cell($wLab[3], 0, 'หมายเหตุ', 1, 1, 'C', 0, '', 0, false, 'T', 'M');
                                foreach ($resultLab as $value) {
                                    if ($value['secname'] == 'Urine Exam') {
                                        $pdf->Cell($w[0], 0, '', 'LR', 0, 'L', 0, '', 0, false, 'T', 'M');
                                        $unit = empty($value['unit']) ? ' ' : ' ( ' . $value['unit'] . ' ) ';
                                        $pdf->Cell($wLab[0], 0, $value['test_name'] . $unit, 1, 0, 'L', 0, '', 0, false, 'T', 'M');
                                        if ($value['commt_all']) {
                                            $pdf->SetTextColor(255, 0, 0);
                                        }
                                        $pdf->Cell($wLab[1], 0, $value['result'], 1, 0, 'C', 0, '', 0, false, 'T', 'M');
                                        $pdf->SetTextColor(0, 0, 0);
                                        $pdf->Cell($wLab[2], 0, $value['normal_range'], 1, 0, 'C', 0, '', 0, false, 'T', 'M');
                                        if (empty($value['commt_all'])) {
                                            $txtValue = 'ปกติ';
                                        } else {
                                            $pdf->SetTextColor(255, 0, 0);
                                            $txtValue = 'ผิดปกติ';
                                        }
                                        $pdf->Cell($wLab[3], 0, $txtValue, 1, 1, 'C', 0, '', 0, false, 'T', 'M');
                                        $pdf->SetTextColor(0, 0, 0);
                                    }
                                }
                                $pdf->Cell($w[0], 0, 'ผลการตรวจ ', 1, 0, 'R', 0, '', 0, false, 'T', 'M');
                                $pdf->SetTextColor(255, 0, 0);
//                            $pdf->Cell($w[1], 0, $this->getValue($data, 'ckr_ua'), 1, 1, 'C', 0, '', 0, false, 'T', 'M');
                                $pdf->MultiCell($w[1], 0, $this->getValue($data, 'ckr_ua'), 1, 'C');
                                $pdf->SetTextColor(0, 0, 0);
                            } else {
                                $pdf->Cell($w[1], 0, $this->getValue($data, 'ckr_ua'), 1, 1, 'C', 0, '', 0, false, 'T', 'M');
                            }
                        }
                        break;
                    //ตรวจเพิ่มพิเศษ
                    case 'IM006':
                        $pdf->Cell($w[0], 0, 'ตรวจเลือดหาเชื้อซิฟิลิส (RPR)', 1, 0, 'L', 0, '', 0, false, 'T', 'M');
                        if ($data['ckr_rpr'] == 2) {
                            $pdf->SetTextColor(255, 0, 0);
                        }
                        $pdf->Cell($w[1], 0, $this->getValue($data, 'ckr_rpr'), 1, 1, 'C', 0, '', 0, false, 'T', 'M');
                        $pdf->SetTextColor(0, 0, 0);
                        break;
                    case 'IM002':
                        $pdf->Cell($w[0], 0, 'ตรวจเลือดหาเชื้อไวรัสตับอักเสบบี (HBsAg)', 1, 0, 'L', 0, '', 0, false, 'T', 'M');
                        if ($data['ckr_hba'] == 2) {
                            $pdf->SetTextColor(255, 0, 0);
                        }
                        $pdf->Cell($w[1], 0, $this->getValue($data, 'ckr_hba'), 1, 1, 'C', 0, '', 0, false, 'T', 'M');
                        $pdf->SetTextColor(0, 0, 0);
                        break;
                    case 'PH001':
                        if ($data['ckr_ekg'] == 1) {
                            $pdf->Cell($w[0], 0, 'ตรวจคลื่นไฟฟ้าหัวใจ (EKG)', 1, 0, 'L', 0, '', 0, false, 'T', 'M');
                            $pdf->Cell($w[1], 0, $this->getValue($data, 'ckr_ekg'), 1, 1, 'C', 0, '', 0, false, 'T', 'M');
                        } elseif ($data['ckr_ekg'] == 2) {
                            $pageEkg = TRUE;
                            $orderekg_id = $value['id'];
                        }
                        break;
                    case 'CG001':
                        if ($data['ckr_cyto'] == '1') {
                            if ($data['ckr_epeptp'] !== '3') {
                                $pdf->Cell($w[0], 0, 'ตรวจมะเร็งปากมดลูก ' . ($data['ckr_epeptp'] == '1' ? 'E - Prep' : 'ThinPrep'), 1, 0, 'L', 0, '', 0, false, 'T', 'M');

                                $pdf->writeHTMLCell($w[1], '', '', '', '<div align="center">เซลล์ปากมดลูก <strong>ปกติ</strong> (Normal : Negative cancer cell)</div>', 'RTL', 1);
                                if ($data['ckr_sgt_1'] == '1') {
                                    $pdf->Cell($w[0], 0, '', 'R', 0, 'L', 0, '', 0, false, 'T', 'M');
                                    $pdf->Cell($w[1], 0, 'ควรมารับการตรวจคัดกรองมะเร็งปากมดลูกทุก ' . $this->getValue($data, 'ckr_sgt_other_1'), 'LRB', 1, 'C', 0, '', 0, false, 'T', 'M');
                                }
                            } else {
                                $pagePap = TRUE;
                            }
                        } else {
                            $pagePap = TRUE;
                        }
                        $cyto_id = $value['id'];
                        break;
//                    case 'CG016':
//                        $pdf->Cell($w[0], 0, 'ตรวจหามะเร็งปากมดลูก ตรวจพิเศษ(HPV DNA)', 1, 0, 'L', 0, '', 0, false, 'T', 'M');
//                        if ($data['ckr_hpv'] == '1') {
//                            $pdf->Cell($w[1], 0, $this->getValue($data, 'ckr_hpv'), 1, 1, 'C', 0, '', 0, false, 'T', 'M');
//                        } elseif ($data['ckr_hpv'] == '2') {
//                            $pdf->SetTextColor(255, 0, 0);
//                            $pdf->MultiCell($w[1], 0, $this->getValue($data, 'ckr_hpv'), 1, 'C');
//                            $pdf->SetTextColor(0, 0, 0);
//                        }
//                        break;
                }
            }

            //Lab Chem
            $pdf->ln(2);
            $w = [0 => 123.75, 1 => 23.75, 2 => 23.75, 3 => 23.75];
            $secname = '';
            $chem = FALSE;
            foreach ($resultLab as $value) {
                $value['result'] = str_replace(",", '', $value['result']);
                if (is_numeric($value['result'])) {
                    if ($value['secname'] == 'Chemistry') {
                        if ($secname !== $value['secname']) {
                            $chem = TRUE;
                            $pdf->Cell($w[0], 0, 'การตรวจสารเคมีในเลือดกลุ่ม ' . $value['secname'], 1, 0, 'C', 0, '', 0, false, 'T', 'M');
                            $pdf->Cell($w[1], 0, 'ค่าปกติ', 1, 0, 'C', 0, '', 0, false, 'T', 'M');
                            $pdf->Cell($w[2], 0, 'ผลตรวจ', 1, 0, 'C', 0, '', 0, false, 'T', 'M');
                            $pdf->Cell($w[3], 0, 'หมายเหตุ', 1, 1, 'C', 0, '', 0, false, 'T', 'M');

                            $secname = $value['secname'];
                        }

                        $pdf->Cell($w[0], 0, Yii::t('patient', $value['test_name'], ['unit' => $value['unit']]), 1, 0, 'L', 0, '', 0, false, 'T', 'M');
                        $pdf->Cell($w[1], 0, $value['normal_range'], 1, 0, 'C', 0, '', 0, false, 'T', 'M');
                        if ($value['commt_all']) {
                            $pdf->SetTextColor(255, 0, 0);
                        }
                        $pdf->Cell($w[2], 0, $value['result'], 1, 0, 'C', 0, '', 0, false, 'T', 'M');
                        $pdf->SetTextColor(0, 0, 0);
                        if (empty($value['commt_all'])) {
                            $txtValue = 'ปกติ';
                        } else {
                            $pdf->SetTextColor(255, 0, 0);
                            $txtValue = 'ผิดปกติ';
                        }
                        $pdf->Cell($w[3], 0, $txtValue, 1, 1, 'C', 0, '', 0, false, 'T', 'M');
                        $pdf->SetTextColor(0, 0, 0);
                    }
                }
            }
            if ($data['ckr_chem'] == '2') {
                $pdf->SetTextColor(255, 0, 0);
                $pdf->Cell($w[0] + $w[1] + $w[2] + $w[3], 0, 'สรุปผลการตรวจ ' . $this->getValue($data, 'ckr_chem'), 1, 1, 'C', 0, '', 0, false, 'T', 'M');
            }
            $pdf->SetTextColor(0, 0, 0);
            if ($chem) {
                $pdf->ln(2);
            }
            $serology = FALSE;
            foreach ($resultLab as $value) {
                $value['result'] = str_replace(",", '', $value['result']);
                if (is_numeric($value['result']) || $value['test_name'] == 'CEA') {
                    if ($value['secname'] == 'Serology') {
                        if ($secname !== $value['secname']) {
                            $serology = TRUE;
                            $pdf->Cell($w[0], 0, 'การตรวจสารเคมีในเลือดกลุ่ม ' . $value['secname'], 1, 0, 'C', 0, '', 0, false, 'T', 'M');
                            $pdf->Cell($w[1], 0, 'ค่าปกติ', 1, 0, 'C', 0, '', 0, false, 'T', 'M');
                            $pdf->Cell($w[2], 0, 'ผลตรวจ', 1, 0, 'C', 0, '', 0, false, 'T', 'M');
                            $pdf->Cell($w[3], 0, 'หมายเหตุ', 1, 1, 'C', 0, '', 0, false, 'T', 'M');

                            $secname = $value['secname'];
                        }

                        $pdf->Cell($w[0], 0, Yii::t('patient', $value['test_name'], ['unit' => $value['unit']]), 1, 0, 'L', 0, '', 0, false, 'T', 'M');
                        $pdf->Cell($w[1], 0, $value['normal_range'], 1, 0, 'C', 0, '', 0, false, 'T', 'M');
                        if ($value['commt_all']) {
                            $pdf->SetTextColor(255, 0, 0);
                        }
                        $pdf->Cell($w[2], 0, $value['result'], 1, 0, 'C', 0, '', 0, false, 'T', 'M');
                        $pdf->SetTextColor(0, 0, 0);
                        if (empty($value['commt_all'])) {
                            $txtValue = 'ปกติ';
                        } else {
                            $pdf->SetTextColor(255, 0, 0);
                            $txtValue = 'ผิดปกติ';
                        }
                        $pdf->Cell($w[3], 0, $txtValue, 1, 1, 'C', 0, '', 0, false, 'T', 'M');
                        $pdf->SetTextColor(0, 0, 0);
                    }
                }
            }
            if ($data['ckr_sero'] == '2') {
                $pdf->SetTextColor(255, 0, 0);
                $pdf->Cell($w[0] + $w[1] + $w[2] + $w[3], 0, 'สรุปผลการตรวจ ' . $this->getValue($data, 'ckr_sero'), 1, 1, 'C', 0, '', 0, false, 'T', 'M');
            }
            $pdf->SetTextColor(0, 0, 0);
            if ($serology) {
                $pdf->ln(1);
            }
            $pdf->SetFont($pdf->fontName, 'B', $pdf->fontSize);
            $pdf->Cell(25, 0, 'สรุปผลการตรวจ ', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->SetFont($pdf->fontName, 'BU', $pdf->fontSize);
//            $pdf->Cell(170, 0, $this->getValue($data, 'ckr_summary'), 0, 1, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->MultiCell(170, $pdf->spaceHigh, $this->getValue($data, 'ckr_summary'), 0, 'L');

            if ($data['ckr_summary_detail']) {
                $pdf->SetFont($pdf->fontName, 'B', $pdf->fontSize);
                $pdf->Cell(25, 0, 'คำแนะนำ', 0, 0, 'R', 0, '', 0, false, 'T', 'M');
                $pdf->SetFont($pdf->fontName, '', $pdf->fontSize);
                $pdf->MultiCell(170, $pdf->spaceHigh, $data['ckr_summary_detail'], 0, 'L');
            }
            $pdf->SetFont($pdf->fontName, '', $pdf->fontSize);
            $pdf->ln(1);
            $pdf->Cell(195, 0, 'โอกาสเสี่ยงที่จะเป็นโรคกล้ามเนื้อหัวใจตาย (myocardial infarction) และโรคอัมพฤกษ์ อัมพาต (stroke:fatal,non-fatal)', 0, 1, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->Cell(195, 0, 'ใน 10 ปีข้างหน้า คุณมีโอกาส ' . $this->getValue($data, 'ckr_about') . ' (ที่มา:Thai CV risk score) ', 0, 1, 'L', 0, '', 0, false, 'T', 'M');
            if ($data['ckr_seedoctor'] == '1') {
                $pdf->Cell(195, 0, '        ควรพบแพทย์ที่ รพ.ใกล้บ้าน เพื่อรับยารักษา ' . $data['ckr_order_drug'], 0, 1, 'L', 0, '', 0, false, 'T', 'M');
            }
            if ($data['ckr_seedoctor2'] == '1') {
                $pdf->Cell(195, 0, '        ควรพบแพทย์ที่ รพ.มะเร็งอุดรธานี เพื่อรับยารักษา ' . $data['ckr_order_drug2'], 0, 1, 'L', 0, '', 0, false, 'T', 'M');
            }
            if ($data['ckr_resultcheck'] == '1') {
                $pdf->MultiCell(190, $pdf->spaceHigh, '        ให้นำผลตรวจนี้ไปปรึกษาแพทย์ รพ.ใกล้บ้าน เพื่อตรวจ ' . $data['ckr_resultcheck_detail'], 0, 'L');
            }
            if ($data['ckr_resultcheck2'] == '1') {
                $pdf->MultiCell(190, $pdf->spaceHigh, '        ให้นำผลตรวจนี้ไปปรึกษาแพทย์ รพ.มะเร็งอุดรธานี เพื่อตรวจ ' . $data['ckr_resultcheck_detail2'], 0, 'L');
            }
        }

        if ($pageEkg) {
            $this->reportEkg($pdf, $data, $orderekg_id);
        }

        if ($pagePap) {
            $this->reportPap($pdf, $data);
        }

        if ($data['ckr_attach_file']) {
            $ezf_table = \backend\modules\patient\Module::$formTableName['cytoreport'];
            $dataCyto = \backend\modules\patient\classes\PatientFunc::loadTbDataByTarget($ezf_table, $cyto_id);
            if ($dataCyto['cyto_file']) {
                $this->reportCytoAbnormal($pdf, $data, $dataCyto['cyto_file']);
            }
        }
    }

    private function reportPap($pdf, $data) {
        $pdf->fontSize = 16;
//        $pdf->SetMargins(20, 15, 0, TRUE);
        $pdf->AddPage();
//        $path = Yii::getAlias('@storageUrl/images') . '/logo2.jpg';
        $dataid = \backend\modules\patient\Module::$dataidForm['hos_config'];
        $ezf_table = \backend\modules\patient\Module::$formTableName['hos_config'];
        $dataHosConfig = \backend\modules\ezforms2\classes\EzfUiFunc::loadTbData($ezf_table, $dataid);
        if ($dataHosConfig['logo_02']) {
            try {
                $path = Yii::getAlias('@storageUrl/ezform/fileinput') . '/' . $dataHosConfig['logo_02'];
            } catch (Exception $ex) {
                $path = null;
            }
        } else {
            $path = null;
        }

        $pdf->Image($path, 38, 3, 25, 25, 'JPG');
        $pdf->SetFont($pdf->fontName, 'B', 18);
        $w = [0 => 5, 1 => 10, 2 => 192];
        //right
        $pdf->Cell(195, 0, 'โรงพยาบาลมะเร็งอุดรธานี', 0, 1, 'C', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(195, 0, 'ใบแจ้งผลการตรวจคัดกรองมะเร็งปากมดลูก', 0, 1, 'C', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(195, 0, 'โดยวิธี ' . $this->getValue($data, 'ckr_epeptp'), 0, 1, 'C', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont($pdf->fontName, 'B', $pdf->fontSize);
        $pdf->ln();
        $pdf->Cell($w[0], 0, '', 0, 0, 'R', 0, '', 0, false, 'T', 'M');
        $pdf->Cell($w[2], 0, $data['fullname'] . ' อายุ : ' . SDdate::getAge(SDdate::dateTh2bod($data['pt_bdate'])) . ' ปี  HN ' . $data['pt_hn'] . ' ' . SDdate::mysql2phpThDate($data['visit_date']), 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        if (empty($data['ckr_cbc'])) {
            $pdf->Cell(195, 0, 'ข้อมูลการตรวจ', 0, 1, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->SetFont($pdf->fontName, 'B', $pdf->fontSize);
            $pdf->Cell(5, 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->Cell(195, 0, (!empty($data['BP']) ? 'ความดันโลหิต ' . $data['BP'] . ' มม.ปรอท' : '') . (!empty($data['vs_pulse']) ? ' ชีพจร ' . $data['vs_pulse'] . ' ครั้ง/นาที ' : '') . (!empty($data['vs_respiratory']) ? 'หายใจ ' . $data['vs_respiratory'] . ' ครั้ง/นาที ' : ''), 0, 1, 'L', 0, '', 0, false, 'T', 'M');
            if (!empty($data['bmi_bw'])) {
                $pdf->Cell(5, 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
                $pdf->Cell(195, 0, 'น้ำหนัก ' . $data['bmi_bw'] . ' กก. ส่วนสูง ' . $data['bmi_ht'] . ' ชม. รอบเอว ' . $data['bmi_waistline'] . ' ซม. ดัชนีมวลกาย(BMI) ' . $data['bmi_bmi'] . ' ' . $this->getValue($data, 'ckr_sum_bmi'), 0, 1, 'L', 0, '', 0, false, 'T', 'M');
            }
        }
        $pdf->SetFont($pdf->fontName, 'BU', $pdf->fontSize);
        $pdf->Cell(195, 0, 'ผลการตรวจเซลล์วิทยาของปากมดลูก (Cervical Cytology)', 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont($pdf->fontName, 'B', $pdf->fontSize);
        if ($data['ckr_cyto'] == '1') {
            $pdf->Cell($w[0], 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->Cell(190, 0, 'เซลล์ปากมดลูก ปกติ (Normal : Negative cancer cell)', 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        }
        if ($data['ckr_ab'] == '1') {
            $pdf->Cell($w[0], 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->Cell(190, 0, 'เซลล์ปากมดลูก ปกติ แต่พบ ' . $data['ckr_ab_other'], 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        }
        $pdf->SetFont($pdf->fontName, '', $pdf->fontSize);
        if ($data['ckr_tibvc_1'] == '1') {
            $tibvc = ($data['ckr_caypa_1'] == '1' ? 'Candida spp' : '') . ($data['ckr_caypa_2'] == '1' ? ' ,Yeast form' : '') . ($data['ckr_caypa_3'] == '1' ? ' ,Pseudohyphae' : '') . ($data['ckr_caypa_4'] == '1' ? ' ,Actinomyces spp' : '');
            $pdf->Cell($w[1], 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->Cell($w[2], 0, 'เชื้อราในช่องคลอดชนิด ' . $tibvc, 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        }
        if ($data['ckr_tibvc_2'] == '1') {
            $pdf->Cell($w[1], 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->Cell($w[2], 0, 'เชื้อพยาธิในช่องคลอด (Trichomomas spp)', 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        }
        if ($data['ckr_tibvc_3'] == '1') {
            $pdf->Cell($w[1], 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->Cell($w[2], 0, 'ปากมดลูกอักเสบ (Inflammation)', 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        }
        if ($data['ckr_tibvc_4'] == '1') {
            $pdf->Cell($w[1], 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->Cell($w[2], 0, 'ช่องคลอดอักเสบจากเชื้อแบคทีเรีย (Bacterial vaginosis)', 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        }
        if ($data['ckr_tibvc_5'] == '1') {
            $pdf->Cell($w[1], 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->Cell($w[2], 0, 'ช่องคลอดอักเสบ (Vaginitis)', 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        }
        if ($data['ckr_tibvc_6'] == '1') {
            $pdf->Cell($w[1], 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->Cell($w[2], 0, 'ปากมดลูกอักเสบ (Cervicitis)', 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        }

        if ($data['ckr_awscc'] == '1') {
            $pdf->SetFont($pdf->fontName, 'B', $pdf->fontSize);
            $pdf->ln();
            $pdf->Cell($w[0], 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->Cell($w[2], 0, 'ผิดปกติสงสัยเซลล์มะเร็ง (Abnormal with suspected cancer cell ) คือ', 0, 1, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->SetFont($pdf->fontName, '', $pdf->fontSize);
            $pdf->Cell($w[1], 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->Cell($w[2], 0, $this->getValue($data, 'ckr_haalh'), 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        }
        if ($data['ckr_awcc'] == '1') {
            $pdf->SetFont($pdf->fontName, 'B', $pdf->fontSize);
            $pdf->ln();
            $pdf->Cell($w[0], 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->Cell($w[2], 0, 'ผิดปกติพบเซลล์มะเร็ง (Abnormal with cancer cell ) คือ', 0, 1, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->SetFont($pdf->fontName, '', $pdf->fontSize);
            $pdf->Cell($w[1], 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->Cell($w[2], 0, $this->getValue($data, 'ckr_awcc_scc'), 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        }
        if ($data['ckr_epeptp'] == '3') {
            $pdf->SetFont($pdf->fontName, 'B', $pdf->fontSize);
            $pdf->ln();
            $pdf->Cell($w[0], 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->Cell($w[2], 0, 'ผลการตรวจหาเชื้อไวรัสฮิวแมนแพพิลโลมา หรือ เชื้อHPV DNA testing ซึ่งเป็นสาเหตุของการเกิดมะเร็งปากมดลูก', 0, 1, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->SetFont($pdf->fontName, '', $pdf->fontSize);
            if ($data['ckr_hpv_1'] == '1') {
                $pdf->Cell($w[1], 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
                $pdf->Cell($w[2], 0, 'ไม่พบเชื้อ HPV ชนิดความเสี่ยงสูง (Negative High Risk HPV)', 0, 1, 'L', 0, '', 0, false, 'T', 'M');
            }
            if ($data['ckr_hpv_2'] == '1') {
                $pdf->Cell($w[1], 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
                $pdf->Cell($w[2], 0, 'พบเชื้อ HPV ชนิดไม่รุนแรง (Other High Risk HPV)', 0, 1, 'L', 0, '', 0, false, 'T', 'M');
            }
            if ($data['ckr_hpv_3'] == '1') {
                $pdf->Cell($w[1], 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
                $pdf->Cell($w[2], 0, 'พบเชื้อ HPV ชนิดความเสี่ยงสูง (Positive High Risk HPV Type 16)', 0, 1, 'L', 0, '', 0, false, 'T', 'M');
            }
            if ($data['ckr_hpv_4'] == '1') {
                $pdf->Cell($w[1], 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
                $pdf->Cell($w[2], 0, 'พบเชื้อ HPV ชนิดความเสี่ยงสูง (Positive High Risk HPV Type 18)', 0, 1, 'L', 0, '', 0, false, 'T', 'M');
            }
        }
        $pdf->ln();
        $pdf->SetFont($pdf->fontName, 'BU', $pdf->fontSize);
        $pdf->Cell(195, 0, 'คำแนะนำ (Suggestions)', 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont($pdf->fontName, 'B', $pdf->fontSize);
        if ($data['ckr_sgt_1'] == '1') {
            $pdf->Cell($w[0], 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->Cell($w[2], 0, 'ควรตรวจคัดกรองมะเร็งปากมดลูกทุก ' . $this->getValue($data, 'ckr_sgt_other_1'), 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        }
        if ($data['ckr_sgt_2'] == '1') {
            $pdf->Cell($w[0], 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->Cell($w[2], 0, 'ควรพบแพทย์เพื่อรับยารักษาที่ โรงพยาบาลใกล้บ้าน ', 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        }
        if ($data['ckr_sgt_3'] == '1') {
            $pdf->Cell($w[0], 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->Cell($w[2], 0, 'ควรพบแพทย์ ด้านนรีเวชที่โรงพยาบาลใกล้บ้าน เพื่อตรวจวินิจฉัยเพิ่มเติม ', 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        }
        if ($data['ckr_sgt_4'] == '1') {
            $pdf->Cell($w[0], 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->MultiCell(185, $pdf->spaceHigh, 'ควรพบแพทย์ด้านมะเร็งนรีเวชโดยเร็วที่ โรงพยาบาลศูนย์อุดรธานี เพื่อตรวจวินิจฉัยเพิ่มเติม (แพทย์ออกตรวจทุก วันจันทร์,อังคาร,พฤหัสบดี ในเวลาราชการ) ', 0, 'L');
        }if ($data['ckr_sgt_5'] == '1') {
            $pdf->Cell($w[0], 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->Cell($w[2], 0, 'ควรมารับการตรวจมะเร็งปากมดลูกซ้ำ ' . $this->getValue($data, 'ckr_sgt_other_5'), 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        }
        if ($data['ckr_pap_comment']) {
            $pdf->Cell($w[0], 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->MultiCell(185, $pdf->spaceHigh, $data['ckr_pap_comment'], 0, 'L');
        }

        if ($data['ckr_epeptp'] == '3') {
            $pdf->SetTextColor(255, 0, 0);
            $pdf->SetFont($pdf->fontName, 'BU', $pdf->fontSize);
            $pdf->Cell(180, 0, 'ข้อมูลที่ควรทราบเบื้องต้นในกรณีที่ตรวจพบเชื้อ HPV อย่างเดียวแต่พบเซลล์วิทยาปกติ', 'RTL', 1, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont($pdf->fontName, '', $pdf->fontSize);
            $pdf->Cell(180, 0, '     1.ร้อยละ 90 - 95 ของการติดเชื้อ HPV จะหายไปได้เอง', 'LR', 1, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->Cell(180, 0, '     2.ประมาณร้อยละ 5 - 10 ของการติดเชื้อจะคงอยู่นาน', 'LR', 1, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->Cell(180, 0, '     3.สตรีที่ติดเชื้อ HPV ส่วนใหญ่ไม่เป็นมะเร็งปากมดลูก เมื่อตรวจวินิจฉัยเพิ่มเติม', 'LR', 1, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->Cell(180, 0, '     4.ควรมาพบแพทย์ตามนัดเพื่อการตรวจติดตามการดำเนินโรค การหายไป และการคงอยู่ของเชื้อ HPV', 'LBR', 1, 'L', 0, '', 0, false, 'T', 'M');
        }
    }

    private function reportEkg($pdf, $data, $orderekg_id) {
        $pdf->fontSize = 16;
        $pdf->SetMargins(5, 10, 0, TRUE);
        $pdf->AddPage();
//        $path = Yii::getAlias('@storageUrl/images') . '/logo2.jpg';
        $dataid = \backend\modules\patient\Module::$dataidForm['hos_config'];
        $ezf_table = \backend\modules\patient\Module::$formTableName['hos_config'];
        $dataHosConfig = \backend\modules\ezforms2\classes\EzfUiFunc::loadTbData($ezf_table, $dataid);
        if ($dataHosConfig['logo_02']) {
            try {
                $path = Yii::getAlias('@storageUrl/ezform/fileinput') . '/' . $dataHosConfig['logo_02'];
            } catch (Exception $ex) {
                $path = null;
            }
        } else {
            $path = null;
        }

        $pdf->Image($path, 50, 5, 19, 19, 'JPG');
        $pdf->SetFont($pdf->fontName, 'B', $pdf->fontSize);
        $w = [0 => 5, 1 => 92];
        //right
        $pdf->Cell(200, 0, 'โรงพยาบาลมะเร็งอุดรธานี', 0, 1, 'C', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(200, 0, 'ใบแจ้งผลการตรวจคลื่นไฟฟ้าหัวใจ (EKG)', 0, 1, 'C', 0, '', 0, false, 'T', 'M');
        $pdf->fontSize = 14;
        $pdf->SetFont($pdf->fontName, '', $pdf->fontSize);
        $pdf->Cell($w[0], 0, '', 0, 0, 'R', 0, '', 0, false, 'T', 'M');
        $pdf->Cell($w[1], 0, $data['fullname'] . ' อายุ : ' . SDdate::getAge(SDdate::dateTh2bod($data['pt_bdate'])) . ' ปี   HN ' . $data['pt_hn'] . ' ' . SDdate::mysql2phpThDate($data['visit_date']), 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont($pdf->fontName, 'BU', $pdf->fontSize);
        $pdf->Cell(195, 0, 'สรุปผลการตรวจคลื่นไฟฟาหัวใจ', 0, 1, 'L', 0, '', 0, false, 'T', 'M');
//        $pdf->Cell(97.5, 0, 'ผลการอ่านคลื่นไฟฟาหัวใจ', 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont($pdf->fontName, '', $pdf->fontSize);
        //สรุปผลการตรวจคลื่นไฟฟาหัวใจ
        $pdf->Cell($w[0], 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(195, 0, $this->getValue($data, 'ckr_ekg'), 0, 1, 'L', 0, '', 0, false, 'T', 'M');

        $ezf_table = \backend\modules\patient\Module::$formTableName['report_ekg'];
        $dataEkg = \backend\modules\patient\classes\PatientFunc::loadTbDataByTarget($ezf_table, $orderekg_id);
        //ผลการอ่านคลื่นไฟฟาหัวใจ
//        $pdf->Cell($w[0], 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
//        $text = str_replace("</p>", "", str_replace("<p>", "<br/>", $dataEkg['ekg_result']));
//        $text = str_replace("</div>", "", str_replace("<div>", "<br/>", $text));
//        $text = str_replace('"=', '=', $text);
//        $pdf->writeHTMLCell($w[1], '', '', '', $text, 0, 1);
        $pdf->SetFont($pdf->fontName, 'BU', $pdf->fontSize);
        $pdf->Cell(195, 0, 'คำแนะนำ', 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont($pdf->fontName, '', $pdf->fontSize);
        $pdf->Cell($w[0], 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->MultiCell(190, $pdf->spaceHigh, $data['ckr_ekg_comment'], 0, 'L');

        if ($dataEkg['ekg_file_upload']) {
            $path = Yii::getAlias('@storageUrl/ezform/fileinput') . '/' . $dataEkg['ekg_file_upload'];
            $pdf->StartTransform();
            $pdf->Rotate(-90);
            $pdf->Image($path, $pdf->GetY() - 50, $pdf->GetX() - 135, 210, 180, 'JPG');
            $pdf->StopTransform();
        }
    }

    private function reportCytoAbnormal($pdf, $data, $dataCyto) {
        $pdf->fontSize = 16;
        if ($dataCyto) {
            $dataCyto = explode(",", $dataCyto);
            foreach ($dataCyto as $value) {
                $pdf->SetMargins(5, 10, 0, TRUE);
                $pdf->AddPage();
//                $path = Yii::getAlias('@storageUrl/images') . '/logo2.jpg';
                $dataid = \backend\modules\patient\Module::$dataidForm['hos_config'];
                $ezf_table = \backend\modules\patient\Module::$formTableName['hos_config'];
                $dataHosConfig = \backend\modules\ezforms2\classes\EzfUiFunc::loadTbData($ezf_table, $dataid);
                if ($dataHosConfig['logo_02']) {
                    try {
                        $path = Yii::getAlias('@storageUrl/ezform/fileinput') . '/' . $dataHosConfig['logo_02'];
                    } catch (Exception $ex) {
                        $path = null;
                    }
                } else {
                    $path = null;
                }

                $pdf->Image($path, 50, 5, 19, 19, 'JPG');
                $pdf->SetFont($pdf->fontName, 'B', $pdf->fontSize);
                $w = [0 => 5, 1 => 92];
                //right
                $pdf->Cell(200, 0, 'โรงพยาบาลมะเร็งอุดรธานี', 0, 1, 'C', 0, '', 0, false, 'T', 'M');
                $pdf->Cell(200, 0, 'ภาพผลตรวจคัดกรองมะเร็งปากมดลูกผิดปกติ', 0, 1, 'C', 0, '', 0, false, 'T', 'M');
                $pdf->Cell($w[0], 0, '', 0, 0, 'R', 0, '', 0, false, 'T', 'M');
                $pdf->Cell($w[1], 0, $data['fullname'] . ' อายุ : ' . SDdate::getAge(SDdate::dateTh2bod($data['pt_bdate'])) . ' ปี  HN ' . $data['pt_hn'] . ' ' . SDdate::mysql2phpThDate($data['visit_date']), 0, 1, 'L', 0, '', 0, false, 'T', 'M');
                $path = Yii::getAlias('@storageUrl/ezform/fileinput') . '/' . $value;
                $pdf->Image($path, $pdf->GetY() - 25, $pdf->GetX() + 35, 200, 200, 'JPG');
            }
        }
    }

    public static function calBmi($bmi) {
        if ($bmi <= 18) {
            $result['txt'] = "ผอม";
            $result['value'] = 1;
        } else {
            if ($bmi <= 24.9) {
                $result['txt'] = "น้ำหนักปกติ";
                $result['value'] = 2;
            } else {
                if ($bmi <= 29.9) {
                    $result['txt'] = "น้ำหนักเกินเกณฑ์";
                    $result['value'] = 3;
                } else {
                    if ($bmi > 29.9) {
                        $result['txt'] = "อ้วน";
                        $result['value'] = 4;
                    } else {
                        $result['txt'] = "อ้วน";
                        $result['value'] = 4;
                    }
                }
            }
        }

        return $result;
    }

    private function getValue($data, $fieldName) {
        $ezf_id = \backend\modules\patient\Module::$formID['report_checkup'];
        $modelFields = \backend\modules\ezforms2\models\EzformFields::find()->where('ezf_id = :ezf_id AND ezf_field_name = :ezf_field_name', [':ezf_field_name' => $fieldName, ':ezf_id' => $ezf_id])->one();
        if (isset(Yii::$app->session['ezf_input'])) {
            $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($modelFields['ezf_field_type'], Yii::$app->session['ezf_input']);
        }
        return \backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $modelFields, $data);
    }

}

class REPORTCHK extends \common\lib\tcpdf\SDPDF {

    public $data;

    function Footer() {
        $this->SetY(-26);
        // Set font
        $x = $this->GetX();
        $y = $this->GetY();
        $this->SetFont($this->fontName, '', 13);
        $this->Cell(0, $this->spaceHigh, 'หน้าที่ ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        $this->SetFont($this->fontName, 'B', 14);
        $path = Yii::getAlias('@storageUrl/images') . '/' . $this->data['ckr_doctorverify'] . '.jpg';
        
        if ($this->data['ckr_doctorverify'] == '1514136149083155900') { //พญ.นฤมล 
            $this->Image($path, $x + 128, $y + 5, 32, 10, 'JPG');
        } elseif ($this->data['ckr_doctorverify'] == '1514136171016664500') { //พญ.กรรณิการ์ 
            $this->Image($path, $x + 128, $y + 5, 37, 7, 'JPG');
        }
        
        $this->Cell(97.5, 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $this->Cell(97.5, 0, '................................................', 0, 1, 'C', 0, '', 0, false, 'T', 'M');

        $this->Cell(97.5, 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $this->Cell(97.5, 0, '(  ' . $this->data['doctor_name'] . '  )', 0, 1, 'C', 0, '', 0, false, 'T', 'M');

        $this->Cell(97.5, 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $this->Cell(97.5, 0, SDdate::mysql2phpThDate($this->data['report_date']), 0, 1, 'C', 0, '', 0, false, 'T', 'M');
    }

}
