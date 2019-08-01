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
$itemsEzform = \backend\modules\ezforms2\classes\EzfQuery::getEzformCoDevAll();

?>

<div class="modal-header" style="margin-bottom: 15px;">
    <h4 class="modal-title" ><?= Yii::t('ezmodule', 'Widget Config')?></h4>
</div>

<!--config start-->

<div class="form-group row">
    <div class="col-md-12">
        <?= Html::label(Yii::t('ezform', 'Title to be displayed before the Widget'), 'options[title]', ['class' => 'control-label']) ?>
        <?= Html::textInput('options[title]', (isset($options['title'])?$options['title']:Yii::t('ezform', 'Title')), ['class'=>'form-control'])?>
    </div>
  
</div>

<div class="form-group row">
    <div class="col-md-6 ">
      <?php
      $attrname_ezf_id = 'options[ezf_id]';
      $value_ezf_id = isset($options['ezf_id'])?$options['ezf_id']:'';
      ?>
        <?= Html::label(Yii::t('ezform', 'Form to work'), $attrname_ezf_id, ['class' => 'control-label']) ?>
        <?php 
        echo kartik\select2\Select2::widget([
            'name' => $attrname_ezf_id,
            'value'=> $value_ezf_id,
            'options' => ['placeholder' => Yii::t('ezform', 'Select Form'), 'id'=>'config_ezf_id'],
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
      $value_fields_json = isset($options['fields']) && is_array($options['fields'])?\appxq\sdii\utils\SDUtility::array2String($options['fields']):'{}';
      ?>
        <?= Html::label(Yii::t('ezform', 'Fields to display'), $attrname_fields, ['class' => 'control-label']) ?>
        <div id="ref_field_box">
            
        </div>
    </div>
</div>

<div class="form-group row">
    <div class="col-md-3 ">
      <?php
      $attrname_pagesize = 'options[pagesize]';
      $value_pagesize = isset($options['pagesize'])?$options['pagesize']:50;
      ?>
        <?= Html::label(Yii::t('ezform', 'Page Size'), $attrname_pagesize, ['class' => 'control-label']) ?>
        <?php 
        echo Html::textInput($attrname_pagesize, $value_pagesize, ['class' => 'form-control ', 'type'=>'number']);
        ?>
    </div>
    <div class="col-md-6 sdbox-col">
      <?php
      $attrname_order = 'options[order]';
      $value_order = isset($options['order']) && is_array($options['order'])?\appxq\sdii\utils\SDUtility::array2String($options['order']):'{}';
      ?>
        <?= Html::label(Yii::t('ezform', 'Order by'), $attrname_order, ['class' => 'control-label']) ?>
        <div id="order_field_box">
            
        </div>
      
    </div>
    <div class="col-md-3 sdbox-col">
      <?php
      $attrname_order_by = 'options[order_by]';
      $value_order_by = isset($options['order_by'])?$options['order_by']:3;//4-ASC - 3-DESC
      ?>
        <?= Html::label(Yii::t('ezform', 'Ordering Methods'), $attrname_order_by, ['class' => 'control-label']) ?>
        <?php 
        echo Html::dropDownList($attrname_order_by, $value_order_by, [4=>'Ascending', 3=>'Descending'], ['class' => 'form-control ']);
        ?>
    </div>
   
</div>


<div class="form-group row">
  <div class="col-md-3">
      <?= Html::hiddenInput('options[default_column]', 0)?>
      <?= backend\modules\ezforms2\classes\EzformWidget::checkbox('options[default_column]', isset($options['default_column'])?$options['default_column']:1, ['label'=> Yii::t('ezmodule', 'Enable Default Column')])?>
  </div>
  <div class="col-md-6 sdbox-col">
      <?= Html::hiddenInput('options[disabled]', 0)?>
      <?= backend\modules\ezforms2\classes\EzformWidget::checkbox('options[disabled]', isset($options['disabled'])?$options['disabled']:0, ['label'=> Yii::t('ezmodule', 'View Only')])?>
  </div>
    <div class="col-md-3 sdbox-col">
        <?= Html::label(Yii::t('ezform', 'Parent Name'), 'options[target]', ['class' => 'control-label']) ?>
        <?= Html::textInput('options[target]', (isset($options['target'])?$options['target']:'target'), ['class'=>'form-control'])?>
    </div>
</div>

<div class="form-group row">
  <div class="col-md-4">
      <?= Html::hiddenInput('options[db2]', 0)?>
        <?php
      $attrname_db2 = 'options[db2]';
      $value_db2 = isset($options['db2'])?$options['db2']:0;
      ?>
      <div id="db2_field_box">
            
      </div>
  </div>
</div>

<div class="modal-header" style="margin-bottom: 15px;">
    <h4 class="modal-title"><?= Yii::t('ezmodule', 'Custom Header')?></h4>
</div>
<div class="form-group">
  <div class="row">
    <div class="col-md-3"><label><?= Yii::t('ezform', 'Varname')?></label></div>
    <div class="col-md-6 sdbox-col"><label><?= Yii::t('ezform', 'Label')?></label></div>
    <div class="col-md-2 sdbox-col"></div>
  </div>
  <div id="header-item-box">
  <?php
  if(isset($options['header']) && is_array($options['header']) && !empty($options['header'])){
      foreach ($options['header'] as $key_header => $value_header) {
          ?>
          <div id="<?=$key_header?>" class="row" style="margin-bottom: 10px;">
            <div class="col-md-3"><input type="text" class="form-control varname-input" name="options[header][<?=$key_header?>][varname]" value="<?= isset($value_header['varname'])?$value_header['varname']:''?>"></div>
            <div class="col-md-6 sdbox-col"><input type="text" class="form-control label-input" name="options[header][<?=$key_header?>][label]" value="<?= isset($value_header['label'])?$value_header['label']:''?>"></div>
            <div class="col-md-2 sdbox-col"><a href="#" class="header-items-del btn btn-danger"><i class="glyphicon glyphicon-remove"></i></a></div>
          </div>
          <?php
      }
  }
  ?>
  </div>
  <div class="row">
      <div class="col-md-4">
        <a href="#" class="header-items-add btn btn-success"><i class="glyphicon glyphicon-plus"></i> <?= Yii::t('ezmodule', 'Custom Header')?></a>
    </div>
      
  </div>
  
  
</div>

<div class="modal-header" style="margin-bottom: 15px;">
  <h4 class="modal-title"><?= Yii::t('ezmodule', 'Actions Column')?></h4> 
</div>
<div class="form-group">
  <div class="alert alert-info">
    <strong>Variable : </strong> {ezf_id} {reloadDiv} {modal} {db2} {sitecode} {department} {user} ... {[data_fields]} <br>
    <strong>Button : </strong> <?= Html::encode('<button class="btn btn-default btn-xs btn-action " data-url="/ezforms2/btn-action/update-data?ezf_id={ezf_id}&id={id}&field=&value=">Click</button>')?><br>
    <strong>Link : </strong> <?= Html::encode('<a href="#" class="btn btn-default btn-xs btn-action " data-url="/ezforms2/btn-action/update-data?ezf_id={ezf_id}&id={id}&field=&value=">Click</a>')?>
  </div>
  <div class="row">
    <div class="col-md-6"><label><?= Yii::t('ezform', 'Actions')?> </label></div>
    <div class="col-md-4 sdbox-col"><label><?= Yii::t('ezform', 'Show data conditions')?></label></div>
    <div class="col-md-2"></div>
  </div>
  <div id="actions-item-box">
  <?php
  if(isset($options['actions']) && is_array($options['actions']) && !empty($options['actions'])){
      foreach ($options['actions'] as $key_action => $value_action) {
          ?>
          <div id="<?=$key_action?>" class="row" style="margin-bottom: 10px;">
            <div class="col-md-6"><input type="text" class="form-control action-input" name="options[actions][<?=$key_action?>][action]" value="<?= isset($value_action['action'])?Html::encode($value_action['action']):''?>"></div>
            <div class="col-md-4 sdbox-col"><input type="text" class="form-control cond-input" name="options[actions][<?=$key_action?>][cond]" value="<?= isset($value_action['cond'])?$value_action['cond']:''?>"></div>
            <div class="col-md-2 sdbox-col"><a href="#" class="action-items-del btn btn-danger"><i class="glyphicon glyphicon-remove"></i></a></div>
          </div>
          <?php
      }
  }
  ?>
  </div>
  <div class="row">
      <div class="col-md-4">
        <a href="#" class="action-items-add btn btn-success"><i class="glyphicon glyphicon-plus"></i> <?= Yii::t('ezmodule', 'Actions Column')?></a>
    </div>
      
  </div>
  
  
</div>

<?php
$this->registerJS("
    fields($('#config_ezf_id').val());
    orderfields($('#config_ezf_id').val());
    db2fields($('#config_ezf_id').val());
    
    $('#config_ezf_id').on('change',function(){
      var ezf_id = $(this).val();
      fields(ezf_id);
      orderfields(ezf_id);
      db2fields(ezf_id);
    });
    
    function fields(ezf_id){
        var value = ".$value_fields_json.";
        $.post('".Url::to(['/ezforms2/target/get-fields'])."',{ ezf_id: ezf_id, multiple:1, name: '{$attrname_fields}', value: value ,id:'config_fields'}
          ).done(function(result){
             $('#ref_field_box').html(result);
          }).fail(function(){
              ". \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
    function orderfields(ezf_id){
        var value = ".$value_order.";
        $.post('".Url::to(['/ezforms2/target/get-fields'])."',{ ezf_id: ezf_id, multiple:1, name: '{$attrname_order}', value: value ,id:'config_order_fields'}
          ).done(function(result){
             $('#order_field_box').html(result);
          }).fail(function(){
              ". \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
    function db2fields(ezf_id){
        var value = ".$value_db2.";
        $.post('".Url::to(['/ezmodules/ezmodule-widget/get-db2'])."',{ ezf_id: ezf_id, name: '{$attrname_db2}', value: value ,id:'config_db2r_fields'}
          ).done(function(result){
             $('#db2_field_box').html(result);
          }).fail(function(){
              ". \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
    $('#actions-item-box').on('click', '.action-items-del', function(){
        $(this).parent().parent().remove();
    });
    
    $('.action-items-add').on('click', function(){
        $.ajax({
            method: 'POST',
            url: '".Url::to(['/ezmodules/ezmodule-tab/get-widget', 'view'=>'/ezmodule-widget/assets/datalist/_form_action'])."',
            dataType: 'HTML',
            success: function(result, textStatus) {
                $('#actions-item-box').append(result);
            }
        });
    });
    
    $('#header-item-box').on('click', '.header-items-del', function(){
        $(this).parent().parent().remove();
    });
    
    $('.header-items-add').on('click', function(){
        getWidget();
    });

    function getWidget() {
        $.ajax({
            method: 'POST',
            url: '".Url::to(['/ezmodules/ezmodule-tab/get-widget', 'view'=>'/ezmodule-widget/assets/datalist/_form_header'])."',
            dataType: 'HTML',
            success: function(result, textStatus) {
                $('#header-item-box').append(result);
            }
        });
    }
");
?>