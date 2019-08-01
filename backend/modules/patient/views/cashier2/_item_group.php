<td style="border:none;"></td>
<td style="border:none;"></td>
<td colspan="4">
  <table id="table_<?= $fin_code ?>" class="table table-bordered" style="margin-bottom:0px ">
    <tbody>
        <?php
        $i = 1;
        foreach ($data as $value) {
            ?>                
          <tr id="<?= $value['id'] ?>">
            <td class="col-md-1">
                <?php
                echo $i;
                $i++;
                ?>
            </td> 
            <td class="col-md-1">
                <?= $value['sks_code']; ?>
            </td> 
            <td class="col-md-7">
                <?php
                echo $value['order_name'];
                ?>
            </td>
            <td class="col-md-2">
              <input type="text" name="order_check[pay][]" class="form-control pay" value="<?= $value['pay'] ?>">
            </td>
            <td class="text-center col-md-1">   
              <span class="button-checkbox">
                <button type="button" class="btn btn-sm btn-default" data-color="success">
                  <i class="state-icon glyphicon glyphicon-unchecked"></i>&nbsp; <?= Yii::t('patient', 'Select'); ?> &nbsp;&nbsp;
                </button>
                <input type="checkbox" value="<?= $value['id'] ?>" name="order_check[item_id][]" class="hidden" checked>
              </span>
            </td>
          </tr>                
          <?php
      }
      ?>
    </tbody>
  </table>
</td>
<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
$url_item = \yii\helpers\Url::to(['/patient/cashier/cashier-receive-item-group', 'visit_id' => $visit_id,]);
$url_receipt = \yii\helpers\Url::to(['/patient/cashier/print-receipt', 'receipt_id' => '',]);
?>
<script>
    $('#table_<?= $fin_code ?> input[type="checkbox"]').on('change', function () {
      if (!this.checked) {
        $('#' + $(this).val() + ' input[type="text"]').attr('disabled', true);
      } else {
        $('#' + $(this).val() + ' input[type="text"]').removeAttr('disabled');
      }
      sumPrice('<?= $fin_code ?>');
    });

    $('#table_<?= $fin_code ?> .button-checkbox').each(function () {
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

    $('#table_<?= $fin_code ?> input[name="order_check[pay][]"]').on('keyup', function () {
      sumPrice('<?= $fin_code ?>');
    });
</script>
<?php \richardfan\widget\JSRegister::end(); ?>