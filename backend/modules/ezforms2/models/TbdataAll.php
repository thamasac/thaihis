<?php

namespace backend\modules\ezforms2\models;

use Yii;
/**
 * This is the model class for table "tbdata_1435745159010048800".
 *
 * @property integer $id
 */
class TbdataAll extends \yii\db\ActiveRecord
{
    protected static $table;
    protected static $colFieldsAddon;
    
    public function attributes()
    {
	$attrDB = array_keys(static::getTableSchema()->columns);
	
        if(isset(self::$colFieldsAddon) && !empty(self::$colFieldsAddon) ){
            $attrDB = array_merge($attrDB, self::$colFieldsAddon);
        }
        
        return $attrDB;
    }
    
    public function rules() {
	$safe = array_keys(static::getTableSchema()->columns);
	
        if(isset(self::$colFieldsAddon) && !empty(self::$colFieldsAddon) ){
            $safe = array_merge($safe, self::$colFieldsAddon);
        }
        
	return [
	    [$safe, 'safe']
	];
    }
    
    public static function tableName()
    {
        return self::$table;
    }

    /* UPDATE */
    public static function setTableName($table)
    {
        self::$table = $table;
    }
    
    public static function colFieldsAddon()
    {
        return self::$colFieldsAddon;
    }

    /* UPDATE */
    public static function setColFieldsAddon($colFields)
    {
        if(isset($colFields) && !empty($colFields)){
            self::$colFieldsAddon = $colFields;
        }
    }

}
