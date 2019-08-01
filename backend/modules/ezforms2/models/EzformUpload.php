<?php

namespace backend\modules\ezforms2\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\Expression;
use yii\db\BaseActiveRecord;
/**
 * This is the model class for table "file_upload".
 *
 * @property integer $fid
 * @property string $target
 * @property string $file_name
 * @property integer $file_active
 * @property integer $ezf_id
 * @property integer $ezf_field_id
 * @property integer $created_by
 * @property string $created_at
 * @property integer $tbid
 * @property integer $file_name_old
 * @property integer $mode
 */
class EzformUpload extends \yii\db\ActiveRecord
{
    public function behaviors() {
	return [
	    [
		    'class' => TimestampBehavior::className(),
		    'attributes' => [
			BaseActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
		    ],
		    'value' => new Expression('NOW()'),
	    ],
	    [
		    'class' => BlameableBehavior::className(),
		    'attributes' => [
			BaseActiveRecord::EVENT_BEFORE_INSERT => ['created_by'],
		    ],
	    ],
	];
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ezform_upload';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['file_name', 'tbid', 'ezf_id', 'ezf_field_id'], 'required'],
            [['file_name', 'file_name_old'], 'string'],
            [['file_active', 'ezf_id', 'ezf_field_id', 'created_by'], 'integer'],
            [['target', 'mode', 'created_at'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'fid' => Yii::t('ezform', 'ID'),
	    'tbid' => Yii::t('ezform', 'FK ID'),
            'target' => Yii::t('ezform', 'Target'),
            'file_name' => Yii::t('ezform', 'Image'),
	    'file_name_old' => Yii::t('ezform', 'File Name'),
            'file_active' => Yii::t('ezform', 'Active'),
            'ezf_id' => Yii::t('ezform', 'Ezform'),
            'ezf_field_id' => Yii::t('ezform', 'Field'),
            'created_by' => Yii::t('ezform', 'Created By'),
            'created_at' => Yii::t('ezform', 'Created At'),
			'mode' => Yii::t('ezform', 'Mode'),
        ];
    }
}
