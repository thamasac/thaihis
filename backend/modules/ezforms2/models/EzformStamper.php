<?php

namespace backend\modules\ezforms2\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "ezform_stamper".
 *
 * @property integer $stamper_id
 * @property integer $auto_id
 * @property string $auto_num
 * @property integer $ezf_id
 * @property integer $ezf_field_id
 * @property integer $dataid
 * @property string $xsourcex
 * @property string $created_at
 * @property integer $created_by
 * @property string $updated_at
 * @property integer $updated_by
 */
class EzformStamper extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
	return 'ezform_stamper';
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
    public function rules()
    {
	return [
            [['stamper_id'], 'required'],
            [['stamper_id', 'auto_id', 'ezf_id', 'ezf_field_id', 'dataid', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['auto_num'], 'string', 'max' => 150],
            [['xsourcex'], 'string', 'max' => 10],
            [['stamper_id'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
	return [
	    'stamper_id' => Yii::t('ezform', 'Stamper ID'),
	    'auto_id' => Yii::t('ezform', 'Auto ID'),
	    'auto_num' => Yii::t('ezform', 'Auto Num'),
	    'ezf_id' => Yii::t('ezform', 'Ezf ID'),
	    'ezf_field_id' => Yii::t('ezform', 'Ezf Field ID'),
	    'dataid' => Yii::t('ezform', 'Dataid'),
	    'xsourcex' => Yii::t('ezform', 'Xsourcex'),
	    'created_at' => Yii::t('ezform', 'Created At'),
	    'created_by' => Yii::t('ezform', 'Created By'),
	    'updated_at' => Yii::t('ezform', 'Updated At'),
	    'updated_by' => Yii::t('ezform', 'Updated By'),
	];
    }
}
