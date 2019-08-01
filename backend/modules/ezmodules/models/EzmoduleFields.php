<?php

namespace backend\modules\ezmodules\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
/**
 * This is the model class for table "ezmodule_fields".
 *
 * @property integer $field_id
 * @property integer $ezm_id
 * @property integer $ezf_id
 * @property integer $ezf_field_id
 * @property string $field_name
 * @property string $order_by
 * @property string $options
 * @property integer $field_order
 * @property integer $created_by
 * @property string $created_at
 * @property integer $updated_by
 * @property string $updated_at
 * @property integer $field_default
 */
class EzmoduleFields extends \yii\db\ActiveRecord
{
    public $ezf_field_name;
    public $table_field_type;
    public $ezf_field_data;
    public $ezf_field_type;

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
	return 'ezmodule_fields';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
	return [
            [['ezm_id', 'ezf_id', 'ezf_field_id'], 'required'],
            [['field_id', 'ezm_id',  'field_order', 'created_by', 'updated_by'], 'integer'],
            [['options', 'ezf_id', 'ezf_field_id'], 'string'],
            [['field_default', 'created_at', 'updated_at', 'ezf_field_name', 'table_field_type', 'ezf_field_data', 'ezf_field_type'], 'safe'],
            [['field_name'], 'string', 'max' => 150],
            [['order_by'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
	return [
	    'field_id' => Yii::t('ezmodule', 'ID'),
	    'ezm_id' => Yii::t('ezmodule', 'Module'),
	    'ezf_id' => Yii::t('ezmodule', 'Form'),
	    'ezf_field_id' => Yii::t('ezmodule', 'Field'),
            'field_default' => Yii::t('ezmodule', 'Field Default'),
	    'field_name' => Yii::t('ezmodule', 'Label'),
	    'order_by' => Yii::t('ezmodule', 'Order By'),
	    'options' => Yii::t('ezmodule', 'Options'),
	    'field_order' => Yii::t('ezmodule', 'Order'),
	    'created_by' => Yii::t('ezmodule', 'Created By'),
	    'created_at' => Yii::t('ezmodule', 'Created At'),
	    'updated_by' => Yii::t('ezmodule', 'Updated By'),
	    'updated_at' => Yii::t('ezmodule', 'Updated At'),
	];
    }
}
