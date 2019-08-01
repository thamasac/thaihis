<?php

use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;

if ($sect_code == 'S076') {
    $url = \yii\helpers\Url::to(['/patient/order/order-receive-submit', 'ezf_id' => $ezf_id, 'target' => $target
                , 'order_status' => $order_status, 'pt_id' => $pt_id, 'dept' => $dept]);
} else {
    $url = \yii\helpers\Url::to(['/patient/order/order-receive-submit-pap', 'visit_id' => $target
                , 'order_status' => $order_status, 'pt_id' => $pt_id, 'dept' => $dept]);
}
?>
<form name="order-receive" id="order-receive" action="<?= $url ?>" method="post">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="itemModalLabel">Order Receive</h4>
  </div>
  <div class="modal-body">
    <div class="row">
      <div class="col-md-12">  
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
                          <td class="col-md-1"><?= Yii::t('patient', 'Amount'); ?></td>
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
                      if ($sect_code == 'S076') {
                          if ($value['ezf_id']) {
                              $ezf_id = \backend\modules\patient\Module::$formID['cytoreport'];
                              $ezf_table = \backend\modules\patient\Module::$formTableName['cytoreport'];
                              $dataid = \backend\modules\patient\classes\PatientFunc::loadTbDataByTarget($ezf_table, $value['id']);
                              $btnClass = $dataid['report_status'] == 'H' ? 'btn-primary' : 'btn-success';
                              $initdata = \backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String(['report_status' => ($order_status == '1' ? 'H' : 'H')]);
                              echo $value['order_name'] . ' ' . \yii\helpers\Html::a('<span class="fa fa-wpforms"></span>', 'javascript:void(0)', ['class' => 'btn btn-sm ' . $btnClass,
                                  'data-url' => yii\helpers\Url::to(['/ezforms2/ezform-data/ezform', 'ezf_id' => $value['ezf_id'], 'modal' => 'modal-ezform-main', 'reloadDiv' => 'order-result-submit',
                                      'target' => $value['id'], 'dataid' => $dataid['id'], 'initdata' => $initdata,])]);

                              $url = \yii\helpers\Url::to(['/patient/order/order-result-submit', 'ezf_id' => $ezf_id, 'ezf_table' => $ezf_table, 'reloadDiv' => 'order-receive', 'dept' => $dept]);
                              ?>
                            <div id="order-result-submit" data-url="<?= $url ?>">

                            </div>
                            <?php
                        } elseif ($order_status == '2' && $value['ezf_id']) {
                            $ezf_id = \backend\modules\patient\Module::$formID['cytoreport'];
                            $ezf_table = \backend\modules\patient\Module::$formTableName['cytoreport'];
                            $dataid = \backend\modules\patient\classes\PatientFunc::loadTbDataByTarget($ezf_table, $value['id']);
                            $btnClass = $dataid['report_status'] == 'H' ? 'btn-primary' : 'btn-success';
                            $initdata = \backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String(['report_status' => ($order_status == '1' ? 'H' : 'H')]);
                            echo $value['order_name'] . ' ' . \yii\helpers\Html::a('<span class="fa fa-wpforms"></span>', 'javascript:void(0)', ['class' => 'btn btn-sm ' . $btnClass,
                                'data-url' => yii\helpers\Url::to(['/ezforms2/ezform-data/ezform', 'ezf_id' => $value['ezf_id'], 'modal' => 'modal-ezform-main', 'reloadDiv' => 'order-result-submit',
                                    'target' => $value['id'], 'dataid' => $dataid['id'], 'initdata' => $initdata,])]);

                            $url = \yii\helpers\Url::to(['/patient/order/order-result-submit', 'ezf_id' => $ezf_id, 'ezf_table' => $ezf_table, 'reloadDiv' => 'order-receive', 'dept' => $dept]);
                            ?>
                            <div id="order-result-submit" data-url="<?= $url ?>">

                            </div>
                            <?php
                        } else {
                            echo $value['order_name'];
                        }
                    } else {
                        if ($order_status <> 'H' && $value['ezf_id']) { //cyto order status 1 = ยังไม่ออกผล
                            $ezf_id = \backend\modules\patient\Module::$formID['cytoreport'];
                            $ezf_table = \backend\modules\patient\Module::$formTableName['cytoreport'];
                            $initdata = \backend\modules\ezforms2\classes\EzfFunc::arrayEncode2String(['report_status' => ($order_status == 'H' ? '1' : '2')]);
                            echo $value['order_name'] . ' ' . \yii\helpers\Html::a('<span class="fa fa-wpforms"></span>', 'javascript:void(0)', ['class' => 'btn btn-sm btn-primary',
                                'data-url' => yii\helpers\Url::to(['/ezforms2/ezform-data/ezform', 'ezf_id' => $ezf_id, 'modal' => 'modal-ezform-main', 'reloadDiv' => 'order-result-submit',
                                    'dataid' => $value['id'], 'initdata' => $initdata,])]);

                            $url = \yii\helpers\Url::to(['/patient/order/order-result-submit', 'ezf_id' => $ezf_id, 'ezf_table' => $ezf_table, 'reloadDiv' => 'order-receive', 'dept' => $order_status]);
                            ?>
                            <div id="order-result-submit" data-url="<?= $url ?>">

                            </div>
                            <?php
                        } else {
                            echo $value['order_name'];
                        }
                    }
                    ?>
                  </td>
                  <td><?= $value['order_qty']; ?></td>
                  <td class="text-center">   
                    <span class="button-checkbox">
                      <button type="button" class="btn btn-sm btn-default" data-color="success">
                        <i class="state-icon glyphicon glyphicon-unchecked"></i>&nbsp; <?= Yii::t('patient', 'Select'); ?> &nbsp;&nbsp;
                      </button>
                      <input type="checkbox" value="<?= $value['id'] . "^" . $value['order_code']; ?>" name="order_check[]" class="hidden" checked>
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
            <?= \yii\helpers\Html::hiddenInput('order_dept', $data[0]['order_tran_dept']); ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="modal-footer">      
    <button type="submit" class="btn btn-primary btn-submit" name="submit" value="1">Submit</button>
    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="glyphicon glyphicon-remove"></i> ปิด</button>    
  </div>
</form>
<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
?>
<script>
    var clicked = false;

    $('#order-receive').on('submit', function () {
      if (clicked == false) {
        clicked = true;
        var url = $(this).attr('action');
        $.post(url, $(this).serialize()).done(function (result) {
<?= SDNoty::show('result.message', 'result.status') ?>
          $(document).find('#modal-order-counter').modal('hide');
          $.pjax.reload({container: '#<?= $reloadDiv ?>'});
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