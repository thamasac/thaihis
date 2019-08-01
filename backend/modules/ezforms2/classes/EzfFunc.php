<?php

namespace backend\modules\ezforms2\classes;

use Yii;
use appxq\sdii\models\SDDynamicModel;
use backend\modules\ezforms2\classes\EzfQuery;
use appxq\sdii\utils\SDUtility;
use yii\helpers\ArrayHelper;
use yii\web\View;
use backend\modules\ezforms2\models\Ezform;
use backend\modules\ezforms2\models\EzformFields;
use backend\modules\ezforms2\models\EzformChoice;
use backend\modules\ezforms2\models\EzformCondition;
use backend\modules\ezforms2\models\EzformVersion;
use backend\modules\ezforms2\models\EzformAutonum;
use yii\helpers\Html;
use backend\modules\core\classes\CoreFunc;

/**
 * OvccaFunc class file UTF-8
 * @author SDII <iencoded@gmail.com>
 * @copyright Copyright &copy; 2015 AppXQ
 * @license http://www.appxq.com/license/
 * @version 1.0.0 Date: 9 ก.พ. 2559 12:38:14
 * @link http://www.appxq.com/
 * @example 
 */
class EzfFunc {

    public static function setDynamicModel($fields, $table, $ezf_input, $annotated = 0) {
        $attributes = ['ptid', 'xsourcex', 'xdepartmentx', 'rstat', 'sitecode', 'ptcode', 'ptcodefull', 'hptcode', 'hsitecode', 'user_create', 'create_date', 'user_update', 'update_date', 'target', 'error', 'sys_lat', 'sys_lng', 'ezf_version'];
        $labels = [];
        $required = [];
        $rules = [];
        //$rulesFields = [];
        $rulesFields['safe'] = $attributes;
        $condFields = [];
        $behavior = [];
        $fields_type = [];
        $ezf_id='';

        if (!empty($fields)) {
            foreach ($fields as $value) {
                $ezf_id = $value['ezf_id'];
                $fields_type[$value['ezf_field_name']] = $value['ezf_field_type'];
                //Attributes array
                $value_default = $value['ezf_field_default'];
                if ($value['table_field_type'] == 'none' || $value['table_field_type'] == 'field') {
                    $value_default = NULL;
                }
                $attributes[$value['ezf_field_name']] = $value_default;
                
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
                    $condFields[] = self::getCondition($value['ezf_id'], $value['ezf_field_name']);
                }

                $dataInput;
                if ($ezf_input) {
                    $dataInput = EzfFunc::getInputByArray($value['ezf_field_type'], $ezf_input);
                }
                if ($dataInput) {
                    $behavior = ArrayHelper::merge($behavior, self::setBehavior($table, $value->attributes, $value->ezf_field_type, $value->ezf_field_name, $dataInput));
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
                        //\appxq\sdii\utils\VarDumper::dump($data,0);
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

    public static function setBehavior($table, $attributes, $ezf_field_type, $ezf_field_name, $dataInput) {

        $behavior = [];

        try {
            if ($dataInput) {
                if (isset($dataInput['input_behavior']) && $dataInput['input_behavior'] != '') {
                    $behavior[$dataInput['input_behavior'] . '_' . $ezf_field_name] = [
                        'class' => $dataInput['input_behavior'],
                        'ezf_field' => $attributes,
                        'ezf_table' => $table,
                    ];
                }
            }
        } catch (\ReflectionException $e) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
        }

        return $behavior;
    }

    public static function getBehavior($fields, $table, $dataInput) {

        $behavior = [];

        if (!empty($fields)) {
            foreach ($fields as $value) {
                if ($dataInput) {
                    $behavior = ArrayHelper::merge($behavior, self::setBehavior($table, $value->attributes, $value->ezf_field_type, $value->ezf_field_name, $dataInput));
                }
            }
        }

        return $behavior;
    }

    public static function generateInput($form, $model, $modelFields, $dataInput, $disableFields=0, $modelEzf, $ui=1, $widgets = '//../modules/ezforms2/views/widgets/_view_item') {
        $html = '';
        $view = Yii::$app->getView();
        $bgformClass = 'bgform-area';
        
        try {
            $widgetOption = [];
            if ($modelFields['table_field_type'] != 'none' && $modelFields['table_field_type'] != '') {
                $dataInput;

                if ($dataInput) {
                    $specific = SDUtility::string2Array($modelFields['ezf_field_specific']);
                    $options = SDUtility::string2ArrayJs($modelFields['ezf_field_options']);
                    
                    if(isset($options['widgetOption'])){
                        $widgetOption = $options['widgetOption'];
                    }
                    if(isset($options['specific']['group_class']) && !empty($options['specific']['group_class'])){
                        if(isset($widgetOption['class'])){
                            $widgetOption['class'] .= " {$options['specific']['group_class']} ";
                        } else {
                            $widgetOption['class'] = "{$options['specific']['group_class']} ";
                        }
                    }
                    if(isset($options['specific']['hide']) && !empty($options['specific']['hide'])){
                        if(isset($widgetOption['style'])){
                            $widgetOption['style'] .= ' display: none;';
                        } else {
                            $widgetOption['style'] = 'display: none;';
                        }
                        
                    }
                    unset($options['specific']);
                    unset($options['widgetOption']);
                    
                    $data = SDUtility::string2Array($modelFields['ezf_field_data']);

                    $attr_label = $model->getAttributeLabel($modelFields['ezf_field_name']);
                    if (isset($modelFields['ezf_field_label']) && $modelFields['ezf_field_label'] == '') {
                        $attr_label = '';
                    }
                    
                    $label = "->label('{$attr_label}')";
                    
                    //inline, label fix
                    if ($dataInput['input_function'] == 'widget') {
                        
                        if (isset(Yii::$app->session['show_varname']) && Yii::$app->session['show_varname']){
                            $options['options']['annotated'] = 1;
                        }
                        
                        if ($disableFields){
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
                            
                            if(isset($options['options']['annotated']) && $options['options']['annotated'] == 1 && isset($options['data'])){
                                $value_label = self::export_obj($options['data']);
                                
                                $label = "->label('{$attr_label} {$value_label}')";
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
                                    if(is_array($initial) && $options['options']['data-type'] != 'file-upload'){
                                        $options['data'] = \yii\helpers\ArrayHelper::map($initial, 'id', 'name');
                                    } else {
                                        if (isset($options['options']['data-name-in'])) {
                                            $data_in = $options['options']['data-name-in'];
                                            $data_set = self::addProperty($data_in, $options['options']['data-name-set'], $initial);
                                            $options = ArrayHelper::merge($options, $data_set);
                                        } else {
                                            $options[$options['options']['data-name-set']] = $initial;
                                        }
                                    }
                                }
                            }
                            
                            if (isset($options['options']['showWidget']) && $options['options']['showWidget']==1) {
                                if (isset($options['options']['data-data-widget']) && !empty($options['options']['data-data-widget'])) {
                                    $widget_render = $view->renderAjax($options['options']['data-data-widget'], [
                                        'model' => $model,
                                        'modelFields' => $modelFields,
                                    ]);
                                }
                            }
                            
                        }
                        
                        $attribute = "\$modelFields['ezf_field_name']";
//                        if (isset($options['options']['multiple']) && $options['options']['multiple'] == true) {
//                            $attribute = "\$modelFields['ezf_field_name'].'[]'";
//                        }

                        eval("\$html = \$form->field(\$model, $attribute, \$specific)->hint(\$modelFields['ezf_field_hint'])->{$dataInput['input_function']}({$dataInput['input_class']}, \$options)$label;");
                        $html .= $widget_render;
                        
                    } else {
                        if (isset(Yii::$app->session['show_varname']) && Yii::$app->session['show_varname']==1){
                            $options['annotated'] = 1;
                        }
                        
                        if ($disableFields){
                            $options['disabled'] = $disableFields;
                        }
                        if (empty($data)) {
                            
                            eval("\$html = \$form->field(\$model, \$modelFields['ezf_field_name'], \$specific)->hint(\$modelFields['ezf_field_hint'])->{$dataInput['input_function']}(\$options)$label;");
                        } else {
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
                            } else {
                                $dataItems = $data['items'];
                            }
                            if(isset($options['annotated']) && $options['annotated'] == 1 && isset($dataItems)){
                                if(isset($dataItems['data'])){
                                    $value_label = self::export_obj($dataItems['data']);
                                } else {
                                    $value_label = self::export_obj($dataItems);
                                }

                                $label = "->label('{$attr_label} {$value_label}')";
                            }
                            
                            eval("\$html = \$form->field(\$model, \$modelFields['ezf_field_name'], \$specific)->hint(\$modelFields['ezf_field_hint'])->{$dataInput['input_function']}(\$dataItems, \$options)$label;");
                        }
                    }
                } else {
                    $html = Html::activeHiddenInput($model, $modelFields['ezf_field_name']);
                }
            } else {

                if ($dataInput) {
                    $class = str_replace('::className()', '', $dataInput['input_class']);
                    $options = SDUtility::string2Array($modelFields['ezf_field_options']);
                    $options['name'] = $modelFields['ezf_field_name'];
                    $options['value'] = $modelFields['ezf_field_label'];
                    $options['model'] = $model;
                    
                    if ($disableFields){
                        $options['options']['disabled'] = true;
                    }
                    
                    if(isset($options['widgetOption'])){
                        $widgetOption = $options['widgetOption'];
                    }
                    
                    if(isset($options['specific']['group_class']) && !empty($options['specific']['group_class'])){
                        if(isset($widgetOption['class'])){
                            $widgetOption['class'] .= " {$options['specific']['group_class']} ";
                        } else {
                            $widgetOption['class'] = "{$options['specific']['group_class']} ";
                        }
                    }
                    if(isset($options['specific']['hide']) && !empty($options['specific']['hide'])){
                        if(isset($widgetOption['style'])){
                            $widgetOption['style'] .= ' display: none;';
                        } else {
                            $widgetOption['style'] = 'display: none;';
                        }
                        
                    }
                    

                    if ($modelFields['ezf_field_type'] == '57' || $modelFields['ezf_field_type'] == '899') {
                        //$html = '';
                        $options['value'] = '';
                        unset($options['specific']['icon']);
                        
                        if(isset($options['specific']['height']) && $options['specific']['height']>0){
                            if(isset($widgetOption['style'])){
                                $widgetOption['style'] .= " min-height: {$options['specific']['height']}px; ";
                            } else {
                                $widgetOption['style'] = " min-height: {$options['specific']['height']}px; ";
                            }
                        }
                        
                        //$bgformClass = 'line-bg-box';
                    }
                    
                    $bgformClass = '';
                    
                    eval("\$html = {$class}::widget(\$options);");
                    if (isset($modelFields['ezf_field_hint'])) {
                        $html .= $modelFields['ezf_field_hint'];
                    }else {
                        $bgformClass = 'line-bg-box';
                    }
                    
                }
            }

            $style_color = '';
            if ($modelFields['ezf_field_color'] != '') {
                $style_color = "background-color: {$modelFields['ezf_field_color']};";
            }

            $hide = $modelFields['ezf_field_type'] == 0 ? 'display: none;' : '';

            $query_tools_html = '';
            if($disableFields==0){
                if(isset($model) && (isset($model->rstat) && $model->rstat==2) && in_array($modelEzf->query_tools, [2,3]) && $modelFields['table_field_type']!='none' && $modelFields['table_field_type']!='field'){
                    $query_tools_html = '<a class="btn btn-warning btn-xs btn-querytool" data-url="'.\yii\helpers\Url::to(['/ezforms2/ezform-community/query-tool', 'modal'=>'modal-ezform-community', 'dataid'=>$model->id, 'object_id'=>$modelEzf->ezf_id, 'query_tool'=>1, 'field'=>$modelFields['ezf_field_name'], 'type'=>'query_tool', 'value_old'=>$model[$modelFields['ezf_field_name']]]).'" style="position: absolute; right: 15px; top: 0;"><i class="glyphicon glyphicon-send"></i> '.\Yii::t('ezform', 'Query Tool').'</a>';
                }
            }
            
            if(isset($widgetOption['class'])){
                $widgetOption['class'] .= " col-md-{$modelFields['ezf_field_lenght']} $bgformClass ";
            } else {
                $widgetOption['class'] = "col-md-{$modelFields['ezf_field_lenght']} $bgformClass ";
            }

            if(isset($widgetOption['style'])){
                $widgetOption['style'] .= " position: relative;{$hide}{$style_color} ";
            } else {
                $widgetOption['style'] = "position: relative;{$hide}{$style_color} ";
            }
            
            $widgetOption['item-id'] = $modelFields['ezf_field_id'];
                 
            if($ui){
                $widget = Html::tag('div', $query_tools_html . $html, $widgetOption);
            } else {
                $widget = $query_tools_html . $html;
            }
            
            
            //$widget = '<div class="col-md-' . $modelFields['ezf_field_lenght'] . '" item-id="' . $modelFields['ezf_field_id'] . '" style="position: relative;' . $hide . $style_color . '">' . $query_tools_html . $html . '</div>';
            return $widget;
//          return $view->renderAjax($widgets, [
//                'field_id' => $modelFields['ezf_field_id'],
//                'field_size' => $modelFields['ezf_field_lenght'],
//                'style_color' => $style_color,
//                'field_item' => $html,
//                'hide'=>$modelFields['ezf_field_type']==0?'display: none;':'',
//          ]);
        } catch (yii\base\Exception $e) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
            return '<code>' . $e->getMessage() . '</code>';
        }
    }
    
    public static function export_obj($obj) {
        $items_dump = \yii\helpers\VarDumper::export($obj);
        $items_dump = highlight_string("<?php\n" . $items_dump, true);
        $items_dump = preg_replace('/&lt;\\?php<br \\/>/', '', $items_dump, 1);
        $items_dump = str_replace("'", "\'", $items_dump);

        $value_label = '<span class="dropdown"><a style="cursor: pointer;color: #c7254e;" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="true">
                        <i class="glyphicon glyphicon-info-sign"></i>
                        </a>
                        <ul class="dropdown-menu" style="padding: 10px; max-height: 400px; overflow-y: auto;"><p>'.$items_dump.'</p></ul>
                        </span>
                        ';
        return $value_label;
    }

    public static function addProperty($obj, $key, $data) {
        foreach ($obj as $i => $value) {
            if (is_array($value)) {
                $obj[$i] = self::addProperty($value, $key, $data);
            } else {
                if ($i == $key) {
                    $obj[$key] = $data;
                }
            }
        }
        return $obj;
    }

    private static function getRuleName($rule) {
        $name = $rule[0];
        if (count($rule) > 1) {
            $name = '';
            foreach ($rule as $key => $value) {
                if (is_integer($key)) {
                    $name .= $value;
                } else {
                    if(is_array($value)){
                        $name .= $key.EzfFunc::arrayEncode2String($value);
                    } else {
                        $name .= $key.$value;
                    }
                }
            }
        }
        return $name;
    }

    public static function getCondition($ezf_id, $ezf_field_name) {
        $model = EzformCondition::find()
                ->where('ezf_id=:ezf_id AND ezf_field_name=:ezf_field_name', [':ezf_id' => $ezf_id, ':ezf_field_name' => $ezf_field_name])
                ->all();

        $dataEzf = [];
        if ($model) {
            $k = 0;
            foreach ($model as $key => $value) {
                $arr_cond_jump = \yii\helpers\Json::decode($value['cond_jump']);
                $arr_cond_require = \yii\helpers\Json::decode($value['cond_require']);

                if (is_array($arr_cond_jump)) {
                    $cond_jump = implode(',', $arr_cond_jump);
                    $var_jump = ArrayHelper::getColumn(EzfQuery::getConditionFieldsName('ezf_field_name', $cond_jump), 'ezf_field_name');
                } else {
                    $var_jump = [];
                }

                if (is_array($arr_cond_require)) {
                    $cond_require = implode(',', $arr_cond_require);
                    $var_require = ArrayHelper::getColumn(EzfQuery::getConditionFieldsName('ezf_field_name', $cond_require), 'ezf_field_name');
                } else {
                    $var_require = [];
                }

                $dataEzf[$k]['ezf_id'] = $ezf_id;
                $dataEzf[$k]['ezf_field_name'] = $value['ezf_field_name'];
                $dataEzf[$k]['ezf_field_value'] = $value['ezf_field_value'];
                $dataEzf[$k]['var_jump'] = isset($var_jump) ? $var_jump : '';
                $dataEzf[$k]['var_require'] = isset($var_require) ? $var_require : '';
                $k++;
            }
        }

        return $dataEzf;
    }

    public static function generateFieldName($ezf_id) {
        $model = EzfQuery::getEzformOne($ezf_id);
        $options = SDUtility::string2Array($model->ezf_options);
        
        $schemaEzform = ArrayHelper::getColumn(EzfQuery::showColumn($model->ezf_table), 'Field');
        
        $autoid = 1;
        if(isset($options['autoid'])){
            $autoid = $options['autoid']+1;
        } 
        
        $num = str_pad($autoid, 3, '0', STR_PAD_LEFT);
        $code = 'v' . $num;
        
        $getField = EzfQuery::getFieldByName($ezf_id, $code);
        if($getField){
            
            if(isset($options['autoid'])){
                $options['autoid'] = $options['autoid']+1;
            } else {
                $options['autoid'] = 1;
            }
            
            $model->ezf_options = SDUtility::array2String($options);
            $model->save();
            
            $code = self::generateFieldName($ezf_id);
        }
        
        if(in_array($code, $schemaEzform)){
            if(isset($options['autoid'])){
                $options['autoid'] = $options['autoid']+1;
            } else {
                $options['autoid'] = 1;
            }

            $model->ezf_options = SDUtility::array2String($options);
            $model->save();

            $code = self::generateFieldName($ezf_id);
        }
                
        return $code;
    }
    
    public static function updateAutoid($ezf_id) {
        $model = EzfQuery::getEzformOne($ezf_id);
        $options = SDUtility::string2Array($model->ezf_options);

        if(isset($options['autoid'])){
            $options['autoid'] = $options['autoid']+1;
        } else {
            $options['autoid'] = 1;
        }
        
        $model->ezf_options = SDUtility::array2String($options);
        
        return $model->save();
    }

    public static function getInputByArray($id, $input) {
        foreach ($input as $key => $value) {
            if ($value['input_id'] == $id) {
                return $value;
            }
        }
        return FALSE;
    }

    public static function mergeValidate($validate) {
        $addArry = [];
        if (isset($validate) && is_array($validate)) {
            foreach ($validate as $row => $items) {
                foreach ($items as $key => $value) {
                    $addArry[$items[0]][$key] = $value;
                }
            }
        }
        $returnArry = [];
        foreach ($addArry as $key => $value) {
            $returnArry[] = $value;
        }

        return $returnArry;
    }

    public static function generateCondition($modelTable, $field, $model, $view, $dataInput) {
        //$view = new View();
        $inputId = Html::getInputId($modelTable, $field['ezf_field_name']);
        $inputName = Html::getInputName($modelTable, $field['ezf_field_name']);
        $inputValue = Html::getAttributeValue($modelTable, $field['ezf_field_name']);

        $dataCond = EzfQuery::getCondition($field['ezf_id'], $field['ezf_field_name']);
        if ($dataCond) {
            //Edit Html
            $condition = SDUtility::string2Array($field['ezf_field_options']);

            $fieldId = $inputId;
            $dataType = 'none';
            $hideAll = 0;
             
            if ($dataInput) {
                if ($dataInput['input_function'] == 'widget') {
                    if (isset($condition['options']['data-type'])) {
                        $dataType = $condition['options']['data-type'];
                    }
                    if (isset($condition['options']['data-hide-all'])) {
                        $hideAll = $condition['options']['data-hide-all'];
                    }
                } else {

                    if (isset($condition['data-type'])) {
                        $dataType = $condition['data-type'];
                    }
                    if (isset($condition['data-hide-all'])) {
                        $hideAll = $condition['data-hide-all'];
                    }
                }
            }
            
            if ($dataType == 'select' || $dataType == 'radio') {
                $fieldId = $field['ezf_field_name'];
            }

            $enable = TRUE;
            foreach ($dataCond as $index => $cvalue) {
                //if($inputValue == $cvalue['ezf_field_value'] || $inputValue == ''){
                $dataCond[$index]['cond_jump'] = \yii\helpers\Json::decode($cvalue['cond_jump']);
                $dataCond[$index]['cond_require'] = \yii\helpers\Json::decode($cvalue['cond_require']);

                if ($dataType == 'select' || $dataType == 'radio') {
                    if ($inputValue == $cvalue['ezf_field_value'] || $inputValue == '') {
                        if ($enable) {
                            $enable = false;
                            $jumpArr = \yii\helpers\Json::decode($cvalue['cond_jump']);
                            if (is_array($jumpArr)) {
                                foreach ($jumpArr as $j => $jvalue) {
                                    $view->registerJs("
					    var fieldIdj = '" . $jvalue . "';
					    var inputIdj = '" . $fieldId . "';
					    var valueIdj = '" . $inputValue . "';
					    var fixValuej = '" . $cvalue['ezf_field_value'] . "';
					    var fTypej = '" . $dataType . "';
                                            var fHideAllj = '" . $hideAll . "';
					    domHtml(fieldIdj, inputIdj, valueIdj, fixValuej, fTypej, 'none', fHideAllj);
				    ");
                                }
                            }

                            $requireArr = \yii\helpers\Json::decode($cvalue['cond_require']);
                            if (is_array($requireArr)) {
                                foreach ($requireArr as $r => $rvalue) {
                                    $view->registerJs("
					    var fieldIdr = '" . $rvalue . "';
					    var inputIdr = '" . $fieldId . "';
					    var valueIdr = '" . $inputValue . "';
					    var fixValuer = '" . $cvalue['ezf_field_value'] . "';
					    var fTyper = '" . $dataType . "';
                                            var fHideAll = '" . $hideAll . "';
					    domHtml(fieldIdr, inputIdr, valueIdr, fixValuer, fTyper, 'block', fHideAll);
				    ");
                                }
                            }
                        }
                    }
                } else {

                    $jumpArr = \yii\helpers\Json::decode($cvalue['cond_jump']);
                    if (is_array($jumpArr)) {
                        foreach ($jumpArr as $j => $jvalue) {
                            $view->registerJs("
				    var fieldIdj = '" . $jvalue . "';
				    var inputIdj = '" . $fieldId . "';
				    var valueIdj = '" . $inputValue . "';
				    var fixValuej = '" . $cvalue['ezf_field_value'] . "';
				    var fTypej = '" . $dataType . "';
                                    var fHideAllj = '" . $hideAll . "';
				    domHtml(fieldIdj, inputIdj, valueIdj, fixValuej, fTypej, 'block', fHideAllj);
			    ");
                        }
                    }

                    $requireArr = \yii\helpers\Json::decode($cvalue['cond_require']);
                    if (is_array($requireArr)) {

                        foreach ($requireArr as $r => $rvalue) {

                            $view->registerJs("
				    var fieldIdr = '" . $rvalue . "';
				    var inputIdr = '" . $fieldId . "';
				    var valueIdr = '" . $inputValue . "';
				    var fixValuer = '" . $cvalue['ezf_field_value'] . "';
				    var fTyper = '" . $dataType . "';
                                    var fHideAll = '" . $hideAll . "';
				    domHtml(fieldIdr, inputIdr, valueIdr, fixValuer, fTyper, 'none', fHideAll);

			    ");
                        }
                    }
                }
            }
            
            //Add Event
            if ($dataType == 'select') {
                $view->registerJs("
			eventSelect('" . $inputId . "', '" . yii\helpers\Json::encode($dataCond) . "', $hideAll);
			setSelect('" . $inputId . "', '" . yii\helpers\Json::encode($dataCond) . "', $hideAll);
		    ");
            } else if ($dataType == 'radio') {
                $view->registerJs("
			eventRadio('" . $inputName . "', '" . yii\helpers\Json::encode($dataCond) . "', $hideAll);
			setRadio('" . $inputName . "', '" . yii\helpers\Json::encode($dataCond) . "', $hideAll);
		    ");
            } else {//if ($dataType == 'checkbox') {
                $view->registerJs("
                    eventCheckBox('" . $inputId . "', '" . yii\helpers\Json::encode($dataCond) . "', $hideAll);
                    setCheckBox('" . $inputId . "', '" . yii\helpers\Json::encode($dataCond) . "', $hideAll);
                ");
            }
        }
    }

    public static function itemAlias($code, $key = NULL) {
        $itemStr['reportItems'] = [
            'bar_chart' => Yii::t('ezform', 'Bar Chart'),
            'pie' => Yii::t('ezform', 'Pie'),
            'line_graph' => Yii::t('ezform', 'Line graph'),
        ];
        
        $itemStr['version_status'] = [
            '0' => Yii::t('ezform', 'Being Revised'),
            '1' => Yii::t('ezform', 'Submit for Approval'),
            '2' => Yii::t('ezform', 'Approved'),
            '3' => Yii::t('ezform', 'Retired'),
            '4' => Yii::t('ezform', 'Under Reviewing'),
        ];

        $return = $itemStr[$code];

        if (isset($key)) {
            return isset($return[$key]) ? $return[$key] : false;
        } else {
            return isset($return) ? $return : false;
        }
    }

    public static function genJs($varArry, $model, $field) {
        $jsPath = [];
        $createEvent = '';
        $inputName = Html::getInputName($model, $field['ezf_field_name']);
        try {
            foreach ($varArry as $varName) {
                $inputNameEvent = Html::getInputName($model, $varName);
                $inputIdEvent = Html::getInputId($model, $varName);

                $eventSelector = "input[name=\"$inputNameEvent\"],select[name=\"$inputNameEvent\"]";
                $jsPath['{' . $varName . '}'] = "Number(getValue('$eventSelector', '$inputIdEvent'))";

                $createEvent .= "
                    $('$eventSelector').on('change', function() {
                        autocal_{$field['ezf_field_name']}();

                    });
                ";
            }
            $inputSelector = "input[name=\"$inputName\"],select[name=\"$inputName\"]";
            $calJs = strtr($field['ezf_field_cal'], $jsPath);

            $createEvent .= "
                autocal_{$field['ezf_field_name']}();
                function autocal_{$field['ezf_field_name']}(){
                    $('$inputSelector').val($calJs);
                }
            ";
            return $createEvent;
         } catch (\Exception $e) {
             return '';
         }
        
    }
    

    public static function array2ConcatStr($fieldsArry) {
        $arry = SDUtility::string2Array($fieldsArry);
        if (is_array($arry) && !empty($arry)) {
            $concat = 'CONCAT(';
            $prefix = '';
            foreach ($arry as $fieldName) {
                $concat .= $prefix . "`$fieldName`";
                $prefix = ", ' ', ";
            }
            $concat .= ')';

            return $concat;
        }
        return false;
    }

    public static function arrayEncode2String($arry) {
        if (!empty($arry)) {
            return base64_encode(SDUtility::array2String($arry));
        }
        return '';
    }

    public static function stringDecode2Array($str) {
        if (!empty($str) && $str != '') {
            return SDUtility::string2Array(base64_decode($str));
        }
        return [];
    }

    public static function getEvenField($modelFields) {
        $arry = [];
        if (!empty($modelFields) && is_array($modelFields)) {
            foreach ($modelFields as $key => $value) {
                if ($value['ezf_target'] == 1) {
                    $arry['target'] = $value;
                } elseif ($value['ezf_special'] == 1) {
                    $arry['special'] = $value;
                }
            }
        }
        return $arry;
    }

    public static function getTargetField($modelFields) {
        $arry = [];
        if (!empty($modelFields) && is_array($modelFields)) {
            foreach ($modelFields as $key => $value) {
                if ($value['ezf_target'] == 1) {
                    $arry[] = $value;
                    return $arry;
                }
            }
        }
        return $arry;
    }

    public static function getSpecialField($modelFields) {
        $arry = [];
        if (!empty($modelFields) && is_array($modelFields)) {
            foreach ($modelFields as $key => $value) {
                if ($value['ezf_special'] == 1) {
                    $arry[] = $value;
                    return $arry;
                }
            }
        }
        return $arry;
    }

    public static function check_citizen($personID) {
//	if (strlen($pid) != 13) return false;
//        for ($i = 0, $sum = 0; $i < 12; $i++)
//            $sum += (int)($pid{$i}) * (13 - $i);
//        if ((11 - ($sum % 11)) % 10 == (int)($pid{12}))
//            return true;
//	
//        return false;
        if (isset($personID) && !empty($personID)) {
            if (strlen($personID) != 13) {
                return false;
            }
            
            if(substr($personID, 0,4)=='9999'){
                return true;
            }

            $rev = strrev($personID); // reverse string ขั้นที่ 0 เตรียมตัว
            $total = 0;
            for ($i = 1; $i < 13; $i++) { // ขั้นตอนที่ 1 - เอาเลข 12 หลักมา เขียนแยกหลักกันก่อน
                $mul = $i + 1;
                $count = $rev[$i] * $mul; // ขั้นตอนที่ 2 - เอาเลข 12 หลักนั้นมา คูณเข้ากับเลขประจำหลักของมัน
                $total = $total + $count; // ขั้นตอนที่ 3 - เอาผลคูณทั้ง 12 ตัวมา บวกกันทั้งหมด
            }
            $mod = $total % 11; //ขั้นตอนที่ 4 - เอาเลขที่ได้จากขั้นตอนที่ 3 มา mod 11 (หารเอาเศษ)
            $sub = 11 - $mod; //ขั้นตอนที่ 5 - เอา 11 ตั้ง ลบออกด้วย เลขที่ได้จากขั้นตอนที่ 4
            $check_digit = $sub % 10; //ถ้าเกิด ลบแล้วได้ออกมาเป็นเลข 2 หลัก ให้เอาเลขในหลักหน่วยมาเป็น Check Digit
            if ($rev[0] == $check_digit) {  // ตรวจสอบ ค่าที่ได้ กับ เลขตัวสุดท้ายของ บัตรประจำตัวประชาชน
                return true; /// ถ้า ตรงกัน แสดงว่าถูก
            } else {
                return false; // ไม่ตรงกันแสดงว่าผิด 
            }
        } else {
            return false;
        }
    }

    public static function checkSpecial($model, $evenFields, $targetReset=false) {
        $fieldSpecial = '';
        if (isset($evenFields['special']) && !empty($evenFields['special'])) {
            if($targetReset){
                $model[$evenFields['special']['ezf_field_name']] = '';
            }
            
            $fieldSpecial = $evenFields['special']['ezf_field_name'];
            $special = $model[$evenFields['special']['ezf_field_name']];

            //$checkcid = EzfFunc::check_citizen($special);
            $specialCheck = isset($special) && !empty($special);
            if (!$specialCheck) {
                $specialFields = [$evenFields['special']];
                return NULL;
            }
        }

        return $fieldSpecial;
    }

    public static function genBtnEzform($model, $modelEzf, $submit=1) {
        $html = '';
        if (isset($modelEzf['query_tools']) && $modelEzf['query_tools'] == 2) {
            if ($model['rstat'] != 2) {
                $html .= Html::submitButton('Save Draft', ['class' => 'btn btn-success btn-submit', 'name' => 'submit', 'value' => '1', 'data-loading-text' => 'Loading...']);
                if($submit){
                    $html .= Html::submitButton('Submit', ['class' => 'btn btn-primary btn-submit', 'name' => 'submit', 'value' => '2', 'data-loading-text' => 'Loading...']);
                }
                
            } else {
                if ((Yii::$app->user->can('administrator') || Yii::$app->user->can('adminsite'))) {
                    $html .= Html::submitButton('ReSaveDraft', ['class' => 'btn btn-warning btn-submit', 'name' => 'submit', 'value' => '1', 'data-loading-text' => 'Loading...']);
                }
            }
        } elseif (isset($modelEzf['query_tools']) && $modelEzf['query_tools'] == 3) {
            if ($model['rstat'] != 2) {
                $html .= Html::submitButton('Save Draft', ['class' => 'btn btn-success btn-submit', 'name' => 'submit', 'value' => '1', 'data-loading-text' => 'Loading...']);
                if($submit){
                    $html .= Html::submitButton('Submit', ['class' => 'btn btn-primary btn-submit', 'name' => 'submit', 'value' => '2', 'data-loading-text' => 'Loading...']);
                }
            } else {
                if ((Yii::$app->user->can('administrator') || Yii::$app->user->can('adminsite'))) {
                    $html .= Html::submitButton('ReSaveDraft', ['class' => 'btn btn-warning btn-submit', 'name' => 'submit', 'value' => '1', 'data-loading-text' => 'Loading...']);
                }
            }
        } else {
            $html .= Html::submitButton('Submit', ['class' => 'btn btn-primary btn-submit', 'name' => 'submit', 'value' => '1', 'data-loading-text' => 'Loading...']);
            
        }
        
        return $html;
    }
    
    public static function genBtnEzformPage($model, $modelEzf) {
        $html = '';
        if (isset($modelEzf['query_tools']) && $modelEzf['query_tools'] == 2) {
            if ($model['rstat'] != 2) {
                $html .= Html::submitButton('Save Draft', ['class' => 'btn btn-success btn-submit', 'name' => 'savedata', 'value' => '1', 'data-loading-text' => 'Loading...']);
                $html .= Html::submitButton('Submit', ['class' => 'btn btn-primary btn-submit', 'name' => 'savedata', 'value' => '2', 'data-loading-text' => 'Loading...']);
                
            } else {
                if ((Yii::$app->user->can('administrator') || Yii::$app->user->can('adminsite'))) {
                    $html .= Html::submitButton('ReSaveDraft', ['class' => 'btn btn-warning btn-submit', 'name' => 'savedata', 'value' => '1', 'data-loading-text' => 'Loading...']);
                }
            }
        } elseif (isset($modelEzf['query_tools']) && $modelEzf['query_tools'] == 3) {
            if ($model['rstat'] != 2) {
                $html .= Html::submitButton('Save Draft', ['class' => 'btn btn-success btn-submit', 'name' => 'savedata', 'value' => '1', 'data-loading-text' => 'Loading...']);
                $html .= Html::submitButton('Submit', ['class' => 'btn btn-primary btn-submit', 'name' => 'savedata', 'value' => '2', 'data-loading-text' => 'Loading...']);
                
            } else {
                if ((Yii::$app->user->can('administrator') || Yii::$app->user->can('adminsite'))) {
                    $html .= Html::submitButton('ReSaveDraft', ['class' => 'btn btn-warning btn-submit', 'name' => 'savedata', 'value' => '1', 'data-loading-text' => 'Loading...']);
                }
            }
        } else {
            $html .= Html::submitButton('Submit', ['class' => 'btn btn-primary btn-submit', 'name' => 'savedata', 'value' => '1', 'data-loading-text' => 'Loading...']);
            
        }
        
        return $html;
    }
    
    public static function cloneRefField($field) {
        $modelRef = EzfQuery::getFieldByName($field->ref_ezf_id, $field->ref_field_id);
        $options = \appxq\sdii\utils\SDUtility::string2Array($field->ezf_field_options);
        $disabled = 0;
        if(isset($options['config']) && $options['config']==1){
            $disabled = 1;
        }

        $field->ref_ezf_id = $modelRef->ref_ezf_id;
        $field->ref_field_id = $modelRef->ref_field_id;
        $field->ref_field_desc = $modelRef->ref_field_desc;
        $field->ref_field_search = $modelRef->ref_field_search;
        $field->ref_form = $modelRef->ref_form;
        
        $field->ezf_field_type = $modelRef->ezf_field_type;
        $field->ezf_field_data = $modelRef->ezf_field_data;
        $field->ezf_field_specific = $modelRef->ezf_field_specific;
        $field->ezf_field_options = $modelRef->ezf_field_options;
        
        $field->ezf_target = $modelRef->ezf_target;
        $field->ezf_special = $modelRef->ezf_special;
        $field->ezf_condition = $modelRef->ezf_condition;
        $field->ezf_field_cal = $modelRef->ezf_field_cal;
        
        return [
            'field'=>$field,
            'disabled'=>$disabled,
        ];
    }
    
    public static function updateDataRefField($target, $ezf_field_ref, $value) {
        $data_ref = EzfQuery::getRefFields($ezf_field_ref);
        $error = [];
        if($data_ref){
            foreach ($data_ref as $key => $ezvalue) {
                try {
                    Yii::$app->db->createCommand()->update($ezvalue['ezf_table'], [$ezvalue['ezf_field_name']=>$value], 'target=:target', [':target'=>$target])->execute();
                } catch (\yii\db\Exception $e) {
                    \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
                    $error[] = $e->getMessage();
                }
            }
        }
        
        return $error;
    }
    
    public static function addErrorLog($error) {
        //$error = new \yii\db\Exception();
        $model = new \backend\modules\ezforms2\models\SystemError();
        
        $model->id = SDUtility::getMillisecTime();
        $model->code = $error->getCode();
        $model->name = $error->getName();
        $model->message = $error->getMessage();
        $model->line = $error->getLine();
        $model->file = $error->getFile();
        $model->trace_string = $error->getTraceAsString();
        $model->created_by = Yii::$app->user->id;
        $model->created_at = new \yii\db\Expression('NOW()');
        
        $model->save();
    }
    
    public static function getLanguage(){
        $languageArry = explode('-', Yii::$app->language);
        if(isset($languageArry[0])){
            $language = $languageArry[0];
        } else {
            $language = 'en';
        }
        return $language;
    }
    
    public static function getAutoNumber($data){
        $num = str_pad($data['count'], $data['digit'], '0', STR_PAD_LEFT);
        return $data['prefix'].$num.$data['suffix'];
    }
    
    public static function exportForm($ezf_id, $modelEzf, $version, $dynamic=0, $db=null){
        if(!isset($db)){
            $db = Yii::$app->db;
        }
        
        $fileName = 'backup_ezform_'. \yii\helpers\Inflector::slug($modelEzf->ezf_name).'_'.$version->ver_code.'_'.SDUtility::getMillisecTime().'.xlsx';
        
        $schemaEzform = Ezform::getTableSchema();
        $schemaEzformVersion = EzformVersion::getTableSchema();
        $schemaEzformFields = EzformFields::getTableSchema();
        $schemaEzformChoice = EzformChoice::getTableSchema();
        $schemaEzformCondition = EzformCondition::getTableSchema();
        $schemaEzformAutonum = EzformAutonum::getTableSchema();
        
	$export = \appxq\sdii\widgets\SDExcel::export([
            'fileName'=>$fileName,
            'savePath'=> Yii::getAlias('@backend/web/print'),
            'format'=>'Xlsx',
            'asAttachment'=>false,
            'isMultipleSheet' => true,
            'models' => [
                    'Ezform' => Ezform::find()->where('ezf_id=:ezf_id', [':ezf_id'=>$ezf_id])->all($db), 
                    'EzformVersion' => EzformVersion::find()->where('ezf_id=:ezf_id AND ver_code=:v', [':ezf_id'=>$ezf_id, ':v'=>$version->ver_code])->all($db),
                    'EzformFields' => EzformFields::find()->where('ezf_id=:ezf_id AND (ezf_version=:v OR ezf_version="all")', [':ezf_id'=>$ezf_id, ':v'=>$version->ver_code])->all($db), 
                    'EzformChoice' => EzformChoice::find()->where('ezf_id=:ezf_id AND ezf_version=:v', [':ezf_id'=>$ezf_id, ':v'=>$version->ver_code])->all($db),
                    'EzformCondition' => EzformCondition::find()->where('ezf_id=:ezf_id AND ezf_version=:v', [':ezf_id'=>$ezf_id, ':v'=>$version->ver_code])->all($db),
                    'EzformAutonum' => EzformAutonum::find()->where('ezf_id=:ezf_id', [':ezf_id'=>$ezf_id])->all($db),
            ], 
            'columns' => [
                    'Ezform' => $schemaEzform->columnNames, 
                    'EzformVersion' => $schemaEzformVersion->columnNames, 
                    'EzformFields' => $schemaEzformFields->columnNames,
                    'EzformChoice' => $schemaEzformChoice->columnNames,
                    'EzformCondition' => $schemaEzformCondition->columnNames,
                    'EzformAutonum' => $schemaEzformAutonum->columnNames,
            ], 
            'headers' => [
                    'Ezform' => ArrayHelper::map($schemaEzform->columns, 'name', 'name'), 
                    'EzformVersion' => ArrayHelper::map($schemaEzformVersion->columns, 'name', 'name'), 
                    'EzformFields' => ArrayHelper::map($schemaEzformFields->columns, 'name', 'name'), 
                    'EzformChoice' => ArrayHelper::map($schemaEzformChoice->columns, 'name', 'name'), 
                    'EzformCondition' => ArrayHelper::map($schemaEzformCondition->columns, 'name', 'name'), 
                    'EzformAutonum' => ArrayHelper::map($schemaEzformAutonum->columns, 'name', 'name'), 
            ], 
        ]);
        
        return $fileName;
    }
    
    public static function importForm($fileName, $clone=0, $v=''){
        $sum = [];
        $data = \moonland\phpexcel\Excel::import($fileName, [
                    'setFirstRecordAsKeys' => true,
                    'setIndexSheetByName' => true,
                        //'getOnlySheet' => 'sheet1',
        ]);
        $modelInput = EzfQuery::getInputv2All();
        
        $version = 'v_'. SDUtility::getMillisecTime();
        if($v!=''){
            $version = $v;
        }
        
        $ezfError=1;
        $ezf_table = '';
        $ezf_id_new = SDUtility::getMillisecTime();
        $ezf_have = 0;
        
        
        if (isset($data['Ezform']) && !empty($data['Ezform'])) {

            $sum['Ezform']['all'] = 0;
            $sum['Ezform']['tsum'] = 0;
            $sum['Ezform']['fsum'] = 0;
            $sum['Ezform']['esum'] = 0;
            foreach ($data['Ezform'] as $value) {
                try {
                    if($clone){
                        $value['ezf_id'] = $ezf_id_new;
                        $value['ezf_name'] = $value['ezf_name'].'-clone';
                        $value['ezf_table'] = 'zdata_'.$ezf_id_new;
                        $value['co_dev'] = '';
                        $value['assign'] = '';
                        
                        $value['shared'] = 0;
                        $value['allowed_clone'] = 0;
                        $value['public_listview'] = 0;
                        $value['public_edit'] = 0;
                        $value['public_delete'] = 0;
                    }
                    $value['ezf_version'] = $version;
                    $value['created_by'] = '';
                    $value['created_at'] = '';
                    $value['updated_by'] = '';
                    $value['updated_at'] = '';
                    
                    $modelEzform = new Ezform();
                    $modelEzform->attributes = $value;
                    
                    $dataEzform = EzfQuery::getEzformById($value['ezf_id']);
                    if($dataEzform){
                        $clone = 1;
                        $ezf_have = 1;
                        $ezf_table = $modelEzform->ezf_table;
                        $ezf_id_new = $value['ezf_id'];
                    } else {
                        $sum['Ezform']['all']++;

                        if ($modelEzform->save()) {
                            $ezf_table = $modelEzform->ezf_table;
                            EzfForm::createZdata($modelEzform->ezf_table);
                            $sum['Ezform']['tsum'] ++;
                        } else {
                            $sum['Ezform']['fsum'] ++;
                        }
                    }
                    $sum['Ezform']['ezf_id'] = $ezf_id_new;
                } catch (\yii\db\Exception $e) {
                    $sum['Ezform']['esum'] ++;
                    EzfFunc::addErrorLog($e);
                }
            }
            $ezfError = $sum['Ezform']['esum']+$sum['Ezform']['fsum'];
        }
        
        //version
        if (isset($data['EzformVersion']) && !empty($data['EzformVersion'])) {
            $sum['Version']['all'] = 0;
            $sum['Version']['tsum'] = 0;
            $sum['Version']['fsum'] = 0;
            $sum['Version']['esum'] = 0;
            foreach ($data['EzformVersion'] as $value) {
                try {
                    $value['ver_for'] = $value['ver_code'];
                    $value['ver_code'] = $version;
                    $value['ver_approved'] = 0;
                    $value['ver_active'] = 0;
                    $value['approved_by'] = '';
                    $value['approved_date'] = '';
                    $value['created_by'] = '';
                    $value['created_at'] = '';
                    $value['updated_by'] = '';
                    $value['updated_at'] = '';

                    if($clone){
                        $value['ezf_id'] = $ezf_id_new;
                    }
                    
                    $sum['Version']['all']++;
                    $modelVersion = new EzformVersion();
                    $modelVersion->attributes = $value;
                    
                    if ($modelVersion->save()) {
                        $sum['Version']['tsum'] ++;
                    } else {
                        $sum['Version']['fsum'] ++;
                    }
                    
                } catch (\yii\db\Exception $e) {
                    $sum['Version']['esum'] ++;
                    EzfFunc::addErrorLog($e);
                }
            }
        }

        $ezf_field_obj = [];
        if (isset($data['EzformFields']) && !empty($data['EzformFields']) && $ezfError==0 && $clone) {
            foreach ($data['EzformFields'] as $value) {
                $ezf_field_id_new = SDUtility::getMillisecTime();
                $ezf_field_obj[$value['ezf_field_id']] = $ezf_field_id_new;
            }
        }

        $ezf_choice_obj = [];
        if (isset($data['EzformChoice']) && !empty($data['EzformChoice']) && $ezfError==0 && $clone) {
            foreach ($data['EzformChoice'] as $value) {
                $ezf_choice_id_new = SDUtility::getMillisecTime();
                $ezf_choice_obj[$value['ezf_choice_id']] = $ezf_choice_id_new;
            }
        }
        
        $ezf_autonum_obj = [];
        if (isset($data['EzformAutonum']) && !empty($data['EzformAutonum']) && $ezfError==0 && $clone) {
            foreach ($data['EzformAutonum'] as $value) {
                $ezf_autonum_id_new = SDUtility::getMillisecTime();
                $ezf_autonum_obj[$value['id']] = $ezf_autonum_id_new;
            }
        }
        
        if (isset($data['EzformFields']) && !empty($data['EzformFields']) && $ezfError==0) {

            $sum['Fields']['all'] = 0;
            $sum['Fields']['tsum'] = 0;
            $sum['Fields']['fsum'] = 0;
            $sum['Fields']['esum'] = 0;
            foreach ($data['EzformFields'] as $value) {
                try {
                    if($value['ezf_version']=='all' && $ezf_have==1){
                        
                    } else {
                        if($clone){
                            $value['ezf_field_id'] = $ezf_field_obj[$value['ezf_field_id']];
                            $value['ezf_id'] = $ezf_id_new;

                            $field_data = SDUtility::string2Array($value['ezf_field_data']);
                            if(!empty($field_data)){
                                $new_data = [];
                                if(isset($field_data['builder'])){
                                    foreach ($field_data['builder'] as $key_builder => $value_builder) {
                                        if(isset($value_builder['fields'])){
                                            foreach ($value_builder['fields'] as $key_fields => $value_fields) {
                                                $fitem_new=[];
                                                if(isset($value_fields['data'])){
                                                    foreach ($value_fields['data'] as $key_fitem => $value_fitem) {
                                                        $choice_id = isset($ezf_choice_obj[$key_fitem])?$ezf_choice_obj[$key_fitem]:$key_fitem;
                                                        $fitem_new[$choice_id] = $value_fitem;
                                                    }
                                                    $value_fields['data'] = $fitem_new;
                                                }
                                                $value_fields['id'] = isset($ezf_field_obj[$value_fields['id']])?$ezf_field_obj[$value_fields['id']]:$value_fields['id'];
                                                $value_builder['fields'][$key_fields] = $value_fields;
                                            }

                                            $new_data['builder'][$key_builder]['fields'] = $value_builder['fields'];

                                            if(isset($value_builder['other'])){
                                                $value_builder['other']['id'] = isset($ezf_field_obj[$value_builder['other']['id']])?$ezf_field_obj[$value_builder['other']['id']]:$value_builder['other']['id'];

                                                $new_data['builder'][$key_builder]['other'] = $value_builder['other'];
                                            }
                                        } else {
                                            if(isset($value_builder['other'])){
                                                $value_builder['other']['id'] = isset($ezf_field_obj[$value_builder['other']['id']])?$ezf_field_obj[$value_builder['other']['id']]:$value_builder['other']['id'];
                                            }
                                            $choice_id = isset($ezf_choice_obj[$key_builder])?$ezf_choice_obj[$key_builder]:$key_builder;

                                            $new_data['builder'][$choice_id] = $value_builder;
                                        }
                                    }
                                }

                                if(isset($field_data['items'])){
                                    if(isset($field_data['items']['other'])){
                                        foreach ($field_data['items']['other'] as $key_other => $value_other) {
                                            $value_other['id'] = isset($ezf_field_obj[$value_other['id']])?$ezf_field_obj[$value_other['id']]:$value_other['id'];
                                            $field_data['items']['other'][$key_other] = $value_other;
                                        }
                                    }
                                    $new_data['items'] = $field_data['items'];
                                }

                                if(isset($field_data['fields'])){
                                    foreach ($field_data['fields'] as $key_fields => $value_fields) {
                                        $fitem_new=[];
                                        if(isset($value_fields['data'])){
                                            foreach ($value_fields['data'] as $key_fitem => $value_fitem) {
                                                $fitem_new[$ezf_choice_obj[$key_fitem]] = $value_fitem;
                                            }
                                            $value_fields['data'] = $fitem_new;
                                        }
                                        $value_fields['id'] = isset($ezf_field_obj[$value_fields['id']])?$ezf_field_obj[$value_fields['id']]:$value_fields['id'];

                                        if(isset($value_fields['other'])){
                                            $value_fields['other']['id'] = isset($ezf_field_obj[$value_fields['other']['id']])?$ezf_field_obj[$value_fields['other']['id']]:$value_fields['other']['id'];
                                        }

                                        $field_data['fields'][$key_fields] = $value_fields;
                                    }

                                    $new_data['fields'] = $field_data['fields'];
                                }

                                $value['ezf_field_data'] = SDUtility::array2String($new_data);
                            }

                        }
                        $value['ezf_version'] = ($value['ezf_version']=='all')?$value['ezf_version']:$version;

                        $value['created_by'] = '';
                        $value['created_at'] = '';
                        $value['updated_by'] = '';
                        $value['updated_at'] = '';

                        $dataInput = EzfFunc::getInputByArray($value['ezf_field_type'], $modelInput);

                        $new_fields = EzfUiFunc::getDefaultFields($dataInput, $value);
                        $value = ArrayHelper::merge($value, $new_fields);

                        $new_options = EzfUiFunc::getDefaultOptions($dataInput, $value);
                        $old_options = SDUtility::string2Array($value['ezf_field_options']);
                        $value['ezf_field_options'] = SDUtility::array2String(ArrayHelper::merge($old_options, $new_options));

                        $sum['Fields']['all']++;
                        $modelEzformFields = new EzformFields();
                        $modelEzformFields->attributes = $value;
                        if ($modelEzformFields->save()) {
                            if(!in_array($modelEzformFields->ezf_field_name, ['id', 'ptid', 'xsourcex', 'xdepartmentx', 'rstat', 'sitecode', 'ptcode', 'ptcodefull', 'hptcode', 'hsitecode', 'user_create', 'create_date', 'user_update', 'update_date', 'target', 'error', 'sys_lat', 'sys_lng'])){
                                if($modelEzformFields->table_field_type!='none' && $modelEzformFields->table_field_type!='field'){
                                    \backend\modules\ezbuilder\classes\EzBuilderFunc::alterTableAdd($ezf_table, $modelEzformFields->ezf_field_name, $modelEzformFields->table_field_type, $modelEzformFields->table_field_length, $modelEzformFields->table_index);
                                }
                            }
                            $sum['Fields']['tsum'] ++;
                        } else {
                            $sum['Fields']['fsum'] ++;
                        }
                    }
                } catch (\yii\db\Exception $e) {
                    $sum['Fields']['esum'] ++;
                    EzfFunc::addErrorLog($e);
                }
            }
        }

        
        
        if (isset($data['EzformChoice']) && !empty($data['EzformChoice']) && $ezfError==0) {

            $sum['Choice']['all'] = 0;
            $sum['Choice']['tsum'] = 0;
            $sum['Choice']['fsum'] = 0;
            $sum['Choice']['esum'] = 0;
            foreach ($data['EzformChoice'] as $value) {
                try {
                    if($clone){
                        $value['ezf_choice_id'] = $ezf_choice_obj[$value['ezf_choice_id']];
                        $value['ezf_field_id'] = isset($ezf_field_obj[$value['ezf_field_id']])?$ezf_field_obj[$value['ezf_field_id']]:$value['ezf_field_id'];
                        $value['ezf_id'] = $ezf_id_new;
                        $value['ezf_choiceetc'] = isset($ezf_field_obj[$value['ezf_choiceetc']])?$ezf_field_obj[$value['ezf_choiceetc']]:$value['ezf_choiceetc'];
                    }
                    $value['ezf_version'] = $version;
                    
                    $sum['Choice']['all']++;
                    $modelEzformChoice = new EzformChoice();
                    $modelEzformChoice->attributes = $value;
                    if ($modelEzformChoice->save()) {
                        $sum['Choice']['tsum'] ++;
                    } else {
                        $sum['Choice']['fsum'] ++;
                    }
                } catch (\yii\db\Exception $e) {
                    $sum['Choice']['esum'] ++;
                    EzfFunc::addErrorLog($e);
                }
            }
        }

        if (isset($data['EzformCondition']) && !empty($data['EzformCondition']) && $ezfError==0) {

            $sum['Condition']['all'] = 0;
            $sum['Condition']['tsum'] = 0;
            $sum['Condition']['fsum'] = 0;
            $sum['Condition']['esum'] = 0;
            foreach ($data['EzformCondition'] as $value) {
                try {
                    if($clone){
                        $value['ezf_id'] = $ezf_id_new;
                        $cond_jump = SDUtility::string2Array($value['cond_jump']);
                        if(!empty($cond_jump)){
                            foreach ($cond_jump as $key_jump => $value_jump) {
                                $cond_jump[$key_jump] = isset($ezf_field_obj[$value_jump])?$ezf_field_obj[$value_jump]:$value_jump;
                            }
                        }
                        $cond_require = SDUtility::string2Array($value['cond_require']);
                        if(!empty($cond_require)){
                            foreach ($cond_require as $key_require => $value_require) {
                                $cond_require[$key_require] = isset($ezf_field_obj[$value_require])?$ezf_field_obj[$value_require]:$value_require;
                            }
                        }
                        
                        $value['cond_jump'] = SDUtility::array2String($cond_jump);
                        $value['cond_require'] = SDUtility::array2String($cond_require);
                    }
                    $value['ezf_version'] = $version;
                    
                    $sum['Condition']['all']++;
                    $modelEzformCondition = new EzformCondition();
                    $modelEzformCondition->attributes = $value;
                    if ($modelEzformCondition->save()) {
                        $sum['Condition']['tsum'] ++;
                    } else {
                        $sum['Condition']['fsum'] ++;
                    }
                } catch (\yii\db\Exception $e) {
                    $sum['Condition']['esum'] ++;
                    EzfFunc::addErrorLog($e);
                }
            }
        }
        
        if (isset($data['EzformAutonum']) && !empty($data['EzformAutonum']) && $ezfError==0) {

            $sum['Autonum']['all'] = 0;
            $sum['Autonum']['tsum'] = 0;
            $sum['Autonum']['fsum'] = 0;
            $sum['Autonum']['esum'] = 0;
            foreach ($data['EzformAutonum'] as $value) {
                try {
                    if($clone){
                        $value['id'] = $ezf_autonum_obj[$value['id']];
                        $value['ezf_field_id'] = isset($ezf_field_obj[$value['ezf_field_id']])?$ezf_field_obj[$value['ezf_field_id']]:$value['ezf_field_id'];
                        $value['ezf_id'] = $ezf_id_new;
                    }
                    
                    $sum['Autonum']['all']++;
                    $modelEzformAutonum = new EzformAutonum();
                    $modelEzformAutonum->attributes = $value;
                    if ($modelEzformAutonum->save()) {
                        $sum['Autonum']['tsum'] ++;
                    } else {
                        $sum['Autonum']['fsum'] ++;
                    }
                } catch (\yii\db\Exception $e) {
                    $sum['Autonum']['esum'] ++;
                    EzfFunc::addErrorLog($e);
                }
            }
        }
        return $sum;
    }
    
    public static function deleteQueueLog($model, $ezf_id){
//        $userProfile = Yii::$app->user->identity->profile;
//        $dept = (isset($userProfile->department) && !empty($userProfile->department)) ? $userProfile->department : '';
        
        $modelQueue = \backend\modules\ezforms2\models\QueueLog::find()->where('dataid=:dataid AND ezf_id=:ezf_id', [':dataid'=>$model->id, ':ezf_id'=>$ezf_id])->all();
        if($modelQueue){
            foreach ($modelQueue as $key => $value) {
                try {
                    $model = \backend\modules\ezforms2\models\QueueLog::findOne($value['id']);
                    if($model){
                        $model->enable = 0;
                        $model->save();
                    }
                } catch (\yii\db\Exception $e) {
                    EzfFunc::addErrorLog($e);
                }
            }
        }
    }
    
    public static function addQueueLog($model, $ezf_id, $ezf_table){
        if(Yii::$app->user->isGuest){
            return false;
        }
        try {
            $userProfile = Yii::$app->user->identity->profile;
            $dept = (isset($userProfile->department) && !empty($userProfile->department)) ? $userProfile->department : '';
            $params = EzfFunc::array2PathTemplate($model->attributes);
            $params['{xsourcex}'] = isset($userProfile->sitecode)?$userProfile->sitecode:'';
            $params['{xdepartmentx}'] = $dept;
            //เช็คว่ามีการสร้างแล้วหรือยัง
            $modelQueue = \backend\modules\ezforms2\models\QueueLog::find()->where('`enable` = 1 AND dataid=:dataid AND ezf_id=:ezf_id AND current_unit=:current_unit', [':dataid'=>$model->id, ':ezf_id'=>$ezf_id, ':current_unit'=>$dept])->all();
            if($modelQueue){
                foreach ($modelQueue as $key_log => $value_log) {
                    //\appxq\sdii\utils\VarDumper::dump($value_log,1,0);
                    $sql = "SELECT *
                    FROM zdata_working_unit_setting
                    WHERE id = :id AND rstat<>3
                    ";
                    $dataSetting = Yii::$app->db->createCommand($sql, [':id'=>$value_log['setting_id']])->queryOne();
                    if($dataSetting){
                        //conditions
                        $cond = SDUtility::string2Array($dataSetting['process_cond']);
                        if(isset($cond) && !empty($cond)){
                            $str_cond = '';
                            $str_comma = '';
                            foreach ($cond as $key_cond => $value_cond) {
                                if(isset($value_cond['field']) && $value_cond['field']!=''){
                                    $str_cond = "$str_comma {$value_cond['bracket1']} '{$model[$value_cond['field']]}' {$value_cond['cond']} '{$value_cond['value1']}' {$value_cond['bracket2']}";
                                    $str_comma = $value_cond['with'];
                                }
                            }
                            
                            $str_cond = strtr($str_cond, $params);
                            $process_cond = FALSE;
                            if($str_cond!=''){
                                try { 
                                    eval("\$process_cond = $str_cond;");
                                } catch (\yii\base\Exception $e) {
                                    \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
                                    $process_cond = FALSE;
                                }
                            }
                            
                            $modelQueue = \backend\modules\ezforms2\models\QueueLog::find()->where('ezf_id=:ezf_id AND dataid=:dataid AND current_unit=:unit_code', [':ezf_id'=>$ezf_id, ':dataid'=>$model->id, ':unit_code'=>$dept])->one();
                            if($modelQueue){
                                $modelQueue->enable = $process_cond?1:0;
                                $modelQueue->save();
                            }
                        }
                        
                    }
                }
                
            } else {
                //รับ
                //เช็คว่ามีการตั่งค่าให้สร้างคิวหรือไม่
                $sql = "SELECT *
                    FROM zdata_working_unit_setting
                    WHERE in_ezf_id = :ezf_id AND unit_code=:unit_code
                    ";
                $dataSetting_r = Yii::$app->db->createCommand($sql, [':ezf_id'=>$ezf_id, ':unit_code'=>$dept])->queryAll();
                if(isset($dataSetting_r) && !empty($dataSetting_r)){
                    foreach ($dataSetting_r as $key => $value) {
                        //conditions
                        $cond = SDUtility::string2Array($value['process_cond']);
                        if(isset($cond) && !empty($cond)){
                            $str_cond = '';
                            $str_comma = '';
                            foreach ($cond as $key_cond => $value_cond) {
                                if(isset($value_cond['field']) && $value_cond['field']!=''){
                                    $str_cond = "$str_comma {$value_cond['bracket1']} '{$model[$value_cond['field']]}' {$value_cond['cond']} '{$value_cond['value1']}' {$value_cond['bracket2']}";
                                    $str_comma = $value_cond['with'];
                                }
                            }
                            
                            $str_cond = strtr($str_cond, $params);
                            $process_cond = FALSE;
                            if($str_cond!=''){
                                try { 
                                    eval("\$process_cond = $str_cond;");
                                } catch (\yii\base\Exception $e) {
                                    \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
                                    $process_cond = FALSE;
                                }
                            }
                            
                            if(!$process_cond){
                                $modelQueue = \backend\modules\ezforms2\models\QueueLog::find()->where('ezf_id=:ezf_id AND dataid=:dataid AND current_unit=:unit_code', [':ezf_id'=>$ezf_id, ':dataid'=>$model->id, ':unit_code'=>$dept])->one();
                                if($modelQueue){
                                    $modelQueue->enable = 0;
                                    $modelQueue->save();
                                }
                                continue;
                            } 
                        }
                        
                        //สร้าง queue log
                        $modelQueue = \backend\modules\ezforms2\models\QueueLog::find()->where('ezf_id=:ezf_id AND dataid=:dataid AND current_unit=:unit_code', [':ezf_id'=>$ezf_id, ':dataid'=>$model->id, ':unit_code'=>$dept])->one();
                        if($modelQueue){
                            $modelQueue->tab_name = $value['tab_name'];
                            $modelQueue->current_unit = $dept;
                            $modelQueue->enable = 1;
                            $modelQueue->unit = $value['unit_target'];
                        } else {
                            $modelQueue = new \backend\modules\ezforms2\models\QueueLog();
                            $modelQueue->id = SDUtility::getMillisecTime();
                            $modelQueue->tab_name = $value['tab_name'];
                            $modelQueue->current_unit = $dept;//ส่ง
                            $modelQueue->unit = $value['unit_target'];//รับ
                            $modelQueue->ezf_id = $ezf_id;
                            $modelQueue->dataid = $model->id;
                            $modelQueue->status = 'in_comming';
                            $modelQueue->type = 'receive';
                            $modelQueue->enable = 1;
                            $modelQueue->setting_id = $value['id'];
                            $modelQueue->module_id = isset($value['action_ezm_id'])?$value['action_ezm_id']:0;
                        }
                        
                        $modelQueue->save();
                        
                    }
                }
            }
        } catch (\yii\db\Exception $e) {
            EzfFunc::addErrorLog($e);
        }
        
    }
    
    public static function completeProcess($model, $ezf_id, $ezf_table){
        if(Yii::$app->user->isGuest){
            return false;
        }
        try {
            $userProfile = Yii::$app->user->identity->profile;
            $dept = (isset($userProfile->department) && !empty($userProfile->department)) ? $userProfile->department : '';
            $modelTarget = EzfQuery::getTargetOne($ezf_id);
            $ezf_id_target = $ezf_id;
            if($modelTarget){
                $ezf_id_target = $modelTarget->ref_ezf_id;
            }
            
            $modelQueue = \backend\modules\ezforms2\models\QueueLog::find()->where('`enable` = 1 AND `status` = "process" AND dataid=:dataid AND ezf_id=:ezf_id AND unit=:unit', [':dataid'=>$model->target, ':ezf_id'=>$ezf_id_target, ':unit'=>$dept])->one();
            if($modelQueue){
                $dataid_receive = SDUtility::string2Array($modelQueue->dataid_receive);
                $dataid_receive[$ezf_id] = $model->id;
                $completed = FALSE;
                
                if($modelQueue->type = 'receive'){//รับ unit setting
                    $sql = "SELECT *
                        FROM zdata_working_unit_setting
                        WHERE id = :id AND rstat<>3
                        ";
                    $dataSetting_s = Yii::$app->db->createCommand($sql, [':id'=>$modelQueue->setting_id])->queryOne();
                    if($dataSetting_s){
                        $process_forms = SDUtility::string2Array($dataSetting_s['process_forms']);
                        $complete_cond = $dataSetting_s['complete_cond'];
                        
                        if($complete_cond == 1){
                             
                            foreach ($process_forms as $form_id) {
                                if(isset($dataid_receive[$form_id]) && !empty($dataid_receive[$form_id])){
                                    $completed = TRUE;
                                } else {
                                    $modelQueue->dataid_receive = SDUtility::array2String($dataid_receive);
                                    $modelQueue->save();
                                    return false;
                                }
                            }
                        } elseif($complete_cond == 2) {
                            $complete_form = SDUtility::string2Array($dataSetting_s['complete_form']);
                            foreach ($complete_form as $form_id) {
                                if(isset($dataid_receive[$form_id]) && !empty($dataid_receive[$form_id])){
                                    $completed = TRUE;
                                } else {
                                    $modelQueue->dataid_receive = SDUtility::array2String($dataid_receive);
                                    $modelQueue->save();
                                    return false;
                                }
                            }
                        }
                    }
                } else {//ส่ง ezinput
                    $modelField = EzfQuery::getFieldById($modelQueue->setting_id);
                    if($modelField){
                        $options = SDUtility::string2Array($modelField['ezf_field_options']);
                        $process_forms = isset($options['process_form'])?$options['process_form']:[];
                        foreach ($process_forms as $form_id) {
                            if(isset($dataid_receive[$form_id]) && !empty($dataid_receive[$form_id])){
                                $completed = TRUE;
                            } else {
                                $modelQueue->dataid_receive = SDUtility::array2String($dataid_receive);
                                $modelQueue->save();
                                return false;
                            }
                        }
                    }
                }
                //complete_cond
                if($completed){
                    $modelQueue->status = 'completed';
                    $modelQueue->dataid_receive = SDUtility::array2String($dataid_receive);
                    $modelQueue->save();
                }
                
            }
        } catch (\yii\db\Exception $e) {
            EzfFunc::addErrorLog($e);
        }
    }
    
    public static function inProcess($model, $ezf_id, $ezf_table){
        try {
            $userProfile = Yii::$app->user->identity->profile;
            $dept = (isset($userProfile->department) && !empty($userProfile->department)) ? $userProfile->department : '';

            $modelQueue = \backend\modules\ezforms2\models\QueueLog::find()->where('`enable` = 1 AND `status` = "in_comming" AND dataid=:dataid AND ezf_id=:ezf_id AND unit=:unit', [':dataid'=>$model->target, ':ezf_id'=>$ezf_id, ':unit'=>$dept])->one();
            if($modelQueue){
                $modelQueue->user_receive = Yii::$app->user->id;
                $modelQueue->time_receive = new \yii\db\Expression('NOW()');
                $modelQueue->status = 'process';
                
                $modelQueue->save();
            }
        } catch (\yii\db\Exception $e) {
            EzfFunc::addErrorLog($e);
        }
    }
    
    public static function renderBtnWork($data){
        $html = '';
        
        if(isset($data) && !empty($data)){
            $li = '';
            $unit = '';
            $unitName = '';
            foreach ($data as $key => $value) {//tab_name
                if($key==0){
                    $unit = $value['unit'];
                    $unitName = $value['unit_name'];
                }
                //$html .= EzfHelper::btn($value['in_ezf_id'])->label('<i class="glyphicon glyphicon-plus"></i> '.$value['tab_name'])->buildBtnAdd().' ';
                $li .= "<li><a class=\"ezform-main-open\" data-modal=\"modal-ezform-main\" data-url=\"/ezforms2/ezform-data/ezform?ezf_id={$value['in_ezf_id']}&amp;modal=modal-ezform-main&amp;reloadDiv=&amp;initdata=&amp;target=\">{$value['tab_name']}</a></li>";
                
                //if($value['unit']!=$unit){
                    $unit = $value['unit'];
                    $unitName = $value['unit_name'];
                    $html = '<div class="btn-group">
                        <button type="button" class="btn btn-success dropdown-toggle " data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                           <i class="glyphicon glyphicon-plus"></i> '.$unitName.' <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                          '.$li.'
                        </ul>
                      </div> ';
                    
                    $li = '';
                    
                //}
            }
            
//            if($value['event_type']==2){
//                $unitName = 'My Forms';
//            }
//            
//            $html = '<div class="btn-group">
//                        <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
//                           <i class="glyphicon glyphicon-plus"></i> '.$unitName.' <span class="caret"></span>
//                        </button>
//                        <ul class="dropdown-menu">
//                          '.$li.'
//                        </ul>
//                      </div> ';
            
        }
        
        return $html;
    }
    
    public static function getFieldsGroup($modelFields, $version) {
        $fieldsGroup = [];
        $fieldsArry = [];
        foreach ($modelFields as $key => $value) {
            if(isset($fieldsArry[$value['ezf_field_name']]) && $value['ezf_field_name']==$version){
                $fieldsArry[$value['ezf_field_name']] = $value['ezf_field_label'];
            } else {
                $fieldsArry[$value['ezf_field_name']] = $value['ezf_field_label'];
            }
        }
        foreach ($modelFields as $key => $value) {
            if(isset($fieldsArry[$value['ezf_field_name']])){
                $fieldsGroup[] = $value;
            }
        }
        return $fieldsGroup;
    }
    
    public static function updateDoubleData($modelEzf, $dataid) {
        try{
            $table1 = $modelEzf->ezf_table;
            $table2 = $table1.'_db2';
            $checkTable = EzfQuery::showTable($table2);

            if($checkTable){//มีตาราง
                //check columns
                $tb_col1 = EzfQuery::showColumn($table1);
                $tb_col2 = EzfQuery::showColumn($table2);
                //$arry_col2 = ArrayHelper::getColumn($tb_col2, 'Field');
                
                foreach ($tb_col1 as $key => $value) {
                    $add = TRUE;
                    foreach ($tb_col2 as $key2 => $value2) {
                        if($value['Field'] == $value2['Field']){
                            if($value['Type'] != $value2['Type']){//check type
                                //update col
                                $sql = "ALTER TABLE `$table2` CHANGE COLUMN `{$value['Field']}` `{$value2['Field']}` {$value['Type']} DEFAULT NULL";
                                Yii::$app->db->createCommand($sql)->execute();
                            } 
                            $add = FALSE;
                        }
                    }
                    if($add){//ไม่มีให้เพิ่มฟิลด์
                        $strIndex = '';
                        
                        if (!empty($value['Key'])) {
                            $strIndex = ", ADD INDEX (`$column`)";
                        }
                        $type = "{$value['Type']} NULL DEFAULT NULL $strIndex";
                        
                        Yii::$app->db->createCommand()->addColumn($table2, $value['Field'], $type)->execute();
                    }
                }
            } else {//ไม่มีตาราง
                EzfQuery::copyTable($table2, $table1);
            }
            //check data
            if($dataid!=''){
                $data2 = EzfUiFunc::loadTbData($table2, $dataid);
                if($data2){
                    return $table2;
                } else {
                    $data1 = EzfUiFunc::loadTbData($table1, $dataid);
                    $userid = Yii::$app->user->id;
                    $userProfile = Yii::$app->user->identity->profile;
                    
                    if($data1){
                        $modelSystem = [
                            'id' => $data1['id'],
                            'ptid' => $data1['ptid'],
                            'sitecode' => $data1['sitecode'],
                            'ptcode' => $data1['ptcode'],
                            'ptcodefull' => $data1['ptcodefull'],
                            'target' => $data1['target'],
                            'sys_lat' => $data1['sys_lat'],
                            'sys_lng' => $data1['sys_lng'],
                            'hptcode' => $data1['hptcode'],
                            'hsitecode' => $data1['hsitecode'],
                            'xsourcex' => $userProfile['sitecode'],
                            'xdepartmentx' => $userProfile['department'],
                            'error' => $data1['error'],
                            'ezf_version' => $data1['ezf_version'],
                            'rstat' => '0',
                            'user_update' => $userid,
                            'update_date' => new \yii\db\Expression('NOW()'),
                            'user_create' => $userid,
                            'create_date' => new \yii\db\Expression('NOW()'),
                        ];

                        $evenFields = EzfQuery::getEventFields($modelEzf->ezf_id);
                        if (isset($evenFields) && !empty($evenFields)) {
                            foreach ($evenFields as $key_target => $value_target) {
                                $modelSystem[$value_target['ezf_field_name']] = $data1[$value_target['ezf_field_name']];
                                
                                $refForm = \appxq\sdii\utils\SDUtility::string2Array($value_target['ref_form']);//ยังไม่เทส
                                if(!empty($refForm)){
                                    foreach ($refForm as $key_ref => $value_ref) {
                                        $nameArry = explode('_', $value_ref);
                                        if ($nameArry[0] == 'my' || $nameArry[0] == 'get') {
                                            $modelSystem[$value_ref] = $data1[$value_ref];
                                        }
                                    }
                                }
                            }
                        }
                        
                        $r = Yii::$app->db->createCommand()->insert($table2, $modelSystem)->execute();
                        
                        return $table2;
                    } else {
                        return FALSE;
                    }
                }
                
            } else {
                return $table2;
            }
            
        } catch (\yii\db\Exception $e) {
            EzfFunc::addErrorLog($e);
            return FALSE;
        }
        
    }
    
    public static function excel2Array($fileName){
        $data = \moonland\phpexcel\Excel::import($fileName, [
                    'setFirstRecordAsKeys' => true,
                    'setIndexSheetByName' => true,
                        //'getOnlySheet' => 'sheet1',
        ]);

        return $data;
    }
    
    public static function slugVariable($string, $replacement = '_', $lowercase = true)
    {
        $string = \yii\helpers\Inflector::transliterate($string);
        $string = preg_replace('/[^a-zA-Z0-9=\s—–-]+/u', '_', $string);
        $string = preg_replace('/[=\s—–-]+/u', $replacement, $string);
        $string = trim($string, $replacement);

        return $lowercase ? strtolower($string) : $string;
    }
    
    public static function hideInput($form, $model, $modelField, $v, $type=1) {
        $view = Yii::$app->getView();
        
        return $view->renderAjax('//../modules/ezforms2/views/widgets/_hide_input', [
            'form' => $form,
            'model' => $model,
            'modelField' => $modelField,
            'type' => $type,
            'v' => $v,
        ]);
    }
    
    public static function apiResponse($url, $token, $userId){
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => $url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_HTTPHEADER => array(
            "application: linebot",
            "authorization: Bearer $token",
            "cache-control: no-cache",
            "device_id: $userId",
            "platform: mobile",
            "version: 1"
          ),
        ));
        
        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return [];
        } else {
            $response_array = json_decode($response, TRUE);
            if($response_array['success']){
                return $response_array['data'];
            } else {
                return [];
            }
        }
    }
    
    public static function array2PathTemplate($array)
    {
        $path = [];
        if(isset($array) && !empty($array)){
            foreach ($array as $key => $value) {
                $path["{{$key}}"] = $value;
            }
        }
        
        return $path;
    }
    
    public static function array2valueStr($array, $tag='')
    {
        $result_data = [];
        foreach ($array as $key => $value) {
            $result_data[$key] = "{$tag}{$value}{$tag}";
        }
        return $result_data;
    }
    
    public static function resultDataAjax($obj) {
        $result_data = [];//int to string support id for JS
        foreach ($obj as $key => $value) {
            $result_data[$key] = "{$value}";
        }
        return $result_data;
    }
}
