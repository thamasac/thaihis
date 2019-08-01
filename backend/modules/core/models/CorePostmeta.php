<?php

namespace backend\modules\core\models;

use Yii;

/**
 * This is the model class for table "core_postmeta".
 *
 * @property string $meta_id
 * @property string $post_id
 * @property string $meta_key
 * @property string $meta_value
 */
class CorePostmeta extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
	return 'core_postmeta';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
	return [
            [['post_id'], 'integer'],
            [['meta_value'], 'safe'],
            [['meta_key'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
	return [
	    'meta_id' => Yii::t('core', 'ID'),
	    'post_id' => Yii::t('core', 'Post'),
	    'meta_key' => Yii::t('core', 'Key'),
	    'meta_value' => Yii::t('core', 'Value'),
	];
    }
}
