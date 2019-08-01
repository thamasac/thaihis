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
        <?= Html::label(Yii::t('ezform', 'Label'), 'options[label]', ['class' => 'control-label']) ?>
        <?= Html::textInput('options[label]', (isset($options['label'])?$options['label']:Yii::t('ezform', 'Add New')), ['class'=>'form-control'])?>
    </div>
</div>
<div class="alert alert-info" role="alert"> 
    <strong>Variable : </strong>
    <span data-content="{module}" class="btn btn-xs btn-warning btn-content" style="margin-top: 5px">{module}</span>
    <span data-content="{reloadDiv}" class="btn btn-xs btn-warning btn-content" style="margin-top: 5px">{reloadDiv}</span>
    <span data-content="{modal}" class="btn btn-xs btn-warning btn-content" style="margin-top: 5px">{modal}</span>
    <span data-content="{target}" class="btn btn-xs btn-warning btn-content" style="margin-top: 5px">{target}</span>
    <span data-content="{dataid}" class="btn btn-xs btn-warning btn-content" style="margin-top: 5px">{dataid}</span>
</div>

<div class="form-group row">
    <div class="col-md-12">
        <?= Html::label(Yii::t('ezform', 'Query String ($_GET)'), 'options[query_params]', ['class' => 'control-label']) ?>
        <?= Html::textInput('options[query_params]', (isset($options['query_params'])?$options['query_params']:'target={target}&dataid={dataid}'), ['class'=>'form-control'])?>
    </div>
</div>
<div class="form-group row">
    <div class="col-md-6">
        <?= Html::label(Yii::t('ezform', 'Parent Name get from query string at URL'), 'options[target]', ['class' => 'control-label']) ?>
        <?= Html::textInput('options[target]', (isset($options['target'])?$options['target']:'target'), ['class'=>'form-control'])?>
    </div>
    <div class="col-md-6 sdbox-col">
        <?= Html::label(Yii::t('ezform', 'DataID Name get from query string at URL'), 'options[dataid]', ['class' => 'control-label']) ?>
        <?= Html::textInput('options[dataid]', (isset($options['dataid'])?$options['dataid']:'dataid'), ['class'=>'form-control'])?>
    </div>
</div>
<div class="form-group row">
    <div class="col-md-6">
        <?= Html::label(Yii::t('ezform', 'Theme Color'), 'options[theme]', ['class' => 'control-label']) ?>
        <?= Html::dropDownList('options[theme]', (isset($options['theme'])?$options['theme']:'btn-success'), ['btn-default'=>'Default', 'btn-primary'=>'Primary', 'btn-success'=>'Success', 'btn-info'=>'Info', 'btn-warning'=>'Warning', 'btn-danger'=>'Danger', 'btn-link'=>'Link'], ['class'=>'form-control'])?>
    </div>
  <div class="col-md-6 sdbox-col">
        <?= Html::label(Yii::t('ezform', 'Size'), 'options[size]', ['class' => 'control-label']) ?>
        <?= Html::dropDownList('options[size]', (isset($options['size'])?$options['size']:''), [''=>'Default', 'btn-lg'=>'Large', 'btn-sm'=>'Small', 'btn-xs'=>'Extra small'], ['class'=>'form-control'])?>
    </div>
</div>

<div class="form-group row">
    <div class="col-md-6 ">
      <?php
      $attrname_date_fields = 'options[initdate]';
      $value_date_fields = isset($options['initdate'])?$options['initdate']:'';
      ?>
        <?= Html::label(Yii::t('ezform', 'Initiated by date field'), $attrname_date_fields, ['class' => 'control-label']) ?>
        <div id="ref_date_field_box">
            
        </div>
    </div>
    <div class="col-md-6 sdbox-col" >
      <?= yii\bootstrap\Html::hiddenInput('options[show]', 0)?>
        <?= \backend\modules\ezforms2\classes\EzformWidget::checkbox('options[show]', (isset($options['show'])?$options['show']:0), ['label'=>'Show when choosing a Parent'])?>
    </div>
  
</div>


<?php $this->registerJS("
    date_fields($('#config_ezf_id').val());
    
    $('#config_ezf_id').on('change',function(){
      var ezf_id = $(this).val();
      date_fields(ezf_id);
    });
    
    function date_fields(ezf_id){
        var value = '".$value_date_fields."';
        $.post('".Url::to(['/ezforms2/target/get-fields', 'type' => backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String([64,63])])."',{ ezf_id: ezf_id, multiple:0, name: '{$attrname_date_fields}', value: value ,id:'config_date_fields'}
          ).done(function(result){
             $('#ref_date_field_box').html(result);
          }).fail(function(){
              ". \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
    
");
?>
