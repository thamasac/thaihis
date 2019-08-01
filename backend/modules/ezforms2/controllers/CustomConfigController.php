<?php

namespace backend\modules\ezforms2\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use appxq\sdii\helpers\SDHtml;
use backend\modules\ezforms2\models\EzformFields;
use backend\modules\ezforms2\classes\EzfFunc;
use backend\modules\ezforms2\classes\EzfQuery;
use yii\helpers\Url;
use yii\helpers\Html;
use backend\modules\ezforms2\classes\EzfUiFunc;
use appxq\sdii\utils\SDUtility;


/**
 * CustomConfigController implements the CRUD actions for EzformInput model.
 */
class CustomConfigController extends Controller
{
    
    public function actionGetWidget()
    {
	if (Yii::$app->getRequest()->isAjax) {
	    $widget = isset($_POST['widget'])?$_POST['widget']:'';
            $ezf_id = isset($_POST['ezf_id'])?$_POST['ezf_id']:0;
            $ezf_field_name = isset($_POST['ezf_field_name'])?$_POST['ezf_field_name']:0;
	    $depend = isset($_POST['depend'])?$_POST['depend']:'';
            
	    return $this->renderAjax($widget, [
                'ezf_id' => $ezf_id,
                'ezf_field_name' => $ezf_field_name,
                'options'=>[],
                'depend' => $depend,
	    ]);
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }
    
    public function actionGetVariable()
    {
	if (Yii::$app->getRequest()->isAjax) {
            $sql_id = isset($_POST['sql_id'])?$_POST['sql_id']:0;
            $html = '';
            $model = EzfUiFunc::modelSqlBuilder($sql_id);
            if($model){
                $sql_builder = SDUtility::string2Array($model->sql_builder);
                
                if(isset($sql_builder['variable']) && !empty($sql_builder['variable'])){
                    foreach ($sql_builder['variable'] as $key => $value) {
                        $html .= "<span data-content=\"{{$value}}\" class=\"btn btn-xs btn-success btn-content\" style=\"margin-top: 5px;\">{{$value}}</span> ";
                    }
                }
            }
            
            return $html;
            //<span data-content="{title}" class="btn btn-xs btn-warning btn-content">{title}</span>
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }
    
    public function actionGetFields()
    {
	if (Yii::$app->getRequest()->isAjax) {
            $sql_id = isset($_POST['sql_id'])?$_POST['sql_id']:0;
            $name = isset($_POST['name']) ? $_POST['name'] : '';
            $value = isset($_POST['value']) ? $_POST['value'] : '';
            $multiple = isset($_POST['multiple']) ? (int)$_POST['multiple'] :0;
            $id = isset($_POST['id']) ? $_POST['id'] : \appxq\sdii\utils\SDUtility::getMillisecTime();
            $path_enable = isset($_POST['path_enable'])?$_POST['path_enable']:1;
            
            $data = [];
            $model = EzfUiFunc::modelSqlBuilder($sql_id);
            if($model){
                $sql_builder = SDUtility::string2Array($model->sql_builder);
                
                if(isset($sql_builder['variable']) && !empty($sql_builder['variable'])){
                    foreach ($sql_builder['variable'] as $key => $value_items) {
                        if($path_enable){
                            $data["{{$value_items}}"] = $value_items;
                        } else {
                            $data["{$value_items}"] = $value_items;
                        }
                    }
                }
            }
            
            return $this->renderAjax("/widgets/_subselect", [
                        'id' => $id,
                        'name' => $name,
                        'value' => $value,
                        'multiple'=>$multiple,
                        'placeholder' => Yii::t('ezform', 'Select field ...'),
                        'data' => $data,
            ]);
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }
    
    public function actionGetParams()
    {
	if (Yii::$app->getRequest()->isAjax) {
            $sql_id = isset($_POST['sql_id'])?$_POST['sql_id']:0;
            $name = isset($_POST['name']) ? $_POST['name'] : '';
            $value = isset($_POST['value']) ? $_POST['value'] : '';
            $multiple = isset($_POST['multiple']) ? (int)$_POST['multiple'] :0;
            $id = isset($_POST['id']) ? $_POST['id'] : \appxq\sdii\utils\SDUtility::getMillisecTime();
            
            $data = [];
            $model = EzfUiFunc::modelSqlBuilder($sql_id);
            if($model){
                $sql_builder = SDUtility::string2Array($model->sql_builder);
                
                if(isset($sql_builder['params']) && !empty($sql_builder['params'])){
                    foreach ($sql_builder['params'] as $key => $value_items) {
                        $index_name = str_replace(':', '', $value_items);
                        $data[$index_name] = $key;
                    }
                }
            }
            
            return $this->renderAjax("/widgets/_subselect", [
                        'id' => $id,
                        'name' => $name,
                        'value' => $value,
                        'multiple'=>$multiple,
                        'placeholder' => Yii::t('ezform', 'Select params ...'),
                        'data' => $data,
            ]);
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }
    
    public function actionSqlGenerate()
    {
	if (Yii::$app->getRequest()->isAjax) {
	    $dataid = isset($_GET['dataid'])?$_GET['dataid']:0;
            $ezf_id = isset($_GET['ezf_id'])?$_GET['ezf_id']:0;
            
            Yii::$app->response->format = Response::FORMAT_JSON;
            
            $modelEzf = EzfQuery::getEzformOne($ezf_id);
            
            try {
                $model = new \backend\modules\ezforms2\models\TbdataAll();
                $model->setTableName($modelEzf->ezf_table);
                
                $params_test = [];
                
                $model = $model->find()->where('id=:id AND rstat <> 3', [':id' => $dataid])->one();
                if ($model) {
                    $sql_builder = [];
                    //select
                    $sql_select = SDUtility::string2Array($model->sql_select);
                    if(isset($sql_select) && !empty($sql_select)){
                        $sql_builder['select'] = [];
                        $alias_select = [];
                        $select_name = ['target'];
                        foreach ($sql_select as $key => $value) {
                            if(isset($value['select']) && !empty($value['select'])){
                                $str = explode('.', str_replace('`', '', $value['select']));
                                if(isset($str[1])){
                                    $sub_select = $str[1];
                                    //`zdata_person`.`fname`
                                    $convert_select = false;
                                    $qselect = '';
                                    if(isset($str[1]) && !empty($str[1])){
                                        $data_field = EzfQuery::getFieldByTbNameVersion($str[0], $str[1]);
                                        if($data_field){
                                            if(in_array($data_field['ezf_field_type'], [60,61])){
                                                $convert_select = true;
                                                $qselect = "(SELECT ezf_choicelabel FROM ezform_choice WHERE ezf_id='{$data_field['ezf_id']}' AND ezf_field_id='{$data_field['ezf_field_id']}' AND ezf_version='{$data_field['ezf_version']}' AND ezf_choicevalue={$value['select']} LIMIT 1)";
                                            }
                                        }
                                    }
                                    if(in_array($sub_select, $select_name)){
                                        $alias_select[$sub_select] = isset($alias_select[$sub_select])?$alias_select[$sub_select]+1:2;
                                        $alias_name = "{$sub_select}_{$alias_select[$sub_select]}";
                                        
                                        if($convert_select){
                                            $orig_select = $qselect;
                                            if(isset($value['func']) && !empty($value['func'])){
                                                $orig_select = strtr($value['func'], ['{xfieldx}'=>$orig_select]);
                                            }
                                            $sql_builder['select'][] = "$orig_select AS $alias_name";
                                        } else {
                                            $orig_select = $value['select'];
                                            if(isset($value['func']) && !empty($value['func'])){
                                                $orig_select = strtr($value['func'], ['{xfieldx}'=>$orig_select]);
                                            }
                                            $sql_builder['select'][] = $orig_select." AS $alias_name";
                                        }
                                        $sql_builder['variable'][] = $alias_name;
                                        $sql_builder['mapping'][$alias_name] = $value['select'];
                                    } else {
                                        $select_name[] = $sub_select;
                                        
                                        if($convert_select){
                                            $orig_select = $qselect;
                                            if(isset($value['func']) && !empty($value['func'])){
                                                $orig_select = strtr($value['func'], ['{xfieldx}'=>$orig_select]);
                                            }
                                            $sql_builder['select'][] = "$orig_select AS $sub_select";
                                        } else {
                                            $orig_select = $value['select'];
                                            if(isset($value['func']) && !empty($value['func'])){
                                                $orig_select = strtr($value['func'], ['{xfieldx}'=>$orig_select]);
                                            }
                                            $sql_builder['select'][] = "$orig_select AS $sub_select";
                                        }
                                        $sql_builder['variable'][] = $sub_select;
                                        $sql_builder['mapping'][$sub_select] = $value['select'];
                                    }
                                } else {
                                    $sql_builder['select'][] = $value['select'];
                                    $sql_builder['variable'][] = $value['select'];
                                }
                            }
                        }
                    }
                    
                    //from
                    if(isset($model->sql_from) && !empty($model->sql_from)){
                        $dataEzf = EzfQuery::getEzformById($model->sql_from);
                        if($dataEzf){
                            $sql_builder['from'] = $dataEzf['ezf_table'];
                        }
                    }
                    
                    //join
                    $sql_join = SDUtility::string2Array($model->sql_join);
                    if(isset($sql_join) && !empty($sql_join)){
                        foreach ($sql_join as $key => $value) {
                            if(isset($value['from']) && !empty($value['from']) && isset($value['on_field1']) && !empty($value['on_field1']) && isset($value['on_field2']) && !empty($value['on_field2'])){
                                $join_obj = [];
                                $dataEzf = EzfQuery::getEzformById($value['from']);
                                if($dataEzf){
                                    
                                    $on = "{$value['on_field1']} = {$value['on_field2']} AND {$dataEzf['ezf_table']}.rstat NOT IN(0,3)";
                                    $join_obj = [
                                        'type'  => $value['join'],
                                        'table' => $dataEzf['ezf_table'],
                                        'on'    => $on,
                                    ];
                                } else {
                                    continue;
                                }
                                
                                $sql_builder['join'][] = $join_obj;
                            }
                        }
                    }
                    
                    //where
                    $sql_where = SDUtility::string2Array($model->sql_where);
                    if(isset($sql_where) && !empty($sql_where)){
                        $where = '';
                        $with = '';
                        foreach ($sql_where as $key => $value) {
                            if(isset($value['field']) && !empty($value['field']) && isset($value['value1']) && !empty($value['value1'])){
                                $orig_field = $value['field'];
                                
                                if(isset($value['func']) && !empty($value['func'])){
                                    $orig_field = strtr($value['func'], ['{xfieldx}'=>$orig_field]);
                                }
                                
                                if($value['cond'] == 'BETWEEN') {
                                     if(isset($value['value2']) && !empty($value['value2'])){
                                         $where .= $with . " {$value['bracket1']} {$orig_field} {$value['cond']} {$value['value1']} AND {$value['value2']}  {$value['bracket2']} ";
                                     } else {
                                         continue;
                                     }
                                } else {
                                    
                                    $where .= $with . " {$value['bracket1']} {$orig_field} {$value['cond']} {$value['value1']}  {$value['bracket2']} ";
                                }
                                
                                $with = $value['with'];
                            }
                        }
                        //{xsourcex} {xdepartmentx} {user_id}
                        if($where != ''){
                            $system = ['{xsourcex}', '{xdepartmentx}', '{user_id}'];
                            
                            preg_match_all('%({)(.*?)(})%is', $where, $match);
                            if(isset($match[0]) && !empty($match[0])){
                                foreach ($match[0] as $key => $value) {
                                    if(in_array($value, $system)){
                                        $sql_builder['system'][$value] = ':'.$match[2][$key];
                                    } else {
                                        $sql_builder['params'][$value] = ':'.$match[2][$key];
                                        
                                        $params_test[':'.$match[2][$key]] = isset($_GET[$match[2][$key]])?$_GET[$match[2][$key]]:'';
                                    }
                                }
                            }
                            
                            $params = isset($sql_builder['params'])?$sql_builder['params']:[];
                            $params_system = isset($sql_builder['system'])?$sql_builder['system']:[];
                            $path = \yii\helpers\ArrayHelper::merge($params, $params_system);
                            
                            $sql_builder['where'] = strtr($where, $path);
                        }
                    }
                    
                    //group
                    $sql_group = SDUtility::string2Array($model->sql_group);
                    if(isset($sql_group) && !empty($sql_group)){
                        foreach ($sql_group as $key => $value) {
                            if(isset($value['group']) && !empty($value['group'])){
                                $sql_builder['group'][] = $value['group'];
                            }
                        }
                    }
                    
                    $sql_order = SDUtility::string2Array($model->sql_order);
                    if(isset($sql_order) && !empty($sql_order)){
                        foreach ($sql_order as $key => $value) {
                            if(isset($value['order']) && !empty($value['order']) && isset($value['sort']) && !empty($value['sort'])){
                                $sql_builder['order'][$value['order']] = $value['sort'];
                            }
                        }
                    }
                    
                    //limit
                    if(isset($model->sql_limit) && !empty($model->sql_limit)){
                        $sql_builder['limit'] = $model->sql_limit;
                    }
                    
                    $sql_raw = '';
                    
                    //$query = new \yii\db\Query();
                    $query = EzfUiFunc::queryBuilder($sql_builder, $params_test);
                    
                    if($query){
                        $sql_raw = $query->createCommand()->sql;
                        Yii::$app->db->createCommand()->update($modelEzf->ezf_table, [
                            'sql_builder' => SDUtility::array2String($sql_builder),
                            'sql_raw' => $sql_raw,
                            'sql_success' => 1,
                        ], 'id = :id', [':id'=>$dataid])->execute();

                        $data = $query->all();
//                        if($model->sql_load == 2){
//                            $data = $query->createCommand()->queryOne();
//                        } else {
//                            $data = $query->createCommand()->queryAll();
//                        }
                        
                        $html = '';
                        
                        $provider = new \yii\data\ArrayDataProvider([
                            'allModels' => isset($data)?$data:[],
                            'pagination' => [
                                'pageSize' => 50,
                            ],
                        ]);
                        
                        $html = $this->renderAjax('_sql_viewer', [
                            'data' => $data,
                            'provider' => $provider,
                            'dataid'   => $dataid,
                            'ezf_id'    => $ezf_id,
                            'params_test' => $params_test,
                        ]);
                        
                        $result = [
                            'status' => 'success',
                            'message' => SDHtml::getMsgSuccess() . Yii::t('ezform', 'Generate completed.'),
                            'html' => $html,
                        ];
                        return $result;
                    }
                    
                } 
                
                $result = [
                    'status' => 'error',
                    'message' => SDHtml::getMsgError() . Yii::t('ezform', 'Generate failed.'),
                ];
                return $result;
            } catch (\yii\base\Exception $e) {
                Yii::$app->db->createCommand()->update($modelEzf->ezf_table, [
                    'sql_success' => 0,
                ], 'id = :id', [':id'=>$dataid])->execute();
                
                $result = [
                    'status' => 'error',
                    'message' => SDHtml::getMsgError() . $e->getMessage(),
                ];
                return $result;
            }
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }
    
}
