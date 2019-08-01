<?php

namespace backend\modules\ezmodules\models;

use Yii;

/**
 * This is the model class for table "ezmodule_addon".
 *
 * @property integer $addon_id
 * @property integer $addon_default
 * @property integer $ezm_id
 * @property integer $module_id
 * @property integer $user_id
 */
class EzmoduleAddon extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
	return 'ezmodule_addon';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
	return [
            [['module_id'], 'required'],
            [['addon_id', 'addon_default', 'ezm_id', 'module_id', 'user_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
	return [
	    'addon_id' => Yii::t('ezmodule', 'ID'),
	    'addon_default' => Yii::t('ezmodule', 'Default'),
	    'ezm_id' => Yii::t('ezmodule', 'Module'),
	    'module_id' => Yii::t('ezmodule', 'Module'),
	    'user_id' => Yii::t('ezmodule', 'User'),
	];
    }
}
