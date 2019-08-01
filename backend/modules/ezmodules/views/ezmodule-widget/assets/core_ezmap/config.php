<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
\backend\modules\ezforms2\assets\EzfColorInputAsset::register($this);

$options = isset($model->options)?\appxq\sdii\utils\SDUtility::string2Array($model->options):[];

//$target = backend\modules\ezforms2\classes\EzfQuery::getTargetOne($ezf_id);
$itemsEzform = \backend\modules\ezforms2\classes\EzfQuery::getEzformCoDevAll();

?>

<div class="modal-header" style="margin-bottom: 15px;">
    <h4 class="modal-title" ><?= Yii::t('ezmodule', 'Widget Config')?></h4>
</div>

<!--config start-->

<div class="row" style="margin-top: 15px; margin-bottom: 15px;">
    <div class="col-md-12">
        <label class="control-label"><?= Yii::t('ezmodule', 'Initial Coordinates')?></label>
        <?php
        echo \appxq\sdii\widgets\MapInput::widget([
            'lat'=>'lat_init',
            'lng'=>'lng_init',
            'latValue'=>isset($options['lat_init'])?$options['lat_init']:'',
            'lngValue'=>isset($options['lng_init'])?$options['lng_init']:'',
        ]);
        echo Html::hiddenInput('options[lat_init]', isset($options['lat_init'])?$options['lat_init']:'', ['id'=>'lat_init']);
        echo Html::hiddenInput('options[lng_init]', isset($options['lng_init'])?$options['lng_init']:'', ['id'=>'lng_init']);
        ?>
    </div>

</div>
<div class="row" style="margin-top: 15px; margin-bottom: 15px;">
    <div class="col-md-12">
      <label class="control-label">Zoom <code><?= Yii::t('ezmodule', 'The more the number, the more zoomed.')?></code></label>
        <?= Html::dropDownList('options[zoom_init]', isset($options['zoom_init'])?$options['zoom_init']:'9', [7=>7,8=>8,9=>9,10=>10,11=>11,12=>12,13=>13,14=>14,15=>15,16=>16,17=>17,18=>18], ['class'=>'form-control'])?>
    </div>
</div>
<div class="row" >
                <div class="col-md-3 "><label>Form</label></div>
                <div class="col-md-3 sdbox-col"><label>Label</label></div>
                <div class="col-md-3 sdbox-col"><label>Map Field</label></div>
                <div class="col-md-2 sdbox-col"><label>Date Field</label></div>
               
            </div>
