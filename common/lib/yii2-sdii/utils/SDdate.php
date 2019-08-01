<?php
namespace appxq\sdii\utils;

use Yii;
use DateTime;
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SDdate
 *
 * @author SDII
 */
class SDdate {
	public static $thaiweek=array("อา","จ","อ","พ","พฤ","ศ","ส");
	public static $thaiweekFull=array("วันอาทิตย์","วันจันทร์","วันอังคาร","วันพุธ","วันพฤหัสบดี","วันศุกร์","วันเสาร์");
	public static $thaimonthFull=array("มกราคม","กุมภาพันธ์","มีนาคม","เมษายน","พฤษภาคม","มิถุนายน","กรกฎาคม","สิงหาคม","กันยายน","ตุลาคม", "พฤศจิกายน","ธันวาคม");
	public static $thaimonth=array("ม.ค.","ก.พ.","มี.ค.","เม.ย.","พ.ค.","มิ.ย.","ก.ค.","ส.ค.","ก.ย.","ต.ค.", "พ.ย.","ธ.ค.");
	
        public static function getAgeMysqlDate($sqlDate) {
		//calculate years of age (input string: YYYY-MM-DD)
		//$dob = substr($sqlDate, 0, 4).'-'.substr($sqlDate, 4, 2).'-'.substr($sqlDate, 6, 2);
		/*list($year, $month, $day) = explode("-", $dob);

		$year_diff  = date("Y") - $year;
		$month_diff = date("m") - $month;
		$day_diff   = date("d") - $day;

		if ($day_diff < 0 || $month_diff < 0)
			$year_diff--;*/

		//return $year_diff;
                $year_diff = '';
                try {
                   $year_diff = date_diff(date_create($sqlDate), date_create('now'))->y;
                } catch (\Exception $e) {
                    \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
                }
                
                return $year_diff;
	}
        
	public static function getAge($sqlDate) {
                try {
                   //calculate years of age (input string: YYYY-MM-DD)
                    $dob = substr($sqlDate, 0, 4).'-'.substr($sqlDate, 4, 2).'-'.substr($sqlDate, 6, 2);
                    /*list($year, $month, $day) = explode("-", $dob);

                    $year_diff  = date("Y") - $year;
                    $month_diff = date("m") - $month;
                    $day_diff   = date("d") - $day;

                    if ($day_diff < 0 || $month_diff < 0)
                            $year_diff--;*/

                    //return $year_diff;

                    return date_diff(date_create($dob), date_create('now'))->y;
                } catch (\Exception $e) {
                    \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
                    return '';
                }
		
	}
	
	public static function getDiffAge($sqlDate, $start, $end) {
		//calculate years of age (input string: YYYY-MM-DD)
		$dob = substr($sqlDate, 0, 4).'-'.substr($sqlDate, 4, 2).'-'.substr($sqlDate, 6, 2);
		list($year, $month, $day) = explode("-", $dob);

		$year_diff  = date("Y") - $year;
		$month_diff = date("m") - $month;
		$day_diff   = date("d") - $day;

		if ($day_diff < 0 || $month_diff < 0)
			$year_diff--;

		if($year_diff>=$start && $year_diff<=$end){
		    return true;
		}
		return false;
	}

	public static function bod2dateTh($sqlDate){
		if($sqlDate==''){
			return '';
		}
		//yyyymmdd To dd/mm/yyyy
		return substr($sqlDate, 6, 2).'/'.substr($sqlDate, 4, 2).'/'.  SDdate::yearEn2yearTh(substr($sqlDate, 0, 4));
	}
	public static function dateTh2bod($phpDate){
                try {
                   if(isset($phpDate) && $phpDate==''){
                            return '';
                    }
                    //dd/mm/yyyy To yyyymmdd
                    $arr = explode('/', $phpDate);
                    return SDdate::yearTh2yearEn($arr[2]).$arr[1].$arr[0];
                } catch (\Exception $e) {
                    \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
                    return '';
                }
		
	}
	
	public static function yearTh2yearEn($yTh){
		return $yTh-543;
	}
	public static function yearEn2yearTh($yEn){
		return $yEn+543;
	}
	
	public static function mysql2phpDate($sqlDate, $delimiter='/'){
		$arr = date_parse($sqlDate);
                if(!isset($arr["year"]) || !isset($arr["month"]) || !isset($arr["day"]) || $arr["year"]=='' || $arr["month"]=='' || $arr["day"]==''){
                    return '';
                }
		$date = new DateTime($arr["year"].'-'.$arr["month"].'-'.$arr["day"]);
		return $date->format("d{$delimiter}m{$delimiter}Y");
	}
	
