<?php

namespace backend\modules\ezmodules\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
/**
 * This is the model class for table "ezmodule_comment".
 *
 * @property integer $commt_id
 * @property integer $ezm_id
 * @property integer $user_id
 * @property integer $vote
 * @property string $message
 * @property integer $created_by
 * @property string $created_at
 * @property integer $updated_by
 * @property string $updated_at
 */
class EzmoduleComment extends \yii\db\ActiveRecord
{
    public $user_name;
    public $avatar_base_url;
    public $avatar_path;

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
	return 'ezmodule_comment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
	return [
            [['commt_id', 'ezm_id'], 'required'],
            [['commt_id', 'ezm_id', 'user_id', 'vote', 'created_by', 'updated_by'], 'integer'],
            [['message'], 'string'],
            [['created_at', 'updated_at', 'user_name', 'avatar_base_url', 'avatar_path'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
	return [
	    'commt_id' => Yii::t('ezmodule', 'ID'),
	    'ezm_id' => Yii::t('ezmodule', 'Module'),
	    'user_id' => Yii::t('ezmodule', 'User'),
	    'vote' => Yii::t('ezmodule', 'Vote'),
	    'message' => Yii::t('ezmodule', 'Comment'),
	    'created_by' => Yii::t('ezmodule', 'Created By'),
	    'created_at' => Yii::t('ezmodule', 'Created At'),
	    'updated_by' => Yii::t('ezmodule', 'Updated By'),
	    'updated_at' => Yii::t('ezmodule', 'Updated At'),
	];
    }
}
