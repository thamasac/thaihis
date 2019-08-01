<?php

use yii\helpers\Html;
use appxq\sdii\helpers\SDHtml;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\UserSearch */
/* @var $form yii\widgets\ActiveForm */
?> 
<div class="ordercounter-search col-md-12">
    <?php
    $form = ActiveForm::begin([
                'id' => 'search-grid-cyto',
                'action' => ['/thaihis/order/order-counter-cyto', 'unit_id' => $unit_id],
                'method' => 'get',
                'options' => [
                    'style' => 'margin-bottom: 0px;'
                ],
    ]);
    $item = ['1' => 'รอรับ', '2' => 'รับแล้ว', '3' => 'ออกผล'];
    ?>
  <div class="col-md-3" style="padding-left: 0px;padding-right: 0px;">
      <?= Html::activeRadioList($model, 'order_tran_status', $item, ['class' => 'pull-left', 'itemOptions' => ['class' => 'radio-inline']]); ?>
  </div>
  <div class="col-md-3 sdbox-col">
      <?=
      kartik\widgets\DatePicker::widget([
          'model' => $model,
          'attribute' => 'create_date',
          'type' => kartik\widgets\DatePicker::TYPE_INPUT,
          'pluginOptions' => [
              'autoclose' => true,
              'format' => 'dd/mm/yyyy'
          ]
      ]);
      ?>
  </div>
  <div class="col-md-5 sdbox-col">
    <div class="input-group">
        <?=
        Html::activeInput('text', $model, 'order_tran_code', ['id' => 'search_ordercounter', 'class' => 'form-control'
            , 'placeholder' => Yii::t('patient', 'Find the form name or hn.')]);
        ?>

      <div class="input-group-btn">
          <?= Html::button(SDHtml::getBtnSearch(), ['class' => 'btn btn-default', 'type' => 'submit']); ?>
      </div>
      <div class="input-group-btn">
        <a class="btn btn-warning btn-sm print-report-order"   href="javascript:print_listname()" title="Print"><span class="fa fa-print"></span></a>
      </div>
    </div>

  </div>
  <?php ActiveForm::end(); ?>
</div>
<?php
$url = \yii\helpers\Url::to(['/patient/restful/print-report-list-name-pep', 'dates' => '']);
?>
<?php
$this->registerJs(" 
function print_listname() {
      var create_date = $('#ez1504537671028647300-create_date').val();
      //var order_tran_status = $('#ez1504537671028647300-order_tran_status input[name='EZ1504537671028647300[order_tran_status]']:checked').val();
      var url = '$url' + create_date + '&order_tran_status=' + order_tran_status + '&dept=' + '$unit_id';
      myWindow = window.open(url, '_blank');
    } 

$('#search_ordercounter').select();

$('#search-grid-cyto input[type=\"radio\"],input[type=\"text\"]').on('change',function(){
    let url = $('#$reloadDiv').attr('data-url') + $('#search-grid-cyto').serialize() + '&unit_id=$unit_id';
    getUiAjaxCyto(url,'$reloadDiv');

    return false;
});

function getUiAjaxCyto(url, reloadDiv) {
    var div = $('#' + reloadDiv);
    div.html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    $.get(url, function (result) {
        div.empty();
        div.html(result);
    });
}
");
?>