	public static function mysql2phpDateTime($sqlDate){
		$strArr = explode(' ', $sqlDate);
		$arr = date_parse($sqlDate);
                if(!isset($arr["year"]) || !isset($arr["month"]) || !isset($arr["day"]) || $arr["year"]=='' || $arr["month"]=='' || $arr["day"]==''){
                    return '';
                }
		$time = (isset($strArr[1]))?' '.$strArr[1]:'';
		$date = new DateTime($arr["year"].'-'.$arr["month"].'-'.$arr["day"].' '.$time);
		return $date->format('d/m/Y H:i');
	}
	
	public static function mysql2phpTime($sqlDate){
		$arr = explode(' ', $sqlDate);
		
		return $arr[1];
	}
	
	public static function mysql2phpThDate($sqlDate){
		$arr = date_parse($sqlDate);
                if(!isset($arr["year"]) || !isset($arr["month"]) || !isset($arr["day"]) || $arr["year"]=='' || $arr["month"]=='' || $arr["day"]==''){
                    return '';
                }
		$date = new DateTime($arr["year"].'-'.$arr["month"].'-'.$arr["day"]);
		return SDdate::thFormatDate($date);
	}
	
	public static function mysql2phpThDateSmall($sqlDate, $mini=true){
		$arr = date_parse($sqlDate);
		$date = new DateTime($arr["year"].'-'.$arr["month"].'-'.$arr["day"]);
		return SDdate::thFormatDateSmall($date, $mini);
	}
	
	public static function mysql2phpThDateTime($sqlDate, $mini=true){
		$arr = explode(' ', $sqlDate);
		$date = new DateTime($arr[0]);
		return SDdate::thFormatDateSmall($date, $mini).' '.  substr($arr[1], 0, 5);
	}
	
	public static function mysql2phpThDateTimeFull($sqlDate){
		$arr = explode(' ', $sqlDate);
		$date = new DateTime($arr[0]);
		return SDdate::thFormatDate($date, true).' เวลา '.  substr($arr[1], 0, 5);
	}
	
	public static function thFormatDate($stDate){
		$strDate = SDdate::$thaiweekFull[$stDate->format('w')].' '.$stDate->format('j').' '.SDdate::$thaimonthFull[$stDate->format('m')-1].' '.($stDate->format('Y')+543);
		return $strDate;
	}
	public static function thFormatDateSmall($stDate, $mini=FALSE){
		$strDate = ($mini)?$stDate->format('j').' '.SDdate::$thaimonth[$stDate->format('m')-1].' '.($stDate->format('Y')+543):$stDate->format('j').' '.SDdate::$thaimonthFull[$stDate->format('m')-1].' '.($stDate->format('Y')+543);
		return $strDate;
	}
	
	public static function php2MySqlTime($phpDate) {
		return date("Y-m-d H:i:s", $phpDate);
	}
	
	public static function phpThDate2mysql($phpDate, $delimiter='/'){
		$arrType = explode(' ', $phpDate);
		$arr = explode($delimiter, $arrType[0]);
		$time = (isset($arrType[1]))?' '.$arrType[1]:'';
		$date = new DateTime($arr[2].'-'.$arr[1].'-'.$arr[0].$time);
		return $date->format('Y-m-d H:i:s');
	}
	
	public static function phpThDate2mysqlDate($phpDate, $delimiter='/'){
		$arr = explode($delimiter, $phpDate);
		$date = new DateTime($arr[2].'-'.$arr[1].'-'.$arr[0]);
		return $date->format('Y-m-d');
	}
	
	public static function mysql2phpTimestamp($sqlDate){
		$arr = date_parse($sqlDate);
		$time = mktime($arr["hour"], $arr["minute"], $arr["second"], $arr["month"], $arr["day"], $arr["year"]);
		return $time;
	}
	
