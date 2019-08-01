<?php

namespace backend\modules\core\models;

use Yii;
use yii\behaviors\SluggableBehavior;
use backend\modules\core\models\CoreTermTaxonomy;

/**
 * This is the model class for table "core_terms".
 *
 * @property string $term_id
 * @property string $name
 * @property string $slug
 * @property integer $term_group
 */

class CoreTerms extends \yii\db\ActiveRecord
{
    public $tagsForm = [];
    
    public function behaviors()
    {
	return [
	    [
		'class' => SluggableBehavior::className(),
		'attribute' => 'name',
		'ensureUnique' => true,
		'immutable' => false,
	    ],
	];
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
	return 'core_terms';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
	return [
            [['term_group'], 'integer'],
            [['name', 'slug'], 'string', 'max' => 200],
            [['slug'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
	return [
	    'term_id' => Yii::t('app', 'ID'),
	    'name' => Yii::t('app', 'Name'),
	    'slug' => Yii::t('app', 'Slug'),
	    'term_group' => Yii::t('app', 'Group'),
	];
    }
    
    public function afterSave($insert, $changedAttributes)
    {
	$tagsForm = $this->tagsForm;
	if(!empty($tagsForm)){
	    $model = new CoreTermTaxonomy;

	    if(isset($tagsForm->term_taxonomy_id) && !empty($tagsForm->term_taxonomy_id)){
		$model = CoreTermTaxonomy::findOne($tagsForm->term_taxonomy_id);
	    } 

	    $model->attributes = $tagsForm->attributes;
	    if($insert){
		$model->term_id = (int) $this->term_id;
	    }
	    $model->parent = (int) $tagsForm->parent;
	    $model->count = (int) $tagsForm->count;
	    
	    $model->save();
	}
	
        parent::afterSave($insert, $changedAttributes);
    }
    
    public function afterDelete()
    {
	$model = CoreTermTaxonomy::find()->where('term_id=:id', ['id'=>$this->term_id])->one();
	$model->delete();
	Yii::$app->db->createCommand()
		->update('core_term_taxonomy', ['parent'=>$model->parent], 'parent=:id', [':id'=>$this->term_id])
		->execute();
	Yii::$app->db->createCommand()
		->delete('core_term_relationships', 'term_taxonomy_id=:id', [':id' => $model->term_taxonomy_id])
		->execute();
        
	parent::afterDelete();
    }
}
