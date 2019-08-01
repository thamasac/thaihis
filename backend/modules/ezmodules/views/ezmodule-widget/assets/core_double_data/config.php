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
$itemsEzform = \backend\modules\ezforms2\classes\EzfQuery::getEzformCoDevCRF();

?>

<div class="modal-header" style="margin-bottom: 15px;">
    <h4 class="modal-title" ><?= Yii::t('ezmodule', 'Widget Config')?></h4>
</div>

<!--config start-->
  <div class="form-group row">
      <div class="col-md-12 ">
        <?= Html::radioList('options[type]', isset($options['type'])?$options['type']:0, ['All CRF', 'Manual'], ['options'=>['id'=>'config_type']])?>
    </div>
</div>
<div class="form-group row" id="config-form" style="<?=isset($options['type']) && $options['type']==1?'display: block;':'display: none;'?>">
    <div class="col-md-12 ">
      <?php
      $attrname_ezf_id = 'options[ezf_id]';
      $value_ezf_id = isset($options['ezf_id'])?$options['ezf_id']:'';
      ?>
        <?= Html::label(Yii::t('ezform', 'Form List'), $attrname_ezf_id, ['class' => 'control-label']) ?>
        <?php 
        echo kartik\select2\Select2::widget([
            'name' => $attrname_ezf_id,
            'value'=> $value_ezf_id,
            'options' => ['placeholder' => Yii::t('ezform', 'Select Form'), 'id'=>'config_ezf_id', 'multiple' => true],
            'data' => ArrayHelper::map($itemsEzform,'ezf_id','ezf_name'),
            'pluginOptions' => [
                'allowClear' => true,
                'tags' => true,
                'tokenSeparators' => [',', ' '],
            ],
        ]);
        ?>
    </div>
    
</div>

<!--config end-->

<?php \richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]); ?>
<script>
    // JS script
    $('input[name="options[type]"]').on('change',function(){
      if($(this).val()==1){
            $('#config-form').show();
        } else {
            $('#config-form').hide();
        }
    });
</script>
<?php \richardfan\widget\JSRegister::end(); ?>