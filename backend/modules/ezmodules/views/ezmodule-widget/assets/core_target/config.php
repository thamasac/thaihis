<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$options = isset($model->options)?\appxq\sdii\utils\SDUtility::string2Array($model->options):[];

$target = backend\modules\ezforms2\classes\EzfQuery::getTargetOne($ezf_id);
if($target){
    $itemsEzform = \backend\modules\ezforms2\classes\EzfQuery::getEzformRef($ezf_id, $target['parent_ezf_id']);
} else {
    $itemsEzform = \backend\modules\ezforms2\classes\EzfQuery::getEzformAll($ezf_id);
}

?>

<div class="modal-header" style="margin-bottom: 15px;">
    <h4 class="modal-title" ><?= Yii::t('ezmodule', 'Widget Config')?></h4>
</div>

<div class="form-group row">
    <div class="col-md-6 ">
      <?php
      $attrname_ezf_id = 'options[ezf_id]';
      $value_ezf_id = isset($options['ezf_id'])?$options['ezf_id']:'';
      
      ?>
        <?= Html::label(Yii::t('ezform', 'Form'), $attrname_ezf_id, ['class' => 'control-label']) ?>
        <?php 
        echo kartik\select2\Select2::widget([
            'name' => $attrname_ezf_id,
            'value'=> $value_ezf_id,
            'options' => ['placeholder' => Yii::t('ezform', 'Form'), 'id'=>'config_ezf_id'],
            'data' => ArrayHelper::map($itemsEzform,'ezf_id','ezf_name'),
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
    <div class="col-md-6 sdbox-col">
      <?php
      $attrname_fields = 'options[fields]';
      $value_fields = isset($options['fields']) && is_array($options['fields'])?\appxq\sdii\utils\SDUtility::array2String($options['fields']):'{}';
      ?>
        <?= Html::label(Yii::t('ezform', 'Fields'), $attrname_fields, ['class' => 'control-label']) ?>
        <div id="ref_field_box">
            
        </div>
    </div>
</div>

<div class="form-group row">
    <div class="col-md-6">
      <?php
      $attrname_fields_search = 'options[fields_search]';
      $value_fields_search = isset($options['fields_search']) && is_array($options['fields_search'])?\appxq\sdii\utils\SDUtility::array2String($options['fields_search']):'{}';
      ?>
        <?= Html::label(Yii::t('ezform', 'Fields Search'), $attrname_fields_search, ['class' => 'control-label']) ?>
        <div id="fields_search_box">
            
        </div>
    </div>
    <div class="col-md-3 sdbox-col">
        <?php
        $attrname_image_field = 'options[image_field]';
        $value_image_field = isset($options['image_field'])?$options['image_field']:'';
        ?>
        <?= Html::label(Yii::t('ezform', 'Image Field (if any)'), $attrname_image_field, ['class' => 'control-label']) ?>
        <div id="pic_field_box">
            
        </div>
    </div>
  <div class="col-md-3 sdbox-col">
        <?php
        $attrname_age_field = 'options[age_field]';
        $value_age_field = isset($options['age_field'])?$options['age_field']:'';
        ?>
        <?= Html::label(Yii::t('ezform', 'Age Field').' {fix_age}', $attrname_age_field, ['class' => 'control-label']) ?>
        <div id="age_field_box">
            
        </div>
    </div>
</div>

<div class="form-group row">
    <div class="col-md-6">
      <?php
      $attrname_placeholder = 'options[placeholder]';
      $value_placeholder = isset($options['placeholder'])?$options['placeholder']:'Search ...';//4-ASC - 3-DESC
      ?>
        <?= Html::label(Yii::t('ezform', 'Short hint'), $attrname_placeholder, ['class' => 'control-label']) ?>
        <?php 
        echo Html::textInput($attrname_placeholder, $value_placeholder, ['class' => 'form-control ']);
        ?>
    </div>
</div>

<div class="form-group">
    <?= Html::label(Yii::t('ezform', 'Items Template').' <span class="btn btn-xs btn-info btn-his">HIS</span>', 'options[template_items]', ['class' => 'control-label']) ?>
   <?= Html::textarea('options[template_items]', isset($options['template_items'])?$options['template_items']:'', ['id'=>'template_items', 'class' => 'form-control', 'row'=>3])?>
</div>

<div class="form-group">
    <?= Html::label(Yii::t('ezform', 'Selection Template'), 'options[template_selection]', ['class' => 'control-label']) ?>
   <?= Html::textarea('options[template_selection]', isset($options['template_selection'])?$options['template_selection']:'', ['id'=>'template_selection', 'class' => 'form-control', 'row'=>3])?>
</div>

<?php
$this->registerJS("
    fields($('#config_ezf_id').val());
    pic_fields($('#config_ezf_id').val());
    fields_search($('#config_ezf_id').val());
    age_fields($('#config_ezf_id').val());
 
    $('#config_ezf_id').on('change',function(){
      var ezf_id = $(this).val();
      fields(ezf_id);
      fields_search(ezf_id);
      pic_fields(ezf_id);
      age_fields(ezf_id);
    });
    
    var his_patient = '<div class=\"row\">'+
                '<div class=\"col-sm-12\">'+
                  '<div style=\"margin-bottom: 5px;font-size: 17px;\"><strong>HN : {hn}</strong> Name : {fname} {lname}</div>'+
                  '<div style=\"margin-bottom: 5px;font-size: 15px;\"><strong>CID : </strong>{cid}</div>'+
                  '<div style=\"margin-bottom: 5px;font-size: 15px;\"><strong>Birthday : </strong><span>{bod}</span> <strong>Age : </strong>{fix_age}</div>'+
                '</div>'+
              '</div>';
              
    $('.btn-his').on('click',function(){
        $('#template_items').val(his_patient);
        $('#template_selection').val('<strong>HN : {hn}</strong> Name : {fname} {lname} <strong>CID : </strong>{cid}');
    });
    
    function fields(ezf_id){
        var value = ".$value_fields.";
        $.post('".Url::to(['/ezforms2/target/get-fields'])."',{ ezf_id: ezf_id, multiple:1, name: '{$attrname_fields}', value: value ,id:'config_fields'}
          ).done(function(result){
             $('#ref_field_box').html(result);
          }).fail(function(){
              ". \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
    function fields_search(ezf_id){
        var value = ".$value_fields_search.";
        $.post('".Url::to(['/ezforms2/target/get-fields'])."',{ ezf_id: ezf_id, multiple:1, name: '{$attrname_fields_search}', value: value ,id:'config_fields_search'}
          ).done(function(result){
             $('#fields_search_box').html(result);
          }).fail(function(){
              ". \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
    function pic_fields(ezf_id){
        var value = '{$value_image_field}';
        $.post('".Url::to(['/ezforms2/target/get-fields'])."',{ ezf_id: ezf_id, multiple:0, name: '{$attrname_image_field}', value: value ,id:'config_pic_fields'}
          ).done(function(result){
             $('#pic_field_box').html(result);
          }).fail(function(){
              ". \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
    function age_fields(ezf_id){
        var value = '{$value_age_field}';
        $.post('".Url::to(['/ezforms2/target/get-fields'])."',{ ezf_id: ezf_id, multiple:0, name: '{$attrname_age_field}', value: value ,id:'config_age_fields'}
          ).done(function(result){
             $('#age_field_box').html(result);
          }).fail(function(){
              ". \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
");
?>
