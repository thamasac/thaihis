<?php
use backend\modules\ezforms2\classes\EzfQuery;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$modelEzf = EzfQuery::getEzformOne($ezf_id);
$ezf_input = EzfQuery::getInputv2All();
$dataInput;
if (isset($ezf_input)) {
    $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($model_field['ezf_field_type'], $ezf_input);
}

if(empty($action)){
    $data = backend\modules\ezforms2\classes\EzfUiFunc::loadTbData($modelEzf->ezf_table, $dataid);
    if($data){
        if ($dataInput && !empty($dataInput['system_class'])) {
            $inputWidget = Yii::createObject($dataInput['system_class']);   
            
            $value = $inputWidget->getValue($model_field, $data->attributes);
        }
        echo $value;
    } else {
        echo '';
    }
} else {
    $form = new \backend\modules\ezforms2\classes\EzActiveForm([
        'id' => $action['id'],
        'action' => $action['action'],
        'options' => $action['options']
    ]);
    
    $modelFields = EzfQuery::getFieldAll($ezf_id, $v);
    $model = \backend\modules\ezforms2\classes\EzfFunc::setDynamicModel($modelFields, $modelEzf->ezf_table, $ezf_input, 0);
    $model = \backend\modules\ezforms2\classes\EzfUiFunc::loadData($model, $modelEzf->ezf_table, $dataid);
    $model->afterFind();
    
    echo \backend\modules\ezforms2\classes\EzfFunc::generateInput($form, $model, $model_field, $dataInput, 0, $modelEzf, 0);
}

?>