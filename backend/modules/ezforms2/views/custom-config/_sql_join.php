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

$on_field1 = isset($options['on_field1'])?$options['on_field1']:'';
$on_field2 = isset($options['on_field2'])?$options['on_field2']:'';
?>
<?php if($header==1): ?>
<div class="row" >
  <div class="col-md-2 "><label>Join</label></div>
  <div class="col-md-3 sdbox-col"><label>Form</label></div>
  <div class="col-md-3 sdbox-col"><label>On (Field1 = Field2)</label></div>
  <div class="col-md-3 sdbox-col"><label></label></div>
  <div class="col-md-1 sdbox-col"><label></label></div>
</div>
<?php else: ?>
<div class="row item" data-id="<?=$index_id?>">
    <div class="col-md-2">
      <?= Html::dropDownList("{$name}[{$index_id}][join]", isset($options['join'])?$options['join']:'INNER', ['INNER JOIN'=>'INNER JOIN', 'LEFT JOIN'=>'LEFT JOIN', 'RIGHT JOIN'=>'RIGHT JOIN', 'CROSS JOIN'=>'CROSS JOIN'], ['class'=>'form-control options-join', 'id'=>"join-$index_id"])?>
    </div>
  <div class="col-md-3 sdbox-col">
      <?php
      $itemsEzform = \backend\modules\ezforms2\classes\EzfQuery::getEzformCoDevTableAll();
      echo kartik\select2\Select2::widget([
            'name' => "{$name}[{$index_id}][from]",
            'value'=> isset($options['from'])?$options['from']:'',
            'options' => ['placeholder' => Yii::t('ezform', 'Form'), 'id'=>"from-$index_id", 'class'=>'options-depend'],
            'data' => yii\helpers\ArrayHelper::map($itemsEzform,'ezf_id','ezf_name'),
            'pluginOptions' => [
                'allowClear' => true
            ],
            'pluginEvents' => [
		"select2:select" => "function(e) { let ezf_items = getDependAll(); tableFields1(ezf_items); tableFields2(ezf_items); }",
                "select2:unselect" => "function(e) { $('#widget_$id .ontbfields2-$index_id').html(''); $('#widget_$id .ontbfields1-$index_id').html(''); }",
	    ]
        ]);
      ?>
  </div>
  <div class="col-md-3 sdbox-col ontbfields1-<?=$index_id?>">
      
  </div>
  <div class="col-md-3 sdbox-col ontbfields2-<?=$index_id?>">
      
  </div>
    <div class="col-md-1 sdbox-col">
	<button type="button" class="btn btn-link del-items"><i class="glyphicon glyphicon-remove" style="color: #ff0000; font-size: 20px;"></i></button>	 
    </div>
</div>
<?php
$this->registerJs("
on_field1 = '$on_field1';
on_field2 = '$on_field2';
if(on_field1!='' || on_field2!=''){
    ezf_items = getDependAll(); 
    tableFields1(ezf_items); 
    tableFields2(ezf_items);
}


function tableFields1(ezf_id){
    let value = '$on_field1';
    $.post('". \yii\helpers\Url::to(['/ezforms2/target/table-fields'])."',{ ezf_id: ezf_id, multiple:0, name: '{$name}[{$index_id}][on_field1]', value: value ,id:'on_field1-$index_id'}
      ).done(function(result){
         $('#widget_$id .ontbfields1-$index_id').html(result);
      }).fail(function(){
          ". \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
          console.log('server error');
      });
}

function tableFields2(ezf_id){
    let value = '$on_field2';
    $.post('". \yii\helpers\Url::to(['/ezforms2/target/table-fields'])."',{ ezf_id: ezf_id, multiple:0, name: '{$name}[{$index_id}][on_field2]', value: value ,id:'on_field2-$index_id'}
      ).done(function(result){
         $('#widget_$id .ontbfields2-$index_id').html(result);
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


