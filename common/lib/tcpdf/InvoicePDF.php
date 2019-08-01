<?php

namespace common\lib\tcpdf;

/**
 * SDPDF class file UTF-8
 * @author SDII <iencoded@gmail.com>
 * @link http://www.appxq.com/
 * @copyright Copyright &copy; Error: on line 6, column 34 in Templates/Scripting/PHPClass.php
  The string doesn't match the expected date/time format. The string to parse was: "17 เม.ย. 2557". The expected format was: "MMM d, yyyy". AppXQ
 * @license http://www.appxq.com/license/
 * @package 
 * @version 1.0.0 Date: 17 เม.ย. 2557 10:17:36
 */
/**
 * @abstract This Component Class is created to access TCPDF plugin for generating reports.
 * @example You can refer http://www.tcpdf.org/examples/example_011.phps for more details for this example.
 * @todo you can extend tcpdf class method according to your need here. You can refer http://www.tcpdf.org/examples.php section for 
 *       More working examples.
 * @version 1.0.0
 */
require_once(dirname(__FILE__) . '/tcpdf/tcpdf.php');

use appxq\sdii\utils\SDdate;
use TCPDF;

//PDF_UNIT = pt , mm , cm , in 
//PDF_PAGE_ORIENTATION = P , LANDSCAPE = L
//PDF_PAGE_FORMAT 4A0,2A0,A0,A1,A2,A3,A4,A5,A6,A7,A8,A9,A10,B0,B1,B2,B3,B4,B5,B6,B7,B8,B9,B10,C0,C1,C2,C3,C4,C5,C6,C7,C8,C9,C10,RA0,RA1,RA2,RA3,RA4,SRA0,SRA1,SRA2,SRA3,SRA4,LETTER,LEGAL,EXECUTIVE,FOLIO
class InvoicePDF extends TCPDF {

    public $fontName = 'thsarabunpsk';
    public $fontSize = 14;
    public $spaceHigh = 7;
    public $spaceWidth = 5;
    public $templateHeader = 'simple';
    public $tableHeader;
    public $tableBody;
    public $contentHeader;
    public $lineFooter = false;
    public $bacgroundImage = '';
    public $dataHeader = 0;
    public $countDetail;
    protected $last_page_flag = false;

    public function Close() {
        $this->last_page_flag = true;
        parent::Close();
    }

    //Page header
    public function Header() {
        //$this->SetY(PDF_MARGIN_HEADER);

        $this->SetFillColor(196, 196, 196); //สีพื้น
        $this->SetTextColor(0, 0, 0); //สีตัวหนังสือ
        $this->SetDrawColor(0, 0, 0); //สีเส้นขอบ
        $this->SetLineWidth(0.1); //ขนาดเส้นขอบ

        $this->SetFont($this->fontName, 'B', $this->fontSize + 4);

        $this->Cell(0, $this->spaceHigh + 1, $this->contentHeader['title'], 0, 1, 'C', 0, '', 0, false, 'T', 'M');

        $this->SetFont($this->fontName, '', $this->fontSize);
        $printDate = 'พิมพ์วันที่ ' . SDdate::mysql2phpThDateTime(date("Y-m-d H:i:s"));

        $titleBy = '';
        if (isset($this->contentHeader['by'])) {
            $titleBy = $this->contentHeader['by'];
        }

        $this->Cell(40, $this->spaceHigh, $titleBy, 0, 0, 'L', 0, '', 0, false, 'T', 'M');

        $this->SetFont($this->fontName, '', $this->fontSize - 2);
        $this->Cell(0, $this->spaceHigh, $printDate, 0, 1, 'R', 0, '', 0, false, 'T', 'M');
        
        $this->SetFont($this->fontName, 'B', $this->fontSize);
        foreach ($this->tableHeader as $key => $value) {
            $align = 'C';
            if (isset($value['align']) && !empty($value['align'])) {
                $align = $value['align'];
            }
            $txt = $value['txt'];
            if ($align == 'L') {
                $txt = '  ' . $txt;
            } elseif ($align == 'R') {
                $txt = $txt . '  ';
            }
           $this->Cell($value['w'], $this->spaceHigh, $txt, 1, 0, $align, 1, '', 0, false, 'T', 'M');
        }
    }

