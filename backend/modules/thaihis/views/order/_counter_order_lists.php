<?php

use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;

$url = \yii\helpers\Url::to(['/thaihis/order/order-receive-submit', 'ezf_id' => $ezf_id, 'target' => $visit_id
            , 'order_status' => $order_status, 'pt_id' => isset($data[0]['ptid']) ? $data[0]['ptid'] : ''
            , 'dept' => $dept['order_type_code']
            , 'oipd_type' => isset($data[0]['order_tran_oi_type']) ? $data[0]['order_tran_oi_type'] : ''
        ]);
$doctor_id = '';
if (isset($data) && count($data) > 0) {
    $deptOrder = backend\modules\patient\classes\PatientQuery::getWorkingUnit($data[0]['unit_id_order']);
} else {
    echo "<p>ไม่มี Order</p>";
    return;
}
?>
<div class="sdbox-header">
  <h3><i class="fa fa-hospital-o" aria-hidden="true"></i> <?= $deptOrder['unit_name']; ?> </h3>
</div>
<form name="order-receive" id="order-receive" action="<?= $url ?>" method="post">
  <div class="modal-body" style="padding: 0px;">
    <table class="table table-bordered" id="table">
      <tbody>
          <?php
          $chkGroup = '';
          foreach ($data as $index => $value) {
              if ($chkGroup !== $value['order_group_name']) {
                  $chkGroup = $value['order_group_name'];
                  if ($index == 0) {
                      ?>
                    <tr class="success">
                      <td class="col-md-3"><?= $chkGroup ?></td>
                      <td class="col-md-6">
                          <?= Yii::t('patient', 'Order Name'); ?>
                      </td>
                      <td class="col-md-1">
                          <?= Yii::t('patient', 'Amount'); ?>
                      </td>
                      <td class="col-md-2"><?= Yii::t('patient', 'Order Receive') ?></td>
                    </tr>
                    <?php
                } else {
                    ?>
                    <tr class="success">
                      <td colspan="4"><?= $chkGroup ?></td>
                    </tr>
                    <?php
                }
            }
            ?>
            <tr>
              <td><?= $value['order_code']; ?></td> 
              <td>
                  <?php
                  $initdata = [];
                  if ($dept['order_type_code'] == 'C') {
                      $ezf_id = \backend\modules\patient\Module::$formID['cytoreport'];
                      $ezf_table = \backend\modules\patient\Module::$formTableName['cytoreport'];
                      $initdata = ['report_status' => 'H'];

                      $data_report = \backend\modules\patient\classes\PatientFunc::loadTbDataByTarget($ezf_table, $value['id']);
                      $btnClass = $data_report ? 'btn-primary' : 'btn-success';
                      $btnPap = '';
                      //reloadDiv noreload Div
                      if ($value['order_ezf_id']) {
                          $btnPap = backend\modules\ezforms2\classes\BtnBuilder::btn()
                                          ->ezf_id($value['order_ezf_id'])
                                          ->tag('a')->options(['class' => 'btn btn-sm ' . $btnClass])
                                          ->label('<span class="fa fa-wpforms"></span>')->initdata($initdata)
                                          ->target($value['id'])->reloadDiv('')
                                          ->dataid($data_report['id'])->buildBtnAdd();
                      }
                      echo $value['order_name'] . ' ' . $btnPap;
                  } elseif ($value['order_ezf_id'] && $order_status <> '1') {
                      $date = date("Y-m-d H:i:s");
                      if ($dept['order_type_code'] == 'X') {
                          $btn = '';
                          $ezf_id = \backend\modules\patient\Module::$formID['report_xray'];
                          $ezf_table = \backend\modules\patient\Module::$formTableName['report_xray'];
                          if (Yii::$app->user->can('doctor')) {
                              $initdata = ['report_status' => ($order_status == '3' ? '1' : '2'), 'report_xray_docid' => $user['user_id'], 'report_xray_date' => $date];
                          } else {
                              $initdata = ['report_status' => ($order_status == '3' ? '1' : '2')];
                          }

                          $data_report = \backend\modules\patient\classes\PatientFunc::loadTbDataByTarget($ezf_table, $value['id']);
                          $btnClass = $data_report ? 'btn-primary' : 'btn-success';
                          if ($data_report) {
                              if ($data_report['report_xray_docid'] == $user['user_id']) {

                                  $btn = ' ' . backend\modules\ezforms2\classes\BtnBuilder::btn()
                                                  ->ezf_id($value['order_ezf_id'])
                                                  ->tag('a')->options(['class' => 'btn btn-sm ' . $btnClass])
                                                  ->initdata($initdata)
                                                  ->label('<span class="fa fa-wpforms"></span>')
                                                  ->target($value['id'])
                                                  ->dataid($data_report['id'])
                                                  ->reloadDiv('order-result-submit')
                                                  ->modal('modal-order-report')
                                                  ->buildBtnAdd();
                              } else {
                                  $btn = ' ' . backend\modules\ezforms2\classes\BtnBuilder::btn()
                                                  ->ezf_id($value['order_ezf_id'])
                                                  ->tag('a')->options(['class' => 'btn btn-sm ' . $btnClass])
                                                  ->label('<span class="fa fa-wpforms"></span>')
                                                  ->target($value['id'])
                                                  ->modal('modal-order-report')
                                                  ->reloadDiv('order-result-submit')
                                                  ->buildBtnView($data_report['id']);
                              }
                              if ($order_status == '3') {
                                  $btn .= ' ' . \yii\helpers\Html::a('<span class="fa fa-print"></span>', \yii\helpers\Url::to(['/thaihis/print/print-report-xray', 'report_id' => $data_report['id']])
                                                  , ['class' => 'btn btn-warning btn-sm print-report-order', 'target' => '_blank']);
                              }
                          } else {
                              if (Yii::$app->user->can('doctor')) {

                                  $btn = ' ' . backend\modules\ezforms2\classes\BtnBuilder::btn()
                                                  ->ezf_id($value['order_ezf_id'])
                                                  ->tag('a')->options(['class' => 'btn btn-sm ' . $btnClass])
                                                  ->label('<span class="fa fa-wpforms"></span>')
                                                  ->initdata($initdata)
                                                  ->target($value['id'])
                                                  ->reloadDiv('order-result-submit')
                                                  ->modal('modal-order-report')
                                                  ->buildBtnAdd();
                              }
                          }
                          echo $value['order_name'] . $btn;
                      } else if ($dept['order_type_code'] == 'E') {
                          $ezf_id = \backend\modules\patient\Module::$formID['report_ekg'];
                          $ezf_table = \backend\modules\patient\Module::$formTableName['report_ekg'];
                          $initdata = ['report_status' => ($order_status == '3' ? '1' : '2')];

                          $data_report = \backend\modules\patient\classes\PatientFunc::loadTbDataByTarget($ezf_table, $value['id']);
                          $btnClass = $data_report ? 'btn-primary' : 'btn-success';

                          echo $value['order_name'] . ' ' . $btn = ' ' . backend\modules\ezforms2\classes\BtnBuilder::btn()->ezf_id($value['order_ezf_id'])
                                  ->tag('a')->options(['class' => 'btn btn-sm ' . $btnClass])
                                  ->label('<span class="fa fa-wpforms"></span>')->initdata($initdata)
                                  ->dataid($data_report['id'])->reloadDiv('order-result-submit')
                                  ->target($value['id'])->buildBtnAdd();
                      }
                  } else {
                      if ($value['doc_fullname']) {
                          $doctor_id = $value['order_tran_doctor'];
                          $value['order_name'] = yii\helpers\Html::a($value['order_name'], '#'
                                          , ['title' => $value['doc_fullname']
                                      , 'onclick' => "showDocname('{$value['doc_fullname']}')"]);
                      }

                      echo $value['order_name'] . ($value['external_flag'] == 'Y' ? ' (External Lab)' : '');
                  }
                  ?>
              </td>
              <td><?= $value['order_qty']; ?></td>
              <?php
              $ordercheck_status = backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String(['order_tran_id' => $value['id']
                          , 'order_code' => $value['order_code']]);
              ?>
              <td class="text-center">   
                <span class="button-checkbox">
                  <button type="button" class="btn btn-sm btn-default" data-color="success">
                    <i class="state-icon glyphicon glyphicon-unchecked"></i>&nbsp; <?= Yii::t('patient', 'Select'); ?> &nbsp;&nbsp;
                  </button>
                  <input type="checkbox" value="<?= $ordercheck_status; ?>" name="order_check[]" class="hidden" checked>
                </span>
                <?php
                if ($order_status == '2') {
                    echo \yii\helpers\Html::hiddenInput('chkorder_tran_id[]', $value['id']);
                }
                ?>
              </td>
            </tr>                
            <?php
        }
        ?>
        <?php if ($order_status == '1' && $dept['order_type_code'] == 'L') : ?>
            <tr class="success">
              <td> <?php
                  if (empty($data[0]['order_vender_status'])) {
                      $stat = ($data[0]['unit_code_order'] == 'S047' ? 1 : 2);
                  } else {
                      $stat = $data[0]['order_vender_status'];
                  }
                  echo \yii\helpers\Html::radioList('order_vender_status', $stat, [
                      1 => Yii::t('patient', 'Checkup'),
                      2 => Yii::t('patient', 'STAT'),
                      3 => Yii::t('patient', 'ASAP'),
                      4 => '4F']);
                  ?>
              </td>
              <td><?php
                  if ($lab_no['order_vender_no']) {
                      echo \yii\helpers\Html::radioList('order_vender_no', 'NEW', [
                          'NEW' => 'New Ln',
                          $lab_no['order_vender_no'] => 'UseLn : ' . $lab_no['order_vender_no']]);
                  }
                  ?></td>
              <td>
                  <?= \yii\helpers\Html::hiddenInput('order_bydept', $data[0]['order_tran_dept']); ?>
              </td>
              <td></td>
            </tr>
        <?php else : ?>
            <tr class="success">
              <td></td>
              <td></td>
              <td></td>
              <td></td>
            </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <div class="modal-footer">     
      <?php
      if (Yii::$app->user->can('doctor') && $order_status == '2') :

      else :
          ?>
        <button type="submit" class="btn btn-primary btn-submit" name="submit" value="1">Submit</button>
    <?php
    endif;
    //appointment
