<?php

namespace backend\modules\core\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\AttributeBehavior;
use appxq\sdii\behaviors\SDMultiValueBehavior;
use appxq\sdii\utils\SDUtility;

/**
 * This is the model class for table "core_options".
 *
 * @property integer $option_id
 * @property string $option_name
 * @property string $option_value
 * @property string $autoload
 * @property string $input_label
 * @property string $input_hint
 * @property string $input_field
 * @property string $input_specific
 * @property string $input_data
 * @property integer $input_required
 * @property string $input_validate
 * @property string $input_meta
 * @property integer $input_order
 */
class CoreOptions extends \yii\db\ActiveRecord {

	public $field_internal;
	public $field_class;
	public $field_name;
	public $field_meta;

	public function behaviors() {
		return [
			[
				'class' => AttributeBehavior::className(),
				'attributes' => [
					ActiveRecord::EVENT_INIT => ['autoload'],
				],
				'value' => function ($event) {
					return 'yes';
				},
			],
			[
				'class' => SDMultiValueBehavior::className(),
				'attributes' => [
					ActiveRecord::EVENT_AFTER_FIND => ['input_validate', 'input_meta', 'input_specific'],
				],
				'value' => function ($event) {
					if(!isset($event->sender->field_meta)){
						return SDUtility::string2strArray($event->sender[$event->data]);
					} else {
						return $event->sender[$event->data];
					}
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
		];
	}
	
	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return 'core_options';
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['option_name'], 'required'],
			[['option_value', 'input_hint', 'input_specific', 'input_data', 'input_validate', 'input_meta'], 'string'],
			[['input_required', 'input_order'], 'integer'],
			[['option_name'], 'string', 'max' => 64],
			[['autoload', 'input_field'], 'string', 'max' => 20],
			[['input_label'], 'string', 'max' => 100],
			[['input_order'], 'default', 'value' => 0],
			[['option_name'], 'unique']
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'option_id' => Yii::t('core', 'ID'),
			'option_name' => Yii::t('core', 'Name'),
			'option_value' => Yii::t('core', 'Value'),
			'autoload' => Yii::t('core', 'Autoload'),
			'input_label' => Yii::t('core', 'Label'),
			'input_hint' => Yii::t('core', 'Hint'),
			'input_field' => Yii::t('core', 'Field'),
			'input_specific' => Yii::t('core', 'Specific'),
			'input_data' => Yii::t('core', 'Data'),
			'input_required' => Yii::t('core', 'Required'),
			'input_validate' => Yii::t('core', 'Validate'),
			'input_meta' => Yii::t('core', 'Option'),
			'input_order' => Yii::t('core', 'Order'),
		];
	}

}
