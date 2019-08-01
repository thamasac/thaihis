<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "auth".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $source
 * @property string $source_id
 */
class Auth extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
	return 'auth';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
	return [
            [['user_id', 'source', 'source_id'], 'required'],
            [['user_id'], 'integer'],
            [['source', 'source_id'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
	return [
	    'id' => Yii::t('ezform', 'ID'),
	    'user_id' => Yii::t('ezform', 'User ID'),
	    'source' => Yii::t('ezform', 'Source'),
	    'source_id' => Yii::t('ezform', 'Source ID'),
	];
    }
}
