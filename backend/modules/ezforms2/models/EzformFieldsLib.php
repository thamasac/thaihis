<?php

namespace backend\modules\ezforms2\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "ezform_fields_lib".
 *
 * @property string $field_lib_id
 * @property integer $ezf_field_id
 * @property string $ezf_id
 * @property integer $field_lib_group
 * @property string $field_lib_name
 * @property integer $field_lib_share
 * @property integer $field_lib_status
 * @property integer $created_by
 * @property string $created_at
 * @property integer $updated_by
 * @property string $updated_at
 * @property integer $field_lib_approved
 * @property string $ezf_version
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
 * @property string $share_options
 */
class EzformFieldsLib extends \yii\db\ActiveRecord {

    public $ezf_name, $lib_group_name;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'ezform_fields_lib';
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
            [['ezf_field_id', 'field_lib_share', 'ezf_id'], 'required'],
            [['field_lib_id', 'ezf_field_id', 'ezf_id', 'field_lib_group', 'field_lib_share', 'field_lib_approved', 'field_lib_status', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['field_lib_name'], 'string', 'max' => 255],
            [['field_lib_id', 'field_lib_name'], 'unique'],
            ['ezf_field_name', 'match', 'pattern' => '/^[a-z][a-z0-9_]+$/', 'message' => Yii::t('ezform', 'Variables must be in English or numbers only and do not contain spaces.')],
            [['ezf_field_id', 'ezf_id', 'ezf_field_group', 'ezf_field_type', 'ezf_field_ref', 'ref_ezf_id', 'parent_ezf_id', 'ezf_field_lenght', 'ezf_margin_col', 'ezf_field_required'], 'integer'],
            [['ref_form', 'ref_field_desc', 'ref_field_search', 'ezf_field_hint', 'ezf_field_validate', 'ezf_field_data', 'ezf_field_specific', 'ezf_field_options', 'ezf_field_cal'], 'string'],
            [['ezf_field_order'], 'number'],
            [['share_options', 'ezf_version', 'ref_field_id', 'table_field_length', 'table_field_type', 'ezf_condition', 'ezf_target', 'table_index', 'ezf_special'], 'safe'],
            [['ezf_field_name', 'ezf_field_label', 'ezf_field_default'], 'string', 'max' => 255],
            [['ezf_field_color'], 'string', 'max' => 20]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'field_lib_id' => 'Field Lib ID',
            'ezf_field_id' => Yii::t('ezform', 'Question Library'),
            'ezf_id' => Yii::t('ezform', 'Form'),
            'field_lib_group' => Yii::t('ezform', 'Group Question Library'),
            'field_lib_name' => Yii::t('ezform', 'Name Question Library'),
            'field_lib_share' => Yii::t('ezform', 'Shared Question Library'),
            'field_lib_status' =>  Yii::t('ezform', 'Status'),
            'field_lib_approved' => Yii::t('ezform', 'Approved'),
            'created_by' => Yii::t('ezform', 'Created By'),
            'created_at' => Yii::t('ezform', 'Created At'),
            'updated_by' => Yii::t('ezform', 'Updated By'),
            'updated_at' => Yii::t('ezform', 'Updated At'),
        ];
    }

}
