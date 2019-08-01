<?php

namespace backend\modules\core\classes;

/**
 * CoreQuery class file UTF-8
 * @author SDII <iencoded@gmail.com>
 * @link http://www.appxq.com/
 * @copyright Copyright &copy; Error: on line 6, column 34 in Templates/Scripting/PHPClass.php
  The string doesn't match the expected date/time format. The string to parse was: "2 เม.ย. 2557". The expected format was: "MMM d, yyyy". AppXQ
 * @license http://www.appxq.com/license/
 * @package 
 * @version 1.0.0 Date: 2 เม.ย. 2557 13:07:28
 */
use Yii;
use backend\modules\core\models\CoreOptions;
use backend\modules\core\models\CoreFields;
use backend\modules\core\models\TablesFields;
use yii\data\SqlDataProvider;
use backend\modules\core\models\CorePostmeta;
use yii\helpers\Inflector;

class CoreQuery {

    public static function getOptionsParams() {
	$model = CoreOptions::find()
		->select('option_name, option_value')
		->all();
        if($model){
            $params = \yii\helpers\ArrayHelper::map($model, 'option_name', 'option_value');
            return $params;
        }
	return [];
    }
    
    public static function getAllOptions($term) {
	$model = CoreOptions::find()
		->select('core_options.*, core_fields.field_internal, core_fields.field_class, core_fields.field_name, core_fields.field_meta')
		->where('option_name LIKE :option_name', [':option_name' => $term . '%'])
		->leftJoin('core_fields', 'core_fields.field_code=core_options.input_field')
		->orderBy('input_order, option_name')
		->all();
	return $model;
    }

    public static function getOptions($optionName) {
	$model = CoreOptions::find()
		->where('option_name = :option_name', [':option_name' => $optionName])
		->one();
	return $model;
    }

    public static function updateOptions($optionName, $optionValue) {
	$model = self::getOptions($optionName);
	$model->option_value = $optionValue;
	$model->update();

	return $model;
    }

    public static function getFields($code) {
	$model = CoreFields::find()
		->where('field_code = :field_code', [':field_code' => $code])
		->one();
	return $model;
    }

    public static function getItemAlias($code) {
	$sql = "SELECT item_data
		FROM core_item_alias
		WHERE item_code = :code";

	return Yii::$app->db->createCommand($sql, [':code' => $code])->queryScalar();
    }

    public static function addColumn($table, $column, $field_type = '', $length = 0, $default = '', $index = '') {
	$strLen = '';
	$strIndex = '';

	if ($length > 0) {
	    $strLen = "($length)";
	}

	if (strlen($index) > 0) {
	    $strIndex = ", ADD $index (`$column`)";
	}

	$comment = Inflector::camel2words($column);

	$type = "$field_type $strLen NOT NULL COMMENT '$comment' $strIndex";

	return Yii::$app->db->createCommand()->addColumn($table, $column, $type)->execute();
    }

    public static function alterColumn($table, $column, $field_type = '', $length = 0, $default = '', $index = '') {
	$strLen = '';
	$strIndex = '';

	if ($length > 0) {
	    $strLen = "($length)";
	}

	if (strlen($index) > 0) {
	    $strIndex = ", ADD $index (`$column`)";
	}

	$comment = Inflector::camel2words($column);

	$type = "$field_type $strLen NOT NULL COMMENT '$comment' $strIndex";

	return Yii::$app->db->createCommand()->alterColumn($table, $column, $type)->execute();
    }

    public static function dropColumn($table, $column) {
	return Yii::$app->db->createCommand()->dropColumn($table, $column)->execute();
    }

    public static function getTableFields($table) {
	$fields = TablesFields::find()
		->where('table_name = :table_name', [':table_name' => $table])
		->all();

	return $fields;
    }

    public static function getAllOptionsTable($table) {
	$sql = "SELECT tables_fields.*, 
			core_fields.field_meta, 
			core_fields.field_internal, 
			core_fields.field_class, 
			core_fields.field_name
		FROM core_fields INNER JOIN tables_fields ON core_fields.field_code = tables_fields.input_field
		WHERE tables_fields.table_name = :table_name
		ORDER BY input_order, input_label";

	return Yii::$app->db->createCommand($sql, [':table_name' => $table])->queryAll();
    }

