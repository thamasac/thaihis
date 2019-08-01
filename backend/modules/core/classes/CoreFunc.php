<?php

namespace backend\modules\core\classes;

/**
 * CoreFunc class file UTF-8
 * @author SDII <iencoded@gmail.com>
 * @link http://www.appxq.com/
 * @copyright Copyright &copy; Error: on line 6, column 34 in Templates/Scripting/PHPClass.php
  The string doesn't match the expected date/time format. The string to parse was: "2 เม.ย. 2557". The expected format was: "MMM d, yyyy". AppXQ
 * @license http://www.appxq.com/license/
 * @package 
 * @version 1.0.0 Date: 2 เม.ย. 2557 13:07:28
 */
use Yii;
use appxq\sdii\models\DynamicModel;
use appxq\sdii\utils\SDUtility;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\data\ArrayDataProvider;
use yii\helpers\Json;
use backend\modules\core\models\CoreTerms;
use backend\modules\core\models\CoreTermTaxonomy;
use backend\modules\core\models\CorePostmeta;
use backend\modules\core\models\CoreTermRelationships;
use backend\modules\core\models\CorePosts;

class CoreFunc {

    public static function setDynamicModel($fields) {
	$attributes = [];
	$labels = [];
	$required = [];
	$rules = [];
	$rulesFields = [];
	if (!empty($fields)) {
	    foreach ($fields as $value) {
		//Attributes array
		$attributes[$value['option_name']] = $value['option_value'];
		//Labels array
		$labels[$value['option_name']] = isset($value['input_label']) ? $value['input_label'] : $value['option_name'];
		//Rule array required
		if ($value['input_required'] == 1) {
		    $required[] = $value['option_name'];
		}
		//Rule array validate
		$validateArray = @unserialize($value['input_validate']);
		if (is_array($validateArray)) {
		    $addRule = false;
		    foreach ($validateArray as $keyRule => $valueRule) {
			if (is_array($valueRule)) {
			    $name = self::getRuleName($valueRule);
			    $rulesFields[$name][] = $value['option_name'];
			    $rules[$name] = $valueRule;
			} else {
			    $addRule = true;
			    break;
			}
		    }

		    if ($addRule) {
			$name = self::getRuleName($validateArray);
			$rulesFields[$name][] = $value['option_name'];
			$rules[$name] = $validateArray;
		    }
		} else {
		    $rulesFields['safe'][] = $value['option_name'];
		    $rules['safe'] = ['safe'];
		}
	    }
	}

	$model = new DynamicModel($attributes);
	foreach ($rules as $key => $value) {
	    $options = [];
	    if(isset($value) && count($value)>1){
		$options = $value;
		ArrayHelper::remove($options, 0);
	    }
	    $model->addRule($rulesFields[$key], $value[0], $options);
	}
	$model->addRule($required, 'required');
	$model->addLabel($labels);

	return $model;
    }

    private static function getRuleName($rule) {
	$name = $rule[0];
	if (count($rule) > 1) {
	    $name = '';
	    foreach ($rule as $key => $value) {
		if (is_integer($key)) {
		    $name .= $value;
		} else {
		    $name .= $key . $value;
		}
	    }
	}
	return $name;
    }

