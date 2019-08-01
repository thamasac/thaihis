<?php
namespace backend\modules\ezmodules\classes;

use Yii;
use backend\modules\ezmodules\models\EzmoduleTemplate;
use backend\modules\ezmodules\models\Ezmodule;
use backend\modules\ezforms2\models\Ezform;
use backend\modules\ezforms2\classes\EzfQuery;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ModuleQuery
 *
 * @author appxq
 */
class ModuleQuery {
//    public static function getIntUserAll() {
//	$sql = "SELECT user_id as id, 
//		    CONCAT(firstname, ' ', lastname) AS text
//		FROM profile
//		";
//	
//	return Yii::$app->db->createCommand($sql)->queryAll();
//    }
    
    public static function getTemplate($user_id) {
        
        $ezform = EzmoduleTemplate::find()
                ->where('(template_system=1 AND public=1) OR (template_system=0 AND (public=1 OR created_by = :created_by ))', ['created_by' => $user_id])
                ->all();
        return $ezform;
    }
    
    public static function getMyModule($userId) {
	$model = Ezmodule::find()
		->where("ezm_system=0 AND active=1 AND ezm_project = 0 AND (created_by=:created_by  )", [':created_by'=>$userId])
		->groupBy('ezm_name')
		->all();
	
	return $model;
    }
    
    public static function getMySubModule($userId) {
	$model = Ezmodule::find()
		->where("ezm_system=0 AND (ezm_visible=0 || ezm_visible is null) AND active=1 AND ezm_project = 1 AND (created_by=:created_by  )", [':created_by'=>$userId])
		->groupBy('ezm_name')
		->all();
	
	return $model;
    }
    
    public static function getMoreModule($userId, $id) {
	$model = Ezmodule::find()
		->where("ezm_system=0 AND (ezm_visible=0 || ezm_visible is null) AND active=1 AND public=1 AND approved=1 AND (created_by=:created_by AND ezm_id<>:ezm_id )", [':ezm_id'=>$id, ':created_by'=>$userId])
		->groupBy('ezm_name')
		->all();
	
	return $model;
    }
    
    public static function getSystemModule() {
	$model = Ezmodule::find()
		->where("ezm_system=1 AND (ezm_template=0 OR ezm_template is null) AND active=1")
		->groupBy('order_module, ezm_name')
		->all();
	
	return $model;
    }
    
    public static function getFavModule($userId) {
	$model = Ezmodule::find()
                ->innerJoin('ezmodule_favorite f', 'f.ezm_id = ezmodule.ezm_id AND f.user_id = :user_id', [':user_id'=>$userId])
		->where("ezm_system=0 AND (ezm_visible=0 || ezm_visible is null) AND active=1 ")
		->groupBy('ezm_name')
		->all();
        
	return $model;
    }
    
    public static function getAssignModule($userId) {
	$model = Ezmodule::find()
		->where("ezm_system=0 AND active=1 AND (FIND_IN_SET('$userId', `share`)>0 OR ezm_id in (SELECT ezmodule_role.ezm_id FROM ezmodule_role WHERE ezmodule_role.role ".\backend\modules\ezforms2\classes\EzfForm::getRoleIn()." ) )")
		->groupBy('ezm_name')
		->all();
        
	return $model;
    }
    
    public static function getCoCreatedModule($userId) {
	$model = Ezmodule::find()
		->where("ezm_system=0 AND active=1 AND FIND_IN_SET('$userId', `ezm_builder`)>0")
		->groupBy('ezm_name')
		->all();
        
	return $model;
    }
    
    public static function getPublicModule() {
	$model = Ezmodule::find()
		->where("ezm_system=0 AND (ezm_visible=0 || ezm_visible is null) AND active=1 AND public=1 AND approved=1")
		->groupBy('ezm_name')
		->all();
	
	return $model;
    }
    
    public static function getAllModule() {
	$model = Ezmodule::find()
		->where("ezm_system=0 AND (ezm_visible=0 || ezm_visible is null) AND active=1")
		->groupBy('ezm_name')
		->all();
	
	return $model;
    }
    
