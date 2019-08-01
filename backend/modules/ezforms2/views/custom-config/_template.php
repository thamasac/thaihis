<?php
use yii\helpers\Html;
use backend\modules\ezmodules\classes\ModuleFunc;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$index_id = isset($index_id) && !empty($index_id)?$index_id:\appxq\sdii\utils\SDUtility::getMillisecTime();
$id="ez{$ezf_id}-{$ezf_field_name}";
$name="EZ{$ezf_id}[{$ezf_field_name}]";
$options = isset($options) && !empty($options)?$options:[];
$depend = isset($depend) && !empty($depend)?$depend:'';
$header = isset($header)?$header:0;
//appxq\sdii\utils\VarDumper::dump($options);
?>
<?php if($header==1): ?>
<div class="row" >
  <div class="col-md-4"><label>Value1</label></div>
  <div class="col-md-4 sdbox-col"><label>Value2</label></div>
  <div class="col-md-1 sdbox-col"><label></label></div>
</div>
<?php else: ?>
<div class="row item" data-id="<?=$index_id?>">
  <div class="col-md-4">
      <?= Html::textInput("{$name}[{$index_id}][value1]", isset($options['value1'])?$options['value1']:'', ['class'=>'form-control options-value1', 'id'=>"value1-$index_id"])?>
    </div>
  <div class="col-md-4 sdbox-col">
      <?= Html::textInput("{$name}[{$index_id}][value2]", isset($options['value2'])?$options['value2']:'', ['class'=>'form-control options-value2', 'id'=>"value2-$index_id"])?>
    </div>
    <div class="col-md-1 sdbox-col">
	<button type="button" class="btn btn-link del-items"><i class="glyphicon glyphicon-remove" style="color: #ff0000; font-size: 20px;"></i></button>	 
    </div>
</div>
<?php
$this->registerJs("

function getDepend(depend){
    let elem_depend = $('#ez{$ezf_id}-'+depend);
    let items = [];    
    if(elem_depend.length > 0){
        if(elem_depend.val()!=''){
            items.push( elem_depend.val() );
        }
    } else {
        elem_depend = $('#widget_ez{$ezf_id}-'+depend);
        if(elem_depend.length > 0){
            elem_depend.find('.options-depend').each(function( index ) {
                items.push( $(this).val() );
            });
        }
    }
    
    let dependMore = elem_depend.attr('data-depend');
    if(dependMore){
        items = items.concat(getDepend(dependMore));
    }
    
    return items;
}

function getMyDepend(){
    let elem_this = $('#widget_$id');
    let items = [];
    if(elem_this.length > 0){
        elem_this.find('.options-depend').each(function( index ) {
            items.push( $(this).val() );
        });
    }
    
    return items;
}

function getDependAll(){
    let items = [];    
    items = getDepend('{$depend}');
    items = items.concat(getMyDepend());
    
    return items.join();
}
");
?>
<?php endif;?>