    public static function generateInput($option, $model, $form, $field_name = 'option_name') {
	$options = ArrayHelper::merge(SDUtility::string2Array($option['field_meta']), SDUtility::string2Array($option['input_meta']));
	$specific = SDUtility::string2Array((isset($option['input_specific'])?$option['input_specific']:''));
        
	if ($option['field_name'] !== NULL) {
	    if ($option['field_internal'] == 1) {
                //\appxq\sdii\utils\VarDumper::dump($option[$field_name]);
		$html = '';
		if ($option['field_name'] == 'widget') {
		    eval("\$html = \$form->field(\$model, '{$option[$field_name]}')->hint(\Yii::t('chanpan','{$option['input_hint']}'))->{$option['field_name']}({$option['field_class']}, \$options)->label(\Yii::t('chanpan','{$option['input_label']}'));");
		} else {
		    if (empty($option['input_data'])) {
                        //\appxq\sdii\utils\VarDumper::dump($option);                        
                        eval("\$html = \$form->field(\$model, '{$option[$field_name]}', \$specific)->hint(\$option['input_hint'])->{$option['field_name']}(\$options)->label(\Yii::t('chanpan','{$option['input_label']}'));");
		    } else {
                        
			eval("\$html = \$form->field(\$model, '{$option[$field_name]}', \$specific)->hint(\$option['input_hint'])->{$option['field_name']}({$option['input_data']}, \$options)->label(\Yii::t('chanpan','{$option['input_label']}'));");
		    }
		}

		return $html;
	    } else {
		$options = SDUtility::string2Array($option['input_meta']);
		$html = '';
                
		if ($option['field_name'] == 'widget') {
		    $options['model'] = $model;
		    $options['attribute'] = $option[$field_name];
		    eval("\$html = {$option['field_class']}::{$option['field_name']}(\$options);");
		} else {
                    
		    if (empty($option['input_data'])) {
			eval("\$html = {$option['field_class']}::{$option['field_name']}(\$model, '{$option[$field_name]}', \$options);");
		    } else {
                        
			eval("\$html = {$option['field_class']}::{$option['field_name']}(\$model, '{$option[$field_name]}', {$option['input_data']}, \$options);");
		    }
		}
                
		return $html;
	    }
	} else {
	    return $form->field($model, $option[$field_name], $specific)->hint($option['input_hint'])->textInput($options);
	}
    }

    public static function getTempList($type, $code = NULL) {
	$_items = [
	    'autoload' => [
		'yes' => 'Yes',
		'no' => 'No',
	    ],
	];
	if (isset($code)) {
	    return isset($_items[$type][$code]) ? $_items[$type][$code] : false;
	} else {
	    return isset($_items[$type]) ? $_items[$type] : false;
	}
    }

    public static function getTranslated($fields) {
	$arr = [];
	if (!empty($fields)) {
	    foreach ($fields as $key => $value) {
		$arr['{' . $key . '}'] = $fields[$key];
	    }
	}
	return $arr;
    }

    public static function itemAlias($code, $key = NULL) {
	$itemStr = CoreQuery::getItemAlias($code);
	
        if($itemStr!=''){
            eval("\$return = $itemStr;");
        }

	if (isset($key)) {
	    return isset($return[$key]) ? $return[$key] : [];
	} else {
	    return isset($return) ? $return : [];
	}
    }

    public static function alterTableAdd($model) {
	return CoreQuery::addColumn($model['table_name'], $model['table_varname'], $model['table_field_type'], $model['table_length'], $model['table_default'], $model['table_index']);
    }

    public static function alterTableChange($model) {
	return CoreQuery::alterColumn($model['table_name'], $model['table_varname'], $model['table_field_type'], $model['table_length'], $model['table_default'], $model['table_index']);
    }

    public static function alterTableDrop($model) {
	return CoreQuery::dropColumn($model['table_name'], $model['table_varname']);
    }

    public static function getTableRulesSearch($table) {
	$integer = [];
	$safe = [];
	$dynamicFields = CoreQuery::getTableFields($table);
	foreach ($dynamicFields as $key => $value) {
	    if(in_array(strtoupper($value['table_field_type']), ['VARCHAR', 'TEXT', 'DATETIME'])) {
		$safe[] = $value['table_varname'];
	    } else {
		$integer[] = $value['table_varname'];
	    }
	}
	
	$rules = [];
	if (!empty($safe)) {
	    $rules[] = [$safe, 'safe'];
	}
	
	if (!empty($integer)) {
	    $rules[] = [$integer, 'integer'];
	}
	
	return $rules;
    }
    
    public static function getTableRules($table) {
	$required = [];
	$rulesType = [];
	$rulesFields = [];
	$dynamicFields = CoreQuery::getTableFields($table);
	foreach ($dynamicFields as $key => $value) {
	    if ($value['input_required'] == 1) {
		$required[] = $value['table_varname'];
	    }
	    //Rule array validate
	    $validateArray = @unserialize($value['input_validate']);
	    if (is_array($validateArray)) {
		$addRule = false;
		foreach ($validateArray as $keyRule => $valueRule) {
		    if (is_array($valueRule)) {
			$name = self::getRuleName($valueRule);
			$rulesFields[$name][] = $value['table_varname'];
			$rulesType[$name] = $valueRule;
		    } else {
			$addRule = true;
			break;
		    }
		}

		if ($addRule) {
		    $name = self::getRuleName($validateArray);
		    $rulesFields[$name][] = $value['table_varname'];
		    $rulesType[$name] = $validateArray;
		}
	    } else {
		$rulesFields['safe'][] = $value['table_varname'];
		$rulesType['safe'] = ['safe'];
	    }
	}

	$rules = [];
	foreach ($rulesType as $key => $value) {
	    $rules[] = array_merge([$rulesFields[$key]], $value);
	}

	if (!empty($required)) {
	    $rules[] = [$required, 'required'];
	}

	return $rules;
    }

