<?php

namespace backend\modules\ezmodules\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
/**
 * This is the model class for table "ezmodule_forms".
 *
 * @property integer $form_id
 * @property integer $ezm_id
 * @property integer $ezf_id
 * @property string $form_name
 * @property string $options
 * @property integer $form_order
 * @property integer $form_default
 * @property integer $created_by
 * @property string $created_at
 * @property integer $updated_by
 * @property string $updated_at
 */
class EzmoduleForms extends \yii\db\ActiveRecord
{
    public $ezf_name;
    public $ezf_table;
    public $unique_record;
    
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
	return 'ezmodule_forms';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
	return [
            [['ezm_id', 'ezf_id'], 'required'],
            [['form_id', 'ezm_id', 'ezf_id', 'form_order', 'form_default', 'created_by', 'updated_by'], 'integer'],
            [['options'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['form_name'], 'string', 'max' => 150]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
	return [
	    'form_id' => Yii::t('ezmodule', 'ID'),
	    'ezm_id' => Yii::t('ezmodule', 'Module'),
	    'ezf_id' => Yii::t('ezmodule', 'Form'),
	    'form_name' => Yii::t('ezmodule', 'Name'),
	    'options' => Yii::t('ezmodule', 'Options'),
	    'form_order' => Yii::t('ezmodule', 'Order'),
	    'form_default' => Yii::t('ezmodule', 'Default'),
	    'created_by' => Yii::t('ezmodule', 'Created By'),
	    'created_at' => Yii::t('ezmodule', 'Created At'),
	    'updated_by' => Yii::t('ezmodule', 'Updated By'),
	    'updated_at' => Yii::t('ezmodule', 'Updated At'),
	];
    }
}
