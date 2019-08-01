<?php

namespace backend\modules\ezforms2\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\behaviors\SluggableBehavior;
/**
 * This is the model class for table "ezform_version".
 *
 * @property string $ver_code
 * @property string $ver_for
 * @property integer $ver_approved
 * @property integer $ver_active
 * @property integer $approved_by
 * @property string $approved_date
 * @property string $ver_options
 * @property integer $ezf_id
 * @property string $field_detail
 * @property string $ezf_sql
 * @property string $ezf_js
 * @property string $ezf_error
 * @property string $ezf_options
 * @property integer $updated_by
 * @property string $updated_at
 * @property integer $created_by
 * @property string $created_at
 */
class EzformVersion extends \yii\db\ActiveRecord
{
    public $fullname;
    public $ezf_name;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
	return 'ezform_version';
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
            [
                'class' => SluggableBehavior::className(),
                'slugAttribute' => 'ver_code',
                'attribute' => 'ver_code',
                'ensureUnique' => true,
                'immutable' => true,
            ],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
	return [
            [['ver_code', 'ezf_id'], 'required'],
            ['ver_code', 'match', 'pattern' => '/^[a-z0-9_]+$/i', 'message' => Yii::t('ezform', 'Variables must be in English or numbers only and do not contain spaces.')],
            ['ver_code', 'match', 'pattern' => '/^(?!.*all).*$/i', 'message' => Yii::t('ezform', 'Unable to use `all`')],
            [['ver_approved', 'ver_active', 'approved_by', 'ezf_id', 'updated_by', 'created_by'], 'integer'],
            [['approved_date', 'updated_at', 'created_at'], 'safe'],
            [['ver_options', 'field_detail', 'ezf_sql', 'ezf_js', 'ezf_error', 'ezf_options'], 'string'],
            [['ver_code', 'ver_for'], 'string', 'max' => 100],
            ['ver_code', 'unique', 'targetAttribute' => ['ver_code', 'ezf_id']],
        ];
    }

    public function checkValue($attribute, $params) {

        if (preg_match('/^[a-z0-9_]+$/i', $attribute)) {   // เช็คว่าต้องข้อความต้องเป็นอังกฤษหรือตัวเลขเท่านั้น
            
        } else {
            $this->addError($attribute, Yii::t('ezform', 'Variables must be in English or numbers only and do not contain spaces.'));
        }
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
	return [
	    'ver_code' => Yii::t('ezforms', 'Version'),
	    'ver_for' => Yii::t('ezforms', 'Base'),
	    'ver_approved' => Yii::t('ezforms', 'Approved'),
	    'ver_active' => Yii::t('ezforms', 'Active'),
	    'approved_by' => Yii::t('ezforms', 'Approved By'),
	    'approved_date' => Yii::t('ezforms', 'Approved Date'),
	    'ver_options' => Yii::t('ezforms', 'Options'),
	    'ezf_id' => Yii::t('ezforms', 'EzForm'),
	    'field_detail' => Yii::t('ezforms', 'Field Detail'),
	    'ezf_sql' => Yii::t('ezforms', 'Sql'),
	    'ezf_js' => Yii::t('ezforms', 'Js'),
	    'ezf_error' => Yii::t('ezforms', 'Error'),
	    'ezf_options' => Yii::t('ezforms', 'Ezform Options'),
	    'updated_by' => Yii::t('ezforms', 'Updated By'),
	    'updated_at' => Yii::t('ezforms', 'Updated At'),
	    'created_by' => Yii::t('ezforms', 'Created By'),
	    'created_at' => Yii::t('ezforms', 'Created At'),
	];
    }
}