<div id="dad-item-box" class="forms-items">
    <?php

    if(isset($options['forms']) && is_array($options['forms']) && !empty($options['forms'])){

        foreach ($options['forms'] as $key_form => $value_form) {
            
    ?>
            <div id="<?=$key_form?>" class="row form-fields dads-children" data-id="<?=$key_form?>" style="margin-bottom: 15px;">
            <div class="col-md-3 " style="padding-top: 10px;">
              <?php 
                  $attrname_ezf_id = "options[forms][$key_form][ezf_id]";
                  $value_ezf_id = isset($value_form['ezf_id'])?$value_form['ezf_id']:'';
                  
                  echo kartik\select2\Select2::widget([
                      'name' => $attrname_ezf_id,
                      'value' => $value_ezf_id,
                      'options' => ['placeholder' => Yii::t('ezform', 'Form'), 'id'=>'config_ezf_id-'.$key_form, 'class'=>'form-input'],
                      'data' => ArrayHelper::map($itemsEzform,'ezf_id','ezf_name'),
                      'pluginOptions' => [
                          'allowClear' => true
                      ],
                  ]);
                  ?>
              </div>
              <div class="col-md-3 sdbox-col" style="padding-top: 10px;"><input type="text" class="form-control label-input" name="options[forms][<?=$key_form?>][label]" value="<?=$value_form['label']?>" placeholder="label"></div>
              <div class="col-md-3 sdbox-col" style="padding-top: 10px;">
                <?php
              $attrname_map_fields = "options[forms][$key_form][field]";
              $value_map_fields = isset($value_form['field'])?$value_form['field']:'';
              ?>

                <div id="map_field_box-<?=$key_form?>">

                </div>
            </div>  
              <div class="col-md-2 sdbox-col" style="padding-top: 10px;">
                  <?php
                $attrname_fields = "options[forms][$key_form][date]";
                $value_fields = isset($value_form['date'])?$value_form['date']:'';
                ?>

                  <div id="ref_field_box-<?=$key_form?>">

                  </div>
              </div>
              

              <div class="col-md-1 sdbox-col" style="padding-top: 10px;">
                  <button type="button" class="forms-items-del btn btn-danger"><i class="glyphicon glyphicon-remove"></i></button>
              </div>

              <div class="col-md-3" style="margin-top: 15px; padding-bottom: 10px;"><?= dominus77\iconpicker\IconPicker::widget([
                                          'name'=>"options[forms][$key_form][icon]",
                                          'value' => isset($value_form['icon'])?$value_form['icon']:'',
                                          'options'=>['class'=>'dicon-input form-control', 'id'=>'iconpicker_specific-'.$key_form, 'placeholder'=>'Icon'],
                                          'clientOptions'=>[
                                              'hideOnSelect'=>true,
                                          ]
                                      ])?></div>
              <div class="col-md-2 sdbox-col" style="margin-top: 15px;">
                  <div class="input-group">
                      <?=  Html::textInput("options[forms][$key_form][color]", isset($value_form['color'])?$value_form['color']:'', ['class'=>'form-control input-color', 'id'=>'specific-color-'.$key_form])?>
                  </div>
              </div>
              
              <div class="col-md-2 sdbox-col" style="margin-top: 15px;"><?= Html::checkbox("options[forms][$key_form][show]", isset($value_form['color'])?$value_form['show']:false, ['label'=>'Show'])?></div>
              <div class="col-md-2 sdbox-col" style="margin-top: 15px;"><?= Html::checkbox("options[forms][$key_form][conddate]", isset($value_form['conddate'])?$value_form['color']:false, ['label'=>'By Date'])?></div>
              <div class="col-md-2 sdbox-col" style="margin-top: 15px;"><?= Html::checkbox("options[forms][$key_form][adddata]", isset($value_form['adddata'])?$value_form['color']:false, ['label'=>'Edit Data'])?></div>
          </div>
  <?php  $this->registerJs("

$.iconpicker.batch('#iconpicker_specific-".$key_form."', 'destroy');
$('#iconpicker_specific-".$key_form."').on('click', function() {
    $('#iconpicker_specific-".$key_form."').iconpicker({hideOnSelect:true}).data('iconpicker').show();
});

$('.dicon-input').on('iconpickerHide', function() {
    $.iconpicker.batch('#iconpicker_specific-".$key_form."', 'destroy');
});

$('#specific-color-$key_form').spectrum({
    showInput: true,
    showPalette:true,
    showPaletteOnly: true,
    hideAfterPaletteSelect:true,
    preferredFormat: 'name',
    palette: [
        ['blue','red','darkred','orange','green','darkgreen','purple','darkpuple', 'cadetblue'],
    ]
});

fields_$key_form($('#config_ezf_id-$key_form').val());
map_fields_$key_form($('#config_ezf_id-$key_form').val()); 
    
$('#config_ezf_id-$key_form').on('change',function(){
    var ezf_id = $(this).val();
    fields_$key_form(ezf_id);
     map_fields_$key_form(ezf_id);     
});
    
function fields_$key_form(ezf_id){
    var value = '$value_fields';
    $.post('".Url::to(['/ezforms2/target/get-fields', 'type'=> backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String([63, 64])])."',{ ezf_id: ezf_id, multiple:0, name: '{$attrname_fields}', value: value ,id:'config_fields-$key_form'}
      ).done(function(result){
         $('#ref_field_box-$key_form').html(result);
      }).fail(function(){
          ". \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
          console.log('server error');
      });
}

function map_fields_$key_form(ezf_id){
    var value = '$value_map_fields';
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
    <?php 
        }
    }
   ?>
</div>

<div class="row add-forms">
    <div class="col-md-10"></div>
    <div class="col-md-2 sdbox-col" style="text-align: right;"><button type="button" class="forms-items-add btn btn-success"><i class="glyphicon glyphicon-plus"></i> Add</button></div>
</div>

<!--config end-->

<?php
$this->registerJS("
    $('.forms-items').on('click', '.forms-items-del', function(){
        $(this).parent().parent().remove();
    });

    $('.forms-items').on('change', '.form-input', function(){
        $(this).parent().parent().find('.label-input').val($(this).find('option:selected').text());
    });

    $('.forms-items-add').on('click', function(){
        getWidget();
    });

    function getWidget() {
        $.ajax({
            method: 'POST',
            url: '".Url::to(['/ezmodules/ezmodule-tab/get-widget', 'view'=>'/ezmodule-widget/assets/core_ezmap/_form_item'])."',
            dataType: 'HTML',
            success: function(result, textStatus) {
                $('#dad-item-box').append(result);
                //$('#dad-item-box').removeClass('dads-children');
            }
        });
    }
");
?>