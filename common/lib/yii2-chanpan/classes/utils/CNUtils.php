<?php
 
namespace cpn\chanpan\classes\utils;
 
class CNUtils {
    /**
     * 
     * @param type $str string 
     * @param type $keyword keyword
     * @return type integer position string
     */
    public static function searchString($str, $keyword){
        return strpos($str,$keyword);
    }
    
    /**
     * 
     * @param type $str
     * @return type string lowercase
     */
    public static function strLowerCase($str){
        return strtolower($str);
    }
    public static function checkLanguageNotThai($key){
        /* 
            if(preg_match('/^[a-z]+$/i',$key)){
        */        
        if(preg_match('/^[a-z0-9_]+$/i',$key)){   // เช็คว่าต้องข้อความต้องเป็นอังกฤษหรือตัวเลขเท่านั้น
            return false;//echo "English+number";
        }else{
            return true; //echo "มีภาษาไทยป่นอยู่";
        }
    }
    /**
     * 
     * @param type $key keyword string 
     * @return string 
     */
    public static function replaceString($key){
        return preg_replace("/[^a-zA-Z0-9]+/", "", $key);
    }
    public static function randomString($length = 6) {
	$str = "";
	$characters = array_merge(range('a','z'), range('0','9'));
	$max = count($characters) - 1;
	for ($i = 0; $i < $length; $i++) {
		$rand = mt_rand(0, $max);
		$str .= $characters[$rand];
	}
	return $str;
    }
    public static function lengthName($gname, $len=''){         
        $checkthai = \backend\modules\ezmodules\classes\ModuleFunc::checkthai($gname);
        $len = ($len != '') ? $len : 12;
        if ($checkthai != '') {
            $len = $len * 3;
        }
        if (strlen($gname) > $len) {
            $gname = substr($gname, 0, $len) . '...';
        }
        return $gname;
    }
}