	public static function differenceTimer($sqlDate) {
		$arr = date_parse($sqlDate);
		
		$earlierDate = new DateTime($arr['year'].'-'.$arr['month'].'-'.$arr['day'].' '.$arr['hour'].':'.$arr['minute'].':'.$arr['second']);
		$laterDate = new DateTime();
		
		$vTime = SDdate::get_time_difference($earlierDate, $laterDate);
		
		$strTime = '';
		if($vTime['days'] > 0){
			$strTime = $vTime['days'] . ' '.Yii::t('app', 'days ago');
		}
		else if($vTime['hours'] > 0){
			$strTime = $vTime['hours'] . ' '.Yii::t('app', 'hours ago');
		}
		else if($vTime['minutes'] > 0){
			$strTime = $vTime['minutes'] . ' '.Yii::t('app', 'minutes ago');
		}
		else {
			$strTime = Yii::t('app', 'a few seconds ago');//'เมื่อไม่กี่วินาทีที่แล้ว'
		}
	
		return $strTime;
	}
	
	public static function differenceTimerBot($sqlDate) {
		$arr = date_parse($sqlDate);
		
		$earlierDate = new DateTime($arr['year'].'-'.$arr['month'].'-'.$arr['day'].' '.$arr['hour'].':'.$arr['minute'].':'.$arr['second']);
		$laterDate = new DateTime();
		
		$vTime = SDdate::get_time_difference($earlierDate, $laterDate);
		
		$strTime = 0;
		if($vTime['days'] > 0){
			$strTime = 2;
		}
		else if($vTime['hours'] > 0){
			$strTime = 2;
		}
		else if($vTime['minutes'] > 5){
			$strTime = 2;
		}
		else if($vTime['minutes'] < 1){
			$strTime = 0;
		}
		else if($vTime['minutes'] >= 1 && $vTime['minutes'] <= 5){
			$strTime = 1;
		}
		
		return $strTime;
	}
	
	public static function get_time_difference($earlierDate, $laterDate)
	{
		$nTotalDiff = $laterDate->getTimestamp() - $earlierDate->getTimestamp();
		$oDiff = array();

		$oDiff['days'] = floor($nTotalDiff/60/60/24);
		$nTotalDiff -= $oDiff['days']*60*60*24;
		
		$oDiff['hours'] = floor($nTotalDiff/60/60);
		$nTotalDiff -= $oDiff['hours']*60*60;
		
		$oDiff['minutes'] = floor($nTotalDiff/60);
		$nTotalDiff -= $oDiff['minutes']*60;

		$oDiff['seconds'] = floor($nTotalDiff);

		return $oDiff;

	}
	
	public static function getPrettyTime($sqlDate)
	{
		$arr = date_parse($sqlDate);
		$earlierDate = new \DateTime($arr['year'].'-'.$arr['month'].'-'.$arr['day'].' '.$arr['hour'].':'.$arr['minute'].':'.$arr['second']);
		$laterDate = new \DateTime();
		
		$vTime = SDdate::get_time_difference($earlierDate, $laterDate);

		$strTime = '';
		if($vTime['days'] > 0){
			$strTime .= 'd'. $vTime['days'] . ' ';
		} 
		if($vTime['hours'] > 0){
			$strTime .= 'hr'. $vTime['hours'] . ' ';
		} 
		if($vTime['minutes'] > 0){
			$strTime .= 'm'. $vTime['minutes'] . ' ';
		} 
		if($vTime['days'] == 0 && $vTime['hours'] == 0 && $vTime['minutes'] == 0){
			$strTime = 's'. $vTime['seconds'] . ' ';
		}
		
		return $strTime;

	}
	
	public static function getDiffDay($sqlDate, $day=30)
	{
		$arr = date_parse($sqlDate);
		$earlierDate = new DateTime($arr['year'].'-'.$arr['month'].'-'.$arr['day'].' '.$arr['hour'].':'.$arr['minute'].':'.$arr['second']);
		$laterDate = new DateTime();
		
		$vTime = SDdate::get_time_difference($earlierDate, $laterDate);

		$strTime = 0;
		if($vTime['days'] > 0){
			$strTime = $vTime['days'];
		} 
		
		$limit = $day - $strTime;
		
		return $limit>0?"เหลือเวลาอีก $limit วัน":'หมดเวลาแล้ว';

	}
	
	public static function getCheckDiffDay($sqlDate, $day=30)
	{
		$arr = date_parse($sqlDate);
		$earlierDate = new DateTime($arr['year'].'-'.$arr['month'].'-'.$arr['day'].' '.$arr['hour'].':'.$arr['minute'].':'.$arr['second']);
		$laterDate = new DateTime();
		
		$vTime = SDdate::get_time_difference($earlierDate, $laterDate);

		$strTime = 0;
		if($vTime['days'] > 0){
			$strTime = $vTime['days'];
		} 
		
		$limit = $day - $strTime;
		
		return $limit>0?true:false;

	}
}

?>
