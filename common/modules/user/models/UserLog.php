<?php

namespace common\modules\user\models;

use Yii;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "user_comment".
 *
 * @property integer $uaid
 * @property integer $user_id
 * @property string $comment
 * @property string $name
 * @property string $update_at
 */
class UserLog extends \yii\db\ActiveRecord
{
    public function behaviors() {
	return [
	    [
		    'class' => TimestampBehavior::className(),
		    'attributes'=>[
			'beforeInsert'=>'update_at', 
			'beforeUpdate'=>'update_at',
			],
		    'value' => new Expression('NOW()'),
	    ]
	];
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id'], 'integer'],
            [['comment'], 'string'],
            [['action','update_at'], 'safe'],
            [['name'], 'string', 'max' => 200]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'uaid' => Yii::t('app', 'Uaid'),
            'user_id' => Yii::t('app', 'User ID'),
            'comment' => Yii::t('app', 'Comment'),
            'name' => Yii::t('app', 'จัดการสิทธิ์โดย'),
            'update_at' => Yii::t('app', 'Update At'),
        ];
    }
}