    public static function getTaxonomyParent($taxonomy = 'category', $term = 0) {
	$sql = "SELECT core_terms.term_id, 
				core_terms.`name`, 
				core_terms.slug, 
				core_terms.term_group, 
				core_term_taxonomy.term_taxonomy_id, 
				core_term_taxonomy.taxonomy, 
				core_term_taxonomy.description, 
				core_term_taxonomy.parent, 
				core_term_taxonomy.count,
				SUM(IFNULL(tmp_child.parent,0)) AS child	
			FROM core_terms INNER JOIN core_term_taxonomy ON core_terms.term_id = core_term_taxonomy.term_id
			INNER JOIN (SELECT p.term_id,
				p.parent,
				p.term_taxonomy_id,
				(SELECT COUNT(*)
				FROM  core_term_taxonomy c
				WHERE c.term_id = p.parent AND c.taxonomy = :taxonomy
				) AS lvl
			FROM core_term_taxonomy p
			WHERE p.taxonomy = :taxonomy
			) parent ON core_terms.term_id = parent.term_id
			LEFT JOIN (SELECT core_term_taxonomy.parent
				FROM core_term_taxonomy
			) tmp_child ON tmp_child.parent = core_terms.term_id
			WHERE lvl = 0 AND core_term_taxonomy.term_id <> :term
			GROUP BY core_terms.term_id
			ORDER BY core_terms.`name`";

	return Yii::$app->db->createCommand($sql, [':taxonomy' => $taxonomy, ':term' => $term])->queryAll();
    }

    public static function getTaxonomyChild($parent, $term = 0) {
	$sql = "SELECT core_terms.term_id, 
				core_terms.`name`, 
				core_terms.slug, 
				core_terms.term_group, 
				core_term_taxonomy.term_taxonomy_id, 
				core_term_taxonomy.taxonomy, 
				core_term_taxonomy.description, 
				core_term_taxonomy.parent, 
				core_term_taxonomy.count,
				SUM(IFNULL(tmp_child.parent,0)) AS child	
			FROM core_terms INNER JOIN core_term_taxonomy ON core_terms.term_id = core_term_taxonomy.term_id
			LEFT JOIN (SELECT core_term_taxonomy.parent
				FROM core_term_taxonomy
			) tmp_child ON tmp_child.parent = core_terms.term_id
			WHERE core_term_taxonomy.parent = :parent AND core_term_taxonomy.term_id <> :term
			GROUP BY core_terms.term_id
			ORDER BY core_terms.`name`";

	return Yii::$app->db->createCommand($sql, [':parent' => $parent, ':term' => $term])->queryAll();
    }

    public static function getTaxonomyDataProvider($name, $taxonomy = 'category') {
	$count = Yii::$app->db->createCommand("SELECT COUNT(*) FROM core_terms INNER JOIN core_term_taxonomy ON core_terms.term_id = core_term_taxonomy.term_id WHERE core_terms.`name` LIKE :name AND core_term_taxonomy.taxonomy = :taxonomy", [':name' => "%$name%", ':taxonomy' => $taxonomy])
		->queryScalar();

	$sql = "SELECT core_terms.term_id, 
				core_terms.`name`, 
				core_terms.slug, 
				core_terms.term_group, 
				core_term_taxonomy.term_taxonomy_id, 
				core_term_taxonomy.taxonomy, 
				core_term_taxonomy.description, 
				core_term_taxonomy.parent, 
				core_term_taxonomy.count
			FROM core_terms INNER JOIN core_term_taxonomy ON core_terms.term_id = core_term_taxonomy.term_id
			WHERE core_terms.`name` LIKE :name AND core_term_taxonomy.taxonomy = :taxonomy";

	$dataProvider = new SqlDataProvider([
	    'sql' => $sql,
	    'key' => 'term_id',
	    'totalCount' => $count,
	    'params' => [':name' => "%$name%", ':taxonomy' => $taxonomy],
	    'sort' => [
		'attributes' => ['name'],
	    ],
	    'pagination' => [
		'pageSize' => 30,
	    ],
	]);

	return $dataProvider;
    }

    public static function getTaxonomySelect2($name, $taxonomy = 'category') {
	$sql = "SELECT core_terms.term_id AS id, 
			core_terms.`name` AS text
		FROM core_terms INNER JOIN core_term_taxonomy ON core_terms.term_id = core_term_taxonomy.term_id
		WHERE core_terms.`name` LIKE :name AND core_term_taxonomy.taxonomy = :taxonomy
		ORDER BY core_terms.`name` limit 30";

	return Yii::$app->db->createCommand($sql, [':name' => "%$name%", ':taxonomy' => $taxonomy])->queryAll();
    }

