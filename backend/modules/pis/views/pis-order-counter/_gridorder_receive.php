<?php

use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use yii\helpers\Url;

$url = \yii\helpers\Url::to(['/pis/pis-order-counter/order-tran-save', 'order_id' => $order_id
            , 'order_status' => $order_status]);
?>
<div class="row">
  <div class="col-md-6">
      <?= backend\modules\patient\classes\PatientHelper::uiPatientCpoe($data[0]['pt_id'], 'view-pt', '_patient_profile_receive') ?>  
  </div>
  <div class="col-md-6">
    <div>
      <strong>เลขที่ใบยา : </strong> <span class="text-info"><?= $data[0]['order_no'] ?> </span>
    </div>
    <div style="margin-top:5px;">
      <strong>แพทย์ผู้สั่ง : </strong> <span class="text-info"><?= $data[0]['doctor_name'] ?> </span>
    </div>
    <div style="margin-top:5px;">
      <button type="button" class="btn btn-success btn-sm ezform-main-open" data-modal="modal-ezform-main" data-url="<?= Url::to(['/pis/pis-item-order', 'visit_id' => $data[0]['order_visit_id'], 'order_id' => $data[0]['order_id']]) ?>">
        <i class="glyphicon glyphicon-plus"></i> เพิ่มใบยา
      </button>
    </div>
    <div style="margin-top:5px;">
        <?= \backend\modules\pis\classes\PisHelper::uiDrugAllergy($data[0]['pt_id'], 'counter-drug-allergy') ?>
    </div>
  </div>
</div>
<form name="order-receive" id="order-receive" action="<?= $url ?>" method="post">
  <div class="modal-body">
    <div class="row">
      <div class="col-md-12">  
        <table class="table table-bordered" id="table">
          <tbody>
            <tr class="success">
              <td style="width:50px;"><?= Yii::t('patient', 'Order Number'); ?></td>
              <td class="col-md-6"><?= Yii::t('patient', 'Order Name'); ?></td>
              <td><?= Yii::t('patient', 'Amount') ?></td>
              <td><?= Yii::t('patient', 'Not pay') ?></td>
              <td><?= Yii::t('patient', 'Pay') ?></td>
              <td><?= Yii::t('patient', 'Order Receive') ?></td>
            </tr>
            <?php
            $i = 1;
            foreach ($data as $value) {
                ?>
                <tr data-key="<?= $value['id'] ?>">
                  <td style="text-align: center;"><?= $i++; ?></td> 
                  <td>
                      <?php
                      $html = yii\helpers\Html::tag('div', '&nbsp;&nbsp;' . $value['label_1'] . ' ' . $value['label_2'], ['style' => 'color:#999;']);
                      echo $value['item_name'] . $html;
                      ?>
                  </td>
                  <td><?= $value['order_tran_qty']; ?></td>
                  <td><?= number_format($value['order_tran_notpay'], 2, ".", ","); ?></td>
                  <td><?= number_format($value['order_tran_pay'], 2, ".", ","); ?></td>
                  <td class="text-center">   
                    <span class="button-checkbox">
                      <button type="button" class="btn btn-sm btn-default" data-color="success">
                        <i class="state-icon glyphicon glyphicon-unchecked"></i>&nbsp; <?= Yii::t('patient', 'Select'); ?> &nbsp;&nbsp;
                      </button>
                      <input type="checkbox" value="<?= $value['id'] ?>" name="order_check[]" class="hidden" checked>
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
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="modal-footer">         
      <?php if ($order_status == '1') { ?>
        <button type="submit" class="btn btn-primary btn-receive" name="submit" value="1">Receive</button>
    <?php } elseif ($order_status == '2') { ?>
        <button type="submit" class="btn btn-danger btn-cancel" name="submit" value="1">Cancel</button>
        <button type="button" class="btn btn-warning print-label"><i class="fa fa-print"></i> Print Label</button>    
    <?php } ?>  
  </div>
</form>
<?php
$url = Url::to(['/pis/pis-order-counter/print-label', 'order_id' => $order_id
            , 'order_status' => '2']);
$urlEditOrder = Url::to(['/ezforms2/ezform-data/ezform', 'ezf_id' => $ezf_id, 'modal' => 'modal-ezform-main', 'reloadDiv' => 'view-order-counter']);
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
    $('#order-receive').on('submit', function () {
      var url = $(this).attr('action');
      $.post(url, $(this).serialize()).done(function (result) {
<?= SDNoty::show('result.message', 'result.status') ?>
        if (result.status !== 'error') {
<?php if ($order_status == '1') { ?>
              myWindow = window.open('<?= $url ?>', '_blank');
              myWindow.focus();
              myWindow.print();
<?php } ?>
          location.reload();
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

    $('#order-receive').on('dblclick', 'tbody tr', function () {
      if ($(this).attr('data-key')) {
        var url = '<?= $urlEditOrder ?>' + '&dataid=' + $(this).attr('data-key');
        modalEzformMain(url, '#modal-ezform-main');
      }
    });

    function modalEzformMain(url, modal) {
      $(modal + ' .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
      $(modal).modal('show')
              .find('.modal-content')
              .load(url);
    }

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