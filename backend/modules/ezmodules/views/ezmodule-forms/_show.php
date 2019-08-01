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
<div class="row form-group items-show" data-id="<?=$id?>" id="items-show<?=$id?>">
    <div class="col-md-2 "><?= Select2::widget([
            'options' => ['id'=>'field_show_'.$id, 'class'=>'sfield-input'],
            'data' => $dataFields,
            'name'=>"options{$prefix}[show][$id][field]",
	    'value' => isset($value['field'])?$value['field']:NULL,
            'pluginOptions' => [
                'allowClear' => false,
            ],
        ])?></div>
    <div class="col-md-1 sdbox-col"><?= Html::dropDownList("options{$prefix}[show][$id][cond]", isset($value['cond'])?$value['cond']:NULL, ModuleFunc::itemAlias('phpCondition'), ['class'=>'form-control scond-input'])?></div>
    <div class="col-md-2 sdbox-col"><?= Html::textInput("options{$prefix}[show][$id][value1]", isset($value['value1'])?$value['value1']:NULL, ['class'=>'form-control svalue1-input'])?></div>
    <div class="col-md-2 sdbox-col"><?= Html::textInput("options{$prefix}[show][$id][value2]", isset($value['value2'])?$value['value2']:NULL, ['class'=>'form-control svalue2-input', 'readonly'=>(isset($value['cond']) && $value['cond']=='BETWEEN')?FALSE:TRUE ])?></div>
    <div class="col-md-1 sdbox-col">
        <?= dominus77\iconpicker\IconPicker::widget([
            'name'=>"options{$prefix}[show][$id][icon]",
            'value'=>isset($value['icon'])?$value['icon']:'fa-check',
            'options'=>['class'=>'sicon-input form-control', 'id'=>"iconpicker_$id"],
            'clientOptions'=>[
                'hideOnSelect'=>true,
            ]
        ])?>
    </div>
    <div class="col-md-1 sdbox-col">
        <?= kartik\color\ColorInput::widget([
            'name'=>"options{$prefix}[show][$id][color]",
            'value'=>isset($value['color'])?$value['color']:NULL,
            'options'=>['class'=>'form-control scolor-input', 'id'=>"spectrum_$id"],
            'pluginOptions'=>[
                'hideAfterPaletteSelect'=>true,
                'allowEmpty'=>true,
            ]        
        ])?>
    </div>
    <div class="col-md-2 sdbox-col"><?= Html::textInput("options{$prefix}[show][$id][label]", isset($value['label'])?$value['label']:NULL, ['class'=>'form-control slabel-input'])?></div>
    <div class="col-md-1 sdbox-col"><a style="cursor: pointer" class="btn btn-danger btn-del" data-id="<?=$id?>"><i class="glyphicon glyphicon-remove"></i></a></div>
</div>
<?php  $this->registerJs("
$.iconpicker.batch('#iconpicker_$id', 'destroy');
$('#iconpicker_$id').on('click', function() {
    $('#iconpicker_$id').iconpicker({hideOnSelect:true}).data('iconpicker').show();
});

$('#iconpicker_$id').on('iconpickerHide', function() {
    $.iconpicker.batch('#iconpicker_$id', 'destroy');
});

");
?>