    public static function t($str = '', $params = array(), $dic = 'core') {
	return Yii::t('Core.' . $dic, $str, $params);
    }

    private static function getSpace($count, $space = '&nbsp;') {//&nbsp;
	$str = '';
	for ($i = 0; $i < $count; $i++) {
	    $str = $str . $space;
	}
	return $str;
    }

    public static function getPostDropDownList($id = 0, $type = 'page', $status = '') {
	$dataParent = CoreQuery::getPostParent($type, $status, $id);
	$arrReturn = self::arrayPostChild($dataParent, [], 0, $id);

	return $arrReturn;
    }

    public static function getPostDataProvider($title = '', $status = 'publish', $type = 'page') {
	$arrReturn = [];
	if (empty($title)) {
	    $dataParent = CoreQuery::getPostParent($type, $status);
	    $arrReturn = self::arrayPostChild($dataParent, [], 0, 0, $status, 'dataprovider');

	    $dataProvider = new ArrayDataProvider([
		'allModels' => $arrReturn,
		'key' => 'ID',
		'pagination' => [
		    'pageSize' => 30,
		],
	    ]);
	} else {
	    $dataProvider = CoreQuery::getPostsDataProvider($title, $status, $type);
	}

	return $dataProvider;
    }

    private static function arrayPostChild($parent, $arrReturn, $lvl, $id = 0, $status = '', $type = 'dropdown') {
	foreach ($parent as $value) {
	    if ($type == 'dropdown') {
		$arrReturn[$value['ID']] = self::getSpace($lvl * 3) . Html::encode($value['post_title']);
	    } else {
		$arrReturn[] = [
		    'ID' => $value['ID'],
		    'post_title' => self::getSpace($lvl * 1, '— ') . $value['post_title'],
		    'post_author' => $value['post_author'],
		    'post_status' => $value['post_status'],
		    'post_modified' => $value['post_modified'],
		    'post_parent' => $value['post_parent'],
		    'menu_order' => $value['menu_order'],
		    'post_type' => $value['post_type'],
		    'comment_count' => $value['comment_count'],
		    'post_password' => $value['post_password'],
		    'parent_title' => isset($value['parent_title']) ? $value['parent_title'] : '',
		];
	    }
	    if ($value['child'] > 0) {
		$dataChild = CoreQuery::getPostChild($value['ID'], $id, $value['post_type'], $status);

		if ($dataChild) {
		    $arrReturn = self::arrayPostChild($dataChild, $arrReturn, $lvl + 1, $id, $status, $type);
		}
	    }
	}
	return $arrReturn;
    }

    public static function getTaxonomyDropDownList($term = 0, $taxonomy = 'category') {
	$dataParent = CoreQuery::getTaxonomyParent($taxonomy, $term);
	$arrReturn = self::arrayTaxonomyChild($dataParent, [], 0, $term, 'dropdown');

	return $arrReturn;
    }

    public static function getTaxonomyCheckList($term = 0, $taxonomy = 'category') {
	$dataParent = CoreQuery::getTaxonomyParent($taxonomy, $term);
	$arrReturn = self::arrayTaxonomyChild($dataParent, [], 0, $term, 'checklist');

	return $arrReturn;
    }

    public static function getTaxonomyDataProvider($taxonomy = 'category', $name = '') {
	$arrReturn = [];
	if (empty($name)) {
	    $dataParent = CoreQuery::getTaxonomyParent($taxonomy);
	    $arrReturn = self::arrayTaxonomyChild($dataParent, [], 0, 0, 'dataprovider');

	    $dataProvider = new ArrayDataProvider([
		'allModels' => $arrReturn,
		'key' => 'term_id',
		'pagination' => [
		    'pageSize' => 30,
		],
	    ]);
	} else {
	    $dataProvider = CoreQuery::getTaxonomyDataProvider($name, $taxonomy);
	}

	return $dataProvider;
    }

