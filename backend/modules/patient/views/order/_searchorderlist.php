<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

//$groupItem = \backend\modules\patient\classes\PatientQuery::getHospitalGroupItem();
//$groupItem = \yii\helpers\ArrayHelper::map($groupItem, 'order_type_code', 'order_type_name');

$groupItem = ['L' => 'LAB', 'X' => 'XRAY', 'C' => 'Cytologic', 'S' => 'Service', 'P' => 'Package'
        //, 'D' => 'Dental',
];
?>
<div id="order-lists-search" style="padding: 5px;">  
  <?php
  $form = ActiveForm::begin([
              'id' => 'search-' . $model->formName(),
              'action' => ['/patient/order/order-search', 'target' => $target, 'reloadDiv' => $reloadDiv],
                  //'options' => ['class' => 'form-inline'],
  ]);
  ?>
  <?=
  $form->field($model, 'group_type')->widget(kartik\widgets\Select2::className(), [
      'data' => $groupItem,
      'options' => ['placeholder' => Yii::t('patient', 'All'), 'multiple' => FALSE, 'class' => 'form-control'],
      'pluginOptions' => [
          'minimumInputLength' => 0,
          'allowClear' => true,
      ],
  ])->label(FALSE);
  ?>
  <?= $form->field($model, 'order_name')->textInput(['class' => 'form-control search-query', 'placeholder' => Yii::t('patient', 'Please Input Order')])->label(false); ?>
  <?php
  ActiveForm::end();
  ?>
</div>
<?php
$this->registerJs(" 
$('form#search-{$model->formName()} input').select();

$('form#search-{$model->formName()}').on('beforeSubmit', function(e) {
    actionSearch();
    return false;
});

$('form#search-{$model->formName()} select').on('change', function(e) {
    actionSearch();
});

$('form#search-{$model->formName()}').on('keyup', function(e) {
   actionSearch();
});

function actionSearch(){
    $.post($('#search-{$model->formName()}').attr('action'),$('#search-{$model->formName()}').serialize()).done(function(result) {
	$('#$reloadDiv').html(result);
    }).fail(function() {
	" . \appxq\sdii\helpers\SDNoty::show("'" . appxq\sdii\helpers\SDHtml::getMsgError() . "Server Error'", '"error"') . "
	console.log('server error');
    });
}

");
?>