    public static function getTaxonomyByName($name, $taxonomy = 'category') {
	$sql = "SELECT core_terms.term_id, 
				core_terms.`name`, 
				core_terms.slug, 
				core_terms.term_group, 
				core_term_taxonomy.term_taxonomy_id, 
				core_term_taxonomy.taxonomy, 
				core_term_taxonomy.description, 
				core_term_taxonomy.parent, 
				core_term_taxonomy.count
			FROM core_terms INNER JOIN core_term_taxonomy ON core_terms.term_id = core_term_taxonomy.term_id
			WHERE core_terms.`name` LIKE :name AND core_term_taxonomy.taxonomy = :taxonomy
			ORDER BY core_terms.`name`";

	return Yii::$app->db->createCommand($sql, [':name' => "%$name%", ':taxonomy' => $taxonomy])->queryAll();
    }

    public static function getTaxonomyById($id) {
	$sql = "SELECT core_terms.term_id, 
				core_terms.`name`, 
				core_terms.slug, 
				core_terms.term_group, 
				core_term_taxonomy.term_taxonomy_id, 
				core_term_taxonomy.taxonomy, 
				core_term_taxonomy.description, 
				core_term_taxonomy.parent, 
				core_term_taxonomy.count
			FROM core_terms INNER JOIN core_term_taxonomy ON core_terms.term_id = core_term_taxonomy.term_id
			WHERE core_terms.term_id = :id";

	return Yii::$app->db->createCommand($sql, [':id' => $id])->queryOne();
    }

    public static function getTaxonomyRelationships($object_id, $taxonomy = 'category') {
	$sql = "SELECT core_terms.term_id, 
				core_terms.`name`, 
				core_terms.slug, 
				core_terms.term_group, 
				core_term_taxonomy.term_taxonomy_id, 
				core_term_taxonomy.taxonomy, 
				core_term_taxonomy.description, 
				core_term_taxonomy.parent, 
				core_term_taxonomy.count,
				core_term_relationships.object_id,
				core_term_relationships.term_order
			FROM core_terms INNER JOIN core_term_taxonomy ON core_terms.term_id = core_term_taxonomy.term_id
			INNER JOIN core_term_relationships ON core_term_taxonomy.term_taxonomy_id = core_term_relationships.term_taxonomy_id
			WHERE core_term_relationships.object_id = :object_id AND core_term_taxonomy.taxonomy = :taxonomy
			ORDER BY core_term_relationships.term_order ";

	return Yii::$app->db->createCommand($sql, [':object_id' => $object_id, ':taxonomy' => $taxonomy])->queryAll();
    }

    public static function checkTaxonomyRelationships($object_id, $term_taxonomy_id) {
	$sql = "SELECT core_term_relationships.object_id,
				core_term_relationships.term_taxonomy_id, 
				core_term_relationships.term_order
			FROM core_term_relationships
			WHERE core_term_relationships.object_id = :object_id AND core_term_relationships.term_taxonomy_id = :term_taxonomy_id ";

	return Yii::$app->db->createCommand($sql, [':object_id' => $object_id, ':term_taxonomy_id' => $term_taxonomy_id])->queryOne();
    }

    public static function delTaxonomyRelationships($object_id, $term_taxonomy_id) {
	return Yii::$app->db->createCommand()->delete('core_term_relationships', 'object_id=:object_id AND term_taxonomy_id = :term_taxonomy_id', [':object_id' => $object_id, ':term_taxonomy_id' => $term_taxonomy_id])->execute();
    }

