<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\modules\subjects\classes;


/**
 * Description of ReportQuery
 *
 * @author Admin
 */
class ReportQuery {

    //put your code here
    public static function onRevenueReport($budgetForm) {

        $sql = " SELECT financial_type,
	section, pro_name FROM zdata_visit_procedure vp 
	INNER JOIN zdata_budget_procedure bp ON vp.procedure_name=bp.pro_name
	WHERE bp.financial_type <>'' AND bp.section <>'' 
	GROUP BY bp.pro_name";

        $result = \Yii::$app->db->createCommand($sql)->queryAll();

        return $result;
    }

    public static function getProjectData($url_curr) {
        $sql = " SELECT * FROM zdata_create_project WHERE CONCAT(projurl,'.',projdomain) = '$url_curr' ";
        $result = \Yii::$app->db_main->createCommand($sql)->queryOne();
        return $result;
    }
    
    public static function getProjectData2($url_curr) {
        $sql = " SELECT * FROM zdata_create_project WHERE projurl = '$url_curr' ";
        $result = \Yii::$app->db->createCommand($sql)->queryOne();
        return $result;
    }
    
    public static function getUserList() {
        $sql = " SELECT DISTINCT `user`.id,profile.firstname, profile.lastname,profile.avatar_path, profile.avatar_base_url  
            FROM `user` INNER JOIN profile ON `user`.id=profile.user_id WHERE `user`.id <> '1' ";
        $result = \Yii::$app->db->createCommand($sql)->queryAll();
        return $result;
    }

    public static function getSitecodeById($code) {
        $sql = "SELECT sect_code,sect_name,sect_his_type FROM const_hospital
            WHERE code='$code'";
        return Yii::$app->db->createCommand($sql)->queryOne();
    }

    public static function getProvinceNameByCode($code) {
        $sql = "SELECT sect_code,sect_name,sect_his_type FROM const_province
            WHERE PROVINCE_CODE='$code'";
        return Yii::$app->db->createCommand($sql)->queryOne();
    }

    public function bahtEng($thb) {
        list($thb, $ths) = explode('.', $thb);
        $ths = substr($ths . '00', 0, 2);
        $thb = Currency::engFormat(intval($thb)) . ' Baht';
        if (intval($ths) > 0) {
            $thb .= ' and ' . Currency::engFormat(intval($ths)) . ' Satang';
        }
        return $thb;
    }

    public static function num2wordsThai($number) {
        $txtnum1 = array('ศูนย์', 'หนึ่ง', 'สอง', 'สาม', 'สี่', 'ห้า', 'หก', 'เจ็ด', 'แปด', 'เก้า', 'สิบ');
        $txtnum2 = array('', 'สิบ', 'ร้อย', 'พัน', 'หมื่น', 'แสน', 'ล้าน', 'สิบ', 'ร้อย', 'พัน', 'หมื่น', 'แสน', 'ล้าน');
        $number = str_replace(",", "", $number);
        $number = str_replace(" ", "", $number);
        $number = str_replace("บาท", "", $number);
        $number = explode(".", $number);
        if (sizeof($number) > 2) {
            return 'ทศนิยมหลายตัวนะจ๊ะ';
            exit;
        }

        $strlen = strlen($number[0]);
        $convert = '';
        for ($i = 0; $i < $strlen; $i++) {
            $n = substr($number[0], $i, 1);
            if ($n != 0) {
                if ($i == ($strlen - 1) AND $n == 1) {
                    $convert .= 'เอ็ด';
                } elseif ($i == ($strlen - 2) AND $n == 2) {
                    $convert .= 'ยี่';
                } elseif ($i == ($strlen - 2) AND $n == 1) {
                    $convert .= '';
                } else {
                    $convert .= $txtnum1[$n];
                }
                $convert .= $txtnum2[$strlen - $i - 1];
            }
        }

        $convert .= 'บาท';
        if(isset($number[1])){
        if ($number[1] == '0' OR $number[1] == '00' OR
            $number[1] == '') {
            $convert .= 'ถ้วน';
        } else {
            $strlen = strlen($number[1]);
            for ($i = 0; $i < $strlen; $i++) {
                $n = substr($number[1], $i, 1);
                if ($n != 0) {
                    if ($i == ($strlen - 1) AND $n == 1) {
                        $convert .= 'เอ็ด';
                    } elseif ($i == ($strlen - 2) AND
                        $n == 2) {
                        $convert .= 'ยี่';
                    } elseif ($i == ($strlen - 2) AND
                        $n == 1) {
                        $convert .= '';
                    } else {
                        $convert .= $txtnum1[$n];
                    }
                    $convert .= $txtnum2[$strlen - $i - 1];
                }
            }
            $convert .= 'สตางค์';
        }
    }
        return $convert;
    }

