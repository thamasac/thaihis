<?php

namespace backend\modules\ezforms2\models;

use Yii;

/**
 * This is the model class for table "system_error".
 *
 * @property integer $id
 * @property integer $code
 * @property string $file
 * @property integer $line
 * @property string $message
 * @property string $name
 * @property string $trace_string
 * @property integer $created_by
 * @property string $created_at
 */
class SystemError extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
	return 'system_error';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
	return [
            [['id'], 'required'],
            [['code', 'line', 'id', 'created_by'], 'integer'],
            [['file', 'message', 'trace_string'], 'string'],
            [['created_at'], 'safe'],
            [['name'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
	return [
	    'id' => Yii::t('ezform', 'ID'),
	    'code' => Yii::t('ezform', 'Code'),
	    'file' => Yii::t('ezform', 'File'),
	    'line' => Yii::t('ezform', 'Line'),
	    'message' => Yii::t('ezform', 'Message'),
	    'name' => Yii::t('ezform', 'Name'),
	    'trace_string' => Yii::t('ezform', 'Trace String'),
	    'created_by' => Yii::t('ezform', 'Created By'),
	    'created_at' => Yii::t('ezform', 'Created At'),
	];
    }
}
