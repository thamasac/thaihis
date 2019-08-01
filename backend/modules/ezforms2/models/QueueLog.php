<?php

namespace backend\modules\ezforms2\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
/**
 * This is the model class for table "queue_log".
 *
 * @property integer $id
 * @property string $unit
 * @property integer $ezf_id
 * @property integer $dataid
 * @property string $status
 * @property integer $enable
 * @property integer $setting_id
 * @property integer $module_id
 * @property string $current_unit
 * @property integer $user_receive
 * @property string $time_receive
 * @property string $options
 * @property integer $updated_by
 * @property string $dataid_receive
 * @property string $updated_at
 * @property integer $created_by
 * @property string $created_at
 * @property string $type
 * @property string $tab_name
 * @property string $staple_id
 */
class QueueLog extends \yii\db\ActiveRecord
{
    public $suser_name;
    public $ruser_name;
    public $sunit_name;
    public $module_name;
    public $process_forms;
    public $field_detail;
    public $ezf_table;
    public $complete_cond;

    public function behaviors() {
		return [
			[
				'class' => TimestampBehavior::className(),
				'value' => new Expression('NOW()'),
			],
			[
				'class' => BlameableBehavior::className(),
			],
		];
	}
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
	return 'queue_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
	return [
            [['id'], 'required'],
            [['id', 'ezf_id', 'dataid', 'enable', 'setting_id', 'module_id', 'user_receive', 'updated_by', 'created_by'], 'integer'],
            [['staple_id', 'type', 'time_receive', 'updated_at', 'created_at', 'suser_name', 'ruser_name', 'sunit_name', 'module_name', 'tab_name', 'process_forms', 'dataid_receive'], 'safe'],
            [['options'], 'string'],
            [['unit', 'status', 'current_unit'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
	return [
	    'id' => Yii::t('ezforms', 'ID'),
	    'unit' => Yii::t('ezforms', 'Unit'),
            'type' => Yii::t('ezforms', 'Type'),
	    'ezf_id' => Yii::t('ezforms', 'Ezf ID'),
	    'dataid' => Yii::t('ezforms', 'Dataid'),
            'staple_id' => Yii::t('ezforms', 'Staple Id'),
	    'status' => Yii::t('ezforms', 'Status'),
	    'enable' => Yii::t('ezforms', 'Enable'),
	    'setting_id' => Yii::t('ezforms', 'Setting ID'),
	    'module_id' => Yii::t('ezforms', 'Module ID'),
	    'current_unit' => Yii::t('ezforms', 'Current Unit'),
	    'user_receive' => Yii::t('ezforms', 'User Receive'),
	    'time_receive' => Yii::t('ezforms', 'Time Receive'),
	    'options' => Yii::t('ezforms', 'Options'),
	    'updated_by' => Yii::t('ezforms', 'Updated By'),
	    'updated_at' => Yii::t('ezforms', 'Updated At'),
	    'created_by' => Yii::t('ezforms', 'Created By'),
	    'created_at' => Yii::t('ezforms', 'Created At'),
            'ruser_name' => Yii::t('ezforms', 'Receiver'),
            'suser_name' => Yii::t('ezforms', 'Sender'),
            'sunit_name' => Yii::t('ezforms', 'Unit'),
            'tab_name' => Yii::t('ezforms', 'Objective'),
            'dataid_receive' => Yii::t('ezforms', 'Dataid'),
	];
    }
}
