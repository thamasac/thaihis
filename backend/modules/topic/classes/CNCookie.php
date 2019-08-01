<?php
namespace backend\modules\topic\classes;
class CNCookie {
    /**
     * @param string $name  name cookie
     * @param string || array $value  value cookie string or array
     * @param string $expire กำหนดวันที่หมดอายุการใช้งาน
     * @param boolean $secure รองรับ https อย่างเดี่ยว true
     * @param boolean $httpOnly จะไม่สามารถใช้ได้ผ่านทางจาวาสคริปต์ true  
     * @return set cookie
    */
    public static function SetCookie($name, $value, $expire='', $secure=false, $httpOnly=true){
       $expire =  !empty($expire) || $expire !='' ? $expire : time() + 86400 * 365; //default 1 year         
       $cookie = new \yii\web\Cookie([
            'name' => $name,
            'value' => $value,
            'expire' => $expire,//time() + 86400 * 365,
            'secure'=>$secure,
            //'httpOnly'=>$httpOnly,
           //'domain' => '*.work.ncrc.in.th'
        ]);
        \Yii::$app->getResponse()->getCookies()->add($cookie);
         
    }
    /**
     * @param string $name ชื่อ cookie 
     * @return value cookie 
    */
    public static function GetCookie($name){         
        return  \Yii::$app->getRequest()->getCookies()->getValue($name);
    }
    /**
     * @param string $name ชื่อ cookie 
     * @return remove cookie 
    */
     public static function RemoveCookie($name){
        return \Yii::$app->getResponse()->getCookies()->remove($name, true);
    }
}
