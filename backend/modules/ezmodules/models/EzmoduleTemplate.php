<?php

namespace backend\modules\ezmodules\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
/**
 * This is the model class for table "ezmodule_template".
 *
 * @property integer $template_id
 * @property string $template_name
 * @property string $template_html
 * @property string $template_js
 * @property string $template_css
 * @property integer $template_system
 * @property integer $public
 * @property string $sitecode
 * @property integer $created_by
 * @property string $created_at
 * @property integer $updated_by
 * @property string $updated_at
 */
class EzmoduleTemplate extends \yii\db\ActiveRecord
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
	return 'ezmodule_template';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
	return [
            [['template_name'], 'required'],
            [['template_id', 'template_system', 'public', 'created_by', 'updated_by'], 'integer'],
            [['template_html', 'template_js', 'template_css'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['template_name'], 'string', 'max' => 150],
            [['sitecode'], 'string', 'max' => 20]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
	return [
	    'template_id' => Yii::t('ezmodule', 'ID'),
	    'template_name' => Yii::t('ezmodule', 'Name'),
	    'template_html' => Yii::t('ezmodule', 'HTML'),
	    'template_js' => Yii::t('ezmodule', 'JS'),
            'template_css' => Yii::t('ezmodule', 'CSS'),
	    'template_system' => Yii::t('ezmodule', 'System Template'),
	    'public' => Yii::t('ezmodule', 'Public'),
	    'sitecode' => Yii::t('ezmodule', 'Sitecode'),
	    'created_by' => Yii::t('ezmodule', 'Created By'),
	    'created_at' => Yii::t('ezmodule', 'Created At'),
	    'updated_by' => Yii::t('ezmodule', 'Updated By'),
	    'updated_at' => Yii::t('ezmodule', 'Updated At'),
	];
    }
}
