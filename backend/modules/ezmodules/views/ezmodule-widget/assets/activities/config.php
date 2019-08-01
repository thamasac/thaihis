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
    <div class="col-md-6">
        <?= Html::label(Yii::t('ezmodule', 'Title'), 'options[title]', ['class' => 'control-label']) ?>
        <?= Html::textInput('options[title]', (isset($options['title'])?$options['title']:Yii::t('ezmodule', 'Title')), ['class'=>'form-control'])?>
    </div>

</div>
<div class="form-group row">
    <div class="col-md-6 ">
      <?php
      $attrname_ezf_id = 'options[ezf_id]';
      $value_ezf_id = isset($options['ezf_id'])?$options['ezf_id']:'';
      
      ?>
        <?= Html::label(Yii::t('ezmodule', 'Form'), $attrname_ezf_id, ['class' => 'control-label']) ?>
        <?php 
        echo kartik\select2\Select2::widget([
            'name' => $attrname_ezf_id,
            'value'=> $value_ezf_id,
            'options' => ['placeholder' => Yii::t('ezmodule', 'Form'), 'id'=>'config_ezf_id'],
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
        <?= Html::label(Yii::t('ezmodule', 'Fields'), $attrname_fields, ['class' => 'control-label']) ?>
        <div id="ref_field_box">
            
        </div>
    </div>
</div>


<?php
$this->registerJS("
    fields($('#config_ezf_id').val());
    pic_fields($('#config_ezf_id').val());
    fields_search($('#config_ezf_id').val());
    
    $('#config_ezf_id').on('change',function(){
      var ezf_id = $(this).val();
      fields(ezf_id);
      fields_search(ezf_id);
      pic_fields(ezf_id);
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
    
    
");
?>
