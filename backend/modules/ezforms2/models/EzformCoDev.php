<?php

namespace backend\modules\ezforms2\models;

use Yii;

/**
 * This is the model class for table "ezform_co_dev".
 *
 * @property string $ezf_id
 * @property string $user_co
 * @property integer $status
 */
class EzformCoDev extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
	return 'ezform_co_dev';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
	return [
            [['ezf_id', 'user_co'], 'required'],
            [['ezf_id', 'user_co', 'status'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
	return [
	    'ezf_id' => Yii::t('ezform', 'ID'),
	    'user_co' => Yii::t('yii', 'User'),
	    'status' => Yii::t('ezform', 'Status'),
	];
    }
}
