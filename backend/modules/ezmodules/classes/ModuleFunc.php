<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\modules\ezmodules\classes;

use Yii;
use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\ezforms2\classes\EzfUiFunc;
use appxq\sdii\utils\SDUtility;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\helpers\Html;
use backend\modules\ezforms2\classes\EzfHelper;
use backend\modules\ezforms2\classes\EzfFunc;

/**
 * Description of ModuleFunc
 *
 * @author appxq
 */
class ModuleFunc {

    //put your code here
    public static function checkthai($word) {
        return preg_replace('/[^ก-๙]/ u', '', $word);
    }

    public static function getNoIconModule() {
        return Yii::getAlias('@storageUrl/ezform/img/no_icon.png');
    }

    public static function getNoUserImage() {
        return Yii::getAlias('@storageUrl/images/nouser.png');
    }

    public static function itemAlias($code, $key = NULL) {
        $items = [
            'type' => [
                Yii::t('ezmodule', 'Internal Module'),
                Yii::t('ezmodule', 'External Module'),
            ],
            'system' => [
                Yii::t('ezmodule', 'Disable'),
                Yii::t('ezmodule', 'Enable'),
            ],
            'public' => [
                Yii::t('ezmodule', 'Private'),
                Yii::t('ezmodule', 'Public'),
            ],
            'approved' => [
                Yii::t('ezmodule', 'Waiting for approval.'),
                Yii::t('ezmodule', 'Approved'),
            ],
            'align' => [
                'left' => Yii::t('ezmodule', 'Left'),
                'center' => Yii::t('ezmodule', 'Center'),
                'right' => Yii::t('ezmodule', 'Right'),
            ],
            'sqlFunction' => [
                '' => '',
                'DATE({xfieldx})' => 'DATE',
                'SUM({xfieldx})' => 'SUM',
                'SUM(IF(ISNULL({xfieldx}),0,1))' => 'SUM-IFNULL',
                'COUNT(*)' => 'COUNT*',
                'CAST({xfieldx} AS UNSIGNED)' => 'INTEGER',
                '{xfieldx}+0' => 'Number',
            ],
            'sqlCondition' => [
                '=' => '=', '<' => '<', '>' => '>', '<=' => '<=', '>=' => '>=', '<>' => '!=', 'LIKE' => 'LIKE', 'IN' => 'IN', 'NOT IN' => 'NOT IN', 'IS '=>'IS ', 'BETWEEN' => 'BETWEEN'
            ],
            'sqlAndOr' => [
                'OR' => 'OR', 'AND' => 'AND'
            ],
            'phpCondition' => [
                '==' => '=', '<' => '<', '>' => '>', '<=' => '<=', '>=' => '>=', '!=' => '!=' //, 'BETWEEN' => 'Between'
            ],
            'phpAndOr' => [
                '||' => 'OR', '&&' => 'AND'
            ],
            'widget' => [
                'content' => Yii::t('ezmodule', 'Record display'), 
                'target' => Yii::t('ezmodule', 'Parent Selector'),
                'datalist' => Yii::t('ezmodule', 'Data Grid'),
                'sql_grid' => Yii::t('ezmodule', 'Data Grid by SQL'),
                'multiple_form' => Yii::t('ezmodule', 'Multiple Form'),
                'ezmap' => Yii::t('ezmodule', 'EzMap'), 
                'ezcalendar' => Yii::t('ezmodule', 'EzCalendar'), 
                'drag_drop_widget' => Yii::t('ezmodule', 'Drag and Drop'),
                'sidemenu' => Yii::t('ezmodule', 'Side Menu'),
                'dropdown_menu' => Yii::t('ezmodule', 'Dropdown Menu'),
                'ajax_content' => Yii::t('ezmodule', 'Ajax Content'),
                'double_data' => Yii::t('ezmodule', 'Double Data Entry'),
                'compare' => Yii::t('ezmodule', 'Compare Data Grid'),
                'eztimeline' => Yii::t('ezmodule', 'EzTimeLine'),
                'process_control' => Yii::t('ezmodule', 'Process Control'),
                
                'core_content' => Yii::t('ezmodule', 'Record display (V2)'), 
                'core_target' => Yii::t('ezmodule', 'Parent Selector (V2)'),
                'core_target_sql' => Yii::t('ezmodule', 'Parent Selector By EzSQL (V2)'),
                'core_addbtn_reload' => Yii::t('ezmodule', 'Create Reload Page (V2)'),
                'core_ezform_render' => Yii::t('ezmodule', 'EzForm Render (V2)'),
                'core_grid' => Yii::t('ezmodule', 'Data Grid (V2)'),
                'core_sql_grid' => Yii::t('ezmodule', 'Data Grid by SQL (V2)'),
                'core_grid_column' => Yii::t('ezmodule', 'Grid Column (V2)'),
                'core_listview' => Yii::t('ezmodule', 'List View by SQL (V2)'),
                'core_multiple_form' => Yii::t('ezmodule', 'Multiple Form (V2)'),
                'core_ezmap' => Yii::t('ezmodule', 'EzMap (V2)'), 
                'core_ezcalendar' => Yii::t('ezmodule', 'EzCalendar (V2)'), 
                'core_sidemenu' => Yii::t('ezmodule', 'Side Menu (V2)'),
                'core_dropdown_menu' => Yii::t('ezmodule', 'Dropdown Menu (V2)'),
                'core_select_items' => Yii::t('ezmodule', 'Select Menu EzSQL (V2)'),
                'core_drag_drop_widget' => Yii::t('ezmodule', 'Drag and Drop (V2)'),
                'core_ajax_content' => Yii::t('ezmodule', 'Ajax Content (V2)'),
                'core_double_data' => Yii::t('ezmodule', 'Double Data Entry (V2)'),
                'core_compare' => Yii::t('ezmodule', 'Compare Data Grid (V2)'),
                'core_eztimeline' => Yii::t('ezmodule', 'EzTimeLine (V2)'),
            ],
            'tab' => [
                'form' => Yii::t('ezmodule', 'Data Grid'), 
                'module' => Yii::t('ezmodule', 'Module'), 
                'html' => Yii::t('ezmodule', 'HTML Editor'), 
                'ezmap' => Yii::t('ezmodule', 'EzMap'), 
                'ezcalendar' => Yii::t('ezmodule', 'EzCalendar'), 
                'communication' => Yii::t('ezmodule', 'Communication Pad'), 
            ],
            'action' => [
                'create' => Yii::t('app', 'Create'), 
                'update' => Yii::t('app', 'Update'),
                'delete' => Yii::t('app', 'Delete'),
                'view' => Yii::t('app', 'View'),
                'data_table' => Yii::t('app', 'Data Table'),
                //'search' => 'search',
            ],
            'display'=>[
                'content_v'=>Yii::t('ezmodule', 'Vertical'),
                'content_h'=>Yii::t('ezmodule', 'Horizontal'),
                'content_table'=>Yii::t('ezmodule', 'Table'),
                'custom'=>Yii::t('ezmodule', 'Custom'),
            ],
            'theme'=>[
                'default'=>Yii::t('ezmodule', 'Default'),
                'primary'=>Yii::t('ezmodule', 'Primary'),
                'success'=>Yii::t('ezmodule', 'Success'),
                'info'=>Yii::t('ezmodule', 'Info'),
                'warning'=>Yii::t('ezmodule', 'Warning'),
                'danger'=>Yii::t('ezmodule', 'Danger'),
            ],
        ];

        $return = $items[$code];

        if (isset($key)) {
            return isset($return[$key]) ? $return[$key] : [];
        } else {
            return isset($return) ? $return : [];
        }
    }

    public static function getFieldsData($data) {
        $field_data = SDUtility::string2Array($data);
        $fields = [];
        if (isset($field_data['fields'])) {
            foreach ($field_data['fields'] as $key => $value) {
                $fields[] = $value['attribute'];
            }
        }
        return $fields;
    }

    public static function getStatusIcon($rstat) {

        $icon = 'class="fa fa-clock-o fa-lg"';

        if ($rstat == 2) {
            $icon = 'class="fa fa-check-circle fa-lg"';
        } elseif ($rstat == 4) {
            $icon = 'class="fa fa-snowflake-o fa-lg"';
        } elseif ($rstat == 5) {
            $icon = 'class="fa fa-lock fa-lg"';
        } else {
            $icon = 'class="fa fa-clock-o fa-lg"';
        }

        return "<i $icon></i>";
    }

