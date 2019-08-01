<?php

namespace backend\modules\ezforms2\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
/**
 * This is the model class for table "ezform_change_log".
 *
 * @property integer $log_id
 * @property integer $ezf_id
 * @property integer $ezf_field_id
 * @property string $ezf_version
 * @property string $log_type
 * @property string $log_event
 * @property integer $log_count
 * @property integer $log_ref_id
 * @property string $log_detail
 * @property string $log_ref_table
 * @property string $log_ref_version
 * @property string $log_ref_varname
 * @property integer $created_by
 * @property string $created_at
 * @property integer $updated_by
 * @property string $updated_at
 */
class EzformChangeLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
	return 'ezform_change_log';
    }

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
    public function rules()
    {
	return [
            [['log_id'], 'required'],
            [['log_id', 'ezf_id', 'ezf_field_id', 'log_count', 'log_ref_id', 'created_by', 'updated_by'], 'integer'],
            [['log_detail'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['ezf_version', 'log_event', 'log_ref_version', 'log_ref_varname'], 'string', 'max' => 100],
            [['log_type', 'log_ref_table'], 'string', 'max' => 150]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
	return [
	    'log_id' => Yii::t('ezforms', 'Log ID'),
	    'ezf_id' => Yii::t('ezforms', 'Ezform'),
	    'ezf_field_id' => Yii::t('ezforms', 'Field'),
	    'ezf_version' => Yii::t('ezforms', 'Version'),
	    'log_type' => Yii::t('ezforms', 'Type'),
	    'log_event' => Yii::t('ezforms', 'Event'),
	    'log_count' => Yii::t('ezforms', 'Count'),
	    'log_ref_id' => Yii::t('ezforms', 'Ref ID'),
	    'log_detail' => Yii::t('ezforms', 'Detail'),
	    'log_ref_table' => Yii::t('ezforms', 'Ref Table'),
	    'log_ref_version' => Yii::t('ezforms', 'Ref Version'),
	    'log_ref_varname' => Yii::t('ezforms', 'Ref Varname'),
	    'created_by' => Yii::t('ezforms', 'Created By'),
	    'created_at' => Yii::t('ezforms', 'Created At'),
	    'updated_by' => Yii::t('ezforms', 'Updated By'),
	    'updated_at' => Yii::t('ezforms', 'Updated At'),
	];
    }
}
