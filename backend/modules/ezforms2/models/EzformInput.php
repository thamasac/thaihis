<?php

namespace backend\modules\ezforms2\models;

use Yii;

/**
 * This is the model class for table "ezform_input".
 *
 * @property integer $input_id
 * @property string $input_name
 * @property string $input_class
 * @property string $input_function
 * @property string $system_class
 * @property string $input_data
 * @property string $input_validate
 * @property string $input_specific
 * @property string $input_option
 * @property string $table_field_type
 * @property integer $table_field_length
 * @property string $input_version
 * @property double $input_order
 * @property integer $input_active
 * @property string $input_behavior
 * @property integer $input_size
 * @property integer input_category
 * @property string $input_link
 * @property string $input_icon
 * @property string $input_base_url
 */
class EzformInput extends \yii\db\ActiveRecord
{
    public $icon;
    public $content;
    
    public function behaviors() {
            return [
                'icon' => [
                'class' => \trntv\filekit\behaviors\UploadBehavior::className(),
                'attribute' => 'icon',
                'pathAttribute' => 'input_icon',
                'baseUrlAttribute' => 'input_base_url'
            ]
            ];
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
	return 'ezform_input';
    }

    
    /**
     * @inheritdoc
     */
    public function rules()
    {
	return [
            [['input_name', 'system_class','input_function', 'table_field_type', 'input_size'], 'required'],
            [['input_data', 'input_validate', 'input_specific', 'input_option', 'input_link', 'input_icon', 'input_base_url'], 'string'],
            [['table_field_length','input_active'], 'integer'],
            [['input_order', 'input_size', 'input_category'], 'number'],
            [['input_name', 'table_field_type', 'input_version'], 'string', 'max' => 50],
            [['input_class'], 'string', 'max' => 80],
            [['input_function'], 'string', 'max' => 30],
            [['icon'], 'safe'],
            [['system_class', 'input_behavior'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
	return [
	    'input_id' => Yii::t('ezform', 'ID'),
	    'input_name' => Yii::t('ezform', 'Name'),
	    'input_class' => Yii::t('ezform', 'Class'),
	    'input_function' => Yii::t('ezform', 'Function'),
	    'system_class' => Yii::t('ezform', 'System Class'),
	    'input_data' => Yii::t('ezform', 'Data Items'),
	    'input_validate' => Yii::t('ezform', 'Validate'),
	    'input_specific' => Yii::t('ezform', 'Specific'),
	    'input_option' => Yii::t('ezform', 'Option'),
	    'table_field_type' => Yii::t('ezform', 'Field Type'),
	    'table_field_length' => Yii::t('ezform', 'Field Length'),
	    'input_version' => Yii::t('ezform', 'Version'),
	    'input_order' => Yii::t('ezform', 'Order'),
            'input_active' => Yii::t('ezform', 'Active'),
            'input_behavior' => Yii::t('ezform', 'Behavior'),
            'input_size' => Yii::t('ezform', 'Size'),
            'input_category' => Yii::t('ezform', 'Category'),
            'input_link' => Yii::t('ezform', 'Help-Link'),
            'input_icon' => Yii::t('ezform', 'Icon'),
            'input_link' => Yii::t('ezform', 'Base Url'),
            'icon' => Yii::t('ezform', 'Icon 32x32'),
	];
    }
}