    private static function arrayTaxonomyChild($parent, $arrReturn, $lvl, $term = 0, $type = 'dropdown') {
	foreach ($parent as $value) {
	    if ($type == 'dropdown') {
		$arrReturn[$value['term_id']] = self::getSpace($lvl * 3) . Html::encode($value['name']);
	    } elseif ($type == 'checklist') {
		$arrReturn[$value['term_id']] = self::getSpace($lvl * 1, '— ') . Html::encode($value['name']);
	    } else {
		$arrReturn[] = [
		    'term_id' => $value['term_id'],
		    'name' => self::getSpace($lvl * 1, '— ') . $value['name'],
		    'slug' => $value['slug'],
		    'term_group' => $value['term_group'],
		    'term_taxonomy_id' => $value['term_taxonomy_id'],
		    'taxonomy' => $value['taxonomy'],
		    'description' => $value['description'],
		    'parent' => $value['parent'],
		    'count' => $value['count'],
		];
	    }
	    if ($value['child'] > 0) {
		$dataChild = CoreQuery::getTaxonomyChild($value['term_id'], $term);

		if ($dataChild) {
		    $arrReturn = self::arrayTaxonomyChild($dataChild, $arrReturn, $lvl + 1, $term, $type);
		}
	    }
	}
	return $arrReturn;
    }

    public static function nameToValue($name) {
	if ($name == 'open') {
	    return 1;
	}

	return 0;
    }

    public static function valueToName($value) {
	if ($value) {
	    return 'open';
	}

	return 'closed';
    }

    public static function getPostMetaValue($post_id, $meta_key) {
	$model = CoreQuery::getPostMetaByPostKey($post_id, $meta_key);
	if (!$model) {
	    return null;
	}
	return $model->meta_value;
    }

    public static function getStickyPost($id) {

	$objSticky = CoreQuery::getOptions('sticky_posts');
	if (!empty($objSticky->option_value)) {
	    $sticky = Json::decode($objSticky->option_value);
	    return in_array($id, $sticky);
	}
	return false;
    }

    public static function getTermRelationships($object_id, $taxonomy, $returnArray = true) {
	$data = CoreQuery::getTaxonomyRelationships($object_id, $taxonomy);
	$returnData;
	if ($returnArray) {
	    $returnData = [];
	    foreach ($data as $value) {
		$returnData[] = $value['term_taxonomy_id'];
	    }
	} else {
	    $returnData = '';
	    $comma = '';
	    foreach ($data as $value) {
		$returnData .= $comma . $value['term_taxonomy_id'] . ':' . $value['name'];
		$comma = ',';
	    }
	}

	return $returnData;
    }

    public static function saveTerm($taxonomyOBJ) {
	$model = new CoreTerms;

	if (!empty($taxonomyOBJ['term_id'])) {
	    $model = CoreTerms::find()->where(['term_id' => $taxonomyOBJ['term_id']])->one();
	}

	$model->name = $taxonomyOBJ['name'];
	$model->slug = $taxonomyOBJ['slug'];
	$model->term_group = (int) $taxonomyOBJ['term_group'];

	$model->save();

	$taxonomyOBJ['term_id'] = $model->term_id;
	$modelTaxonomy = self::saveTaxonomy($taxonomyOBJ);

	return $modelTaxonomy;
    }

    private static function saveTaxonomy($taxonomyOBJ) {
	$model = new CoreTermTaxonomy;

	if (!empty($taxonomyOBJ['term_taxonomy_id'])) {
	    $model = CoreTermTaxonomy::find()->where(['term_taxonomy_id' => $taxonomyOBJ['term_taxonomy_id']])->one();
	}

	$model->term_id = (int) $taxonomyOBJ['term_id'];
	$model->taxonomy = $taxonomyOBJ['taxonomy'];
	$model->description = $taxonomyOBJ['description'];
	$model->parent = (int) $taxonomyOBJ['parent'];
	$model->count = (int) $taxonomyOBJ['count'];

	$model->save();

	$taxonomyOBJ['term_taxonomy_id'] = $model->term_taxonomy_id;
	return $taxonomyOBJ;
    }

