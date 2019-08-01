<?php

namespace backend\modules\ezmodules\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
/**
 * This is the model class for table "ezmodule_filter".
 *
 * @property integer $filter_id
 * @property string $filter_name
 * @property integer $ezm_id
 * @property string $sitecode
 * @property integer $public
 * @property string $share
 * @property string $options
 * @property integer $filter_order
 * @property integer $ezm_default
 * @property integer $created_by
 * @property string $created_at
 * @property integer $updated_by
 * @property string $updated_at
 * @property integer $filter_type
 */
class EzmoduleFilter extends \yii\db\ActiveRecord
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
        ];
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
	return 'ezmodule_filter';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
	return [
            [['filter_name', 'ezm_id'], 'required'],
            [['filter_id', 'filter_type', 'ezm_id', 'public', 'filter_order', 'ezm_default', 'created_by', 'updated_by'], 'integer'],
            //[[], 'string'],
            [['created_at', 'updated_at', 'share', 'options'], 'safe'],
            [['filter_name'], 'string', 'max' => 100],
            [['sitecode'], 'string', 'max' => 10]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
	return [
	    'filter_id' => Yii::t('ezmodule', 'ID'),
	    'filter_name' => Yii::t('ezmodule', 'Name'),
	    'ezm_id' => Yii::t('ezmodule', 'Module'),
	    'sitecode' => Yii::t('ezmodule', 'Sitecode'),
	    'public' => Yii::t('ezmodule', 'Public'),
	    'share' => Yii::t('ezmodule', 'Share'),
	    'options' => Yii::t('ezmodule', 'Options'),
	    'filter_order' => Yii::t('ezmodule', 'Order'),
            'filter_type' => Yii::t('ezmodule', 'Filter Type'),
	    'ezm_default' => Yii::t('ezmodule', 'Default'),
	    'created_by' => Yii::t('ezmodule', 'Created By'),
	    'created_at' => Yii::t('ezmodule', 'Created At'),
	    'updated_by' => Yii::t('ezmodule', 'Updated By'),
	    'updated_at' => Yii::t('ezmodule', 'Updated At'),
	];
    }
}
