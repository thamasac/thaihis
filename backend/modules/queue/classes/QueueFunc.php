<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace backend\modules\queue\classes;

use appxq\sdii\utils\VarDumper;
use backend\modules\ezforms2\models\EzformFields;
use yii\db\Query;
use backend\modules\ezforms2\classes\EzfQuery;
use yii\helpers\Html;
use Yii;
use appxq\sdii\utils\SDUtility;

/**
 * Description of GetFields
 *
 * @author AR9
 */
class QueueFunc
{
    //put your code here

    /**
     *
     * @param type $bdate
     * @param type $year
     * @param type $month
     * @param type $day
     * @return string
     */
    public static function calAge($bdate, $year = true, $month = true, $day = true, $label = true)
    {
        //$age = date_diff(date('Y-m-d'), $bdate);
//        \appxq\sdii\utils\VarDumper::dump($bdate);
        $age = '';
        if ($bdate != null || $bdate != '') {
            $diff = abs(strtotime(date('Y-m-d')) - strtotime($bdate));
            if ($year) {
                $years = floor($diff / (365 * 60 * 60 * 24));
                $label ? $age .= $years . ' ปี ' : $age .= $years;
            }
            if ($month) {
                $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
                $label ? $age .= $months . ' เดือน ' : $age .= $months;
            }
            if ($day) {
                $days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));
                $label ? $age .= $days . ' วัน ' : $age .= $days . ' วัน ';
            }
        }
        return $age;
    }

    public static function getQueryJoin($ezf_main_id, $ezf_ref_id, $field_name = null, $typeQuery = '', $groupBy = '')
    {

        if (isset($ezf_main_id) && $ezf_main_id != '') {
            $query = new Query();
            $dataEzfMain = (new Query())->select('*')->from('ezform')->where(['ezf_id' => $ezf_main_id])->one();
            $ezformParent = Null;
            $targetField = EzfQuery::getTargetOne($ezf_main_id);
            $arrEzf = [];
            $arrEzfCheck = [];
            $arrForm = [];
            if ($targetField) {
                $ezformParent = EzfQuery::getEzformById($targetField->ref_ezf_id);
                if ($ezformParent) {
                    $arrEzfCheck[] = $ezformParent['ezf_id'];
                    if (is_array($field_name)) {
                        $field_name[] = $ezformParent['ezf_table'] . '.id as idParent';
                        $field_name[] = $ezformParent['ezf_table'] . '.target as targetParent';
                        $field_name[] = $ezformParent['ezf_table'] . '.ptid as ptidParent';
                    } else {
                        if ($field_name == '' || $field_name == null) {
                            $field_name .= $ezformParent['ezf_table'] . '.id as idParent,' . $ezformParent['ezf_table'] . '.target as targetParent,' . $ezformParent['ezf_table'] . '.ptid as ptidParent';
                        } else {
                            $field_name .= ',' . $ezformParent['ezf_table'] . '.id as idParent,' . $ezformParent['ezf_table'] . '.target as targetParent,' . $ezformParent['ezf_table'] . '.ptid as ptidParent';
                        }
                    }
                }
            }

            if (is_array($field_name)) {
                $field_name[] = $dataEzfMain['ezf_table'] . '.id as idMain';
                $field_name[] = $dataEzfMain['ezf_table'] . '.target as targetMain';
                $field_name[] = $dataEzfMain['ezf_table'] . '.ptid as ptidMain';
            } else {
                if ($field_name == '' || $field_name == null) {
                    $field_name .= $dataEzfMain['ezf_table'] . '.id as idMain,' . $dataEzfMain['ezf_table'] . '.target as targetMain,' . $dataEzfMain['ezf_table'] . '.ptid as ptidMain';
                } else {
                    $field_name .= ',' . $dataEzfMain['ezf_table'] . '.id as idMain,' . $dataEzfMain['ezf_table'] . '.target as targetMain,' . $dataEzfMain['ezf_table'] . '.ptid as ptidMain';
                }
            }

            if (($dataEzfMain && !empty($dataEzfMain)) && ($field_name != '' && !empty($field_name))) {
                $query->select($field_name)->from($dataEzfMain['ezf_table'])->where('1');
//                    ->where([$dataEzfMain['ezf_table'] . '.xsourcex' => Yii::$app->user->identity->profile->sitecode]);
            } else {
                return false;
            }

//        \appxq\sdii\utils\VarDumper::dump($ezf_ref_id);
            if ($targetField && !empty($targetField)) {// join 
                $pk = $targetField->ezf_field_name;
                $pkJoin = $targetField->ref_field_id;
                $query->innerJoin($ezformParent['ezf_table'], "`{$ezformParent['ezf_table']}`.`$pkJoin` = `{$dataEzfMain['ezf_table']}`.`$pk` AND {$ezformParent['ezf_table']}.rstat not in (0,3)");
            }

            if ($ezf_ref_id && is_array($ezf_ref_id) && !empty($ezf_ref_id)) {
                $dataEzfRef2 = null;
                foreach ($ezf_ref_id as $vRef) {
                    if (isset($vRef['value']) && is_array($vRef['value'])) {
                        if (isset($vRef['type_join']) && $vRef['type_join'] == 'Inner Join') {
                            $type_join = 'innerJoin';
                        } else if (isset($vRef['type_join']) && $vRef['type_join'] == 'Right Join') {
                            $type_join = 'rightJoin';
                        } else if (isset($vRef['type_join']) && $vRef['type_join'] == 'Left Join') {
                            $type_join = 'leftJoin';
                        } else {
                            $type_join = 'innerJoin';
                        }
                        foreach ($vRef['value'] as $valEzf) {
//                            $arrEzfCheck[] = $valEzf;
                            $selectCom = '(' . (new Query())->select('ezf_table')->from('ezform as ezf')->where('ezf.ezf_id = ezff.ezf_id')->createCommand()->rawSql . ') as ref_form';
                            $selectCom .= ',(' . (new Query())->select('ezf_table')->from('ezform as ezf')->where('ezf.ezf_id = ezff.ref_ezf_id')->createCommand()->rawSql . ') as parent_form';
                            $selectCom .= ',ezff.ref_field_id,ezff.ezf_field_name,ezff.ref_ezf_id';
                            $dataEzfCom = (new Query())->select($selectCom)->from('ezform_fields as ezff')
                                ->where(['ezff.ezf_id' => $valEzf, 'ezff.ezf_field_type' => '80'])
                                ->all();
                            if ($dataEzfCom && is_array($dataEzfCom) && !empty($dataEzfCom)) {
                                foreach ($dataEzfCom as $vEzfCom) {
                                    $arrEzfCheck[] = $vEzfCom['ref_ezf_id'];

                                }
                            }
//                            VarDumper::dump($dataEzfCom,0,10);
//                            echo '<br>';

                            if (!in_array($valEzf, $arrEzfCheck)) {
                                $select = '(' . (new Query())->select('ezf_table')->from('ezform as ezf')->where('ezf.ezf_id = ezff.ezf_id')->createCommand()->rawSql . ') as ref_form';
                                $select .= ',(' . (new Query())->select('ezf_table')->from('ezform as ezf')->where('ezf.ezf_id = ezff.ref_ezf_id')->createCommand()->rawSql . ') as parent_form';
                                $select .= ',ezff.ref_field_id,ezff.ezf_field_name';
                                $dataEzfRef2 = (new Query())->select($select)->from('ezform_fields as ezff')
                                    ->where(['ezff.ezf_id' => $valEzf, 'ezff.ezf_field_type' => '79'])
                                    ->one();
                                if ($dataEzfRef2 && is_array($dataEzfRef2) && !empty($dataEzfRef2)) {
                                    $arrEzf[] = $valEzf;
                                    if (isset($dataEzfRef2['ref_form']) && $dataEzfRef2['ref_form'] != '') {

                                        eval("\$query->$type_join(\$dataEzfRef2['ref_form'], \$dataEzfRef2['ref_form'] . '.target = ' . \$dataEzfRef2['parent_form'] . '.id AND '.\$dataEzfRef2['ref_form'].'.rstat not in (0,3)');");
                                    }
                                    $dataEzfRef2 = null;
//                            }
                                } else {
                                    $select = '(' . (new Query())->select('ezf_table')->from('ezform as ezf')->where('ezf.ezf_id = ezff.ezf_id')->createCommand()->rawSql . ') as ref_form';
                                    $select .= ',(' . (new Query())->select('ezf_table')->from('ezform as ezf')->where('ezf.ezf_id = ezff.ref_ezf_id')->createCommand()->rawSql . ') as parent_form';
                                    $select .= ',ezff.ref_field_id,ezff.ezf_field_name';
                                    $dataEzfRef2 = (new Query())->select($select)->from('ezform_fields as ezff')
                                        ->where(['ezff.ref_ezf_id' => $valEzf, 'ezff.ezf_id' => $arrEzf])
                                        ->one();
                                    if (isset($dataEzfRef2['parent_form']) && $dataEzfRef2['parent_form'] != '') {
                                        eval("\$query->$type_join(\$dataEzfRef2['parent_form'], \$dataEzfRef2['parent_form'] . '.id = ' . \$dataEzfRef2['ref_form'] . '.target AND '.\$dataEzfRef2['parent_form'].'.rstat not in (0,3)');");
                                    }
                                    $dataEzfRef2 = null;
                                }
                            }
//                            VarDumper::dump($ezf_ref_id);
                            if ($dataEzfCom && is_array($dataEzfCom) && !empty($dataEzfCom)) {
                                foreach ($dataEzfCom as $vEzfCom) {
                                    foreach ($ezf_ref_id as $vRef2) {
                                        if (isset($vRef2['value']) && is_array($vRef2['value'])) {
                                            foreach ($vRef2['value'] as $valEzf2) {
                                                if ($valEzf2 == $vEzfCom['ref_ezf_id'] && !in_array($vEzfCom['parent_form'],$arrForm)) {
                                                    $arrForm[] = $vEzfCom['parent_form'];
                                                    eval("\$query->$type_join(\$vEzfCom['parent_form'], \$vEzfCom['parent_form'] . '.' . \$vEzfCom['ref_field_id'] . ' = ' . \$vEzfCom['ref_form'] . '.' . \$vEzfCom['ezf_field_name'].' AND '.\$vEzfCom['parent_form'].'.rstat not in (0,3)');");
//                                                    $query->leftJoin($vEzfCom['parent_form'], $vEzfCom['parent_form'] . '.' . $vEzfCom['ref_field_id'] . ' = ' . $vEzfCom['ref_form'] . '.' . $vEzfCom['ezf_field_name'].' AND '.$vEzfCom['parent_form'].'.rstat not in (0,3)');
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        if ($vRef != '') {
                            $selectCom = '(' . (new Query())->select('ezf_table')->from('ezform as ezf')->where('ezf.ezf_id = ezff.ezf_id')->createCommand()->rawSql . ') as ref_form';
                            $selectCom .= ',(' . (new Query())->select('ezf_table')->from('ezform as ezf')->where('ezf.ezf_id = ezff.ref_ezf_id')->createCommand()->rawSql . ') as parent_form';
                            $selectCom .= ',ezff.ref_field_id,ezff.ezf_field_name,ezff.ref_ezf_id';
                            $dataEzfCom = (new Query())->select($selectCom)->from('ezform_fields as ezff')
                                ->where(['ezff.ezf_id' => $vRef, 'ezff.ezf_field_type' => '80'])
                                ->all();
                            if ($dataEzfCom && is_array($dataEzfCom) && !empty($dataEzfCom)) {
                                foreach ($dataEzfCom as $vEzfCom) {
                                    $arrEzfCheck[] = $vEzfCom['ref_ezf_id'];
                                }
                            }
                            if (!in_array($vRef, $arrEzfCheck)) {
                                $select = '(' . (new Query())->select('ezf_table')->from('ezform as ezf')->where('ezf.ezf_id = ezff.ezf_id')->createCommand()->rawSql . ') as ref_form';
                                $select .= ',(' . (new Query())->select('ezf_table')->from('ezform as ezf')->where('ezf.ezf_id = ezff.ref_ezf_id')->createCommand()->rawSql . ') as parent_form';
                                $select .= ',ezff.ref_field_id,ezff.ezf_field_name';
                                $dataEzfRef2 = (new Query())->select($select)->from('ezform_fields as ezff')
                                    ->where(['ezff.ezf_id' => $vRef, 'ezff.ezf_field_type' => '79'])
                                    ->one();
                                if ($dataEzfRef2 && is_array($dataEzfRef2) && !empty($dataEzfRef2)) {
                                    $arrEzf[] = $vRef;
                                    if (isset($dataEzfRef2['ref_form']) && $dataEzfRef2['ref_form'] != '') {
                                        $query->innerJoin($dataEzfRef2['ref_form'], $dataEzfRef2['ref_form'] . '.target = ' . $dataEzfRef2['parent_form'] . '.id' . ' AND ' . $dataEzfRef2['ref_form'] . '.rstat not in (0,3)');
                                    }
                                    $dataEzfRef2 = null;
//                            }
                                } else {
                                    $select = '(' . (new Query())->select('ezf_table')->from('ezform as ezf')->where('ezf.ezf_id = ezff.ezf_id')->createCommand()->rawSql . ') as ref_form';
                                    $select .= ',(' . (new Query())->select('ezf_table')->from('ezform as ezf')->where('ezf.ezf_id = ezff.ref_ezf_id')->createCommand()->rawSql . ') as parent_form';
                                    $select .= ',ezff.ref_field_id,ezff.ezf_field_name';
                                    $dataEzfRef2 = (new Query())->select($select)->from('ezform_fields as ezff')
                                        ->where(['ezff.ref_ezf_id' => $vRef, 'ezff.ezf_id' => $arrEzf])
                                        ->one();
                                    if (isset($dataEzfRef2['parent_form']) && $dataEzfRef2['parent_form'] != '') {
                                        $query->innerJoin($dataEzfRef2['parent_form'], $dataEzfRef2['parent_form'] . '.id = ' . $dataEzfRef2['ref_form'] . '.target' . ' AND ' . $dataEzfRef2['parent_form'] . '.rstat not in (0,3)');
                                    }
                                    $dataEzfRef2 = null;
                                }
                            }
                            if ($dataEzfCom && is_array($dataEzfCom) && !empty($dataEzfCom)) {
                                foreach ($dataEzfCom as $vEzfCom) {
                                    foreach ($ezf_ref_id as $vRef2) {
                                        if ($vRef2 == $vEzfCom['ref_ezf_id'] && !in_array($vEzfCom['parent_form'],$arrForm)) {
                                            $arrForm[] = $vEzfCom['parent_form'];
                                            $query->leftJoin($vEzfCom['parent_form'], $vEzfCom['parent_form'] . '.' . $vEzfCom['ref_field_id'] . ' = ' . $vEzfCom['ref_form'] . '.' . $vEzfCom['ezf_field_name'] . ' AND ' . $vEzfCom['parent_form'] . '.rstat not in (0,3)');
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
//VarDumper::dump($arrForm);
            if ($ezformParent) {
                $query->andWhere("`{$ezformParent['ezf_table']}`.rstat NOT IN(0,3)");
//                $query->groupBy("{$ezformParent['ezf_table']}.id");
            }

            if ($groupBy != '') {
                $select = '(' . (new Query())->select('ezf_table')->from('ezform as ezf')->where('ezf.ezf_id = ezff.ezf_id')->createCommand()->rawSql . ') as ezf_table,ezff.ezf_field_name';
                $dataField = (new Query())->select($select)->from('ezform_fields as ezff')
                    ->where(['ezff.ezf_field_id' => $groupBy])
                    ->one();
                if ($dataField) {
                    $query->groupBy("{$dataField['ezf_table']}.{$dataField['ezf_field_name']}");
                }
            }
//exit();
            if ($typeQuery != '' || $typeQuery != null) {
                if ($typeQuery == 'all') {
                    $query->all();
                } else if ($typeQuery == 'one') {
                    $query->one();
                } else if ($query == 'sql') {
                    $query->createCommand()->sql;
                }
            }

            return $query;
        } else {
            return false;
        }
    }

    public static function htmlFilter($dataField, $dataInput, $field_name, $value = '')
    {
        $htmlFilter = Html::textInput('search_field[' . $field_name . ']', $value, ['class' => 'form-control search-input', 'placeholder' => $dataField['ezf_field_label']]);
        if ($dataField['ezf_field_type'] != 0 && !in_array($dataField['ezf_field_type'], [51, 55, 62, 66, 67, 68, 71, 81, 83, 84, 912, 913])) {
            if ($dataField['ezf_field_type'] == 61) {
                $data = SDUtility::string2Array($dataField['ezf_field_data']);
                if (isset($data['items']['data'])) {
                    $newData = \yii\helpers\ArrayHelper::merge(['' => 'ค่าเริ่มต้น'], $data['items']['data']);
                    $items = ['items' => $newData];
                    $dataField['ezf_field_data'] = SDUtility::array2String($items);
                }
                $dataInput['input_function'] = 'dropDownList';
            }

            if (in_array($dataField['ezf_field_type'], [63, 64])) {
                $htmlFilter = \kartik\date\DatePicker::widget([
                    'name' => 'search_field[' . $field_name . ']',
                    'value' => $value,
                    'type' => \kartik\date\DatePicker::TYPE_COMPONENT_APPEND,
                    'options' => ['class' => 'search-input form-control'],
                    'pluginOptions' => [
                        'todayHighlight' => true,
                        'todayBtn' => true,
                        'format' => 'yyyy-mm-dd',
                        'autoclose' => true,
                    ]
                ]);
            } else if (in_array($dataField['table_field_type'], ['none', 'field'])) {
                $htmlFilter = '';
            } else {
                $htmlFilter = self::generateInputOnly($dataField, $dataInput, false, $value);
            }
        }

        return $htmlFilter;
    }

    public static function generateInputOnly($dataFields, $dataInput, $disableFields = 0, $value = '')
    {
        $html = '';
        $model = null;
        try {
            if ($dataFields['table_field_type'] != 'none' && $dataFields['table_field_type'] != '') {
                $dataInput;

                if ($dataInput) {
                    $options = SDUtility::string2ArrayJs($dataFields['ezf_field_options']);
                    unset($options['specific']);

                    $data = SDUtility::string2Array($dataFields['ezf_field_data']);

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
                                        'field' => $dataFields,
                                        'data' => $model
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
                        if (isset($model[$dataFields['ezf_field_name']]) && !empty($model[$dataFields['ezf_field_name']])) {

                            if (isset($options['options']['data-func-set']) && !empty($options['options']['data-func-set'])) {
                                $pathStr = [
                                    '{model}' => "\$model",
                                    '{modelFields}' => "\$dataFields",
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

                        $attribute = $dataFields['ezf_field_name'];
                        if (isset($options['options']['multiple']) && $options['options']['multiple'] == true) {
                            $attribute = $dataFields['ezf_field_name'] . '[]';
                        }

                        $arryClass = explode('::', $dataInput['input_class']);

//                        $options['model'] = $model;
                        $options['name'] = 'search_field[' . $attribute . ']';

                        $options['id'] = 'SD_' . $dataFields['ezf_id'] . '_' . $dataFields['ezf_field_name'] . '_' . SDUtility::getMillisecTime();
                        $options['value'] = $value;
                        if (isset($options['options']['class'])) {
                            $options['options']['class'] .= ' form-control search-input';
                        } else {
                            $options['options']['class'] = 'form-control search-input';
                        }
                        $options['options']['id'] = $options['id'];
                        eval("\$html = {$arryClass[0]}::widget(\$options);");
                    } else {
                        if (isset(Yii::$app->session['show_varname']) && Yii::$app->session['show_varname']) {
                            $options['annotated'] = 1;
                        }

                        if ($disableFields) {
                            $options['disabled'] = $disableFields;
                        }

                        $input_function = ucfirst($dataInput['input_function']);


                        if (isset($options['options']['class'])) {
                            $options['class'] .= ' form-control search-input';
                        } else {
                            $options['class'] = 'form-control search-input';
                        }

                        if (empty($data)) {
                            eval("\$html = \yii\helpers\Html::$input_function('search_field['.\$dataFields['ezf_field_name'].']',\$value,'' \$options);");
                        } else {
                            if (isset($data['func'])) {
                                eval("\$dataItems = {$data['func']};");
                            } else {
                                $dataItems = $data['items'];
                            }

                            eval("\$html = \yii\helpers\Html::$input_function('search_field['.\$dataFields['ezf_field_name'].']',\$value, \$dataItems, \$options);");
                        }
//                        \appxq\sdii\utils\VarDumper::dump($input_function);
                    }
                } else {
                    $html = \yii\helpers\Html::activeHiddenInput($model, $dataFields['ezf_field_name']);
                }
            }

            return $html;
        } catch (yii\base\Exception $e) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
            return '<code>' . $e->getMessage() . '</code>';
        }
    }

    /**
     *
     * @param type $ezf_id one & array
     * @return type array field
     */
    public static function getFieldFormById($ezf_id, $field_type = null)
    {
        $data = (new Query())->select(['ezf_field_id as id', 'ezf_name', 'CONCAT(ezf_field_name," (",ezf_field_label,")") as name', 'ef.ezf_id', 'ezf_field_type'])
            ->from('ezform_fields ef')
            ->innerJoin('ezform ez', "ez.ezf_id=ef.ezf_id AND (ez.ezf_version=ef.ezf_version OR ef.ezf_version='all')")
            ->where('(ezf_field_type NOT IN(0,57,899,59) OR  ezf_field_name = \'id\')')
            ->andWhere(['ef.ezf_id' => $ezf_id]);

        if ($field_type) {
            $data->andWhere(['ezf_field_type' => $field_type]);
        }
//        VarDumper::dump($data->createCommand()->rawSql,1,0);
        return $data->all();
    }

    public static function getFormRefMainForm($ezf_id)
    {
        $model1 = new Query();
        $model1->select('ezf.ezf_id as `id`,ezf.ezf_name as `name`')
            ->from('ezform_fields ezff')
            ->innerJoin('ezform ezf', 'ezf.ezf_id=ezff.ezf_id')
            ->where(['ezff.ref_ezf_id' => $ezf_id])
            ->andWhere('ezff.ezf_field_type=79  OR ezff.ezf_field_type=80');

        $model2 = new Query();
        $model2->select('ezf.ezf_id as `id`,ezf.ezf_name as `name`')
            ->from('ezform_fields ezff')
            ->innerJoin('ezform ezf', 'ezf.ezf_id=ezff.ref_ezf_id')
            ->where(['ezff.ezf_id' => $ezf_id])
            ->andWhere('ezff.ezf_field_type=79  OR ezff.ezf_field_type=80');
        $result = $model1->union($model2);

        $dataForm = $result->all();

        return $dataForm;
    }

    /**
     *
     * @param type $field_id
     * @param type $limit default one
     * @return type
     */
    public static function getFieldDetailById($field_id, $limit = "one")
    {
        $data = (new Query())
            ->select(['ez.ezf_id', 'ezf_name', 'ezf_table', 'ef.*'])
            ->from('ezform_fields ef')
            ->innerJoin('ezform ez', "ez.ezf_id=ef.ezf_id AND (ez.ezf_version=ef.ezf_version OR ef.ezf_version='all')")
            ->where('(ezf_field_type NOT IN(0,57,899,59) OR  ezf_field_name = \'id\')')
            ->andWhere(['ef.ezf_field_id' => $field_id]);

        if ($limit == "one") {
            return $data->one();
        } else {
            return $data->all();
        }
    }

}
