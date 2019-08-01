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
        $attrname_ezf_id = "options[forms][$key_form][ezf_id]";
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
    <div class="col-md-3 sdbox-col" style="padding-top: 10px;"><input type="text" class="form-control label-input" name="options[forms][<?=$key_form?>][label]" value="" placeholder="label"></div>
      
    <div class="col-md-3 sdbox-col" style="padding-top: 10px;">
        <?php
      $attrname_map_fields = "options[forms][$key_form][field]";
      ?>
        
        <div id="map_field_box-<?=$key_form?>">
            
        </div>
    </div>
    <div class="col-md-2 sdbox-col" style="padding-top: 10px;">
        <?php
      $attrname_fields = "options[forms][$key_form][date]";
      ?>
        
        <div id="ref_field_box-<?=$key_form?>">
            
        </div>
    </div>
    
    
    <div class="col-md-1 sdbox-col" style="padding-top: 10px;" >
        <button type="button" class="forms-items-del btn btn-danger"><i class="glyphicon glyphicon-remove"></i></button>
    </div>
    
    <div class="col-md-3" style="margin-top: 15px;"><?= dominus77\iconpicker\IconPicker::widget([
                                'name'=>"options[forms][$key_form][icon]",
                                'options'=>['class'=>'dicon-input form-control', 'id'=>'iconpicker_specific-'.$key_form, 'placeholder'=>'Icon'],
                                'clientOptions'=>[
                                    'hideOnSelect'=>true,
                                ]
                            ])?></div>
    <div class="col-md-2 sdbox-col" style="margin-top: 15px; padding-bottom: 10px;">
        <div class="input-group">
            <?=  Html::textInput("options[forms][$key_form][color]", 'red', ['class'=>'form-control input-color', 'id'=>'specific-color-'.$key_form])?>
        </div>
    </div>
    <div class="col-md-2 sdbox-col" style="margin-top: 15px;"><?= Html::checkbox("options[forms][$key_form][show]", true, ['label'=>'Show'])?></div>
    <div class="col-md-2 sdbox-col" style="margin-top: 15px;"><?= Html::checkbox("options[forms][$key_form][conddate]", true, ['label'=>'By Date'])?></div>
    <div class="col-md-2 sdbox-col" style="margin-top: 15px;"><?= Html::checkbox("options[forms][$key_form][adddata]", true, ['label'=>'Edit Data'])?></div>
</div>
<?php  $this->registerJs("
var color_options = {
    showInput: true,
    showPalette:true,
    showPaletteOnly: true,
    hideAfterPaletteSelect:true,
    preferredFormat: 'name',
    palette: [
        ['blue','red','darkred','orange','green','darkgreen','purple','darkpuple', 'cadetblue'],
    ]
};

$.iconpicker.batch('#iconpicker_specific-".$key_form."', 'destroy');
$('#iconpicker_specific-".$key_form."').on('click', function() {
    $('#iconpicker_specific-".$key_form."').iconpicker({hideOnSelect:true}).data('iconpicker').show();
});

$('.dicon-input').on('iconpickerHide', function() {
    $.iconpicker.batch('#iconpicker_specific-".$key_form."', 'destroy');
});

$('#specific-color-$key_form').spectrum(color_options);

fields_$key_form($('#config_ezf_id-$key_form').val());
map_fields_$key_form($('#config_ezf_id-$key_form').val());    

$('#config_ezf_id-$key_form').on('change',function(){
    var ezf_id = $(this).val();
    fields_$key_form(ezf_id);
    map_fields_$key_form(ezf_id);    
});
    
function fields_$key_form(ezf_id){
    var value = '';
    $.post('".Url::to(['/ezforms2/target/get-fields', 'type'=> backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String([63, 64])])."',{ ezf_id: ezf_id, multiple:0, name: '{$attrname_fields}', value: value ,id:'config_fields-$key_form'}
      ).done(function(result){
         $('#ref_field_box-$key_form').html(result);
      }).fail(function(){
          ". \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
          console.log('server error');
      });
}

function map_fields_$key_form(ezf_id){
    var value = '';
    $.post('".Url::to(['/ezforms2/target/get-fields', 'type'=> backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String([74])])."',{ ezf_id: ezf_id, multiple:0, name: '{$attrname_map_fields}', value: value ,id:'config_map_fields-$key_form'}
      ).done(function(result){
         $('#map_field_box-$key_form').html(result);
      }).fail(function(){
          ". \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
          console.log('server error');
      });
}
");
?>