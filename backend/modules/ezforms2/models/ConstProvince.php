<?php

namespace backend\modules\ezforms2\models;

use Yii;

/**
 * This is the model class for table "const_province".
 *
 * @property integer $PROVINCE_ID
 * @property string $PROVINCE_CODE
 * @property string $PROVINCE_NAME
 * @property integer $GEO_ID
 */
class ConstProvince extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
	return 'const_province';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
	return [
            [['PROVINCE_CODE', 'PROVINCE_NAME'], 'required'],
            [['GEO_ID'], 'integer'],
            [['PROVINCE_CODE'], 'string', 'max' => 2],
            [['PROVINCE_NAME'], 'string', 'max' => 150]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
	return [
	    'PROVINCE_ID' => Yii::t('ezform', 'Province  ID'),
	    'PROVINCE_CODE' => Yii::t('ezform', 'Province  Code'),
	    'PROVINCE_NAME' => Yii::t('ezform', 'Province  Name'),
	    'GEO_ID' => Yii::t('ezform', 'Geo  ID'),
	];
    }
}
