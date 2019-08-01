<?php

namespace backend\modules\core\models;

use Yii;

/**
 * This is the model class for table "core_item_alias".
 *
 * @property string $item_code
 * @property string $item_name
 * @property string $item_data
 */
class CoreItemAlias extends \yii\db\ActiveRecord {

	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return 'core_item_alias';
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['item_code', 'item_name', 'item_data'], 'required'],
			[['item_code'], 'unique'],
			[['item_data'], 'string'],
			[['item_code'], 'string', 'max' => 50],
			[['item_name'], 'string', 'max' => 200]
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'item_code' => Yii::t('core', 'ID'),
			'item_name' => Yii::t('core', 'Name'),
			'item_data' => Yii::t('core', 'Data'),
		];
	}

}
