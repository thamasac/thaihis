<?php
use backend\modules\subjects\classes\SubjectManagementQuery;
use backend\modules\ezforms2\classes\EzfQuery;
const TEMPIMGLOC = 'tempimage.png';
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//$invoicePDF = new common\lib\tcpdf\InvoicePDF();
//        $invoicePDF->contentHeader = $header;
//        $invoicePDF->createTableData($header,$data);
//        $invoicePDF->Output($subject_id.'-Invoice' . $idfile . ".pdf");
$url_curr = \cpn\chanpan\classes\CNServerConfig::getDomainName();
//$url_curr="udon.work.ncrc.in.th";
$projectData = backend\modules\subjects\classes\ReportQuery::getProjectData($url_curr);
$userdata = EzfQuery::getUserProfile(Yii::$app->user->id);

if($projectData['project_icon'] != null){
    $logo = Yii::getAlias('@storageUrl').'/ezform/fileinput/'.$projectData['project_icon'];
    $pic= $projectData['projecticon'];
    $log = base64_decode($img[1]);
    if ($log !== false) {
        if (file_put_contents(TEMPIMGLOC, $log) !== false) {
            $logo = TEMPIMGLOC;
        }
    }
}else{
    $logo = Yii::getAlias('@backendUrl')."/img/health-icon.png";
}

$pdf = new \common\lib\phppdf\InvoicePDF();
$pdf->AddFont('THSarabunNew', '', 'THSarabunNew.php');
$pdf->AddFont('THSarabunNew', 'B', 'THSarabunNew_b.php');
$pdf->SetFont('THSarabunNew', '', 16);

$pdf->AddPage();

// Content Header Right column
$pdf->Image($logo, 95, 10, 25);
$pdf->SetFont('THSarabunNew', 'B', 16);

$pdf->setXY(150, 35);
$pdf->Cell(40, 10, iconv('UTF-8', 'cp874', Yii::t('subjects', 'Invoice No').' '.$invoice_no));
$pdf->setXY(150, 40);
$pdf->Cell(40, 12, iconv('UTF-8', 'cp874', Yii::t('subjects', 'Date').' '.SubjectManagementQuery::convertDate(date('Y-m-d'))));

// Content Header Left column

