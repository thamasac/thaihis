<?php

namespace backend\modules\ezforms2\models;

use Yii;

/**
 * This is the model class for table "ezform_token".
 *
 * @property string $token
 * @property integer $user_id
 * @property integer $ezf_id
 */
class EzformToken extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
	return 'ezform_token';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
	return [
            [['token'], 'required'],
            [['user_id', 'ezf_id'], 'integer'],
            [['token'], 'string', 'max' => 20]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
	return [
	    'token' => Yii::t('ezmodule', 'Token'),
	    'user_id' => Yii::t('ezmodule', 'User ID'),
	    'ezf_id' => Yii::t('ezmodule', 'Ezf ID'),
	];
    }
}
