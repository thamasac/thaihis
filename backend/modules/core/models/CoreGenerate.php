<?php

namespace backend\modules\core\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\behaviors\AttributeBehavior;
use appxq\sdii\behaviors\SDMultiValueBehavior;
use appxq\sdii\utils\SDUtility;

/**
 * This is the model class for table "core_generate".
 *
 * @property integer $gen_id
 * @property string $gen_group
 * @property string $gen_name
 * @property string $gen_tag
 * @property string $gen_link
 * @property string $gen_process
 * @property string $gen_ui
 * @property string $template_php
 * @property string $template_html
 * @property string $template_js
 * @property string $updated_at
 * @property integer $updated_by
 * @property string $created_at
 * @property integer $created_by
 */
class CoreGenerate extends ActiveRecord {

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
				'class' => SDMultiValueBehavior::className(),
				'attributes' => [
					ActiveRecord::EVENT_AFTER_FIND => 'gen_ui',
				],
				'value' => function ($event) {
					return SDUtility::string2Array($event->sender[$event->data]);
				},
			],
			[
				'class' => AttributeBehavior::className(),
				'attributes' => [
					ActiveRecord::EVENT_BEFORE_INSERT => 'gen_ui',
					ActiveRecord::EVENT_BEFORE_UPDATE => 'gen_ui',
				],
				'value' => function ($event) {
					$session = Yii::$app->session;
					$arr = [];

					if (isset($session['field_tmp'])) {
						$arr = $session['field_tmp'];
					}
					
					return SDUtility::array2String($arr);
				},
			],
			[
				'class' => AttributeBehavior::className(),
				'attributes' => [
					ActiveRecord::EVENT_AFTER_INSERT => 'gen_ui',
					ActiveRecord::EVENT_AFTER_UPDATE => 'gen_ui',
				],
				'value' => function ($event) {
					unset(Yii::$app->session['field_tmp']);
					
					return $event->sender->gen_ui;
				},
			],
		];
	}

	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return 'core_generate';
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['gen_group', 'gen_name'], 'required'],
			[['gen_tag', 'gen_link', 'gen_process',  'template_php', 'template_html', 'template_js'], 'string'],
			[['gen_ui', 'updated_at', 'created_at'], 'safe'],
			[['updated_by', 'created_by'], 'integer'],
			[['gen_group', 'gen_name'], 'string', 'max' => 200]
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'gen_id' => Yii::t('core', 'ID'),
			'gen_group' => Yii::t('core', 'Group'),
			'gen_name' => Yii::t('core', 'Name'),
			'gen_tag' => Yii::t('core', 'Tag'),
			'gen_link' => Yii::t('core', 'Link'),
			'gen_process' => Yii::t('core', 'Process'),
			'gen_ui' => Yii::t('core', 'UI'),
			'template_php' => Yii::t('core', 'PHP'),
			'template_html' => Yii::t('core', 'HTML'),
			'template_js' => Yii::t('core', 'Javascript'),
			'updated_at' => Yii::t('core', 'Updated At'),
			'updated_by' => Yii::t('core', 'Updated By'),
			'created_at' => Yii::t('core', 'Created At'),
			'created_by' => Yii::t('core', 'Created By'),
		];
	}

}
