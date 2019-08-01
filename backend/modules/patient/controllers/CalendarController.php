<?php

namespace backend\modules\patient\controllers;

use Yii;
use yii\web\Controller;
use backend\modules\patient\classes\CalendarFunc;

/**
 * Calendar controller
 */
class CalendarController extends Controller {

    public function actionIndex($date = null) {
        $dateNow = isset($date) ? $date : date('Y-m-d');
        $userProfile = Yii::$app->user->identity->profile;
        $events = CalendarFunc::getEventCalendarFront('app', $dateNow);

        return $this->render('calendar', ['events' => $events, 'dateNow' => $dateNow]);
    }

    public function actionFrontEnd($date = null) {
        $dateNow = isset($date) ? $date : date('Y-m-d');
        $userProfile = Yii::$app->user->identity->profile;
        $pt_id = '1503471963004608800';
        $initdata = \backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String(['app_chk_pt_id' => $pt_id]);
        $events = CalendarFunc::getEventCalendarFront('app', $dateNow);

        return $this->render('calendar-frontend', ['events' => $events, 'dateNow' => $dateNow, 'initdata' => $initdata]);
    }

    public function actionBackEnd($date = null) {
        $dateNow = isset($date) ? $date : date('Y-m-d');
        $userProfile = Yii::$app->user->identity->profile;
        $events = CalendarFunc::getEventCalendarFront('app', $dateNow);

        return $this->render('calendar-backend', ['events' => $events, 'dateNow' => $dateNow]);
    }
    
    public function actionAppoinReport() {
        $ezf_id = \backend\modules\patient\Module::$formID['visit'];
        Yii::$app->session['ezf_input'] = \backend\modules\ezforms2\classes\EzfQuery::getInputv2All();
        $searchModel = \backend\modules\patient\classes\PatientFunc::getModel($ezf_id, '');
        $searchModel['create_date'] = date('d-m-Y');

        return $this->render('appoin_report', [
                    'ezf_id' => $ezf_id,
                    'searchModel' => $searchModel,
        ]);
    }
    
    public function actionAppoinGrid() {
        $ezf_id = \backend\modules\patient\Module::$formID['visit'];
        $searchModel = \backend\modules\patient\classes\PatientFunc::getModel($ezf_id, '');
        $filter = Yii::$app->request->post();
        $dataProvider = CalendarFunc::getReportTypeDoctor($searchModel, $filter);
        
        return $this->renderAjax('_appoin_grid', [
            'ezf_id' => $ezf_id,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    public function actionAppoinPdf() {
        $appoint_id = Yii::$app->request->get('appoint_id');
        $data = CalendarFunc::getPatientByAppid($appoint_id);
        $dataApp = CalendarFunc::getAppointitem($appoint_id);
        $pdf = new \common\lib\tcpdf\SDPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);
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

//        $path = Yii::getAlias('@storageUrl/images') . '/logo2.jpg';
//        $pdf->Image($path, 30, 5, 20, 20, 'JPG');
        $w = [0 => 87.5, 1 => 175, 2 => 20];
        $pdf->SetFont('thsarabunpsk', 'B', $pdf->fontSize);
        $pdf->Cell(190, 0, 'ใบนัดรายการตรวจ', 0, 1, 'C', 0, '', 0, false, 'T', 'M');
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
        $html = "<table>"
                . "<tr><td>HN {$data['pt_hn']} </td><td></td><td></td></tr>"
                . "<tr><td>ชื่อผู้ป่วย {$data['patientfullname']}</td><td></td><td></td></tr>"
                . "<tr><td>นัดตรวจ {$data['InspectName']}</td><td></td><td></td></tr>"
                . "<tr><td>วันนัด {$data['app_date']}</td><td></td><td></td></tr>"
                . "<tr><td>นัดมาแผนก {$data['sect_name']}</td><td></td><td></td></tr>"
                . "<tr><td>นัดมาพบ {$data['DoctorFullname']}</td><td></td><td></td></tr>"
                . "</table>";
        $pdf->writeHTML($html);

        $pdf->MultiCell(90, 5, 'รายการตรวจ ', 0, 'L', 0, 1, '', '', true);
        $pdf->MultiCell(30, 5, 'รหัสรายการ', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(120, 5, 'ชื่อรายการ', 1, 'L', 0, 0, '', '', true);
        $pdf->MultiCell(30, 5, 'ราคา', 1, 'R', 0, 1, '', '', true);
        foreach ($dataApp AS $rowdata) {
            $pdf->MultiCell(30, 5, $rowdata['order_tran_code'], 1, 'L', 0, 0, '', '', true);
            $pdf->MultiCell(120, 5, $rowdata['order_name'], 1, 'L', 0, 0, '', '', true);
            $pdf->MultiCell(30, 5, $rowdata['full_price'], 1, 'R', 0, 1, '', '', true);
        }
        $pdf->Output('report.pdf', 'I');
        Yii:: $app->end();
    }

}
