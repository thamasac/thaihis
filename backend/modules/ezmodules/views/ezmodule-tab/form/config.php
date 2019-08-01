<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 
 * จำเป็นต้องมี options[render] และถ้ามีการส่งค่า options[params]
 */
$options = isset($model->options)?\appxq\sdii\utils\SDUtility::string2Array($model->options):[];

$itemsEzform = \backend\modules\ezforms2\classes\EzfQuery::getEzformCoDevAll();

?>
<?=\yii\helpers\Html::hiddenInput('options[render]', '/ezmodule-tab/form/widget');?>


<div class="form-group row">
    <div class="col-md-12">
        <?= Html::label(Yii::t('ezform', 'Title to be displayed before the Widget'), 'options[params][title]', ['class' => 'control-label']) ?>
        <?= Html::textInput('options[params][title]', (isset($options['params']['title'])?$options['params']['title']:Yii::t('ezform', 'Title')), ['class'=>'form-control'])?>
    </div>
  
</div>

<div class="form-group row">
    <div class="col-md-6 ">
      <?php
      $attrname_ezf_id = 'options[params][ezf_id]';
      $value_ezf_id = isset($options['params']['ezf_id'])?$options['params']['ezf_id']:'';
      ?>
        <?= Html::label(Yii::t('ezform', 'Form to work'), $attrname_ezf_id, ['class' => 'control-label']) ?>
        <?php 
        echo kartik\select2\Select2::widget([
            'name' => $attrname_ezf_id,
            'value'=> $value_ezf_id,
            'options' => ['placeholder' => Yii::t('ezform', 'Select Form'), 'id'=>'config_tab_ezf_id'],
            'data' => ArrayHelper::map($itemsEzform,'ezf_id','ezf_name'),
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);
        ?>
    </div>
    <div class="col-md-6 sdbox-col">
      <?php
      $attrname_fields = 'options[params][fields]';
      $value_fields_json = isset($options['params']['fields']) && is_array($options['params']['fields'])?\appxq\sdii\utils\SDUtility::array2String($options['params']['fields']):'{}';
      ?>
        <?= Html::label(Yii::t('ezform', 'Fields to display'), $attrname_fields, ['class' => 'control-label']) ?>
        <div id="ref_field_box_tab">
            
        </div>
    </div>
</div>

<div class="form-group row">
    <div class="col-md-3 ">
      <?php
      $attrname_pagesize = 'options[params][pagesize]';
      $value_pagesize = isset($options['params']['pagesize'])?$options['params']['pagesize']:50;
      ?>
        <?= Html::label(Yii::t('ezform', 'Page Size'), $attrname_pagesize, ['class' => 'control-label']) ?>
        <?php 
        echo Html::textInput($attrname_pagesize, $value_pagesize, ['class' => 'form-control ', 'type'=>'number']);
        ?>
    </div>
    <div class="col-md-6 sdbox-col">
      <?php
      $attrname_order = 'options[params][order]';
      $value_order = isset($options['params']['order']) && is_array($options['params']['order'])?\appxq\sdii\utils\SDUtility::array2String($options['params']['order']):'{}';
      ?>
        <?= Html::label(Yii::t('ezform', 'Order By'), $attrname_order, ['class' => 'control-label']) ?>
        <div id="order_field_box_tab">
            
        </div>
    </div>
    <div class="col-md-3 sdbox-col">
      
      <?php
      $attrname_order_by = 'options[params][order_by]';
      $value_order_by = isset($options['params']['order_by'])?$options['params']['order_by']:3;//4-ASC - 3-DESC
      ?>
        <?= Html::label(Yii::t('ezform', 'Ordering Methods'), $attrname_order_by, ['class' => 'control-label']) ?>
        <?php 
        echo Html::dropDownList($attrname_order_by, $value_order_by, [4=>'Ascending', 3=>'Descending'], ['class' => 'form-control ']);
        ?>
    </div>
   
</div>

<div class="modal-header" style="margin-bottom: 15px;">
    <h4 class="modal-title"><?= Yii::t('ezmodule', 'Custom Header')?></h4>
</div>
<div class="form-group">
  <div class="row">
    <div class="col-md-3"><label><?= Yii::t('ezform', 'Varname')?></label></div>
    <div class="col-md-6 sdbox-col"><label><?= Yii::t('ezform', 'Label')?></label></div>
    <div class="col-md-2"></div>
  </div>
  <div id="header-item-tab">
  <?php
  if(isset($options['params']['header']) && is_array($options['params']['header']) && !empty($options['params']['header'])){
      foreach ($options['params']['header'] as $key_header => $value_header) {
          ?>
          <div id="<?=$key_header?>" class="row" style="margin-bottom: 10px;">
            <div class="col-md-3"><input type="text" class="form-control varname-input" name="options[params][header][<?=$key_header?>][varname]" value="<?=$value_header['varname']?>"></div>
            <div class="col-md-6 sdbox-col"><input type="text" class="form-control label-input" name="options[params][header][<?=$key_header?>][label]" value="<?=$value_header['label']?>"></div>
            <div class="col-md-2 sdbox-col"><a href="#" class="header-items-del btn btn-danger"><i class="glyphicon glyphicon-remove"></i></a></div>
          </div>
          <?php
      }
  }
  ?>
  </div>
  <div class="row">
      <div class="col-md-4">
        <a href="#" class="header-tab-add btn btn-success"><i class="glyphicon glyphicon-plus"></i> <?= Yii::t('ezmodule', 'Custom Header')?></a>
    </div>
      
  </div>
  
  
</div>

<div class="form-group row">
  <div class="col-md-4">
      <?= Html::hiddenInput('options[params][default_column]', 0)?>
      <?= backend\modules\ezforms2\classes\EzformWidget::checkbox('options[params][default_column]', isset($options['params']['default_column'])?$options['params']['default_column']:1, ['label'=> Yii::t('ezmodule', 'Enable Default Column')])?>
  </div>
</div>

<div class="form-group row">
  <div class="col-md-4">
      <?= Html::hiddenInput('options[params][db2]', 0)?>
        <?php
      $attrname_db2 = 'options[params][db2]';
      $value_db2 = isset($options['params']['db2'])?$options['params']['db2']:0;
      ?>
      <div id="db2_field_box_tab">
            
      </div>
  </div>
</div>

<?php
$this->registerJS("
    fields($('#config_tab_ezf_id').val());
    orderfields($('#config_tab_ezf_id').val());
    db2fields($('#config_tab_ezf_id').val());
    
    $('#config_tab_ezf_id').on('change',function(){
      var ezf_id = $(this).val();
      fields(ezf_id);
      orderfields(ezf_id);
      db2fields(ezf_id);
    });
    
    function fields(ezf_id){
        var value = ".$value_fields_json.";
        $.post('".Url::to(['/ezforms2/target/get-fields'])."',{ ezf_id: ezf_id, multiple:1, name: '{$attrname_fields}', value: value ,id:'config_tab_fields'}
          ).done(function(result){
             $('#ref_field_box_tab').html(result);
          }).fail(function(){
              ". \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
    function orderfields(ezf_id){
        var value = ".$value_order.";
        $.post('".Url::to(['/ezforms2/target/get-fields'])."',{ ezf_id: ezf_id, multiple:1, name: '{$attrname_order}', value: value ,id:'config_tab_order_fields'}
          ).done(function(result){
             $('#order_field_box_tab').html(result);
          }).fail(function(){
              ". \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
    function db2fields(ezf_id){
        var value = ".$value_db2.";
        $.post('".Url::to(['/ezmodules/ezmodule-widget/get-db2'])."',{ ezf_id: ezf_id, name: '{$attrname_db2}', value: value ,id:'config_tab_db2r_fields'}
          ).done(function(result){
             $('#db2_field_box_tab').html(result);
          }).fail(function(){
              ". \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
    $('#header-item-tab').on('click', '.header-items-del', function(){
        $(this).parent().parent().remove();
    });
    
    $('.header-tab-add').on('click', function(){
        getWidget_tab();
    });

    function getWidget_tab() {
        $.ajax({
            method: 'POST',
            url: '".Url::to(['/ezmodules/ezmodule-tab/get-widget', 'view'=>'form/_form_header'])."',
            dataType: 'HTML',
            success: function(result, textStatus) {
                $('#header-item-tab').append(result);
            }
        });
    }
");
?>