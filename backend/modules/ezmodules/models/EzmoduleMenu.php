<?php

namespace backend\modules\ezmodules\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
/**
 * This is the model class for table "ezmodule_menu".
 *
 * @property integer $menu_id
 * @property integer $ezm_id
 * @property string $menu_name
 * @property integer $menu_parent
 * @property string $menu_content
 * @property integer $menu_active
 * @property double $menu_order
 * @property integer $created_by
 * @property string $created_at
 * @property integer $updated_by
 * @property string $updated_at
 */
class EzmoduleMenu extends \yii\db\ActiveRecord
{
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
    public static function tableName()
    {
	return 'ezmodule_menu';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
	return [
            [['ezm_id', 'menu_name'], 'required'],
            [['menu_id', 'ezm_id', 'menu_parent', 'menu_active', 'created_by', 'updated_by'], 'integer'],
            [['menu_content'], 'string'],
            [['menu_order'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['menu_name'], 'string', 'max' => 150]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
	return [
	    'menu_id' => Yii::t('ezmodule', 'ID'),
	    'ezm_id' => Yii::t('ezmodule', 'Module'),
	    'menu_name' => Yii::t('ezmodule', 'Name'),
	    'menu_parent' => Yii::t('ezmodule', 'Parent'),
	    'menu_content' => Yii::t('ezmodule', 'Content'),
	    'menu_active' => Yii::t('ezmodule', 'Active'),
	    'menu_order' => Yii::t('ezmodule', 'Order'),
	    'created_by' => Yii::t('ezmodule', 'Created By'),
	    'created_at' => Yii::t('ezmodule', 'Created At'),
	    'updated_by' => Yii::t('ezmodule', 'Updated By'),
	    'updated_at' => Yii::t('ezmodule', 'Updated At'),
	];
    }
}
