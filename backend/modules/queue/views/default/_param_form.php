<?php

use kartik\select2\Select2;
use yii\helpers\Html;
use appxq\sdii\utils\SDUtility;

$id = SDUtility::getMillisecTime();
$param_active = isset($param_active) ? $param_active : '';
?>

<div class="col-md-12 divMainParam" style="margin-top:2%">
  <div class="col-md-1">
      <?php
      echo Html::checkbox('options[param][' . $id . '][param_active]', 0,['class'=>'check_box_active']);
      ?>
  </div>
  <div class="col-md-4">
      <?php echo Html::textInput('options[param][' . $id . '][name]', $param_name, ['class' => 'form-control']); ?>
  </div>
  <div class="col-md-5">
      <?php
      echo Select2::widget([
          'id' => 'select-value-param-' . SDUtility::getMillisecTime(),
          'name' => 'options[param][' . $id . '][value]',
          'value' => $param_value,
          'data' => $dataForm,
          'options' => ['placeholder' => Yii::t('ezform', 'Select field ...')],
          'pluginOptions' => [
              'allowClear' => true,
          ]
      ]);
      ?>
  </div>
  <div class="col-md-2">
    <?php echo Html::tag('div', '<i class="glyphicon glyphicon-remove"></i>', ['class' => 'btn btn-danger btn-remove-param']) ?>
  </div>

</div>