if (Yii::t('subjects', 'Check Language') == 'Eng') {
    $pdf->SetFont('THSarabunNew', 'B', 16);
    $pdf->setXY(10, 35);
    $pdf->Cell(30, 10, iconv('UTF-8', 'cp874', Yii::t('subjects', 'Sponsor Name') . ' : '));
    $pdf->SetFont('THSarabunNew', '', 16);
    $pdf->Cell(40, 10, iconv('UTF-8', 'cp874', $customer_name));

    $pdf->SetFont('THSarabunNew', 'B', 16);
    $pdf->setXY(10, 40);
    $pdf->Cell(27, 10, iconv('UTF-8', 'cp874', Yii::t('subjects', 'Address') . ' : '));
    $pdf->SetFont('THSarabunNew', '', 16);
    $pdf->setXY(30, 43);
    $pdf->MultiCell(100, 5, iconv('UTF-8', 'cp874', $address), null, 'LB');

    $pdf->SetFont('THSarabunNew', 'B', 16);
    $pdf->setX(10);
    $pdf->Cell(20, 5, iconv('UTF-8', 'cp874', Yii::t('subjects', 'Protocol') . ' : '));
    $pdf->SetFont('THSarabunNew', '', 16);
    $pdf->MultiCell(100, 5, iconv('UTF-8', 'cp874', $projectData['projectname']));

    $pdf->SetFont('THSarabunNew', 'B', 16);
    $pdf->setX(10);
    $pdf->Cell(25, 5, iconv('UTF-8', 'cp874', Yii::t('subjects', 'Protocol No') . ' : '));
    $pdf->SetFont('THSarabunNew', '', 16);
    $pdf->MultiCell(100, 5, iconv('UTF-8', 'cp874', $projectData['projectacronym']));

    $pdf->SetFont('THSarabunNew', 'B', 16);
    $pdf->setX(10);
    $pdf->Cell(17, 5, iconv('UTF-8', 'cp874', Yii::t('subjects', 'Site No') . ' : '));
    $pdf->SetFont('THSarabunNew', '', 16);
    $pdf->MultiCell(100, 5, iconv('UTF-8', 'cp874', $projectData['sitecode']));

} else {
    $pdf->SetFont('THSarabunNew', 'B', 16);
    $pdf->setXY(10, 35);
    $pdf->Cell(15, 10, iconv('UTF-8', 'cp874', Yii::t('subjects', 'Sponsor Name') . ' : '));
    $pdf->SetFont('THSarabunNew', '', 16);
    $pdf->Cell(40, 10, iconv('UTF-8', 'cp874', $customer_name));

    $pdf->SetFont('THSarabunNew', 'B', 16);
    $pdf->setXY(10, 40);
    $pdf->Cell(15, 10, iconv('UTF-8', 'cp874', Yii::t('subjects', 'Address') . ' : '));
    $pdf->SetFont('THSarabunNew', '', 16);
    $pdf->setXY(22, 43);
    $pdf->MultiCell(100, 5, iconv('UTF-8', 'cp874', $address), null, 'LB');

    $pdf->SetFont('THSarabunNew', 'B', 16);
    $pdf->setX(10);
    $pdf->Cell(22, 5, iconv('UTF-8', 'cp874', Yii::t('subjects', 'Protocol') . ' : '));
    $pdf->SetFont('THSarabunNew', '', 16);
    $pdf->MultiCell(100, 5, iconv('UTF-8', 'cp874', $projectData['projectname']));

    $pdf->SetFont('THSarabunNew', 'B', 16);
    $pdf->setX(10);
    $pdf->Cell(25, 5, iconv('UTF-8', 'cp874', Yii::t('subjects', 'Protocol No') . ' : '));
    $pdf->SetFont('THSarabunNew', '', 16);
    $pdf->MultiCell(100, 5, iconv('UTF-8', 'cp874', $projectData['projectacronym']));

    $pdf->SetFont('THSarabunNew', 'B', 16);
    $pdf->setX(10);
    $pdf->Cell(27, 5, iconv('UTF-8', 'cp874', Yii::t('subjects', 'Site No') . ' : '));
    $pdf->SetFont('THSarabunNew', '', 16);
   $pdf->MultiCell(100, 5, iconv('UTF-8', 'cp874', $projectData['sitecode']));

}

$pdf->Ln(10);
$pdf->SetFont('THSarabunNew', 'B', 45);
$pdf->setX(10);
$pdf->Cell(40, 5, iconv('UTF-8', 'cp874', Yii::t('subjects', $contenHeader['title'])));
//=================================================

// Content body
$pdf->SetFont('THSarabunNew', '', 16);
$pdf->Ln(15);
$pdf->FancyTable($header, $data);
$pdf->Ln();

$pdf->SetFont('THSarabunNew', 'B', 16);
$pdf->SetX(150);
$pdf->Cell(40, 32, iconv('UTF-8', 'cp874','('.$projectData['pi_name'].')' ),0,null,'C');
$pdf->setX(150);
$pdf->Cell(40, 43, iconv('UTF-8', 'cp874', Yii::t('subjects', 'Principal Investigator') ),0,null,'C');

$idfile = strtotime(date('his')) . '-' . date('d') . date('m') . date('Y');

$pdf->Output(Yii::getAlias('@backend/web/print/').'Invoice' . $idfile . ".pdf", "F");
unlink(TEMPIMGLOC);
echo "<meta http-equiv='refresh' content='0;url=".Yii::getAlias('@web/print/'.'Invoice'. $idfile.'.pdf' ) . "'>";