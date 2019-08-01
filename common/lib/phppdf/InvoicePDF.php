<?php

namespace common\lib\phppdf;

require('fpdf.php');
$rep = isset($_GET['rep'])?$_GET['rep']:'';
$start_date = isset($_GET['start_date'])?$_GET['start_date']:'';
$end_date = isset($_GET['end_date'])?$_GET['end_date']:'';

class InvoicePDF extends \FPDF {

//Load data
    function LoadData($file) {
        //Read file lines
        $lines = file($file);
        $data = array();
        foreach ($lines as $line)
            $data[] = explode(';', chop($line));
        return $data;
    }

//Simple table
    function BasicTable($header, $data) {
        //Header
        $w = array(30, 30, 55, 25, 20, 20);
        //Header
        for ($i = 0; $i < count($header); $i++)
            $this->Cell($w[$i], 7, $header[$i], 1, 0, 'C');
        $this->Ln();
        //Data
        foreach ($data as $eachResult) {
            $this->Cell(30, 6, $eachResult["CustomerID"], 1);
            $this->Cell(30, 6, $eachResult["Name"], 1);
            $this->Cell(55, 6, $eachResult["Email"], 1);
            $this->Cell(25, 6, $eachResult["CountryCode"], 1, 0, 'C');
            $this->Cell(20, 6, $eachResult["Budget"], 1);
            $this->Cell(20, 6, $eachResult["Budget"], 1);
            $this->Ln();
        }
    }

//Better table
    function ImprovedTable($header, $data) {
        //Column widths
        $w = array(20, 30, 55, 25, 25, 25);
        //Header
        for ($i = 0; $i < count($header); $i++)
            $this->Cell($w[$i], 7, $header[$i], 1, 0, 'C');
        $this->Ln();
        //Data

        foreach ($data as $eachResult) {
            $this->Cell(20, 6, $eachResult["CustomerID"], 1);
            $this->Cell(30, 6, $eachResult["Name"], 1);
            $this->Cell(55, 6, $eachResult["Email"], 1);
            $this->Cell(25, 6, $eachResult["CountryCode"], 1, 0, 'C');
            $this->Cell(25, 6, number_format($eachResult["Budget"], 2), 1, 0, 'R');
            $this->Cell(25, 6, number_format($eachResult["Budget"], 2), 1, 0, 'R');
            $this->Ln();
        }

        //Closure line
        $this->Cell(array_sum($w), 0, '', 'T');
    }

//Colored table
    function FancyTable($header, $data) {
        //Colors, line width and bold font
        $this->SetFillColor(191, 191, 191);
        $this->SetTextColor(0);
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(.3);
        $this->SetFont('THSarabunNew', 'B');
        //Header
        $w = array();
        foreach ($header as $key => $value) {
            $w[] = $value['w'];
            $this->Cell($value['w'], 8, iconv('UTF-8', 'cp874', $value['txt']), 1, 0, 'C', true);
        }
        $this->Ln();
        //Color and font restoration
        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(0);
        $this->SetFont('THSarabunNew');
        //Data
        $fill = false;
        $total_price = 0;
        $total_net = 0;
        $x = 1;
        foreach ($data as $key => $row) {
 
            $this->SetFont('THSarabunNew', 'B');
            if(isset($row['header']) && is_array($row['header'])){
                foreach ($row['header'] as $krow => $vrow) {
                    foreach ($header as $k => $v) {
                        $this->Cell($w[$k], 6, iconv('UTF-8', 'cp874', $k == 1 ? $vrow : ''), 'LR', 0, 'L', true);
                    }
                    $this->Ln();
                }
            }

            $this->SetFont('THSarabunNew', '', 16);
            foreach ($row['data'] as $krow => $vrow) {
                foreach ($header as $k => $v) {
                    if ($v['name'] == 'orderID') {
                        $this->Cell($w[$k], 6, iconv('UTF-8', 'cp874', $x), 'LR', 0, 'C', false);
                    } elseif ($v['type'] == 'number') {
                        $this->Cell($w[$k], 6, iconv('UTF-8', 'cp874', number_format($vrow[$k - 1])), 'LR', 0, 'R', false);
                        $total_price += $vrow[$k - 1];
                        $total_net += $vrow[$k - 1];
                    } else {
                        $this->Cell($w[$k], 6, iconv('UTF-8', 'cp874', $vrow[$k - 1]), 'LR', 0, 'L', false);
                    }
                }
                $x++;
                $this->Ln();
                $fill = !$fill;
            }
        }
        foreach ($header as $k => $v) {
            if ($v['name'] == 'orderID') {
                $this->Cell($w[$k], 6, iconv('UTF-8', 'cp874', ''), 'LR', 0, 'C', true);
            } elseif ($v['name'] == 'amount') {
                $this->Cell($w[$k], 6, iconv('UTF-8', 'cp874', number_format($total_price)), 'LR', 0, 'R', true);
            } elseif ($v['name'] == 'items') {
                $this->SetFont('THSarabunNew', 'B');
                $this->Cell($w[$k], 6, iconv('UTF-8', 'cp874', \Yii::t('subjects', \Yii::t('subjects', 'Total'))), 'LR', 0, 'R', true);
            } else {
                $this->Cell($w[$k], 6, iconv('UTF-8', 'cp874', ''), 'LR', 0, 'C', true);
            }
        }

        $this->Ln();
        $this->Cell(array_sum($w), 0, '', 'T');
        $this->Ln();
        $this->Ln();
        if (\Yii::t('subjects', 'Check Language') == 'Eng') {
            $numberStr = \backend\modules\subjects\classes\ReportQuery::engFormat($total_price);
        } else {
            $numberStr = \backend\modules\subjects\classes\ReportQuery::num2wordsThai($total_price);
        }
        $this->SetX(10);
        $this->Cell(array_sum($w), 12, iconv('UTF-8', 'cp874', \Yii::t('subjects', 'TOTAL AMOUNT').' : '.$numberStr),'LR',0,'C');
        $this->Ln();
        $this->Cell(array_sum($w), 0, '', 'T');
        $this->Ln();
    }

}

?>