<?php

namespace backend\modules\thaihis\classes;

use Yii;
use yii\helpers\ArrayHelper;
use appxq\sdii\utils\SDUtility;
use backend\modules\ezforms2\classes\EzfFunc;
use yii\helpers\Html;
use backend\modules\core\classes\CoreFunc;
use appxq\sdii\models\SDDynamicModel;
use DateTime;

/**
 * OvccaFunc class file UTF-8
 * @author SDII <iencoded@gmail.com>
 * @copyright Copyright &copy; 2015 AppXQ
 * @license http://www.appxq.com/license/
 * @version 1.0.0 Date: 9 ก.พ. 2559 12:38:14
 * @link http://www.appxq.com/
 * @example 
 */
class ThaiHisFunc {

    public static function modelSearch($model, $ezform, $targetField, $ezformParent, $modelFields, $modelFilter, $filter, $params) {
        //$model = new TbdataAll();
        $addonFields = [];
        $userProfile = Yii::$app->user->identity->profile;

        $selectCol = [];

        $query = $model->find()->where("{$ezform['ezf_table']}.rstat not in(0,3)"); //->where('rstat not in(0, 3)');

        if (isset($targetField)) {// join 
            $pk = $targetField->ezf_field_name;
            $pkJoin = $targetField->ref_field_id;

            $query->innerJoin($ezformParent['ezf_table'], "`{$ezformParent['ezf_table']}`.`$pkJoin` = `{$ezform['ezf_table']}`.`$pk`");
            $query->andWhere("`{$ezformParent['ezf_table']}`.rstat NOT IN(0,3)");
            $query->groupBy("{$ezformParent['ezf_table']}.id");
        }

        if (isset($modelFields) && !empty($modelFields)) { //fields
            $tmpCol = [];
            $chkRefField = '';
            foreach ($modelFields as $field) {

                if (!empty($field['ref_field']) && $field['ref_field'] !== $chkRefField) {
                    $query->innerJoin($field['ezf_table'], "`{$field['ezf_table']}`.`{$field['ref_field']}` = `{$ezform['ezf_table']}`.`{$field['join_field_id']}` AND `{$field['ezf_table']}`.rstat NOT IN(0,3)");
                    $chkRefField = $field['ref_field'];
                }

                if (!in_array($field['ezf_id'] . $field['ezf_field_id'], $tmpCol)) {
                    $tmpCol[] = $field['ezf_id'] . $field['ezf_field_id'];
                    if (isset($ezformParent['ezf_id']) && $ezformParent['ezf_id'] == $field['ezf_id']) {
//                        $addonFields[] = 'fparent_' . $field['ezf_field_name'];
                        $addonFields[] = $field['ezf_field_name'];
                        if ($field['table_field_type'] == 'field') {
                            $fieldsChildren = \backend\modules\ezmodules\classes\ModuleFunc::getFieldsData($field['ezf_field_data']);
                            foreach ($fieldsChildren as $field_child) {
                                if (!in_array($ezformParent['ezf_table'] . '.' . $field_child, $selectCol)) {
                                    $selectCol[] = $ezformParent['ezf_table'] . '.' . $field_child;
                                    $addonFields[] = $field_child;
                                }
                            }
                        } else {
//                            $selectCol[] = $ezformParent['ezf_table'] . '.' . $field['ezf_field_name'] . ' AS fparent_' . $field['ezf_field_name'];
                            $selectCol[] = $ezformParent['ezf_table'] . '.' . $field['ezf_field_name'];
                        }
                    } else {
                        $selectCol[] = (isset($field['ezf_table']) ? $field['ezf_table'] : $ezform['ezf_table']) . '.' . $field['ezf_field_name'];
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
        } else
        if ($ezform['public_listview'] == 2) {
            $query->andWhere("{$ezform['ezf_table']}.xsourcex = :site", [':site' => $userProfile->sitecode]);
        }
        if ($modelFilter) {
            foreach ($modelFilter as $keyFilter => $valueFilter) {
                $query->andWhere("{$ezform['ezf_table']}.{$keyFilter} = :value", [':value' => $valueFilter]);
            }
        }
//        \appxq\sdii\utils\VarDumper::dump($query->createCommand()->rawSql);
        return $query->createCommand()->queryOne();
    }

    public static function modelSearchAll($model, $ezform, $targetField, $ezformParent, $modelFields, $where = null, $filter = null, $params = null) {
        //$model = new TbdataAll();
        $addonFields = [];
        $userProfile = Yii::$app->user->identity->profile;

        $selectCol = [];

        $query = $model->find()->where("{$ezform['ezf_table']}.rstat not in(0,3)"); //->where('rstat not in(0, 3)');
        $chkRefField = '';
        if (isset($targetField)) {// join 
            $pk = $targetField->ezf_field_name;
            $pkJoin = $targetField->ref_field_id;

            $query->innerJoin($ezformParent['ezf_table'], "`{$ezformParent['ezf_table']}`.`$pkJoin` = `{$ezform['ezf_table']}`.`$pk`");
            $query->andWhere("`{$ezformParent['ezf_table']}`.rstat NOT IN(0,3)");
            $query->groupBy("{$ezformParent['ezf_table']}.id");
            $chkRefField = $ezformParent['ezf_table'];
        }

        if (isset($modelFields) && !empty($modelFields)) { //fields
            $tmpCol = [];


            foreach ($modelFields as $field) {
                if (!empty($field['ref_field_id']) && $field['ezf_table'] != $ezform['ezf_table'] && $field['ezf_table'] !== $chkRefField) {
                    $query->innerJoin($field['ezf_table'], "`{$field['ezf_table']}`.`{$field['ref_field']}` = `{$ezform['ezf_table']}`.`{$field['ref_field_id']}` AND `{$field['ezf_table']}`.rstat NOT IN(0,3)");
                    $chkRefField = $field['ezf_table'];
                }


                if (!in_array($field['ezf_id'] . $field['ezf_field_id'], $tmpCol) && $field['field_to_join'] == 'no') {
                    $tmpCol[] = $field['ezf_id'] . $field['ezf_field_id'];
                    if (isset($ezformParent['ezf_id']) && $ezformParent['ezf_id'] == $field['ezf_id']) {
//                        $addonFields[] = 'fparent_' . $field['ezf_field_name'];
                        $addonFields[] = $field['ezf_field_name'];
                        if ($field['table_field_type'] == 'field') {
                            $fieldsChildren = \backend\modules\ezmodules\classes\ModuleFunc::getFieldsData($field['ezf_field_data']);
                            foreach ($fieldsChildren as $field_child) {
                                if (!in_array($ezformParent['ezf_table'] . '.' . $field_child, $selectCol)) {
                                    $selectCol[] = $ezformParent['ezf_table'] . '.' . $field_child;
                                    $addonFields[] = $field_child;
                                }
                            }
                        } else {
//                            $selectCol[] = $ezformParent['ezf_table'] . '.' . $field['ezf_field_name'] . ' AS fparent_' . $field['ezf_field_name'];
                            $selectCol[] = $ezformParent['ezf_table'] . '.' . $field['ezf_field_name'];
                        }
                    } else {
                        $selectCol[] = (isset($field['ezf_table']) ? $field['ezf_table'] : $ezform['ezf_table']) . '.' . $field['ezf_field_name'];
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

        if ($where != null) {
            $query->andWhere($where);
        }

        if ($ezform['public_listview'] == 0) {
            $query->andWhere("{$ezform['ezf_table']}.user_create=:created_by", [':created_by' => Yii::$app->user->id]);
        }

        if ($ezform['public_listview'] == 3) {
            $query->andWhere("{$ezform['ezf_table']}.xdepartmentx = :unit", [':unit' => $userProfile->department]);
        }

        if (isset($specialField)) {
            $query->andWhere("{$ezform['ezf_table']}.hsitecode = :site", [':site' => $userProfile->sitecode]);
        } else
        if ($ezform['public_listview'] == 2) {
            $query->andWhere("{$ezform['ezf_table']}.xsourcex = :site", [':site' => $userProfile->sitecode]);
        }
        //\appxq\sdii\utils\VarDumper::dump($query->createCommand()->rawSql);
        return $query->createCommand()->queryAll();
    }

    /**
     * 
     * @param type $model
     * @param type $ezform
     * @param type $targetField
     * @param type $ezformParent
     * @param type $modelFields
     * @param type $where
     * @param type $target
     * @param type $customSelect
     * @param type $groupField
     * @param type $responseQuery
     * @return type data array
     */
    public static function modelSearchAll2($model, $ezform, $targetField, $ezformParent, $modelFields, $where = null, $target = null, $customSelect = null, $groupField = null, $responseQuery = false, $sort_order = null, $limit = null, $subquery = null) {
        //$model = new TbdataAll();

        $addonFields = [];
        $userProfile = Yii::$app->user->identity->profile;

        $selectCol = [];
        $tablejoiner = [];
        $query = $model->find()->from($ezform['ezf_table'])->where("{$ezform['ezf_table']}.rstat not in(0,3)"); //->where('rstat not in(0, 3)');
        //$query = new \yii\db\Query();
        //$query->from($ezform['ezf_table'])->where("{$ezform['ezf_table']}.rstat not in(0,3)");
        $tablejoiner[$ezform['ezf_table']] = $ezform['ezf_table'];

        if (isset($targetField)) {// join 
            $pk = $targetField->ezf_field_name;
            $pkJoin = $targetField->ref_field_id;

            $query->innerJoin($ezformParent['ezf_table'], "`{$ezformParent['ezf_table']}`.`$pkJoin` = `{$ezform['ezf_table']}`.`$pk`");
            $query->andWhere("`{$ezformParent['ezf_table']}`.rstat NOT IN(0,3)");
            $tablejoiner[$ezformParent['ezf_table']] = $ezformParent['ezf_table'];
        }

        if (isset($modelFields) && !empty($modelFields)) { //fields
            $tmpCol = [];

            // Loop for join table 
            foreach ($modelFields as $f) {
                foreach ($modelFields as $field) {
                    if ($field['ezf_table'] == 'zdata_patientright' && !in_array($field['ezf_table'], $tablejoiner)) {
                        $query->innerJoin([$field['ezf_table']=>$subquery], "`zdata_patientprofile`.`id` = `{$field['ezf_table']}`.`ptid`");
                        $tablejoiner[$field['ezf_table']] = $field['ezf_table'];
                    }
                    
                    if ($field['field_to_join'] == 'yes' && $field['type_to_join'] == 'dynamic') {
                        if (!empty($field['ref_field_id'])) {
                            if (in_array($field['ezf_table'], $tablejoiner) && !in_array($field['ref_ezf_table'], $tablejoiner)) {
                                if ($field['ezf_field_type'] == '79') {
                                    $query->innerJoin($field['ref_ezf_table'], "`{$field['ezf_table']}`.`{$field['ref_field_id']}` = `{$field['ref_ezf_table']}`.`{$field['ref_field']}` AND `{$field['ref_ezf_table']}`.rstat NOT IN(0,3)");
                                } else {
                                    $query->leftJoin($field['ref_ezf_table'], "`{$field['ezf_table']}`.`{$field['ref_field_id']}` = `{$field['ref_ezf_table']}`.`{$field['ref_field']}` AND `{$field['ref_ezf_table']}`.rstat NOT IN(0,3)");
                                }

                                $tablejoiner[$field['ref_ezf_table']] = $field['ref_ezf_table'];
                                $tablejoiner[$field['ezf_table']] = $field['ezf_table'];
                                $chkRefField[] = $field['ref_ezf_table'];
                            } else if (in_array($field['ref_ezf_table'], $tablejoiner) && !in_array($field['ezf_table'], $tablejoiner)) {

                                if ($field['ezf_field_type'] == '79') {
                                    $query->innerJoin($field['ezf_table'], "`{$field['ezf_table']}`.`{$field['ref_field_id']}` = `{$field['ref_ezf_table']}`.`{$field['ref_field']}` AND `{$field['ezf_table']}`.rstat NOT IN(0,3)");
                                } else {
                                    $query->leftJoin($field['ezf_table'], "`{$field['ezf_table']}`.`{$field['ref_field_id']}` = `{$field['ref_ezf_table']}`.`{$field['ref_field']}` AND `{$field['ezf_table']}`.rstat NOT IN(0,3)");
                                }

                                $tablejoiner[$field['ref_ezf_table']] = $field['ref_ezf_table'];
                                $tablejoiner[$field['ezf_table']] = $field['ezf_table'];
                                $chkRefField[] = $field['ezf_table'];
                            }
                        }
                    } else if ($field['field_to_join'] == 'yes' && $field['type_to_join'] == 'left_join') {
                        if (!empty($field['ref_field_id'])) {
                            if (in_array($field['ezf_table'], $tablejoiner) && !in_array($field['ref_ezf_table'], $tablejoiner)) {
                                $query->leftJoin($field['ref_ezf_table'], "`{$field['ezf_table']}`.`{$field['ref_field_id']}` = `{$field['ref_ezf_table']}`.`{$field['ref_field']}` AND `{$field['ref_ezf_table']}`.rstat NOT IN(0,3)");
                                $tablejoiner[$field['ref_ezf_table']] = $field['ref_ezf_table'];
                                $tablejoiner[$field['ezf_table']] = $field['ezf_table'];
                                $chkRefField[] = $field['ref_ezf_table'];
                            } else if (in_array($field['ref_ezf_table'], $tablejoiner) && !in_array($field['ezf_table'], $tablejoiner)) {
                                $query->leftJoin($field['ezf_table'], "`{$field['ezf_table']}`.`{$field['ref_field_id']}` = `{$field['ref_ezf_table']}`.`{$field['ref_field']}` AND `{$field['ezf_table']}`.rstat NOT IN(0,3)");

                                $tablejoiner[$field['ref_ezf_table']] = $field['ref_ezf_table'];
                                $tablejoiner[$field['ezf_table']] = $field['ezf_table'];
                                $chkRefField[] = $field['ezf_table'];
                            }
                        }
                    }
                }
            }

            foreach ($modelFields as $field) {
                if (!in_array($field['ezf_id'] . $field['ezf_field_id'], $tmpCol) && $field['field_to_join'] == 'no' && $field['ezf_field_type'] <> 76) {
                    $tmpCol[] = $field['ezf_id'] . $field['ezf_field_id'];
                    if (isset($ezformParent['ezf_id']) && $ezformParent['ezf_id'] == $field['ezf_id']) {
//                        $addonFields[] = 'fparent_' . $field['ezf_field_name'];
                        $addonFields[] = $field['ezf_field_name'];
                        if ($field['table_field_type'] == 'field') {
                            $fieldsChildren = \backend\modules\ezmodules\classes\ModuleFunc::getFieldsData($field['ezf_field_data']);
                            foreach ($fieldsChildren as $field_child) {
                                if (!in_array($ezformParent['ezf_table'] . '.' . $field_child, $selectCol)) {
                                    $selectCol[] = $ezformParent['ezf_table'] . '.' . $field_child;
                                    $addonFields[] = $field_child;
                                }
                            }
                        } else {
//                            $selectCol[] = $ezformParent['ezf_table'] . '.' . $field['ezf_field_name'] . ' AS fparent_' . $field['ezf_field_name'];
                            $selectCol[] = $ezformParent['ezf_table'] . '.' . $field['ezf_field_name'];
                        }
                    } else {
                        $selectCol[] = (isset($field['ezf_table']) ? $field['ezf_table'] : $ezform['ezf_table']) . '.' . $field['ezf_field_name'];
                    }
                }
            }

            if (isset($ezformParent['ezf_table'])) {
                $addonFields = ArrayHelper::merge($addonFields, ['fparent_id', 'fparent_target', 'fparent_ptid', 'fparent_rstat']);
                $selectCol = \yii\helpers\ArrayHelper::merge($selectCol, ["{$ezformParent['ezf_table']}.id AS fparent_id", "{$ezformParent['ezf_table']}.target AS fparent_target", "{$ezformParent['ezf_table']}.ptid AS fparent_ptid", "{$ezformParent['ezf_table']}.rstat AS fparent_rstat"]);
            }

            $selectCol = \yii\helpers\ArrayHelper::merge($selectCol, ["{$ezform['ezf_table']}.id", "{$ezform['ezf_table']}.target", "{$ezform['ezf_table']}.ptid", "{$ezform['ezf_table']}.xsourcex", "{$ezform['ezf_table']}.user_update", "{$ezform['ezf_table']}.update_date", "{$ezform['ezf_table']}.user_create", "{$ezform['ezf_table']}.create_date", "{$ezform['ezf_table']}.rstat", "{$ezform['ezf_table']}.ezf_version"]);
        } else {
            $selectCol[] = "{$ezform['ezf_table']}.*";
        }

        $model->setColFieldsAddon($addonFields);

        if ($customSelect != null) {
            foreach ($customSelect as $val) {
                $selectCol[] = $val;
            }
        }

        $query->select($selectCol)->distinct();

        if ($where != null) {
            foreach ($where as $val) {
                $query->andWhere($val);
            }
        }

//        if ($ezform['public_listview'] == 0) {
//            $query->andWhere("{$ezform['ezf_table']}.user_create=:created_by", [':created_by' => Yii::$app->user->id]);
//        }
//
//        if ($ezform['public_listview'] == 3) {
//            $query->andWhere("{$ezform['ezf_table']}.xdepartmentx = :unit", [':unit' => $userProfile->department]);
//        }

        if (isset($specialField)) {
            $query->andWhere("{$ezform['ezf_table']}.hsitecode = :site", [':site' => $userProfile->sitecode]);
        } else
        if ($ezform['public_listview'] == 2) {
            $query->andWhere("{$ezform['ezf_table']}.xsourcex = :site", [':site' => $userProfile->sitecode]);
        }

        $group = null;
        if ($groupField != null) {
            foreach ($groupField as $val) {
                $group[] = $val;
            }
        }

        $query->groupBy($group);
        if (isset($sort_order['column']))
            $query->orderBy($sort_order['column'] . ' ' . $sort_order['order']);
        if ($limit != null)
            $query->limit($limit);
//        \appxq\sdii\utils\VarDumper::dump($query->createCommand()->rawSql);
        $response = null;
        if ($responseQuery == true) {
            $response = $query;
        } else {
            $response = $query->createCommand()->queryAll();
        }

        return $response;
    }

    public static function setDynamicModel($fields, $table, $ezf_input, $annotated = 0) {
        $attributes = ['dataid', 'id', 'ptid', 'xsourcex', 'xdepartmentx', 'rstat', 'sitecode', 'ptcode', 'ptcodefull', 'hptcode', 'hsitecode', 'user_create', 'create_date', 'user_update', 'update_date', 'target', 'error', 'sys_lat', 'sys_lng', 'ezf_version'];
        $labels = [];
        $required = [];
        $rules = [];
        //$rulesFields = [];
        $rulesFields['safe'] = $attributes;
        $condFields = [];
        $behavior = [];
        $fields_type = [];
        $ezf_id = '';

        if (!empty($fields)) {
            foreach ($fields as $value) {
                $ezf_id = $value['ezf_id'];
                $fields_type[$value['ezf_field_name']] = $value['ezf_field_type'];
                //Attributes array
                $attributes[$value['ezf_field_name']] = $value['ezf_field_default'];

                //Labels array
                $labels[$value['ezf_field_name']] = isset($value['ezf_field_label']) ? Html::encode($value['ezf_field_label']) : '';
                if ($annotated == 1 && $value['table_field_type'] != 'none' && $value['table_field_type'] != 'field') {
                    $labels[$value['ezf_field_name']] .= " <code>{$value['ezf_field_name']}</code>";
                }

                //Rule array required
                if ($value['ezf_field_required'] == 1) {
                    $required[] = $value['ezf_field_name'];
                }

                //Rule array validate
                $validateArray = SDUtility::string2Array($value['ezf_field_validate']);
                if (is_array($validateArray)) {
                    $addRule = false;
                    foreach ($validateArray as $keyRule => $valueRule) {
                        if (is_array($valueRule)) {
                            $name = self::getRuleName($valueRule);
                            $rulesFields[$name][] = $value['ezf_field_name'];
                            $rules[$name] = $valueRule;
                        } else {
                            $addRule = true;
                            break;
                        }
                    }

                    if ($addRule) {
                        $name = self::getRuleName($validateArray);
                        $rulesFields[$name][] = $value['ezf_field_name'];
                        $rules[$name] = $validateArray;
                    }
                }

                $rulesFields['safe'][] = $value['ezf_field_name'];
                $rules['safe'] = ['safe'];

                if ($value['ezf_condition'] == 1) {
                    $condFields[] = EzfFunc::getCondition($value['ezf_id'], $value['ezf_field_name']);
                }

                $dataInput;
                if ($ezf_input) {
                    $dataInput = EzfFunc::getInputByArray($value['ezf_field_type'], $ezf_input);
                }
                if ($dataInput) {
                    $behavior = ArrayHelper::merge($behavior, EzfFunc::setBehavior(isset($value['ezf_table']) ? $value['ezf_table'] : $table, $value, $value['ezf_field_type'], $value['ezf_field_name'], $dataInput));
                }
            }
        }

        $model = new SDDynamicModel($attributes);
        $model->formName = "EZ$ezf_id";

        foreach ($rules as $key => $value) {
            $options = $value;
            unset($options[0]);
            $model->addRule($rulesFields[$key], $value[0], $options);
        }

        $js = '';
        foreach ($condFields as $key => $value) {
            if (!empty($value)) {
                foreach ($required as $i => $v) {
                    foreach ($value as $k => $data) {
                        $inputId = Html::getInputId($model, $data['ezf_field_name']);
                        $inputName = Html::getInputName($model, $data['ezf_field_name']);

                        $setSelector = "#$inputId}";
                        $jumpCheck = false;
                        if (in_array($fields_type[$data['ezf_field_name']], CoreFunc::itemAlias('ezf_check_conditon'))) {
                            $jumpCheck = true;
                            $setSelector = "#$inputId:checked";
                        } elseif (in_array($fields_type[$data['ezf_field_name']], CoreFunc::itemAlias('ezf_radio_conditon'))) {
                            $setSelector = "input[name=\"$inputName\"]:checked";
                        }
                        // required ก็ต่อมือ condition แสดง
                        if ((!empty($data['var_require']) && in_array($v, $data['var_require']))) {//|| (!empty($data['var_jump']) && in_array($v, $data['var_jump']))
                            $js .= "if(attribute.name == '$v') {
				    var r = $('$setSelector').val()=='{$data['ezf_field_value']}';
				    return r;	
			    }";
                        }
                        if ($jumpCheck) {
                            // required ก็ต่อมือ condition ซ่อน
                            if ((!empty($data['var_jump']) && in_array($v, $data['var_jump']))) {//|| (!empty($data['var_jump']) && in_array($v, $data['var_jump']))
                                $js .= "if(attribute.name == '$v') {
                                        var r = $('$setSelector').val()=='{$data['ezf_field_value']}';
                                        return !r;	
                                }";
                            }
                        }
                    }
                }
            }
        }

        $whenClient = $js != '' ? ['whenClient' => "function (attribute, value) { $js }"] : [];

        $model->addRule($required, 'required', $whenClient);

        $model->addLabel($labels);

        if (!empty($behavior)) {
            foreach ($behavior as $keyBehavior => $valueBehavior) {
                $model->attachBehavior($keyBehavior, $valueBehavior);
            }
        }

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
                    if (is_array($value)) {
                        $name .= $key . EzfFunc::arrayEncode2String($value);
                    } else {
                        $name .= $key . $value;
                    }
                }
            }
        }
        return $name;
    }

    public static function getOrderTran($visit_id) {
        $sql = "SELECT  zot.id,zot.order_tran_code,zot.order_tran_status,zol.order_name,zol.group_type,zol.unit_price,order_tran_doctor,
                zwu.unit_name,zot.create_date,zol.order_ezf_id,order_qty,zog.order_group_name,order_type_name,
                zot.order_tran_pay,zot.order_tran_notpay,order_tran_cashier_status,
                zot.ptid,zpp.pt_hn,external_flag,CONCAT(title,pf.firstname,' ',pf.lastname) AS doc_fullname
                FROM zdata_order_tran zot
                INNER JOIN zdata_order_lists zol ON(zol.order_code=zot.order_tran_code) 
                INNER JOIN zdata_patientprofile zpp ON (zpp.id=zot.ptid) 
		/*INNER JOIN zdata_patientright AS zpr ON(zpr.id=(SELECT MAX(id) FROM zdata_patientright WHERE rstat='1' AND right_pt_id=zot.ptid))*/
		/*INNER JOIN zdata_right zr ON(zr.right_code=zpr.right_code) */
                INNER JOIN zdata_working_unit zwu ON(zwu.id=zot.order_tran_dept) 
                INNER JOIN zdata_order_group zog ON(zog.id=zol.group_code)
                INNER JOIN zdata_order_type cot ON(cot.id=zol.group_type)
                LEFT JOIN `profile` pf ON(pf.user_id=zot.order_tran_doctor) 
                INNER JOIN zdata_order_header zoh ON(zoh.id=zot.order_header_id)
                WHERE zot.rstat='1' AND zoh.order_visit_id=:visitid 
                ORDER BY zog.order_group_orderby ASC,zot.create_date DESC";

        $dataProvider = new \yii\data\SqlDataProvider([
            'sql' => $sql,
            'params' => [':visitid' => $visit_id],
            'sort' => ['attributes' => ['order_tran_code', 'order_name', 'full_price', 'create_date']],
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

        return $dataProvider;
    }

    public static function modelSearchDataProvider($ezform, $modelFields, $modelFilter = null, $where = null, $params = null, $pageSize = 10, $orderby = SORT_DESC, $userProfile) {
        $query = new \yii\db\Query();
        $query->where("{$ezform['ezf_table']}.rstat=1");

        if (isset($modelFields) && !empty($modelFields)) { //fields
            $tmpCol = [];
            $chkRefField = [];
            foreach ($modelFields as $field) {
                if (!empty($field['ref_field']) && !in_array($field['ref_field'], $chkRefField)) {
                    if ($field['ezf_table'] != $ezform['ezf_table']) {
                        $query->innerJoin($field['ezf_table'], "`{$field['ezf_table']}`.`{$field['ref_field']}` = `{$ezform['ezf_table']}`.`{$field['ref_field_id']}` AND `{$field['ezf_table']}`.rstat=1");
                        $chkRefField[] = $field['ref_field'];
                    }
                }

                if (!in_array($field['ezf_id'] . $field['ezf_field_id'], $tmpCol)) {
                    $tmpCol[] = $field['ezf_id'] . $field['ezf_field_id'];
                    $selectCol[] = (isset($field['ezf_table']) ? $field['ezf_table'] : $ezform['ezf_table']) . '.' . $field['ezf_field_name'];
                }
            }

            $selectCol = \yii\helpers\ArrayHelper::merge($selectCol, ["{$ezform['ezf_table']}.id", "{$ezform['ezf_table']}.target", "{$ezform['ezf_table']}.ptid", "{$ezform['ezf_table']}.xsourcex", "{$ezform['ezf_table']}.create_date", "{$ezform['ezf_table']}.rstat", "{$ezform['ezf_table']}.ezf_version"]);
        } else {
            $selectCol[] = "{$ezform['ezf_table']}.*";
        }
        $query->select($selectCol)->from($ezform['ezf_table']);

        if ($where != null) {
            if (count($where) > 1) {
                foreach ($where as $value) {
                    $query->andWhere($value);
                }
            } else {
                $query->andWhere($where);
            }
        }

        if ($ezform['public_listview'] == 0) {
            $query->andWhere("{$ezform['ezf_table']}.user_create=:created_by", [':created_by' => Yii::$app->user->id]);
        }

        if ($ezform['public_listview'] == 3) {
            $query->andWhere("{$ezform['ezf_table']}.xdepartmentx = :unit", [':unit' => $userProfile->department]);
        }

        if (isset($specialField)) {
            $query->andWhere("{$ezform['ezf_table']}.hsitecode = :site", [':site' => $userProfile->sitecode]);
        } elseif ($ezform['public_listview'] == 2) {
            $query->andWhere("{$ezform['ezf_table']}.xsourcex = :site", [':site' => $userProfile->sitecode]);
        }

        if (!is_array($orderby)) {
            $query->orderBy([$ezform['ezf_table'] . '.create_date' => $orderby]);
        } else {
            $query->orderBy($orderby);
        }

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
        ]);

        if (isset($model['create_date']) && !empty($model['create_date'])) {
            $daterang = explode(' to ', $model['create_date']);
            if (isset($daterang[1])) {
                $sdate = \appxq\sdii\utils\SDdate::phpThDate2mysqlDate($daterang[0], '-');
                $edate = \appxq\sdii\utils\SDdate::phpThDate2mysqlDate($daterang[1], '-');

                $query->andFilterWhere(['between', 'date(' . $ezform['ezf_table'] . '.create_date)', $sdate, $edate]);
            }
        }
        if (isset($modelFilter) && !empty($modelFilter)) { //fields
            $tmpCol = [];
            $concat = "CONCAT(";
            foreach ($modelFilter as $field) {
                if (!empty($field['ref_field']) && !in_array($field['ref_field'], $chkRefField)) {
                    if ($field['ezf_table'] != $ezform['ezf_table']) {
                        $query->innerJoin($field['ezf_table'], "`{$field['ezf_table']}`.`{$field['ref_field']}` = `{$ezform['ezf_table']}`.`{$field['join_field_id']}` AND `{$field['ezf_table']}`.rstat=1");
                        $chkRefField[] = $field['ref_field'];
                    }
                }

                if ($field['ref_field']) {
                    $ref_field_search = SDUtility::string2Array($field['ref_field_search']);
                    if ($ref_field_search) {
                        foreach ($ref_field_search as $value) {
                            $concat .= $field['ezf_table'] . "." . $value . ",";
                        }
                    }
                } else {
                    $concat .= (isset($field['ezf_table']) ? $field['ezf_table'] : $ezform['ezf_table']) . '.' . $field['ezf_field_name'] . ",";
                }
            }
            $concat .= "'')";
            $query->andFilterWhere(['like', $concat, $params['order_name']]); //fix ไปก่อนยังคิดไม่ออก $params['order_name']
        }
//        \appxq\sdii\utils\VarDumper::dump($query->createCommand()->rawSql);
        return $dataProvider;
    }

    public static function mysql2phpThDateMonthTime($sqlDate) {
        $arr = explode(' ', $sqlDate);
        $date = new DateTime($arr[0]);
        return \appxq\sdii\utils\SDdate::thFormatDateSmall($date) . ' ' . substr($arr[1], 0, 5);
    }

    /**
     * 
     * @param string $bdate (YYYY-MM-DD)
     * @param type $splitter ("/")
     * @return string
     */
    public static function checkBdate($bdate, $splitter = "/") {
        $arrBdate = explode($splitter, $bdate);
        $changeDate = FALSE;
        if ($arrBdate[0] == '00') {
            $changeDate = TRUE;
            $arrBdate[2] = date('Y') - 111; //year            
        }
        if ($arrBdate[1] == '00') {
            $changeDate = TRUE;
            $arrBdate[1] = '07'; //month
        }
        if ($arrBdate[2] == '00') {
            $changeDate = TRUE;
            $arrBdate[0] = '01'; //day
        }
        if ($changeDate) {
            $bdate = $arrBdate[0] . '/' . $arrBdate[1] . '/' . $arrBdate[2];
        }

        return $bdate;
    }

    public static function changeSexDbToText($sex) {
        switch ($sex) {
            case "1":
                $sex = "ชาย";
                break;
            case "2":
                $sex = "หญิง";
                break;
            default:
                $sex = FALSE;
                break;
        }
    }

    private function array_sort($array, $on, $order = SORT_ASC) {
        $new_array = array();
        $sortable_array = array();

        if (count($array) > 0) {
            foreach ($array as $k => $v) {
                if (is_array($v)) {
                    foreach ($v as $k2 => $v2) {
                        if ($k2 == $on) {
                            $sortable_array[$k] = $v2;
                        }
                    }
                } else {
                    $sortable_array[$k] = $v;
                }
            }

            switch ($order) {
                case SORT_ASC:
                    asort($sortable_array);
                    break;
                case SORT_DESC:
                    arsort($sortable_array);
                    break;
            }

            foreach ($sortable_array as $k => $v) {
                array_push($new_array, $array[$k]);
            }
        }

        return $new_array;
    }

    public static function isUrlExist($url) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($code == 200) {
            $status = true;
        } else {
            $status = false;
        }
        curl_close($ch);
        return $status;
    }

}
