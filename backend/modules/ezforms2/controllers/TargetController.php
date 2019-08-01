<?php

namespace backend\modules\ezforms2\controllers;

use Yii;
use yii\web\Controller;
use yii\helpers\Json;
use backend\modules\ezforms2\models\EzformFields;
use backend\modules\ezforms2\classes\EzfFunc;
use backend\modules\ezforms2\classes\EzfQuery;
use yii\web\Response;
use appxq\sdii\helpers\SDHtml;
use appxq\sdii\utils\SDUtility;


/**
 * TargetController implements the CRUD actions for EzformInput model.
 */
class TargetController extends Controller {

    public function actionGetFields() {
        $ezf_id = isset($_POST['ezf_id']) ? $_POST['ezf_id'] : 0;
        $name = isset($_POST['name']) ? $_POST['name'] : '';
        $type = isset($_GET['type']) ? $_GET['type'] : '';
        $value = isset($_POST['value']) ? $_POST['value'] : '';
        $multiple = isset($_POST['multiple']) ? (int)$_POST['multiple'] :0;
        $input_only = isset($_POST['input_only']) ? $_POST['input_only'] : 0;
        
        $type = EzfFunc::stringDecode2Array($type);
        
        $andWhere = " AND table_field_type <> 'none' ";//,'field'
        if($input_only){
            $andWhere = " AND table_field_type not in('none','field') ";//,'field'
        }
        //$andWhere = "";
        if(is_array($type) && !empty($type)){
            $instr = implode(',', $type);
            $andWhere = " AND ezf_field_type in($instr)";
        }
        
        $id = isset($_POST['id']) ? $_POST['id'] : \appxq\sdii\utils\SDUtility::getMillisecTime();
        $sql = "SELECT ezf_field_name AS `id`, concat(ezf_field_name, ' (', ezf_field_label, ')') AS`name`, ezf_version, ezf_field_name, ezf_field_label FROM `ezform_fields` WHERE `ezf_id` = :id $andWhere  ORDER BY ezf_version, ezf_field_order";
        $data = Yii::$app->db->createCommand($sql, [':id' => $ezf_id])->queryAll();

        $modelEzf = EzfQuery::getEzformOne($ezf_id);
        $fieldsGroup = [];
        if($modelEzf){
            $fieldsGroup = \backend\modules\ezforms2\classes\EzfFunc::getFieldsGroup($data, $modelEzf->ezf_version);
        }
        
        return $this->renderAjax("/widgets/_subselect", [
                    'id' => $id,
                    'name' => $name,
                    'value' => $value,
                    'multiple'=>$multiple,
                    'data' => \yii\helpers\ArrayHelper::map($fieldsGroup, 'id', 'name'),
        ]);
    }
    
    public function actionGetFieldsAddon() {
        $ezf_id = isset($_POST['ezf_id']) ? $_POST['ezf_id'] : 0;
        $name = isset($_POST['name']) ? $_POST['name'] : '';
        $type = isset($_GET['type']) ? $_GET['type'] : '';
        $value = isset($_POST['value']) ? $_POST['value'] : '';
        $multiple = isset($_POST['multiple']) ? (int)$_POST['multiple'] :0;
        $input_only = isset($_POST['input_only']) ? $_POST['input_only'] : 0;
        
        $type = EzfFunc::stringDecode2Array($type);
        
        $andWhere = " AND table_field_type <> 'none' ";//,'field'
        if($input_only){
            $andWhere = " AND table_field_type not in('none','field') ";//,'field'
        }
        //$andWhere = "";
        if(is_array($type) && !empty($type)){
            $instr = implode(',', $type);
            $andWhere = " AND ezf_field_type in($instr)";
        }
        
        $id = isset($_POST['id']) ? $_POST['id'] : \appxq\sdii\utils\SDUtility::getMillisecTime();
        $sql = "SELECT ezf_field_name AS `id`, concat(ezf_field_name, ' (', ezf_field_label, ')') AS`name`, ezf_version, ezf_field_name, ezf_field_label FROM `ezform_fields` WHERE `ezf_id` = :id $andWhere  ORDER BY ezf_version, ezf_field_order";
        $data = Yii::$app->db->createCommand($sql, [':id' => $ezf_id])->queryAll();

        $modelEzf = EzfQuery::getEzformOne($ezf_id);
        $fieldsGroup = [];
        if($modelEzf){
            $fieldsGroup = \backend\modules\ezforms2\classes\EzfFunc::getFieldsGroup($data, $modelEzf->ezf_version);
        }
        
        //addon system fields
        if(!empty($fieldsGroup)){
            $fieldsGroup[] = [
                'id' => 'target',
                'name' => 'target'
            ];
            $fieldsGroup[] = [
                'id' => 'xsourcex',
                'name' => 'xsourcex'
            ];
            $fieldsGroup[] = [
                'id' => 'xdepartmentx',
                'name' => 'xdepartmentx'
            ];
            $fieldsGroup[] = [
                'id' => 'rstat',
                'name' => 'rstat'
            ];
        }
        
        return $this->renderAjax("/widgets/_subselect", [
                    'id' => $id,
                    'name' => $name,
                    'value' => $value,
                    'multiple'=>$multiple,
                    'data' => \yii\helpers\ArrayHelper::map($fieldsGroup, 'id', 'name'),
        ]);
    }
    
