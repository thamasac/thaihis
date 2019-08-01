<?php

namespace backend\modules\ezmodules\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
/**
 * This is the model class for table "ezmodule_rating".
 *
 * @property integer $rating_id
 * @property integer $ezm_id
 * @property integer $star
 * @property integer $created_by
 * @property string $created_at
 * @property integer $updated_by
 * @property string $updated_at
 */
class EzmoduleRating extends \yii\db\ActiveRecord
{
    public $total;
    
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
	return 'ezmodule_rating';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
	return [
            [['star', 'ezm_id'], 'required'],
            [['rating_id', 'ezm_id', 'star', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
	return [
	    'rating_id' => Yii::t('ezmodule', 'ID'),
	    'ezm_id' => Yii::t('ezmodule', 'Module'),
	    'star' => Yii::t('ezmodule', 'Rating'),
	    'created_by' => Yii::t('ezmodule', 'Created By'),
	    'created_at' => Yii::t('ezmodule', 'Created At'),
	    'updated_by' => Yii::t('ezmodule', 'Updated By'),
	    'updated_at' => Yii::t('ezmodule', 'Updated At'),
	];
    }
}
