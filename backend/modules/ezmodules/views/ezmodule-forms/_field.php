<?php
use yii\helpers\Html;
use backend\modules\ezmodules\classes\ModuleFunc;
use kartik\widgets\Select2;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

?>
<div class="row form-group items-field" data-id="<?=$id?>" id="items-field<?=$id?>">
    <div class="col-md-6 "><?= Select2::widget([
            'options' => ['id'=>'field_show_'.$id, 'class'=>'sfield-input'],
            'data' => $dataFields,
            'name'=>"options{$prefix}[fields][$id][field]",
	    'value' => isset($value['field'])?$value['field']:NULL,
            'pluginOptions' => [
                'allowClear' => false,
            ],
        ])?></div>
    <div class="col-md-2 sdbox-col"><a style="cursor: pointer" class="btn btn-danger btn-del" data-id="<?=$id?>"><i class="glyphicon glyphicon-remove"></i></a></div>
</div>