    public function actionTableFields() {
        $ezf_id = isset($_POST['ezf_id']) ? $_POST['ezf_id'] : '';
        $name = isset($_POST['name']) ? $_POST['name'] : '';
        $value = isset($_POST['value']) ? $_POST['value'] : '';
        $multiple = isset($_POST['multiple']) ? (int)$_POST['multiple'] :0;
        $allform = isset($_POST['allform']) ? (int)$_POST['allform'] :0;
        
        if(isset($ezf_id) && !empty($ezf_id)){
            $ezf_id = explode(',', $ezf_id);
        }
        
        if($allform==1 && isset($ezf_id[0]) && !empty($ezf_id[0])){
            $field_target = EzfQuery::getTargetOne($ezf_id[0]);
            $ezform_all;
            if($field_target){
                if(isset($field_target['parent_ezf_id']) && !empty($field_target['parent_ezf_id'])){
                    $ezform_all = EzfQuery::getJoinFieldsAll($field_target['parent_ezf_id']);
                    if($ezform_all){
                        $ezform_all_array = \yii\helpers\ArrayHelper::getColumn($ezform_all, 'ezf_id');
                        $ezform_all_array[] = $field_target['parent_ezf_id'];
                        $ezf_id = $ezform_all_array;
                    }
                    //$andWhere .= " OR ( ezform_fields.parent_ezf_id = '{$field_target['parent_ezf_id']}' OR ezform_fields.ezf_id = '{$field_target['parent_ezf_id']}' ) ";
                }
            } else {
                //$andWhere .= " OR ezform_fields.parent_ezf_id = '{$ezf_id[0]}' ";
                $ezform_all = EzfQuery::getJoinFieldsAll($ezf_id[0]);
                if($ezform_all){
                    $ezform_all_array = \yii\helpers\ArrayHelper::getColumn($ezform_all, 'ezf_id');
                    $ezform_all_array[] = $ezf_id[0];
                    $ezf_id = $ezform_all_array;
                }
            }
            
        } elseif($allform==2 && isset($ezf_id[0]) && !empty($ezf_id[0])){
            $field_target = EzfQuery::getTargetOne($ezf_id[0]);
            if($field_target){
                if(isset($field_target['parent_ezf_id']) && !empty($field_target['parent_ezf_id'])){
                    $ezf_id[] = $field_target['parent_ezf_id'];
                }
            }
           
            
        }
        
        $andWhere = '';//,'field'
        if(isset($ezf_id) && !empty($ezf_id)){
            foreach ($ezf_id as $key => $valueWhere) {
                if(empty($valueWhere)){
                    unset($ezf_id[$key]);
                }
            }
            if(is_array($ezf_id) && !empty($ezf_id)){
                $instr = implode(',', $ezf_id);
                $andWhere = " AND ezform.ezf_id in($instr)";
            }
        }
        

        
        $id = isset($_POST['id']) ? $_POST['id'] : \appxq\sdii\utils\SDUtility::getMillisecTime();
        
        if($allform>0){
            $sql = "SELECT 
                    concat(ezform.ezf_id, ':', ezf_table, ':', ezf_field_name) AS `id`, 
                    concat(ezf_name, '.', ezf_field_name, ' (', IFNULL(ezf_field_label,''), ') ', ezform.ezf_version) AS`name`,
                    ezform.ezf_id,
                    ezform.ezf_table,
                    ezf_field_name,
                    ezf_name
                FROM 
                    `ezform` 
                INNER JOIN `ezform_fields` ON ezform_fields.ezf_id = ezform.ezf_id
                WHERE table_field_type not in('none','field') $andWhere  
                ORDER BY ezf_table, ezf_field_name";
        } else {
            $sql = "SELECT 
                    concat('`', ezf_table, '`.`', ezf_field_name, '`') AS `id`, 
                    concat(ezf_name, '.', ezf_field_name, ' (', IFNULL(ezf_field_label,''), ') ', ezform.ezf_version) AS`name`,
                    ezf_table,
                    ezf_field_name,
                    ezf_name
                FROM 
                    `ezform` 
                INNER JOIN `ezform_fields` ON ezform_fields.ezf_id = ezform.ezf_id
                WHERE table_field_type not in('none','field') $andWhere  
                ORDER BY ezf_table, ezf_field_name";
        }
        
        $data = Yii::$app->db->createCommand($sql)->queryAll();

        $fieldsGroup = [];
        if($data){
            $table = '';
            foreach ($data as $key_data => $value_data) {
                if($table != $value_data['ezf_table']){
                    $table = $value_data['ezf_table'];
                    
                    $value_custom['ezf_name'] = $value_data['ezf_name'];
                    $value_custom['id'] = ($allform>0)?"{$value_data['ezf_id']}:{$value_data['ezf_table']}:ptid":"`{$value_data['ezf_table']}`.`ptid`";
                    $value_custom['name'] = "{$value_data['ezf_name']}.ptid";
                    $fieldsGroup[] = $value_custom;
                    
                    $value_custom['id'] = ($allform>0)?"{$value_data['ezf_id']}:{$value_data['ezf_table']}:target":"`{$value_data['ezf_table']}`.`target`";
                    $value_custom['name'] = "{$value_data['ezf_name']}.target";
                    $fieldsGroup[] = $value_custom;
                    
                    $value_custom['id'] = ($allform>0)?"{$value_data['ezf_id']}:{$value_data['ezf_table']}:xsourcex":"`{$value_data['ezf_table']}`.`xsourcex`";
                    $value_custom['name'] = "{$value_data['ezf_name']}.xsourcex";
                    $fieldsGroup[] = $value_custom;
                    
                    $value_custom['id'] = ($allform>0)?"{$value_data['ezf_id']}:{$value_data['ezf_table']}:xdepartmentx":"`{$value_data['ezf_table']}`.`xdepartmentx`";
                    $value_custom['name'] = "{$value_data['ezf_name']}.xdepartmentx";
                    $fieldsGroup[] = $value_custom;
                    
                    $value_custom['id'] = ($allform>0)?"{$value_data['ezf_id']}:{$value_data['ezf_table']}:rstat":"`{$value_data['ezf_table']}`.`rstat`";
                    $value_custom['name'] = "{$value_data['ezf_name']}.rstat";
                    $fieldsGroup[] = $value_custom;
                    
                    $value_custom['id'] = ($allform>0)?"{$value_data['ezf_id']}:{$value_data['ezf_table']}:user_create":"`{$value_data['ezf_table']}`.`user_create`";
                    $value_custom['name'] = "{$value_data['ezf_name']}.user_create";
                    $fieldsGroup[] = $value_custom;
                    
                    $value_custom['id'] = ($allform>0)?"{$value_data['ezf_id']}:{$value_data['ezf_table']}:user_update":"`{$value_data['ezf_table']}`.`user_update`";
                    $value_custom['name'] = "{$value_data['ezf_name']}.user_update";
                    $fieldsGroup[] = $value_custom;
                    
                    $value_custom['id'] = ($allform>0)?"{$value_data['ezf_id']}:{$value_data['ezf_table']}:create_date":"`{$value_data['ezf_table']}`.`create_date`";
                    $value_custom['name'] = "{$value_data['ezf_name']}.create_date";
                    $fieldsGroup[] = $value_custom;
                    
                    $value_custom['id'] = ($allform>0)?"{$value_data['ezf_id']}:{$value_data['ezf_table']}:update_date":"`{$value_data['ezf_table']}`.`update_date`";
                    $value_custom['name'] = "{$value_data['ezf_name']}.update_date";
                    $fieldsGroup[] = $value_custom;
                }
                
                $fieldsGroup[] = $value_data;
            }
            //$fieldsGroup = $data;
        }
        
        return $this->renderAjax("/widgets/_subselect", [
                    'id' => $id,
                    'name' => $name,
                    'value' => $value,
                    'multiple'=>$multiple,
                    'data' => \yii\helpers\ArrayHelper::map($fieldsGroup, 'id', 'name', 'ezf_name'),
        ]);
    }
    
