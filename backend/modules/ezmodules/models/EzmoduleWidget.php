<?php

namespace backend\modules\ezmodules\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\behaviors\SluggableBehavior;
/**
 * This is the model class for table "ezmodule_widget".
 *
 * @property integer $widget_id
 * @property string $widget_name
 * @property string $widget_varname
 * @property string $widget_type
 * @property string $widget_detail
 * @property string $widget_example
 * @property string $widget_render
 * @property integer $widget_attribute
 * @property string $options
 * @property integer $enable
 * @property integer $ezm_id
 * @property integer $ezf_id
 * @property integer $created_by
 * @property string $created_at
 * @property integer $updated_by
 * @property string $updated_at
 */
class EzmoduleWidget extends \yii\db\ActiveRecord
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
                    [
                        'class' => SluggableBehavior::className(),
                        'slugAttribute' => 'widget_varname',
                        'attribute' => 'widget_name',
                        'ensureUnique' => true,
                        'immutable' => true,
                    ],
            ];
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
	return 'ezmodule_widget';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
	return [
            [['widget_id', 'widget_name'], 'required'],
            [['widget_id', 'enable', 'ezm_id', 'ezf_id', 'created_by', 'updated_by'], 'integer'],
            [['widget_detail', 'widget_example', 'options'], 'string'],
            [['created_at', 'updated_at', 'widget_attribute'], 'safe'],
            [['widget_name', 'widget_varname', 'widget_render'], 'string', 'max' => 255],
            [['widget_type'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
	return [
	    'widget_id' => Yii::t('ezmodule', 'ID'),
	    'widget_name' => Yii::t('ezmodule', 'Name'),
	    'widget_varname' => Yii::t('ezmodule', 'Varname'),
	    'widget_type' => Yii::t('ezmodule', 'Widget Type'),
	    'widget_detail' => Yii::t('ezmodule', 'Description'),
	    'widget_example' => Yii::t('ezmodule', 'Example'),
            'widget_render' => Yii::t('ezmodule', 'Render View Name'),
            'widget_attribute' => Yii::t('ezmodule', 'Set to Attribute whitout redering'),
	    'options' => Yii::t('ezmodule', 'Options'),
	    'enable' => Yii::t('ezmodule', 'Enable'),
	    'ezm_id' => Yii::t('ezmodule', 'Module'),
	    'ezf_id' => Yii::t('ezmodule', 'Form'),
	    'created_by' => Yii::t('ezmodule', 'Created By'),
	    'created_at' => Yii::t('ezmodule', 'Created At'),
	    'updated_by' => Yii::t('ezmodule', 'Updated By'),
	    'updated_at' => Yii::t('ezmodule', 'Updated At'),
	];
    }
}
