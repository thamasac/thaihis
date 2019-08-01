<?php

use backend\modules\subjects\classes\SubjectManagementQuery;
use backend\modules\ezforms2\classes\EzfQuery;

const TEMPIMGLOC = 'tempimage.png';
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$val_prop = [];


$url_curr = \cpn\chanpan\classes\CNServerConfig::getDomainName();
//$url_curr = "capt.work.ncrc.in.th";
$projectData = backend\modules\subjects\classes\ReportQuery::getProjectData($url_curr);
$userdata = EzfQuery::getUserProfile(Yii::$app->user->id);

if (isset($projectData['projecticon']) && $projectData['projecticon'] != '') {
    $logo = Yii::getAlias('@storageUrl') . '/ezform/fileinput/' . $projectData['projecticon'];
    $pic = $projectData['projecticon'];
    $img = explode(',', $pic);
    $log = base64_decode($img[1]);
    if ($log !== false) {
        if (file_put_contents(TEMPIMGLOC, $log) !== false) {
            $logo = TEMPIMGLOC;
        }
    }

} else {
    $logo = Yii::getAlias('@backendUrl') . "/img/health-icon.png";
}

$pdf = new \common\lib\phppdf\InvoicePDF();

$pdf->AddFont('THSarabunNew', '', 'THSarabunNew.php');
$pdf->AddFont('THSarabunNew', 'B', 'THSarabunNew_b.php');
$pdf->SetFont('THSarabunNew', '', 16);

$pdf->AddPage();

// Content Header Right column



$pdf->setXY(0, 0);
$pdf->SetFillColor(183,191,198);
$pdf->SetTextColor(254);
$pdf->SetFont('THSarabunNew', 'B', 40);
$pdf->setXY(0, 0);
$pdf->Cell(250, 38, iconv('UTF-8', 'cp874', '         '.Yii::t('subjects', $contenHeader['title'])), 0, 1, 'L', true);

$pdf->SetFont('THSarabunNew', 'B', 30);
$pdf->setXY(135, 0);
$pdf->SetFillColor(132,149,162);
$pdf->Cell(75, 38,iconv('UTF-8', 'cp874', $total_invoice.' ('.Yii::t('subjects', 'Baht').')'), 0, 1, 'C', true);


$pdf->SetFont('THSarabunNew', 'B', 14);
$pdf->setXY(135, 11);
$pdf->Cell(75, 5,iconv('UTF-8', 'cp874', Yii::t('subjects', 'Amount')), 0, 1, 'C', true);


$pdf->Image($logo, 12, 12, 15);
$pdf->SetTextColor(0);