    public function actionDepend() {
        $ref_id = isset($_POST['ref_id']) ? $_POST['ref_id'] : 0;
        $ezf_id = isset($_POST['ezf_id']) ? $_POST['ezf_id'] : 0;
        $name = isset($_POST['name']) ? $_POST['name'] : '';
        $value = isset($_POST['value']) ? $_POST['value'] : '';
        $multiple = isset($_POST['multiple']) ? (int)$_POST['multiple'] :0;
        $type = isset($_GET['type']) ? $_GET['type'] : '';
        
        $type = EzfFunc::stringDecode2Array($type);
        
        $andWhere = " AND table_field_type <> 'none' ";//,'field'
        //$andWhere = "";
        if(is_array($type) && !empty($type)){
            $instr = implode(',', $type);
            $andWhere = " AND ezf_field_type in($instr)";
        }
        
        if($multiple == 1 && $value != ''){
            $value = \appxq\sdii\utils\SDUtility::string2Array($value);
        }
        
        $target = EzfQuery::getTargetOne($ref_id);
        $data = [];
        $id = isset($_POST['id']) ? $_POST['id'] : \appxq\sdii\utils\SDUtility::getMillisecTime();
        if($target){
            $sql = "SELECT ezf_field_name AS `id`, concat(ezf_field_name, ' (', ezf_field_label, ')') AS`name`, ezf_version, ezf_field_name, ezf_field_label FROM `ezform_fields` WHERE `ezf_id` = :id AND `ref_ezf_id` = :ref_id  $andWhere  ORDER BY ezf_version, ezf_field_order";
            $data = Yii::$app->db->createCommand($sql, [':id' => $ezf_id, ':ref_id'=>$target['ref_ezf_id']])->queryAll();
        }
        
        return $this->renderAjax("/widgets/_subselect", [
                    'id' => $id,
                    'name' => $name,
                    'value' => $value,
                    'multiple'=>$multiple,
                    'placeholder' => Yii::t('ezform', 'Select form ...'),
                    'data' => \yii\helpers\ArrayHelper::map($data, 'id', 'name'),
        ]);
    }
    
