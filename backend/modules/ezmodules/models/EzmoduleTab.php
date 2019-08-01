<?php

namespace backend\modules\ezmodules\models;

use Yii;

/**
 * This is the model class for table "ezmodule_tab".
 *
 * @property integer $tab_id
 * @property string $label
 * @property string $widget
 * @property integer $tab_default
 * @property integer $ezm_id
 * @property integer $user_id
 * @property string $options
 * @property integer $parent
 * @property integer $order
 * @property string $template
 */
class EzmoduleTab extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
	return 'ezmodule_tab';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
	return [
            [['tab_id'], 'required'],
            [['tab_id', 'tab_default', 'ezm_id', 'user_id', 'parent'], 'integer'],
            [['options'], 'string'],
            [['label'], 'string', 'max' => 100],
            [['widget'], 'string', 'max' => 255],
            [['order', 'template'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
	return [
	    'tab_id' => Yii::t('ezmodule', 'ID'),
	    'label' => Yii::t('ezmodule', 'Tab Name'),
	    'widget' => Yii::t('ezmodule', 'Widget'),
	    'tab_default' => Yii::t('ezmodule', 'Public Tab'),
	    'ezm_id' => Yii::t('ezmodule', 'Module'),
	    'user_id' => Yii::t('ezmodule', 'User'),
	    'options' => Yii::t('ezmodule', 'Options'),
            'parent' => Yii::t('ezmodule', 'Parent or Sub-tab Menu'),
            'order' => Yii::t('ezmodule', 'Order'),
            'template' => Yii::t('ezmodule', 'Template'),
	];
    }
}
