<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div id="ezf-search" style="padding: 5px;">
  <?php
  $form = ActiveForm::begin([
              'id' => 'search-' . $model->formName(),
              'action' => ['/patient/cashier2/que-view', 'reloadDiv' => $reloadDiv],
              'method' => 'get',
  ]);
  ?>
  <?=
  kartik\widgets\DatePicker::widget([
      'model' => $model,
      'attribute' => 'create_date',
      'type' => kartik\widgets\DatePicker::TYPE_INPUT,
      'options' => ['class' => 'form-control', 'style' => 'margin-bottom: 5px;'],
      'pluginOptions' => [
          'autoclose' => true,
          'format' => 'yyyy-mm-dd',
          'todayHighlight' => true,
      ]
  ]);
  ?>

  <?= Html::activeDropDownList($model, 'order_tran_status', ['1' => 'ยังไม่ชำระ', '2' => 'ชำระแล้ว'], ['class' => 'form-control', 'style' => 'margin-bottom: 5px;']); ?>

  <?= Html::activeTextInput($model, 'order_tran_code', ['class' => 'form-control search-query', 'placeholder' => Yii::t('patient', 'Find the form name or hn.')]) ?> 

  <?php ActiveForm::end(); ?>
</div>
<?php
$this->registerJs(" 
$('form#search-{$model->formName()}').on('change', function(e) {
    actionGet();
    //$(this).submit();   
    return false;
});

$('form#search-{$model->formName()}').on('beforeSubmit', function(e) {
    actionGet();
    return false;
});

function actionGet(){
    var url = $('#search-{$model->formName()}').attr('action');
    $.get(url,$('#search-{$model->formName()}').serialize()).done(function(result) {
        $('#$reloadDiv').html(result);
        $('#ez1504537671028647300-order_tran_code').select();
    }).fail(function() {
            " . SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') . "
            console.log('server error');
    });
}
$('#ez1504537671028647300-order_tran_code').on('click',function(){
    $(this).select();
});

$('#ez1504537671028647300-order_tran_code').select();
");
?>
