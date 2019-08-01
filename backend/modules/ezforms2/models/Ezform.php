<?php

namespace backend\modules\ezforms2\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "ezform".
 *
 * @property string $ezf_id
 * @property string $ezf_version
 * @property string $ezf_name
 * @property string $ezf_detail
 * @property string $xsourcex
 * @property string $ezf_table
 * @property string $created_by
 * @property string $created_at
 * @property string $updated_by
 * @property string $updated_at
 * @property integer $status
 * @property integer $shared
 * @property integer $public_listview
 * @property integer $public_edit
 * @property integer $public_delete
 * @property string $co_dev
 * @property string $assign
 * @property integer $category_id
 * @property string $field_detail
 * @property string $ezf_sql
 * @property string $ezf_js
 * @property string $ezf_error
 * @property integer $query_tools
 * @property integer $unique_record
 * @property integer $consult_tools
 * @property string $consult_users
 * @property string $consult_telegram
 * @property string $ezf_options
 * @property integer $enable_version
 * @property string $ezf_icon
 * @property integer $ezf_crf
 * @property integer $ezf_db2
 * @property string $ezf_role
 * @property string $ezf_tag
 */
class Ezform extends \yii\db\ActiveRecord {

    public $fullname;

    /** 
     * @inheritdoc
     */
    public static function tableName() {
        return 'ezform';
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
            [['ezf_id', 'ezf_table', 'status', 'shared', 'ezf_name'], 'required'],
            [['ezf_id',  'status', 'shared', 'public_listview', 'public_edit', 'public_delete', 'category_id', 'query_tools', 'unique_record', 'consult_tools'], 'integer'],
            [['ezf_detail', 'ezf_js', 'ezf_error', 'ezf_options'], 'string'],
            [['ezf_role', 'ezf_tag', 'ezf_sql', 'created_at', 'updated_at', 'co_dev', 'assign', 'consult_users', 'field_detail', 'enable_version', 'ezf_icon', 'ezf_crf', 'ezf_db2'], 'safe'],
            [['ezf_version', 'consult_telegram'], 'string', 'max' => 50],
            [['ezf_name'], 'string', 'max' => 255],
            [['xsourcex'], 'string', 'max' => 20],
            //[['ezf_table'], 'unique'],
            [['ezf_table'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'ezf_id' => Yii::t('app', 'ID'),
            'ezf_version' => Yii::t('ezform', 'Version'),
            'ezf_name' => Yii::t('ezform', 'Short Name'),
            'ezf_detail' => Yii::t('ezform', 'Long Name'),
            'xsourcex' => Yii::t('ezform', 'Xsourcex'),
            'ezf_table' => Yii::t('ezform', 'Table'),
            'created_by' => Yii::t('ezform', 'Created By'),
            'created_at' => Yii::t('ezform', 'Created At'),
            'updated_by' => Yii::t('ezform', 'Updated By'),
            'updated_at' => Yii::t('ezform', 'Updated At'),
            'status' => Yii::t('ezform', 'Status'),
            'enable_version' => Yii::t('ezform', 'Enable Version Control'),
            'shared' => Yii::t('ezform', 'Form Sharing'),
            'ezf_role' => Yii::t('ezform', 'Role'),
            'ezf_tag' => Yii::t('ezform', 'Tag'),
            'public_listview' => Yii::t('ezform', 'Data Sharing'),
            'public_edit' => Yii::t('ezform', 'Allows to update list of data tables.'),
            'public_delete' => Yii::t('ezform', 'Allows to delete list of data tables.'),
            'co_dev' => Yii::t('ezform', 'Co-creator'),
            'assign' => Yii::t('ezform', 'Assign to'),
            'category_id' => Yii::t('ezform', 'Form Category'),
            'field_detail' => Yii::t('ezform', 'Display fields'),
            'ezf_sql' => Yii::t('ezform', 'SQL Validation check upon saving draft before submision'),
            'ezf_js' => Yii::t('ezform', 'JS'),
            'ezf_error' => Yii::t('ezform', 'Error'),
            'query_tools' => Yii::t('ezform', 'Query Tools'),
            'unique_record' => Yii::t('ezform', 'Unique Record (Patient mode)'),
            'consult_tools' => Yii::t('ezform', 'Consultation Tools'),
            'consult_users' => Yii::t('ezform', 'Consult User'),
            'consult_telegram' => Yii::t('ezform', 'Telegram'),
            'ezf_options' => Yii::t('ezform', 'Options'),
            'ezf_icon' => Yii::t('ezform', 'Icon'),
            'ezf_crf' => Yii::t('ezform', 'Declare to be a Case Report Form (CRF)'),
            'ezf_db2' => Yii::t('ezform', 'Enable Double Data Entry'),
        ];
    }

}
