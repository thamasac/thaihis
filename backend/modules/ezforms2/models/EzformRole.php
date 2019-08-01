<?php

namespace backend\modules\ezforms2\models;

use Yii;

/**
 * This is the model class for table "ezform_role".
 *
 * @property string $ezf_id
 * @property string $role
 * @property integer $status
 */
class EzformRole extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
	return 'ezform_role';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
	return [
            [['ezf_id', 'role'], 'required'],
            [['ezf_id', 'status'], 'integer'],
            [['role'], 'string', 'max' => 200],
            [['ezf_id', 'role'], 'unique', 'targetAttribute' => ['ezf_id', 'role']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
	return [
	    'ezf_id' => Yii::t('ezform', 'รหัสฟอร์ม'),
	    'role' => Yii::t('ezform', 'บทบาท'),
	    'status' => Yii::t('ezform', 'active=1, '),
	];
    }
}
