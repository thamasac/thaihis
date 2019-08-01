<?php

namespace backend\modules\ezmodules\models;

use Yii;

/**
 * This is the model class for table "ezmodule_favorite".
 *
 * @property integer $fav_id
 * @property integer $ezm_id
 * @property integer $user_id
 */
class EzmoduleFavorite extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
	return 'ezmodule_favorite';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
	return [
            [['fav_id', 'ezm_id', 'user_id'], 'required'],
            [['fav_id', 'ezm_id', 'user_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
	return [
	    'fav_id' => Yii::t('ezmodule', 'Fav ID'),
	    'ezm_id' => Yii::t('ezmodule', 'Ezm ID'),
	    'user_id' => Yii::t('ezmodule', 'User ID'),
	];
    }
}
