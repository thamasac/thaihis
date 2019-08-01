<?php

namespace backend\modules\ezmodules\models;

use Yii;

/**
 * This is the model class for table "ezmodule_role".
 *
 * @property string $ezm_id
 * @property string $role
 * @property integer $status
 */
class EzmoduleRole extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
	return 'ezmodule_role';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
	return [
            [['ezm_id', 'role'], 'required'],
            [['ezm_id', 'status'], 'integer'],
            [['role'], 'string', 'max' => 200],
            [['ezm_id', 'role'], 'unique', 'targetAttribute' => ['ezm_id', 'role']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
	return [
	    'ezm_id' => Yii::t('ezmodule', 'รหัสโมูดล'),
	    'role' => Yii::t('ezmodule', 'บทบาท'),
	    'status' => Yii::t('ezmodule', 'active=1, '),
	];
    }
}
