<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


$key_form = \appxq\sdii\utils\SDUtility::getMillisecTime();
$itemsEzform = \backend\modules\ezforms2\classes\EzfQuery::getEzformCoDevAll();
?>
<div id="<?=$key_form?>" class="row form-fields dads-children" data-id="<?=$key_form?>" style="margin-bottom: 15px;">
  <div class="col-md-3 " style="padding-top: 10px;">
    <?php 
        $attrname_ezf_id = "options[params][forms][$key_form][ezf_id]";
        echo kartik\select2\Select2::widget([
            'name' => $attrname_ezf_id,
            'options' => ['placeholder' => Yii::t('ezform', 'Form'), 'id'=>'config_ezf_id-'.$key_form, 'class'=>'form-input'],
            'data' => ArrayHelper::map($itemsEzform,'ezf_id','ezf_name'),
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
    
    <div class="col-md-2 sdbox-col" style="padding-top: 10px;">
        <?php
      $attrname_subject_fields = "options[params][forms][$key_form][subject]";
      ?>
        
        <div id="subject_field_box-<?=$key_form?>">
            
        </div>
    </div>
    <div class="col-md-2 sdbox-col" style="padding-top: 10px;">
        <?php
      $attrname_start_fields = "options[params][forms][$key_form][start]";
      ?>
        
        <div id="start_field_box-<?=$key_form?>">
            
        </div>
    </div>
  
    <div class="col-md-2 sdbox-col" style="padding-top: 10px;">
        <?php
      $attrname_end_fields = "options[params][forms][$key_form][end]";
      ?>
        
        <div id="end_field_box-<?=$key_form?>">
            
        </div>
    </div>
  
    <div class="col-md-2 sdbox-col" style="padding-top: 10px;">
        <?php
      $attrname_allday_fields = "options[params][forms][$key_form][allday]";
      ?>
        
        <div id="allday_field_box-<?=$key_form?>">
            
        </div>
    </div>
    
    
    <div class="col-md-1 sdbox-col" style="padding-top: 10px;" >
        <button type="button" class="forms-items-del btn btn-danger"><i class="glyphicon glyphicon-remove"></i></button>
    </div>
    
     <div class="col-md-3 " style="padding-top: 10px;"><input type="text" class="form-control label-input" name="options[params][forms][<?=$key_form?>][label]" value="" placeholder="label"></div>
                             
    <div class="col-md-1 sdbox-col" style="margin-top: 15px; padding-bottom: 10px;">
        <div class="input-group">
            <?=  Html::textInput("options[params][forms][$key_form][color]", '#3a87ad', ['class'=>'form-control input-color', 'id'=>'specific-color-'.$key_form])?>
        </div>
    </div>
     <div class="col-md-2 sdbox-col text-right" style="margin-top: 15px; padding-bottom: 10px;">
                <label style="margin-top: 7px;">Repeat Field</label> 
            </div>
            <div class="col-md-2 sdbox-col" style="margin-top: 15px; padding-bottom: 10px;">
              
                <?php
                    $attrname_repeat_fields = "options[params][forms][$key_form][repeat]";
                    ?>

                  <div id="repeat_field_box-<?= $key_form ?>">

                  </div>
            </div>
    <div class="col-md-2 sdbox-col" style="margin-top: 15px;"><?= Html::checkbox("options[params][forms][$key_form][show]", true, ['label'=>'Show'])?></div>
    <div class="col-md-2 sdbox-col" style="margin-top: 15px;"><?= Html::checkbox("options[params][forms][$key_form][editable]", true, ['label'=>'Edit Data'])?></div>
</div>
<?php  $this->registerJs("
var color_options = {
    showInput: true,
    showInitial:true,
    //allowEmpty:true,
    showPalette:true,
    showSelectionPalette:true,
    hideAfterPaletteSelect:true,
    showAlpha:false,
    preferredFormat:'hex',
    palette: [
        ['#000','#444','#666','#999','#ccc','#eee','#f3f3f3','#fff'],
        ['#f00','#f90','#ff0','#0f0','#0ff','#00f','#90f','#f0f'],
        ['#f4cccc','#fce5cd','#fff2cc','#d9ead3','#d0e0e3','#cfe2f3','#d9d2e9','#ead1dc'],
        ['#ea9999','#f9cb9c','#ffe599','#b6d7a8','#a2c4c9','#9fc5e8','#b4a7d6','#d5a6bd'],
        ['#e06666','#f6b26b','#ffd966','#93c47d','#76a5af','#6fa8dc','#8e7cc3','#c27ba0'],
        ['#c00','#e69138','#f1c232','#6aa84f','#45818e','#3d85c6','#674ea7','#a64d79'],
        ['#900','#b45f06','#bf9000','#38761d','#134f5c','#0b5394','#351c75','#741b47'],
        ['#600','#783f04','#7f6000','#274e13','#0c343d','#073763','#20124d','#4c1130']
    ]
};

$('#specific-color-$key_form').spectrum(color_options);

subject_fields_$key_form($('#config_ezf_id-$key_form').val());
start_fields_$key_form($('#config_ezf_id-$key_form').val());
end_fields_$key_form($('#config_ezf_id-$key_form').val());   
allday_fields_$key_form($('#config_ezf_id-$key_form').val());      
repeat_fields_$key_form($('#config_ezf_id-$key_form').val());
    
$('#config_ezf_id-$key_form').on('change',function(){
    var ezf_id = $(this).val();
    subject_fields_$key_form(ezf_id);
    start_fields_$key_form(ezf_id);
    end_fields_$key_form(ezf_id); 
    allday_fields_$key_form(ezf_id);    
    repeat_fields_$key_form(ezf_id);       
});
    

function subject_fields_$key_form(ezf_id){
    var value = '';
    $.post('".Url::to(['/ezforms2/target/get-fields', 'type'=> backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String([51,52,53,54,55])])."',{ ezf_id: ezf_id, multiple:0, name: '{$attrname_subject_fields}', value: value ,id:'config_subject_fields-$key_form'}
      ).done(function(result){
         $('#subject_field_box-$key_form').html(result);
      }).fail(function(){
          ". \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
          console.log('server error');
      });
}

function start_fields_$key_form(ezf_id){
    var value = '';
    $.post('".Url::to(['/ezforms2/target/get-fields', 'type'=> backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String([63,64])])."',{ ezf_id: ezf_id, multiple:0, name: '{$attrname_start_fields}', value: value ,id:'config_start_fields-$key_form'}
      ).done(function(result){
         $('#start_field_box-$key_form').html(result);
      }).fail(function(){
          ". \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
          console.log('server error');
      });
}

function end_fields_$key_form(ezf_id){
    var value = '';
    $.post('".Url::to(['/ezforms2/target/get-fields', 'type'=> backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String([63,64])])."',{ ezf_id: ezf_id, multiple:0, name: '{$attrname_end_fields}', value: value ,id:'config_end_fields-$key_form'}
      ).done(function(result){
         $('#end_field_box-$key_form').html(result);
      }).fail(function(){
          ". \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
          console.log('server error');
      });
}

function allday_fields_$key_form(ezf_id){
    var value = '';
    $.post('".Url::to(['/ezforms2/target/get-fields', 'type'=> backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String([62])])."',{ ezf_id: ezf_id, multiple:0, name: '{$attrname_allday_fields}', value: value ,id:'config_allday_fields-$key_form'}
      ).done(function(result){
         $('#allday_field_box-$key_form').html(result);
      }).fail(function(){
          ". \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
          console.log('server error');
      });
}
function repeat_fields_$key_form(ezf_id){
    var value = '';
    $.post('" . Url::to(['/ezforms2/target/get-fields', 'type' => backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String([85])]) . "',{ ezf_id: ezf_id, multiple:0, name: '{$attrname_repeat_fields}', value: value ,id:'config_repeat_fields-$key_form'}
      ).done(function(result){
         $('#repeat_field_box-$key_form').html(result);
      }).fail(function(){
          " . \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
          console.log('server error');
      });
}
");
?>