//    if ($dept['order_type_code'] == 'X') {
//        $initdata = [];
//
//        $initdata['app_doctor'] = $doctor_id;
//        $initdata['app_dept'] = $dept['unit_id'];
//        $initdata['app_status'] = '1';
//
//        $initdata = \backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String($initdata);
//        echo ' ' .
//        \yii\helpers\Html::a('<i class="glyphicon glyphicon-plus"></i> Appoint', 'javascript:void(0)', ['class' => 'btn btn-success ezform-main-open btn-appoint', 'data-modal' => 'modal-ezform-main',
//            'data-url' => yii\helpers\Url::to(['/ezforms2/ezform-data/ezform', 'ezf_id' => $ezfAppoint_id, 'modal' => 'modal-ezform-main', 'reloadDiv' => 'btn-appoint-save',
//                'target' => $data[0]['ptid'], 'initdata' => $initdata,])]);
//    }
    ?>
  </div>
</form>
<?php
echo \appxq\sdii\widgets\ModalForm::widget([
    'id' => 'modal-order-report',
    'size' => 'modal-xxl',
    'tabindexEnable' => false,
    'clientOptions' => ['backdrop' => 'static', 'keyboard' => false],
]);

$url = '';
$urlRedirect = \yii\helpers\Url::to(['/ezmodules/ezmodule/view', 'id' => $ezm_id, 'search_field[order_tran_status]' => '1']);
if ($order_status <> '1' AND ! in_array($dept['order_type_code'], ['L', 'C'])) {
    $url = \yii\helpers\Url::to(['/thaihis/order/order-result-submit', 'ezf_id' => $ezf_id, 'ezf_table' => $ezf_table, 'reloadDiv' => 'order-receive', 'dept' => $dept['order_type_code'], 'ezm_id' => $ezm_id]);
    ?>
    <div id="order-result-submit" data-url="<?= $url ?>"></div>
    <?php
}
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
    $('.print-order-report').on('click', function () {
      var url = $(this).attr('href');
      myWindow = window.open(url, '_blank');
      return false;
    });

    function showDocname(name) {
      yii.confirm(name);
    }

    var clicked = false;
    $('#order-receive').on('submit', function () {
      if (clicked == false) {
        clicked = true;
        var url = $(this).attr('action');
        $.post(url, $(this).serialize()).done(function (result) {
<?= SDNoty::show('result.message', 'result.status') ?>
          window.location.href = '<?= $urlRedirect ?>';
        }).fail(function () {
<?= SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') ?>
          console.log('server error');
          clicked = false;
        });
      }
      return false;
    });

    $('.button-checkbox').each(function () {
      // Settings
      var $widget = $(this),
              $button = $widget.find('button'),
              $checkbox = $widget.find('input:checkbox'),
              color = $button.data('color'),
              settings = {
                on: {
                  icon: 'glyphicon glyphicon-check'
                },
                off: {
                  icon: 'glyphicon glyphicon-unchecked'
                }
              };
      // Event Handlers
      $button.on('click', function () {
        $checkbox.prop('checked', !$checkbox.is(':checked'));
        $checkbox.triggerHandler('change');
        updateDisplay();
      });
      $checkbox.on('change', function () {
        updateDisplay();
      });
      // Actions
      function updateDisplay() {
        var isChecked = $checkbox.is(':checked');
        // Set the button's state
        $button.data('state', (isChecked) ? "on" : "off");
        // Set the button's icon
        $button.find('.state-icon')
                .removeClass()
                .addClass('state-icon ' + settings[$button.data('state')].icon);
        // Update the button's color
        if (isChecked) {
          $button
                  .removeClass('btn-default')
                  .addClass('btn-' + color + ' active');
        } else {
          $button
                  .removeClass('btn-' + color + ' active')
                  .addClass('btn-default');
        }
      }

      // Initialization
      function init() {

        updateDisplay();
        // Inject the icon if applicable
        if ($button.find('.state-icon').length == 0) {
          $button.prepend('<i class="state-icon ' + settings[$button.data('state')].icon + '"></i> ');
        }
      }
      init();
    });
</script>
<?php \richardfan\widget\JSRegister::end(); ?>