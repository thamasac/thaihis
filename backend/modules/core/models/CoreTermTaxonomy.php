<?php

namespace backend\modules\core\models;

use Yii;

/**
 * This is the model class for table "core_term_taxonomy".
 *
 * @property string $term_taxonomy_id
 * @property string $term_id
 * @property string $taxonomy
 * @property string $description
 * @property string $parent
 * @property integer $count
 */
class CoreTermTaxonomy extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
	return 'core_term_taxonomy';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
	return [
	    [['term_id', 'taxonomy'], 'required'],
            [['term_id', 'parent', 'count'], 'integer'],
            [['description'], 'string'],
            [['taxonomy'], 'string', 'max' => 32],
            [['term_id', 'taxonomy'], 'unique', 'targetAttribute' => ['term_id', 'taxonomy'], 'message' => 'The combination of Term and Taxonomy has already been taken.']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
	return [
	    'term_taxonomy_id' => Yii::t('app', 'ID'),
	    'term_id' => Yii::t('app', 'Term'),
	    'taxonomy' => Yii::t('app', 'Taxonomy'),
	    'description' => Yii::t('app', 'Description'),
	    'parent' => Yii::t('app', 'Parent'),
	    'count' => Yii::t('app', 'Count'),
	];
    }
}