    public static function getModuleID($id) {
	$model = Ezmodule::find()
                ->select(['ezmodule.*', '(SELECT ezf_table from ezform where ezf_id = ezmodule.ezf_id) AS ezf_table'])
		->where("ezm_id=:ezm_id", [':ezm_id'=>$id])
		->one();
	
	return $model;
    }
    
    public static function getModuleOne($id, $userId) {
	$model = Ezmodule::find()
                ->select(['ezmodule.*', '(SELECT ezf_table from ezform where ezf_id = ezmodule.ezf_id) AS ezf_table'])
		->where("ezm_id=:ezm_id AND active=1 AND (created_by=:created_by OR ezm_system=0 OR (public=1 AND approved=1) OR  (FIND_IN_SET('$userId', `share`)>0 OR ezm_id in (SELECT ezmodule_role.ezm_id FROM ezmodule_role WHERE ezmodule_role.role ".\backend\modules\ezforms2\classes\EzfForm::getRoleIn()." ) ) )", [':ezm_id'=>$id, ':created_by'=>$userId])
		->one();
	
	return $model;
    }
    
    public static function getModuleMyAll($userId) {
	$model = Ezmodule::find()
                ->select(['ezmodule.*', '(SELECT ezf_table from ezform where ezf_id = ezmodule.ezf_id) AS ezf_table'])
		->where("active=1 AND (created_by=:created_by OR ezm_system=0 OR (public=1 AND approved=1) OR  (FIND_IN_SET('$userId', `share`)>0 OR ezm_id in (SELECT ezmodule_role.ezm_id FROM ezmodule_role WHERE ezmodule_role.role ".\backend\modules\ezforms2\classes\EzfForm::getRoleIn()." ) ) )", [':created_by'=>$userId])
		->all();
	
	return $model;
    }
    
    public static function getModuleMyAllAddon($userId) {
	$model = Ezmodule::find()
                ->select(['ezmodule.*', '(SELECT ezf_table from ezform where ezf_id = ezmodule.ezf_id) AS ezf_table'])
		->where("active=1 AND ezm_type<>1 AND (created_by=:created_by OR ezm_system=0 OR (public=1 AND approved=1) OR  (FIND_IN_SET('$userId', `share`)>0 OR ezm_id in (SELECT ezmodule_role.ezm_id FROM ezmodule_role WHERE ezmodule_role.role ".\backend\modules\ezforms2\classes\EzfForm::getRoleIn()." ) ) )", [':created_by'=>$userId])
		->all();
	
	return $model;
    }
    
    public static function countUseModule($id) {
        $sql="select count(*) AS user, count(distinct p.sitecode)  AS org from profile p inner join ezmodule_favorite f on p.user_id=f.user_id where f.ezm_id=:id";
        return Yii::$app->db->createCommand($sql, [':id'=>$id])->queryOne();
    }
    
    public static function countPcocModule($ezf_table) {
        $sql="select count(*) AS ntime,count(distinct ifnull(ptid,id)) AS npatient from {$ezf_table} where rstat<>3 AND rstat<>0;";
        return Yii::$app->db->createCommand($sql)->queryOne();
    }
    
    public static function getModuleList($id, $userId) {
	$model = Ezmodule::find()
                ->select(['ezmodule.*', '(SELECT ezf_table from ezform where ezf_id = ezmodule.ezf_id) AS ezf_table', 'a.addon_id', 'a.module_id', 'a.user_id'])
                ->innerJoin('ezmodule_addon a', 'a.module_id=ezmodule.ezm_id')
		->where("a.ezm_id=:ezm_id AND (a.addon_default=1 OR a.user_id=:created_by)", [':ezm_id'=>$id, ':created_by'=>$userId])
		->all();
	
	return $model;
    }
    
     public static function getTabList($id, $userId) {
	$model = \backend\modules\ezmodules\models\EzmoduleTab::find()
                ->where('ezm_id=:ezm_id AND parent = 0 AND (tab_default=1 OR user_id=:created_by)', [':ezm_id'=>$id, ':created_by'=>$userId])
                ->orderBy('order')
                ->all();
	
	return $model;
    }
    