    public static function getConvertLanguage() {
	if (Yii::$app->language == 'en-US') {
	    $language = NULL;
	} elseif (Yii::$app->language == 'th') {
	    $language = 'th_TH';
	} elseif (Yii::$app->language == 'ja-JP') {
	    $language = 'ja';
	} elseif (Yii::$app->language == 'ko') {
	    $language = 'ko_KR';
	} else {
	    $language = Yii::$app->language;
	}

	return $language;
    }

    public static function savePostMeta($post_id, $meta_key, $value) {
	$model = CoreQuery::getPostMetaByPostKey($post_id, $meta_key);
	if (!$model) {
	    $model = new CorePostmeta;
	    $model->post_id = $post_id;
	    $model->meta_key = $meta_key;
	}
	$model->meta_value = $value;
	$model->save();

	return $model;
    }

    public static function saveStickyPost($id, $value) {
	$sticky = [];
	$modelSticky = CoreQuery::getOptions('sticky_posts');
	if (!empty($modelSticky->option_value)) {
	    $sticky = Json::decode($modelSticky->option_value);
	}
	if (in_array($id, $sticky)) {
	    if ($value) {
		return false;
	    } else {
		unset($sticky[array_search($id, $sticky)]);
	    }
	} else {
	    if ($value) {
		array_push($sticky, $id);
	    } else {
		return false;
	    }
	}
	$sticky = array_values($sticky);
	$modelSticky->option_value = Json::encode($sticky);
	$modelSticky->save();

	return true;
    }

    public static function addTermRelationships($object_id, $term_taxonomy, $taxonomy) {
	$data = CoreQuery::getTaxonomyRelationships($object_id, $taxonomy);
	$term_order = ($data) ? $data[count($data) - 1]['term_order'] + 1 : 1;
	//del old
	if ($data) {
	    foreach ($term_taxonomy as $index => $id) {
		foreach ($data as $key => $value) {
		    if ($id == $value['term_taxonomy_id']) {
			CoreQuery::delTaxonomyRelationships($value['object_id'], $value['term_taxonomy_id']);
			unset($term_taxonomy[$index]);
			unset($data[$key]);
		    }
		}
	    }
	}
	//add new
	foreach ($term_taxonomy as $id) {
	    if ($id > 0) {
		$model = new CoreTermRelationships;

		$model->object_id = $object_id;
		$model->term_taxonomy_id = $id;
		$model->term_order = $term_order;
		$model->save();
		$term_order++;
	    }
	}
	//del old
	if ($data) {
	    foreach ($data as $value) {
		CoreQuery::delTaxonomyRelationships($value['object_id'], $value['term_taxonomy_id']);
	    }
	}

	return true;
    }

    public static function deletePostByTrash($id, $type) {
	$model = CorePosts::model()->findByPk($id);
	if ($model) {
	    if (!in_array($type, Yii::$app->controller->module->hasParentPost)) {
		self::saveStickyPost($id, false);
	    }

	    if ($model->post_status == 'trash') {
		$model->delete();

		Yii::$app->db->createCommand()->update('core_posts', ['post_parent' => $model->post_parent], 'post_parent=:id', [':id' => $id]);

		Yii::$app->db->createCommand()->delete('core_term_relationships', 'object_id=:id', [':id' => $id]);

		CoreQuery::deletePostMetaByPostId($id);

		if (!in_array($type, Yii::$app->controller->module->hasParentPost)) {
		    $categories = self::getTermRelationships($id, 'category');
		    $tags_id = self::getTermRelationships($id, 'post_tag', false);

		    if (is_array($categories)) {
			self::addTermRelationships($id, $categories, 'category');
		    }

		    if ($tags_id != '') {
			$tagsArr = explode(',', $tags_id);
			self::addTermRelationships($id, $tagsArr, 'post_tag');
		    }
		}
	    } else {
		self::savePostMeta($model->ID, 'trash_meta_status', $model->post_status);
		self::savePostMeta($model->ID, 'trash_meta_time', time());
		$model->post_status = 'trash';
		$model->save();
	    }
	}
	return $model;
    }

