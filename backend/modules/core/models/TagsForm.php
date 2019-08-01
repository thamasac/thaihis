<?php
namespace backend\modules\core\models;
/**
 * TagsForm class file UTF-8
 * @author SDII <iencoded@gmail.com>
 * @copyright Copyright &copy; 2015 AppXQ
 * @license http://www.appxq.com/license/
 * @version 1.0.0 Date: 2 ธ.ค. 2558 12:02:34
 * @link http://www.appxq.com/
 * @example 
 */
use Yii;
use yii\base\Model;
use backend\modules\core\classes\CoreFunc;

class TagsForm extends Model {
    
    public $term_id;
    public $name;
    public $slug;
    public $term_group;
    public $term_taxonomy_id;
    public $taxonomy;
    public $description;
    public $parent;
    public $count;
    
    public function rules()
    {
	return [
	    [['name'], 'required'],
	    [['term_id', 'term_group', 'term_taxonomy_id', 'parent', 'count'], 'integer'],
	    [['taxonomy'], 'string', 'max' => 32],
            [['name', 'slug'], 'string', 'max' => 200],
	    [['description'], 'string'],
	    [['parent'], 'compare', 'compareAttribute' => 'term_id', 'operator' => '!=='],     
	    ['term_id', 'compare'],

            //[['slug'], 'unique']
        ];
    }
    
    public function attributeLabels() {
	return [
	    'term_id' => CoreFunc::t('TermID'),
	    'name' => CoreFunc::t('Name'),
	    'slug' => CoreFunc::t('Slug'),
	    'term_group' => CoreFunc::t('Term Group'),
	    'term_taxonomy_id' => CoreFunc::t('TaxonomyID'),
	    'taxonomy' => CoreFunc::t('Taxonomy'),
	    'description' => CoreFunc::t('Description'),
	    'parent' => CoreFunc::t('Parent'),
	    'count' => CoreFunc::t('Post'),
	];
    }
    
    public function save()
    {
	try {
	    $model = new CoreTerms();
	    
	    if(!empty($this->term_id)){
		$model = CoreTerms::findOne($this->term_id);
	    } 
	    
	    $model->attributes = $this->attributes;
	    $model->term_group = (int) $model->term_group;
	    $model->tagsForm = $this;
	   
	    $model->save();
	    
	    return $model;
	} catch (\yii\db\Exception $e) {
	   return false;
	}
    }
    
}
