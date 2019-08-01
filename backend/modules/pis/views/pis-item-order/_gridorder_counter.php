<?php

use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use yii\helpers\Url;
use kartik\grid\GridView;
use yii\helpers\Html;

$url = \yii\helpers\Url::to(['/pis/pis-order-counter/order-tran-save', 'order_id' => $model['id']
            , 'order_status' => $options['order_tran_status']]);

$options['action_view'] = '2';
$options['action_counter'] = true; // disnable btn approve order *** orderby pis counter
if ($visit_id) {
    ?>
    <div class="row">  
      <div class="col-md-12">
          <?php
          $modelFields = \backend\modules\ezforms2\models\EzformFields::find()->where('ezf_id = :ezf_id AND ezf_field_name = :ezf_field_name', [':ezf_field_name' => 'order_doctor_id', ':ezf_id' => $ezfOrder_id])->one();
          if (isset(Yii::$app->session['ezf_input'])) {
              $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($modelFields['ezf_field_type'], Yii::$app->session['ezf_input']);
          }

          $doc_name = \backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $modelFields, $model);
          ?>
        <strong>แพทย์ผู้สั่ง : </strong> <span class="text-info"><?= $doc_name ?> </span>
        <strong>เลขที่ใบยา : </strong> <span class="text-info"><?= $model['order_no'] ?> </span>      
        <?php
        echo backend\modules\ezforms2\classes\BtnBuilder::btn()
                ->ezf_id($ezfOrder_id)
                ->reloadDiv('openPisOrder')->label('<i class="glyphicon glyphicon-plus"></i> เพิ่มใบยา')
                ->options(['class' => 'btn btn-success btn-sm'])->target($visit_id)->initdata(['order_status' => '1', 'order_orderby' => '2'])
                ->buildBtnAdd();
        ?>
        <span id="openPisOrder" data-url="<?= Url::to(['/pis/pis-item-order/open-order', 'visitid' => $visit_id, 'options' => \backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String($options)]) ?>"></span>
      </div>
      <div class="col-md-12" style="margin-top:5px;">
          <?= \backend\modules\pis\classes\PisHelper::uiDrugAllergy($ptid, 'counter-drug-allergy') ?>
      </div>
    </div>
<?php } ?>
<form name="order-receive" id="order-receive" action="<?= $url ?>" method="post">
  <div class="modal-body">
    <div class="row">
      <div class="col-md-12">          
          <?=
          GridView::widget([
              'id' => 'grid-order-trans-' . rand(),
//'filterModel' => $searchModel,
              'dataProvider' => $dataProvider,
              'striped' => false,
              'hover' => true,
              'showPageSummary' => true,
              'rowOptions' => function ($model)use($user_id) {
                  return [
                      'data' => [
                          'key' => ($model['order_tran_status'] == '1') ? $model['id'] : ''
                          , 'tmt' => $model['trad_tmt']
                          , 'order-tran' => $model['order_status']
                      ],
                  ];
              },
              'columns' => [
//                  [
//                      'attribute' => 'order_tran_use_type',
//                      'label' => Yii::t('patient', 'Type'),
//                      'group' => true, // enable grouping,
//                      'groupedRow' => true, // move grouped column to a single grouped row
//                      'groupOddCssClass' => 'kv-grouped-row', // configure odd group cell css class
//                      'groupEvenCssClass' => 'kv-grouped-row', // configure even group cell css class   
//                      'value' => function ($model)use($ezf_id) {
//
//                          $modelFields = \backend\modules\ezforms2\models\EzformFields::find()->where('ezf_id = :ezf_id AND ezf_field_name = :ezf_field_name', [':ezf_field_name' => 'order_tran_use_type', ':ezf_id' => $ezf_id])->one();
//                          if (isset(Yii::$app->session['ezf_input'])) {
//                              $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($modelFields['ezf_field_type'], Yii::$app->session['ezf_input']);
//                          }
//
//                          return \backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $modelFields, $model);
//                      },
//                      'contentOptions' => ['class' => 'danger']
//                  ],
                  [
                      'attribute' => 'item_name',
                      'label' => Yii::t('patient', 'Order Name'),
                      'format' => 'html',
                      'value' => function ($model)use($ezf_id) {
                          $html = '';
                          if ($model['order_tran_chemo_amount'] && $model['order_tran_chemo_result']) {
                              $txt = '&nbsp;&nbsp;สูตร ' . $model['order_tran_chemo_cal'] . ' ปริมาณ ' . $model['order_tran_chemo_amount'] . ' ผลลัพธ์ ' . $model['order_tran_chemo_result'];
                              $html .= Html::tag('div', $txt, ['style' => 'color:#999;']);
                          }

                          $html .= Html::tag('div', '&nbsp;&nbsp;' . $model['order_tran_label'], ['style' => 'color:#999;']);

                          if ($model['order_tran_note']) {
                              $html .= Html::tag('div', '&nbsp;&nbsp;หมายเหตุ ' . $model['order_tran_note'], ['style' => 'color:#999;']);
                          }

                          if ($model['order_tran_ned']) {
                              $modelFields = \backend\modules\ezforms2\models\EzformFields::find()->where('ezf_id = :ezf_id AND ezf_field_name = :ezf_field_name', [':ezf_field_name' => 'order_tran_ned', ':ezf_id' => $ezf_id])->one();
                              if (isset(Yii::$app->session['ezf_input'])) {
                                  $dataInput = \backend\modules\ezforms2\classes\EzfFunc::getInputByArray($modelFields['ezf_field_type'], Yii::$app->session['ezf_input']);
                              }
                              $txtNed = \backend\modules\ezforms2\classes\EzfUiFunc::getValueEzform($dataInput, $modelFields, $model);
                              $html .= Html::tag('div', '&nbsp;&nbsp;' . $model['order_tran_ned'] . ' : ' . $txtNed, ['style' => 'color:#999;']);
                          }
                          return $model['trad_itemname'] . $html;
                      },
                      'pageSummary' => 'รวม',
                      'pageSummaryOptions' => ['class' => 'text-right text-warning'],
                  ],
                  [
                      'attribute' => 'order_tran_qty',
                      'label' => Yii::t('patient', 'Amount'),
                      'format' => 'raw',
                      'value' => function ($model)use($user_id) {
                          if ($model['order_tran_status'] == '1' && $model['order_status'] == '1' && $model['order_doctor_id'] == $user_id) {
                              $result = Html::textInput('order_tran_qty', $model['order_tran_qty'], ['class' => 'form-control text-center', 'data-key' => $model['id']]);
                          } else {
                              $result = $model['order_tran_qty'];
                          }
                          return $result;
                      },
                      'contentOptions' => ['style' => 'width:40px;text-align: center;'],
                  ],
                  [
                      'attribute' => 'order_tran_notpay',
                      'format' => ['decimal', 2],
                      'label' => Yii::t('patient', 'Not pay'),
                      'pageSummary' => true,
                      'pageSummaryFunc' => GridView::F_SUM,
                      'pageSummaryOptions' => ['class' => 'text-right'],
                  ], [
                      'attribute' => 'order_tran_pay',
                      'format' => ['decimal', 2],
                      'label' => Yii::t('patient', 'Pay'),
                      'pageSummary' => true,
                      'pageSummaryFunc' => GridView::F_SUM,
                      'pageSummaryOptions' => ['class' => 'text-right'],
                  ],
                  [
                      'class' => 'kartik\grid\CheckboxColumn',
//                      'contentOptions' => ['class' => 'kv-row-select'],
                      'headerOptions' => ['class' => 'kartik-sheet-style'],
                      'checkboxOptions' => function($model, $key, $index, $widget) {

                          return ["value" => $model['id'], 'checked' => $model['order_tran_status'] == 1 ? 'checked' : ''];
                      },
                      'name' => 'order_check'
                  ],
              ],
          ]);
          ?>
      </div>
    </div>
  </div>
  <div class="modal-footer">         
      <?php
      if ($dataProvider->getCount() > 0) {
          if ($options['order_tran_status'] == '1') {
              ?>
            <button type="submit" class="btn btn-primary btn-receive" name="submit" value="1">Receive</button>
            <span class="btn btn-warning print-order" name="print">Print Order</span>    
        <?php } elseif ($options['order_tran_status'] == '2') { ?>
            <button type="submit" class="btn btn-danger btn-cancel" name="submit" value="1">Cancel</button>
            <span class="btn btn-warning print-label"><i class="fa fa-print"></i> Print Sticker</span>
            <span class="btn btn-warning print-order" name="print"><i class="fa fa-file-o"></i> Print Order</span>
            <?php
        }
    }
    ?>  
  </div>
