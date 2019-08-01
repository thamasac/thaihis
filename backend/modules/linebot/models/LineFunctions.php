<?php

namespace backend\modules\linebot\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
/**
 * This is the model class for table "line_functions".
 *
 * @property integer $id
 * @property string $channel_id
 * @property string $command
 * @property string $api
 * @property string $template
 * @property string $options
 * @property string $role
 * @property integer $active
 * @property integer $updated_by
 * @property string $updated_at
 * @property integer $created_by
 * @property string $created_at
 */
class LineFunctions extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
	return 'line_functions';
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
            [['id', 'command'], 'required'],
            [['id', 'active', 'updated_by', 'created_by'], 'integer'],
            [['api', 'template', 'options', 'role'], 'string'],
            [['updated_at', 'created_at'], 'safe'],
            [['channel_id'], 'string', 'max' => 100],
            [['command'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
	return [
	    'id' => Yii::t('linebot', 'ID'),
	    'channel_id' => Yii::t('linebot', 'Channel ID'),
	    'command' => Yii::t('linebot', 'Command'),
	    'api' => Yii::t('linebot', 'Api'),
	    'template' => Yii::t('linebot', 'Template'),
	    'options' => Yii::t('linebot', 'Options'),
	    'role' => Yii::t('linebot', 'Role'),
	    'active' => Yii::t('linebot', 'Active'),
	    'updated_by' => Yii::t('linebot', 'Updated By'),
	    'updated_at' => Yii::t('linebot', 'Updated At'),
	    'created_by' => Yii::t('linebot', 'Created By'),
	    'created_at' => Yii::t('linebot', 'Created At'),
	];
    }
}
