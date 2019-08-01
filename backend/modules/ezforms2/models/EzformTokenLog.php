<?php

namespace backend\modules\ezforms2\models;

use Yii;

/**
 * This is the model class for table "ezform_token_log".
 *
 * @property integer $id
 * @property integer $ezf_id
 * @property string $token
 * @property integer $dataid
 * @property string $ip
 * @property string $options
 */
class EzformTokenLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
	return 'ezform_token_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
	return [
            [['id', 'ezf_id', 'dataid'], 'integer'],
            [['options'], 'string'],
            [['token'], 'string', 'max' => 32],
            [['ip'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
	return [
	    'id' => Yii::t('ezform', 'ID'),
	    'ezf_id' => Yii::t('ezform', 'Ezf ID'),
	    'token' => Yii::t('ezform', 'Token'),
	    'dataid' => Yii::t('ezform', 'Dataid'),
	    'ip' => Yii::t('ezform', 'Ip'),
	    'options' => Yii::t('ezform', 'Options'),
	];
    }
}
