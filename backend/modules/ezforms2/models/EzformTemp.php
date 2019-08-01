<?php

namespace backend\modules\ezforms2\models;

use Yii;

/**
 * This is the model class for table "ezform_temp".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $filename
 * @property string $data
 * @property string $options
 */
class EzformTemp extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
	return 'ezform_temp';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
	return [
            [['id'], 'required'],
            [['id', 'user_id'], 'integer'],
            [['data', 'options'], 'string'],
            [['filename'], 'string', 'max' => 255]
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
	    'filename' => Yii::t('ezform', 'Filename'),
	    'data' => Yii::t('ezform', 'Data'),
            'options' => Yii::t('ezform', 'Options'),
	];
    }
}
