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
  $url = \yii\helpers\Url::to(['/patient/medical-history/visit', 'target' => $target, 'view' => 'cpoe',
              'sitecode' => $sitecode, 'reloadDiv' => $reloadDiv,'options'=>$options,]);
  $form = ActiveForm::begin([
              'id' => 'search-history',
              'action' => $url,
  ]);
  ?>
  <?php
  echo \kartik\daterange\DateRangePicker::widget([
      'model' => $model,
      'attribute' => 'visit_date',
      'convertFormat' => true,
      //'useWithAddon'=>true,
      'options' => ['id' => 'dr_visit_date', 'class' => 'form-control','placeholder' => Yii::t('patient', 'Date')],
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
$('form#search-history').on('change', function(e) {
    var url = $(this).attr('action');
    var date = $('#dr_visit_date').val();
    if(date){
        $.get(url,{ragedate:date}).done(function (result) {
            $('#$reloadDiv').html(result);
        }).fail(function () {
            console.log('server error');
        });
        }
    return false; 
});
");
?>
