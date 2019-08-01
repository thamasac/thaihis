<?php

use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use kartik\widgets\Select2;
use yii\web\JsExpression;

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
                'action' => ['/customer/report-admin/report-xray-doctor-grid'],
                'method' => 'POST',
                'options' => ['class' => 'form-horizontal'],
    ]);
    ?>
  <div class="form-group">
    <div class="col-md-3">
      <label class="control-label">ประเภทรายงาน</label>
      <?php
      $item = ['COUNT_REPORT' => 'ยอดตอบผลแพทย์'];

      echo \kartik\widgets\Select2::widget([
          'model' => $model,
          'name' => 'EZ' . $ezf_id . '[report_status]',
          'data' => $item,
          'options' => ['placeholder' => 'รายงาน', 'multiple' => FALSE, 'class' => 'form-control'],
      ]);
      ?>
    </div>
    <div class="col-md-3">
      <label class="control-label">วันที่</label>
      <?php
      echo \kartik\daterange\DateRangePicker::widget([
          'model' => $model,
          'attribute' => 'create_date',
          'convertFormat' => true,
          //'useWithAddon'=>true,
          'options' => ['id' => 'create_date', 'class' => 'form-control', 'placeholder' => Yii::t('patient', 'Date')],
          'pluginOptions' => [
              'locale' => [
                  'format' => 'd-m-Y',
                  'separator' => ',',
              ],
          ]
      ]);
      ?>
    </div>
    <div class="col-md-1">
      <label class="control-label">ค้นหา</label>
      <?=
      Html::a('Search', Url::to(['/customer/report-admin/report-xray-doctor-grid',]), ['class' => 'form-control btn btn-success'
          , 'data-action' => 'search']);
      ?>
    </div>
  </div>

  <?php ActiveForm::end(); ?>
</div>
<?php
$this->registerJs("    
$('form#search-{$model->formName()}').on('beforeSubmit', function(e) {
    let url = $('#search-{$model->formName()}').attr('action');
    actionGet(url);
    return false;
});

$('form#search-{$model->formName()} a').on('click', function(e) {
    let url = $(this).attr('href');
    let action = $(this).attr('data-action');
    actionGet(url,action);
    return false;
});

$('.report-content').on('click', '#grid-report .pagination li a', function () { //Next 
      let url = $(this).attr('href');
      actionGet(url,'search');
      return false;
});

function actionGet(url,action){    
    $.post(url,$('#search-{$model->formName()}').serialize()).done(function(result) {
      if(action === 'search'){
        $('.report-content').html(result);
      }else{
        $('.report-export').html(result);
      }        
    }).fail(function() {
            " . SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') . "
            console.log('server error');
    });
}
");
?>
