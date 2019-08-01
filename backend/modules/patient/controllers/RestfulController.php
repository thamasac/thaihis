<?php

namespace backend\modules\patient\controllers;

use appxq\sdii\utils\VarDumper;
use backend\modules\ezforms2\classes\EzfFunc;
use backend\modules\thaihis\classes\ThaiHisFunc;
use Yii;
use yii\helpers\Url;
use yii\web\Response;
use backend\modules\patient\classes\PatientQuery;
use common\lib\tcpdf\SDPDF;
use appxq\sdii\utils\SDdate;
use yii\web\NotFoundHttpException;
use backend\modules\patient\classes\PatientFunc;
use backend\modules\ezforms2\classes\EzfUiFunc;

class RestfulController extends \yii\web\Controller
{

    public $enableCsrfValidation = false;

    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;


        if (parent::beforeAction($action)) {
            if (in_array($action->id, array('create', 'update'))) {

            }
            return true;
        } else {
            return false;
        }
    }

    public function actionIndex()
    {
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

//backend/modules/patient/restfulController.php
    public function actionReportNavi($dataid, $dept, $date)
    {
// create new PDF document
//PDF_UNIT = pt , mm , cm , in 
//PDF_PAGE_ORIENTATION = P , LANDSCAPE = L
//PDF_PAGE_FORMAT 4A0,2A0,A0,A1,A2,A3,A4,A5,A6,A7,A8,A9,A10,B0,B1,B2,B3,B4,B5,B6,B7,B8,B9,B10,C0,C1,C2,C3,C4,C5,C6,C7,C8,C9,C10,RA0,RA1,RA2,RA3,RA4,SRA0,SRA1,SRA2,SRA3,SRA4,LETTER,LEGAL,EXECUTIVE,FOLIO
        $visit_type = Yii::$app->request->get('visit_type');
        $dataProfile = \backend\modules\thaihis\classes\ThaiHisQuery::getPtProfile($dataid);

        if (strpos($dataProfile['pt_bdate'], '/')) {
            $bdate = ' อายุ ' . SDdate::getAge(SDdate::dateTh2bod($dataProfile['pt_bdate'])) . ' ปี';
        } else {
            $bdate = \backend\modules\patient\classes\PatientFunc::integeter2date($dataProfile['pt_bdate']);
            $bdate = ' อายุ ' . SDdate::getAge(SDdate::dateTh2bod($bdate)) . ' ปี';
        }

        $pdf = new SDPDF('P', PDF_UNIT, [80, 130], true, 'UTF-8', false);
//spl_autoload_register(array('YiiBase', 'autoload'));
//set document information
        $pdf->SetCreator('AppXQ');
        $pdf->SetAuthor('iencoded@gmail.com');
        $pdf->SetTitle('Report');
        $pdf->SetSubject('Original');
        $pdf->SetKeywords('AppXQ, SDII, PDF, report, medical, clinic');

// remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

// set margins
        $pdf->SetMargins(5, 5, 0);
        $pdf->SetHeaderMargin(10);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, 5);

// set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set font
        $pdf->SetFont($pdf->fontName, '', $pdf->fontSize);
        $pdf->fontSize = 15;

// add a page
        $pdf->AddPage();
        $dataid = \backend\modules\patient\Module::$dataidForm['hos_config'];
        $ezf_table = \backend\modules\patient\Module::$formTableName['hos_config'];
        $dataHosConfig = EzfUiFunc::loadTbData($ezf_table, $dataid);
        if ($dataHosConfig['logo_05']) {
            try {
                $path = Yii::getAlias('@storageUrl/ezform/fileinput') . '/' . $dataHosConfig['logo_05'];
            } catch (Exception $ex) {
                $path = null;
            }
        } else {
            $path = null;
        }
        if ($path) {
            $pdf->Image($path, 2, 5, 18, 18, 'JPG');
        }
        $pdf->SetFont('thsarabunpsk', 'B', $pdf->fontSize);
        $pdf->Cell(15, 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(50, 0, 'UDONTHANI CANCER HOSPITAL', 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(15, 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(150, 0, 'โรงพยาบาลมะเร็งอุดรธานี ', 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(15, 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(150, 0, 'กรมการแพทย์ กระทรวงสาธารณสุข', 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Ln(4);
        $pdf->SetFont($pdf->fontName, 'BU', $pdf->fontSize);
        $pdf->Cell(0, $pdf->spaceHigh, 'ข้อมูลผู้รับบริการ', 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont($pdf->fontName, 'B', $pdf->fontSize);
        $pdf->Cell(80, $pdf->spaceHigh, "HN : {$dataProfile['pt_hn']}  " . $bdate, 0, 1, 'L', 0, '', 0, false, 'T', 'M'); //CID : {$dataProfile['pt_cid']}
        $pdf->Cell(80, $pdf->spaceHigh, "ชื่อ - สกุล : {$dataProfile['fullname']}", 0, 1, 'L', 0, '', 0, false, 'T', 'M');

//$pdf->Ln(3);       
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

        $price = PatientQuery::getCashierCounterItemSum($dataVisitTran['visit_id']);
        $pdf->SetFont($pdf->fontName, 'BU', $pdf->fontSize);
        $pdf->Cell(0, $pdf->spaceHigh, 'ข้อมูลส่งตรวจ', 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont($pdf->fontName, 'B', $pdf->fontSize);
        $pdf->Cell(80, $pdf->spaceHigh, 'วันที่รับบริการ : ' . SDdate::mysql2phpThDateTime($dataVisitTran['visit_date']), 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->MultiCell(0, $pdf->spaceHigh, "ตรวจแผนก : {$dataVisitTran['sect_name']} " . ($dataVisitTran['visit_type'] == '1' ? $txtCounter : ''), 0, 'L');
        $pdf->Cell(80, $pdf->spaceHigh, "ประเภทการมา : {$this->getVisitTypeName($dataVisitTran['visit_type'])}", 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        $dataRight = PatientQuery::getRightLast($dataProfile['pt_id']);
        if ($dataRight['right_project_id']) {
            $ezf_id = \backend\modules\patient\Module::$formID['patientright'];
            $modelFields = \backend\modules\ezforms2\models\EzformFields::find()->where('ezf_id = :ezf_id AND ezf_field_name = :ezf_field_name', [':ezf_field_name' => 'right_project_id', ':ezf_id' => $ezf_id])->one();
            if (isset(Yii::$app->session['ezf_input'])) {
                $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($modelFields['ezf_field_type'], Yii::$app->session['ezf_input']);
            }
            $dataRight['right_project_id'] = \backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $modelFields, $dataRight);
        }
        $txtRightLGO = '';
        if ($dataRight['right_code'] == 'LGO') {
            $txtRightLGO = '(' . ($dataRight['right_prove_no'] == '' ? 'ยังไม่มีเลขที่อนุมัติ' : $dataRight['right_prove_no']) . ')';
        }
        $pdf->MultiCell(0, $pdf->spaceHigh, "สิทธิ : {$dataRight['right_name']} {$dataRight['right_project_id']}{$txtRightLGO}", 0, 'L');
//$pdf->Cell(80, $pdf->spaceHigh, "สิทธิ : {$dataRight['right_name']} {$dataRight['right_project_id']}", 0, 1, 'L', 0, '', 0, false, 'T', 'M');
//$pdf->Ln(3);

        $pdf->SetFont($pdf->fontName, 'BU', $pdf->fontSize);
        $pdf->Cell(0, $pdf->spaceHigh, 'ค่าใช้จ่าย', 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont($pdf->fontName, 'B', $pdf->fontSize);
        $price2 = !empty($price['pay']) ? number_format($price['pay'], 2) : 0;
        $pdf->Cell(80, $pdf->spaceHigh, "ยอดที่ต้องชำระ : {$price2} บาท", 0, 1, 'L', 0, '', 0, false, 'T', 'M');

// set style for barcode
        $style = array(
            'border' => 2,
            'vpadding' => 'auto',
            'hpadding' => 'auto',
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false, //array(255,255,255)
            'module_width' => 1, // width of a single module in points
            'module_height' => 1 // height of a single module in points
        );
        $pdf->SetXY(12, 110);
        $pdf->write1DBarcode("{$dataProfile['pt_hn']}", 'C128A', '', '', '', 15, 0.4, $style, 'N');

//$url = \yii\helpers\Url::to(['/cpoe', 'action' => 'search', 'ptid' => $dataid]);
// $pdf->write2DBarcode($url, 'QRCODE,L', 10, 120, 90, 75, $style, 'N');

        $pdf->Output('report.pdf', 'I');
        Yii::$app->end();
    }

    private function getVisitTypeName($visit_type)
    {
        $typeTxt = "";
        switch ($visit_type) {
            case '1': //opd checkup
                $typeTxt = Yii::t('patient', 'Checkup');
                break;
            case '2': //Appointment 
                $typeTxt = Yii::t('patient', 'Follow up');
                break;
            case '3': //refer
                $typeTxt = Yii::t('patient', 'Refer');
                break;
            case '4': //ส่งตรวจสอบสิทธิก่อน
                $typeTxt = Yii::t('patient', 'Treatment');
                break;
            default:
                return FALSE;
        }

        return $typeTxt;
    }

    private function getCounterName($counter_code)
    {
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

    private function getOrderStatus($order_status)
    {
        $result = "";
        switch ($order_status) {
            case '1': //opd checkup
                $result = 'รอรับ';
                break;
            case '2': //Appointment 
                $result = 'รับแล้ว';
                break;
            case '3': //refer
                $result = 'ออกผล';
                break;
            default:
                return FALSE;
        }

        return $result;
    }

    public function actionSaveProfile()
    {
        if (Yii::$app->request->post()) {
            $ezf_id = \backend\modules\patient\Module::$formID['profile'];

            $dataProfile = Yii::$app->request->post('EZ1503378440057007100');
            $data['pt_cid'] = str_replace('-', '', $dataProfile['pt_cid']);
            $profileData = PatientQuery::getPatientSearch($data['pt_cid'], '12276');
            if (empty($profileData)) {
                $data['pt_pic'] = '';

                $name = explode(" ", $dataProfile['fullname_th']);
                $data['pt_bdate'] = $dataProfile['bdate']; //$this->checkBdate($dataProfile['bdate']);
                $dataSex = PatientQuery::getPrefixId($name[0]);
                $data['pt_sex'] = $dataSex['prefix_sex'];
                $data['pt_prefix_id'] = $dataSex['prefix_id'];
                $data['pt_firstname'] = $name[1];
                $data['pt_lastname'] = $name[2];
                $address = explode("#", trim($dataProfile['address']));
                $arrLength = count($address) - 1;

                $data['pt_address'] = $address[0];
                $data['pt_moi'] = str_replace("หมู่ที่", "", $address[1]);
                $data['pt_addr_tumbon'] = str_replace("ตำบล", "", $address[$arrLength - 2]);
                $data['pt_addr_amphur'] = str_replace("อำเภอ", "", $address[$arrLength - 1]);
                $data['pt_addr_province'] = str_replace("จังหวัด", "", $address[$arrLength]);

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
            } else {
                $dataid = $profileData[0]['id'];
            }

            return $dataid;
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionOpenBrowser($user, $pt_id)
    {
        $user = \common\models\User::findOne(['username' => $user]);

        if ($user) {
            Yii::$app->user->switchIdentity($user);
        }
        \Yii::$app->response->redirect(['/patient/patient', 'reloadDiv' => 'view-patient', 'tab' => '1', 'dataid' => $pt_id]);
    }

    public static function checkBdate($bdate)
    {
        $arrBdate = explode("/", $bdate);
        $changeDate = FALSE;
        if ($arrBdate[0] == '00') {
            $changeDate = TRUE;
            $arrBdate[0] = '01'; //day
        }
        if ($arrBdate[1] == '00') {
            $changeDate = TRUE;
            $arrBdate[1] = '07'; //month
        }
        if ($arrBdate[2] == '00') {
            $changeDate = TRUE;
            $arrBdate[2] = date('Y') - 111; //year
        }
        if ($changeDate) {
            $bdate = $arrBdate[0] . '/' . $arrBdate[1] . '/' . $arrBdate[2];
        }

        return $bdate;
    }

    public function actionPtprofileByid($pt_id)
    {
//        if (Yii::$app->getRequest()->isAjax) {
        $result = \backend\modules\thaihis\classes\ThaiHisQuery::getPtProfile($pt_id);
        if ($result) {
            try {
                $result['pt_age'] = \backend\modules\thaihis\classes\ThaiHisQuery::calAge($result['pt_bdate']);
            } catch (Exception $ex) {
                $result['pt_age'] = '';
            }
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $result;
//        } else {
//            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
//        }
    }

    public function actionPtprofileToKm4($hn, $visit_date)
    {
//if (Yii::$app->getRequest()->isAjax) {
        $result = PatientQuery::getPtProfileToKm4($hn, $visit_date);
        if ($result) {
            $result['pt_address'] = \backend\modules\patient\classes\PatientFunc::getFulladdress($result);
            try {
                $result['pt_age'] = SDdate::getAge(SDdate::dateTh2bod($result['pt_bdate']));
            } catch (Exception $ex) {
                $bdate = PatientFunc::integeter2date($result['pt_bdate']);
                $result['pt_age'] = SDdate::getAge(SDdate::dateTh2bod($bdate));
            }
        } else {
            $url = Yii::$app->params['udchWebService'] . "module/customer/show_subpatent.php?action=getPtToKm4&hn=" . $hn . "&visit_date=" . $visit_date;
            $result = \backend\modules\patient\classes\PatientFunc::callWebService($url);
            $result = json_decode($result, TRUE);
            $result['visit_tran_dept'] = \backend\modules\patient\classes\PatientQuery::getSereneDeptToNhisDept($result['visit_tran_dept'])['sect_code'];
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $result;
        /* } else {
          throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
          } */
    }

    public function actionCheckPtProfileOld($cid, $userid)
    {
        if (Yii::$app->getRequest()->isAjax) {
            $result = \backend\modules\patient\classes\PatientFunc::checkPtProfileOld($cid, $userid);

            Yii::$app->response->format = Response::FORMAT_JSON;
            return $result;
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionPrintStickerCyto($value)
    {
        if (Yii::$app->getRequest()->isAjax) {
            $result = \backend\modules\patient\classes\PatientFunc::getPrintStickerCyto($value);

            return $result;
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

    public function actionImportToZdata($ezf_id, $action)
    {
        if ($action == 'serene') {
            $result = \backend\modules\patient\classes\PatientFunc::getImportSereneToZdata();
        } elseif ($action == 'cscd') {
            $result['result'] = \backend\modules\patient\classes\Order2Query::seleteDataImport();
        } else {
            $result['result'] = \backend\modules\patient\classes\Order2Query::getOrder();
        }

        ini_set('memory_limit', '-1');
        foreach ($result['result'] as $value) {
            \backend\modules\patient\classes\PatientFunc::backgroundInsert($ezf_id, '', '', [
                'unit_name' => $value['sect_name'],
                'unit_code' => $value['sect_code'],
//                'unit_desc' => $value['unit_desc'],
//                'unit_type' => $value['unit_type'],
//                'unit_show' => $value['unit_show'],
                'unit_order_type' => $value['zot_id'],
            ]);
        }
    }

    public function actionReceiptRunNumber()
    {
        $result = \backend\modules\patient\classes\Order2Query::getSks();

        ini_set('memory_limit', '-1');
        $chkHn = '';
        $chkDate = '';
        $num = 0;
        $j = 1;
        foreach ($result as $value) {
            if ($chkDate == $value['visit_date']) {
                if ($chkHn !== $value['hn_no']) {
                    $num++;
                    $chkHn = $value['hn_no'];
                }
            } else {
                $j = 1;
                $num = 1;
                if ($j == 1) {
                    $j++;
                    $chkHn = $value['hn_no'];
                }
                $chkDate = $value['visit_date'];
            }

//            echo $value['hn_no'] . ' O ' . $value['visit_date'] . ' = ' . $num . '<br>';
            \backend\modules\patient\classes\PatientFunc::saveDataNoSys('1524886524041096400', 'zdata_sks'
                , $value['sks_id'], ['ref_num' => $num]);
        }
    }

    public function actionImportKm4($visit_id)
    {
        $ezf_id = \backend\modules\patient\Module::$formID['drug_km4'];
        $ezf_table = \backend\modules\patient\Module::$formTableName['drug_km4'];
        $dataVisit = PatientQuery::getVisitDetail($visit_id);
        $km4drug = $this->km4OrderPayment($dataVisit['pt_hn'], $dataVisit['visit_date']);
        $initdata = [];
        $i = 0;
        if ($km4drug && $dataVisit) {
            PatientQuery::deleteZdata($visit_id, $ezf_table);
            foreach ($km4drug as $arvalue) {
                foreach ($arvalue as $key => $value) {
                    $initdata['drugtran_' . strtolower($key)] = $value;
                }
                \backend\modules\ezforms2\classes\EzfUiFunc::backgroundInsert($ezf_id, '', $visit_id, $initdata);
            }
        }
        \appxq\sdii\utils\VarDumper::dump($dataVisit, 0);
        \appxq\sdii\utils\VarDumper::dump($km4drug, 0);
    }

    private function km4OrderPayment($hn, $visit_date)
    {
        $url = Yii::$app->params['km4Url'] . "/api/core-api/payment?pt_hn=" . $hn . "&visit_date=" . $visit_date;
        $body = \backend\modules\patient\classes\PatientFunc::callWebService($url);
        $body = json_decode($body, TRUE);

        if (empty($body['status'])) {
            foreach ($body as $valueBody) {
                foreach ($valueBody as $key => $value) {
                    $arrData[] = $value;
                }
            }
        } else {
            $arrData = [];
        }

        return $arrData;
    }

    public function actionZdataToJson()
    {
        return $this->checkBdate('00/00/2495');
    }

    public function actionGetProjectidByCid()
    {
        $pt_id = Yii::$app->request->get('pt_id');
        $result = PatientQuery::getprojectidByptid($pt_id);
        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'project_id' => $result['project_id'],
            'project_name' => $result['project_name']
        ];
    }

    public function actionPrintReportXray()
    {
        $report_id = Yii::$app->request->get('report_id');
        $data = PatientQuery::getXrayReportByid($report_id);
        $pdf = new SPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);
        $pdf->SetCreator('AppXQ');
        $pdf->SetAuthor('iencoded@gmail.com');
//  $pdf->SetTitle('รายละเอียดค่าใช้จ่าย' . $data[0]['fullname']);
        $pdf->SetSubject('Original');
        $pdf->SetKeywords('AppXQ, SDII, PDF, report, medical,');
        $GLOBALS["report_doctor"] = $data[0]['doc_result'];

// remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(true);
// set margins
        $pdf->SetMargins(15, 10, 15, TRUE);
        $pdf->SetHeaderMargin(10);
        $pdf->SetFooterMargin(10);
// set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, 15);

// set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set font
        $pdf->SetFont($pdf->fontName, '', $pdf->fontSize);
        $pdf->fontSize = 18;

// add a page
        $pdf->AddPage();
        $this->HeaderXrayReport($pdf, $data);
        $this->ContentXrayReport($pdf, $data);

//$this->receiptContentDetail($pdf, $data);
        $pdf->Output('report.pdf', 'I');
        Yii::$app->end();
    }

    /** @var pdf,data This documentation function HeaderXrayReport. */
    private static function HeaderXrayReport($pdf = null, $data = null)
    {

        $pdf->SetFont('thsarabunpsk', 'B', 20);
        $w = array(50, 60, 75);
        $pdf->Ln(5);
        $pdf->Cell(60, 0, 'โรงพยาบาลมะเร็งอุดรธานี', 'TL', 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont('thsarabunpsk', 'B', 16);
        $pdf->Cell(125, 0, 'Patient: ' . $data[0]['fullname'], 'TR', 1, 'L', 0, '', 0, false, 'T', 'M');

        $pdf->Cell(60, 0, '', 'L', 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell($w[0], 0, 'HN: ' . $data[0]['pt_hn'], '', 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell($w[2], 0, ' ', 'R', 1, 'L', 0, '', 0, false, 'T', 'M');

        $pdf->Cell($w[1], 0, 'งานรังสีวินิจฉัย', 'L', 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell($w[0], 0, 'AN: ' . !empty($data[0]['pt_an']) ? '' : '', '', 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell($w[2], 0, 'Birth Date: ' . SDdate::mysql2phpThDateSmall($data[0]['pt_bdate']), 'R', 1, 'L', 0, '', 0, false, 'T', 'M');

        $pdf->Cell($w[1], 0, $data[0]['order_group_name'], 'L', 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell($w[0], 0, '', '', 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell($w[2], 0, 'Age/Sex: ' . \backend\modules\thaihis\classes\ThaiHisQuery::calAge($data[0]['pt_bdate']) . ' ' . $data[0]['pt_sex'], 'R', 1, 'L', 0, '', 0, false, 'T', 'M');

        $pdf->Cell($w[1], 0, 'Dept: ' . $data[0]['unit_name'], 'L', 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell($w[0], 0, ' ', '', 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell($w[2], 0, 'Ordered: ' . SDdate::mysql2phpThDateSmall($data[0]['order_date']), 'R', 1, 'L', 0, '', 0, false, 'T', 'M');

        $pdf->Cell($w[1], 0, 'Doctor: ' . $data[0]['doc_order'], 'BL', 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell($w[0], 0, '', 'B', 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell($w[2], 0, 'Reported: ' . SDdate::mysql2phpThDateSmall($data[0]['result_date']), 'BR', 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont('thsarabunpsk', 'B', 16);

        $pdf->Cell(80, 0, 'Service: ' . $data[0]['xray_item_des'], 'B', 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(105, 0, 'Acquisition Date/Time: ' . SDdate::mysql2phpThDateTime($data[0]['result_date']), 'B', 1, 'L', 0, '', 0, false, 'T', 'M');
    }

    private function ContentXrayReport($pdf, $data)
    {

        $pdf->SetFont('thsarabunpsk', 'B', 16);
        $text = str_replace("</p>", "", str_replace("<p>", "<br/>", $data[0]['result']));
        $text = str_replace("</div>", "", str_replace("<div>", "<br/>", $text));
        $text = str_replace('"=', '=', $text);
        $text = str_replace('<p ="">', '<br/>', $text);
        $text = str_replace('<br>', '', $text);
        $pdf->writeHTML($text, true, false, true, false, '');
    }

    public function actionPrintReportLab()
    {
        $hn = Yii::$app->request->get('pt_hn');
        $date = Yii::$app->request->get('date');
        $data = PatientQuery::getLabReportByHeaderHn($hn, $date);
        $GLOBALS['data'] = $data;
        $pdf = new KKPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);
        $pdf->SetMargins(20, 32, 0, true);
        $pdf->SetHeaderMargin(5);
        $pdf->SetFooterMargin(5);
        $pdf->AddPage();
//  $pdf->Header($data);
//$this->ReportLabHeader($pdf, $data);
        $this->ReportLabContent($pdf, $data);
//        ;
//        $this->ReportLabContent();
        $pdf->Output();
    }

    private function ReportLabHeader($pdf, $data)
    {
        $w = array(55, 40, 30, 30, 15);
        $dataid = \backend\modules\patient\Module::$dataidForm['hos_config'];
        $ezf_table = \backend\modules\patient\Module::$formTableName['hos_config'];
        $dataHosConfig = EzfUiFunc::loadTbData($ezf_table, $dataid);
        if ($dataHosConfig['logo_05']) {
            try {
                $path = Yii::getAlias('@storageUrl/ezform/fileinput') . '/' . $dataHosConfig['logo_05'];
            } catch (Exception $ex) {
                $path = null;
            }
        } else {
            $path = null;
        }
        if ($path) {
            $pdf->Image($path, 20, 4, 21, 21, 'JPG');
        }

//  $this->Image('../../images/logo.png', 20, 4, 21, 21, 'PNG', 'http://www.udcancer.org');
        $pdf->SetFont('thsarabunpsk', 'B', 16);
        $pdf->Cell(23, 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(140, 0, 'โรงพยาบาลมะเร็งอุดรธานี (UDONTHANI CANCER HOSPITAL)', 0, 1, 'L', 0, '', 0, false, 'T', 'M');

        $pdf->Cell(23, 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(80, 0, 'NAME : ' . $data[0]['header_fullname'], 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(32, 0, 'HN : ' . $data[0]['header_hn'], 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(40, 0, 'Lab No : ' . $data[0]['header_ln'], 0, 1, 'L', 0, '', 0, false, 'T', 'M');

        $pdf->Cell(23, 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(25, 0, 'เพศ : ' . $data[0]['sex'], 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(25, 0, 'อายุ : ' . $data[0]['age'], 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(62, 0, 'แผนก : ' . $data[0]['dept_name'], 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(50, 0, 'Date : ' . $data[0]['receive_date'], 0, 1, 'L', 0, '', 0, false, 'T', 'M');
//$this->SetFont('thsarabunpsk', 'UB', 14);
//$this->Cell(192, 0, 'Laboratory', 'T', 1, 'L', 0, '', 0, false, 'T', 'M');

        $pdf->SetFont('thsarabunpsk', 'B', 14);
        $pdf->Cell($w[0], 0, '  รายการทดสอบ', 'T', 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell($w[1], 0, 'ผลการตรวจ', 'T', 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell($w[2], 0, 'หน่วย', 'T', 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell($w[3], 0, 'ค่าปกติ', 'T', 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell($w[4] + 12, 0, '', 'T', 1, 'L', 0, '', 0, false, 'T', 'M');
        $GLOBALS['approved_by'] = $data[0]['approved_by'];
        $GLOBALS['app_date'] = $data[0]['app_date'];
    }

    private function ReportLabContent($pdf, $data)
    {
        $testChk = '';
        $w = array(45, 10, 40, 30, 30, 15);
        for ($i = 0; $i < count($data); $i++) {
            $pdf->SetFont('thsarabunpsk', 'UB', 14);
            if ($testChk !== $data[$i]['secname']) {
                $pdf->Cell(50, 0, $data[$i]['secname'], 0, 0, 'L', 0, '', 0, false, 'T', 'M');
                $pdf->SetFont('thsarabunpsk', 'B', 14);
                $pdf->Cell(120, 0, $data[$i]['reported_by'], 0, 1, 'R', 0, '', 0, false, 'T', 'M');
                $testChk = $data[$i]['secname'];
            }
            $resultValue = "";
            if (!$data[$i]['commt_all']) {
                $resultValue = $data[$i]['result'];
            } else {
                $resultValue = $data[$i]['result'] . '  *';
            }
            $pdf->SetFont('thsarabunpsk', 'B', 14);
            $pdf->Cell($w[0], 0, '  ' . $data[$i]['test_name'], 0, 0, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->Cell($w[1], 0, '', 'B', 0, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->Cell($w[2], 0, $resultValue, 'B', 0, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->Cell($w[3], 0, $data[$i]['unit'], 'B', 0, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->Cell($w[4], 0, $data[$i]['normal_range'], 'B', 0, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->Cell($w[5], 0, '', 'B', 1, 'L', 0, '', 0, false, 'T', 'M');
            if ($data[$i]['comment']) {
                $pdf->SetFont('thsarabunpsk', 'B', 14);
                $pdf->Cell($w[0], 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
                $pdf->Cell(135, 0, 'Comment : ' . $data[$i]['comment'], 0, 1, 'L', 0, '', 0, false, 'T', 'M');
            }
        }
    }

    public function actionPrintReportListNamePep()
    {
        $date = Yii::$app->request->get('dates');
        $order_tran_status = Yii::$app->request->get('order_tran_status');
        $date = !empty($date) ? SDdate::phpThDate2mysql($date) : date('Y-m-d');
        $order_tran_status = !empty($order_tran_status) ? $order_tran_status : '2';
        $group_type = Yii::$app->request->get('dept');
        $data = PatientQuery::getreportlistnamepep($date, $group_type, $order_tran_status);
//        $pdf = new SDPDF();
        $pdf = new SDPDF('L', PDF_UNIT, 'A4', true, 'UTF-8', false);
        $pdf->SetMargins(5, 5, 0, true);
//$pdf->SetHeaderMargin(10);
        $pdf->SetFooterMargin(5);
        $pdf->AddPage();
        $pdf->SetFont('thsarabunpsk', 'B', 15);
        $pdf->Cell(0, 0, 'รายชื่อผู้รับบริการตรวจ ' . $this->getCounterName($group_type) . ' สถานะ ' . $this->getOrderStatus($order_tran_status), 0, 1, 'C', 0, '', 0, false, 'T', 'M');

        $pdf->SetFont('thsarabunpsk', '', 13);
        $pdf->Cell(0, 0, '' . SDdate::mysql2phpThDate($date) . '', 0, 1, 'C', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(15, 0, 'ลำดับที่', 'TLB', 0, 'C', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(20, 0, 'HN', 'TB', 0, 'C', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(40, 0, 'ชื่อนามสกุล', 'TB', 0, 'C', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(210, 0, 'รายการ', 'TRB', 1, 'C', 0, '', 0, false, 'T', 'M');
        $i = 1;
        foreach ($data as $row) {
            $pdf->Cell(15, 0, $i, 'TLRB', 0, 'C', 0, '', 0, false, 'T', 'M');
            $pdf->Cell(20, 0, $row['pt_hn'], 'TRB', 0, 'C', 0, '', 0, false, 'T', 'M');
            $pdf->Cell(40, 0, $row['fullname'], 'TRB', 0, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->Cell(210, 0, $row['order_name'], 'TRB', 1, 'L', 0, '', 0, false, 'T', 'M');
            $i++;
        }
        $pdf->Output();
    }

    public function actionPrintReportCer()
    {
        $cer_id = Yii::$app->request->get('cer_id');
        $target = Yii::$app->request->get('target');
        $data = PatientQuery::getCerReportByCerid($cer_id);
        $options = \backend\modules\ezforms2\classes\EzfFunc::stringDecode2Array(Yii::$app->request->get('options', ''));
        $template = isset($options['template']) ? $options['template'] : '';
        $ezf_id = isset($options['ezf_id']) ? $options['ezf_id'] : '';
//
////        \appxq\sdii\utils\VarDumper::dump($options);
        $pdf = new SDPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);
        $pdf->SetCreator('KK');
        $pdf->SetAuthor('UD CANCER');
        $pdf->SetTitle('medical certificate');
        $pdf->SetSubject('medical certificate');
        $pdf->SetKeywords('medical certificate');
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(15, 10, 10, true);
        $pdf->SetHeaderMargin(10);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, 3);

// set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->SetFont($pdf->fontName, '', $pdf->fontSize);
        $pdf->fontSize = 18;
        $pdf->AddFont('thsarabunpsk', '', 'thsarabunpsk.php');
        $pdf->AddFont('thsarabunpsk', 'B', 'thsarabunpskb.php');
        $pdf->AddPage();
//
////        if ($template != '') {
//        $path_template = [];
//        $defaultTemp = '';
        $hospital = (new \yii\db\Query())->select('site_detail')->from('zdata_sitecode')->where(['site_name' => Yii::$app->user->identity->profile->sitecode])->scalar();
        $sitecode = $hospital;
//        $dateFull = SDdate::mysql2phpThDate(date('Y-m-d'));
//        $date = explode(' ', $dateFull);
//        if (isset($date) && is_array($date) && !empty($date)) {
//            $path_template['{date}'] = $date[1];
//            $path_template['{month}'] = $date[2];
//            $path_template['{year}'] = $date[3];
//        }
//        $path_template['{full_date}'] = $dateFull;
//        $path_template['{hospital}'] = $hospital;
//        $path_template['{sitecode}'] = Yii::$app->user->identity->profile->sitecode;
//
////        $dataEzf
//
//        if ($ezf_id != '') {
//            $dataEzf = \backend\modules\ezforms2\classes\EzfQuery::getEzformById($ezf_id);
//            $dataTb = new \backend\modules\ezforms2\models\TbdataAll();
//            $dataTb->setTableName($dataEzf['ezf_table']);
//            $dataTb = $dataTb->find()
//                    ->where(['ptid' => $target])
//                    ->andWhere('rstat != 0 OR rstat != 3')
//                    ->orderBy(['create_date' => SORT_DESC])
//                    ->one();
//
////                \appxq\sdii\utils\VarDumper::dump($model->attributes);
//            $dataFields = \backend\modules\ezforms2\models\EzformFields::find()->where(['ezf_id' => $ezf_id])->all();
//            $model = \backend\modules\ezforms2\classes\EzfFunc::setDynamicModel($dataFields, $dataEzf['ezf_table'], Yii::$app->session['ezf_input']);
//            $model->attributes = $dataTb->attributes;
//            if ($model && $dataFields) {
//                foreach ($dataFields as $key => $value) {
//                    $fieldName = $value['ezf_field_name'];
//
//                    $var = $value['ezf_field_name'];
//                    $label = $value['ezf_field_label'];
//
//                    $dataInput = null;
//                    if (isset(Yii::$app->session['ezf_input'])) {
//                        $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($value['ezf_field_type'], Yii::$app->session['ezf_input']);
//                    }
//                    isset($model[$value['ezf_field_id'] . '_id']) ? $model['id'] = $model[$value['ezf_field_id'] . '_id'] : $model['id'] = \appxq\sdii\utils\SDUtility::getMillisecTime();
////                        \appxq\sdii\utils\VarDumper::dump($model);
//                    $dataValue = \backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $value, $model);
//                    if (in_array($value['ezf_field_type'], [63, 64])) {
//                        $dataValue = SDdate::mysql2phpThDate($dataValue);
//                    }
//                    if ($fieldName != 'id' && $label != '') {
//                        $defaultTemp .= $label . ' : {' . $var . '} <br/>';
//                    }
//                    $path_template['{' . $var . '}'] = $dataValue;
//                }
//            }
//        }
//        if ($template != '') {
//            $template = strtr($template, $path_template);
//        } else {
//            $template = strtr($defaultTemp, $path_template);
//        }
////            \appxq\sdii\utils\VarDumper::dump($path_template);
////            return \backend\modules\reports\classes\CustomReport::printPDF('1','P', 'A4', 'TEST', $template, '', '');
//        $pdf->writeHTML($template, true, false, true, false, '');
//        } else {
////
//            $path = Yii::getAlias('@storageUrl/images') . '/logo2.jpg';
//            $pdf->Image($path, 70, 5, 20, 20, 'JPG', '');
        $dataid = \backend\modules\patient\Module::$dataidForm['hos_config'];
        $ezf_table = \backend\modules\patient\Module::$formTableName['hos_config'];
        $dataHosConfig = \backend\modules\ezforms2\classes\EzfUiFunc::loadTbData($ezf_table, $dataid);
        $path = '';
        if ($dataHosConfig && isset($dataHosConfig['logo_02']) && $dataHosConfig['logo_02'] != '') {
            $path = Yii::getAlias('@storageUrl/ezform/fileinput') . '/' . $dataHosConfig['logo_02'];

//            $path = Yii::getAlias('@storageUrl/ezform/fileinput') . '/logo_02_1544679584007028400.jpeg';
        }
        if ($path != '' && ThaiHisFunc::isUrlExist($path)) {
            $pdf->Image($path, 96, 10, 20, 20, 'JPG');
        }
        $pdf->Ln(22);
        $pdf->SetFont('thsarabunpsk', 'B', 20);
        $pdf->Cell(0, 0, 'ใบรับรองแพทย์', 0, 1, 'C', 0, '', 0, false, 'T', 'M');
        $pdf->Ln(5);
        $pdf->SetFont('thsarabunpsk', 'B', 16);
        $pdf->Cell(0, 0, 'ส่วนที่ 1 ของผู้ขอรับใบรับรองแพทย์', 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont('thsarabunpsk', '', 15);

        $pdf->Cell(15, 0, "ข้าพเจ้า", 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont('thsarabunpsk', 'b', 15);
        $pdf->Cell(0, 0, '' . $data["prefix_name"] . $data["pt_firstname"] . ' ' . $data["pt_lastname"], 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont('thsarabunpsk', '', 15);
        $pdf->Cell(55, 0, 'สถานที่อยู่ (ที่อยู่สามารถติดต่อได้) ', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont('thsarabunpsk', 'B', 15);
        $pdf->MultiCell(0, 0, '' . $data["FULL_ADDRESS"], '', 'L');

        $pdf->SetFont('thsarabunpsk', '', 15);
        $pdf->Cell(55, 0, 'หมายเลขบัตรประชาชน ', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont('thsarabunpsk', 'B', 15);
        $pdf->Cell(55, 0, $data["pt_cid"], 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont('thsarabunpsk', '', 15);
        $pdf->Cell(0, 0, 'ข้าพเจ้าขอใบรับรองสุขภาพโดยมีประวัติสุขภาพดังนี้ ', 0, 1, 'L', 0, '', 0, false, 'T', 'M');

        $text_d = 'ไม่มี';
//        if ($data['cer_diseases'] != 1) {
        $text_d = 'ไม่มี      มี ' . ($data['var_11_other_2'] ? $data['var_11_other_2'] : '..........................................................................................................................');
//        }

        $pdf->Cell(55, 0, '1.โรคประจำตัว ', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont('thsarabunpsk', 'b', 15);
        $pdf->Cell(0, 0, '' . $text_d, 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont('thsarabunpsk', '', 15);
        $text_d = 'ไม่มี';
//        if ($data['cer_accident'] != 1) {
        $text_d = 'ไม่มี      มี ' . ($data['var_12_other_2'] ? $data['var_12_other_2'] : '..........................................................................................................................');
//        }

        $pdf->Cell(55, 0, '2.อุบัติเหตุ และการผ่าตัด ', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont('thsarabunpsk', 'b', 15);
        $pdf->Cell(0, 0, '' . $text_d, 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont('thsarabunpsk', '', 15);
        $text_d = 'ไม่มี';
//        if ($data['cer_everbeentreated'] != 1) {
        $text_d = 'ไม่มี      มี ' . ($data['var_13_other_2'] ? $data['var_13_other_2'] : '..........................................................................................................................');
//        }

        $pdf->Cell(55, 0, '3.เคยเข้ารับการรักษาในโรงพยาบาล ', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont('thsarabunpsk', 'b', 15);
        $pdf->Cell(0, 0, '' . $text_d, 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont('thsarabunpsk', '', 15);
        $pdf->Cell(55, 0, '4.ประวัติอื่นที่สำคัญ ', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont('thsarabunpsk', 'b', 15);
        $pdf->Cell(0, 0, '' . ($data['cer_history'] ? $data['cer_history'] : '..........................................................................................................................'), 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont('thsarabunpsk', '', 15);
        $pdf->Ln();
        $pdf->Cell(0, 0, 'ลงชื่อ...........................................' . SDdate::mysql2phpThDate($data['cer_date']), 0, 1, 'R', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont('thsarabunpsk', '', 14);
        $pdf->Cell(0, 0, 'ในกรณีเด็กที่ไม่สามารถรับรองตนเองได้ให้ผู้ปกครองลงนามรับรองแทนได้', 0, 1, 'R', 0, '', 0, false, 'T', 'M');

        $pdf->SetFont('thsarabunpsk', 'B', 16);
        $pdf->Cell(0, 0, 'ส่วนที่ 2 ของแพทย์', 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont('thsarabunpsk', '', 15);
        $html = '&nbsp;สถานที่ตรวจ <b>' . $sitecode . '&nbsp;&nbsp;' . SDdate::mysql2phpThDate($data['cer_date']) . '</b>';
        $pdf->writeHTML($html, true, false, true, false, '');

        $html = '&nbsp;ข้าพเจ้า&nbsp;&nbsp;<b>' . $data['doc_fullname'] . '</b>';
        $pdf->writeHTML($html, true, false, true, false, '');

        $html = '&nbsp;สถานประกอบวิชาชีพเวชกรรม&nbsp;&nbsp;<b> ' . $sitecode . ' เลขที่ 36 หมู่ 1 ต.หนองไผ่ อ.เมือง จ.อุดรธานี 41330</b>';
        $pdf->writeHTML($html, true, false, true, false, '');
//  $pdf->Cell(0, 0, 'สถานประกอบวิชาชีพเวชกรรม โรงพยาบาลมะเร็งอุดรธานี เลขที่ 36 หมู่ 1 ต.หนองไผ่ อ.เมือง จ.อุดรธานี 41330', 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(25, 0, 'ได้ตรวจร่างกาย ', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $fontsize = 15;
        $pdf->SetFont('thsarabunpsk', 'B', $fontsize);
        $pdf->Cell(0, 0, '' . $data["prefix_name"] . $data["pt_firstname"] . ' ' . $data["pt_lastname"], 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont('thsarabunpsk', '', $fontsize);
        $pdf->Cell(20, 0, 'แล้วเมื่อวันที่ ', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont('thsarabunpsk', 'B', $fontsize);
        $pdf->Cell(50, 0, '' . SDdate::mysql2phpThDate($data['cer_date']), 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(0, 0, 'มีรายละเอียดดังนี้', 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont('thsarabunpsk', '', $fontsize);
        $pdf->Cell(20, 0, 'น้ำหนักตัว ', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont('thsarabunpsk', 'B', $fontsize);
        $pdf->Cell(10, 0, $data['cer_weight'], 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont('thsarabunpsk', '', $fontsize);
        $pdf->Cell(20, 0, 'กก-ความสูง ', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont('thsarabunpsk', 'B', $fontsize);
        $pdf->Cell(10, 0, $data['cer_height'], 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont('thsarabunpsk', '', $fontsize);
        $pdf->Cell(38, 0, 'เซนติเมตร-ความดันโลหิต ', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont('thsarabunpsk', 'B', $fontsize);
        $pdf->Cell(15, 0, $data['cer_pressure'], 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont('thsarabunpsk', '', $fontsize);
        $pdf->Cell(27, 0, ' มม.ปรอท-ชีพจร ', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont('thsarabunpsk', 'B', $fontsize);
        $pdf->Cell(8, 0, $data['cer_pulse'], 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont('thsarabunpsk', '', $fontsize);
        $pdf->Cell(18, 0, ' ครั้ง/นาที ', 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont('thsarabunpsk', '', $fontsize);
        $text_d = 'ปกติ';
        if ($data['cer_physicalcondition'] != 1) {
            $text_d = 'ผิดปกติ ' . $data['var_19_other_2'];
        }
        $pdf->Cell(45, 0, 'สภาพร่างกายทั่วไป อยู่ในเกณฑ์ ', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont('thsarabunpsk', 'B', $fontsize);
        $pdf->Cell(0, 0, $text_d, 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont('thsarabunpsk', '', $fontsize);
// $pdf->Cell(0, 0, 'สภาพร่างกายทั่วไป อยู่ในเกณฑ์ ' . $text_d, 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->MultiCell(0, 7, '        ขอรับรองว่าบุคคลดังกล่าว ไม่เป็นผู้มีร่างกายทุพพลภาพจนไม่สามารถปฏิบัติหน้าที่ได้ ไม่ปรากฏอาการของโรคจิต หรือจิตฟั่นเฟือน หรือปัญญาอ่อน ไม่ปรากฏ อาการของการติดยาเสพติดให้โทษ และอาการของโรคพิษสุราเรื้อรัง ไม่ปรากฏอาการและอาการ แสดงของโรคต่อไปนี้', '', 'L');
        $pdf->Cell(0, 0, '        ๑. โรคเรื้อนในระยะติดต่อ หรือระยะที่ปรากฏอาการเป็นที่รังเกียจแก่สังคม', 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(0, 0, '        ๒. วัณโรคระยะอันตราย', 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(0, 0, '        ๓. โรคเท้าช้างในระยะที่ปรากฏอาการเป็นที่น่ารังเกียจแก่สังคม', 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        if (!empty($data['cer_disease'])) {
            $pdf->MultiCell(0, 7, '        ๔. ' . $data['cer_disease'], '', 'L');
        }
        $pdf->Cell(70, 0, '        สรุปความเห็นและข้อแนะนำของแพทย์  ', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont('thsarabunpsk', 'b', 15);
        $pdf->MultiCell(0, 7, (empty($data['cer_comment']) ? '..........................................................................................................................' : $data['cer_comment']), '', 'L');
        $pdf->SetFont('thsarabunpsk', '', 15);
        $pdf->Ln(4);
//        $pdf->Ln();
        $pdf->Cell(0, 0, 'ลงชื่อ...............................................แพทย์ผู้ตรวจร่างกาย', 0, 1, 'R', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(100, 0, '', 0, 0, 'R', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(85, 0, '( ' . $data['doc_fullname'] . ' ว.' . $data['certificate'] . ' )', 0, 1, 'C', 0, '', 0, false, 'T', 'M');
//        }
        $pdf->SetFont('thsarabunpsk', '', 14);
        $pdf->Cell(0, 0, 'หมายเหตุ (๑) ต้องเป็นแพทย์ชึ่งได้ขึ้นทะเบียนรับใบอนุญาตประกอบวิชาชีพเวชกรรม', 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(0, 0, '             (๒) ให้แสดงว่าเป็นผู้มีร่างกายสมบูรณ์เพียงใด ใบรับรองแพทบ์ฉบับนี้ให้ใช้ได้ ๑ เดือนนับแต่วันที่ตรวจร่างกาย', 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(0, 0, 'แบบฟอร์มนี้ได้รับการรับรองจากมติคณะกรรมการแพทยสภาในการประชุมครั้งที่ 8/2551 วันที่ 14 สิงหาคม 2551', 0, 1, 'L', 0, '', 0, false, 'T', 'M');
//        }
        $pdf->Output();
    }

    public function actionCheckDateWork($range_date, $range_type)
    {
        $fixDate = Yii::$app->request->get('date');
        if (empty($fixDate)) {
            $calDate = date("Y-m-d", strtotime("+{$range_date} {$range_type}", strtotime(date('Y-m-d'))));
        } else {
            $calDate = SDdate::phpThDate2mysqlDate($fixDate, '-');
        }

        return SDdate::mysql2phpDate($this->CheckHoliday($calDate), '-');
    }

    private function CheckHoliday($date)
    {
        $result = TRUE;
        while ($result) {
            $DayOfWeek = date("w", strtotime($date));
            if ($DayOfWeek == 0 or $DayOfWeek == 6) {  // 0 = Sunday, 6 = Saturday;
                $date = date("Y-m-d", strtotime("+1 day", strtotime($date)));
            } else {
                $result = FALSE;
                $resultStop = TRUE;
                while ($resultStop) {
                    $stopDate = \backend\modules\patient\classes\PatientFunc::getCalendarStopEvent('', $date, $date);
                    if (empty($stopDate)) {
                        $resultStop = FALSE;
                    } else {
                        $result = TRUE;
                        $date = date("Y-m-d", strtotime("+1 day", strtotime($date)));
                    }
                }
            }
        }

        return $date;
    }

    public function actionPrintAppoint()
    {
        $app_id = Yii::$app->request->get('app_id');
        $data = PatientQuery::getPrintAppoint($app_id);
        $pdf = new SDPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);
        $pdf->SetCreator('AppXQ');
        $pdf->SetAuthor('iencoded@gmail.com');
        $pdf->SetSubject('Original');
        $pdf->SetKeywords('AppXQ, SDII, PDF, report, medical,');

// remove default header/footer
        $pdf->setPrintHeader(true);
        $pdf->setPrintFooter(true);
// set margins
        $pdf->SetMargins(15, 10, 15, TRUE);
        $pdf->SetHeaderMargin(10);
        $pdf->SetFooterMargin(10);
// set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, 15);

// set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set font
        $pdf->SetFont($pdf->fontName, '', $pdf->fontSize);
        $pdf->fontSize = 18;

// add a page
        $pdf->AddPage();

        $dataid = \backend\modules\patient\Module::$dataidForm['hos_config'];
        $ezf_table = \backend\modules\patient\Module::$formTableName['hos_config'];
        $dataHosConfig = \backend\modules\ezforms2\classes\EzfUiFunc::loadTbData($ezf_table, $dataid);
        $path = '';
        if ($dataHosConfig && isset($dataHosConfig['logo_02']) && $dataHosConfig['logo_02'] != '') {
            $path = Yii::getAlias('@storageUrl/ezform/fileinput') . '/' . $dataHosConfig['logo_02'];
//            $path = Yii::getAlias('@storageUrl/ezform/fileinput') . '/logo_02_1544679584007028400.jpeg';
        }
        if ($path != '' && ThaiHisFunc::isUrlExist($path)) {
            $pdf->Image($path, 96, 10, 20, 20, 'JPG');
        }
        $pdf->Ln(24);
        $w = [0 => 5, 1 => 190];
        $pdf->SetFont('thsarabunpsk', 'B', $pdf->fontSize);
        $pdf->Cell(0, 0, 'โรงพยาบาลมะเร็งอุดรธานี (UDONTHANI CANCER HOSPITAL)', 0, 1, 'C', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(0, 0, 'กรมการแพทย์ กระทรวงสาธารณสุข', 0, 1, 'C', 0, '', 0, false, 'T', 'M');
        $pdf->Ln(15);
        $pdf->fontSize = 16;
        $pdf->SetFont($pdf->fontName, 'B', $pdf->fontSize);
        if ($data) {
            if ($data[0]['pt_bdate'] != '') {
                $arr = explode('-', $data[0]['pt_bdate']);
                $data[0]['pt_bdate'] = $arr[0] . $arr[1] . $arr[2];
            }
//        VarDumper::dump($data[0]['pt_bdate']);
            $bdate = $data[0]['pt_bdate'] != '' ? ' อายุ ' . SDdate::getAge($data[0]['pt_bdate']) . ' ปี' : '';
            $pdf->Cell($w[1], $pdf->spaceHigh, "HN : {$data[0]['pt_hn']} ชื่อ - สกุล : {$data[0]['fullname']}" . $bdate, 0, 1, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->Ln();
            $pdf->SetFont($pdf->fontName, 'BU', $pdf->fontSize);
            $data[0]['app_time'] == '' ? $data[0]['app_time'] = '00:00' : null;
//        VarDumper::dump($data[0]['app_time'] . ':00');
            $date = SDdate::mysql2phpThDateTimeFull($data[0]['app_date'] . ' ' . $data[0]['app_time'] . ':00');

            $pdf->Cell(0, $pdf->spaceHigh, "วันนัด {$date}", 0, 1, 'L', 0, '', 0, false, 'T', 'M');

            $pdf->SetFont($pdf->fontName, 'B', $pdf->fontSize);
            $pdf->Cell($w[0], $pdf->spaceHigh, "", 0, 0, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->Cell($w[1], $pdf->spaceHigh, "นัดมาเพื่อ {$data[0]['ins_name']}", 0, 1, 'L', 0, '', 0, false, 'T', 'M');
            if ($data[0]['doc_fullname']) {
                $pdf->Cell($w[0], $pdf->spaceHigh, "", 0, 0, 'L', 0, '', 0, false, 'T', 'M');
                $pdf->Cell($w[1], $pdf->spaceHigh, "แพทย์ผู้นัด {$data[0]['doc_fullname']}", 0, 1, 'L', 0, '', 0, false, 'T', 'M');
            }
            if ($data[0]['sect_name']) {
                $pdf->Cell($w[0], $pdf->spaceHigh, "", 0, 0, 'L', 0, '', 0, false, 'T', 'M');
                $pdf->Cell($w[1], $pdf->spaceHigh, "นัดมาแผนก {$data[0]['sect_name']}", 0, 1, 'L', 0, '', 0, false, 'T', 'M');
            }
//VarDumper::dump($data);
            $appHOrder = '';
            foreach ($data as $value) {
                if (isset($value['order_type_name']) && $value['order_type_name'] != '' && $value['order_type_name'] !== $appHOrder) {
                    $pdf->SetFont($pdf->fontName, 'B', $pdf->fontSize);
                    $pdf->Cell($w[0], $pdf->spaceHigh, "", 0, 0, 'L', 0, '', 0, false, 'T', 'M');
                    $pdf->Cell($w[1], $pdf->spaceHigh, Yii::t('patient', $value['order_type_name']), 0, 1, 'L', 0, '', 0, false, 'T', 'M');
                    $appHOrder = $value['order_type_name'];
                }
                if (isset($value['order_name']) && $value['order_name'] != '') {
                    $pdf->SetFont($pdf->fontName, 'B', 14);
                    $pdf->Cell($w[0] + 5, $pdf->spaceHigh, "", 0, 0, 'L', 0, '', 0, false, 'T', 'M');
                    $pdf->MultiCell($w[1], $pdf->spaceHigh, $value['order_name'], 0, 'L');
                }
            }
            $pdf->Ln();
            $pdf->SetFont($pdf->fontName, 'BU', $pdf->fontSize);
            $pdf->Cell(0, $pdf->spaceHigh, 'คำแนะนำก่อนมาตามนัด', 0, 1, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->SetFont($pdf->fontName, 'B', $pdf->fontSize);
            $pdf->Cell($w[0], $pdf->spaceHigh, "", 0, 0, 'L', 0, '', 0, false, 'T', 'M');

            $pdf->MultiCell($w[1], $pdf->spaceHigh, $data[0]['app_pt_detail'], 0, 'L');

// set style for barcode
            $style = array(
                'border' => 0,
                'vpadding' => 'auto',
                'hpadding' => 'auto',
                'fgcolor' => array(0, 0, 0),
                'bgcolor' => false, //array(255,255,255)
                'module_width' => 1, // width of a single module in points
                'module_height' => 1 // height of a single module in points
            );
            $pdf->SetXY(140, 55);
            $pdf->write1DBarcode("{$data[0]['pt_hn']}", 'C128A', '', '', '', 15, 0.4, $style, 'N');
        }

        $pdf->Output('report.pdf', 'I');
        Yii::$app->end();
    }

    public function actionPrintDischarge()
    {
        $visit_id = Yii::$app->request->get('visit_id');
        $data = PatientQuery::getDischargeCpoe($visit_id);
        $pdf = new SDPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);
        $pdf->SetCreator('AppXQ');
        $pdf->SetAuthor('iencoded@gmail.com');
        $pdf->SetSubject('Original');
        $pdf->SetKeywords('AppXQ, SDII, PDF, report, medical,');

// remove default header/footer
        $pdf->setPrintHeader(FALSE);
        $pdf->setPrintFooter(FALSE);
// set margins
        $pdf->SetMargins(15, 10, 15, TRUE);
        $pdf->SetHeaderMargin(10);
        $pdf->SetFooterMargin(10);
// set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, 15);

// set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set font
        $pdf->SetFont($pdf->fontName, '', $pdf->fontSize);
        $pdf->fontSize = 18;

// add a page
        $pdf->AddPage();

        $dataid = \backend\modules\patient\Module::$dataidForm['hos_config'];
        $ezf_table = \backend\modules\patient\Module::$formTableName['hos_config'];
        $dataHosConfig = \backend\modules\ezforms2\classes\EzfUiFunc::loadTbData($ezf_table, $dataid);
        $path = '';
        if ($dataHosConfig && isset($dataHosConfig['logo_02']) && $dataHosConfig['logo_02'] != '') {
            $path = Yii::getAlias('@storageUrl/ezform/fileinput') . '/' . $dataHosConfig['logo_02'];
        }
        if ($path != '' && ThaiHisFunc::isUrlExist($path)) {
            $pdf->Image($path, 30, 5, 20, 20, 'JPG');
        }
        $w = [0 => 87.5, 1 => 175];
        $pdf->SetFont('thsarabunpsk', 'B', $pdf->fontSize);
        $pdf->Cell(190, 0, 'โรงพยาบาลมะเร็งอุดรธานี (UDONTHANI CANCER HOSPITAL)', 0, 1, 'C', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(190, 0, 'กรมการแพทย์ กระทรวงสาธารณสุข', 0, 1, 'C', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(190, 0, 'เอกสาร จำหน่ายผู้ป่วย', 0, 1, 'C', 0, '', 0, false, 'T', 'M');
        $pdf->Ln(4);
        $pdf->fontSize = 16;
        $pdf->SetFont($pdf->fontName, 'B', $pdf->fontSize);
        $bdate = ' อายุ ' . SDdate::getAge(SDdate::dateTh2bod($data['pt_bdate'])) . ' ปี';
        $pdf->Cell($w[0], $pdf->spaceHigh, "HN : {$data['pt_hn']}", 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell($w[0], $pdf->spaceHigh, "AN : {$data['admit_an']}", 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell($w[0], $pdf->spaceHigh, "ชื่อ - สกุล : {$data['fullname']} ", 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell($w[0], $pdf->spaceHigh, "เกิดวันที่ : " . SDdate::mysql2phpThDateSmall(SDdate::dateTh2bod($data['pt_bdate'])) . $bdate, 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Ln(4);
        $pdf->Cell($w[0], $pdf->spaceHigh, "หอผู้ป่วย : {$data['sect_name']} ", 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell($w[0], $pdf->spaceHigh, "เตียง : {$data['bed']}", 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell($w[0], $pdf->spaceHigh, "วันที่เข้ารักษา : " . SDdate::mysql2phpThDateTime($data['admit_date']), 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell($w[0], $pdf->spaceHigh, "วันที่จำหน่าย : " . SDdate::mysql2phpThDateTime($data['discharge_date']), 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell($w[0], $pdf->spaceHigh, "รวมวันรักษา : {$data['LOS']}", 0, 1, 'L', 0, '', 0, false, 'T', 'M');


        $ezf_id = \backend\modules\patient\Module::$formID['discharge'];
        $modelFields = \backend\modules\ezforms2\models\EzformFields::find()->where('ezf_id = :ezf_id AND ezf_field_name = :ezf_field_name', [':ezf_field_name' => 'discharge_code', ':ezf_id' => $ezf_id])->one();
        if (isset(Yii::$app->session['ezf_input'])) {
            $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($modelFields['ezf_field_type'], Yii::$app->session['ezf_input']);
        }
        $data['discharge_code'] = \backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $modelFields, $data);
        $modelFields = \backend\modules\ezforms2\models\EzformFields::find()->where('ezf_id = :ezf_id AND ezf_field_name = :ezf_field_name', [':ezf_field_name' => 'discharge_status', ':ezf_id' => $ezf_id])->one();
        $data['discharge_status'] = \backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $modelFields, $data);

        $pdf->Cell($w[0], $pdf->spaceHigh, "สถานะการจำหน่าย : {$data['discharge_code']} ", 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell($w[0], $pdf->spaceHigh, "ผลการจำหน่าย : {$data['discharge_status']}", 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->writeHTMLCell($w[1], '', '', '', 'การวินิจฉัย : ' . $data['di_txt'], 0, 1);
        $pdf->Output('report.pdf', 'I');
        Yii:: $app->end
        ();
    }

    public function actionPrintTranfer()
    {
        Yii::$app->session['ezf_input'] = \backend\modules\ezforms2\classes\EzfQuery::getInputv2All();
        $tranfer_id = Yii::$app->request->get('tranfer_id');
        $data = PatientQuery::getTranfer($tranfer_id);
        $pdf = new SDPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);
        $pdf->SetCreator('AppXQ');
        $pdf->SetAuthor('iencoded@gmail.com');
        $pdf->SetSubject('Original');
        $pdf->SetKeywords('AppXQ, SDII, PDF, report, medical,');

// remove default header/footer
        $pdf->setPrintHeader(FALSE);
        $pdf->setPrintFooter(FALSE);
// set margins
        $pdf->SetMargins(15, 10, 15, TRUE);
        $pdf->SetHeaderMargin(10);
        $pdf->SetFooterMargin(10);
// set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, 15);

// set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set font
        $pdf->SetFont($pdf->fontName, '', $pdf->fontSize);
        $pdf->fontSize = 18;

// add a page
        $pdf->AddPage();

        $dataid = \backend\modules\patient\Module::$dataidForm['hos_config'];
        $ezf_table = \backend\modules\patient\Module::$formTableName['hos_config'];
        $dataHosConfig = \backend\modules\ezforms2\classes\EzfUiFunc::loadTbData($ezf_table, $dataid);
        $path = '';
        if ($dataHosConfig && isset($dataHosConfig['logo_02']) && $dataHosConfig['logo_02'] != '') {
            $path = Yii::getAlias('@storageUrl/ezform/fileinput') . '/' . $dataHosConfig['logo_02'];
        }
        if ($path != '' && ThaiHisFunc::isUrlExist($path)) {
            $pdf->Image($path, 30, 5, 20, 20, 'JPG');
        }
        $w = [0 => 87.5, 1 => 175, 2 => 20];
        $pdf->SetFont('thsarabunpsk', 'B', $pdf->fontSize);
        $pdf->Cell(190, 0, 'โรงพยาบาลมะเร็งอุดรธานี (UDONTHANI CANCER HOSPITAL)', 0, 1, 'C', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(190, 0, 'กรมการแพทย์ กระทรวงสาธารณสุข', 0, 1, 'C', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(190, 0, 'แบบสำหรับส่งผู้ป่วยไปรักการตรวจหรือรักษาต่อ', 0, 1, 'C', 0, '', 0, false, 'T', 'M');
        $pdf->Ln(4);
        $pdf->fontSize = 16;
        $pdf->SetFont($pdf->fontName, 'B', $pdf->fontSize);

        $pdf->Cell($w[0], $pdf->spaceHigh, "เลขที่ {$data['refer_no']}", 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell($w[0], $pdf->spaceHigh, "วันที่ส่งออก " . SDdate::mysql2phpThDate($data['refer_date']), 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell($w[0], $pdf->spaceHigh, "ถึง  โรงพยาบาล " . $this->getValue($data, 'refer_hospital'), 0, 1, 'L', 0, '', 0, false, 'T', 'M');

        $pdf->Cell($w[2], $pdf->spaceHigh, "", 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell($w[0], $pdf->spaceHigh, "พร้อมหนังสือนี้ ขอส่งผู้ป่วยชื่อ {$data['fullname']}   เพศ " . ($data['pt_sex'] == '1' ? 'ชาย' : 'หญิง') . "   อายุ " . SDdate::getAge(SDdate::dateTh2bod($data['pt_bdate'])) . " ปี", 0, 1, 'L', 0, '', 0, false, 'T', 'M');

        $pdf->Cell($w[0], $pdf->spaceHigh, "อยู่บ้านเลขที่ {$data['pt_address']}     หมู่ที่ {$data['pt_moi']}   ตำบล {$data['DISTRICT_NAME']}   อำเภอ {$data['AMPHUR_NAME']}    จังหวัด {$data['PROVINCE_NAME']}  {$data['pt_addr_zipcode']}", 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Ln(4);
        $pdf->Cell($w[0], $pdf->spaceHigh, "มาเพื่อโปรด " . $this->getValue($data, 'refer_type'), 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Ln(4);
//        $pdf->MultiCell($w[0], $pdf->spaceHigh, "มาเพื่อโปรด ".$this->getValue($data, 'refer_type'), 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->MultiCell(0, 5, "ประวัติการป่วย {$data['refer_ph']}", 0, 'L', 0, 1, '', '', true);
        $pdf->Ln(4);
        $pdf->MultiCell(0, 5, "ผลการตรวจ {$data['refer_result']}", 0, 'L', 0, 1, '', '', true);
        $pdf->Ln(4);
        $pdf->MultiCell(0, 5, "การวินิจฉัยโรคขั้นต้น : " . $data['code'] . " " . $data['diag1name'], 0, 'L', 0, 1, '', '', true);
        $pdf->Ln(4);
        $pdf->MultiCell(0, 5, "การวินิจฉัยโรคขั้นต้น 2 : " . $data['code1'] . ' ' . $data['diag2name'], 0, 'L', 0, 1, '', '', true);
        $pdf->Ln(4);
        $pdf->MultiCell(0, 5, "การรักษาที่ได้ให้ไว้แล้ว {$data['refer_treatment']}", 0, 'L', 0, 1, '', '', true);
        $pdf->Ln(4);
        $pdf->MultiCell(0, 5, "สาเหตุที่ส่ง {$data['refer_cause']}", 0, 'L', 0, 1, '', '', true);
        $pdf->Ln(4);
        $pdf->MultiCell(0, 5, "รายละเอียดอื่นๆ {$data['refer_comment']}", 0, 'L', 0, 1, '', '', true);
        $pdf->Ln(4);
        $pdf->Cell($w[0], $pdf->spaceHigh, "เดินทางโดย " . $this->getValue($data, 'refer_by'), 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Ln(4);
        $pdf->Cell($w[0], $pdf->spaceHigh, "เอกสารแนบ ", 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Ln(4);
        $pdf->Cell($w[0], $pdf->spaceHigh, $this->refer_attr($data['refer_attr_1'], 'refer_attr_1') . ' ' . $this->refer_attr($data['refer_attr_2'], 'refer_attr_2') . ' ' . $this->refer_attr($data['refer_attr_3'], 'refer_attr_3') . $this->refer_attr($data['refer_attr_4'], 'refer_attr_4'), 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Ln(4);

        $pdf->Output('report.pdf', 'I');
        Yii:: $app->end();
    }

    private function refer_attr($value, $field)
    {
        if ($value == 1 && $field == 'refer_attr_1') {
            return ' patho ';
        } else if ($value == 1 && $field == 'refer_attr_2') {
            return ' Film/ผลอ่าน ';
        } else if ($value == 1 && $field == 'refer_attr_3') {
            return ' Lab ';
        } else if ($value == 1 && $field == 'refer_attr_4') {
            return ' อื่นๆ   ';
        }
    }

    private function getValue($data, $fieldName)
    {
        $ezf_id = \backend\modules\patient\Module::$formID['tranfer'];
        $modelFields = \backend\modules\ezforms2\models\EzformFields::find()->where('ezf_id = :ezf_id AND ezf_field_name = :ezf_field_name', [':ezf_field_name' => $fieldName, ':ezf_id' => $ezf_id])->one();
        if (isset(Yii::$app->session['ezf_input'])) {
            $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($modelFields['ezf_field_type'], Yii::$app->session['ezf_input']);
        }
        return \backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $modelFields, $data);
    }

    public function actionGetListProject($q = null)
    {
        $ezf_id = \backend\modules\patient\Module::$formID['project'];
        $ezf_tbname = \backend\modules\patient\Module::$formTableName['project'];

        $data = PatientQuery::getProjectName($q);

        $out = ['results' => ['id' => '', 'text' => '']];
        $out['results'] = array_values($data);

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $out;
    }

    public function actionKm4OrderShow($hn, $visit_date, $reloadDiv)
    {
        $url = Yii::$app->params['km4Url'] . "/api/core-api/cpoe-detail?pt_hn=" . $hn . "&visit_date=" . $visit_date;

        $body = \backend\modules\patient\classes\PatientFunc::callWebService($url);
        $body = json_decode($body, TRUE);

        if (empty($body['status'])) {
            foreach ($body as $valueBody) {
                foreach ($valueBody as $key => $value) {
                    $arrData[] = $value;
                }
            }
        } else {
            $arrData = [];
        }

        $dataProvider = \backend\modules\patient\classes\PatientFunc::ArrayToDataProvider($arrData);

        return $this->renderAjax('_km4_order_show', [
            'dataProvider' => $dataProvider,
            'reloadDiv' => $reloadDiv,
        ]);
    }

    public function actionPrintSticker()
    {
        $ptid = Yii::$app->request->get('pt_id');
        $data = PatientQuery::getPatient2($ptid);

        $pdf = new SDPDF('L', PDF_UNIT, [45, 85], true, 'UTF-8', false);
        $pdf->SetCreator('AppXQ');
        $pdf->SetAuthor('iencoded@gmail.com');
        $pdf->SetSubject('Original');
        $pdf->SetKeywords('AppXQ, SDII, PDF, report, medical,');

        // remove default header/footer
        $pdf->setPrintHeader(FALSE);
        $pdf->setPrintFooter(FALSE);
        // set margins
        $pdf->SetMargins(5, 5, 5, TRUE);
        $pdf->SetHeaderMargin(10);
        $pdf->SetFooterMargin(10);
        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, 15);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set font
        $pdf->SetFont($pdf->fontName, '', $pdf->fontSize);
        $pdf->fontSize = 22;
        $style = array(
            'position' => '',
            'align' => 'C',
            'stretch' => false,
            'fitwidth' => true,
            'cellfitalign' => '',
            // 'border' => true,
            'hpadding' => 'auto',
            'vpadding' => 'auto',
            // 'fgcolor' => array(0, 0, 0),
            // 'bgcolor' => false, //array(255,255,255),
            'text' => true,
            'font' => 'helvetica',
            'fontsize' => 14,
            'stretchtext' => 4
        );
        // add a page
        $pdf->AddPage();
        $ptsex = $data['pt_sex'] == 1 ? 'ชาย' : 'หญิง';
        $pdf->write1DBarcode($data['pt_hn'], 'C128', '', '', '', 17, 0.7, $style, 'N');
        $pdf->SetFont('thsarabunpsk', 'B', 17);
        $pdf->Cell(0, 0, $data['fullname'] . ' อายุ ' . $data['ageInYears'] . ' ปี เพศ ' . $ptsex, 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Output('report.pdf', 'I');
        Yii:: $app->end();
    }

    public function actionReportPatient()
    {
        $ptid = Yii::$app->request->get('pt_id');
        Yii::$app->session['ezf_input'] = \backend\modules\ezforms2\classes\EzfQuery::getInputv2All();
        $tranfer_id = Yii::$app->request->get('tranfer_id');
        $data = PatientQuery::getPatient2($ptid);

        $pdf = new SDPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);
        $pdf->SetCreator('AppXQ');
        $pdf->SetAuthor('iencoded@gmail.com');
        $pdf->SetSubject('Original');
        $pdf->SetKeywords('AppXQ, SDII, PDF, report, medical,');

        // remove default header/footer
        $pdf->setPrintHeader(FALSE);
        $pdf->setPrintFooter(FALSE);
        // set margins
        $pdf->SetMargins(15, 10, 15, TRUE);
        $pdf->SetHeaderMargin(10);
        $pdf->SetFooterMargin(10);
        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, 15);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set font
        $pdf->SetFont($pdf->fontName, '', $pdf->fontSize);
        $pdf->fontSize = 18;

        // add a page
        $pdf->AddPage();

        $dataid = \backend\modules\patient\Module::$dataidForm['hos_config'];
        $ezf_table = \backend\modules\patient\Module::$formTableName['hos_config'];
        $dataHosConfig = \backend\modules\ezforms2\classes\EzfUiFunc::loadTbData($ezf_table, $dataid);
        if ($dataHosConfig['logo_02']) {
            $path = Yii::getAlias('@storageUrl/ezform/fileinput') . '/' . $dataHosConfig['logo_02'];
        } else {
            $path = Yii::getAlias('@storageUrl/images') . '/nouser.png';
        }

        $pdf->Image($path, 30, 5, 20, 20, 'JPG');
        $w = [0 => 87.5, 1 => 175, 2 => 20];
        $pdf->SetFont('thsarabunpsk', 'B', $pdf->fontSize);
        $pdf->Cell(190, 0, 'โรงพยาบาลมะเร็งอุดรธานี (UDONTHANI CANCER HOSPITAL)', 0, 1, 'C', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(190, 0, 'กรมการแพทย์ กระทรวงสาธารณสุข', 0, 1, 'C', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(190, 0, 'เวชระเบียนผู้ป่วย (ห้ามนำออกนอกโรงพยาบาล)', 0, 1, 'C', 0, '', 0, false, 'T', 'M');
        $pdf->Ln(4);
        $pdf->fontSize = 16;
        $pdf->SetFont($pdf->fontName, 'B', $pdf->fontSize);
//        $pdf->SetXY(15, 20);
        $style = array(
            'position' => '',
            'align' => 'C',
            'stretch' => false,
            'fitwidth' => true,
            'cellfitalign' => '',
            // 'border' => true,
            'hpadding' => 'auto',
            'vpadding' => 'auto',
            // 'fgcolor' => array(0, 0, 0),
            // 'bgcolor' => false, //array(255,255,255),
            'text' => true,
            'font' => 'helvetica',
            'fontsize' => 14,
            'stretchtext' => 4
        );
        $pdf->write1DBarcode($data['pt_hn'], 'C128', '', '', '', 17, 0.5, $style, 'N');
//        $pdf->Cell(190, 0, $data['pt_hn'], 0, 1, 'L', 0, '', 0, false, 'T', 'M');

        $ptsex = $data['pt_sex'] == 1 ? 'ชาย' : 'หญิง';
        $datatime = date('d/m/Y'); //SDdate::mysql2phpThDateSmall($data['create_date']);
        $pdf->Cell(0, $pdf->spaceHigh, "ชื่อผู้ป่วย {$data['fullname']}  เพศ {$ptsex}   อายุ {$data['ageInYears']} ปี   ณ วันที่  {$datatime} วันที่ทำบัตร {$datatime}", 1, 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->MultiCell(90, 5, 'ประวัติ ', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(90, 5, 'ที่อยู่ ' . $data['pt_address'] . ' หมู่ที่  ' . $data['pt_moi'], 1, 'L', 0, 1, '', '', true);
        $pdf->MultiCell(90, 5, 'วันเกิด  ' . SDdate::mysql2phpThDateSmall($data['pt_bdate']), 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(90, 5, '  ชอย                       ถนน ', 1, 'L', 0, 1, '', '', true);
        if (trim($data['pt_contact_status']) == 'บิดา') {
            $pdf->MultiCell(90, 5, 'ชื่อบิดา ' . $data['pt_contact_name'], 1, 'L', 0, 0, '', '', true);
        } else {
            $pdf->MultiCell(90, 5, 'ชื่อบิดา ', 1, 'L', 0, 0, '', '', true);
        }
        //$pdf->MultiCell(90, 5, 'ชื่อบิดา ' . $data['pt_contact_name'], 1, 'L', 0, 0, '', '', true);

        $pdf->MultiCell(90, 5, '  ตำบล ' . $data['DISTRICT_NAME'], 1, 'L', 0, 1, '', '', true);
        if (trim($data['pt_contact_status']) == 'มารดา') {
            $pdf->MultiCell(90, 5, 'ชื่อมารดา ' . $data['pt_contact_name'], 1, 'L', 0, 0, '', '', true);
        } else {
            $pdf->MultiCell(90, 5, 'ชื่อมารดา ', 1, 'L', 0, 0, '', '', true);
        }
        // $pdf->MultiCell(90, 5, 'ชื่อมารดา ' . $data['pt_contact_name'], 1, 'L', 0, 0, '', '', true);

        $pdf->MultiCell(90, 5, '  อำเภอ ' . $data['AMPHUR_NAME'], 1, 'L', 0, 1, '', '', true);
        $pdf->MultiCell(90, 5, 'เชื่อชาติ   ' . $data['origin_name'] . '  สัญชาติ  ' . $data['national_name'], 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(90, 5, '  จังหวัด ' . $data['PROVINCE_NAME'], 1, 'L', 0, 1, '', '', true);
        $pdf->MultiCell(90, 5, 'ศาสนา ' . $data['religion_name'], 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(90, 5, '  รหัสไปรษณี   ' . $data['pt_addr_zipcode'] . '  เบอร์โทรศัพท์   ' . $data['pt_phone'], 1, 'L', 0, 1, '', '', true, 0, false, true, 40, 'TRBL');
        $pdf->MultiCell(90, 5, 'อาชีพ ' . $data['occ_name'], 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(90, 5, 'ผู้รับแจ้ง ' . $data['pt_contact_name'], 1, 'L', 0, 1, '', '', true, 0, false, true, 40, 'TRBL');
        $pdf->MultiCell(90, 5, 'สถานะภาพสมรส ' . $this->getMstatus($data['pt_mstatus']), 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(90, 5, 'เกี่ยวข้องเป็น ' . $data['pt_contact_status'], 1, 'L', 0, 1, '', '', true, 0, false, true, 40, 'TRBL');
        $pdf->MultiCell(90, 5, 'ประเภทสิทธิผู้ป่วย ' . $data['right_name'], 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(90, 5, 'ที่ติดต่อ ' . $data['pt_address'] . ' หมู่ที่  ' . $data['pt_moi'] . ' ต.' . $data['DISTRICT_NAME'] . ' อ.' . $data['AMPHUR_NAME'] . 'จ.' . $data['PROVINCE_NAME'] . 'รหัสไปรษณี ' . $data['pt_addr_zipcode'] . ' โทร ' . $data['pt_contact_phone'], 1, 'L', 0, 1, '', '', true, 0, false, true, 40, 'TRBL');
        $pdf->MultiCell(90, 5, 'เลขบัตรประชาชน   ' . $data['pt_cid'], 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(90, 5, 'IN ' . $data['pt_inno'], 1, 'L', 0, 1, '', '', true, 0, false, true, 40, 'TRBL');
        $pdf->Output('report.pdf', 'I');
        Yii:: $app->end();
    }

    public static function getMstatus($n)
    {
        if ($n == '1') {
            return 'โสด';
        } else if ($n == '2') {
            return 'คู่';
        } else if ($n == '3') {
            return 'หม้าย';
        } else if ($n == '4') {
            return 'หย่า';
        } else if ($n == '5') {
            return 'แยก';
        } else if ($n == '6') {
            return 'สมณะ';
        } else if ($n == '9') {
            return 'ไม่ทราบ';
        }
    }

    public function actionAutocom($sex, $q, $field)
    {
        $ezf_id = \backend\modules\patient\Module::$formID['pe'];
        $ezf_tbname = \backend\modules\patient\Module::$formTableName['pe'];
        $json_data = [];
        $data = PatientQuery::getDataAutocom($q, $ezf_tbname, $field);
        foreach ($data as $value) {
            $json_data[] = $value[$field];
        }

        return json_encode($json_data);
    }

    public function actionDrugOrderCount()
    {
        $ezf_id = \backend\modules\patient\Module::$formID['pis_order_count'];
        $ezf_table = \backend\modules\patient\Module::$formTableName['pis_order_count'];
        $date = date('Y-m-d');
        $data = \backend\modules\pis\classes\PisQuery::getDrugOrderCount($date);

        foreach ($data as $value) {
            $dataCount = PatientFunc::loadTbDataByField($ezf_table, [
                'order_c_docid' => $value['user_create'],
                'order_c_itemid' => $value['order_trad_id']
            ]);
            if ($dataCount) {
                PatientFunc::saveDataNoSys($ezf_id, $ezf_table, $dataCount['id'], [
                    'order_c_count' => $dataCount['order_c_count'] + $value['c_item']
                ]);
            } else {
                PatientFunc::backgroundInsert($ezf_id, '', '', [
                    'order_c_docid' => $value['user_create'],
                    'order_c_itemid' => $value['order_trad_id'],
                    'order_c_count' => $value['c_item']
                ]);
            }
        }
    }

    private function urlExists($url)
    {
        // Remove all illegal characters from a url
        $url = filter_var($url, FILTER_SANITIZE_URL);

        // Validate URI
        if (filter_var($url, FILTER_VALIDATE_URL) === FALSE
            // check only for http/https schemes.
            || !in_array(strtolower(parse_url($url, PHP_URL_SCHEME)), ['http', 'https'], true)
        ) {
            return false;
        }

        // Check that URL exists
        $file_headers = @get_headers($url);
        return !(!$file_headers || $file_headers[0] === 'HTTP/1.1 404 Not Found');
    }

}

class SPDF extends \common\lib\tcpdf\SDPDF
{

    public function Footer()
    {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('thsarabunpsk', 'B', 16);
        // Page number
        $this->Cell(0, 10, 'Report By' . $GLOBALS["report_doctor"], 0, false, 'L', 0, '', 0, false, 'T', 'M');
    }

}

class KKPDF extends \common\lib\tcpdf\SDPDF
{

    public function Header()
    {
        $data = $GLOBALS['data'];
        //print_r($this->result);
        $w = array(55, 40, 30, 30, 15);
        $dataid = \backend\modules\patient\Module::$dataidForm['hos_config'];
        $ezf_table = \backend\modules\patient\Module::$formTableName['hos_config'];
        $dataHosConfig = EzfUiFunc::loadTbData($ezf_table, $dataid);
        if ($dataHosConfig['logo_05']) {
            try {
                $path = Yii::getAlias('@storageUrl/ezform/fileinput') . '/' . $dataHosConfig['logo_02'];
            } catch (Exception $ex) {
                $path = null;
            }
        } else {
            $path = null;
        }
        if ($path) {
            $this->Image($path, 20, 4, 21, 21, 'JPG');
        }
        //  $this->Image('../../images/logo.png', 20, 4, 21, 21, 'PNG', 'http://www.udcancer.org');
        $this->SetFont('thsarabunpsk', 'B', 16);
        $this->Cell(23, 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $this->Cell(140, 0, 'โรงพยาบาลมะเร็งอุดรธานี (UDONTHANI CANCER HOSPITAL)', 0, 1, 'L', 0, '', 0, false, 'T', 'M');

        $this->Cell(23, 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $this->Cell(80, 0, 'NAME : ' . $data[0]['header_fullname'], 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $this->Cell(32, 0, 'HN : ' . $data[0]['header_hn'], 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $this->Cell(40, 0, 'Lab No : ' . $data[0]['header_ln'], 0, 1, 'L', 0, '', 0, false, 'T', 'M');

        $this->Cell(23, 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $this->Cell(25, 0, 'เพศ : ' . $data[0]['sex'], 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $this->Cell(25, 0, 'อายุ : ' . $data[0]['age'], 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $this->Cell(62, 0, 'แผนก : ' . $data[0]['dept_name'], 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $this->Cell(50, 0, 'Date : ' . $data[0]['receive_date'], 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        //$this->SetFont('thsarabunpsk', 'UB', 14);
        //$this->Cell(192, 0, 'Laboratory', 'T', 1, 'L', 0, '', 0, false, 'T', 'M');

        $this->SetFont('thsarabunpsk', 'B', 14);
        $this->Cell($w[0], 0, '  รายการทดสอบ', 'T', 0, 'L', 0, '', 0, false, 'T', 'M');
        $this->Cell($w[1], 0, 'ผลการตรวจ', 'T', 0, 'L', 0, '', 0, false, 'T', 'M');
        $this->Cell($w[2], 0, 'หน่วย', 'T', 0, 'L', 0, '', 0, false, 'T', 'M');
        $this->Cell($w[3], 0, 'ค่าปกติ', 'T', 0, 'L', 0, '', 0, false, 'T', 'M');
        $this->Cell($w[4] + 12, 0, '', 'T', 1, 'L', 0, '', 0, false, 'T', 'M');
        $GLOBALS['approved_by'] = $data[0]['approved_by'];
        $GLOBALS['app_date'] = $data[0]['app_date'];
    }

    function Footer()
    {
        $this->SetY(-18);
        // Set font
        $this->SetFont('thsarabunpsk', 'B', 14);
        // Page number
        $this->Cell(95, 0, 'Approved By : ' . $GLOBALS['approved_by'], 'T', 0, 'L', 0, '', 0, false, 'T', 'M');
        $this->Cell(88, 0, 'Print date : ' . date('d/m/Y H:i:s') . ' Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 'T', 1, 'R', 0, '', 0, false, 'T', 'M');
        $this->Cell(95, 0, 'Approved Date/time : ' . $GLOBALS['app_date'], 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $this->Cell
        (88, 0, '', 0, 1, 'R', 0, '', 0, false, 'T', 'M');
    }

}

?>
