<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div id="history-search">
  <?php  
  $form = ActiveForm::begin([
              'id' => 'search-history',
              'action' => ['/pis/pis-item-order/order-history', 'ptid' => $ptid
            , 'visit_id' => $visit_id
            , 'order_id' => $order_id
            , 'right_code' => $right_code],
              'enableClientValidation' => false,
  ]);

  echo \kartik\daterange\DateRangePicker::widget([
//      'model' => $model,
//      'attribute' => 'visit_date',
      'id'=>'visit-date-id',
      'name'=>'visit_date',
      'convertFormat' => true,
      //'useWithAddon'=>true,
      'options' => ['id' => 'dr_visit_date', 'class' => 'form-control', 'placeholder' => Yii::t('patient', 'Date')],
      'pluginOptions' => [
          'locale' => [
              'format' => 'd-m-Y',
              'separator' => ',',
          //'language'=>'TH',
          ],
      //'opens'=>'left'
      ]
  ]);
  ?>

  <?php ActiveForm::end(); ?>
</div>
<?php
$this->registerJs(" 
$('form#search-history').on('beforeSubmit', function(e) {
    actionSearchHistory();  
    return false;
});

$('form#search-history input').on('keyup', function(e) {
   actionSearchHistory();
});

function actionSearchHistory(){
    let form = $('form#search-package')
    let q = $('#package_name').val();
    $.get(form.attr('action'),{q:q}).done(function(result) {
	$('#$reloadDiv').html(result);
    }).fail(function() {
	" . \appxq\sdii\helpers\SDNoty::show("'" . appxq\sdii\helpers\SDHtml::getMsgError() . "Server Error'", '"error"') . "
	console.log('server error');
    });
    
    return false;
}
");
?>
