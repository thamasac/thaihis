<?php

namespace backend\modules\ezmodules\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
/**
 * This is the model class for table "ezmodule".
 *
 * @property integer $ezm_id
 * @property string $ezm_name
 * @property string $ezm_detail
 * @property integer $ezm_type
 * @property integer $ezm_system
 * @property string $ezm_devby
 * @property string $ezm_link
 * @property string $ezm_tag
 * @property string $ezm_icon
 * @property string $icon_base_url
 * @property integer $template_id
 * @property string $ezm_js
 * @property string $ezm_html
 * @property string $ezm_css
 * @property integer $ezf_id
 * @property string $sitecode
 * @property string $ezm_builder
 * @property integer $public
 * @property integer $approved
 * @property string $share
 * @property integer $active
 * @property string $options
 * @property integer $order_module
 * @property integer $created_by
 * @property string $created_at
 * @property integer $updated_by
 * @property string $updated_at
 * @property integer $ezm_project
 * @property string $ezm_short_title
 * @property integer $ezm_visible
 * @property integer $ezm_template
 * @property string $ezm_role
 */
class Ezmodule extends \yii\db\ActiveRecord
{
    public $picture;
    public $form_name;
    public $ezf_table;
    public $template_name;
    public $addon_id;
    public $module_id;
    public $user_id;
    
    public function behaviors() {
            return [
                    [
                            'class' => TimestampBehavior::className(),
                            'value' => new Expression('NOW()'),
                    ],
                    [
                            'class' => BlameableBehavior::className(),
                    ],
                'picture' => [
                'class' => \trntv\filekit\behaviors\UploadBehavior::className(),
                'attribute' => 'picture',
                'pathAttribute' => 'ezm_icon',
                'baseUrlAttribute' => 'icon_base_url'
            ]
            ];
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
	return 'ezmodule';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
	return [
            [['ezm_name', 'ezm_short_title', 'ezm_type', 'ezm_system', 'public', 'approved', 'active'], 'required'],
            [['ezm_id', 'ezm_type', 'ezm_system', 'template_id', 'ezf_id', 'public', 'approved', 'active', 'order_module', 'created_by', 'updated_by', 'ezm_project', 'ezm_template'], 'integer'],
            [['ezm_detail', 'ezm_devby', 'ezm_link', 'ezm_tag', 'ezm_icon', 'icon_base_url', 'ezm_js','ezm_html','ezm_css', 'options'], 'string'],
            [['ezm_role', 'created_at', 'updated_at', 'picture', 'ezm_builder', 'share', 'form_name', 'template_name', 'ezf_table', 'ezm_visible'], 'safe'],
            [['ezm_name'], 'string', 'max' => 150],
            [['sitecode'], 'string', 'max' => 10]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
	return [
	    'ezm_id' => Yii::t('ezmodule', 'ID'),
	    'ezm_name' => Yii::t('ezmodule', 'Module Name (Long Title)'),
	    'ezm_detail' => Yii::t('ezmodule', 'Application Information'),
	    'ezm_type' => Yii::t('ezmodule', 'Type'),
            'ezm_role' => Yii::t('ezmodule', 'Role'),
	    'ezm_system' => Yii::t('ezmodule', 'Built-in Module'),
	    'ezm_devby' => Yii::t('ezmodule', 'Development Team'),
	    'ezm_link' => Yii::t('ezmodule', 'URL'),
	    'ezm_tag' => Yii::t('ezmodule', 'Tag'),
	    'ezm_icon' => Yii::t('ezmodule', 'Icon'),
	    'icon_base_url' => Yii::t('ezmodule', 'Base Url'),
	    'template_id' => Yii::t('ezmodule', 'Template'),
	    'ezm_js' => Yii::t('ezmodule', 'Javascript for advanced users'),
            'ezm_html' => Yii::t('ezmodule', 'HTML Template'),
            'ezm_css' => Yii::t('ezmodule', 'CSS'),
	    'ezf_id' => Yii::t('ezmodule', 'Form'),
	    'sitecode' => Yii::t('ezmodule', 'Sitecode'),
	    'ezm_builder' => Yii::t('ezmodule', 'Co-Creator'),
	    'public' => Yii::t('ezmodule', 'Public'),
	    'approved' => Yii::t('ezmodule', 'Approved'),
	    'share' => Yii::t('ezmodule', 'Restricted to whom:'),
	    'active' => Yii::t('ezmodule', 'Active'),
	    'options' => Yii::t('ezmodule', 'Options'),
	    'order_module' => Yii::t('ezmodule', 'Order'),
	    'created_by' => Yii::t('ezmodule', 'Created By'),
	    'created_at' => Yii::t('ezmodule', 'Created At'),
	    'updated_by' => Yii::t('ezmodule', 'Updated By'),
	    'updated_at' => Yii::t('ezmodule', 'Updated At'),
            'picture' => Yii::t('ezmodule', 'Icon'),
            'form_name'=> Yii::t('ezmodule', 'Form'),
            'template_name'=> Yii::t('ezmodule', 'Template'),
            'ezm_project' => Yii::t('ezmodule', 'Sub-Module (To be further added to a Main Module)'),
            'ezm_short_title' => Yii::t('ezmodule', 'Short Title (To be displayed under the Module Icon)'),
            'ezm_visible' => Yii::t('ezmodule', 'System Module'),
            'ezm_template' => Yii::t('ezmodule', 'c) Propose to be a Module Template'),
            
	];
    }
}