    public static function deleteTrashMetaByPostId($id) {
	CoreQuery::deleteTrashMetaByPostKey($id, 'trash_meta_status');
	CoreQuery::deleteTrashMetaByPostKey($id, 'trash_meta_time');
    }

    //--------------- start-----------
    /**
     * 
     * @param type $name
     * @param type $profield if $profield != null and '' return string
     * @return type object or string
     */
    public static function getParams($name, $profield=''){
        if(isset($profield) && !empty($profield)){
            return isset(\Yii::$app->params[$name]) ? \Yii::$app->params[$name] : '';
        }
        return isset(Yii::$app->params['profilefields'][$name])?Yii::$app->params['profilefields'][$name]:'';
    }
    
    public static function setTokenOption(){
        $model = CoreQuery::getOptions('token_expired');
        if(!$model){
            $model = new \backend\modules\core\models\CoreOptions();
            $model->option_name = 'token_expired';
            $model->input_label = 'Expired Content';
            $model->option_value = '<div class="alert alert-danger" role="alert">The token is invalid or expired</div>';
            $model->input_field = 'HTMLEditor';
            $model->input_order = 5;
            $model->autoload = 'no';
            $model->save();
        }
        
        $model = CoreQuery::getOptions('token_new');
        if(!$model){
            $model = new \backend\modules\core\models\CoreOptions();
            $model->option_name = 'token_new';
            $model->input_label = 'Button New Record';
            $model->option_value = 'Back to the new record';
            $model->input_field = 'TextInput';
            $model->input_order = 2;
            $model->autoload = 'no';
            $model->save();
        }
        
        $model = CoreQuery::getOptions('token_register');
        if(!$model){
            $model = new \backend\modules\core\models\CoreOptions();
            $model->option_name = 'token_register';
            $model->input_label = 'Button Register';
            $model->option_value = 'Register Click!';
            $model->input_field = 'TextInput';
            $model->input_order = 3;
            $model->autoload = 'no';
            $model->save();
        }
        
        $model = CoreQuery::getOptions('token_content');
        if(!$model){
            $model = new \backend\modules\core\models\CoreOptions();
            $model->option_name = 'token_content';
            $model->input_label = 'Footer Content';
            $model->option_value = '';
            $model->input_field = 'HTMLEditor';
            $model->input_order = 4;
            $model->autoload = 'no';
            $model->save();
        }
        
        $model = CoreQuery::getOptions('token_thanks');
        if(!$model){
            $model = new \backend\modules\core\models\CoreOptions();
            $model->option_name = 'token_thanks';
            $model->input_label = 'Top Content';
            $model->option_value = '<div class="row"><div class="col-md-12 text-center"><h1>Thanks for the information</h1></div></div>';
            $model->input_field = 'HTMLEditor';
            $model->input_order = 1;
            $model->autoload = 'no';
            $model->save();
        }
    }
    
    
    /**
     * 
     * @param type string $name params name example   $name = 'step;
     * @param type array  $data example $data=['option_value'=>2];
     * @return type
     */
    public static function updateCoreOptionValueByName($name, $data, $dbnameClone=""){
        
        try {
            if($dbnameClone != ""){
                $dataUpdate = \Yii::$app->db->createCommand()
                ->update("{$dbnameClone}.core_options", $data, "option_name=:option_name", [
                    ":option_name"=>$name
                ])
                ->execute();
            }else{
                $dataUpdate = \Yii::$app->db->createCommand()
                ->update("core_options", $data, "option_name=:option_name", [
                    ":option_name"=>$name
                ])
                ->execute();
            }
            if($dataUpdate){return true;}
            return false;
        } catch (\yii\db\Exception $error) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($error);
            return FALSE;
        }
        return $dataUpdate;
    }
    
    /**
     * 
     * @param type string $name params name example   $name = 'step';
     * @param type array  $data example  $data=['option_value'=>3];
     * @return type
     */
    public static function updateCoreOptionValueByNameInDb($name, $data){        
        try {
             $dataUpdate = \Yii::$app->db->createCommand()
                ->update("core_options", $data, "option_name=:option_name", [
                    ":option_name"=>$name
                ])
                ->execute();
            if($dataUpdate){return true;}
            return false;
        } catch (\yii\db\Exception $error) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($error);
            return FALSE;
        }
        return $dataUpdate;
    }
    
}