// Content Header Left column
if (Yii::t('subjects', 'Check Language') == 'Eng') {
    $pdf->setXY(135, 48);
    $pdf->SetFont('THSarabunNew', 'B', 14);
    $pdf->SetTextColor(137,149,162);
    $pdf->Cell(22, 5, iconv('UTF-8', 'cp874', Yii::t('subjects', 'Protocol') . ''));

    $pdf->setXY(135, 53);
    $pdf->SetFont('THSarabunNew', 'B', 14);
    $pdf->SetTextColor(0);
    $pdf->MultiCell(70, 4, iconv('UTF-8', 'cp874', $projectData['projectname']),0,'LB');

    $pdf->SetFont('THSarabunNew', 'B', 14);
    $pdf->setXY(135, 70.5);
    $pdf->SetTextColor(137,149,162);
    $pdf->Cell(25, 5, iconv('UTF-8', 'cp874', Yii::t('subjects', 'Protocol No') . ''));

    $pdf->setXY(135, 75);
    $pdf->SetTextColor(0);
    $pdf->SetFont('THSarabunNew', 'B', 14);
    $pdf->MultiCell(70, 5, iconv('UTF-8', 'cp874', $projectData['projectacronym']));

    $pdf->SetFont('THSarabunNew', 'B', 14);
    $pdf->setXY(135, 81);
    $pdf->SetTextColor(137,149,162);
    $pdf->Cell(27, 5, iconv('UTF-8', 'cp874', Yii::t('subjects', 'Site No') . ''));

    $pdf->SetFont('THSarabunNew', 'B', 14);
    $pdf->setXY(135, 85);
    $pdf->SetTextColor(0);
    $pdf->MultiCell(70, 5, iconv('UTF-8', 'cp874', $projectData['sitecode']));



    $pdf->SetFont('THSarabunNew', 'B', 14);
    $pdf->setXY(15, 48);
    $pdf->SetTextColor(137,149,162);
    $pdf->Cell(15, 5, iconv('UTF-8', 'cp874', Yii::t('subjects', 'Sponsor Name')));

    $pdf->setXY(15, 53);
    $pdf->SetTextColor(0);
    $pdf->SetFont('THSarabunNew', 'B', 14);
    $pdf->Cell(75, 5, iconv('UTF-8', 'cp874', $customer_name));

    $pdf->SetFont('THSarabunNew', 'B', 14);
    $pdf->setXY(15, 60);
    $pdf->SetTextColor(137,149,162);
    $pdf->Cell(15, 5, iconv('UTF-8', 'cp874', Yii::t('subjects', 'Address') ));

    $pdf->setXY(15, 65.5);
    $pdf->SetTextColor(0);
    $pdf->SetFont('THSarabunNew', 'B', 14);
    $pdf->MultiCell(85, 4, iconv('UTF-8', 'cp874', $address), null, 'LB');

    $pdf->SetFont('THSarabunNew', 'B', 14);
    $pdf->setXY(15, 81);
    $pdf->SetTextColor(137,149,162);
    $pdf->Cell(15, 5, iconv('UTF-8', 'cp874',Yii::t('subjects', 'Date')  ));

    $pdf->setXY(15, 85);
    $pdf->SetTextColor(0);
    $pdf->SetFont('THSarabunNew', 'B', 14);
    $pdf->MultiCell(70, 5, iconv('UTF-8', 'cp874', SubjectManagementQuery::convertDate(date('Y-m-d'))));


} else {
    $pdf->setXY(135, 48);
    $pdf->SetFont('THSarabunNew', 'B', 14);
    $pdf->SetTextColor(137,149,162);
    $pdf->Cell(22, 5, iconv('UTF-8', 'cp874', Yii::t('subjects', 'Protocol') . ''));

    $pdf->setXY(135, 53);
    $pdf->SetFont('THSarabunNew', 'B', 14);
    $pdf->SetTextColor(0);
    $pdf->MultiCell(70, 4, iconv('UTF-8', 'cp874', $projectData['projectname']),0,'LB');

    $pdf->SetFont('THSarabunNew', 'B', 14);
    $pdf->setXY(135, 70.5);
    $pdf->SetTextColor(137,149,162);
    $pdf->Cell(25, 5, iconv('UTF-8', 'cp874', Yii::t('subjects', 'Protocol No') . ''));

    $pdf->setXY(135, 75);
    $pdf->SetTextColor(0);
    $pdf->SetFont('THSarabunNew', 'B', 14);
    $pdf->MultiCell(70, 5, iconv('UTF-8', 'cp874', $projectData['projectacronym']));

    $pdf->SetFont('THSarabunNew', 'B', 14);
    $pdf->setXY(135, 81);
    $pdf->SetTextColor(137,149,162);
    $pdf->Cell(27, 5, iconv('UTF-8', 'cp874', Yii::t('subjects', 'Site No') . ''));

    $pdf->SetFont('THSarabunNew', 'B', 14);
    $pdf->setXY(135, 85);
    $pdf->SetTextColor(0);
    $pdf->MultiCell(70, 5, iconv('UTF-8', 'cp874', $projectData['sitecode']));



    $pdf->SetFont('THSarabunNew', 'B', 14);
    $pdf->setXY(15, 48);
    $pdf->SetTextColor(137,149,162);
    $pdf->Cell(15, 5, iconv('UTF-8', 'cp874', Yii::t('subjects', 'Sponsor Name')));

    $pdf->setXY(15, 53);
    $pdf->SetTextColor(0);
    $pdf->SetFont('THSarabunNew', 'B', 14);
    $pdf->Cell(75, 5, iconv('UTF-8', 'cp874', $customer_name));

    $pdf->SetFont('THSarabunNew', 'B', 14);
    $pdf->setXY(15, 60);
    $pdf->SetTextColor(137,149,162);
    $pdf->Cell(15, 5, iconv('UTF-8', 'cp874', Yii::t('subjects', 'Address') ));

    $pdf->setXY(15, 65.5);
    $pdf->SetTextColor(0);
    $pdf->SetFont('THSarabunNew', 'B', 14);
    $pdf->MultiCell(85, 4, iconv('UTF-8', 'cp874', $address), null, 'LB');

    $pdf->SetFont('THSarabunNew', 'B', 14);
    $pdf->setXY(15, 81);
    $pdf->SetTextColor(137,149,162);
    $pdf->Cell(15, 5, iconv('UTF-8', 'cp874',Yii::t('subjects', 'Date')  ));

    $pdf->setXY(15, 85);
    $pdf->SetTextColor(0);
    $pdf->SetFont('THSarabunNew', 'B', 14);
    $pdf->MultiCell(70, 5, iconv('UTF-8', 'cp874', SubjectManagementQuery::convertDate(date('Y-m-d'))));

}
$pdf->Ln(10);
$pdf->SetFont('THSarabunNew', 'B', 45);

//=================================================
// Content body
$pdf->SetFont('THSarabunNew', '', 16);
$pdf->Ln(5);
$pdf->FancyTable($header, $data);
$pdf->Ln();

$pdf->SetFont('THSarabunNew', 'B', 16);
$pdf->SetX(120);
$pdf->Cell(40, 35, iconv('UTF-8', 'cp874', Yii::t('subjects', 'Sign') . '................................................' . Yii::t('subjects', 'RECEIVED BY')));
$pdf->setX(128);
$pdf->Cell(80, 45, iconv('UTF-8', 'cp874', '(' . ($received_by != '' ? $received_by : '...............................................') . ')'));

$pdf->setX(120);
$pdf->Cell(40, 70, iconv('UTF-8', 'cp874', Yii::t('subjects', 'Sign') . '.................................................' . Yii::t('subjects', 'PAID BY')));
$pdf->setX(128);
$pdf->Cell(40, 80, iconv('UTF-8', 'cp874', '(' . ($paid_by != '' ? $paid_by : '...............................................') . ')'), 0, 0, 'L');

$idfile = strtotime(date('his')) . '-' . date('d') . date('m') . date('Y');

$pdf->Output(Yii::getAlias('@backend/web/print/') . $subject_id . '-Invoice' . $idfile . ".pdf", "F");
unlink(TEMPIMGLOC);
echo "<meta http-equiv='refresh' content='0;url=" . Yii::getAlias('@web/print/' . $subject_id . '-Invoice' . $idfile . '.pdf') . "'>";