    // Page footer
    public function Footer() {

        // Position at 15 mm from bottom
        if ($this->page == 1) {
            $this->SetY(-17);
        } else {
            if ($this->templateHeader == 'simple') {
                $this->SetY(-18);
            } else {
                $this->SetY(-19);
            }
        }

        $lastPage = $this->getAliasNbPages();
        if ($this->lineFooter && !$this->last_page_flag) {
            $this->Cell(0, 0, '', 'T');
        } else {
            $this->Cell(0, 0, '', 0);
        }
        // Set font
        $this->SetFont($this->fontName, '', 11);
        $this->SetTextColor(80, 80, 80);
        // Page number
        $this->Cell(0, $this->spaceHigh, 'หน้าที่ ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }

    // Colored table
    public function createTableData($header, $data) {

        $sumCol = true; $margin_top = 32; $fullsize = true; $h = 7;
        $this->SetY($margin_top);

        // Colors, line width and bold font
        $this->SetFillColor(196, 196, 196); //สีพื้น
        $this->SetTextColor(0, 0, 0); //สีตัวหนังสือ
        $this->SetDrawColor(0, 0, 0); //สีเส้นขอบ
        $this->SetLineWidth(0.1); //ขนาดเส้นขอบ
        $this->SetFont($this->fontName, 'B', $this->fontSize);

        // Header
        $w = array();
        $a = array();
        $n = array();
        $t = array();

        foreach ($header as $key => $value) {
            $align = 'C';
            if (isset($value['align']) && !empty($value['align'])) {
                $align = $value['align'];
            }
            $w[] = $value['w'];
            $a[] = $align;
            $n[] = $value['name'];
            $t[] = isset($value['type']) ? $value['type'] : '';
            //$this->Cell($value['w'], $this->spaceHigh, $value['txt'], 1, 0, $align, 1, '', 0, false, 'T', 'M');
        }

        //$this->Ln();
        // Color and font restoration
        $this->SetFillColor(240, 240, 240);
        $this->SetTextColor(0);
        $this->SetFont('');

        // Data
        $fill = 0;
        $sum = array();
        //for ($x=0;$x<20;$x++){
        foreach ($data as $key => $row) {
            foreach ($n as $i => $field) {
                $txt = '';
                if ($field === 'orderID') {
                    $txt = $key + 1;
                } else {
                    if ($t[$i] == 'number') {
                        $sumTmp = isset($sum[$i]) ? $sum[$i] : 0;
                        $sum[$i] = $sumTmp + $row[$field];
                        $txt = number_format($row[$field], 2);
                    } else {
                        $txt = $row[$field];
                    }
                }

                if ($a[$i] == 'L') {
                    $txt = ' ' . $txt;
                } elseif ($a[$i] == 'R') {
                    $txt = $txt . ' ';
                }
                
                echo $this->Cell($w[$i], $this->spaceHigh, $txt, 'LR', 0, $a[$i], $fill, '', 0, false, 'T', 'M');
            }
            $this->Ln();
            $fill = !$fill;
        }
        //}
        // Sum
        $endLine = 0;
        if (!$fullsize) {
            $endLine = array_sum($w);
        }

        if ($sumCol) {
            $sumW = 0;
            foreach ($n as $i => $field) {
                if (isset($sum[$i])) {
                    if ($sumW > 0) {
                        $this->Cell($sumW, $this->spaceHigh, 'รวม', 'LTB', 0, 'R');
                        $sumW = 0;
                    }
                    $sumNum = number_format($sum[$i], 2);
                    if ($a[$i] == 'L') {
                        $sumNum = ' ' . $sumNum;
                    } elseif ($a[$i] == 'R') {
                        $sumNum = $sumNum . ' ';
                    }
                    $this->Cell($w[$i], $this->spaceHigh, $sumNum, 1, 0, $a[$i], 0, '', 0, false, 'T', 'M');
                    continue;
                }

                $sumW = $sumW + $w[$i];
            }
        } else {
            $this->Cell($endLine, 0, '', 'T');
        }
    }
    
    public function Output($name = 'doc.pdf', $dest = 'I') {
        parent::Output("@web/pdf/".$name, $dest);
    }

}

?>