    public static function modelSearch($model, $ezform, $targetField, $specialField, $ezformParent, $modelFields, $modelFilter, $filter, $module, $params) {
        //$model = new TbdataAll();
        $addonFields = [];
        $userProfile = Yii::$app->user->identity->profile;

        $selectCol = [];
        //\appxq\sdii\utils\VarDumper::dump($ezform['ezf_table']);

        $query = $model->find()->where("{$ezform['ezf_table']}.rstat not in(0,3)"); //->where('rstat not in(0, 3)');
        //$query = new \yii\db\Query();
        $ezformTmp[$ezform['ezf_id']] = $ezform['ezf_table'];
        if (isset($targetField)) {// join 
            $pk = $targetField->ezf_field_name;
            $pkJoin = $targetField->ref_field_id;
//	    if(isset($specialField)){
//		$pk = 'ptid';
//		$pkJoin = 'ptid';
//	    }
            $ezformTmp[$ezformParent['ezf_id']] = $ezformParent['ezf_table'];
            $query->innerJoin($ezformParent['ezf_table'], "`{$ezformParent['ezf_table']}`.`$pkJoin` = `{$ezform['ezf_table']}`.`$pk`");
            $query->andWhere("`{$ezformParent['ezf_table']}`.rstat NOT IN(0,3)");
            $query->groupBy("{$ezformParent['ezf_table']}.id");
        }
        //\appxq\sdii\utils\VarDumper::dump(\yii\helpers\ArrayHelper::getColumn($modelFields, 'ezf_field_name'));
        if (isset($modelFields) && !empty($modelFields)) { //fields
            $tmpCol = [];
            foreach ($modelFields as $field) {
                //$fieldOptions = SDUtility::string2Array($field['options']);
                $currentForm = [$ezform['ezf_id']];
                if (isset($ezformParent['ezf_id'])) {
                    $currentForm[] = $ezformParent['ezf_id'];
                }

                if (in_array($field['ezf_id'], $currentForm)) {

                    if (!in_array($field['ezf_id'] . $field['ezf_field_id'], $tmpCol)) {
                        $tmpCol[] = $field['ezf_id'] . $field['ezf_field_id'];
                        if (isset($ezformParent['ezf_id']) && $ezformParent['ezf_id'] == $field['ezf_id']) {
                            $addonFields[] = 'fparent_' . $field['ezf_field_name'];

                            if ($field['table_field_type'] == 'field') {
                                $fieldsChildren = self::getFieldsData($field['ezf_field_data']);
                                foreach ($fieldsChildren as $field_child) {
                                    if (!in_array($ezformParent['ezf_table'] . '.' . $field_child, $selectCol)) {
                                        $selectCol[] = $ezformParent['ezf_table'] . '.' . $field_child;
                                        $addonFields[] = $field_child;
                                    }
                                }
                            } else {
                                $selectCol[] = $ezformParent['ezf_table'] . '.' . $field['ezf_field_name'] . ' AS fparent_' . $field['ezf_field_name'];
                            }
                        } else {
                            if ($field['table_field_type'] == 'field') {
                                $addonFields = \yii\helpers\ArrayHelper::merge($addonFields, [$field['ezf_field_name']]);

                                $fieldsChildren = self::getFieldsData($field['ezf_field_data']);
                                foreach ($fieldsChildren as $field_child) {
                                    if (!in_array($ezform['ezf_table'] . '.' . $field_child, $selectCol)) {
                                        $selectCol[] = $ezform['ezf_table'] . '.' . $field_child;
                                    }
                                }
                            } else {
                                $selectCol[] = $ezform['ezf_table'] . '.' . $field['ezf_field_name'];
                            }
                        }
                    }
                }
            }

            if (isset($ezformParent['ezf_table'])) {
                $addonFields = ArrayHelper::merge($addonFields, ['fparent_id', 'fparent_target', 'fparent_ptid', 'fparent_rstat']);
                $selectCol = \yii\helpers\ArrayHelper::merge($selectCol, ["{$ezformParent['ezf_table']}.id AS fparent_id", "{$ezformParent['ezf_table']}.target AS fparent_target", "{$ezformParent['ezf_table']}.ptid AS fparent_ptid", "{$ezformParent['ezf_table']}.rstat AS fparent_rstat"]);
            }

            $selectCol = \yii\helpers\ArrayHelper::merge($selectCol, ["{$ezform['ezf_table']}.id", "{$ezform['ezf_table']}.target", "{$ezform['ezf_table']}.ptid", "{$ezform['ezf_table']}.xsourcex", "{$ezform['ezf_table']}.create_date", "{$ezform['ezf_table']}.rstat", "{$ezform['ezf_table']}.ezf_version"]);
        } else {
            $selectCol[] = "{$ezform['ezf_table']}.*";
        }
        $model->setColFieldsAddon($addonFields);

        $query->select($selectCol);

        if ($ezform['public_listview'] == 0) {
            $query->andWhere("{$ezform['ezf_table']}.user_create=:created_by", [':created_by' => Yii::$app->user->id]);
        }
        
        if ($ezform['public_listview'] == 3) {
            $query->andWhere("{$ezform['ezf_table']}.xdepartmentx = :unit", [':unit' => $userProfile->department]);
        }

        if (isset($specialField)) {
            $query->andWhere("{$ezform['ezf_table']}.hsitecode = :site", [':site' => $userProfile->sitecode]);
        } elseif($ezform['public_listview'] == 2) {
            $query->andWhere("{$ezform['ezf_table']}.xsourcex = :site", [':site' => $userProfile->sitecode]);
        }

        if (isset($modelFilter)) { //filter
            if (isset($modelFilter['filter_type']) && $modelFilter['filter_type'] == 1) {
                $options = SDUtility::string2Array($modelFilter['options']);

                if (isset($options) && !empty($options)) {
                    //innerJoin
                    $ezformTmpUnipue = [];
                    foreach ($options as $key => $condition) {
                        if ($ezform['ezf_id'] != $condition['form']) {
                            if (isset($ezformParent['ezf_id']) && $ezformParent['ezf_id'] == $condition['form']) {
                                //มีการ join แล้วไม่ต้องทำอีก
                            } else {

                                if (!in_array($condition['form'], $ezformTmpUnipue)) {
                                    $modelTarget = EzfQuery::getTargetOne($condition['form']);
                                    $refFormCond = SDUtility::string2Array($modelTarget['ref_form']);
                                    $refFormTarget = [];
                                    if (isset($targetField)) {
                                        $refFormTarget = SDUtility::string2Array($targetField['ref_form']);
                                    }

                                    $lvlTarget = count($refFormTarget);
                                    $lvlCond = count($refFormCond);

                                    if ($lvlTarget < $lvlCond) {//ต่ำกว่าฟอร์มตั้งต้น
                                        if (isset($refFormCond[$ezform['ezf_id']])) {
                                            $joinField = $refFormCond[$ezform['ezf_id']];
                                            $modelEzfCond = EzfQuery::getFormTableName($condition['form']);
                                            $query->innerJoin($modelEzfCond['ezf_table'], "{$modelEzfCond['ezf_table']}.$joinField = {$ezform['ezf_table']}.id");

                                            $ezformTmp[$modelEzfCond['ezf_id']] = $modelEzfCond['ezf_table'];
                                        } elseif ($modelTarget['ref_ezf_id'] == $ezform['ezf_id']) {// กรณี ref กัน 1 lvl
                                            $joinField = $modelTarget['ezf_field_name'];
                                            $modelEzfCond = EzfQuery::getFormTableName($condition['form']);
                                            $query->innerJoin($modelEzfCond['ezf_table'], "{$modelEzfCond['ezf_table']}.$joinField = {$ezform['ezf_table']}.id");

                                            $ezformTmp[$modelEzfCond['ezf_id']] = $modelEzfCond['ezf_table'];
                                        }
                                    } elseif ($lvlTarget == $lvlCond) {
                                        //$modelTarget = EzfQuery::getTargetOne($condition['form']);
                                        $modelEzfCond = EzfQuery::getFormTableName($condition['form']);
                                        if (isset($modelTarget)) {//ต่ำกว่าฟอร์มตั้งต้น
                                            if ($ezform['ezf_id'] == $modelTarget['ref_ezf_id']) {
                                                $query->innerJoin($modelEzfCond['ezf_table'], "{$modelEzfCond['ezf_table']}.{$modelTarget['ezf_field_name']} = {$ezform['ezf_table']}.id");
                                                $ezformTmp[$modelEzfCond['ezf_id']] = $modelEzfCond['ezf_table'];
                                            }
                                        } else {//สูงกว่าฟอร์มตั้งต้น
                                            if (isset($targetField)) {
                                                if ($condition['form'] == $targetField['ref_ezf_id']) {
                                                    $query->innerJoin($modelEzfCond['ezf_table'], "{$modelEzfCond['ezf_table']}.id = {$ezform['ezf_table']}.{$targetField['ezf_field_name']}");
                                                    $ezformTmp[$modelEzfCond['ezf_id']] = $modelEzfCond['ezf_table'];
                                                }
                                            }
                                        }
                                    } elseif ($lvlTarget > $lvlCond) {//สูงกว่าฟอร์มตั้งต้น
                                        if (isset($refFormTarget[$condition['form']])) {
                                            $joinField = $refFormTarget[$condition['form']];
                                            $modelEzfCond = EzfQuery::getFormTableName($condition['form']);
                                            $query->innerJoin($modelEzfCond['ezf_table'], "{$modelEzfCond['ezf_table']}.id = {$ezform['ezf_table']}.$joinField");

                                            $ezformTmp[$modelEzfCond['ezf_id']] = $modelEzfCond['ezf_table'];
                                        }
                                    }

                                    $ezformTmpUnipue[] = $condition['form'];
                                }
                            }
                        }
                    }

                    //Where
                    $condType = 'AND';
                    foreach ($options as $key => $condition) {
                        $whereFunc = 'andWhere';
                        if ($condType == 'OR') {
                            $whereFunc = 'orWhere';
                        }

                        if ($condition['cond'] != 'BETWEEN') {
                            $pos = strpos($condition['value1'], '_FIELD_');
                            $checkPos = $pos === false;
                            if ($condition['value1'] == '_SITECODE_') {
                                $query->$whereFunc("{$ezformTmp[$condition['form']]}.{$condition['field']} {$condition['cond']} :cond_$key", [":cond_$key" => $userProfile->sitecode]);
                            } elseif ($checkPos !== true) {
                                $fieldStr = str_replace('_FIELD_', '', $condition['value1']);
                                $query->$whereFunc("{$ezformTmp[$condition['form']]}.{$condition['field']} {$condition['cond']} $fieldStr");
                            } else {
                                $query->$whereFunc("{$ezformTmp[$condition['form']]}.{$condition['field']} {$condition['cond']} :cond_$key", [":cond_$key" => $condition['value1']]);
                            }
                        } else {
                            $query->$whereFunc("{$ezformTmp[$condition['form']]}.{$condition['field']} {$condition['cond']} :cond1_$key AND :cond2_$key", [":cond1_$key" => $condition['value1'], ":cond2_$key" => $condition['value2']]);
                        }

                        $condType = $condition['more'];
                    }
                }
            } else {
                $query->innerJoin('ezmodule_filter_list fl', "fl.dataid = {$ezform['ezf_table']}.id");
                $query->andWhere('fl.filter_id = :filter_id AND fl.ezm_id = :ezm_id', [':filter_id' => $filter, ':ezm_id' => $module]);
            }
        }

        //\appxq\sdii\utils\VarDumper::dump($query->createCommand()->rawSql);
        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'create_date' => SORT_DESC
                ]
            ],
            'pagination' => [
                'pageSize' => 50,
            //'route' => '/ezforms2/fileinput/grid-update',
            ],