</form>
<?php
$url = Url::to(['/pis/pis-order-counter/print-label', 'order_id' => $model['id']
            , 'order_status' => '2']);
$editInit = backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String(['order_tran_flagstatus' => '2']);
$editUrl = Url::to(['/ezforms2/ezform-data/ezform', 'ezf_id' => $ezf_id, 'modal' => 'modal-ezform-main', 'reloadDiv' => $reloadDiv, 'initdata' => $editInit]);
$urlreload = Url::to(['/ezmodules/ezmodule/view', 'id' => $options['ezm_id'], 'search_field[order_tran_status]' => '1']);
$urlPrint = Url::to(['/pis/pis-item-order/print-order-approve', 'order_id' => $model['id']]);
\richardfan\widget\JSRegister::begin([
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
    $('#ezf-fix-modal-box').append('<div id="modal-<?= $ezfOrder_id ?>" class="fade modal" role="dialog"><div class="modal-dialog modal-xxl"><div class="modal-content"></div></div></div>');
    $('#order-receive').on('submit', function () {
      var url = $(this).attr('action');
      $.post(url, $(this).serialize()).done(function (result) {
<?= SDNoty::show('result.message', 'result.status') ?>
        if (result.status !== 'error') {
<?php if ($options['order_tran_status'] == '1') { ?>
              myWindow = window.open('<?= $url ?>', '_blank');
              myWindow.focus();
              myWindow.print();
<?php } ?>
          location.href = "<?= $urlreload ?>";
        }
      }).fail(function () {
<?= SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') ?>
        console.log('server error');
      });
      return false;
    });

    $('.print-label').on('click', function () {
      myWindow = window.open('<?= $url ?>', '_blank');
      myWindow.focus();
      myWindow.print();
    });

    $('.print-order').on('click', function () {
      myWindow = window.open('<?= $urlPrint ?>', '_blank');
      myWindow.focus();
      myWindow.print();
    });

    function modalEzformMain(url, modal) {
      $(modal + ' .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
      $(modal).modal('show')
              .find('.modal-content')
              .load(url);
    }

    $('#<?= $reloadDiv ?> .grid-view').on('dblclick', 'tbody tr', function () {
      let data_key = $(this).attr('data-key');
      if (data_key) {
        let txt = 'ต้องการแก้ไข รายการใช่หรือไม่ ?';
        yii.confirm(txt, function () {
          var url = '<?= $editUrl ?>' + '&dataid=' + data_key;
          modalEzformMain(url, '#modal-ezform-main');
        });
      }
    });
</script>
<?php \richardfan\widget\JSRegister::end(); ?>