<?php

namespace backend\modules\ezforms2\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "ezform_fields_lib_group".
 *
 * @property integer $lib_group_id
 * @property string $lib_group_name
 * @property integer $created_by
 * @property string $created_at
 * @property integer $updated_by
 * @property string $updated_at
 */
class EzformFieldsLibGroup extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
	return 'ezform_fields_lib_group';
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
            [['lib_group_id'], 'required'],
            [['lib_group_id', 'created_by', 'updated_by'], 'integer'],
            [['lib_group_name'], 'string', 'max' => 255],
            [['lib_group_id'], 'unique'],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
	return [
	    'lib_group_id' => 'Lib Group ID',
	    'lib_group_name' => 'Lib Group Name',
	];
    }
}
