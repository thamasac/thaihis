<?php

namespace backend\modules\ezforms2\models;

use Yii;

/**
 * This is the model class for table "ezform_condition".
 *
 * @property integer $cond_id
 * @property integer $ezf_id
 * @property string $ezf_field_name
 * @property string $ezf_field_value
 * @property string $cond_jump
 * @property string $cond_require
 * @property string $ezf_version
 */
class EzformCondition extends \yii\db\ActiveRecord
{
    public $label_jump;
    public $label_require;
    public $var_jump;
    public $var_require;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ezform_condition';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cond_id', 'ezf_id', 'ezf_field_name', 'ezf_field_value'], 'required'],
            [['ezf_id', 'ezf_field_name', 'ezf_field_value', 'ezf_version'], 'safe'],
            [['cond_jump', 'cond_require', 'label_jump', 'label_require', 'var_jump', 'var_require'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'cond_id' => Yii::t('ezform', 'ID'),
            'ezf_id' => Yii::t('ezform', 'Ezform'),
            'ezf_version' => Yii::t('ezform', 'Version'),
            'ezf_field_name' => Yii::t('ezform', 'Field'),
	    'ezf_field_value' => Yii::t('ezform', 'Value'),
            'cond_jump' => Yii::t('ezform', 'Jump'),
            'cond_require' => Yii::t('ezform', 'Require'),
        ];
    }
}