    public function actionGetParentFields() {
        $ref_id = isset($_POST['ref_id']) ? $_POST['ref_id'] : 0;
        $ezf_id = isset($_POST['ezf_id']) ? $_POST['ezf_id'] : 0;
        $depend_name = isset($_POST['depend_name']) ? $_POST['depend_name'] : '';
        
        $field = EzfQuery::getFieldByName($ezf_id, $depend_name);
        if($field){
            if(isset($field['ezf_field_type']) && $field['ezf_field_type']=='87'){
                $options = isset($field['ezf_field_options'])?SDUtility::string2Array($field['ezf_field_options']):[];
                
                return isset($options['options']['data-parent'])?$options['options']['data-parent']:'';
                
            } else {
                return $depend_name;
            }
        }
        
        return '';
    }
    
    public function actionTargetEzform() {
        $ezf_id = isset($_POST['ref_id']) ? $_POST['ref_id'] : 0;
        $name = isset($_POST['name']) ? $_POST['name'] : '';
        $value = isset($_POST['value']) ? $_POST['value'] : '';
        $multiple = isset($_POST['multiple']) ? (int)$_POST['multiple'] :0;
        
        if($multiple == 1 && $value != ''){
            $value = \appxq\sdii\utils\SDUtility::string2Array($value);
        }
        
        $id = isset($_POST['id']) ? $_POST['id'] : \appxq\sdii\utils\SDUtility::getMillisecTime();
        
        $data = EzfQuery::getEzformSubRef($ezf_id);
            
        return $this->renderAjax("/widgets/_subselect", [
                    'id' => $id,
                    'name' => $name,
                    'value' => $value,
                    'multiple'=>$multiple,
                    'placeholder' => Yii::t('ezform', 'Select form ...'),
                    'data' => \yii\helpers\ArrayHelper::map($data, 'ezf_id', 'ezf_name'),
        ]);
    }
    
