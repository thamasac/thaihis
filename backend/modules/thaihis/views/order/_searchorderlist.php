<?php

use yii\bootstrap\ActiveForm;
//$groupItem = ['L' => 'LAB', 'X' => 'XRAY', 'C' => 'Cytologic', 'S' => 'Service', 'P' => 'Package'];
?>
<div id="order-lists-search" style="padding: 5px;">  
  <?php
  $options = \backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String($options);
  $form = ActiveForm::begin([
              'id' => 'search-' . $model->formName(),
              'enableClientValidation' => FALSE,
              'action' => ['/thaihis/order/order-search',
                  'visit_id' => $visit_id,
                  'reloadDiv' => $reloadDiv, 'options' => $options],
  ]);
  ?>
  <?php
  if ($filterOrderType['enable']) {
      $ezform = backend\modules\ezforms2\classes\EzfQuery::getEzformOne($filterOrderType['ezf_id']);
      $model_ordertype = \backend\modules\patient\classes\PatientFunc::loadTbDataByField($ezform->ezf_table, ['order_type_status' => 1], "all");
      $model_ordertype = yii\helpers\ArrayHelper::map($model_ordertype, 'order_type_code', 'order_type_name');

      echo $form->field($model, 'group_type')->widget(kartik\widgets\Select2::className(), [
          'data' => $model_ordertype,
          'options' => ['placeholder' => Yii::t('patient', 'All'), 'multiple' => FALSE, 'class' => 'form-control'],
          'pluginOptions' => [
              'minimumInputLength' => 0,
              'allowClear' => true,
          ],
      ])->label(FALSE);
  }
  ?>
  <?= $form->field($model, 'order_name')->textInput(['class' => 'form-control search-query', 'placeholder' => Yii::t('patient', 'โปรดพิมพ์ 3 ตัวอีกษรขึ้นไปในการค้นหา')])->label(false); ?>
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

$('form#search-{$model->formName()} input').on('keyup', function(e) {
    if ($(this).val().length > 2) {
        actionSearch();
    }
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