    public static function getPostParent($type = 'page', $status = 'publish', $id = 0) {
	$paramsArr = array(':id' => $id, ':status' => $status, ':type' => $type);
	$paramsStrP = ' AND p.post_status = :status';
	$paramsStrC = ' AND c.post_status = :status';

	if ($type == '') {
	    $type = 'page';
	}

	if ($status == '') {
	    $paramsStrP = ' AND p.post_status <> :status';
	    $paramsStrC = ' AND c.post_status <> :status';
	    $status = 'trash';
	} else {
	    $data = self::getPostsByStatus($status, $type);
	    if (!$data) {
		$paramsStrP = ' AND p.post_status <> :status';
		$paramsStrC = ' AND c.post_status <> :status';
		$status = 'trash';
	    }
	}

	$paramsArr[':status'] = $status;

	$sql = "SELECT core_posts.ID, 
				`profile`.name AS post_author,  
				core_posts.post_title, 
				core_posts.post_status, 
				core_posts.post_modified, 
				core_posts.post_parent, 
				core_posts.menu_order, 
				core_posts.post_type, 
				core_posts.post_password,
				core_posts.comment_count,
				tmp_parent.post_title AS parent_title,
				SUM(IFNULL(tmp_child.post_parent,0)) AS child
			FROM core_posts INNER JOIN `profile` ON `profile`.user_id = core_posts.post_author
			INNER JOIN (SELECT p.ID,
				p.post_parent,
				(SELECT COUNT(*)
				FROM  core_posts c
				WHERE c.ID = p.post_parent AND c.post_type = :type $paramsStrC
				) AS lvl
			FROM core_posts p
			WHERE p.post_type = :type $paramsStrP
			) parent ON core_posts.ID = parent.ID
			LEFT JOIN (SELECT core_posts.post_parent
					FROM core_posts
				) tmp_child ON tmp_child.post_parent = core_posts.ID
			LEFT JOIN (SELECT core_posts.post_title, core_posts.ID
					FROM core_posts
				) tmp_parent ON tmp_parent.ID = core_posts.post_parent
			WHERE lvl = 0 AND core_posts.ID <> :id 
			GROUP BY core_posts.ID
			ORDER BY core_posts.menu_order, core_posts.post_title";

	return Yii::$app->db->createCommand($sql, $paramsArr)->queryAll();
    }

    public static function getPostChild($parent, $id = 0, $type = 'page', $status = 'publish') {
	$paramsArr = array(':id' => $id, ':parent' => $parent, ':status' => $status, ':type' => $type);
	$paramsStr = ' AND core_posts.post_status = :status';

	if ($type == '') {
	    $type = 'page';
	}

	if ($status == '') {
	    $paramsStr = ' AND core_posts.post_status <> :status';
	    $status = 'trash';
	} else {
	    $data = self::getPostsByStatus($status, $type);
	    if (!$data) {
		$paramsStr = ' AND core_posts.post_status <> :status';
		$status = 'trash';
	    }
	}

	$paramsArr[':status'] = $status;

	$sql = "SELECT core_posts.ID, 
				`profile`.name AS post_author,  
				core_posts.post_title, 
				core_posts.post_status, 
				core_posts.post_modified, 
				core_posts.post_parent, 
				core_posts.menu_order, 
				core_posts.post_type, 
				core_posts.post_password,
				core_posts.comment_count,
				SUM(IFNULL(tmp_child.post_parent,0)) AS child
			FROM core_posts INNER JOIN `profile` ON `profile`.user_id = core_posts.post_author
				LEFT JOIN (SELECT core_posts.post_parent
					FROM core_posts
				) tmp_child ON tmp_child.post_parent = core_posts.ID
			WHERE core_posts.post_type = :type AND core_posts.post_parent = :parent AND core_posts.ID <> :id $paramsStr
			GROUP BY core_posts.ID
			ORDER BY core_posts.menu_order, core_posts.post_title";

	return Yii::$app->db->createCommand($sql, $paramsArr)->queryAll();
    }

    public static function getPostsByStatus($status, $type = 'post') {
	$sql = "SELECT core_posts.ID, 
				core_posts.post_title, 
				core_posts.post_status
			FROM core_posts 
			WHERE core_posts.post_type = :type AND core_posts.post_status = :status
			GROUP BY core_posts.post_status ";

	return Yii::$app->db->createCommand($sql, [':status' => $status, ':type' => $type])->queryOne();
    }

    public static function getPostMetaByPostKey($post_id, $meta_key) {
	$model = CorePostmeta::find()
		->where('post_id = :post_id AND meta_key = :meta_key', [':post_id' => $post_id, ':meta_key' => $meta_key])
		->one();

	return $model;
    }

    public static function deleteTrashMetaByPostKey($post_id, $meta_key) {
	return Yii::$app->db->createCommand()->delete('core_postmeta', 'post_id=:id AND meta_key=:meta_key', [':id' => $post_id, ':meta_key' => $meta_key]);
    }

}
