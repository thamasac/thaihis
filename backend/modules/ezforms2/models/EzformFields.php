<?php

namespace backend\modules\ezforms2\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
/**
 * This is the model class for table "ezform_fields".
 *
 * @property string $ezf_field_id
 * @property string $ezf_id
 * @property integer $ezf_field_group
 * @property string $ezf_field_name
 * @property string $ezf_field_label
 * @property string $ezf_field_default
 * @property integer $ezf_field_type
 * @property integer $ezf_field_ref
 * @property integer $ref_ezf_id
 * @property integer $ref_field_id
 * @property string $ref_field_desc
 * @property string $ref_field_search
 * @property string $ref_form
 * @property double $ezf_field_order
 * @property integer $ezf_field_lenght
 * @property integer $ezf_margin_col
 * @property string $ezf_field_hint
 * @property integer $ezf_field_required
 * @property string $ezf_field_validate
 * @property string $ezf_field_data
 * @property string $ezf_field_specific
 * @property string $ezf_field_options
 * @property string $table_field_type
 * @property integer $table_field_length
 * @property integer $table_index
 * @property string $ezf_field_color
 * @property integer $ezf_condition
 * @property integer $parent_ezf_id
 * @property integer $ezf_target
 * @property integer $ezf_special
 * @property string $ezf_field_cal
 * @property integer $created_by
 * @property string $created_at
 * @property integer $updated_by
 * @property string $updated_at
 * @property string $ezf_version
 * @property string $share_options
 */
class EzformFields extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'ezform_fields';
    }
    
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
    public function rules() {
        return [
            [['ezf_field_id'], 'required'],
            ['ezf_field_name', 'match', 'pattern' => '/^[a-z][a-z0-9_]+$/', 'message' => Yii::t('ezform', 'Variables must be in English or numbers only and do not contain spaces.')],
            [['ezf_field_id', 'ezf_id', 'ezf_field_group', 'ezf_field_type', 'ezf_field_ref', 'ref_ezf_id', 'parent_ezf_id', 'ezf_field_lenght', 'ezf_margin_col', 'ezf_field_required', 'created_by', 'updated_by'], 'integer'],
            [['ref_form', 'ref_field_desc', 'ref_field_search', 'ezf_field_hint', 'ezf_field_validate', 'ezf_field_data', 'ezf_field_specific', 'ezf_field_options', 'ezf_field_cal'], 'string'],
            [['ezf_field_order'], 'number'],
            [['share_options', 'ezf_version', 'ref_field_id', 'table_field_length', 'table_field_type', 'ezf_condition', 'ezf_target', 'table_index', 'ezf_special', 'created_at', 'updated_at'], 'safe'],
            [['ezf_field_name', 'ezf_field_label', 'ezf_field_default'], 'string', 'max' => 255],
            [['ezf_field_color'], 'string', 'max' => 20]
        ];
    }

    public function checkValue($attribute, $params) {

        if (preg_match('/^[a-z0-9_]+$/i', $this->ezf_field_name)) {   // เช็คว่าต้องข้อความต้องเป็นอังกฤษหรือตัวเลขเท่านั้น
            
        } else {
            $this->addError($attribute, Yii::t('ezform', 'Variables must be in English or numbers only and do not contain spaces.'));
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'ezf_field_id' => Yii::t('ezform', 'Field Id'),
            'ezf_id' => Yii::t('ezform', 'Form Id'),
            'ezf_version' => Yii::t('ezform', 'Version'),
            'ezf_field_group' => Yii::t('ezform', 'Group'),
            'ezf_field_name' => Yii::t('ezform', 'Variable'),
            'ezf_field_label' => Yii::t('ezform', 'Question Statement'),
            'ezf_field_default' => Yii::t('ezform', 'Default'),
            'ezf_field_type' => Yii::t('ezform', 'Question type'),
            'ezf_field_ref' => Yii::t('ezform', 'Parent Field'),
            'ref_ezf_id' => Yii::t('ezform', 'Reference Form'),
            'ref_field_id' => Yii::t('ezform', 'Reference Field'),
            'ref_field_desc' => Yii::t('ezform', 'Reference Display'),
            'ref_field_search' => Yii::t('ezform', 'Reference Search'),
            'ref_form' => Yii::t('ezform', 'Reference Form'),
            'ezf_field_order' => Yii::t('ezform', 'Order'),
            'ezf_field_lenght' => Yii::t('ezform', 'Area lenght'),
            'ezf_margin_col' => Yii::t('ezform', 'Margin'),
            'ezf_field_hint' => Yii::t('ezform', 'Hint'),
            'ezf_field_required' => Yii::t('ezform', 'Required'),
            'ezf_field_validate' => Yii::t('ezform', 'Validate'),
            'ezf_field_data' => Yii::t('ezform', 'Data Choice'),
            'ezf_field_specific' => Yii::t('ezform', 'Specific'),
            'ezf_field_options' => Yii::t('ezform', 'Options'),
            'table_field_type' => Yii::t('ezform', 'Field Type'),
            'table_field_length' => Yii::t('ezform', 'Field Length'),
            'ezf_field_color' => Yii::t('ezform', 'BG Color'),
            'ezf_condition' => Yii::t('ezform', 'Condition'),
            'ezf_target' => Yii::t('ezform', 'Target'),
            'ezf_special' => Yii::t('ezform', 'Special'),
            'ezf_field_cal' => Yii::t('ezform', 'Calculator'),
            'created_by' => Yii::t('ezform', 'Created By'),
            'created_at' => Yii::t('ezform', 'Created At'),
            'updated_by' => Yii::t('ezform', 'Updated By'),
            'updated_at' => Yii::t('ezform', 'Updated At'),
            'parent_ezf_id'  => Yii::t('ezform', 'Parent Form'),
            'table_index'=> Yii::t('ezform', 'SQL-Index'),
            'share_options'=> Yii::t('ezform', 'Sharing Settings'),
        ];
    }

}
