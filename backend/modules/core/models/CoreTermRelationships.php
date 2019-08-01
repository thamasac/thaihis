<?php

namespace backend\modules\core\models;

use Yii;

/**
 * This is the model class for table "core_term_relationships".
 *
 * @property string $object_id
 * @property string $term_taxonomy_id
 * @property integer $term_order
 */
class CoreTermRelationships extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
	return 'core_term_relationships';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
	return [
            [['object_id', 'term_taxonomy_id'], 'required'],
            [['object_id', 'term_taxonomy_id', 'term_order'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
	return [
	    'object_id' => Yii::t('app', 'Object'),
	    'term_taxonomy_id' => Yii::t('app', 'Taxonomy'),
	    'term_order' => Yii::t('app', 'Order'),
	];
    }
}
