<?php

namespace backend\modules\restfull\classes;

use Yii;
use appxq\sdii\utils\SDdate;

/**
 * OvccaFunc class file UTF-8
 * @author SDII <iencoded@gmail.com>
 * @copyright Copyright &copy; 2015 AppXQ
 * @license http://www.appxq.com/license/
 * @version 1.0.0 Date: 9 ก.พ. 2559 12:38:14
 * @link http://www.appxq.com/
 * @example 
 */
class RestfullFunc {

    public static function toholriday($strStartDate, $strEndDate) {
        $intWorkDay = 0;
        $intHoliday = 0;
        $intTotalDay = ((strtotime($strEndDate) - strtotime($strStartDate)) / ( 60 * 60 * 24 )) + 1;
        $statuss = '';
        $arr = array();
        while (strtotime($strStartDate) <= strtotime($strEndDate)) {
            $DayOfWeek = date("w", strtotime($strStartDate));
            if ($DayOfWeek == 0 or $DayOfWeek == 6) {  // 0 = Sunday, 6 = Saturday;
                $intHoliday++;
                $statuss = 'true';
                //echo "$strStartDate = <font color=red>Holiday</font><br>";
            } else {

                $intWorkDay++;
                $statuss = 'false';
                //echo "$strStartDate = <b>Work Day</b><br>";
            }
            $strStartDate = date("Y-m-d", strtotime("+1 day", strtotime($strStartDate)));
        }
        return $statuss;
    }
    
    

}