    public static function getTab($id) {
	$model = \backend\modules\ezmodules\models\EzmoduleTab::find()
                ->where('tab_id=:tab_id', [':tab_id'=>$id])
                ->one();
	
	return $model;
    }
    
    public static function getFilterList($module, $userId) {
        $sql="SELECT *
            FROM
            `ezmodule_filter`
            WHERE ezm_id=:ezm_id AND (ezm_default=1 OR created_by=:created_by OR `public`=1 OR  FIND_IN_SET('$userId', `share`)>0 ) order by filter_order";
        return Yii::$app->db->createCommand($sql, [':ezm_id'=>$module, ':created_by'=>$userId])->queryAll();
    }
    
    public static function getFilterListCutom($module, $filter, $userId) {
        $sql="SELECT *
            FROM
            `ezmodule_filter`
            WHERE ezm_id=:ezm_id AND filter_id<>:filter_id AND filter_type=0 AND (created_by=:created_by OR FIND_IN_SET('$userId', `share`)>0 ) order by filter_order";
        return Yii::$app->db->createCommand($sql, [':ezm_id'=>$module, ':filter_id'=>$filter, ':created_by'=>$userId])->queryAll();
    }
    
    public static function getFieldsList($module, $userId) {
        $modelFields = \backend\modules\ezmodules\models\EzmoduleFields::find()
                ->select(['ezmodule_fields.*', 'ezform_fields.ezf_field_name', 'ezform_fields.table_field_type', 'ezform_fields.ezf_field_data', 'ezform_fields.ezf_field_type'])
                ->innerJoin('ezform_fields', 'ezform_fields.ezf_field_id = ezmodule_fields.ezf_field_id')
                ->where('ezm_id=:ezm_id AND (field_default=1 OR ezmodule_fields.created_by=:created_by)', [':ezm_id'=>$module, ':created_by'=>$userId])->orderBy('ezmodule_fields.field_order')->all();
        return $modelFields;
    }
    
    public static function getFieldsFind($inform, $q) {
        //IF(ezf_field_label<>'' OR ezf_field_label<>Null,ezf_field_label,ezf_field_name) AS`name`, 
        //ezf_field_type<>0
        $sql = "SELECT ezf_field_id AS `id`,  
                
                concat(`ezf_field_name`, ' (', `ezf_field_label`, ')', ' [', ezform_fields.ezf_version, ']') AS`name`,
                ezform.ezf_id ,
                ezf_field_name,
                ezform_fields.ezf_version,
                ezform.ezf_name
            FROM `ezform_fields` 
            INNER JOIN ezform ON ezform.ezf_id = ezform_fields.ezf_id
            WHERE table_field_type<>'none' AND table_field_type<>'field' AND ezform.ezf_id in($inform) AND CONCAT(`ezf_field_name`, `ezf_field_label`) LIKE :q ORDER BY ezf_id, ezf_field_order";

        $data = Yii::$app->db->createCommand($sql, [':q' => "%$q%"])->queryAll();
        
        return $data;
    }
    
    public static function getFieldsCountById($module) {
	$sql = "SELECT MAX(field_order)+1 AS num
		FROM ezmodule_fields
		WHERE ezm_id=:module
		";
	$order = Yii::$app->db->createCommand($sql, [':module'=>$module])->queryScalar();
	return isset($order)?$order:1;
    }
    
    public static function getFormsCountById($module) {
	$sql = "SELECT MAX(form_order)+1 AS num
		FROM ezmodule_forms
		WHERE ezm_id=:module
		";
	$order = Yii::$app->db->createCommand($sql, [':module'=>$module])->queryScalar();
	return isset($order)?$order:1;
    }
    
