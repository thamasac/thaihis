<?php
use yii\helpers\Html;
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
$order = isset($options['order'])?$options['order']:''; 
?>
<?php if($header==1): ?>
<div class="row" >
  <div class="col-md-6 "><label>Order By</label></div>
  <div class="col-md-3 sdbox-col"><label>Sort</label></div>
  <div class="col-md-1 sdbox-col"><label></label></div>
</div>
<?php else: ?>
<div class="row item" data-id="<?=$index_id?>">
    <div class="col-md-6 tbfields-<?=$index_id?>">
    </div>
    <div class="col-md-3 sdbox-col">
      <?= Html::dropDownList("{$name}[{$index_id}][sort]", isset($options['sort'])?$options['sort']:4, ['asc'=>'ASC', 'desc'=>'DESC'], ['class'=>'form-control options-sort', 'id'=>"sort-$index_id"])?>
    </div>
    <div class="col-md-1 sdbox-col">
	<button type="button" class="btn btn-link del-items"><i class="glyphicon glyphicon-remove" style="color: #ff0000; font-size: 20px;"></i></button>	 
    </div>
</div>
<?php
$this->registerJs("
ezf_order_items = getDependAll(); 
tableFields(ezf_order_items);  

function tableFields(ezf_id){
    let value = '$order';
    $.post('". \yii\helpers\Url::to(['/ezforms2/target/table-fields'])."',{ ezf_id: ezf_id, multiple:0, name: '{$name}[{$index_id}][order]', value: value ,id:'order-$index_id'}
      ).done(function(result){
         $('#widget_$id .tbfields-$index_id').html(result);
      }).fail(function(){
          ". \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
          console.log('server error');
      });
}

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