    public function actionWidgetEzform() {
        $ezf_id = isset($_POST['ref_id']) ? $_POST['ref_id'] : 0;
        $name = isset($_POST['name']) ? $_POST['name'] : '';
        $value = isset($_POST['value']) ? $_POST['value'] : '';
        $multiple = isset($_POST['multiple']) ? (int)$_POST['multiple'] :0;
        
        if($multiple == 1 && $value != ''){
            $value = \appxq\sdii\utils\SDUtility::string2Array($value);
        }
        
        $id = isset($_POST['id']) ? $_POST['id'] : \appxq\sdii\utils\SDUtility::getMillisecTime();
        $sql = "SELECT
                `ezform`.`ezf_id` AS id,
                `ezform`.`ezf_name` AS `name`
                FROM
                `ezform_fields`
                JOIN `ezform`
                ON `ezform_fields`.`ezf_id` = `ezform`.`ezf_id` 
                JOIN `ezform` AS `ezf_ref`
                ON `ezf_ref`.`ezf_id` = `ezform_fields`.`ref_ezf_id`
                WHERE
                `ezf_ref`.`ezf_id` = :id
                GROUP BY
                `ezform`.`ezf_id` ORDER BY `ezform`.`ezf_name`";
        $data = Yii::$app->db->createCommand($sql, [':id' => $ezf_id])->queryAll();
        
        return $this->renderAjax("/widgets/_subselect", [
                    'id' => $id,
                    'name' => $name,
                    'value' => $value,
                    'multiple'=>$multiple,
                    'placeholder' => Yii::t('ezform', 'Select form ...'),
                    'data' => \yii\helpers\ArrayHelper::map($data, 'id', 'name'),
        ]);
    }
    
    public function actionWidgetEzformFields() {
        $ezf_id = isset($_POST['ref_id']) ? $_POST['ref_id'] : 0;
        $name = isset($_POST['name']) ? $_POST['name'] : '';
        $value = isset($_POST['value']) ? $_POST['value'] : '';
        $multiple = isset($_POST['multiple']) ? (int)$_POST['multiple'] :0;
        
        if($multiple == 1 && $value != ''){
            $value = \appxq\sdii\utils\SDUtility::string2Array($value);
        }
        
        $id = isset($_POST['id']) ? $_POST['id'] : \appxq\sdii\utils\SDUtility::getMillisecTime();
        $sql = "SELECT ezf_field_name AS `id`, concat(ezf_field_name, ' (', ezf_field_label, ')') AS`name` FROM `ezform_fields` WHERE `ezf_id` = :id AND table_field_type not in('none','field') ORDER BY ezf_field_order";
        $data = Yii::$app->db->createCommand($sql, [':id' => $ezf_id])->queryAll();

        return $this->renderAjax("/widgets/_subselect", [
                    'id' => $id,
                    'name' => $name,
                    'value' => $value,
                    'multiple'=>$multiple,
                    'data' => \yii\helpers\ArrayHelper::map($data, 'id', 'name'),
        ]);
    }
    
    public function actionParentFields() {
        $ezf_id = isset($_POST['ezf_id']) ? $_POST['ezf_id'] : 0;
        $target = EzfQuery::getTargetOne($ezf_id);
        if($target){
            return $target['parent_ezf_id'];
        }
        
        return $ezf_id;
    }

