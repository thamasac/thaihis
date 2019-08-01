<?php

namespace backend\modules\ezforms2\models;

use backend\modules\ezforms2\models\EzformFields;
use backend\modules\ezforms2\models\Ezform;

class EzformDynamic extends \yii\db\ActiveRecord {

    private static $_tablename;
    private static $_fields;

    public function __construct($tablename = '', $fields = null, $config = []) {
	if ($tablename != '') {
	    self::$_tablename = $tablename;
	}
        
        if (isset($fields)) {
	    self::$_fields = $fields;
	}
	parent::__construct($config);
    }

    public static function tableName() {
	return self::$_tablename;
    }

    public function attributes() {
	$fields = self::$_fields;
	$attribute = [
			'id'=>'',
			'xsourcex'=>'',
			'xdepartmentx'=>'',
			'rstat'=>'',
			'sitecode'=>'',
			'ptid'=>'',
			'ptcode'=>'',
			'ptcodefull'=>'',
			'hsitecode'=>'',
			'hptcode'=>'',
			'user_create'=>'',
			'create_date'=>'',
			'user_update'=>'',
			'update_date'=>'',
			'target'=>'',
			'error'=>''
	    	    ];// fix attribute
	
	foreach ($fields as $key => $value) {
	    $attribute[$value['ezf_field_name']] = '';
	}

	return array_keys($attribute);

    }

    public function rules() {
	$fields = self::$_fields;
	$safe = [];
	
	foreach ($fields as $key => $value) {
	    $safe[] = $value['ezf_field_name'];
	}

	$safe[] ='ptid';
	$safe[] ='sitecode';
	$safe[] ='ptcode';
	$safe[] ='ptcodefull';
	$safe[] ='hsitecode';
	$safe[] ='hptcode';
	return [
	    [$safe, 'safe']
	];
    }

    public function attributeLabels() {

		$fields = self::$_fields;
		$labels = [];
		foreach ($fields as $key => $value) {
			$labels[$value['ezf_field_name']] = isset($value['ezf_field_label']) ? $value['ezf_field_label'] : $value['ezf_field_name'];
		}
		return $labels;

	}
    
    public static function updateAll($attributes, $condition = '', $params = [])
    {
        $command = static::getDb()->createCommand();
	$condition[static::primaryKey()[0]] = $attributes[static::primaryKey()[0]];
        $command->update(static::tableName(), $attributes, $condition, $params);
	
        return $command->execute();
    }
}