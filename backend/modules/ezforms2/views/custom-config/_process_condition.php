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
$field = isset($options['field'])?$options['field']:'';
?>
<?php if($header==1): ?>
<div class="row" >
  <div class="col-md-1 "><label>Bracket</label></div>
  <div class="col-md-3 sdbox-col"><label>Field</label></div>
  <div class="col-md-2 sdbox-col"><label>Condition</label></div>
  <div class="col-md-3 sdbox-col"><label>Value</label></div>
  <div class="col-md-1 sdbox-col"><label>Bracket</label></div>
  <div class="col-md-1 sdbox-col"><label>With</label></div>
  <div class="col-md-1 sdbox-col"><label></label></div>
</div>
<?php else: ?>
<div class="row item" data-id="<?=$index_id?>">
    <div class="col-md-1">
      <?= Html::dropDownList("{$name}[{$index_id}][bracket1]", isset($options['bracket1'])?$options['bracket1']:'', [''=>'', '('=>'('], ['class'=>'form-control options-bracket1', 'id'=>"bracket1-$index_id"])?>
    </div>
 
  <div class="col-md-3 sdbox-col tbfields-<?=$index_id?>">
      
    </div>
  <div class="col-md-2 sdbox-col">
      <?= Html::dropDownList("{$name}[{$index_id}][cond]", isset($options['cond'])?$options['cond']:'==', ModuleFunc::itemAlias('phpCondition'), ['class'=>'form-control options-cond', 'id'=>"cond-$index_id"])?>
    </div>
  <div class="col-md-3 sdbox-col">
      <?= Html::textInput("{$name}[{$index_id}][value1]", isset($options['value1'])?$options['value1']:'', ['class'=>'form-control options-value1', 'id'=>"value1-$index_id", 'maxlength'=>255])?>
    </div>
  <div class="col-md-1 sdbox-col">
      <?= Html::dropDownList("{$name}[{$index_id}][bracket2]", isset($options['bracket2'])?$options['bracket2']:'', [''=>'', ')'=>')'], ['class'=>'form-control options-bracket2', 'id'=>"bracket2-$index_id"])?>
    </div>
  <div class="col-md-1 sdbox-col">
      <?= Html::dropDownList("{$name}[{$index_id}][with]", isset($options['with'])?$options['with']:'&&', ModuleFunc::itemAlias('phpAndOr'), ['class'=>'form-control options-with', 'id'=>"with-$index_id"])?>
    </div>
    <div class="col-md-1 sdbox-col">
	<button type="button" class="btn btn-link del-items"><i class="glyphicon glyphicon-remove" style="color: #ff0000; font-size: 20px;"></i></button>	 
    </div>
</div>
<?php
$this->registerJs("
ezf_where_items = getDependAll(); 
tableFields(ezf_where_items);  

function tableFields(ezf_id){
    let value = '$field';
    $.post('". \yii\helpers\Url::to(['/ezforms2/target/get-fields-addon'])."',{ ezf_id: ezf_id, multiple:0, name: '{$name}[{$index_id}][field]', value: value ,id:'field-$index_id'}
      ).done(function(result){
         $('#widget_$id .tbfields-$index_id').html(result);
      }).fail(function(){
          ". \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
          console.log('server error');
      });
}

$('#cond-$index_id').on('change', function(){
    if($(this).val()=='BETWEEN'){
	$(this).parent().parent().find('.options-value2').removeAttr('readonly');
    } else {
	$(this).parent().parent().find('.options-value2').attr('readonly','readonly');
	$(this).parent().parent().find('.options-value2').val('');
    }
});

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