    public function actionFindTarget($q = null, $id = null) {
        $ezf_field_id = isset($_GET['ezf_field_id']) ? $_GET['ezf_field_id'] : 0;
        $ezf_id = isset($_GET['ezf_id']) ? $_GET['ezf_id'] : 0;
        
        $filter_name = isset($_GET['filter_name']) ? $_GET['filter_name'] : '';
        $filter_id = isset($_GET['filter_id']) ? $_GET['filter_id'] : '';
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => []];

        $dataEzf = EzfQuery::getEzformTargetField($ezf_field_id);
        $modelFields = EzfQuery::findSpecialOne($ezf_id);
        $ezform = EzfQuery::getEzformOne($dataEzf['ref_ezf_id']);
        
        if ($dataEzf) {
            $table = $dataEzf['ezf_table'];
            $ref_id = $dataEzf['ref_field_id'];
            $nameConcat = EzfFunc::array2ConcatStr($dataEzf['ref_field_desc']);
            if (!$nameConcat) {
                return $out;
            }

            $searchConcat = EzfFunc::array2ConcatStr($dataEzf["ref_field_search"]);
            if (!$searchConcat) {
                return $out;
            }
        } else {
            return $out;
        }

        if (is_null($q)) {
            $q = '';
        }
        
        $pamars = [':q' => "%$q%"];
        
        
        
        
        $query = new \yii\db\Query();
        $query->select(["`$ref_id` AS id", "$nameConcat AS `name`"]);
        $query->from("`$table`");
        
        $filterSql = '';
        if($filter_name!=''){
            $filterSql = " AND $filter_name LIKE :filter";
            $pamars[':filter'] = "%$filter_id%";
            
            $query->orderBy('create_date DESC');
        }
        
        $query->where("$searchConcat LIKE :q  AND rstat not in(0, 3) $filterSql", $pamars);
        $query->orderBy('`name`');
        $query->limit(50);
        
        if (isset($modelFields) || $ezform['public_listview'] == 2) {
           $query->andWhere('xsourcex = :site', [':site'=>Yii::$app->user->identity->profile->sitecode]);
        }
        
        if ($ezform['public_listview'] == 3) {
            $query->andWhere('xdepartmentx = :unit', [':unit' => Yii::$app->user->identity->profile->department]);
        }

        if ($ezform['public_listview'] == 0) {
            $query->andWhere("user_create=:created_by", [':created_by' => Yii::$app->user->id]);
        }
        
        $data = $query->createCommand()->queryAll();

        foreach ($data as $value) {
            $out["results"][] = ['id' => "{$value['id']}", 'text' => $value["name"]];
        }