    public static function getEzformListFind($ezf_id, $q) {
         $model = Ezform::find()->select(['ezform.ezf_id', 'ezform.ezf_name', 'ezform.ezf_table', 'ezform_fields.ezf_version'])
                    ->innerJoin('ezform_fields', 'ezform_fields.ezf_id = ezform.ezf_id')
                    ->where('ezform.status = 1 AND ezf_field_type<>0 AND (ezform_fields.ref_ezf_id=:ezf_id) AND ezform.ezf_name like :q', [':ezf_id'=>$ezf_id, ':q'=>"%$q%"])
                    ->andWhere('shared = 1 OR shared = 4 OR (shared = 3 AND ezform.xsourcex=:xsourcex) OR (shared = 2 AND ezform.ezf_id in (SELECT ezf_id FROM ezform_assign WHERE user_id = :user_id AND ezf_id<>ezform.ezf_id)) OR (shared = 0 AND (ezform.created_by=:user_id OR ezform.ezf_id in (SELECT ezf_id FROM ezform_co_dev WHERE user_co = :user_id AND ezf_id<>ezform.ezf_id)))', [':user_id' => Yii::$app->user->id, ':xsourcex' => Yii::$app->user->identity->profile->sitecode])
                    ->groupBy('ezform.ezf_id')
                    ->all();
         return $model;
     }
     
     public static function getEzformList($ezf_id) {
         $model = Ezform::find()->select(['ezform.ezf_id', 'ezform.ezf_name', 'ezform.ezf_table'])
                    ->innerJoin('ezform_fields', 'ezform_fields.ezf_id = ezform.ezf_id')
                    ->where('ezform.status = 1 AND ezf_field_type<>0 AND (ezform_fields.ref_ezf_id=:ezf_id)', [':ezf_id'=>$ezf_id])
                    ->andWhere('shared = 1 OR shared = 4 OR (shared = 3 AND ezform.xsourcex=:xsourcex) OR (shared = 2 AND ezform.ezf_id in (SELECT ezf_id FROM ezform_assign WHERE user_id = :user_id AND ezf_id<>ezform.ezf_id)) OR (shared = 0 AND (ezform.created_by=:user_id OR ezform.ezf_id in (SELECT ezf_id FROM ezform_co_dev WHERE user_co = :user_id AND ezf_id<>ezform.ezf_id)))', [':user_id' => Yii::$app->user->id, ':xsourcex' => Yii::$app->user->identity->profile->sitecode])
                    ->groupBy('ezform.ezf_id')
                    ->all();
         return $model;
     }
     
     public static function getFormsList($module, $userId) {
        $modelForms = \backend\modules\ezmodules\models\EzmoduleForms::find()
                ->select(['ezmodule_forms.*', 'ezform.ezf_name', 'ezform.ezf_table', 'ezform.unique_record'])
                ->innerJoin('ezform', 'ezform.ezf_id = ezmodule_forms.ezf_id')
                ->where('ezm_id=:ezm_id AND (form_default=1 OR ezmodule_forms.created_by=:created_by)', [':ezm_id'=>$module, ':created_by'=>$userId])->orderBy('ezmodule_forms.form_order')->all();
        return $modelForms;
    }
    
    public static function getFieldsOptionList($ezf_id){
        $sql = "SELECT `ezf_field_name` AS id, concat(`ezf_field_name`, ' (', `ezf_field_label`, ')', ' [', `ezf_version`, ']') AS name FROM `ezform_fields` WHERE table_field_type<>'none' AND table_field_type<>'field' AND `ezf_id` = :id";
	$data = Yii::$app->db->createCommand($sql, [':id'=>$ezf_id])->queryAll();
        $data = \yii\helpers\ArrayHelper::merge($data, [['id'=>'rstat', 'name'=>'Rstat']]);
        return $data;
    }
    
    public static function getFormsSelect($select) {
	if($select==''){
	    return false;
	}
        
	$sql = "SELECT ezf_id, ezf_name, ezf_table, field_detail, unique_record FROM ezform WHERE ezf_id IN ($select)";
	
	return Yii::$app->db->createCommand($sql)->queryAll();
    }
    
    public static function getTabParent($module) {
	$model = \backend\modules\ezmodules\models\EzmoduleTab::find()
		->where("ezm_id=:module AND parent=0 AND widget='dropdown' ", [':module'=>$module])
		->all();
	
	return $model;
    }
    
    public static function getTabOrder($module) {
	$sql = "SELECT MAX(`order`)+10 AS num
		FROM ezmodule_tab
		WHERE ezm_id=:module
		";
	$order = Yii::$app->db->createCommand($sql, [':module'=>$module])->queryScalar();
	return isset($order)?(int)$order:10;
    }
    
