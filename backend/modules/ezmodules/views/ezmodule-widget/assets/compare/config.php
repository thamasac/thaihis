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
$itemsEzform = \backend\modules\ezforms2\classes\EzfQuery::getEzformCoDevDb2();

?>

<div class="modal-header" style="margin-bottom: 15px;">
    <h4 class="modal-title" ><?= Yii::t('ezmodule', 'Widget Config')?></h4>
</div>

<!--config start-->

<div class="form-group row">
    <div class="col-md-12">
        <?= Html::label(Yii::t('ezform', 'Title'), 'options[title]', ['class' => 'control-label']) ?>
        <?= Html::textInput('options[title]', (isset($options['title'])?$options['title']:Yii::t('ezform', 'Title')), ['class'=>'form-control'])?>
    </div>
  
</div>

<div class="form-group row">
    <div class="col-md-12 ">
      <?php
      $attrname_ezf_id = 'options[ezf_id]';
      $value_ezf_id = isset($options['ezf_id'])?$options['ezf_id']:'';
      ?>
        <?= Html::label(Yii::t('ezform', 'Form'), $attrname_ezf_id, ['class' => 'control-label']) ?>
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
    <div class="col-md-3 sdbox-col">
      <?php
      $attrname_order_by = 'options[order_by]';
      $value_order_by = isset($options['order_by'])?$options['order_by']:3;//4-ASC - 3-DESC
      ?>
        <?= Html::label(Yii::t('ezform', 'Order By'), $attrname_order_by, ['class' => 'control-label']) ?>
        <?php 
        echo Html::dropDownList($attrname_order_by, $value_order_by, [4=>'ASC', 3=>'DESC'], ['class' => 'form-control ']);
        ?>
    </div>
   
</div>


<!--config end-->

<?php
$this->registerJS("

    
    
");
?>