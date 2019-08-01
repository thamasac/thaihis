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

<!--config start-->

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
    
</div>

<div class="form-group row">
    <div class="col-md-6">
      <?php
      $attrname_sdate = 'options[sdate]';
      $value_sdate = isset($options['sdate'])?$options['sdate']:'';
      ?>
        <?= Html::label(Yii::t('ezform', 'Start Date'), $attrname_sdate, ['class' => 'control-label']) ?>
        <div id="sdate_box">
            
        </div>
    </div>
  
    <div class="col-md-6 sdbox-col">
      <?php
      $attrname_edate = 'options[edate]';
      $value_edate = isset($options['edate'])?$options['edate']:'';
      ?>
        <?= Html::label(Yii::t('ezform', 'End Date'), $attrname_edate, ['class' => 'control-label']) ?>
        <div id="edate_box">
            
        </div>
    </div>
</div>

<div class="form-group row">
    <div class="col-md-4">
      <?php
      $attrname_text = 'options[text]';
      $value_text = isset($options['text'])?$options['text']:'';
      ?>
        <?= Html::label(Yii::t('ezform', 'Text'), $attrname_text, ['class' => 'control-label']) ?>
        <div id="text_box">
            
        </div>
    </div>
  
    <div class="col-md-4 sdbox-col">
      <?php
      $attrname_caption = 'options[caption]';
      $value_caption = isset($options['caption'])?$options['caption']:'';
      ?>
        <?= Html::label(Yii::t('ezform', 'Caption'), $attrname_caption, ['class' => 'control-label']) ?>
        <div id="caption_box">
            
        </div>
    </div>
  
    <div class="col-md-4 sdbox-col">
      <?php
      $attrname_description = 'options[description]';
      $value_description = isset($options['description'])?$options['description']:'';
      ?>
        <?= Html::label(Yii::t('ezform', 'Description'), $attrname_description, ['class' => 'control-label']) ?>
        <div id="description_box">
            
        </div>
    </div>
</div>

<div class="form-group row">
    <div class="col-md-4">
        <?= Html::label(Yii::t('ezform', 'Height'), 'options[height]', ['class' => 'control-label']) ?>
        <?= Html::textInput('options[height]', (isset($options['height'])?$options['height']:400), ['class'=>'form-control'])?>
    </div>
  <?php
  $unit = ['MILLISECOND', 'SECOND', 'MINUTE', 'HOUR', 'DAY', 'WEEK', 'MONTH', 'YEAR', 'DECADE', 'CENTURY', 'MILLENNIUM'];
  
  ?>
    <div class="col-md-4 sdbox-col">
        <?= Html::label(Yii::t('ezform', 'Unit Main Band'), 'options[mUnit]', ['class' => 'control-label']) ?>
        <?= Html::dropDownList('options[mUnit]', (isset($options['mUnit'])?$options['mUnit']:\sjaakp\timeline\Timeline::WEEK), $unit, ['class'=>'form-control'])?>
    </div>
  
    <div class="col-md-4 sdbox-col">
        <?= Html::label(Yii::t('ezform', 'Unit Secundary Band'), 'options[sUnit]', ['class' => 'control-label']) ?>
        <?= Html::dropDownList('options[sUnit]', (isset($options['sUnit'])?$options['sUnit']:\sjaakp\timeline\Timeline::MONTH), $unit, ['class'=>'form-control'])?>
    </div>
</div>


<!--config end-->

<?php
$this->registerJS("
    sdateField($('#config_ezf_id').val());
    edateField($('#config_ezf_id').val());
    textField($('#config_ezf_id').val());
    captionField($('#config_ezf_id').val());
    descriptionField($('#config_ezf_id').val());
    
    $('#config_ezf_id').on('change',function(){
      var ezf_id = $(this).val();
      sdateField(ezf_id);
      edateField(ezf_id);
      textField(ezf_id);
      captionField(ezf_id);
      descriptionField(ezf_id);
    });
    
    function sdateField(ezf_id){
        var value = '".$value_sdate."';
        $.post('".Url::to(['/ezforms2/target/get-fields'])."',{ ezf_id: ezf_id, multiple:0, name: '{$attrname_sdate}', value: value ,id:'config_sdate'}
          ).done(function(result){
             $('#sdate_box').html(result);
          }).fail(function(){
              ". \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
    function edateField(ezf_id){
        var value = '".$value_edate."';
        $.post('".Url::to(['/ezforms2/target/get-fields'])."',{ ezf_id: ezf_id, multiple:0, name: '{$attrname_edate}', value: value ,id:'config_edate'}
          ).done(function(result){
             $('#edate_box').html(result);
          }).fail(function(){
              ". \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
    function textField(ezf_id){
        var value = '".$value_text."';
        $.post('".Url::to(['/ezforms2/target/get-fields'])."',{ ezf_id: ezf_id, multiple:0, name: '{$attrname_text}', value: value ,id:'config_text'}
          ).done(function(result){
             $('#text_box').html(result);
          }).fail(function(){
              ". \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
    function captionField(ezf_id){
        var value = '".$value_caption."';
        $.post('".Url::to(['/ezforms2/target/get-fields'])."',{ ezf_id: ezf_id, multiple:0, name: '{$attrname_caption}', value: value ,id:'config_caption'}
          ).done(function(result){
             $('#caption_box').html(result);
          }).fail(function(){
              ". \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
    function descriptionField(ezf_id){
        var value = '".$value_description."';
        $.post('".Url::to(['/ezforms2/target/get-fields'])."',{ ezf_id: ezf_id, multiple:0, name: '{$attrname_description}', value: value ,id:'config_description'}
          ).done(function(result){
             $('#description_box').html(result);
          }).fail(function(){
              ". \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
");
?>