    public static function getSettingWorkList($dept, $module, $type) {
        $sql="SELECT *
            FROM
            `queue_log`
            WHERE unit=:dept AND module_id=:module AND `status`=:type AND `enable`=1  order by created_at";

        return Yii::$app->db->createCommand($sql, [':dept'=>$dept, ':module'=>$module, ':type'=>$type])->queryAll();
    }
    
    public static function genMapData($ezform, $sedate, $var_date, $lng_field, $lat_field) {
//        if(isset($sedate) && !empty($sedate)){
//            $wdate = " AND DATE(`$var_date`) between '{$sedate['s']}' AND '{$sedate['e']}' ";
//        }
        
//	$sql = "	
//            SELECT id, $lng_field, $lat_field
//            FROM {$ezform['ezf_table']}
//            WHERE rstat<>3 AND rstat<>0 $wdate
//		";
//	
        $model = new \backend\modules\ezforms2\models\TbdataAll();    
        $model->setTableName($ezform->ezf_table);
        
        $query = $model->find()->where('rstat not in(0,3)');
        
        if (isset($sedate) && !empty($sedate)) {
            $query->andWhere("DATE(`$var_date`) between '{$sedate['s']}' AND '{$sedate['e']}'");
        }
        
        $modelEvent = EzfQuery::getEventFields($ezform->ezf_id);
        $modelFields;
        if ($modelEvent) {
            foreach ($modelEvent as $key => $value) {
                if ($value['ezf_target'] == 1) {
                    $modelFields = EzfQuery::findSpecialOne($ezform->ezf_id);
                } elseif ($value['ezf_special'] == 1) {
                    $modelFields = true;
                }
            }
        }
        
        $query->select([
            "id", $lng_field, $lat_field
        ]);
        
        if (isset($modelFields) || $ezform['public_listview'] == 2) {
            $query->andWhere('xsourcex = :site', [':site' => Yii::$app->user->identity->profile->sitecode]);
        }
        
        if ($ezform['public_listview'] == 3) {
            $query->andWhere('xdepartmentx = :unit', [':unit' => Yii::$app->user->identity->profile->department]);
        }

        if ($ezform['public_listview'] == 0) {
            $query->andWhere("user_create=:created_by", [':created_by' => Yii::$app->user->id]);
        }
        
	return $query->all();
    }
    
    public static function getWidgetList($q) {
        $sql="SELECT
                `ezmodule_widget`.`widget_id`,
                `ezmodule_widget`.`widget_name`,
                `ezmodule_widget`.`widget_varname`,
                `ezmodule_widget`.`widget_type`,
                `ezmodule_widget`.`ezm_id`,
                `ezmodule_widget`.`ezf_id`,
                `ezmodule_widget`.`enable`,
                `ezmodule_widget`.`created_by`,
                CONCAT(IFNULL(ezm_name,''), ' # ', IFNULL(widget_name,''), ' [', IFNULL(widget_varname,''), ']') AS fname,
                `ezmodule`.`ezm_name`
                FROM
                `ezmodule`
                JOIN `ezmodule_widget`
                ON `ezmodule`.`ezm_id` = `ezmodule_widget`.`ezm_id`
                WHERE
                (`ezmodule_widget`.`created_by` = :user || (`ezmodule`.`public` = 1 AND `ezmodule`.`approved` = 1)) AND `ezmodule_widget`.widget_type <> 'core' AND `ezmodule_widget`.`enable` = 1
                AND CONCAT(IFNULL(widget_name,''), ' [', IFNULL(widget_varname,''), ']') LIKE :q
                ORDER BY
                `ezmodule`.`ezm_name` ASC,
                `ezmodule_widget`.`widget_name` ASC
                LIMIT 50
                ";

        return Yii::$app->db->createCommand($sql, [':user'=> Yii::$app->user->id, ':q'=>"%$q%"])->queryAll();
    }
    
    public static function insertEzmRole($ezm_id, $role) {
        $sql = "INSERT IGNORE ezmodule_role VALUES(:id,:role,1)";
        return Yii::$app->db->createCommand($sql, [':id' => $ezm_id, ':role' => $role])->execute();
    }
    
}
