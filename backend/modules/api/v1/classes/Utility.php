<?php
/**
 * Created by PhpStorm.
 * User: kawin
 * Date: 1/15/2018
 * Time: 10:48 AM
 */

namespace backend\modules\api\v1\classes;


use DateTime;
use DateTimeZone;
class Utility
{

    public static function ConvertIsoDate($dateString){
        $date = new DateTime($dateString, new DateTimeZone("UTC"));
        $date->setTimeZone(new DateTimeZone(date_default_timezone_get()));
        $date = $date->format('Y-m-d H:i:s');
        return $date;
    }


}