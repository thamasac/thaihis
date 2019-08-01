<?php

namespace backend\modules\core\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use appxq\sdii\behaviors\SDMultiValueBehavior;
use appxq\sdii\utils\SDUtility;
use yii\behaviors\AttributeBehavior;
use backend\modules\core\classes\CoreFunc;

/**
 * This is the model class for table "tables_fields".
 *
 * @property integer $table_id
 * @property string $table_name
 * @property string $table_varname
 * @property string $table_field_type
 * @property string $table_length
 * @property string $table_default
 * @property string $table_index
 * @property string $input_field
 * @property string $input_label
 * @property string $input_hint
 * @property string $input_specific
 * @property string $input_data
 * @property integer $input_required
 * @property string $input_validate
 * @property string $input_meta
 * @property integer $input_order
 * @property string $updated_at
 * @property integer $updated_by
 * @property string $created_at
 * @property integer $created_by
 */
class TablesFields extends ActiveRecord {

	public $field_internal;
	public $field_class;
	public $field_name;
	public $field_meta;

	public function behaviors() {
		return [
			[
				'class' => TimestampBehavior::className(),
				'value' => new Expression('NOW()'),
			],
			[
				'class' => BlameableBehavior::className(),
			],
			[
				'class' => AttributeBehavior::className(),
				'attributes' => [
					ActiveRecord::EVENT_INIT => ['input_field'],
				],
				'value' => function ($event) {
					return 'TextInput';
				},
			],
			[
				'class' => SDMultiValueBehavior::className(),
				'attributes' => [
					ActiveRecord::EVENT_AFTER_FIND => ['input_validate', 'input_meta', 'input_specific'],
				],
				'value' => function ($event) {
					return SDUtility::string2strArray($event->sender[$event->data]);
				},
			],
			[
				'class' => SDMultiValueBehavior::className(),
				'attributes' => [
					ActiveRecord::EVENT_BEFORE_INSERT => ['input_validate', 'input_meta', 'input_specific'],
					ActiveRecord::EVENT_BEFORE_UPDATE => ['input_validate', 'input_meta', 'input_specific'],
				],
				'value' => function ($event) {
					return SDUtility::strArray2String($event->sender[$event->data]);
				},
			],
			[
				'class' => AttributeBehavior::className(),
				'attributes' => [
					ActiveRecord::EVENT_AFTER_INSERT => 'table_name',
				],
				'value' => function ($event) {
					CoreFunc::alterTableAdd($event->sender);
					
					return $event->sender->table_name;
				},
			],
			[
				'class' => AttributeBehavior::className(),
				'attributes' => [
					ActiveRecord::EVENT_AFTER_DELETE => 'table_name',
				],
				'value' => function ($event) {
					CoreFunc::alterTableDrop($event->sender);
					
					return $event->sender->table_name;
				},
			],
		];
	}

	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return 'tables_fields';
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['table_name', 'table_varname', 'table_field_type', 'input_field'], 'required'],
			[['table_default', 'input_hint', 'input_specific', 'input_data', 'input_validate', 'input_meta'], 'string'],
			[['input_required', 'input_order', 'updated_by', 'created_by'], 'integer'],
			[['updated_at', 'created_at'], 'safe'],
			[['table_name', 'table_varname', 'table_field_type', 'table_index'], 'string', 'max' => 50],
			[['table_length'], 'string', 'max' => 10],
			[['input_field'], 'string', 'max' => 20],
			[['input_label'], 'string', 'max' => 100],
			[['input_order'], 'default', 'value' => 0],
			[['table_varname'], 'unique']
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'table_id' => Yii::t('core', 'ID'),
			'table_name' => Yii::t('core', 'Table name'),
			'table_varname' => Yii::t('core', 'Varname'),
			'table_field_type' => Yii::t('core', 'Field type'),
			'table_length' => Yii::t('core', 'Length'),
			'table_default' => Yii::t('core', 'Default'),
			'table_index' => Yii::t('core', 'Index'),
			'input_field' => Yii::t('core', 'Field'),
			'input_label' => Yii::t('core', 'Label'),
			'input_hint' => Yii::t('core', 'Hint'),
			'input_specific' => Yii::t('core', 'Specific'),
			'input_data' => Yii::t('core', 'Data'),
			'input_required' => Yii::t('core', 'Required'),
			'input_validate' => Yii::t('core', 'Validate'),
			'input_meta' => Yii::t('core', 'Option'),
			'input_order' => Yii::t('core', 'Order'),
			'updated_at' => Yii::t('core', 'Updated At'),
			'updated_by' => Yii::t('core', 'Updated By'),
			'created_at' => Yii::t('core', 'Created At'),
			'created_by' => Yii::t('core', 'Created By'),
		];
	}

}
