<?php

namespace backend\modules\ezforms2\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
/**
 * This is the model class for table "ezform_community".
 *
 * @property integer $id
 * @property integer $parent_id
 * @property string $send_to
 * @property string $type
 * @property integer $object_id
 * @property integer $dataid
 * @property string $content
 * @property integer $query_tool
 * @property string $field
 * @property string $value_old
 * @property string $value_new
 * @property integer $approv_by
 * @property string $approv_date
 * @property integer $approv_status
 * @property integer $status
 * @property integer $created_by
 * @property string $created_at
 * @property integer $updated_by
 * @property string $updated_at
 */
class EzformCommunity extends \yii\db\ActiveRecord
{
    public $user_name;
    public $avatar_base_url;
    public $avatar_path;
    public $send_to_name;
    public $ezf_name;


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
	return 'ezform_community';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
	return [
            [['id'], 'required'],
            ['send_to', 'required', 'when' => function ($model) {
                return $model->query_tool == 1;
            }, 'whenClient' => "function (attribute, value) {
                console.log($('#ezformcommunity-query_tool').val());
                return $('#query_tool').val() == '1';
              }"],
            [['id', 'parent_id', 'object_id', 'dataid', 'query_tool', 'approv_by', 'approv_status', 'status', 'created_by', 'updated_by'], 'integer'],
            [['content', 'value_old', 'value_new'], 'string'],
            [['approv_date', 'created_at', 'updated_at', 'user_name', 'avatar_base_url', 'avatar_path', 'send_to_name', 'send_to', 'ezf_name'], 'safe'],
            [['type'], 'string', 'max' => 50],
            [['field'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
	return [
	    'id' => Yii::t('ezform', 'ID'),
	    'parent_id' => Yii::t('ezform', 'Parent ID'),
	    'send_to' => Yii::t('ezform', 'Send To'),
	    'type' => Yii::t('ezform', 'Type'),
	    'object_id' => Yii::t('ezform', 'Object ID'),
	    'dataid' => Yii::t('ezform', 'Dataid'),
	    'content' => Yii::t('ezform', 'Content'),
	    'query_tool' => Yii::t('ezform', 'Query Tool'),
	    'field' => Yii::t('ezform', 'Field'),
	    'value_old' => Yii::t('ezform', 'Old Value'),
	    'value_new' => Yii::t('ezform', 'New Value'),
	    'approv_by' => Yii::t('ezform', 'Approved By'),
	    'approv_date' => Yii::t('ezform', 'Approved Date'),
	    'approv_status' => Yii::t('ezform', 'Approved Status'),
	    'status' => Yii::t('ezform', 'Status'),
	    'created_by' => Yii::t('ezform', 'Created By'),
	    'created_at' => Yii::t('ezform', 'Created At'),
	    'updated_by' => Yii::t('ezform', 'Updated By'),
	    'updated_at' => Yii::t('ezform', 'Updated At'),
            'ezf_name' => Yii::t('ezform', 'EzForm'),
	];
    }
}
