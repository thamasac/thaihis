<?php
use yii\helpers\Html;
use yii\helpers\Url;
use backend\modules\ezmodules\classes\ModuleFunc;
use yii\web\JsExpression;
use kartik\widgets\Select2;
use kartik\depdrop\DepDrop;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


?>

<div class="row form-group items-condition" data-id="<?=$id?>" id="<?=$id?>">
    <div class="col-md-2 "><?= Select2::widget([
            'options' => ['id'=>'forms_'.$id, 'class'=>'cform-input'],
            'data' => $dataForm,
            'name'=>"options[$id][form]",
	    'value' => isset($value['form'])?$value['form']:NULL,
            'pluginOptions' => [
                'allowClear' => false,
		'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
		'templateResult' => new JsExpression('function(result) { return result.text; }'),
		'templateSelection' => new JsExpression('function (result) { return result.text; }'),
            ],
	    
        ])?></div>
    <div class="col-md-2 sdbox-col"><?= DepDrop::widget([
            'type'=>  DepDrop::TYPE_SELECT2,
            'options'=>['id'=>'fields_'.$id, 'class'=>'cfield-input'],
            'name'=>"options[$id][field]",
	    'select2Options'=>['pluginOptions'=>['allowClear'=>false]],
            'pluginOptions'=>[
		'allowClear' => false,
                'depends'=>['forms_'.$id],
                'url'=>Url::to(['/ezmodules/ezmodule/get-fields']),
                'initialize' => true,
		'initDepends'=>['forms_'.$id],
		'params'=>['tmp_fields_'.$id],
            ],
        ])?><?= Html::hiddenInput('tmp_fields_'.$id, isset($value['field'])?$value['field']:NULL, ['id'=>'tmp_fields_'.$id])?></div>
    <div class="col-md-2 sdbox-col"><?= Html::dropDownList("options[$id][cond]", isset($value['cond'])?$value['cond']:NULL, ModuleFunc::itemAlias('sqlCondition'), ['class'=>'form-control ccond-input'])?></div>
    <div class="col-md-2 sdbox-col"><?= Html::textInput("options[$id][value1]", isset($value['value1'])?$value['value1']:NULL, ['class'=>'form-control cvalue1-input'])?></div>
    <div class="col-md-2 sdbox-col"><?= Html::textInput("options[$id][value2]", isset($value['value2'])?$value['value2']:NULL, ['class'=>'form-control cvalue2-input', 'readonly'=>(isset($value['cond']) && $value['cond']=='BETWEEN')?FALSE:TRUE ])?></div>
    <div class="col-md-1 sdbox-col"><?= Html::dropDownList("options[$id][more]", isset($value['more'])?$value['more']:'OR', ModuleFunc::itemAlias('sqlAndOr'), ['class'=>'form-control cmore-input'])?></div>
    <div class="col-md-1 sdbox-col"><a style="cursor: pointer" class="btn btn-danger btn-del" data-id="<?=$id?>"><i class="glyphicon glyphicon-remove"></i></a></div>
</div>