<div class="card"> 
  <div class="card-block">
    <div class="col-md-12">
        <?php
        if (!empty($data[0]['visit_type'])) {
            echo '<strong>ประเภทการมา </strong>: ' . backend\modules\patient\classes\PatientFunc::visit_type($data[0]['visit_type']) . ' <strong> วันที่ : </strong> ' . \appxq\sdii\utils\SDdate::mysql2phpThDate($data[0]['visit_date']);
        }
        ?>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">  
    <table class="table table-bordered">
      <tbody>
          <?php
          foreach ($data as $value) {
              ?>                
            <tr>
              <td class="text-center col-md-1">   
                <span class="dept-check">
                  <button type="button" class="btn btn-sm btn-default" data-color="success">
                    <i class="state-icon glyphicon glyphicon-unchecked"></i>&nbsp; <?= Yii::t('patient', 'Select'); ?> &nbsp;&nbsp;
                  </button>
                  <input type="checkbox" value="<?= $value['unit_id'] ?>" name="order_tran_dept" class="hidden">
                </span>
              </td> 
              <td><?= \appxq\sdii\utils\SDdate::mysql2phpThDate($value['visit_date']) ?></td> 
              <td><?= $value['unit_name']; ?></td> 
              <td><?= number_format($value['pay'], 2, ".", ","); ?></td>
              <td><?= number_format($value['notpay'], 2, ".", ","); ?></td>
            </tr>
            <?php
        }
        ?>
      </tbody>
    </table>
  </div>
  <div class="col-md-12" id="receive-detail">

  </div>
</div>
<?php
\richardfan\widget\JSRegister::begin([
    //'key' => 'bootstrap-modal',
    'position' => \yii\web\View::POS_READY
]);
$url = \yii\helpers\Url::to(['/patient/cashier2/receive-show-detail', 
    'reloadDiv' => 'receive-detail', 
    'params' => $params]);
?>
<script>
<?php if ($cashier_status) : ?>
        $('.dept-check input[type="checkbox"]').trigger('click');
        loadDetail();
<?php endif; ?>

    $('.dept-check input[type="checkbox"]').on('change', function () {
      loadDetail();
    });

    function loadDetail() {
      var objCheckbox = $('.dept-check input[type="checkbox"]:checked');
      var unit_id = $.map(objCheckbox, function (obj) {
        return "'" + obj.value + "'";
      }).join(',');
      if (unit_id) {
        $.get('<?= $url ?>', {unit_id: unit_id}).done(function (result) {
          $('#receive-detail').html(result);
        }).fail(function () {
          console.log('server error');
        });
      } else {
        $('#receive-detail').html('');
      }
    }

    $('.dept-check').each(function () {
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