//            'sort' => [
//                'route' => '/ezforms2/fileinput/grid-update',
//            ]
        ]);

        $model->load($params);


        if (isset($model['create_date']) && !empty($model['create_date'])) {
            $daterang = explode(' to ', $model['create_date']);
            if (isset($daterang[1])) {
                $sdate = \appxq\sdii\utils\SDdate::phpThDate2mysqlDate($daterang[0], '-');
                $edate = \appxq\sdii\utils\SDdate::phpThDate2mysqlDate($daterang[1], '-');

                $query->andFilterWhere(['between', 'date(' . $ezform['ezf_table'] . '.create_date)', $sdate, $edate]);
            }
        }

        if (isset($modelFields) && !empty($modelFields)) { //fields
            $tmpCol = [];

            foreach ($modelFields as $field) {
                //$fieldOptions = SDUtility::string2Array($field['options']);
                $currentForm = [$ezform['ezf_id']];
                if (isset($ezformParent['ezf_id'])) {
                    $currentForm[] = $ezformParent['ezf_id'];
                }

                if (in_array($field['ezf_id'], $currentForm)) {
                    if (!in_array($field['ezf_id'] . $field['ezf_field_id'], $tmpCol)) {
                        $tmpCol[] = $field['ezf_id'] . $field['ezf_field_id'];
                        $field_name = $field['ezf_field_name'];
                        $table = $ezform['ezf_table'];
                        if (isset($ezformParent['ezf_id']) && $ezformParent['ezf_id'] == $field['ezf_id']) {
                            $field_name = 'fparent_' . $field['ezf_field_name'];
                            $table = $ezformParent['ezf_table'];
                        }

                        if (isset($model[$field_name]) && !empty($model[$field_name])) {
                            $query->andFilterWhere(['like', $table . '.' . $field['ezf_field_name'], $model[$field_name]]);
                        }
                    }
                }
            }
        }

        return $dataProvider;
    }

    public static function generateInputOnly($model, $modelFields, $dataInput, $disableFields = 0) {
        $html = '';

        try {
            if ($modelFields['table_field_type'] != 'none' && $modelFields['table_field_type'] != '') {
                $dataInput;

                if ($dataInput) {
                    $options = SDUtility::string2ArrayJs($modelFields['ezf_field_options']);
                    unset($options['specific']);

                    $data = SDUtility::string2Array($modelFields['ezf_field_data']);

                    //inline, label fix
                    if ($dataInput['input_function'] == 'widget') {
                        
                        if (isset(Yii::$app->session['show_varname']) && Yii::$app->session['show_varname']) {
                            $options['options']['annotated'] = 1;
                        }

                        if ($disableFields) {
                            $options['options']['disabled'] = $disableFields;
                        }

                        if (!empty($data)) {
                            if (isset($data['items'])) {
                                $options['data'] = $data['items'];
                            }

                            if (isset($data['func'])) {
                                try {
                                    $params = [
                                        'field'=>$modelFields,
                                        'data'=>$model
                                    ];
                                    eval("\$dataItems = {$data['func']};");
                                } catch (\yii\base\Exception $e) {
                                    \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
                                    $dataItems = [];
                                }
                                $options['data'] = $dataItems;
                            }

                            if (isset($data['fields'])) {
                                $options['fields'] = $data['fields'];
                            }
                        }

                        $widget_render = '';
                        if (isset($model[$modelFields['ezf_field_name']]) && !empty($model[$modelFields['ezf_field_name']])) {

                            if (isset($options['options']['data-func-set']) && !empty($options['options']['data-func-set'])) {
                                $pathStr = [
                                    '{model}' => "\$model",
                                    '{modelFields}' => "\$modelFields",
                                ];

                                $funcSet = strtr($options['options']['data-func-set'], $pathStr);

                                try {
                                    $initial = eval("return $funcSet;");
                                } catch (\yii\base\Exception $e) {
                                    \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
                                    $initial = FALSE;
                                }


                                if ($initial) {
                                    if (isset($options['options']['data-name-in'])) {
                                        $data_in = $options['options']['data-name-in'];
                                        $data_set = \backend\modules\ezforms2\classes\EzfFunc::addProperty($data_in, $options['options']['data-name-set'], $initial);
                                        $options = ArrayHelper::merge($options, $data_set);
                                    } else {
                                        $options[$options['options']['data-name-set']] = $initial;
                                    }
                                }
                            }
                        }

                        $attribute = $modelFields['ezf_field_name'];
                        if (isset($options['options']['multiple']) && $options['options']['multiple'] == true) {
                            $attribute = $modelFields['ezf_field_name'] . '[]';
                        }

                        $arryClass = explode('::', $dataInput['input_class']);

                        $options['model'] = $model;
                        $options['attribute'] = $attribute;

                        $options['id'] = 'SD_' . $modelFields['ezf_id'] . '_' . $modelFields['ezf_field_name'].'_'.SDUtility::getMillisecTime();
                        $options['options']['id'] = $options['id'];
                        
                        eval("\$html = {$arryClass[0]}::widget(\$options);");
                    } else {
                        if (isset(Yii::$app->session['show_varname']) && Yii::$app->session['show_varname']) {
                            $options['annotated'] = 1;
                        }

                        if ($disableFields) {
                            $options['disabled'] = $disableFields;
                        }

                        $input_function = 'active' . ucfirst($dataInput['input_function']);

                        if (isset($options['class'])) {
                            $options['class'] .= ' form-control';
                        } else {
                            $options['class'] = 'form-control';
                        }

                        if (empty($data)) {
                            eval("\$html = \yii\helpers\Html::$input_function(\$model, \$modelFields['ezf_field_name'], \$options);");
                        } else {
                            if (isset($data['func'])) {
                                eval("\$dataItems = {$data['func']};");
                            } else {
                                $dataItems = $data['items'];
                            }

                            eval("\$html = \yii\helpers\Html::$input_function(\$model, \$modelFields['ezf_field_name'], \$dataItems, \$options);");
                        }
                    }
                } else {
                    $html = \yii\helpers\Html::activeHiddenInput($model, $modelFields['ezf_field_name']);
                }
            }

            return $html;
        } catch (yii\base\Exception $e) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
            return '<code>' . $e->getMessage() . '</code>';
        }
    }

    public static function formsItemWidget($ezf_id, $parent_ezf_id, $special, $reloadDiv, $data, $options, $modal = 'modal-ezform-main') {

        $url = Url::to(['/ezmodules/ezmodule-forms/forms-widget', 'ezf_id' => $ezf_id, 'parent_ezf_id' => $parent_ezf_id, 'special' => $special, 'data' => $data, 'modal' => $modal, 'reloadDiv' => $reloadDiv, 'options' => $options]);
        $html = Html::tag('div', '<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>', ['id' => $reloadDiv, 'data-url' => $url, 'data-reload' => 0]);
        $view = \Yii::$app->getView();

        $view->registerJs("
            $.ajax({
                method: 'POST',
                url: '$url',
                dataType: 'HTML',
                success: function(result, textStatus) {
                    $('#$reloadDiv').html(result);
                }
            });   
        ");

        return $html;
    }

    public static function getCondition($data, $joinData, $options, $pkJoin, &$ezformTable) {
        if (isset($options['conditions'])) {
            $sqlSelect = [];

            foreach ($options['conditions'] as $key => $value) {
                if (isset($value['form']) && !empty($value['form'])) {
                    if (!isset($ezformTable[$value['form']])) {
                        $ezform = \backend\modules\ezforms2\classes\EzfQuery::getEzformById($value['form']);
                        $ezformTable[$value['form']] = $ezform;
                    }

                    $sqlSelect[] = "(SELECT GROUP_CONCAT({$value['field']}) AS value FROM {$ezformTable[$value['form']]['ezf_table']} WHERE $pkJoin = :id AND rstat<>0 AND rstat<>3) AS cond_$key";
                }
            }

            $selecStr = implode(', ', $sqlSelect);
            $sql = "SELECT $selecStr";

            $dataCond = Yii::$app->db->createCommand($sql, [':id' => $data[$joinData]])->queryOne();
            $arrCond = [];

            foreach ($options['conditions'] as $key => $value) {
                if (isset($value['form']) && !empty($value['form'])) {
                    $index = "cond_$key";
                    if (isset($dataCond[$index])) {
                        $condArry = explode(',', $dataCond[$index]);
                        if (isset($condArry) && !empty($condArry)) {
                            $condVarAll = false;
                            foreach ($condArry as $value_cond) {
                                $condVar = false;

                                if ($value['cond'] == 'BETWEEN') {
                                    $strCond = "'{$value_cond}'>='{$value['value1']}' && '{$value_cond}'<='{$value['value2']}'";

                                    eval("\$condVar = $strCond;");
                                } elseif ($value['cond'] == 'func') {
                                    $template = $value['value1'];
                                    $strCond = strtr($template, ['{value}' => $value_cond]);

                                    eval("\$condVar = $strCond;");
                                } else {

                                    $strCond = "'{$value_cond}'" . $value['cond'] . "'{$value['value1']}'";

                                    eval("\$condVar = ($strCond);");
                                }
                                $condVarAll = $condVarAll || $condVar;
                            }

                            $arrCond[] = [
                                'cond' => ($condVarAll) ? 'TRUE' : 'FALSE',
                                'with' => $value['more'],
                            ];
                        }
                    }
                }
            }

            if (!empty($arrCond)) {
                $strCond = '';
                $with = '';
                foreach ($arrCond as $ackey => $acvalue) {
                    $strCond .= $with . $acvalue['cond'];
                    $with = " {$acvalue['with']} ";
                }

                eval("\$show = $strCond;");

                return $show;
            }
        }

        return true;
    }

    public static function getItemsBtn($data, $joinData, $groupField, $options, $ezf_id, $pkJoin, $unique_record, $reloadDiv, $modal, &$ezformTable) {
        $fieldShow = ['id', 'target', 'ptid', 'rstat'];
        $unique_record = isset($unique_record)?$unique_record:1;
        
        if (isset($options['show'])) {
            foreach ($options['show'] as $key => $value) {
                if (isset($value['field']) && !empty($value['field'])) {
                    if (!in_array($value['field'], $fieldShow)) {
                        $fieldShow[] = $value['field'];
                    }
                }
            }
        }
        $selecStr = implode(', ', $fieldShow);
        $sql = "SELECT $selecStr FROM {$ezformTable[$ezf_id]['ezf_table']} WHERE $pkJoin = :id AND rstat<>0 AND rstat<>3 ";
        $dataItems = Yii::$app->db->createCommand($sql, [':id' => $data[$joinData]])->queryAll();
        
        $hasData = 0;
        $hasSubmitData = 0;
        $groupItems = [];
        $allItems = [];
        if (isset($dataItems)) {
            
            $numDraft = 0;
            $numSubmit = 0;
            $numAll = 0;
            $itemsBtn = [];
            
            foreach ($dataItems as $keyItem => $valueItem) {
                $groupItems[$valueItem[$groupField]] = $valueItem[$groupField];
                $allItems[$valueItem['id']] = $valueItem['id'];
                
                $hasData = 1;
                $numAll++;
                if ($valueItem['rstat'] == 1) {
                    $numDraft++;
                } elseif ($valueItem['rstat'] == 2) {
                    $numSubmit++;
                    $hasSubmitData = 1;
                }
                
                if (isset($options['show'])) {
                    foreach ($options['show'] as $key => $value) {
                        if (isset($value['field']) && !empty($value['field'])) {
                            $condVar = false;
                            if ($value['cond'] == 'BETWEEN') {
                                $strCond = "'{$valueItem[$value['field']]}'>='{$value['value1']}' && '{$valueItem[$value['field']]}'<='{$value['value2']}'";

                                eval("\$condVar = $strCond;");
                            } elseif ($value['cond'] == 'func') {
                                $template = $value['value1'];
                                $strCond = strtr($template, ['{value}' => $valueItem[$value['field']]]);

                                eval("\$condVar = $strCond;");
                            } else { //between
                                $strCond = "'{$valueItem[$value['field']]}'" . $value['cond'] . "'{$value['value1']}'";
                                
                                eval("\$condVar = $strCond;");
                            }
                            
                            if ($condVar) {
                                
                                $color = '';
                                if($value['color']){
                                    $color = "color: {$value['color']};";
                                }
                                $itemsBtn[$valueItem['id']] = Html::a((isset($value['icon']) ? "<i class=\"fa {$value['icon']} fa-lg\"></i>" : '?'), null, [
                                    'data-modal' => $modal,
                                    'data-url' => Url::to(['/ezforms2/ezform-data/ezform',
                                        'ezf_id' => $ezf_id,
                                        'dataid' => $valueItem['id'],
                                        'reloadDiv' => $reloadDiv,
                                        'modal' => $modal,
                                    ]),
                                    'style' => 'cursor: pointer; '.$color,
                                    'data-toggle' => 'tooltip',
                                    'title' => $value['label'],
                                    'class' => 'ezform-main-open',
                                ]);
                            }
                        }
                    }
                }
            }
        }
        $data_column = '';
        if(isset($options['fields'])){
            $fields = ArrayHelper::getColumn($options['fields'], 'field');
            $data_column = \backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String(array_values($fields));
        }
        $btnItems = [
            'emrBtn' => EzfHelper::btnOpenForm($ezf_id, '', "$numDraft / $numSubmit", [
                'class' => 'btn btn-warning btn-xs ezform-main-open',
                'data-modal' => $modal,
                'data-url' => Url::to(['/ezforms2/ezform-data/view',
                    'ezf_id' => $ezf_id,
                    'target' => $data[$joinData],
                    'targetField'=>$pkJoin,
                    'reloadDiv' => $reloadDiv,
                    'data_column'=>$data_column,
                    'popup' => 1,
                    'modal' => $modal,
                    'addbtn' => $unique_record == 1 ? 1 : 0,
                ]),
            ]),
            'itemsBtn' => implode(' ', $itemsBtn),
            'groupItems'=>$groupItems,
            'allItems'=>$allItems,
            'hasData' => $hasData,
            'hasSubmitData' => $hasSubmitData,
        ];
        return $btnItems;
    }

    public static function btnAdd($data, $joinData, $pkJoin, $special, $parent_ezf_id, $ezf_id, $reloadDiv, $modal) {
        if($parent_ezf_id>0){
            $targetField = EzfQuery::getTargetOne($parent_ezf_id);
            if($targetField){
                $ref_form = SDUtility::string2Array($targetField['ref_form']);
                $pkJoin = 'target';
                if(isset($ref_form[$targetField['parent_ezf_id']])){
                    $pkJoin = $ref_form[$targetField['parent_ezf_id']];
                }
                if ($special == 1) {
                    $pkJoin = 'ptid';
                }
            }
        }
        
        return EzfHelper::btnOpenForm($ezf_id, '', '<i class="fa fa-plus"></i>', [
                    'class' => 'btn btn-success btn-xs ezform-main-open',
                    'data-modal' => $modal,
                    'data-url' => Url::to(['/ezforms2/ezform-data/ezform',
                        'ezf_id' => $ezf_id,
                        'target' => $data[$joinData],
                        'targetField' => $pkJoin,
                        'reloadDiv' => $reloadDiv,
                        'modal' => $modal,
                    ])
        ]);
    }

    public static function getItemsProgress($data, $joinData, $special, $options, $ezf_id, $pkJoin, $reloadDiv, $modal, $progressTotal, &$ezformTable) {
        $parent_ezf_id = $ezf_id;
        $i=0;
        $htmlReturn = '';
        $countReturn = 0;
        $totalReturn = 0;
        
        foreach ($options as $key => $value) {
            $totalGroup = $progressTotal;
            
            $html = '';
            $progressCount = 0;
            $progressValue = 0;
            if(isset($value['form'])){
                $i++;
                $ezf_id = $value['form'];
                if (!isset($ezformTable[$ezf_id])) {
                    $ezform = \backend\modules\ezforms2\classes\EzfQuery::getEzformById($ezf_id);
                    $ezformTable[$ezf_id] = $ezform;
                }
                $ezform = $ezformTable[$ezf_id];
                
                if (isset($ezform)) {
                    $targetField = EzfQuery::getTargetOne($ezf_id);
                    if($targetField){
                        $ref_form = SDUtility::string2Array($targetField['ref_form']);
                        
                        $pkJoin = 'target';
                        if(isset($ref_form[$targetField['parent_ezf_id']])){
                            $pkJoin = $ref_form[$targetField['parent_ezf_id']];
                        }
                        $groupField = 'target';
                        if ($special == 1) {
                            $groupField = 'ptid';
                            $pkJoin = 'ptid';
                        }
                    }
                    
                    $showItems = ModuleFunc::getCondition($data, $joinData, $value, $pkJoin, $ezformTable);
                    $unique_record = $ezform['unique_record'];;

                    $btnItems = ['emrBtn' => '', 'itemsBtn' => '', 'hasData' => 0, 'hasSubmitData' => 0];
                    $btnadd = '';
                    if ($showItems) {
                        $btnItems = ModuleFunc::getItemsBtn($data, $joinData, $groupField, $value, $ezf_id, $pkJoin, $unique_record, $reloadDiv, $modal, $ezformTable);
                        $progressCount = count($btnItems['groupItems']);
                        $progressValue = count($btnItems['allItems']);
                        
                        $btnadd = ModuleFunc::btnAdd($data, $joinData, $pkJoin, $special, $parent_ezf_id, $ezf_id, $reloadDiv, $modal);
                        if($unique_record==2 && $btnItems['hasData']==1){
                            $btnadd = '';
                        } elseif ($unique_record==3 && $btnItems['hasSubmitData']==1) {
                            $btnadd = '';
                        }
                    }
                    
                    $color = 0;
                    if($progressCount < $totalGroup){
                        $color = 1;
                    }
                    
                    $progress = '';
                    if (isset($value['forms'])) {
                        $progressItems = ModuleFunc::getItemsProgress($data, $joinData, $special, $value['forms'], $ezf_id, $pkJoin, $reloadDiv, $modal, $progressValue, $ezformTable);
                        if($progressItems['count']>0 && $progressItems['total']>0){
                            $percent = ($progressItems['count']/$progressItems['total'])*100;
                        } else {
                            $percent = 0;
                        }
                        
                        
                        $totalGroup = $totalGroup+$progressValue;
                        $progressCount = $progressCount+$progressItems['count'];
                        
                        if($progressItems['html']!=''){
                            $progress = Yii::$app->controller->renderPartial('_popover', [
                                'content'=> Html::encode($progressItems['html']),
                                'percent'=> number_format($percent),
                                'form_name'=> $value['label'],
                            ]);
                        }
                    }
                    
                    $html = Yii::$app->controller->renderPartial('_grid_form', [
                        'color'=>$color,
                        'form_name'=>$value['label'],
                        'btnItems'=>$btnItems,
                        'btnadd'=>$btnadd,
                        'progress' => $progress
                    ]);
                }
                
                $htmlReturn .= $html;
                $countReturn += $progressCount;
                $totalReturn += $totalGroup;
            }
            
        }
        
        return [
            'html'=>$htmlReturn,
            'count'=>$countReturn,
            'total'=>$totalReturn,
        ];
    }
    
    public static function htmlFilter($value, $dataInput, $searchModel, $field_name){
        $htmlFilter = Html::activeTextInput($searchModel, $field_name, ['class'=>'form-control']);
        
        if($value['ezf_field_type']!=0 && !in_array($value['ezf_field_type'], [55, 62, 66, 67, 68, 71, 81, 83, 84, 89, 912, 913, 90, 916])){
            if($value['ezf_field_type'] == 61){
                $data = SDUtility::string2Array($value['ezf_field_data']);
                if (isset($data['items']['data'])) {
                    $newData = \yii\helpers\ArrayHelper::merge([''=>'All'], $data['items']['data']);
                    $items = ['items'=>$newData];
                    $value['ezf_field_data'] = SDUtility::array2String($items);
                }
                $dataInput['input_function'] = 'dropDownList';
            }
            
            if(in_array($value['ezf_field_type'], [63,64])){
                $m = 'moment()';
                $htmlFilter = \kartik\daterange\DateRangePicker::widget([
                        'model'=>$searchModel,
                        'attribute'=>$field_name,
                        'convertFormat'=>true,
                        //'useWithAddon'=>true,
                        //'presetDropdown'=>TRUE,
                        
                        'options'=>['id'=>'dr_'.$field_name.'_'.$value['ezf_field_id'], 'class'=>'form-control'],
                        'pluginOptions'=>[
                            'locale'=>[
                                'format'=>'d-m-Y',
                                'separator'=>' to ',
                                //'language'=>'TH',
                            ],
                            'alwaysShowCalendars'=>true,
                            'autoUpdateInput'=>FALSE,
                            'ranges'=>[
                                Yii::t('kvdrp', 'Today') => ["{$m}.startOf('day')", $m],
                                Yii::t('kvdrp', 'Yesterday') => [
                                    "{$m}.startOf('day').subtract(1,'days')",
                                    "{$m}.endOf('day').subtract(1,'days')",
                                ],
                                Yii::t('kvdrp', 'Last {n} Days', ['n' => 7]) => ["{$m}.startOf('day').subtract(6, 'days')", $m],
                                Yii::t('kvdrp', 'Last {n} Days', ['n' => 30]) => ["{$m}.startOf('day').subtract(29, 'days')", $m],
                                Yii::t('kvdrp', 'This Month') => ["{$m}.startOf('month')", "{$m}.endOf('month')"],
                                Yii::t('kvdrp', 'Last Month') => [
                                    "{$m}.subtract(1, 'month').startOf('month')",
                                    "{$m}.subtract(1, 'month').endOf('month')",
                                ],
                            ],
                            'autoApply'=>true,                
                            'opens'=>'left',
                        ]
                    ]);
            } elseif(in_array($value['table_field_type'], ['none', 'field']) || in_array($value['ezf_field_type'], [87, 917])){
                $htmlFilter = '';
            } elseif ($value['ezf_field_type'] == 88) {
                $items = [0,1,2,3,4,5];
                $options = SDUtility::string2Array($value['ezf_field_options']);
                
                if(isset($options['options']['maxNummber'])){
                    $items = \appxq\sdii\utils\SDUtility::num2array($options['options']['maxNummber']);
                }
                
                $htmlFilter = Html::activeDropDownList($searchModel, $field_name, $items, ['prompt'=>'All', 'class'=>'form-control']);
            } else {
                $htmlFilter = \backend\modules\ezmodules\classes\ModuleFunc::generateInputOnly($searchModel, $value, $dataInput);
            }
            
        } 
        
        return $htmlFilter;
    }

    public static function getFormOption($options, $forms){
        if(isset($options['forms'])){
            foreach ($options['forms'] as $key => $value) {
                $forms = ArrayHelper::merge($forms, [$value['form']]);
                if(isset($value['forms'])){
                    $forms = self::getFormOption($value, $forms);
                }
            }
            
        }
        return $forms;
    }
    
    public static function getMenu($ezf_table, $parent = 0){
        $model_menu = new \backend\modules\ezforms2\models\TbdataAll();
        $model_menu->setTableName($ezf_table);
        
        $whereStr = 'AND (menu_parent is null OR menu_parent = 0)';
        $params = [];
        if($parent>0){
            $whereStr = 'AND menu_parent = :parent';
            $params = [':parent'=>$parent];
        }
        
        $model_menu = $model_menu->find()->where("rstat not in(0,3) $whereStr", $params)->orderBy('menu_order')->all();
        
        
//        if(Yii::$app->user->isGuest){
//            $model_menu->andWhere('xsourcex = :site', [':site' => Yii::$app->user->identity->profile->sitecode]);
//        }
             
        return $model_menu;
    }
    
    public static function getMenuContent($ezf_table, $id){
        $model_menu = new \backend\modules\ezforms2\models\TbdataAll();
        $model_menu->setTableName($ezf_table);
        
        $model_menu = $model_menu->find()->where("rstat not in(0,3) AND id=:id", [':id'=>$id])->one();
        
        return $model_menu;
    }
    
    public static function renderSettingWorkList($value, $type){
        echo $value['tab_name'].' ';
        
        if(isset($value['setting_ezf_id']) && !empty($value['setting_ezf_id'])){
            if($value['ezf_action']==1){
                echo EzfHelper::btn($value['setting_ezf_id'])->options(['class'=>'btn btn-success btn-sm '])->buildBtnAdd();
            } else {
                echo EzfHelper::btn($value['setting_ezf_id'])->options(['class'=>'btn btn-info btn-sm '])->buildBtnEmr();
            }
        }

        if(isset($value['setting_ezm_id']) && !empty($value['setting_ezm_id'])){
            echo Html::a('Go to Module', Url::to(['/ezmodules/ezmodule/view', 'id'=>$value['setting_ezm_id']]), ['class'=>'btn btn-default btn-sm']);
        }
    }
    
    public static function getProcessLabel($value, $dataSetting){
        $modelEzf = EzfQuery::getEzformOne($value['ezf_id']);

        Yii::$app->session['show_varname'] = 0;
        Yii::$app->session['ezf_input'] = EzfQuery::getInputv2All();
        //Yii::$app->session['ezform'] = $modelEzf->attributes;

        $userProfile = Yii::$app->user->identity->profile;

        $modelFields = \backend\modules\ezforms2\models\EzformFields::find()
                ->where('ezf_id = :ezf_id', [':ezf_id' => $modelEzf->ezf_id])
                ->orderBy(['ezf_field_order' => SORT_ASC])
                ->all();
        
        $model = \backend\modules\ezforms2\classes\EzfFunc::setDynamicModel($modelFields, $modelEzf->ezf_table, Yii::$app->session['ezf_input'], Yii::$app->session['show_varname']);
            
        $model = EzfUiFunc::loadData($model, $modelEzf->ezf_table, $value['dataid']);
        
        $fields = SDUtility::string2Array($dataSetting['display_request']);
        
        $path_items = [];
        
        $template_item = '<span class="{id}"><strong>{label}:</strong> {value}</span> ';
        if(isset($dataSetting['display_template']) && !empty($dataSetting['display_template'])){
            $template_item = '<span class="{id}">{value}</span> ';
        }
        
        $template_content = '
                    <div class="item-label" >';
        foreach ($fields as $field) {
            $fieldName = $field;
            
            $template_content .= "{{$fieldName}}";

            foreach ($modelFields as $key => $fvalue) {
                $var = $fvalue['ezf_field_name'];
                $label = $fvalue['ezf_field_label'];

                if ($fieldName == $var) {
                    $dataInput;
                    if (isset(Yii::$app->session['ezf_input'])) {
                        $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($fvalue['ezf_field_type'], Yii::$app->session['ezf_input']);
                    }

                    $path_data = [
                        '{id}' => Html::getInputId($model, $fieldName),
                        '{label}' => $label,
                        '{value}' => EzfUiFunc::getValueEzform($dataInput, $fvalue, $model),
                    ];

                    $path_items["{{$fieldName}}"] = strtr($template_item, $path_data);

                    break;
                }
            }
        }
        $template_content .= '</div>';
        
        $content = '';
        if(isset($dataSetting['display_template']) && !empty($dataSetting['display_template'])){
            $content = strtr($dataSetting['display_template'], $path_items);
        } else {
            $content = strtr($template_content, $path_items);
        }
        
        return $content;
    }
    
    public static function exportModule($ezm_id, $model){
        
        $fileName = 'backup_ezmodule_'. \yii\helpers\Inflector::slug($model->ezm_short_title).'_'.SDUtility::getMillisecTime().'.xlsx';
        
        $schemaEzmodule = \backend\modules\ezmodules\models\Ezmodule::getTableSchema();
        $schemaEzmoduleAddon = \backend\modules\ezmodules\models\EzmoduleAddon::getTableSchema();
        $schemaEzmoduleFields = \backend\modules\ezmodules\models\EzmoduleFields::getTableSchema();
        $schemaEzmoduleFilter = \backend\modules\ezmodules\models\EzmoduleFilter::getTableSchema();
        $schemaEzmoduleForms = \backend\modules\ezmodules\models\EzmoduleForms::getTableSchema();
        $schemaEzmoduleMenu = \backend\modules\ezmodules\models\EzmoduleMenu::getTableSchema();
        $schemaEzmoduleWidget = \backend\modules\ezmodules\models\EzmoduleWidget::getTableSchema();
        $schemaEzmoduleTab = \backend\modules\ezmodules\models\EzmoduleTab::getTableSchema();

	$export = \appxq\sdii\widgets\SDExcel::export([
            'fileName'=>$fileName,
            'savePath'=> Yii::getAlias('@backend/web/print'),
            'format'=>'Xlsx',
            'asAttachment'=>false,
            'isMultipleSheet' => true,
            'models' => [
                'Ezmodule' => \backend\modules\ezmodules\models\Ezmodule::find()->where('ezm_id=:ezm_id', [':ezm_id'=>$ezm_id])->all(), 
                'EzmoduleAddon' => \backend\modules\ezmodules\models\EzmoduleAddon::find()->where('ezm_id=:ezm_id', [':ezm_id'=>$ezm_id])->all(), 
                'EzmoduleFields' => \backend\modules\ezmodules\models\EzmoduleFields::find()->where('ezm_id=:ezm_id', [':ezm_id'=>$ezm_id])->all(), 
                'EzmoduleFilter' => \backend\modules\ezmodules\models\EzmoduleFilter::find()->where('ezm_id=:ezm_id', [':ezm_id'=>$ezm_id])->all(), 
                'EzmoduleForms' => \backend\modules\ezmodules\models\EzmoduleForms::find()->where('ezm_id=:ezm_id', [':ezm_id'=>$ezm_id])->all(), 
                'EzmoduleMenu' => \backend\modules\ezmodules\models\EzmoduleMenu::find()->where('ezm_id=:ezm_id', [':ezm_id'=>$ezm_id])->all(), 
                'EzmoduleWidget' => \backend\modules\ezmodules\models\EzmoduleWidget::find()->where('ezm_id=:ezm_id', [':ezm_id'=>$ezm_id])->all(),
                'EzmoduleTab' => \backend\modules\ezmodules\models\EzmoduleTab::find()->where('ezm_id=:ezm_id', [':ezm_id'=>$ezm_id])->all(), 
            ], 
            'columns' => [
                    'Ezmodule' => $schemaEzmodule->columnNames, 
                    'EzmoduleAddon' => $schemaEzmoduleAddon->columnNames, 
                    'EzmoduleFields' => $schemaEzmoduleFields->columnNames,
                    'EzmoduleFilter' => $schemaEzmoduleFilter->columnNames,
                    'EzmoduleForms' => $schemaEzmoduleForms->columnNames,
                    'EzmoduleMenu' => $schemaEzmoduleMenu->columnNames,
                    'EzmoduleWidget' => $schemaEzmoduleWidget->columnNames,
                    'EzmoduleTab' => $schemaEzmoduleTab->columnNames,
            ], 
            'headers' => [
                    'Ezmodule' => ArrayHelper::map($schemaEzmodule->columns, 'name', 'name'), 
                    'EzmoduleAddon' => ArrayHelper::map($schemaEzmoduleAddon->columns, 'name', 'name'), 
                    'EzmoduleFields' => ArrayHelper::map($schemaEzmoduleFields->columns, 'name', 'name'), 
                    'EzmoduleFilter' => ArrayHelper::map($schemaEzmoduleFilter->columns, 'name', 'name'), 
                    'EzmoduleForms' => ArrayHelper::map($schemaEzmoduleForms->columns, 'name', 'name'), 
                    'EzmoduleMenu' => ArrayHelper::map($schemaEzmoduleMenu->columns, 'name', 'name'), 
                    'EzmoduleWidget' => ArrayHelper::map($schemaEzmoduleWidget->columns, 'name', 'name'), 
                    'EzmoduleTab' => ArrayHelper::map($schemaEzmoduleTab->columns, 'name', 'name'), 
            ], 
        ]);
        
        return $fileName;
    }
    
    public static function importModule($fileName, $clone=0){
        $sum = [];
        $data = \moonland\phpexcel\Excel::import($fileName, [
                    'setFirstRecordAsKeys' => true,
                    'setIndexSheetByName' => true,
                        //'getOnlySheet' => 'sheet1',
        ]);
        
        $userProfile = Yii::$app->user->identity->profile;
        $ezm_error=1;
        $ezm_id_new = SDUtility::getMillisecTime();
        $ezm_have = 0;
        
        if (isset($data['Ezmodule']) && !empty($data['Ezmodule'])) {

            $sum['Ezmodule']['all'] = 0;
            $sum['Ezmodule']['tsum'] = 0;
            $sum['Ezmodule']['fsum'] = 0;
            $sum['Ezmodule']['esum'] = 0;
            foreach ($data['Ezmodule'] as $value) {
                try {
                    if($clone){
                        $value['ezm_id'] = $ezm_id_new;
                        $value['ezm_name'] = $value['ezm_name'].'-clone';
                        $value['ezm_short_title'] = $value['ezm_short_title'].'-clone';
                        $value['share'] = '';
                        $value['ezm_builder'] = '';
                        $value['ezm_visible'] = 0;
                        $value['ezm_system'] = 0;
                        $value['ezm_template'] = 0;
                        $value['approved'] = 0;
                    }

                    
                    
                    $sum['Ezmodule']['all']++;
                    $modelEzmodule = \backend\modules\ezmodules\models\Ezmodule::findOne($value['ezm_id']);
                    if($modelEzmodule){
                       $value['updated_by'] = '';
                       $value['updated_at'] = '';
                    } else {
                        $modelEzmodule = new \backend\modules\ezmodules\models\Ezmodule();
                        $value['created_by'] = '';
                        $value['created_at'] = '';
                        $value['updated_by'] = '';
                        $value['updated_at'] = '';
                    }
                    $modelEzmodule->attributes = $value;
                    if ($modelEzmodule->save()) {
                        $sum['Ezmodule']['tsum'] ++;
                        Yii::$app->db->createCommand()->update('ezmodule', [
                            'ezm_icon'=>$value['ezm_icon'],
                            'icon_base_url'=>$value['icon_base_url'],
                        ], 'ezm_id=:ezm_id', [':ezm_id'=>$value['ezm_id']])->execute();
                    } else {
                        $sum['Ezmodule']['fsum'] ++;
                    }
                    $sum['Ezmodule']['ezm_id'] = $value['ezm_id'];
                } catch (\yii\db\Exception $e) {
                    $sum['Ezmodule']['esum'] ++;
                    EzfFunc::addErrorLog($e);
                }
            }
            $ezm_error = $sum['Ezmodule']['esum']+$sum['Ezmodule']['fsum'];
        }
        
        
        
        if (isset($data['EzmoduleAddon']) && !empty($data['EzmoduleAddon']) && $ezm_error==0) {

            $sum['EzmoduleAddon']['all'] = 0;
            $sum['EzmoduleAddon']['tsum'] = 0;
            $sum['EzmoduleAddon']['fsum'] = 0;
            $sum['EzmoduleAddon']['esum'] = 0;
            foreach ($data['EzmoduleAddon'] as $value) {
                try {
                    if($clone){
                        $value['addon_id'] = SDUtility::getMillisecTime();
                        $value['ezm_id'] = $ezm_id_new;
                        $value['user_id'] = Yii::$app->user->id;
                    }
                    
                    $sum['EzmoduleAddon']['all']++;
                    $modelEzmoduleAddon = \backend\modules\ezmodules\models\EzmoduleAddon::findOne($value['addon_id']);
                    if($modelEzmoduleAddon){
//                        $value['updated_by'] = '';
//                        $value['updated_at'] = '';
                    } else {
                        $modelEzmoduleAddon = new \backend\modules\ezmodules\models\EzmoduleAddon();
//                        $value['created_by'] = '';
//                        $value['created_at'] = '';
//                        $value['updated_by'] = '';
//                        $value['updated_at'] = '';
                    }
                    $modelEzmoduleAddon->attributes = $value;
                    
                    if ($modelEzmoduleAddon->save()) {
                        $sum['EzmoduleAddon']['tsum'] ++;
                    } else {
                        $sum['EzmoduleAddon']['fsum'] ++;
                    }
                } catch (\yii\db\Exception $e) {
                    $sum['EzmoduleAddon']['esum'] ++;
                    EzfFunc::addErrorLog($e);
                }
            }
        }
        
        
        if (isset($data['EzmoduleFields']) && !empty($data['EzmoduleFields']) && $ezm_error==0) {

            $sum['EzmoduleFields']['all'] = 0;
            $sum['EzmoduleFields']['tsum'] = 0;
            $sum['EzmoduleFields']['fsum'] = 0;
            $sum['EzmoduleFields']['esum'] = 0;
            foreach ($data['EzmoduleFields'] as $value) {
                try {
                    if($clone){
                        $value['field_id'] = SDUtility::getMillisecTime();
                        $value['ezm_id'] = $ezm_id_new;
                    }
                    
                    $sum['EzmoduleFields']['all']++;
                    $modelEzmoduleFields = \backend\modules\ezmodules\models\EzmoduleFields::findOne($value['field_id']);
                    if($modelEzmoduleFields){
                        $value['updated_by'] = '';
                        $value['updated_at'] = '';
                    } else {
                        $modelEzmoduleFields = new \backend\modules\ezmodules\models\EzmoduleFields();
                        $value['created_by'] = '';
                        $value['created_at'] = '';
                        $value['updated_by'] = '';
                        $value['updated_at'] = '';
                    }
                    $modelEzmoduleFields->attributes = $value;
                    
                    if ($modelEzmoduleFields->save()) {
                        $sum['EzmoduleFields']['tsum'] ++;
                    } else {
                        $sum['EzmoduleFields']['fsum'] ++;
                    }
                } catch (\yii\db\Exception $e) {
                    $sum['EzmoduleFields']['esum'] ++;
                    EzfFunc::addErrorLog($e);
                }
            }
        }
        
        if (isset($data['EzmoduleFilter']) && !empty($data['EzmoduleFilter']) && $ezm_error==0) {

            $sum['EzmoduleFilter']['all'] = 0;
            $sum['EzmoduleFilter']['tsum'] = 0;
            $sum['EzmoduleFilter']['fsum'] = 0;
            $sum['EzmoduleFilter']['esum'] = 0;
            foreach ($data['EzmoduleFilter'] as $value) {
                try {
                    if($clone){
                        $value['filter_id'] = SDUtility::getMillisecTime();
                        $value['ezm_id'] = $ezm_id_new;
                        $value['share'] = '';
                        $value['sitecode'] = $userProfile->sitecode;
                    }
                    
                    $sum['EzmoduleFilter']['all']++;
                    $modelEzmoduleFilter = \backend\modules\ezmodules\models\EzmoduleFilter::findOne($value['filter_id']);
                    if($modelEzmoduleFilter){
                        $value['updated_by'] = '';
                        $value['updated_at'] = '';
                    } else {
                        $modelEzmoduleFilter = new \backend\modules\ezmodules\models\EzmoduleFilter();
                        $value['created_by'] = '';
                        $value['created_at'] = '';
                        $value['updated_by'] = '';
                        $value['updated_at'] = '';
                    }
                    $modelEzmoduleFilter->attributes = $value;
                    
                    if ($modelEzmoduleFilter->save()) {
                        $sum['EzmoduleFilter']['tsum'] ++;
                    } else {
                        $sum['EzmoduleFilter']['fsum'] ++;
                    }
                } catch (\yii\db\Exception $e) {
                    $sum['EzmoduleFilter']['esum'] ++;
                    EzfFunc::addErrorLog($e);
                }
            }
        }
        
        if (isset($data['EzmoduleForms']) && !empty($data['EzmoduleForms']) && $ezm_error==0) {

            $sum['EzmoduleForms']['all'] = 0;
            $sum['EzmoduleForms']['tsum'] = 0;
            $sum['EzmoduleForms']['fsum'] = 0;
            $sum['EzmoduleForms']['esum'] = 0;
            foreach ($data['EzmoduleForms'] as $value) {
                try {
                    if($clone){
                        $value['form_id'] = SDUtility::getMillisecTime();
                        $value['ezm_id'] = $ezm_id_new;
                    }
                    
                    $sum['EzmoduleForms']['all']++;
                    $modelEzmoduleForms = \backend\modules\ezmodules\models\EzmoduleForms::findOne($value['form_id']);
                    if($modelEzmoduleForms){
                        $value['updated_by'] = '';
                        $value['updated_at'] = '';
                    } else {
                        $modelEzmoduleForms = new \backend\modules\ezmodules\models\EzmoduleForms();
                        $value['created_by'] = '';
                        $value['created_at'] = '';
                        $value['updated_by'] = '';
                        $value['updated_at'] = '';
                    }
                    $modelEzmoduleForms->attributes = $value;
                    
                    if ($modelEzmoduleForms->save()) {
                        $sum['EzmoduleForms']['tsum'] ++;
                    } else {
                        $sum['EzmoduleForms']['fsum'] ++;
                    }
                } catch (\yii\db\Exception $e) {
                    $sum['EzmoduleForms']['esum'] ++;
                    EzfFunc::addErrorLog($e);
                }
            }
        }
        
        
        if (isset($data['EzmoduleMenu']) && !empty($data['EzmoduleMenu']) && $ezm_error==0) {

            $sum['EzmoduleMenu']['all'] = 0;
            $sum['EzmoduleMenu']['tsum'] = 0;
            $sum['EzmoduleMenu']['fsum'] = 0;
            $sum['EzmoduleMenu']['esum'] = 0;
            foreach ($data['EzmoduleMenu'] as $value) {
                try {
                    if($clone){
                        $value['menu_id'] = SDUtility::getMillisecTime();
                        $value['ezm_id'] = $ezm_id_new;
                    }
                    
                    $sum['EzmoduleMenu']['all']++;
                    $modelEzmoduleMenu = \backend\modules\ezmodules\models\EzmoduleMenu::findOne($value['menu_id']);
                    if($modelEzmoduleMenu){
                        $value['updated_by'] = '';
                        $value['updated_at'] = '';
                    } else {
                        $modelEzmoduleMenu = new \backend\modules\ezmodules\models\EzmoduleMenu();
                        $value['created_by'] = '';
                        $value['created_at'] = '';
                        $value['updated_by'] = '';
                        $value['updated_at'] = '';
                    }
                    $modelEzmoduleMenu->attributes = $value;
                    
                    if ($modelEzmoduleMenu->save()) {
                        $sum['EzmoduleMenu']['tsum'] ++;
                    } else {
                        $sum['EzmoduleMenu']['fsum'] ++;
                    }
                } catch (\yii\db\Exception $e) {
                    $sum['EzmoduleMenu']['esum'] ++;
                    EzfFunc::addErrorLog($e);
                }
            }
        }
        
        if (isset($data['EzmoduleWidget']) && !empty($data['EzmoduleWidget']) && $ezm_error==0) {

            $sum['EzmoduleWidget']['all'] = 0;
            $sum['EzmoduleWidget']['tsum'] = 0;
            $sum['EzmoduleWidget']['fsum'] = 0;
            $sum['EzmoduleWidget']['esum'] = 0;
            foreach ($data['EzmoduleWidget'] as $value) {
                try {
                    if($clone){
                        $value['widget_id'] = SDUtility::getMillisecTime();
                        $value['ezm_id'] = $ezm_id_new;
                    }
                    
                    $sum['EzmoduleWidget']['all']++;
                    $modelEzmoduleWidget = \backend\modules\ezmodules\models\EzmoduleWidget::findOne($value['widget_id']);
                    if($modelEzmoduleWidget){
                        $value['updated_by'] = '';
                        $value['updated_at'] = '';
                    } else {
                        $modelEzmoduleWidget = new \backend\modules\ezmodules\models\EzmoduleWidget();
                        $value['created_by'] = '';
                        $value['created_at'] = '';
                        $value['updated_by'] = '';
                        $value['updated_at'] = '';
                    }
                    $modelEzmoduleWidget->attributes = $value;
                    
                    if ($modelEzmoduleWidget->save()) {
                        $sum['EzmoduleWidget']['tsum'] ++;
                    } else {
                        $sum['EzmoduleWidget']['fsum'] ++;
                    }
                } catch (\yii\db\Exception $e) {
                    $sum['EzmoduleWidget']['esum'] ++;
                    EzfFunc::addErrorLog($e);
                }
            }
        }
        
        
        if (isset($data['EzmoduleTab']) && !empty($data['EzmoduleTab']) && $ezm_error==0) {

            $sum['EzmoduleTab']['all'] = 0;
            $sum['EzmoduleTab']['tsum'] = 0;
            $sum['EzmoduleTab']['fsum'] = 0;
            $sum['EzmoduleTab']['esum'] = 0;
            foreach ($data['EzmoduleTab'] as $value) {
                try {
                    if($clone){
                        $value['tab_id'] = SDUtility::getMillisecTime();
                        $value['user_id'] = Yii::$app->user->id;
                    }
                    
                    $sum['EzmoduleTab']['all']++;
                    $modelEzmoduleTab = \backend\modules\ezmodules\models\EzmoduleTab::findOne($value['tab_id']);
                    if($modelEzmoduleTab){
//                        $value['updated_by'] = '';
//                        $value['updated_at'] = '';
                    } else {
                        $modelEzmoduleTab = new \backend\modules\ezmodules\models\EzmoduleTab();
//                        $value['created_by'] = '';
//                        $value['created_at'] = '';
//                        $value['updated_by'] = '';
//                        $value['updated_at'] = '';
                    }
                    $modelEzmoduleTab->attributes = $value;
                    
                    if ($modelEzmoduleTab->save()) {
                        $sum['EzmoduleTab']['tsum'] ++;
                    } else {
                        $sum['EzmoduleTab']['fsum'] ++;
                    }
                } catch (\yii\db\Exception $e) {
                    $sum['EzmoduleTab']['esum'] ++;
                    EzfFunc::addErrorLog($e);
                }
            }
        }
        
        return $sum;
    }
    
    public static function saveEzmRole($arrRole, $ezm_id) {
        try {
            $roleIn = "role NOT IN('" . implode("','", $arrRole) . "')";
            \backend\modules\ezmodules\models\EzmoduleRole::deleteAll("ezm_id = '$ezm_id' AND $roleIn");
            foreach ($arrRole as $role_name) {
                ModuleQuery::insertEzmRole($ezm_id, $role_name);
            }
        } catch (\yii\db\Exception $e) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
            \backend\modules\ezmodules\models\EzmoduleRole::deleteAll(['ezm_id' => $ezm_id]);
            return FALSE;
        }
    }
    
}