        return $out;
    }

    public static function initTarget($model, $modelFields) {
        $options = SDUtility::string2Array($modelFields['ezf_field_options']);
        $code = $model[$modelFields['ezf_field_name']];
        $str = '';
        
        $modelEzf = EzfQuery::getEzformOne($modelFields['ref_ezf_id']);

        $table = $modelEzf['ezf_table'];
        $ref_id = $modelFields['ref_field_id'];
        $nameConcat = EzfFunc::array2ConcatStr($modelFields['ref_field_desc']);
        if (!$nameConcat) {
            return $str;
        }

        if (isset($code) && !empty($code)) {
            if(isset($options['options']['multiple']) && $options['options']['multiple']==1){
                $sql = "SELECT `$ref_id` AS id, $nameConcat AS`name` FROM `$table`";
                $data = Yii::$app->db->createCommand($sql)->queryAll();
                $str = $data;
            } else {
                $sql = "SELECT `$ref_id` AS id, $nameConcat AS`name` FROM `$table` WHERE `$ref_id` =:id";
                $data = Yii::$app->db->createCommand($sql, [':id' => $code])->queryOne();
                $str = $data['name'];
            }
        }

        return $str;
    }
    
    public function actionGencid() {
        $ezf_id = isset($_GET['ezf_id']) ? $_GET['ezf_id'] : 0;
        $ezf_field_id = isset($_GET['ezf_field_id']) ? $_GET['ezf_field_id'] : 0;
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $data_field = EzfQuery::getEzformWithField($ezf_field_id);
        if($data_field){
            $maxnum = EzfQuery::genCidForeigner($data_field['ezf_table'], $data_field['ezf_field_name']);
            if($maxnum){
                $maxnum = $maxnum+1;
            } else {
                $maxnum = 9999000000001;
            }
            
            $result = [
                'status' => 'success',
                'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
                'data' => $maxnum,
            ];
            return $result;
        }
        
        $result = [
            'status' => 'error',
            'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not load the data.'),
            'data' => 0,
        ];
        return $result;
        
    }
    
    public function actionCheckcid() {
        $cid = isset($_GET['cid']) ? $_GET['cid'] : 0;
        $ezf_id = isset($_GET['ezf_id']) ? $_GET['ezf_id'] : 0;
        $ezf_field_id = isset($_GET['ezf_field_id']) ? $_GET['ezf_field_id'] : 0;
        $initdata_default = isset($_GET['initdata']) ? EzfFunc::stringDecode2Array($_GET['initdata']) : [];
        
        $data_field = EzfQuery::getEzformWithField($ezf_field_id);
        $userProfile = Yii::$app->user->identity->profile;
        $checkcid = EzfFunc::check_citizen($cid);
        if($checkcid){
            $data = EzfQuery::checkCidAll($data_field['ezf_table'], $data_field['ezf_field_name'], $cid);
            if($data){
                $dataSpecial;
                foreach ($data as $key => $value) {
                    if($value['xsourcex']==$userProfile->sitecode){
                        $dataSpecial['present']=$value;
                    } elseif($value['ptid']==$value['target']){
                        $dataSpecial['first']=$value;
                    }
                }

                if(isset($dataSpecial['present'])){
                    //return '<div class="alert alert-danger" role="alert" style="font-size: 20px;">'.SDHtml::getMsgError() . Yii::t('ezform', 'Have this card number in the agency.').'</div>';
                    $dataSet = EzfFunc::arrayEncode2String($initdata_default);
                    
                    return $this->renderAjax("/widgets/_cidselect", [
                                'initdata' => $dataSet,
                                'initdataEmpty' => [],
                                'ezf_id' => $ezf_id,
                                'dataid' => $dataSpecial['present']['id'],
                                'data' => $dataSpecial['present'],
                                'type' => 3,
                    ]);
                } elseif($dataSpecial['first']) {
                    //clone
                    unset($dataSpecial['first']['id']);
                    $initdata = $dataSpecial['first'];
                    $initdata = \yii\helpers\ArrayHelper::merge($initdata_default, $initdata);
                    $dataSet = EzfFunc::arrayEncode2String($initdata);

                    $initdataEmpty = [
                        'ptid'=>$dataSpecial['first']['ptid'],
                        'sitecode'=>$dataSpecial['first']['sitecode'],
                        'ptcode'=>$dataSpecial['first']['ptcode'],
                        'ptcodefull'=>$dataSpecial['first']['ptcodefull'],
                        $data_field['ezf_field_name']=>$cid
                    ];
                    $initdataEmpty = \yii\helpers\ArrayHelper::merge($initdata_default, $initdataEmpty);
                    $dataSetEmpty = EzfFunc::arrayEncode2String($initdataEmpty);
                    
                    return $this->renderAjax("/widgets/_cidselect", [
                                'initdata' => $dataSet,
                                'initdataEmpty' => $dataSetEmpty,
                                'ezf_id' => $ezf_id,
                                'data' => $dataSpecial['first'],
                                'type' => 2,
                    ]);
                }
            } 
            // new
            $initdata = [$data_field['ezf_field_name']=>$cid];
            $initdata = \yii\helpers\ArrayHelper::merge($initdata_default, $initdata);
            $dataSet = EzfFunc::arrayEncode2String($initdata);
            
            return $this->renderAjax("/widgets/_cidselect", [
                        'initdata' => $dataSet,
                        'ezf_id' =>$ezf_id,
                        'type'=>1,
            ]);
            
        }
        return '<div class="alert alert-danger" role="alert" style="font-size: 20px;">'.SDHtml::getMsgError() . Yii::t('ezform', 'Invalid card number.').'</div>';
    }

}