    public static function engFormat($number) {
        if(isset($thb)){
             list($thb, $ths) = explode('.', $thb);
            $ths = substr($ths . '00', 0, 2);
        }
        $max_size = pow(10, 18);
        if (!$number)
            return "ZERO";
        if (is_int($number) && $number < abs($max_size)) {
            switch ($number) {
                case $number < 0:
                    $prefix = "NEGATIVE";
                    $suffix = ReportQuery::engFormat(-1 * $number);
                    $string = $prefix . " " . $suffix;
                    break;
                case 1:
                    $string = "ONE";
                    break;
                case 2:
                    $string = "TWO";
                    break;
                case 3:
                    $string = "THREE";
                    break;
                case 4:
                    $string = "FOUR";
                    break;
                case 5:
                    $string = "FIVE";
                    break;
                case 6:
                    $string = "SIX";
                    break;
                case 7:
                    $string = "SEVEN";
                    break;
                case 8:
                    $string = "EIGHT";
                    break;
                case 9:
                    $string = "NINE";
                    break;
                case 10:
                    $string = "TEN";
                    break;
                case 11:
                    $string = "ELEVEN";
                    break;
                case 12:
                    $string = "TWELVE";
                    break;
                case 13:
                    $string = "THIRTEEN";
                    break;
                case 15:
                    $string = "FIFTEEN";
                    break;
                case $number < 20:
                    $string = ReportQuery::engFormat($number % 10);
                    if ($number == 18) {
                        $suffix = "EEN";
                    } else {
                        $suffix = "TEEN";
                    }
                    $string .= $suffix;
                    break;
                case 20:
                    $string = "TWENTY";
                    break;
                case 30:
                    $string = "THIRTY";
                    break;
                case 40:
                    $string = "FORTY";
                    break;
                case 50:
                    $string = "FIFTY";
                    break;
                case 60:
                    $string = "SIXTY";
                    break;
                case 70:
                    $string = "SEVENTY";
                    break;
                case 80:
                    $string = "EIGHTTY";
                    break;
                case 90:
                    $string = "NINETY";
                    break;
                case $number < 100:
                    $prefix = ReportQuery::engFormat($number - $number % 10);
                    $suffix = ReportQuery::engFormat($number % 10);
                    $string = $prefix . "-" . $suffix;
                    break;
                case $number < pow(10, 3):
                    $prefix = ReportQuery::engFormat(intval(floor($number / pow(10, 2)))) . " HUNDRED";
                    if ($number % pow(10, 2))
                        $suffix = " AND " . ReportQuery::engFormat($number % pow(10, 2));
                    $string = $prefix . $suffix;
                    break;
                case $number < pow(10, 6):
                    $prefix = ReportQuery::engFormat(intval(floor($number / pow(10, 3)))) . " THOUSAND";
                    if ($number % pow(10, 3))
                        $suffix = ReportQuery::engFormat($number % pow(10, 3));
                    $string = $prefix . " " . $suffix;
                    break;
                case $number < pow(10, 9):
                    $prefix = ReportQuery::engFormat(intval(floor($number / pow(10, 6)))) . " MILLION";
                    if ($number % pow(10, 6))
                        $suffix = ReportQuery::engFormat($number % pow(10, 6));
                    $string = $prefix . " " . $suffix;
                    break;
                case $number < pow(10, 12):
                    $prefix = ReportQuery::engFormat(intval(floor($number / pow(10, 9)))) . " BILLION";
                    if ($number % pow(10, 9))
                        $suffix = ReportQuery::engFormat($number % pow(10, 9));
                    $string = $prefix . " " . $suffix;
                    break;
                case $number < pow(10, 15):
                    $prefix = ReportQuery::engFormat(intval(floor($number / pow(10, 12)))) . " TRILLION";
                    if ($number % pow(10, 12))
                        $suffix = ReportQuery::engFormat($number % pow(10, 12));
                    $string = $prefix . " " . $suffix;
                    break;
                case $number < pow(10, 18):
                    $prefix = ReportQuery::engFormat(intval(floor($number / pow(10, 15)))) . " QUADRILLION";
                    if ($number % pow(10, 15))
                        $suffix = ReportQuery::engFormat($number % pow(10, 15));
                    $string = $prefix . " " . $suffix;
                    break;
            }
        }
        return $string;
    }
    
    public static function  excelToArray($filePath, $header = true) {
        //Create excel reader after determining the file type
        $inputFileName = $filePath;
        /**  Identify the type of $inputFileName  * */
        \PHPExcel_Settings::setZipClass(\PHPExcel_Settings::PCLZIP);
        $inputFileType = \PHPExcel_IOFactory::identify($inputFileName);
        /**  Create a new Reader of the type that has been identified  * */
        $objReader = \PHPExcel_IOFactory::createReader($inputFileType);

        $objReader->setReadDataOnly(true);
        /**  Load $inputFileName to a PHPExcel Object  * */
        $objPHPExcel = $objReader->load($inputFileName);
        //Get worksheet and built array with first row as header
        $objWorksheet = $objPHPExcel->getActiveSheet();
        //excel with first row header, use header as key
        if ($header) {
            $highestRow = $objWorksheet->getHighestRow();
            $highestColumn = $objWorksheet->getHighestColumn();
            $headingsArray = $objWorksheet->rangeToArray('A1:' . $highestColumn . '1', null, true, true, true);
            $headingsArray = $headingsArray[1];
            $r = -1;
            $namedDataArray = array();
            for ($row = 2; $row <= $highestRow; ++$row) {
                $dataRow = $objWorksheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, null, true, true, true);
                if ((isset($dataRow[$row]['A'])) && ($dataRow[$row]['A'] > '')) {
                    ++$r;
                    foreach ($headingsArray as $columnKey => $columnHeading) {
                        $namedDataArray[$r][$columnHeading] = $dataRow[$row][$columnKey];
                    }
                }
            }
        } else {
            //excel sheet with no header
            $namedDataArray = $objWorksheet->toArray(null, true, true, true);
        }
        return $namedDataArray;
    }
    
    public static function day2DayWeekMonth($day){
        $weekAmt = 0;
        $dayAmt = 0;
        while($day >= 7){
            $weekAmt += 1;
            $day -= 7;
        }
        $dayAmt = $day;
        
        return $weekAmt." Weeks ".$dayAmt." Days ";
    }

}
