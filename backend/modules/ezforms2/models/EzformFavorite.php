<?php

namespace backend\modules\ezforms2\models;

use Yii;

/**
 * This is the model class for table "ezform_favorite".
 *
 * @property integer $ezf_id
 * @property integer $userid
 * @property integer $forder
 */
class EzformFavorite extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
	return 'ezform_favorite';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
	return [
            [['ezf_id', 'userid'], 'required'],
            [['ezf_id', 'userid', 'forder'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
	return [
	    'ezf_id' => Yii::t('ezform', 'Ezf ID'),
	    'userid' => Yii::t('ezform', 'Userid'),
	    'forder' => Yii::t('ezform', 'Forder'),
	];
    }
}
