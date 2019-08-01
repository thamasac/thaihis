<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\modules\subjects\classes;

/**
 * Description of JKDate
 *
 * @author Admin
 */
class JKDate {

    //put your code here
    public static function convertDate($date, $type = null) {
        $inx = 1;
        if ($type == 'full') {
            $inx = 0;
        }
        if (isset($date) && $date != '') {
            $month = [];
            $month[0] = [\Yii::t('subjects', 'January'), \Yii::t('subjects', 'Jan')];
            $month[1] = [\Yii::t('subjects', 'February'), \Yii::t('subjects', 'Feb')];
            $month[2] = [\Yii::t('subjects', 'March'), \Yii::t('subjects', 'Mar')];
            $month[3] = [\Yii::t('subjects', 'April'), \Yii::t('subjects', 'Apr')];
            $month[4] = [\Yii::t('subjects', 'May'), \Yii::t('subjects', 'Ma')];
            $month[5] = [\Yii::t('subjects', 'June'), \Yii::t('subjects', 'Jun')];
            $month[6] = [\Yii::t('subjects', 'July'), \Yii::t('subjects', 'Jul')];
            $month[7] = [\Yii::t('subjects', 'August'), \Yii::t('subjects', 'Aug')];
            $month[8] = [\Yii::t('subjects', 'September'), \Yii::t('subjects', 'Sep')];
            $month[9] = [\Yii::t('subjects', 'October'), \Yii::t('subjects', 'Oct')];
            $month[10] = [\Yii::t('subjects', 'November'), \Yii::t('subjects', 'Nov')];
            $month[11] = [\Yii::t('subjects', 'December'), \Yii::t('subjects', 'Dec')];

            $dateStamp = strtotime($date);
            $y = date('Y', $dateStamp);
            $m = date('m', $dateStamp);
            $d = date('d', $dateStamp);

            $convert = $d . ' ' . $month[((int) $m - 1)][$inx] . ' ' . ($y + (int) \Yii::t('subjects', 'Y'));
        } else {
            $convert = '';
        }
        return $convert;
    }
    
        public static function convertDateTime($date, $type = null) {
        $explode = explode(' ', $date);
        $inx = 1;
        if ($type == 'full') {
            $inx = 0;
        }
        if (isset($date) && $date != '') {
            $month = [];
            $month[0] = [\Yii::t('subjects', 'January'), \Yii::t('subjects', 'Jan')];
            $month[1] = [\Yii::t('subjects', 'February'), \Yii::t('subjects', 'Feb')];
            $month[2] = [\Yii::t('subjects', 'March'), \Yii::t('subjects', 'Mar')];
            $month[3] = [\Yii::t('subjects', 'April'), \Yii::t('subjects', 'Apr')];
            $month[4] = [\Yii::t('subjects', 'May'), \Yii::t('subjects', 'Ma')];
            $month[5] = [\Yii::t('subjects', 'June'), \Yii::t('subjects', 'Jun')];
            $month[6] = [\Yii::t('subjects', 'July'), \Yii::t('subjects', 'Jul')];
            $month[7] = [\Yii::t('subjects', 'August'), \Yii::t('subjects', 'Aug')];
            $month[8] = [\Yii::t('subjects', 'September'), \Yii::t('subjects', 'Sep')];
            $month[9] = [\Yii::t('subjects', 'October'), \Yii::t('subjects', 'Oct')];
            $month[10] = [\Yii::t('subjects', 'November'), \Yii::t('subjects', 'Nov')];
            $month[11] = [\Yii::t('subjects', 'December'), \Yii::t('subjects', 'Dec')];

            $dateStamp = strtotime($explode[0]);
            $y = date('Y', $dateStamp);
            $m = date('m', $dateStamp);
            $d = date('d', $dateStamp);
            
            $t = explode(':', $explode[1]);

            $convert = $d . ' ' . $month[((int) $m - 1)][$inx] . ' ' . ($y + (int) \Yii::t('subjects', 'Y')).'  '.$t[0].':'.$t[1].':'.$t[2];
        } else {
            $convert = '';
        }
        return $convert;
    }

    public static function checkFormatDate($date) {
        if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $date)) {
            return true;
        } else {
            return false;
        }
    }
    
    static public function verifyDateTime($date)
    {
        return \DateTime::createFromFormat('Y-m-d h:i:s', $date);
    }

}
